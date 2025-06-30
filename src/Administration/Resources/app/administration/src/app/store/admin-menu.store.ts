/**
 * @sw-package framework
 * @private
 * @description Apply for upselling service only, no public usage
 */
import type { ModuleManifest } from '../../core/factory/module.factory';

type NavigationEntry = Exclude<ModuleManifest['navigation'], undefined>[number];

const adminMenuStore = HeyPanel.Store.register({
    id: 'adminMenu',

    state: () => ({
        /**
         * The expanded state of the sidebar menu
         */
        isExpanded: localStorage.getItem('sw-admin-menu-expanded') !== 'false',
        /**
         * The entries that are currently expanded in the sidebar menu
         */
        expandedEntries: [] as NavigationEntry[],
        /**
         * The navigation entries for the sidebar menu
         */
        adminModuleNavigation: [] as NavigationEntry[],
    }),

    actions: {
        /**
         * Clears the expanded menu entries collapsing all entries
         */
        clearExpandedMenuEntries() {
            this.expandedEntries = [];
        },
        /**
         * Expands a sidebar menu entry
         * @param entry The Navigation Entry to expand
         */
        expandMenuEntry(entry: NavigationEntry) {
            this.expandedEntries.push(entry);
        },
        /**
         * Collapses a sidebar menu entry
         * @param entry The Navigation Entry to collapse
         */
        collapseMenuEntry(entry: NavigationEntry) {
            this.expandedEntries = this.expandedEntries.filter((e) => e.id !== entry.id);
        },
        /**
         * Expands the  sidebar menu
         */
        collapseSidebar() {
            this.isExpanded = false;
            localStorage.setItem('sw-admin-menu-expanded', 'false');
        },
        /**
         * Collapses the sidebar menu
         */
        expandSidebar() {
            this.isExpanded = true;
            localStorage.setItem('sw-admin-menu-expanded', 'true');
        },
    },
});

/**
 * @private
 */
export type AdminMenuStore = ReturnType<typeof adminMenuStore>;

/**
 * @private
 * @description
 * The `adminMenuStore` is responsible for managing the state of the sidebar menu.
 */
export default adminMenuStore;
