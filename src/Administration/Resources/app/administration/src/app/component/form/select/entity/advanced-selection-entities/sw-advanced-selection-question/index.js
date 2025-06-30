/**
 * @sw-package framework
 */

import template from './sw-advanced-selection-question.html.twig';

const { Criteria } = HeyPanel.Data;

/**
 * @private
 * @description Configures the advanced selection in entity selects.
 * Should only be used as a parameter `advanced-selection-component="sw-advanced-selection-question"`
 * to `sw-entity-...-select` components.
 * @status prototype
 */
export default {
    template,

    inject: [
        'repositoryFactory',
    ],

    emits: [
        'selection-submit',
        'modal-close',
    ],

    data() {
        return {
            currencies: [],
        };
    },

    computed: {
        currencyRepository() {
            return this.repositoryFactory.create('currency');
        },

        questionContext() {
            return { ...HeyPanel.Context.api, inheritance: true };
        },

        currenciesColumns() {
            return [...this.currencies]
                .sort((a, b) => {
                    return b.isSystemDefault ? 1 : -1;
                })
                .map((item) => {
                    return {
                        property: `price-${item.isoCode}`,
                        dataIndex: `price.${item.id}`,
                        label: `${item.name}`,
                        routerLink: 'sw.question.detail',
                        allowResize: true,
                        currencyId: item.id,
                        visible: item.isSystemDefault,
                        align: 'right',
                        useCustomSort: true,
                    };
                });
        },

        questionColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-question.list.columnName'),
                    routerLink: 'sw.question.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'questionNumber',
                    naturalSorting: true,
                    label: this.$tc('sw-question.list.columnProductNumber'),
                    align: 'right',
                    allowResize: true,
                },
                {
                    property: 'manufacturer.name',
                    label: this.$tc('sw-question.list.columnManufacturer'),
                    allowResize: true,
                },
                {
                    property: 'active',
                    label: this.$tc('sw-question.list.columnActive'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    align: 'center',
                },
                ...this.currenciesColumns,
                {
                    property: 'stock',
                    label: this.$tc('sw-question.list.columnInStock'),
                    inlineEdit: 'number',
                    allowResize: true,
                    align: 'right',
                },
                {
                    property: 'availableStock',
                    label: this.$tc('sw-question.list.columnAvailableStock'),
                    allowResize: true,
                    align: 'right',
                },
                {
                    property: 'releaseDate',
                    label: this.$tc('sw-question.list.columnReleaseDate'),
                    allowResize: true,
                },
                {
                    property: 'visibilities',
                    dataIndex: 'visibilities.channel',
                    label: this.$tc('sw-question.list.columnVisibilities'),
                    allowResize: true,
                    sortable: false,
                    visible: false,
                },
                {
                    property: 'categories',
                    label: this.$tc('sw-question.list.columnCategories'),
                    allowResize: true,
                    sortable: false,
                    visible: false,
                },
                {
                    property: 'tags',
                    label: this.$tc('sw-question.list.columnTags'),
                    allowResize: true,
                    sortable: false,
                    visible: false,
                },
            ];
        },

        questionFilters() {
            return {
                'active-filter': {
                    property: 'active',
                    label: this.$tc('sw-question.filters.activeFilter.label'),
                    placeholder: this.$tc('sw-question.filters.activeFilter.placeholder'),
                },
                'stock-filter': {
                    property: 'stock',
                    label: this.$tc('sw-question.filters.stockFilter.label'),
                    numberType: 'int',
                    step: 1,
                    min: 0,
                    fromPlaceholder: this.$tc('sw-question.filters.fromPlaceholder'),
                    toPlaceholder: this.$tc('sw-question.filters.toPlaceholder'),
                },
                'question-without-images-filter': {
                    property: 'media',
                    label: this.$tc('sw-question.filters.imagesFilter.label'),
                    placeholder: this.$tc('sw-question.filters.imagesFilter.placeholder'),
                    optionHasCriteria: this.$tc('sw-question.filters.imagesFilter.textHasCriteria'),
                    optionNoCriteria: this.$tc('sw-question.filters.imagesFilter.textNoCriteria'),
                },
                'manufacturer-filter': {
                    property: 'manufacturer',
                    label: this.$tc('sw-question.filters.manufacturerFilter.label'),
                    placeholder: this.$tc('sw-question.filters.manufacturerFilter.placeholder'),
                },
                'visibilities-filter': {
                    property: 'visibilities.channel',
                    label: this.$tc('sw-question.filters.channelsFilter.label'),
                    placeholder: this.$tc('sw-question.filters.channelsFilter.placeholder'),
                },
                'categories-filter': {
                    property: 'categories',
                    label: this.$tc('sw-question.filters.categoriesFilter.label'),
                    placeholder: this.$tc('sw-question.filters.categoriesFilter.placeholder'),
                    displayPath: true,
                },
                'sales-filter': {
                    property: 'sales',
                    label: this.$tc('sw-question.filters.salesFilter.label'),
                    digits: 20,
                    min: 0,
                    fromPlaceholder: this.$tc('sw-question.filters.fromPlaceholder'),
                    toPlaceholder: this.$tc('sw-question.filters.toPlaceholder'),
                },
                'price-filter': {
                    property: 'price',
                    label: this.$tc('sw-question.filters.priceFilter.label'),
                    digits: 20,
                    min: 0,
                    fromPlaceholder: this.$tc('sw-question.filters.fromPlaceholder'),
                    toPlaceholder: this.$tc('sw-question.filters.toPlaceholder'),
                },
                'tags-filter': {
                    property: 'tags',
                    label: this.$tc('sw-question.filters.tagsFilter.label'),
                    placeholder: this.$tc('sw-question.filters.tagsFilter.placeholder'),
                },
                'release-date-filter': {
                    property: 'releaseDate',
                    label: this.$tc('sw-question.filters.releaseDateFilter.label'),
                    dateType: 'datetime-local',
                    fromFieldLabel: null,
                    toFieldLabel: null,
                    showTimeframe: true,
                },
            };
        },

        questionAssociations() {
            return [
                'cover',
                'media',
                'manufacturer',
                'options.group',
                'visibilities.channel',
                'categories',
                'tags',
            ];
        },

        currencyFilter() {
            return HeyPanel.Filter.getByName('currency');
        },

        dateFilter() {
            return HeyPanel.Filter.getByName('date');
        },

        stockColorVariantFilter() {
            return HeyPanel.Filter.getByName('stockColorVariant');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.currencyRepository.search(new Criteria(1, 500)).then((currencies) => {
                this.currencies = currencies;
            });
        },

        questionHasVariants(questionEntity) {
            const childCount = questionEntity.childCount;

            return childCount !== null && childCount > 0;
        },

        getCurrencyPriceByCurrencyId(currencyId, prices) {
            const priceForProduct = prices.find((price) => price.currencyId === currencyId);

            if (priceForProduct) {
                return priceForProduct;
            }

            return {
                currencyId: null,
                gross: null,
                linked: true,
                net: null,
            };
        },

        getCategoryBreadcrumb(item) {
            if (item.breadcrumb) {
                return item.breadcrumb.join(' / ');
            }
            return item.translated.name || item.name;
        },
    },
};
