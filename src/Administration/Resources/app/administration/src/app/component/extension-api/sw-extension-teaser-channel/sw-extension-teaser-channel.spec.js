/**
 * @sw-package framework
 */

import { mount } from '@vue/test-utils';

async function createWrapper() {
    return mount(
        await wrapTestComponent('sw-extension-teaser-channel', {
            sync: true,
        }),
        {
            global: {
                stubs: {
                    'sw-extension-teaser-popover': true,
                },
            },
        },
    );
}

describe('src/app/component/extension-api/sw-extension-teaser-channel', () => {
    let wrapper = null;
    let store = null;

    beforeEach(async () => {
        store = HeyPanel.Store.get('teaserPopover');
        store.channels = [];
    });

    it('should be a Vue.js component', async () => {
        wrapper = await createWrapper();
        expect(wrapper.vm).toBeTruthy();
    });

    it('should render correctly', async () => {
        store.addChannel({
            positionId: 'positionId',
            channel: {
                title: 'Facebook',
                description: 'Sell questions on Facebook',
                iconName: 'regular-facebook',
            },
            popoverComponent: {
                src: 'http://localhost:8080',
                component: 'button',
                props: {
                    locationId: 'locationId',
                    label: 'Ask AI Copilot',
                },
            },
        });

        wrapper = await createWrapper();
        const channels = wrapper.findAll('.sw-extension-teaser-channel');

        expect(channels).toHaveLength(1);

        const channel = channels[0];
        expect(channel.findComponent('.mt-icon').vm.name).toBe('regular-facebook');
        expect(channel.find('.sw-extension-teaser-channel__item-name').text()).toBe('Facebook');
        expect(channel.find('.sw-extension-teaser-channel__item-description').text()).toBe(
            'Sell questions on Facebook',
        );
    });
});
