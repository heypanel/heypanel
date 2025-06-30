/**
 * @sw-package discovery
 */

import template from './sw-channel-detail-questions.html.twig';
import './sw-channel-detail-questions.scss';

const { Mixin, Context } = HeyPanel;
const { EntityCollection, Criteria } = HeyPanel.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: [
        'repositoryFactory',
        'feature',
        'acl',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        channel: {
            type: Object,
            required: false,
            default: () => {},
        },
    },

    data() {
        return {
            questions: [],
            questionSelection: [],
            isLoading: false,
            searchTerm: null,
            page: 1,
            limit: 25,
            total: 0,
            showProductsModal: false,
            isAssignProductLoading: false,
        };
    },

    computed: {
        questionRepository() {
            return this.repositoryFactory.create('question');
        },

        questionVisibilityRepository() {
            return this.repositoryFactory.create('question_visibility');
        },

        questionCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            criteria.setTotalCountMode(1);

            criteria.addAssociation('visibilities.channel');
            criteria.addAssociation('options.group');
            criteria.addFilter(Criteria.equals('question.visibilities.channelId', this.channel.id));

            if (this.searchTerm) {
                criteria.setTerm(this.searchTerm);
            }

            return criteria;
        },

        questionColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-channel.detail.questions.columnProductName'),
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'active',
                    label: this.$tc('sw-channel.detail.questions.columnActive'),
                    allowResize: true,
                    align: 'center',
                },
                {
                    property: 'questionNumber',
                    label: this.$tc('sw-channel.detail.questions.columnProductNumber'),
                    allowResize: true,
                },
            ];
        },

        assetFilter() {
            return HeyPanel.Filter.getByName('asset');
        },
    },

    watch: {
        channel: {
            deep: true,
            immediate: true,
            handler(newValue, oldValue) {
                if (!newValue || oldValue?.id === newValue.id) {
                    return;
                }

                this.getProducts();
            },
        },
    },

    methods: {
        getProducts() {
            if (!this.channel?.id) {
                return Promise.reject();
            }

            const context = { ...Context.api };
            context.inheritance = true;

            this.isLoading = true;
            return this.questionRepository
                .search(this.questionCriteria, context)
                .then((questions) => {
                    this.questions = questions;
                    this.total = questions.total;

                    if (this.total > 0 && this.questions.length <= 0) {
                        this.page = this.page === 1 ? 1 : this.page - 1;
                        this.getProducts();
                    }
                })
                .catch(() => {
                    this.questions = [];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onDeleteProduct(question) {
            const deleteId = this.getDeleteId(question);

            return this.questionVisibilityRepository
                .delete(deleteId, Context.api)
                .then(() => {
                    this.getProducts();

                    this.$refs.entityListing.resetSelection();
                })
                .catch((error) => {
                    if (error?.response?.data?.errors) {
                        this.showNotificationError(error.response.data.errors);

                        return;
                    }

                    this.createNotificationError({
                        message: error.message,
                    });
                });
        },

        onDeleteProducts() {
            const deleteIds = Object.values(this.questionSelection).map((question) => {
                return this.getDeleteId(question);
            });

            this.isLoading = true;
            return this.questionVisibilityRepository
                .syncDeleted(deleteIds, Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.getProducts();

                    this.$refs.entityListing.resetSelection();
                })
                .catch((error) => {
                    this.isLoading = false;

                    if (error?.response?.data?.data?.question_visibility?.result) {
                        this.showNotificationError(error.response.data.data.question_visibility.result);

                        return;
                    }

                    this.createNotificationError({
                        message: error.message,
                    });
                });
        },

        getDeleteId(question) {
            return question.visibilities.find((visibility) => {
                return visibility.channelId === this.channel.id;
            }).id;
        },

        showNotificationError(errors) {
            errors.forEach((error) => {
                if (error.errors) {
                    this.showNotificationError(error.errors);
                } else {
                    this.createNotificationError({
                        message: `${error.code}: ${error.detail}`,
                    });
                }
            });
        },

        onChangePage(data) {
            this.page = data.page;
            this.limit = data.limit;
            this.questions.criteria.sortings.forEach(({ field, naturalSorting, order }) => {
                this.questionCriteria.addSorting(Criteria.sort(field, order, naturalSorting));
            });

            this.getProducts();
        },

        onChangeSearchTerm(searchTerm) {
            this.searchTerm = searchTerm;

            if (searchTerm) {
                this.page = 1;
            }

            this.getProducts();
        },

        openAddProductsModal() {
            this.showProductsModal = true;
        },

        onAddProducts(questions) {
            if (questions.length <= 0) {
                this.showProductsModal = false;
                return Promise.reject();
            }

            const visibilities = new EntityCollection(
                this.questionVisibilityRepository.route,
                this.questionVisibilityRepository.entityName,
                Context.api,
            );

            questions.forEach((el) => {
                if (this.questions?.has(el.id)) {
                    return;
                }

                const visibility = this.questionVisibilityRepository.create(Context.api);
                Object.assign(visibility, {
                    visibility: 30,
                    questionId: el.id,
                    channelId: this.channel.id,
                    channel: this.channel,
                });

                visibilities.add(visibility);
            });

            this.isAssignProductLoading = true;

            return this.saveProductVisibilities(visibilities)
                .then(() => {
                    this.getProducts();
                })
                .catch((error) => {
                    this.createNotificationError({
                        message: error,
                    });
                })
                .finally(() => {
                    this.showProductsModal = false;
                    this.isAssignProductLoading = false;
                });
        },

        saveProductVisibilities(data) {
            if (data.length <= 0) {
                return Promise.resolve();
            }

            return this.questionVisibilityRepository.saveAll(data, Context.api);
        },

        isProductRemovable(question) {
            const relevantVisibility = question.visibilities.find(
                (visibility) => visibility.channelId === this.channel.id,
            );

            return question.parentId !== relevantVisibility?.questionId;
        },

        onProductSelectionChanged(selection) {
            this.questionSelection = selection;
        },
    },
};
