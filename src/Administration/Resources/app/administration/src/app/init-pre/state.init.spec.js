/**
 * @sw-package framework
 */
import initState from 'src/app/init-pre/state.init';

describe('src/app/init-pre/state.init.ts', () => {
    initState();

    it('should contain all state methods', () => {
        expect(HeyPanel.State._store).toBeDefined();
        expect(HeyPanel.State.list).toBeDefined();
        expect(HeyPanel.State.get).toBeDefined();
        expect(HeyPanel.State.getters).toBeDefined();
        expect(HeyPanel.State.commit).toBeDefined();
        expect(HeyPanel.State.dispatch).toBeDefined();
        expect(HeyPanel.State.watch).toBeDefined();
        expect(HeyPanel.State.subscribe).toBeDefined();
        expect(HeyPanel.State.subscribeAction).toBeDefined();
        expect(HeyPanel.State.registerModule).toBeDefined();
        expect(HeyPanel.State.unregisterModule).toBeDefined();
    });

    it('should initialized all state modules', () => {
        expect(HeyPanel.Store.get('heypanelApps')).toBeDefined();
    });

    it('should be able to get cmsPageState backwards compatible', () => {
        // The cmsPageState is deprecated and causes a warning, therefore ignore it
        global.allowedErrors.push({
            method: 'warn',
            msgCheck: (_, msg) => {
                if (typeof msg !== 'string') {
                    return false;
                }

                return msg === 'HeyPanel.State.get("cmsPageState") is deprecated! Use HeyPanel.Store.get instead.';
            },
        });

        HeyPanel.Store.register({
            id: 'cmsPage',
            state: () => ({
                foo: 'bar',
            }),
        });

        expect(HeyPanel.Store.get('cmsPage').foo).toBe('bar');
        HeyPanel.Store.unregister('cmsPage');
    });

    it('should be able to commit cmsPageState backwards compatible', () => {
        // The cmsPageState is deprecated and causes a warning, therefore ignore it
        global.allowedErrors.push({
            method: 'warn',
            msgCheck: (_, msg) => {
                if (typeof msg !== 'string') {
                    return false;
                }

                return msg === 'HeyPanel.State.get("cmsPageState") is deprecated! Use HeyPanel.Store.get instead.';
            },
        });

        HeyPanel.Store.register({
            id: 'cmsPage',
            state: () => ({
                foo: 'bar',
            }),
            actions: {
                setFoo(foo) {
                    this.foo = foo;
                },
            },
        });

        const store = HeyPanel.Store.get('cmsPage');
        expect(store.foo).toBe('bar');

        store.setFoo('jest');
        expect(store.foo).toBe('jest');

        HeyPanel.Store.unregister('cmsPage');
    });
});
