/**
 * @sw-package framework
 */
import { watch } from 'vue';
/* Is covered by E2E tests */
import { publish } from '@heypanel-ag/meteor-admin-sdk/es/channel';
import '../store/context.store';
import useSession from '../composables/use-session';

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function initializeContext(): void {
    // Handle incoming context requests from the ExtensionAPI
    HeyPanel.ExtensionAPI.handle('contextCurrency', () => {
        return {
            systemCurrencyId: HeyPanel.Context.app.systemCurrencyId ?? '',
            systemCurrencyISOCode: HeyPanel.Context.app.systemCurrencyISOCode ?? '',
        };
    });

    HeyPanel.ExtensionAPI.handle('contextEnvironment', () => {
        return HeyPanel.Context.app.environment ?? 'questionion';
    });

    HeyPanel.ExtensionAPI.handle('contextLanguage', () => {
        return {
            languageId: HeyPanel.Context.api.languageId ?? '',
            systemLanguageId: HeyPanel.Context.api.systemLanguageId ?? '',
        };
    });

    HeyPanel.ExtensionAPI.handle('contextLocale', () => {
        return {
            fallbackLocale: HeyPanel.Context.app.fallbackLocale ?? '',
            locale: HeyPanel.Store.get('session').currentLocale ?? '',
        };
    });

    HeyPanel.ExtensionAPI.handle('contextHeyPanelVersion', () => {
        return HeyPanel.Context.app.config.version ?? '';
    });

    HeyPanel.ExtensionAPI.handle('contextUserTimezone', () => {
        return HeyPanel.Store.get('session').currentUser?.timeZone ?? 'UTC';
    });

    HeyPanel.ExtensionAPI.handle('contextModuleInformation', (_, additionalInformation) => {
        const extension = Object.values(HeyPanel.Store.get('extensions').extensionsState).find((ext) =>
            ext.baseUrl.startsWith(additionalInformation._event_.origin),
        );

        if (!extension) {
            return {
                modules: [],
            };
        }

        // eslint-disable-next-line max-len,@typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        const modules = HeyPanel.Store.get('extensionSdkModules').getRegisteredModuleInformation(
            extension.baseUrl,
        ) as Array<{
            displaySearchBar: boolean;
            heading: string;
            id: string;
            locationId: string;
        }>;

        return {
            modules,
        };
    });

    HeyPanel.ExtensionAPI.handle('contextUserInformation', (_, { _event_ }) => {
        const appOrigin = _event_.origin;
        const extension = Object.entries(HeyPanel.Store.get('extensions').extensionsState).find((ext) => {
            return ext[1].baseUrl.startsWith(appOrigin);
        });

        if (!extension) {
            return Promise.reject(new Error(`Could not find a extension with the given event origin "${_event_.origin}"`));
        }

        if (!(extension[1]?.permissions?.read as string[])?.includes('user')) {
            return Promise.reject(new Error(`Extension "${extension[0]}" does not have the permission to read users`));
        }

        const currentUser = HeyPanel.Store.get('session').currentUser;

        return Promise.resolve({
            aclRoles: currentUser?.aclRoles as unknown as Array<{
                name: string;
                type: string;
                id: string;
                privileges: Array<string>;
            }>,
            active: !!currentUser?.active,
            admin: !!currentUser?.admin,
            avatarId: currentUser?.avatarId ?? '',
            email: currentUser?.email ?? '',
            firstName: currentUser?.firstName ?? '',
            id: currentUser?.id ?? '',
            lastName: currentUser?.lastName ?? '',
            localeId: currentUser?.localeId ?? '',
            title: currentUser?.title ?? '',
            // @ts-expect-error - type is not defined in entity directly
            type: (currentUser?.type as unknown as string) ?? '',
            username: currentUser?.username ?? '',
        });
    });

    HeyPanel.ExtensionAPI.handle('contextAppInformation', (_, { _event_ }) => {
        const appOrigin = _event_.origin;
        const extensionEntry = Object.entries(HeyPanel.Store.get('extensions').extensionsState).find((ext) => {
            return ext[1].baseUrl.startsWith(appOrigin);
        });

        if (extensionEntry === undefined) {
            return {
                name: 'unknown',
                type: 'app' as const,
                version: '0.0.0',
                inAppPurchases: [],
                privileges: {},
            };
        }

        const [
            extensionName,
            extension,
        ] = extensionEntry;

        return {
            name: extensionName,
            type: extension.type,
            version: extension.version ?? '',
            inAppPurchases: HeyPanel.InAppPurchase.getByExtension(extension.name),
            privileges: extension.permissions,
        };
    });

    const contextStore = HeyPanel.Store.get('context');

    watch(
        () => {
            return {
                languageId: contextStore.api.languageId,
                systemLanguageId: contextStore.api.systemLanguageId,
            };
        },
        ({ languageId, systemLanguageId }, { languageId: oldLanguageId, systemLanguageId: oldSystemLanguageId }) => {
            if (languageId === oldLanguageId && systemLanguageId === oldSystemLanguageId) {
                return;
            }

            void publish('contextLanguage', {
                languageId: languageId ?? '',
                systemLanguageId: systemLanguageId ?? '',
            });
        },
    );

    watch(
        () => {
            return {
                fallbackLocale: contextStore.app.fallbackLocale,
            };
        },
        ({ fallbackLocale }, { fallbackLocale: oldFallbackLocale }) => {
            if (fallbackLocale === oldFallbackLocale) {
                return;
            }

            void publish('contextLocale', {
                locale: HeyPanel.Store.get('session').currentLocale ?? '',
                fallbackLocale: fallbackLocale ?? '',
            });
        },
    );

    HeyPanel.Vue.watch(useSession().currentLocale, (locale) => {
        void publish('contextLocale', {
            locale: locale ?? '',
            fallbackLocale: contextStore.app.fallbackLocale ?? '',
        });
    });

    HeyPanel.ExtensionAPI.handle('windowGetId', () => {
        if (!contextStore.app.windowId) {
            contextStore.app.windowId = HeyPanel.Utils.createId();
        }

        return contextStore.app.windowId;
    });

    HeyPanel.ExtensionAPI.handle('contextShopId', () => {
        return contextStore.app.config.shopId;
    });
}
