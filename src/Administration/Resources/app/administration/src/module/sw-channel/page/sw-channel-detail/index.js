/**
 * @sw-package discovery
 */

import template from './sw-channel-detail.html.twig';

const { Mixin, Context, Defaults } = HeyPanel;
const { Criteria } = HeyPanel.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: [
        'repositoryFactory',
        'exportTemplateService',
        'acl',
        'feature',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
    },

    data() {
        return {
            channel: null,
            isLoading: false,
            customFieldSets: [],
            isSaveSuccessful: false,
            questionComparison: {
                newProductExport: null,
                questionComparisonAccessUrl: null,
                invalidFileName: false,
                templateOptions: [],
                templates: null,
                templateName: null,
                showTemplateModal: false,
                selectedTemplate: null,
            },
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.channel, 'name');
        },

        questionExport() {
            if (this.channel && this.channel.questionExports.first()) {
                return this.channel.questionExports.first();
            }

            if (this.questionComparison.newProductExport) {
                return this.questionComparison.newProductExport;
            }

            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.questionComparison.newProductExport = this.questionExportRepository.create();
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.questionComparison.newProductExport.interval = 0;
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.questionComparison.newProductExport.generateByCronjob = false;

            return this.questionComparison.newProductExport;
        },

        isStorefront() {
            if (!this.channel) {
                return this.$route.params.typeId === Defaults.storefrontChannelTypeId;
            }

            return this.channel.typeId === Defaults.storefrontChannelTypeId;
        },

        isProductComparison() {
            if (!this.channel) {
                return this.$route.params.typeId === Defaults.questionComparisonTypeId;
            }

            return this.channel.typeId === Defaults.questionComparisonTypeId;
        },

        isHeadless() {
            if (!this.channel) {
                return this.$route.params.typeId === Defaults.apiChannelTypeId;
            }

            return this.channel.typeId === Defaults.apiChannelTypeId;
        },

        channelRepository() {
            return this.repositoryFactory.create('channel');
        },

        channelAnalyticsRepository() {
            return this.repositoryFactory.create('channel_analytics');
        },

        customFieldRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        questionExportRepository() {
            return this.repositoryFactory.create('question_export');
        },

        storefrontChannelCriteria() {
            const criteria = new Criteria(1, 25);

            return criteria.addFilter(Criteria.equals('typeId', Defaults.storefrontChannelTypeId));
        },

        tooltipSave() {
            if (!this.allowSaving) {
                return {
                    message: this.$tc('sw-privileges.tooltip.warning'),
                    disabled: this.allowSaving,
                    showOnDisabledElements: true,
                };
            }

            const systemKey = this.$device.getSystemKey();

            return {
                message: `${systemKey} + S`,
                appearance: 'light',
            };
        },

        allowSaving() {
            return this.acl.can('channel.editor');
        },
    },

    watch: {
        '$route.params.id'() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            HeyPanel.ExtensionAPI.publishData({
                id: 'sw-channel-detail__channel',
                path: 'channel',
                scope: this,
            });
            this.loadEntityData();
            this.loadProductExportTemplates();
        },

        loadEntityData() {
            if (!this.$route.params.id) {
                return;
            }

            if (this.$route.params.typeId) {
                return;
            }

            if (this.channel) {
                this.channel = null;
            }

            this.loadChannel();
            this.loadCustomFieldSets();
        },

        loadChannel() {
            this.isLoading = true;
            this.channelRepository
                .get(this.$route.params.id.toLowerCase(), Context.api, this.getLoadChannelCriteria())
                .then((entity) => {
                    this.channel = entity;

                    // eslint-disable-next-line inclusive-language/use-inclusive-words
                    if (!this.channel.maintenanceIpWhitelist) {
                        // eslint-disable-next-line inclusive-language/use-inclusive-words
                        this.channel.maintenanceIpWhitelist = [];
                    }

                    this.generateAccessUrl();

                    this.isLoading = false;
                });
        },

        getLoadChannelCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addAssociation('paymentMethods');
            criteria.addAssociation('shippingMethods');
            criteria.addAssociation('countries');
            criteria.getAssociation('currencies').addSorting(Criteria.sort('name', 'ASC'));
            criteria.addAssociation('domains');
            criteria.getAssociation('languages').addSorting(Criteria.sort('name', 'ASC'));
            criteria.addAssociation('analytics');

            criteria.addAssociation('questionExports');
            criteria.addAssociation('questionExports.channelDomain.channel');

            criteria.getAssociation('domains.language').addSorting(Criteria.sort('name', 'ASC'));
            criteria.getAssociation('domains.snippetSet').addSorting(Criteria.sort('name', 'ASC'));
            criteria.addAssociation('domains.currency');
            criteria.addAssociation('domains.questionExports');

            return criteria;
        },

        onTemplateSelected(templateName) {
            if (this.questionComparison.templates === null || this.questionComparison.templates[templateName] === undefined) {
                return;
            }

            this.questionComparison.selectedTemplate = this.questionComparison.templates[templateName];
            const contentChanged = Object.keys(this.questionComparison.selectedTemplate).some((value) => {
                return this.questionExport[value] !== this.questionComparison.selectedTemplate[value];
            });

            if (!contentChanged) {
                return;
            }

            this.questionComparison.showTemplateModal = true;
        },

        onTemplateModalClose() {
            this.questionComparison.selectedTemplate = null;
            this.questionComparison.templateName = null;
            this.questionComparison.showTemplateModal = false;
        },

        onTemplateModalConfirm() {
            Object.keys(this.questionComparison.selectedTemplate).forEach((value) => {
                this.questionExport[value] = this.questionComparison.selectedTemplate[value];
            });
            this.onTemplateModalClose();

            this.createNotificationInfo({
                message: this.$tc('sw-channel.detail.questionComparison.templates.message.template-applied-message'),
            });
        },

        loadCustomFieldSets() {
            const criteria = new Criteria(1, 100);

            criteria.addFilter(Criteria.equals('relations.entityName', 'channel'));
            criteria.getAssociation('customFields').addSorting(Criteria.sort('config.customFieldPosition', 'ASC', true));

            this.customFieldRepository.search(criteria, Context.api).then((searchResult) => {
                this.customFieldSets = searchResult;
            });
        },

        generateAccessUrl() {
            if (!this.questionExport.channelDomain) {
                this.questionComparison.questionComparisonAccessUrl = '';
                return;
            }

            const domainUrl = this.questionExport.channelDomain.url.replace(/\/+$/g, '');
            // eslint-disable-next-line max-len
            this.questionComparison.questionComparisonAccessUrl = `${domainUrl}/client-api/question-export/${this.questionExport.accessKey}/${this.questionExport.fileName}`;
        },

        loadProductExportTemplates() {
            this.questionComparison.templateOptions = Object.values(
                this.exportTemplateService.getProductExportTemplateRegistry(),
            );
            this.questionComparison.templates = this.exportTemplateService.getProductExportTemplateRegistry();
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        },

        setInvalidFileName(invalidFileName) {
            this.questionComparison.invalidFileName = invalidFileName;
        },

        async onSave() {
            this.isLoading = true;

            this.isSaveSuccessful = false;
            if (this.isProductComparison && !this.channel.questionExports.length) {
                this.channel.questionExports.add(this.questionExport);
            }

            const analyticsId = this.updateAnalytics();

            try {
                await this.channelRepository.save(this.channel, Context.api);

                if (analyticsId && !this.channel?.analytics?.trackingId) {
                    await this.channelAnalyticsRepository.delete(analyticsId, Context.api);
                }

                this.isLoading = false;
                this.isSaveSuccessful = true;

                HeyPanel.Utils.EventBus.emit('sw-channel-detail-channel-change');
                this.loadEntityData();
            } catch (error) {
                this.isLoading = false;

                this.createNotificationError({
                    message: this.$tc(
                        'sw-channel.detail.messageSaveError',
                        {
                            name: this.channel.name || this.placeholder(this.channel, 'name'),
                        },
                        0,
                    ),
                });
            }
        },

        updateAnalytics() {
            const analyticsId = this.channel.analyticsId;
            if (analyticsId && !this.channel?.analytics?.trackingId) {
                this.channel.analyticsId = null;
                delete this.channel.analytics;
            }

            return analyticsId;
        },

        abortOnLanguageChange() {
            return this.channelRepository.hasChanges(this.channel);
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        onChangeLanguage() {
            this.loadEntityData();
        },
    },
};
