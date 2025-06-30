/**
 * @sw-package framework
 *
 * @private
 */
export default function initializeMediaModal(): void {
    // eslint-disable-next-line @typescript-eslint/require-await
    HeyPanel.ExtensionAPI.handle('uiMediaModalOpen', (modalConfig) => {
        HeyPanel.Store.get('mediaModal').openModal(modalConfig);
    });
}
