/**
 * @sw-package framework
 *
 * @private
 */
export default function initMainModules(): void {
    HeyPanel.ExtensionAPI.handle('mainModuleAdd', async (mainModuleConfig, additionalInformation) => {
        const extensionName = Object.keys(HeyPanel.Store.get('extensions').extensionsState).find((key) =>
            HeyPanel.Store.get('extensions').extensionsState[key].baseUrl.startsWith(additionalInformation._event_.origin),
        );

        if (!extensionName) {
            throw new Error(`Extension with the origin "${additionalInformation._event_.origin}" not found.`);
        }

        const extension = HeyPanel.Store.get('extensions').extensionsState?.[extensionName];

        await HeyPanel.Store.get('extensionSdkModules')
            .addModule({
                heading: mainModuleConfig.heading,
                locationId: mainModuleConfig.locationId,
                displaySearchBar: mainModuleConfig.displaySearchBar ?? true,
                baseUrl: extension.baseUrl,
            })
            .then((moduleId) => {
                if (typeof moduleId !== 'string') {
                    return;
                }

                HeyPanel.Store.get('extensionMainModules').addMainModule({
                    extensionName,
                    moduleId,
                });
            });
    });

    HeyPanel.ExtensionAPI.handle('smartBarButtonAdd', (configuration) => {
        HeyPanel.Store.get('extensionSdkModules').addSmartBarButton(configuration);
    });

    HeyPanel.ExtensionAPI.handle('smartBarHide', (configuration) => {
        HeyPanel.Store.get('extensionSdkModules').addHiddenSmartBar(configuration.locationId);
    });
}
