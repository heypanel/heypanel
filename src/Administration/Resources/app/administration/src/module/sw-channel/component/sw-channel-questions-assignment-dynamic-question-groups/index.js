/**
 * @sw-package discovery
 */

import template from './sw-channel-questions-assignment-dynamic-question-groups.html.twig';
import './sw-channel-questions-assignment-dynamic-question-groups.scss';

const { Mixin } = HeyPanel;
const { Criteria } = HeyPanel.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['repositoryFactory'],

    emits: [
        'selection-change',
        'question-loading',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        channel: {
            type: Object,
            required: true,
        },

        containerStyle: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            questionStreams: [],
            questionStreamFilter: [],
            isProductStreamsLoading: false,
            isProductLoading: false,
            page: 1,
            limit: 10,
            total: 0,
            term: null,
        };
    },

    computed: {
        questionRepository() {
            return this.repositoryFactory.create('question');
        },

        questionStreamRepository() {
            return this.repositoryFactory.create('question_stream');
        },

        questionCriteria() {
            const criteria = new Criteria(1, 500);

            criteria.filters = this.questionStreamFilter;
            criteria.addAssociation('visibilities.channel');
            criteria.addFilter(
                Criteria.not('AND', [
                    Criteria.equals('question.visibilities.channelId', this.channel.id),
                ]),
            );

            return criteria;
        },

        questionStreamCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            if (this.term) {
                criteria.setTerm(this.term);
            }

            return criteria;
        },

        questionStreamColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-channel.detail.questionAssignmentModal.dynamicProductGroups.columnName'),
                    sortable: false,
                },
            ];
        },

        assetFilter() {
            return HeyPanel.Filter.getByName('asset');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getProductStreams();
        },

        getProductStreams() {
            this.isProductStreamsLoading = true;

            return this.questionStreamRepository
                .search(this.questionStreamCriteria)
                .then((questionStreams) => {
                    this.questionStreams = questionStreams;
                    this.total = questionStreams.total;
                })
                .catch(() => {
                    this.questionStreams = [];
                    this.total = 0;
                })
                .finally(() => {
                    this.isProductStreamsLoading = false;
                });
        },

        onSearch(term) {
            this.term = term;

            if (term) {
                this.page = 1;
            }

            this.getProductStreams();
        },

        onPaginate(data) {
            this.page = data.page;
            this.limit = data.limit;
            this.getProductStreams();
        },

        onOpen(questionStream) {
            const route = this.$router.resolve({
                name: 'sw.question.stream.detail',
                params: { id: questionStream.id },
            });

            window.open(route.href, '_blank');
        },

        async onSelect(questionStreams) {
            if (Object.keys(questionStreams).length <= 0) {
                this.$emit('selection-change', [], 'groupProducts');
                return;
            }

            try {
                const questions = await this.getProductsFromProductStreams(questionStreams);
                this.$emit('selection-change', questions, 'groupProducts');
            } catch (error) {
                this.createNotificationError({ message: error.message });
            }
        },

        getProductsFromProductStreams(questionStreams) {
            const promises = Object.keys(questionStreams).map((id) => {
                return this.getProductStreamFilter(id).then(() => this.getProducts());
            });

            this.$emit('question-loading', true);
            this.isProductLoading = true;

            return Promise.all(promises)
                .then((values) => {
                    const questions = values.flat();
                    return questions;
                })
                .finally(() => {
                    this.$emit('question-loading', false);
                    this.isProductLoading = false;
                });
        },

        getProductStreamFilter(id) {
            return this.questionStreamRepository
                .get(id)
                .then((questionStreamFilter) => {
                    this.questionStreamFilter = questionStreamFilter.apiFilter;
                })
                .catch((error) => {
                    this.questionStreamFilter = [];
                    return error;
                });
        },

        getProducts() {
            return this.questionRepository.search(this.questionCriteria).then((questions) => {
                return questions;
            });
        },
    },
};
