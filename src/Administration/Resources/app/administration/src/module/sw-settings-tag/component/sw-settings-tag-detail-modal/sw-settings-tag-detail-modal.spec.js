import { mount } from '@vue/test-utils';

/**
 * @sw-package inventory
 */

async function createWrapper() {
    return mount(
        await wrapTestComponent('sw-settings-tag-detail-modal', {
            sync: true,
        }),
        {
            global: {
                renderStubDefaultSlot: true,
                provide: {
                    repositoryFactory: {
                        create: () => ({
                            create: () => {
                                return {
                                    isNew: () => true,
                                };
                            },

                            save: jest.fn(() => Promise.resolve()),
                        }),
                    },
                    syncService: {
                        sync: jest.fn(),
                    },
                    acl: {
                        can: () => {
                            return true;
                        },
                    },
                },
                stubs: {
                    'sw-modal': true,
                    'sw-tabs': await wrapTestComponent('sw-tabs', {
                        sync: true,
                    }),
                    'sw-tabs-item': true,
                    'sw-text-field': true,
                    'sw-settings-tag-detail-assignments': true,
                    'sw-tabs-deprecated': true,
                },
            },
        },
    );
}

describe('module/sw-settings-tag/component/sw-settings-tag-detail-modal', () => {
    it('should be a Vue.JS component', async () => {
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();

        expect(wrapper.vm).toBeTruthy();
    });

    it('should set tag, to be added and to be deleted on create', async () => {
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();

        expect(wrapper.vm.tag).not.toBeNull();

        const initialAssignments = {
            questions: {},
            media: {},
            newsletterRecipients: {},
            categories: {},
            members: {},
            orders: {},
            landingPages: {},
            rules: {},
            shippingMethods: {},
        };

        expect(wrapper.vm.assignmentsToBeAdded).toEqual(initialAssignments);
        expect(wrapper.vm.assignmentsToBeDeleted).toEqual(initialAssignments);
    });

    it('should emit event on save', async () => {
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();

        await wrapper.setData({
            assignmentsToBeDeleted: {
                questions: { '0b7957f43b9b489fb7bc02a0a233274e': {} },
            },
        });

        await wrapper.vm.onSave();

        expect(wrapper.vm.syncService.sync).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.tagRepository.save).toHaveBeenCalledTimes(1);

        const onSaveEvents = wrapper.emitted('finish');
        expect(onSaveEvents).toHaveLength(1);
    });

    it('should emit event on cancel', async () => {
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();

        wrapper.vm.onCancel();

        const onCancelEvents = wrapper.emitted('close');
        expect(onCancelEvents).toHaveLength(1);
    });

    it('should increase and decrease counts from to be added and to be deleted', async () => {
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();

        await wrapper.setProps({
            counts: { questions: 7 },
        });

        expect(wrapper.vm.computedCounts.questions).toBe(7);

        await wrapper.setData({
            assignmentsToBeDeleted: {
                questions: { a: {}, b: {} },
                invalid: { a: {} },
            },
            assignmentsToBeAdded: {
                questions: { a: {}, b: {}, c: {}, d: {} },
                invalid: { a: {} },
            },
        });

        expect(wrapper.vm.computedCounts.questions).toBe(9);
    });

    it('should add and remove assignments', async () => {
        const wrapper = await createWrapper();
        await wrapper.vm.$nextTick();

        await wrapper.setData({
            assignmentsToBeDeleted: {
                questions: { a: { id: 'a' }, b: { id: 'b' } },
            },
            assignmentsToBeAdded: {
                questions: { c: { id: 'c' }, d: { id: 'd' } },
            },
        });

        wrapper.vm.addAssignment('questions', 'b', { id: 'b' });
        wrapper.vm.addAssignment('questions', 'e', { id: 'e' });
        wrapper.vm.removeAssignment('questions', 'd', { id: 'd' });
        wrapper.vm.removeAssignment('questions', 'f', { id: 'f' });

        expect(wrapper.vm.assignmentsToBeDeleted.questions).toEqual({
            a: { id: 'a' },
            f: { id: 'f' },
        });
        expect(wrapper.vm.assignmentsToBeAdded.questions).toEqual({
            c: { id: 'c' },
            e: { id: 'e' },
        });
    });
});
