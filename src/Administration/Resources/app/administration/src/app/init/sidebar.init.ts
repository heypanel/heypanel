/**
 * @sw-package framework
 *
 * @private
 */
export default function initializeSidebar(): void {
    // eslint-disable-next-line @typescript-eslint/require-await
    HeyPanel.ExtensionAPI.handle('uiSidebarAdd', async (sidebarConfig, { _event_ }) => {
        const extension = Object.values(HeyPanel.Store.get('extensions').extensionsState).find((ext) =>
            ext.baseUrl.startsWith(_event_.origin),
        );

        if (!extension) {
            throw new Error(`Extension with the origin "${_event_.origin}" not found.`);
        }

        // create sidebar store
        HeyPanel.Store.get('sidebar').addSidebar({
            baseUrl: extension.baseUrl,
            active: false,
            ...sidebarConfig,
        });
    });

    HeyPanel.ExtensionAPI.handle('uiSidebarClose', ({ locationId }) => {
        HeyPanel.Store.get('sidebar').closeSidebar(locationId);
    });

    HeyPanel.ExtensionAPI.handle('uiSidebarRemove', ({ locationId }) => {
        HeyPanel.Store.get('sidebar').removeSidebar(locationId);
    });
}
