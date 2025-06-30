/**
 * @sw-package framework
 */
import { watch } from 'vue';

let isInitialized = false;

/**
 * @private
 */
export default function LanguageAutoFetchingService() {
    if (isInitialized) return;
    isInitialized = true;

    // initial loading of the language
    loadLanguage(HeyPanel.Context.api.languageId);

    // load the language Entity
    async function loadLanguage(newLanguageId) {
        const languageRepository = HeyPanel.Service('repositoryFactory').create('language');
        const newLanguage = await languageRepository.get(newLanguageId, {
            ...HeyPanel.Context.api,
            inheritance: true,
        });

        HeyPanel.Store.get('context').api.language = newLanguage;
    }

    watch(HeyPanel.Store.get('context').api.languageId, loadLanguage);
}
