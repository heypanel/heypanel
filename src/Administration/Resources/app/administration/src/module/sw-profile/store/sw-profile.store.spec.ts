describe('sw-profile.store', () => {
    it('has initial state', () => {
        const store = HeyPanel.Store.get('swProfile');
        expect(store.searchPreferences).toStrictEqual([]);
        expect(store.userSearchPreferences).toBeNull();
    });
});
