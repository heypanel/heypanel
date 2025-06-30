/**
 * @sw-package framework
 *
 * @private
 */
export default function initMenuItems(): void {
    HeyPanel.ExtensionAPI.handle('menuItemAdd', async (menuItemConfig, additionalInformation) => {
        const extension = Object.values(HeyPanel.Store.get('extensions').extensionsState).find((ext) =>
            ext.baseUrl.startsWith(additionalInformation._event_.origin),
        );

        if (!extension) {
            throw new Error(`Extension with the origin "${additionalInformation._event_.origin}" not found.`);
        }

        await HeyPanel.Store.get('extensionSdkModules')
            .addModule({
                heading: menuItemConfig.label,
                locationId: menuItemConfig.locationId,
                displaySearchBar: menuItemConfig.displaySearchBar!,
                displaySmartBar: menuItemConfig.displaySmartBar,
                baseUrl: extension.baseUrl,
            })
            .then((moduleId) => {
                if (typeof moduleId !== 'string') {
                    return;
                }

                HeyPanel.Store.get('menuItem').addMenuItem({
                    ...menuItemConfig,
                    moduleId,
                });
            });
    });

    HeyPanel.ExtensionAPI.handle('menuCollapse', () => {
        HeyPanel.Store.get('adminMenu').collapseSidebar();
    });

    HeyPanel.ExtensionAPI.handle('menuExpand', () => {
        HeyPanel.Store.get('adminMenu').expandSidebar();
    });
}
