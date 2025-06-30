/**
 * @sw-package framework
 */

import AclService from 'src/app/service/acl.service';

describe('src/app/service/acl.service.ts', () => {
    beforeEach(() => {
        HeyPanel.Application.view = {};
        HeyPanel.Application.view.root = {};
        HeyPanel.Application.view.root.$router = {};
        HeyPanel.Application.view.root.$router.resolve = () => ({});
        HeyPanel.Store.get('settingsItems').settingsGroups.shop = [];
        HeyPanel.Store.get('settingsItems').settingsGroups.system = [];
    });

    it('should be an admin', async () => {
        HeyPanel.Store.get('session').setCurrentUser({ admin: true });
        const aclService = new AclService();

        expect(aclService.isAdmin()).toBe(true);
    });

    it('should not be an admin', async () => {
        HeyPanel.Store.get('session').setCurrentUser({ admin: false });
        const aclService = new AclService();

        expect(aclService.isAdmin()).toBe(false);
    });

    it('should not be an admin if the store is empty', async () => {
        HeyPanel.Store.get('session').removeCurrentUser();
        const aclService = new AclService();

        expect(aclService.isAdmin()).toBe(false);
    });

    it('should allow every privilege as an admin', async () => {
        HeyPanel.Store.get('session').setCurrentUser({ admin: true });
        const aclService = new AclService();

        expect(aclService.can('system.clear_cache')).toBe(true);
    });

    it('should disallow when privilege does not exist', async () => {
        HeyPanel.Store.get('session').setCurrentUser({ admin: false });
        const aclService = new AclService();

        expect(aclService.can('system.clear_cache')).toBeFalsy();
    });

    it('should allow when privilege exists', async () => {
        const aclService = new AclService();
        HeyPanel.Store.get('session').setCurrentUser({ admin: false, aclRoles: [{ privileges: ['system.clear_cache'] }] });

        expect(aclService.can('system.clear_cache')).toBe(true);
    });

    it('should return all privileges', async () => {
        HeyPanel.Store.get('session').setCurrentUser({
            admin: false,
            aclRoles: [
                {
                    privileges: [
                        'system.clear_cache',
                        'orders.create_discounts',
                    ],
                },
            ],
        });
        const aclService = new AclService();

        expect(aclService.privileges).toContain('system.clear_cache');
        expect(aclService.privileges).toContain('orders.create_discounts');
    });

    it('should return true if router is undefined', async () => {
        HeyPanel.Application.view.root.$router = null;
        HeyPanel.Store.get('session').setCurrentUser({ admin: false, aclRoles: [{ privileges: ['question.viewer'] }] });
        const aclService = new AclService();

        expect(aclService.hasAccessToRoute('sw.question.index')).toBe(true);
    });

    it('should have access to the route when no privilege exists', async () => {
        HeyPanel.Application.view.root.$router.resolve = () => ({});
        HeyPanel.Store.get('session').setCurrentUser({ admin: false, aclRoles: [{ privileges: ['question.viewer'] }] });
        const aclService = new AclService();

        expect(aclService.hasAccessToRoute('sw.question.index')).toBe(true);
    });

    it('should not have access to the route when privilege not matches', async () => {
        HeyPanel.Application.view.root.$router.resolve = () => ({
            meta: {
                privilege: 'category.viewer',
            },
        });
        HeyPanel.Store.get('session').setCurrentUser({ admin: false, aclRoles: [{ privileges: ['question.viewer'] }] });
        const aclService = new AclService();

        expect(aclService.hasAccessToRoute('sw.question.index')).toBeFalsy();
    });

    it('should have access to the route when privilege matches', async () => {
        HeyPanel.Application.view.root.$router.resolve = () => ({
            meta: {
                privilege: 'question.viewer',
            },
        });
        HeyPanel.Store.get('session').setCurrentUser({ admin: false, aclRoles: [{ privileges: ['question.viewer'] }] });
        const aclService = new AclService();

        expect(aclService.hasAccessToRoute('sw.question.index')).toBe(true);
    });

    it('should have access to the settings route when user has any access to settings', async () => {
        HeyPanel.Store.get('settingsItems').settingsGroups.shop = [
            {
                group: 'shop',
                icon: 'default-chart-pie',
                id: 'sw-settings-tax',
                label: 'sw-settings-tax.general.mainMenuItemGeneral',
                name: 'settings-tax',
                privilege: 'tax.viewer',
                to: 'sw.settings.tax.index',
            },
        ];
        HeyPanel.Store.get('session').setCurrentUser({ admin: false, aclRoles: [{ privileges: ['tax.viewer'] }] });
        const aclService = new AclService();

        expect(aclService.hasAccessToRoute('.sw.settings.index')).toBe(true);
        expect(aclService.hasAccessToRoute('/sw/settings/index')).toBe(true);
    });

    it('should have access to the settings route when user has no access to settings', async () => {
        HeyPanel.Store.get('settingsItems').settingsGroups.shop = [
            {
                group: 'shop',
                icon: 'default-chart-pie',
                id: 'sw-settings-tax',
                label: 'sw-settings-tax.general.mainMenuItemGeneral',
                name: 'settings-tax',
                privilege: 'tax.viewer',
                to: 'sw.settings.tax.index',
            },
        ];
        HeyPanel.Store.get('settingsItems').settingsGroups.system = [];
        HeyPanel.Store.get('session').setCurrentUser({ admin: false });
        const aclService = new AclService();

        expect(aclService.hasAccessToRoute('.sw.settings.index')).toBe(false);
        expect(aclService.hasAccessToRoute('/sw/settings/index')).toBe(false);
    });
});
