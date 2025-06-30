import template from './sw-category-detail-questions.html.twig';
import './sw-category-detail-questions.scss';

const { Criteria } = HeyPanel.Data;
const { mapPropertyErrors } = HeyPanel.Component.getComponentHelper();
const HeyPanelError = HeyPanel.Classes.HeyPanelError;

/**
 * @sw-package discovery
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        'placeholder',
    ],

    props: {
        isLoading: {
            type: Boolean,
            required: true,
        },
    },

    data() {
        return {
            questionStreamFilter: null,
            questionStreamInvalid: false,
            manualAssignedProductsCount: 0,
            parentProducts: [],
        };
    },

    computed: {
        category() {
            return HeyPanel.Store.get('swCategoryDetail').category;
        },

        questionStreamRepository() {
            return this.repositoryFactory.create('question_stream');
        },

        questionRepository() {
            return this.repositoryFactory.create('question');
        },

        questionColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-category.base.questions.columnNameLabel'),
                    dataIndex: 'name',
                    routerLink: 'sw.question.detail',
                    sortable: false,
                },
                {
                    property: 'manufacturer.name',
                    label: this.$tc('sw-category.base.questions.columnManufacturerLabel'),
                    routerLink: 'sw.manufacturer.detail',
                    sortable: false,
                },
            ];
        },

        manufacturerColumn() {
            return 'column-manufacturer.name';
        },

        nameColumn() {
            return 'column-name';
        },

        questionCriteria() {
            return new Criteria(1, 10).addAssociation('options.group').addAssociation('manufacturer');
        },

        questionStreamInvalidError() {
            if (this.questionStreamInvalid) {
                return new HeyPanelError({
                    code: 'PRODUCT_STREAM_INVALID',
                    detail: this.$tc('sw-category.base.questions.dynamicProductGroupInvalidMessage'),
                });
            }
            return null;
        },

        ...mapPropertyErrors('category', [
            'questionStreamId',
            'questionAssignmentType',
        ]),

        questionAssignmentTypes() {
            return [
                {
                    value: 'question',
                    label: this.$tc('sw-category.base.questions.questionAssignmentTypeManualLabel'),
                },
                {
                    value: 'question_stream',
                    label: this.$tc('sw-category.base.questions.questionAssignmentTypeStreamLabel'),
                },
            ];
        },

        dynamicProductGroupHelpText() {
            const link = {
                name: 'sw.question.stream.index',
            };

            const helpText = this.$tc(
                'sw-category.base.questions.dynamicProductGroupHelpText.label',
                {
                    link: `<sw-internal-link
                           :router-link=${JSON.stringify(link)}
                           :inline="true">
                           ${this.$tc('sw-category.base.questions.dynamicProductGroupHelpText.linkText')}
                       </sw-internal-link>`,
                },
                0,
            );

            try {
                // eslint-disable-next-line no-new
                new URL(this.$tc('sw-category.base.questions.dynamicProductGroupHelpText.videoUrl'));
            } catch {
                return helpText;
            }

            return `${helpText}
                    <br>
                    <sw-external-link
                        href="${this.$tc('sw-category.base.questions.dynamicProductGroupHelpText.videoUrl')}">
                        ${this.$tc('sw-category.base.questions.dynamicProductGroupHelpText.videoLink')}
                    </sw-external-link>`;
        },

        assetFilter() {
            return HeyPanel.Filter.getByName('asset');
        },
    },

    watch: {
        'category.questionStreamId'(id) {
            if (!id) {
                this.questionStreamFilter = null;
                return;
            }
            this.loadProductStreamPreview();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (!this.category.questionStreamId) {
                return;
            }
            this.loadProductStreamPreview();
        },

        loadProductStreamPreview() {
            this.questionStreamRepository
                .get(this.category.questionStreamId)
                .then((response) => {
                    this.questionStreamFilter = response.apiFilter;
                    this.questionStreamInvalid = response.invalid;
                })
                .catch(() => {
                    this.questionStreamFilter = null;
                    this.questionStreamInvalid = true;
                });
        },

        onPaginateManualProductAssignment(assignment) {
            this.getParentProducts(assignment);

            this.manualAssignedProductsCount = assignment.total;
        },

        getParentProducts(questions) {
            const parentIds = questions.map((question) => question.parentId).filter((id) => id !== null);

            if (parentIds.length > 0) {
                const criteria = new Criteria(1, parentIds.length)
                    .addAssociation('manufacturer')
                    .addFilter(Criteria.equalsAny('id', parentIds));

                this.questionRepository.search(criteria).then((parentProducts) => {
                    this.parentProducts = parentProducts;
                });
            }
        },

        getItemName(question) {
            const name = question.name ? question.name : question.translated.name;
            if (name) {
                return name;
            }

            const parent = this.parentProducts.find((parentProduct) => {
                return parentProduct.id === question.parentId;
            });

            if (parent) {
                return parent.name ? parent.name : question.translated.name;
            }

            return null;
        },

        getManufacturer(question) {
            if (question.manufacturerId) {
                return question.manufacturer;
            }

            const parent = this.parentProducts.find((parentProduct) => {
                return parentProduct.id === question.parentId;
            });

            if (parent && parent.manufacturerId) {
                return parent.manufacturer;
            }

            return null;
        },
    },
};
