import { ui } from '@heypanel-ag/meteor-admin-sdk';
import initializeSidebar from './sidebar.init';

describe('src/app/init/sidebar.init', () => {
    beforeAll(() => {
        // Execute the initializeSidebar function
        initializeSidebar();
    });

    beforeEach(() => {
        // Reset the sidebar store
        HeyPanel.Store.get('sidebar').sidebars = [];

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
    });

    it('should handle uiSidebarAdd', async () => {
        // Check that sidebar store is empty
        expect(HeyPanel.Store.get('sidebar').sidebars).toEqual([]);

        // Add a sidebar
        await ui.sidebar.add({
            icon: 'regular-star',
            title: 'Test sidebar',
            locationId: 'test-sidebar',
        });

        // Check that sidebar store has the added sidebar
        expect(HeyPanel.Store.get('sidebar').sidebars).toHaveLength(1);
        expect(HeyPanel.Store.get('sidebar').sidebars[0]).toEqual({
            icon: 'regular-star',
            title: 'Test sidebar',
            locationId: 'test-sidebar',
            active: false,
            baseUrl: '',
        });
    });

    it('should handle uiSidebarClose', async () => {
        // Add a sidebar
        await ui.sidebar.add({
            icon: 'regular-star',
            title: 'Test sidebar',
            locationId: 'test-sidebar',
        });

        // Check that sidebar store has the added sidebar
        expect(HeyPanel.Store.get('sidebar').sidebars).toHaveLength(1);

        // Check that sidebar is not active
        expect(HeyPanel.Store.get('sidebar').sidebars[0].active).toBe(false);

        // Open the sidebar
        HeyPanel.Store.get('sidebar').sidebars[0].active = true;

        // Close the sidebar
        await ui.sidebar.close({
            locationId: 'test-sidebar',
        });

        // Check that sidebar is not active
        expect(HeyPanel.Store.get('sidebar').sidebars[0].active).toBe(false);
    });

    it('should handle uiSidebarRemove', async () => {
        // Add a sidebar
        await ui.sidebar.add({
            icon: 'regular-star',
            title: 'Test sidebar',
            locationId: 'test-sidebar',
        });

        // Check that sidebar store has the added sidebar
        expect(HeyPanel.Store.get('sidebar').sidebars).toHaveLength(1);

        // Remove the sidebar
        await ui.sidebar.remove({
            locationId: 'test-sidebar',
        });

        // Check that sidebar store is empty
        expect(HeyPanel.Store.get('sidebar').sidebars).toEqual([]);
    });
});
