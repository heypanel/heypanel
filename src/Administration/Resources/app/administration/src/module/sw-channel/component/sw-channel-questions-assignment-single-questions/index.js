/**
 * @sw-package discovery
 */

import template from './sw-channel-questions-assignment-single-questions.html.twig';
import './sw-channel-questions-assignment-single-questions.scss';

const { Mixin, Filter } = HeyPanel;
const { Criteria } = HeyPanel.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['repositoryFactory'],

    emits: ['selection-change'],

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
            questions: [],
            searchTerm: null,
            isLoading: false,
            page: 1,
            limit: 25,
            total: 0,
        };
    },

    computed: {
        questionRepository() {
            return this.repositoryFactory.create('question');
        },

        questionCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            if (this.searchTerm) {
                criteria.setTerm(this.searchTerm);
            }

            criteria.addAssociation('visibilities.channel');
            criteria.addFilter(
                Criteria.not('and', [
                    Criteria.equals('question.visibilities.channelId', this.channel.id),
                ]),
            );
            criteria.addFilter(Criteria.equals('parentId', null));

            return criteria;
        },

        questionColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-channel.detail.questions.columnProductName'),
                    allowResize: true,
                },
                {
                    property: 'questionNumber',
                    label: this.$tc('sw-channel.detail.questions.columnProductNumber'),
                    allowResize: true,
                },
            ];
        },

        assetFilter() {
            return Filter.getByName('asset');
        },
    },

    created() {
        this.getProducts();
    },

    methods: {
        getProducts() {
            this.isLoading = true;

            return this.questionRepository
                .search(this.questionCriteria)
                .then((questions) => {
                    this.questions = questions;
                    this.total = questions.total;
                })
                .catch((error) => {
                    this.questions = [];
                    this.total = 0;
                    this.createNotificationError({
                        message: error.message,
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onChangeSearchTerm(searchTerm) {
            this.searchTerm = searchTerm;

            if (searchTerm) {
                this.page = 1;
            }

            this.getProducts();
        },

        onSelectionChange(selection) {
            const questions = Object.values(selection);
            this.$emit('selection-change', questions, 'singleProducts');
        },

        onChangePage(data) {
            this.page = data.page;
            this.limit = data.limit;
            this.questions.criteria.sortings.forEach(({ field, naturalSorting, order }) => {
                this.questionCriteria.addSorting(Criteria.sort(field, order, naturalSorting));
            });

            this.getProducts();
        },
    },
};
