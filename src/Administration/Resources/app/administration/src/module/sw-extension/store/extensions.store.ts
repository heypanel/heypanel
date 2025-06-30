/**
 * @sw-package checkout
 */

import type { UserInfo } from 'src/core/service/api/store.api.service';
import type { Extension } from '../service/extension-store-action.service';
import type { MappedError } from '../service/extension-error-handler.service';

/**
 * @private
 */
export interface HeyPanelExtensionsState {
    search: {
        page: number;
        limit: number;
        rating: $TSFixMe;
        sorting: $TSFixMe;
        term: null | string;
        filter: $TSFixMe;
    };
    extensionListing: Extension[];
    categoriesLanguageId: string | null;
    myExtensions: {
        loading: boolean;
        data: Extension[];
    };
    userInfo: UserInfo | null;
}

type SearchValue<T, K extends keyof T> = {
    key: K;
    value: T[K];
};

const heypanelExtensionsStore = HeyPanel.Store.register({
    id: 'heypanelExtensions',

    state: () =>
        ({
            search: {
                page: 1,
                limit: 12,
                rating: null,
                sorting: null,
                term: null,
                filter: {},
            },
            extensionListing: [],
            categoriesLanguageId: null,
            myExtensions: {
                loading: true,
                data: [],
            },
            userInfo: null,
            heypanelId: null,
            loginStatus: false,
            licensedExtensions: {
                loading: false,
                data: [],
            },
            totalPlugins: 0,
            plugins: null,
        }) as HeyPanelExtensionsState,

    actions: {
        setSearchValue<K extends keyof HeyPanelExtensionsState['search']>({
            key,
            value,
        }: SearchValue<HeyPanelExtensionsState['search'], K>) {
            this.search.page = 1;
            this.search[key] = value;
        },

        loadMyExtensions() {
            this.myExtensions.loading = true;
        },

        setLoading(value: boolean = true) {
            this.myExtensions.loading = value;
        },

        setMyExtensions(myExtensions: Extension[]) {
            this.myExtensions.data = myExtensions;
            this.myExtensions.loading = false;
        },

        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        pluginErrorsMapped(_mappedError: MappedError[]) {
            /* nth */
        },
    },
});

/**
 * @private
 */
export type HeyPanelExtensionsStore = ReturnType<typeof heypanelExtensionsStore>;

/**
 * @sw-package checkout
 * @private
 */
export default heypanelExtensionsStore;
