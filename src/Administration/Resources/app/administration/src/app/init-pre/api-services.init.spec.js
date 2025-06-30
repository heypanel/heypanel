/**
 * @sw-package framework
 */
import initializeApiServices from 'src/app/init-pre/api-services.init';

describe('src/app/init-pre/api-services.init.ts', () => {
    beforeEach(() => {
        HeyPanel._private.ApiServices = jest.fn(() => {
            const services = [];
            const serviceNames = [
                'aclApiService',
                'appActionButtonService',
                'appCmsBlocks',
                'appModulesService',
                'appUrlChangeService',
                'businessEventService',
                'cacheApiService',
                'calculate-price',
                'cartStoreService',
                'checkoutStoreService',
                'configService',
                'customSnippetApiService',
                'memberGroupRegistrationService',
                'memberValidationService',
                'documentService',
                'excludedSearchTermService',
                'extensionSdkService',
                'firstRunWizardService',
                'flowActionService',
                'importExportService',
                'integrationService',
                'knownIpsService',
                'languagePluginService',
                'mailService',
                'mediaFolderService',
                'mediaService',
                'messageQueueService',
                'notificationsService',
                'numberRangeService',
                'orderDocumentApiService',
                'orderStateMachineService',
                'orderService',
                'questionExportService',
                'questionStreamPreviewService',
                'promotionSyncService',
                'recommendationsService',
                'ruleConditionsConfigApiService',
                'channelService',
                'scheduledTaskService',
                'searchService',
                'seoUrlTemplateService',
                'seoUrlService',
                'snippetSetService',
                'snippetService',
                'stateMachineService',
                'contextStoreService',
                'storeService',
                'syncService',
                'systemConfigApiService',
                'tagApiService',
                'updateService',
                'userActivityApiService',
                'userConfigService',
                'userInputSanitizeService',
                'userRecoveryService',
                'userValidationService',
                'userService',
            ];

            serviceNames.forEach((serviceName) => {
                const MockApiService = jest.fn().mockImplementation(function () {
                    this.name = serviceName;
                });
                services.push(() => Promise.resolve({ default: MockApiService }));
            });

            return services;
        });
    });

    it('should initialize the api services', async () => {
        expect(HeyPanel.Service('aclApiService')).toBeUndefined();
        expect(HeyPanel.Service('appActionButtonService')).toBeUndefined();
        expect(HeyPanel.Service('appCmsBlocks')).toBeUndefined();
        expect(HeyPanel.Service('appModulesService')).toBeUndefined();
        expect(HeyPanel.Service('appUrlChangeService')).toBeUndefined();
        expect(HeyPanel.Service('businessEventService')).toBeUndefined();
        expect(HeyPanel.Service('cacheApiService')).toBeUndefined();
        expect(HeyPanel.Service('calculate-price')).toBeUndefined();
        expect(HeyPanel.Service('cartStoreService')).toBeUndefined();
        expect(HeyPanel.Service('checkoutStoreService')).toBeUndefined();
        expect(HeyPanel.Service('configService')).toBeUndefined();
        expect(HeyPanel.Service('customSnippetApiService')).toBeUndefined();
        expect(HeyPanel.Service('memberGroupRegistrationService')).toBeUndefined();
        expect(HeyPanel.Service('memberValidationService')).toBeUndefined();
        expect(HeyPanel.Service('documentService')).toBeUndefined();
        expect(HeyPanel.Service('excludedSearchTermService')).toBeUndefined();
        expect(HeyPanel.Service('extensionSdkService')).toBeUndefined();
        expect(HeyPanel.Service('firstRunWizardService')).toBeUndefined();
        expect(HeyPanel.Service('flowActionService')).toBeUndefined();
        expect(HeyPanel.Service('importExportService')).toBeUndefined();
        expect(HeyPanel.Service('integrationService')).toBeUndefined();
        expect(HeyPanel.Service('knownIpsService')).toBeUndefined();
        expect(HeyPanel.Service('languagePluginService')).toBeUndefined();
        expect(HeyPanel.Service('mailService')).toBeUndefined();
        expect(HeyPanel.Service('mediaFolderService')).toBeUndefined();
        expect(HeyPanel.Service('mediaService')).toBeUndefined();
        expect(HeyPanel.Service('messageQueueService')).toBeUndefined();
        expect(HeyPanel.Service('notificationsService')).toBeUndefined();
        expect(HeyPanel.Service('numberRangeService')).toBeUndefined();
        expect(HeyPanel.Service('orderDocumentApiService')).toBeUndefined();
        expect(HeyPanel.Service('orderStateMachineService')).toBeUndefined();
        expect(HeyPanel.Service('orderService')).toBeUndefined();
        expect(HeyPanel.Service('questionExportService')).toBeUndefined();
        expect(HeyPanel.Service('questionStreamPreviewService')).toBeUndefined();
        expect(HeyPanel.Service('promotionSyncService')).toBeUndefined();
        expect(HeyPanel.Service('recommendationsService')).toBeUndefined();
        expect(HeyPanel.Service('ruleConditionsConfigApiService')).toBeUndefined();
        expect(HeyPanel.Service('channelService')).toBeUndefined();
        expect(HeyPanel.Service('scheduledTaskService')).toBeUndefined();
        expect(HeyPanel.Service('searchService')).toBeUndefined();
        expect(HeyPanel.Service('seoUrlTemplateService')).toBeUndefined();
        expect(HeyPanel.Service('seoUrlService')).toBeUndefined();
        expect(HeyPanel.Service('snippetSetService')).toBeUndefined();
        expect(HeyPanel.Service('snippetService')).toBeUndefined();
        expect(HeyPanel.Service('stateMachineService')).toBeUndefined();
        expect(HeyPanel.Service('contextStoreService')).toBeUndefined();
        expect(HeyPanel.Service('storeService')).toBeUndefined();
        expect(HeyPanel.Service('syncService')).toBeUndefined();
        expect(HeyPanel.Service('systemConfigApiService')).toBeUndefined();
        expect(HeyPanel.Service('tagApiService')).toBeUndefined();
        expect(HeyPanel.Service('updateService')).toBeUndefined();
        expect(HeyPanel.Service('userActivityApiService')).toBeUndefined();
        expect(HeyPanel.Service('userConfigService')).toBeUndefined();
        expect(HeyPanel.Service('userInputSanitizeService')).toBeUndefined();
        expect(HeyPanel.Service('userRecoveryService')).toBeUndefined();
        expect(HeyPanel.Service('userValidationService')).toBeUndefined();
        expect(HeyPanel.Service('userService')).toBeUndefined();

        await initializeApiServices();

        expect(HeyPanel.Service('aclApiService')).toBeDefined();
        expect(HeyPanel.Service('appActionButtonService')).toBeDefined();
        expect(HeyPanel.Service('appCmsBlocks')).toBeDefined();
        expect(HeyPanel.Service('appModulesService')).toBeDefined();
        expect(HeyPanel.Service('appUrlChangeService')).toBeDefined();
        expect(HeyPanel.Service('businessEventService')).toBeDefined();
        expect(HeyPanel.Service('cacheApiService')).toBeDefined();
        expect(HeyPanel.Service('calculate-price')).toBeDefined();
        expect(HeyPanel.Service('cartStoreService')).toBeDefined();
        expect(HeyPanel.Service('checkoutStoreService')).toBeDefined();
        expect(HeyPanel.Service('configService')).toBeDefined();
        expect(HeyPanel.Service('customSnippetApiService')).toBeDefined();
        expect(HeyPanel.Service('memberGroupRegistrationService')).toBeDefined();
        expect(HeyPanel.Service('memberValidationService')).toBeDefined();
        expect(HeyPanel.Service('documentService')).toBeDefined();
        expect(HeyPanel.Service('excludedSearchTermService')).toBeDefined();
        expect(HeyPanel.Service('extensionSdkService')).toBeDefined();
        expect(HeyPanel.Service('firstRunWizardService')).toBeDefined();
        expect(HeyPanel.Service('flowActionService')).toBeDefined();
        expect(HeyPanel.Service('importExportService')).toBeDefined();
        expect(HeyPanel.Service('integrationService')).toBeDefined();
        expect(HeyPanel.Service('knownIpsService')).toBeDefined();
        expect(HeyPanel.Service('languagePluginService')).toBeDefined();
        expect(HeyPanel.Service('mailService')).toBeDefined();
        expect(HeyPanel.Service('mediaFolderService')).toBeDefined();
        expect(HeyPanel.Service('mediaService')).toBeDefined();
        expect(HeyPanel.Service('messageQueueService')).toBeDefined();
        expect(HeyPanel.Service('notificationsService')).toBeDefined();
        expect(HeyPanel.Service('numberRangeService')).toBeDefined();
        expect(HeyPanel.Service('orderDocumentApiService')).toBeDefined();
        expect(HeyPanel.Service('orderStateMachineService')).toBeDefined();
        expect(HeyPanel.Service('orderService')).toBeDefined();
        expect(HeyPanel.Service('questionExportService')).toBeDefined();
        expect(HeyPanel.Service('questionStreamPreviewService')).toBeDefined();
        expect(HeyPanel.Service('promotionSyncService')).toBeDefined();
        expect(HeyPanel.Service('recommendationsService')).toBeDefined();
        expect(HeyPanel.Service('ruleConditionsConfigApiService')).toBeDefined();
        expect(HeyPanel.Service('channelService')).toBeDefined();
        expect(HeyPanel.Service('scheduledTaskService')).toBeDefined();
        expect(HeyPanel.Service('searchService')).toBeDefined();
        expect(HeyPanel.Service('seoUrlTemplateService')).toBeDefined();
        expect(HeyPanel.Service('seoUrlService')).toBeDefined();
        expect(HeyPanel.Service('snippetSetService')).toBeDefined();
        expect(HeyPanel.Service('snippetService')).toBeDefined();
        expect(HeyPanel.Service('stateMachineService')).toBeDefined();
        expect(HeyPanel.Service('contextStoreService')).toBeDefined();
        expect(HeyPanel.Service('storeService')).toBeDefined();
        expect(HeyPanel.Service('syncService')).toBeDefined();
        expect(HeyPanel.Service('systemConfigApiService')).toBeDefined();
        expect(HeyPanel.Service('tagApiService')).toBeDefined();
        expect(HeyPanel.Service('updateService')).toBeDefined();
        expect(HeyPanel.Service('userActivityApiService')).toBeDefined();
        expect(HeyPanel.Service('userConfigService')).toBeDefined();
        expect(HeyPanel.Service('userInputSanitizeService')).toBeDefined();
        expect(HeyPanel.Service('userRecoveryService')).toBeDefined();
        expect(HeyPanel.Service('userValidationService')).toBeDefined();
        expect(HeyPanel.Service('userService')).toBeDefined();
    });
});
