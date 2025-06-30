/**
 * @sw-package inventory
 */
import { mount } from '@vue/test-utils';

async function createWrapper() {
    return mount(
        await wrapTestComponent('sw-bulk-edit-question-visibility', {
            sync: true,
        }),
        {
            global: {
                stubs: {
                    'sw-inherit-wrapper': {
                        template: '<div class="sw-inherit-wrapper"><slot name="content"></slot></div>',
                    },
                    'sw-question-visibility-select': true,
                    'sw-container': await wrapTestComponent('sw-container'),
                    'sw-question-visibility-detail': true,
                },
                provide: {
                    repositoryFactory: {
                        create: () => {
                            return {
                                create: () => {
                                    return Promise.resolve();
                                },
                            };
                        },
                    },
                },
            },
            props: {
                bulkEditProduct: {},
                disabled: false,
            },
        },
    );
}

describe('sw-bulk-edit-question-visibility', () => {
    let wrapper;

    beforeAll(() => {
        HeyPanel.Store.get('swProductDetail').question = {
            visibilities: [
                {
                    questionId: 'questionId',
                    questionVersionId: 'questionVersionId',
                    channel: {},
                    channelId: 'channelId',
                    visibility: 30,
                },
            ],
        };
    });

    beforeEach(async () => {
        wrapper = await createWrapper();
    });

    it('should be a Vue.js component', async () => {
        expect(wrapper.vm).toBeTruthy();
    });

    it('should be disabled correctly', async () => {
        await wrapper.setProps({ disabled: true });
        expect(wrapper.find('sw-question-visibility-select-stub').attributes().disabled).toBeTruthy();
        expect(wrapper.find('.sw-card__quick-link.advanced-visibility').classes()).toContain('is--disabled');

        await wrapper.setProps({ disabled: false });
        expect(wrapper.find('sw-question-visibility-select-stub').attributes().disabled).toBeUndefined();
        expect(wrapper.find('.sw-card__quick-link.advanced-visibility').classes()).not.toContain('is--disabled');
    });
});
