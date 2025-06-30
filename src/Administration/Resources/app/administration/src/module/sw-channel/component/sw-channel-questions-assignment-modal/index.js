/**
 * @sw-package discovery
 */

import template from './sw-channel-questions-assignment-modal.html.twig';
import './sw-channel-questions-assignment-modal.scss';

const { uniqBy } = HeyPanel.Utils.array;

const updateElementVisibility = (element, binding) => {
    element.style.visibility = binding.value ? 'visible' : 'hidden';
    element.style.position = binding.value ? 'static' : 'absolute';
    element.style.top = binding.value ? 'auto' : '0';
    element.style.left = binding.value ? 'auto' : '0';
    element.style.bottom = binding.value ? 'auto' : '0';
    element.style.right = binding.value ? 'auto' : '0';
    element.style.transform = binding.value ? 'translateX(0)' : 'translateX(100%)';
};

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    emits: [
        'modal-close',
        'questions-add',
    ],

    directives: {
        hide: {
            beforeMount: updateElementVisibility,
            updated: updateElementVisibility,
        },
    },

    props: {
        channel: {
            type: Object,
            required: true,
        },

        isAssignProductLoading: {
            type: Boolean,
            required: true,
        },
    },

    data() {
        return {
            singleProducts: [],
            categoryProducts: [],
            groupProducts: [],
            isProductLoading: false,
            tabContentHeight: '600px',
            questionContainerStyle: {
                display: 'grid',
                placeItems: 'stretch',
            },
            categoryContainerStyle: {
                display: 'grid',
                placeItems: 'stretch',
            },
            questionGroupContainerStyle: {
                display: 'grid',
                placeItems: 'stretch',
            },
        };
    },

    computed: {
        questionCount() {
            return this.questions.length;
        },

        questions() {
            return uniqBy(
                [
                    ...this.singleProducts,
                    ...this.categoryProducts,
                    ...this.groupProducts,
                ],
                'id',
            );
        },
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        mountedComponent() {
            this.getProductContainerStyle();
            this.getCategoryContainerStyle();
            this.getProductGroupContainerStyle();
        },

        getProductContainerStyle() {
            // eslint-disable-next-line max-len
            const cardSectionSecondaryHeight = `${this.$refs?.question?.$refs?.cardSectionSecondary?.$el?.offsetHeight ?? 0}px`;

            this.questionContainerStyle['grid-template-rows'] =
                `auto calc(${this.tabContentHeight} - ${cardSectionSecondaryHeight})`;
        },

        getCategoryContainerStyle() {
            const tabContentGutter = '20px';
            const alertHeight = `${this.$refs?.category?.$refs?.alert?.$el?.offsetHeight ?? 0}px`;
            // eslint-disable-next-line max-len
            const cardSectionSecondaryHeight = `${this.$refs?.category?.$refs?.cardSectionSecondary?.$el?.offsetHeight ?? 0}px`;

            this.categoryContainerStyle['grid-template-rows'] =
                `auto calc(${this.tabContentHeight} - (${tabContentGutter} + ${alertHeight} + ${
                    cardSectionSecondaryHeight
                }))`;
        },

        getProductGroupContainerStyle() {
            const tabContentGutter = '20px';
            const alertHeight = `${this.$refs?.questionGroup?.$refs?.alert?.$el?.offsetHeight ?? 0}px`;
            // eslint-disable-next-line max-len
            const cardSectionSecondaryHeight = `${this.$refs?.questionGroup?.$refs?.cardSectionSecondary?.$el?.offsetHeight ?? 0}px`;

            this.questionGroupContainerStyle['grid-template-rows'] =
                `auto calc(${this.tabContentHeight} - (${tabContentGutter} + ${alertHeight} + ${
                    cardSectionSecondaryHeight
                }))`;
        },

        onChangeSelection(questions, type) {
            this[type] = questions;
        },

        onCloseModal() {
            this.$emit('modal-close');
        },

        onAddProducts() {
            this.$emit('questions-add', this.questions);
        },

        setProductLoading(isProductLoading) {
            this.isProductLoading = isProductLoading;
        },
    },
};
