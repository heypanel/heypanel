import type { AxiosError } from 'axios';
import template from './sw-extension-my-extensions-account.html.twig';
import './sw-extension-my-extensions-account.scss';
import extensionErrorHandler from '../../service/extension-error-handler.service';
import type { MappedError } from '../../service/extension-error-handler.service';
import type { UserInfo } from '../../../../core/service/api/store.api.service';

const { Store, Mixin, Filter } = HeyPanel;

/**
 * @sw-package checkout
 * @private
 */
export default HeyPanel.Component.wrapComponentConfig({
    template,

    inject: [
        'systemConfigApiService',
        'heypanelExtensionService',
        'storeService',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    data(): {
        isLoading: boolean;
        unsubscribeStore: (() => void) | null;
        form: {
            password: string;
            heypanelId: string;
        };
    } {
        return {
            isLoading: true,
            unsubscribeStore: null,
            form: {
                password: '',
                heypanelId: '',
            },
        };
    },

    computed: {
        userInfo(): UserInfo | null {
            return Store.get('heypanelExtensions').userInfo;
        },

        isLoggedIn(): boolean {
            return Store.get('heypanelExtensions').userInfo !== null;
        },

        assetFilter() {
            return Filter.getByName('asset');
        },
    },

    created() {
        this.createdComponent()
            .then(() => {
                this.unsubscribeStore = Store.get('heypanelExtensions').$onAction(({ name, args }) =>
                    this.showErrorNotification({ type: name, payload: args as MappedError[][] }),
                );
            })
            // eslint-disable-next-line @typescript-eslint/no-empty-function
            .catch(() => {});
    },

    beforeUnmount() {
        if (this.unsubscribeStore !== null) {
            this.unsubscribeStore();
        }
    },

    methods: {
        async createdComponent() {
            try {
                this.isLoading = true;
                await this.heypanelExtensionService.checkLogin();
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            try {
                await this.storeService.logout();
                this.$emit('logout-success');
            } catch (errorResponse) {
                this.commitErrors(
                    errorResponse as AxiosError<{
                        errors: StoreApiException[];
                    }>,
                );
            } finally {
                await this.heypanelExtensionService.checkLogin();
            }
        },

        async login() {
            this.isLoading = true;

            try {
                await this.storeService.login(this.form.heypanelId, this.form.password);

                this.$emit('login-success');

                // eslint-disable-next-line @typescript-eslint/no-unsafe-call
                this.createNotificationSuccess({
                    message: this.$tc('sw-extension.my-extensions.account.loginNotificationMessage'),
                });
            } catch (errorResponse) {
                this.commitErrors(
                    errorResponse as AxiosError<{
                        errors: StoreApiException[];
                    }>,
                );
            } finally {
                await this.heypanelExtensionService.checkLogin();
                this.isLoading = false;
            }
        },

        showErrorNotification({ type, payload }: { type: string; payload: MappedError[][] }) {
            if (type !== 'pluginErrorsMapped') {
                return;
            }

            payload.forEach((errors) => {
                errors.forEach((error) => {
                    if (error.parameters) {
                        this.showApiNotification(error);
                        return;
                    }

                    // Methods from mixins are not recognized
                    // eslint-disable-next-line @typescript-eslint/no-unsafe-call
                    this.createNotificationError({
                        message: this.$tc(error.message),
                    });
                });
            });
        },

        showApiNotification(error: MappedError) {
            // @ts-expect-error
            const docLink = this.$tc('sw-extension.errors.messageToTheHeyPanelDocumentation', error.parameters, 0);

            // Methods from mixins are not recognized
            // eslint-disable-next-line @typescript-eslint/no-unsafe-call
            this.createNotificationError({
                title: error.title,
                message: `${error.message} ${docLink}`,
                autoClose: false,
            });
        },

        commitErrors(errorResponse: AxiosError<{ errors: StoreApiException[] }>): never {
            if (errorResponse.response) {
                const mappedErrors = extensionErrorHandler.mapErrors(errorResponse.response.data.errors);
                HeyPanel.Store.get('heypanelExtensions').pluginErrorsMapped(mappedErrors);
            }

            throw errorResponse;
        },
    },
});
