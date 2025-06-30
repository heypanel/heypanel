/**
 * @sw-package inventory
 */

import template from './sw-question-stream-grid-preview.html.twig';
import './sw-question-stream-grid-preview.scss';

const { Context, Defaults } = HeyPanel;
const { Criteria } = HeyPanel.Data;

/**
 * @private
 */
export default {
    template,

    inject: [
        'repositoryFactory',
        'questionStreamPreviewService',
    ],

    emits: ['selection-change'],

    props: {
        /**
         * The apiFilter of a loaded question stream
         */
        // eslint-disable-next-line vue/require-prop-types
        filters: {
            required: true,
        },
        columns: {
            required: false,
            type: Array,
            default() {
                return [];
            },
        },
        criteria: {
            required: false,
            type: Object,
            default() {
                return new Criteria(1, 10);
            },
        },
        showSelection: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            questions: [],
            systemCurrency: null,
            searchTerm: '',
            page: 1,
            total: 0,
            limit: 10,
            isLoading: false,
        };
    },

    computed: {
        questionRepository() {
            return this.repositoryFactory.create('question');
        },

        currencyRepository() {
            return this.repositoryFactory.create('currency');
        },

        channelRepository() {
            return this.repositoryFactory.create('channel');
        },

        channelCriteria() {
            return new Criteria(1, 1)
                .addFilter(
                    Criteria.not('OR', [
                        Criteria.equals('typeId', Defaults.questionComparisonTypeId),
                    ]),
                )
                .addSorting(Criteria.sort('type.iconName', 'ASC'));
        },

        defaultColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-question-stream.filter.values.question'),
                    type: 'text',
                    routerLink: 'sw.question.detail',
                },
                {
                    property: 'manufacturer.name',
                    label: this.$tc('sw-question-stream.filter.values.manufacturer'),
                },
                {
                    property: 'active',
                    label: this.$tc('sw-question-stream.filter.values.active'),
                    align: 'center',
                    type: 'bool',
                },
                {
                    property: 'price',
                    label: this.$tc('sw-question-stream.filter.values.price'),
                },
                {
                    property: 'stock',
                    label: this.$tc('sw-question-stream.filter.values.stock'),
                    align: 'right',
                },
            ];
        },

        questionColumns() {
            if (this.columns.length) {
                return this.columns;
            }

            return this.defaultColumns;
        },

        emptyStateMessage() {
            if (!this.filters) {
                return this.$tc('global.entity-components.questionStreamPreview.emptyMessageNoStream');
            }

            if (this.searchTerm.length) {
                return this.$tc(
                    'global.entity-components.questionStreamPreview.emptyMessageNoSearchResults',
                    this.searchTerm,
                    {
                        term: this.searchTerm,
                    },
                );
            }

            return this.$tc('global.entity-components.questionStreamPreview.emptyMessageNoProducts');
        },

        assetFilter() {
            return HeyPanel.Filter.getByName('asset');
        },

        currencyFilter() {
            return HeyPanel.Filter.getByName('currency');
        },
    },

    watch: {
        async filters(filtersValue) {
            if (!filtersValue) {
                this.total = 0;
                return;
            }

            this.isLoading = true;
            this.systemCurrency = await this.loadSystemDefaultCurrency();
            this.loadProducts();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        onSearchTermChange(searchTerm) {
            this.searchTerm = searchTerm;
            this.page = 1;
            this.loadProducts();
        },
        async createdComponent() {
            if (!this.filters) {
                return;
            }

            this.isLoading = true;
            this.systemCurrency = await this.loadSystemDefaultCurrency();
            this.loadProducts();
        },

        loadSystemDefaultCurrency() {
            return this.currencyRepository.get(Context.app.systemCurrencyId, Context.api);
        },

        loadProducts() {
            // eslint-disable-next-line vue/no-mutating-props
            this.criteria.term = this.searchTerm || null;
            // eslint-disable-next-line vue/no-mutating-props
            this.criteria.filters = [...this.filters];
            // eslint-disable-next-line vue/no-mutating-props
            this.criteria.limit = this.limit;
            this.criteria.setPage(this.page);
            this.criteria.addAssociation('manufacturer');
            this.criteria.addAssociation('options.group');
            this.criteria.addGroupField('displayGroup');
            this.criteria.addFilter(
                Criteria.not('AND', [
                    Criteria.equals('displayGroup', null),
                ]),
            );

            return this.channelRepository
                .searchIds(this.channelCriteria)
                .then(({ data }) => {
                    return this.questionStreamPreviewService.preview(data.at(0), this.criteria, [], {
                        'sw-currency-id': Context.app.systemCurrencyId,
                        'sw-inheritance': true,
                    });
                })
                .then((result) => {
                    this.questions = Object.values(result.elements);
                    this.total = result.total;
                    this.isLoading = false;
                });
        },

        onPageChange({ page = 1, limit = 25 }) {
            this.page = page;
            this.limit = limit;
            this.isLoading = true;

            this.loadProducts();
        },

        getPriceForDefaultCurrency(question) {
            const price = question.price.find((questionPrice) => {
                return questionPrice.currencyId === this.systemCurrency.id;
            });

            return price ? price.gross : '-';
        },

        onSelectionChange(questions) {
            this.$emit('selection-change', questions);
        },
    },
};
