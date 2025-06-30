/**
 * @sw-package discovery
 */

import template from './sw-channel-modal.html.twig';
import './sw-channel-modal.scss';

const { Defaults } = HeyPanel;
const { Criteria } = HeyPanel.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['repositoryFactory'],

    emits: ['modal-close'],

    data() {
        return {
            detailType: null,
            questionStreamsExist: false,
            questionStreamsLoading: false,
        };
    },

    computed: {
        modalTitle() {
            if (this.detailType) {
                return this.$tc(
                    'sw-channel.modal.titleDetailPrefix',
                    {
                        name: this.detailType.name,
                    },
                    0,
                );
            }

            return this.$tc('sw-channel.modal.title');
        },

        questionStreamRepository() {
            return this.repositoryFactory.create('question_stream');
        },

        addChannelAction() {
            return {
                loading: (channelTypeId) => {
                    return this.isProductComparisonChannelType(channelTypeId) && this.questionStreamsLoading;
                },

                disabled: (channelTypeId) => {
                    return this.isProductComparisonChannelType(channelTypeId) && !this.questionStreamsExist;
                },
            };
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.questionStreamsLoading = true;
            this.questionStreamRepository.search(new Criteria(1, 1)).then((result) => {
                if (result.total > 0) {
                    this.questionStreamsExist = true;
                }
                this.questionStreamsLoading = false;
            });
        },

        onGridOpenDetails(detailType) {
            this.detailType = detailType;
        },

        onCloseModal() {
            this.$emit('modal-close');
        },

        onAddChannel(id) {
            this.onCloseModal();

            if (id) {
                this.$router.push({
                    name: 'sw.channel.create',
                    params: { typeId: id },
                });
            }
        },

        openRoute(route) {
            this.onCloseModal();

            this.$router.push(route);
        },

        isProductComparisonChannelType(channelTypeId) {
            return channelTypeId === Defaults.questionComparisonTypeId;
        },
    },
};
