import { mount } from '@vue/test-utils';

const userInfo = {
    avatarUrl: 'https://avatar.url',
    email: 'max@muster.com',
    name: 'Max Muster',
};

async function createWrapper() {
    return mount(
        await wrapTestComponent('sw-extension-my-extensions-account', {
            sync: true,
        }),
        {
            global: {
                stubs: {
                    'sw-text-field': {
                        props: ['value'],
                        template: `
                    <input type="text" :value="value" @input="$emit('update:value', $event.target.value)" />
                `,
                    },
                    'sw-skeleton': true,
                    'sw-avatar': true,
                    'sw-meteor-card': {
                        template: '<div><slot></slot></div>',
                    },
                },
                provide: {
                    heypanelExtensionService: {
                        checkLogin: () => {
                            return Promise.resolve({
                                userInfo,
                            });
                        },
                    },
                    systemConfigApiService: {
                        getValues: () => {
                            return Promise.resolve({
                                'core.store.apiUri': 'https://api.heypanel.com',
                                'core.store.licenseHost': 'sw6.test.heypanel.in',
                                'core.store.shopSecret': 'very.s3cret',
                            });
                        },
                    },
                    storeService: {
                        login: (heypanelId, password) => {
                            if (heypanelId !== 'max@muster.com') {
                                return Promise.reject();
                            }
                            if (password !== 'v3ryS3cret') {
                                return Promise.reject();
                            }

                            HeyPanel.Store.get('heypanelExtensions').userInfo = userInfo;

                            return Promise.resolve();
                        },
                        logout: () => {
                            HeyPanel.Store.get('heypanelExtensions').userInfo = null;

                            return Promise.resolve();
                        },
                    },
                },
            },
        },
    );
}

/**
 * @sw-package checkout
 */
describe('src/module/sw-extension/page/sw-extension-my-extensions-account', () => {
    beforeEach(async () => {
        HeyPanel.Store.get('heypanelExtensions').userInfo = null;
    });

    it('should show the login fields when not logged in', async () => {
        const wrapper = await createWrapper();

        const heypanelIdField = wrapper.find('.sw-extension-my-extensions-account__heypanel-id-field');
        const passwordField = wrapper.find('.sw-extension-my-extensions-account__password-field');
        const loginButton = wrapper.find('.sw-extension-my-extensions-account__login-button');

        // check if fields exists when user is not logged in
        expect(heypanelIdField.isVisible()).toBe(true);
        expect(passwordField.isVisible()).toBe(true);
        expect(loginButton.isVisible()).toBe(true);
    });

    it('should login when user clicks login', async () => {
        const wrapper = await createWrapper();

        // check if login status is not visible
        let loginStatus = wrapper.find('.sw-extension-my-extensions-account__wrapper-content-login-status-id');
        expect(loginStatus.exists()).toBe(false);

        // get fields
        const heypanelIdField = wrapper.get('.sw-extension-my-extensions-account__heypanel-id-field input');
        const passwordField = wrapper.findByLabel('sw-extension.my-extensions.account.passwordLabel');
        const loginButton = wrapper.find('.sw-extension-my-extensions-account__login-button');

        // enter credentials
        await heypanelIdField.setValue('max@muster.com');
        await passwordField.setValue('v3ryS3cret');

        await wrapper.vm.$nextTick();
        await flushPromises();

        // login
        await loginButton.trigger('click');
        await flushPromises();

        // check if layout switches
        loginStatus = wrapper.find('.sw-extension-my-extensions-account__wrapper-content-login-status-id');
        expect(loginStatus.exists()).toBe(true);
        expect(loginStatus.text()).toBe('max@muster.com');
    });

    it('should show the logged in view when logged in', async () => {
        HeyPanel.Store.get('heypanelExtensions').userInfo = userInfo;

        // create component with logged in view
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();
        await wrapper.vm.$nextTick();

        // check if layout shows the logged in information
        const loginStatus = wrapper.find('.sw-extension-my-extensions-account__wrapper-content-login-status-id');
        expect(loginStatus.exists()).toBe(true);
        expect(loginStatus.text()).toBe('max@muster.com');
    });

    it('should logout when user clicks logout button', async () => {
        HeyPanel.Store.get('heypanelExtensions').userInfo = userInfo;

        // create component with logged in view
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();
        await wrapper.vm.$nextTick();

        // check if logout button exists
        let logoutButton = wrapper.find('.sw-extension-my-extensions-account__logout-button');
        expect(logoutButton.exists()).toBe(true);

        // click on logout
        await logoutButton.trigger('click');

        // check if logout button disappears
        logoutButton = wrapper.find('.sw-extension-my-extensions-account__logout-button');
        expect(logoutButton.exists()).toBe(false);

        // check if user is sees login view
        const loginButton = wrapper.find('.sw-extension-my-extensions-account__login-button');
        expect(loginButton.exists()).toBe(true);
    });
});
