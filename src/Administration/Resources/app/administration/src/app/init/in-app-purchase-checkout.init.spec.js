/**
 * @sw-package checkout
 */
import { purchase } from '@heypanel-ag/meteor-admin-sdk/es/iap';
import initializeInAppPurchaseCheckout from './in-app-purchase-checkout.init';
import 'src/app/store/in-app-purchase-checkout.store';

describe('src/app/init/in-app-purchase.init.ts', () => {
    beforeAll(() => {
        initializeInAppPurchaseCheckout();
    });

    beforeEach(() => {
        HeyPanel.Store.get('extensions').extensionsState = {};
        HeyPanel.Store.get('extensions').addExtension({
            name: 'jestapp',
            baseUrl: '',
            permissions: [],
            version: '1.0.0',
            type: 'app',
            integrationId: '123',
            active: true,
        });

        HeyPanel.Store.get('inAppPurchaseCheckout').$reset();

        HeyPanel.Context.app.config.bundles = {
            jestapp: {
                identifier: 'jestapp',
            },
        };
    });

    it('should handle incoming inAppPurchases requests', async () => {
        await purchase({
            title: 'Your purchase title',
            variant: 'default',
            showHeader: true,
            showFooter: true,
            closable: true,
        });

        expect(HeyPanel.Store.get('inAppPurchaseCheckout').entry).toBeDefined();
    });
});
