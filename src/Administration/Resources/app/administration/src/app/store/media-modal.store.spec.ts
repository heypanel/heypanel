/**
 * @sw-package framework
 */

import { createPinia, setActivePinia } from 'pinia';

const mediaModalConfig = {
    allowMultiSelect: false,
    fileAccept: 'image/*',
    callback: () => {},
} as const;

describe('media-modal.store', () => {
    let store = HeyPanel.Store.get('mediaModal');

    beforeEach(() => {
        setActivePinia(createPinia());
        store = HeyPanel.Store.get('mediaModal');
    });

    afterEach(() => {
        store.mediaModal = null;
    });

    it('has initial state', () => {
        expect(store.mediaModal).toBeNull();
    });

    it('opens media modal', () => {
        store.openModal(mediaModalConfig);

        expect(store.mediaModal).toStrictEqual(mediaModalConfig);
    });
});
