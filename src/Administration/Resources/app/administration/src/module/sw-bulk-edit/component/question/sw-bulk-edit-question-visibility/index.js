/**
 * @sw-package inventory
 */
import template from './sw-bulk-edit-question-visibility.html.twig';

const { Context } = HeyPanel;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['repositoryFactory'],

    props: {
        bulkEditProduct: {
            type: Object,
            required: true,
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data() {
        return {
            displayVisibilityDetail: false,
        };
    },

    computed: {
        question() {
            return HeyPanel.Store.get('swProductDetail').question;
        },

        questionVisibilityRepository() {
            return this.repositoryFactory.create(this.question.visibilities.entity);
        },
    },

    methods: {
        visibilitiesRemoveInheritanceFunction(newValue) {
            newValue.forEach(({ questionVersionId, channelId, channel, visibility }) => {
                const visibilities = this.questionVisibilityRepository.create(Context.api);

                Object.assign(visibilities, {
                    questionId: this.question.id,
                    questionVersionId,
                    channelId,
                    channel,
                    visibility,
                });

                this.question.visibilities.push(visibilities);
            });

            this.$refs.questionVisibilitiesInheritance.forceInheritanceRemove = true;

            return this.question.visibilities;
        },
        openModal() {
            this.displayVisibilityDetail = true;
        },

        closeModal() {
            this.displayVisibilityDetail = false;
        },
    },
};
