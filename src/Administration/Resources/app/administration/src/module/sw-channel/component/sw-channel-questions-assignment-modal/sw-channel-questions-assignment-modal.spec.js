/**
 * @sw-package discovery
 */

import { mount } from '@vue/test-utils';
import 'src/app/component/base/sw-button';

async function createWrapper(activeTab = 'singleProducts') {
    return mount(
        await wrapTestComponent('sw-channel-questions-assignment-modal', {
            sync: true,
        }),
        {
            global: {
                directives: {
                    hide: {},
                },
                stubs: {
                    'sw-channel-questions-assignment-single-questions': true,
                    'sw-channel-question-assignment-categories': true,
                    'sw-channel-questions-assignment-dynamic-question-groups': true,
                    'sw-container': {
                        template: '<div class="sw-container"><slot></slot></div>',
                    },
                    'sw-modal': {
                        template:
                            '<div class="sw-modal"><slot></slot><slot name="content"></slot><slot name="modal-footer"></slot></div>',
                    },
                    'sw-tabs': {
                        data() {
                            return { active: activeTab };
                        },
                        template: '<div><slot></slot><slot name="content" v-bind="{ active }"></slot></div>',
                    },
                    'sw-tabs-item': true,
                    'sw-loader': true,
                    'router-link': true,
                },
            },
            props: {
                channel: {
                    id: 1,
                    name: 'Headless',
                },
                isAssignProductLoading: false,
            },
        },
    );
}

describe('src/module/sw-channel/component/sw-channel-questions-assignment-modal', () => {
    it('should emit modal close event', async () => {
        const wrapper = await createWrapper();

        await wrapper.get('.sw-channel-questions-assignment-modal__close-button').trigger('click');

        expect(wrapper.emitted('modal-close')).toBeTruthy();
    });

    it('should emit questions data when clicking Add Products button to assign question individually', async () => {
        const wrapper = await createWrapper();
        await wrapper.setData({
            singleProducts: [
                {
                    id: '1',
                    name: 'Test question',
                },
            ],
        });

        await wrapper.findByText('button', 'sw-channel.detail.questions.buttonAddProducts').trigger('click');

        expect(wrapper.emitted('questions-add')).toBeTruthy();
        expect(wrapper.emitted('questions-add')[0]).toEqual([
            wrapper.vm.questions,
        ]);
    });

    it('should emit questions data when clicking Add Products button to assign question by categories', async () => {
        const questions = [
            {
                name: 'Test question 1',
                id: '1',
            },
            {
                name: 'Test question 2',
                id: '2',
            },
        ];

        const wrapper = await createWrapper();
        await wrapper.setData({
            categoryProducts: questions,
        });

        const assignButton = wrapper.findByText('button', 'sw-channel.detail.questions.buttonAddProducts');
        await assignButton.trigger('click');

        expect(wrapper.emitted('questions-add')).toBeTruthy();
        expect(wrapper.emitted('questions-add')[0]).toEqual([questions]);
    });

    it('should remove duplicated questions before emitting', async () => {
        const wrapper = await createWrapper();
        await wrapper.setData({
            singleProducts: [
                {
                    name: 'Test question 1',
                    id: '1',
                },
                {
                    name: 'Test question 2',
                    id: '2',
                },
            ],
            groupProducts: [
                {
                    name: 'Test question 2',
                    id: '2',
                },
                {
                    name: 'Test question 3',
                    id: '3',
                },
            ],
        });

        expect(wrapper.vm.questions).toEqual([
            {
                name: 'Test question 1',
                id: '1',
            },
            {
                name: 'Test question 2',
                id: '2',
            },
            {
                name: 'Test question 3',
                id: '3',
            },
        ]);
        expect(wrapper.vm.questionCount).toBe(3);
    });

    it('should update the corresponding question successfully', async () => {
        const wrapper = await createWrapper();
        const groupProductsMock = [
            {
                id: 1,
                name: 'Low prices',
            },
            {
                id: 2,
                name: 'Standard prices',
            },
            {
                id: 3,
                name: 'High prices',
            },
        ];

        wrapper.vm.onChangeSelection(groupProductsMock, 'groupProducts');

        expect(wrapper.vm.groupProducts).toEqual(
            expect.arrayContaining([
                expect.objectContaining({ name: 'Low prices' }),
                expect.objectContaining({ name: 'Standard prices' }),
                expect.objectContaining({ name: 'High prices' }),
            ]),
        );
    });

    it('should update the question loading state correctly', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.setProductLoading(true);

        expect(wrapper.vm.isProductLoading).toBe(true);
    });
});
