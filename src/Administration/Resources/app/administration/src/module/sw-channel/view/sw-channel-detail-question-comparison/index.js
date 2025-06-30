/**
 * @sw-package discovery
 */

import template from './sw-channel-detail-question-comparison.html.twig';
import './sw-channel-detail-question-comparison.scss';

const { Mixin } = HeyPanel;
const { Criteria } = HeyPanel.Data;
const { warn } = HeyPanel.Utils.debug;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: [
        'channelService',
        'repositoryFactory',
        'questionExportService',
        'entityMappingService',
        'acl',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder'),
    ],

    props: {
        // eslint-disable-next-line vue/require-prop-types
        channel: {
            required: true,
        },

        // eslint-disable-next-line vue/require-prop-types
        questionExport: {
            required: true,
        },

        isLoading: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            showDeleteModal: false,
            defaultSnippetSetId: '71a916e745114d72abafbfdc51cbd9d0',
            isLoadingDomains: false,
            deleteDomain: null,
            previewContent: null,
            previewErrors: null,
            isLoadingPreview: false,
            isPreviewSuccessful: false,
            isLoadingValidate: false,
            isValidateSuccessful: false,
        };
    },

    computed: {
        editorConfig() {
            return {
                enableBasicAutocompletion: true,
            };
        },

        questionExportRepository() {
            return this.repositoryFactory.create('question_export');
        },

        domainRepository() {
            return this.repositoryFactory.create(this.channel.domains.entity, this.channel.domains.source);
        },

        channelRepository() {
            return this.repositoryFactory.create('channel');
        },

        mainNavigationCriteria() {
            const criteria = new Criteria(1, 10);

            return criteria.addFilter(Criteria.equals('type', 'page'));
        },

        outerCompleterFunctionHeader() {
            return this.outerCompleterFunction({
                questionExport: 'question_export',
            });
        },

        outerCompleterFunctionBody() {
            return this.outerCompleterFunction({
                questionExport: 'question_export',
                question: 'question',
            });
        },

        outerCompleterFunctionFooter() {
            return this.outerCompleterFunction({
                questionExport: 'question_export',
            });
        },
    },

    methods: {
        validateTemplate() {
            const notificationValidateSuccess = {
                message: this.$tc('sw-channel.detail.questionComparison.notificationMessageValidateSuccessful'),
            };

            this.isLoadingValidate = true;

            this.questionExportService
                .validateProductExportTemplate(this.questionExport)
                .then((data) => {
                    this.isLoadingValidate = false;

                    if (data.errors) {
                        this.previewContent = data.content;
                        this.previewErrors = data.errors;
                        return;
                    }

                    this.createNotificationSuccess(notificationValidateSuccess);
                    this.isValidateSuccessful = true;
                })
                .catch((exception) => {
                    this.createNotificationError({
                        message: exception.response.data.errors[0].detail,
                    });
                    warn(this._name, exception.message, exception.response);

                    this.isLoadingValidate = false;
                    this.isValidateSuccessful = false;
                });
        },

        preview() {
            this.isLoadingPreview = true;

            this.questionExportService
                .previewProductExport(this.questionExport)
                .then((data) => {
                    this.isLoadingPreview = false;
                    this.previewContent = data.content;

                    if (data.errors) {
                        this.previewErrors = data.errors;
                        return;
                    }

                    this.isPreviewSuccessful = true;
                })
                .catch((exception) => {
                    this.createNotificationError({
                        message: exception.response.data.errors[0].detail,
                    });
                    warn(this._name, exception.message, exception.response);

                    this.isLoadingPreview = false;
                });
        },

        outerCompleterFunction(mapping) {
            const entityMappingService = this.entityMappingService;

            return function completerFunction(prefix) {
                const entityMapping = entityMappingService.getEntityMapping(prefix, mapping);
                return Object.keys(entityMapping).map((val) => {
                    return { value: val };
                });
            };
        },

        onPreviewClose() {
            this.previewContent = null;
            this.previewErrors = null;
            this.isPreviewSuccessful = false;
        },

        resetValid() {
            this.isValidateSuccessful = false;
        },
    },
};
