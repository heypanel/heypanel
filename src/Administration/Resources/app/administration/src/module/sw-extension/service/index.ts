import type { SubContainer } from 'src/global.types';

import type { App } from 'vue';
import ExtensionStoreActionService from './extension-store-action.service';
import HeyPanelExtensionService from './heypanel-extension.service';
import ExtensionErrorService from './extension-error.service';

const { Application } = HeyPanel;

/**
 * @sw-package checkout
 */
declare global {
    interface ServiceContainer extends SubContainer<'service'> {
        extensionStoreActionService: ExtensionStoreActionService;
        heypanelExtensionService: HeyPanelExtensionService;
        extensionErrorService: ExtensionErrorService;
    }
}

Application.addServiceProvider('extensionStoreActionService', () => {
    return new ExtensionStoreActionService(
        HeyPanel.Application.getContainer('init').httpClient,
        HeyPanel.Service('loginService'),
    );
});

Application.addServiceProvider('heypanelExtensionService', () => {
    return new HeyPanelExtensionService(
        HeyPanel.Service('extensionStoreActionService'),
        HeyPanel.Service('heypanelDiscountCampaignService'),
        HeyPanel.Service('storeService'),
    );
});

Application.addServiceProvider('extensionErrorService', () => {
    const root = HeyPanel.Application.getApplicationRoot() as App<Element>;

    return new ExtensionErrorService(
        {
            FRAMEWORK__APP_LICENSE_COULD_NOT_BE_VERIFIED: {
                title: 'sw-extension.errors.appLicenseCouldNotBeVerified.title',
                message: 'sw-extension.errors.appLicenseCouldNotBeVerified.message',
                autoClose: false,
                actions: [
                    {
                        label: root.$tc('sw-extension.errors.appLicenseCouldNotBeVerified.actionSetLicenseDomain'),
                        method: () => {
                            void root.$router.push({
                                name: 'sw.settings.store.index',
                            });
                        },
                    },
                    {
                        label: root.$tc('sw-extension.errors.appLicenseCouldNotBeVerified.actionLogin'),
                        method: () => {
                            void root.$router.push({
                                name: 'sw.extension.my-extensions.account',
                            });
                        },
                    },
                ],
            },
            FRAMEWORK__APP_NOT_COMPATIBLE: {
                title: 'global.default.error',
                message: 'sw-extension.errors.appIsNotCompatible',
            },
        },
        {
            title: 'global.default.error',
            message: 'global.notification.unspecifiedSaveErrorMessage',
        },
    );
});
