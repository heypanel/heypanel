import { mount } from '@vue/test-utils';

/**
 * @sw-package after-sales
 */

const memberGroupMock = [
    {
        translated: { name: 'Test net group' },
        id: '1',
    },
    {
        translated: { name: 'Test gross group' },
        id: '2',
    },
    {
        translated: { name: 'Test VIP group' },
        id: '3',
    },
];

async function createWrapper() {
    return mount(
        await wrapTestComponent('sw-flow-change-member-group-modal', {
            sync: true,
        }),
        {
            propsData: {
                sequence: {},
            },
            global: {
                provide: {
                    shortcutService: {
                        startEventListener() {},
                        stopEventListener() {},
                    },
                    repositoryFactory: {
                        create: () => {
                            return {
                                search: () => Promise.resolve(memberGroupMock),
                            };
                        },
                    },
                },
                stubs: {
                    'sw-modal': await wrapTestComponent('sw-modal'),
                    'sw-entity-single-select': await wrapTestComponent('sw-entity-single-select'),
                    'sw-question-variant-info': await wrapTestComponent('sw-question-variant-info'),
                    'sw-select-result-list': await wrapTestComponent('sw-select-result-list'),
                    'sw-select-result': await wrapTestComponent('sw-select-result'),
                    'sw-select-base': await wrapTestComponent('sw-select-base'),
                    'sw-block-field': await wrapTestComponent('sw-block-field'),
                    'sw-base-field': await wrapTestComponent('sw-base-field'),
                    'sw-highlight-text': await wrapTestComponent('sw-highlight-text'),
                    'sw-field-error': await wrapTestComponent('sw-field-error'),
                    'sw-popover': await wrapTestComponent('sw-popover'),
                    'sw-popover-deprecated': await wrapTestComponent('sw-popover-deprecated', { sync: true }),
                    'sw-loader': true,
                    'router-link': true,
                    'sw-inheritance-switch': true,
                    'sw-ai-copilot-badge': true,
                    'sw-help-text': true,
                },
            },
        },
    );
}

describe('module/sw-flow/component/sw-flow-change-member-group-modal', () => {
    it('should show validation if member group field is empty', async () => {
        const wrapper = await createWrapper();
        await flushPromises();

        const memberGroupSelect = wrapper.find('.sw-entity-single-select');
        expect(memberGroupSelect.classes()).not.toContain('has--error');

        const saveButton = wrapper.find('.sw-flow-change-member-group-modal__save-button');
        await saveButton.trigger('click');
        await flushPromises();

        expect(memberGroupSelect.classes()).toContain('has--error');

        await wrapper.find('.sw-select__selection').trigger('click');
        await flushPromises();

        await wrapper.find('.sw-select-option--1 .sw-select-result__result-item-text').trigger('click');
        await flushPromises();

        await saveButton.trigger('click');
        await flushPromises();

        expect(memberGroupSelect.classes()).not.toContain('has--error');
    });

    it('should emit process-finish when member group is selected', async () => {
        const wrapper = await createWrapper();
        await flushPromises();

        await wrapper.find('.sw-select__selection').trigger('click');
        await flushPromises();

        await wrapper.find('.sw-select-result-list .sw-select-option--1').trigger('click');
        await flushPromises();

        const saveButton = wrapper.find('.sw-flow-change-member-group-modal__save-button');
        await saveButton.trigger('click');
        await flushPromises();

        expect(wrapper.emitted()['process-finish'][0]).toEqual([
            {
                config: {
                    memberGroupId: '2',
                },
            },
        ]);
    });

    it('should be able to close modal', async () => {
        const wrapper = await createWrapper();
        await flushPromises();

        const cancelButton = wrapper.find('.sw-flow-change-member-group-modal__cancel-button');
        expect(cancelButton.isVisible()).toBeTruthy();

        await cancelButton.trigger('click');
        await flushPromises();

        expect(wrapper.emitted()['modal-close']).toBeTruthy();
    });
});
