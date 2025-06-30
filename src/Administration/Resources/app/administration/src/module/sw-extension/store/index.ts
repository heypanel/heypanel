import type { HeyPanelClass } from 'src/core/heypanel';
import useSession from '../../../app/composables/use-session';
import './extensions.store';

let initialLoad = false;

/**
 * @sw-package checkout
 * @private
 */
export default function initState(HeyPanel: HeyPanelClass): void {
    HeyPanel.Vue.watch(useSession().languageId, async () => {
        if (!HeyPanel.Service('acl').can('system.plugin_maintain')) {
            return;
        }

        // Always on page load setAdminLocale will be called once. Catch it to not load refresh extensions
        if (!initialLoad) {
            initialLoad = true;
            return;
        }

        await HeyPanel.Service('heypanelExtensionService').updateExtensionData(false);
    });
}
