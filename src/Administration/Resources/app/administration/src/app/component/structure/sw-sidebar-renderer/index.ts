/**
 * @sw-package framework
 */

import { computed } from 'vue';
import template from './sw-sidebar-renderer.html.twig';
import './sw-sidebar-renderer.scss';

/**
 * @private
 */
export default HeyPanel.Component.wrapComponentConfig({
    template,

    setup() {
        const activeSidebar = computed(() => {
            return HeyPanel.Store.get('sidebar').getActiveSidebar;
        });

        const sidebars = computed(() => {
            return HeyPanel.Store.get('sidebar').sidebars;
        });

        const closeSidebar = (locationId: string) => {
            HeyPanel.Store.get('sidebar').closeSidebar(locationId);
        };

        return {
            activeSidebar,
            sidebars,
            closeSidebar,
        };
    },
});
