/**
 * @sw-package discovery
 */
import { mount } from '@vue/test-utils';

function mockCriteria() {
    return {
        limit: 25,
        page: 1,
        sortings: [{ field: 'name', naturalSorting: false, order: 'ASC' }],
        resetSorting() {
            this.sortings = [];
        },
        addSorting(sorting) {
            this.sortings.push(sorting);
        },
    };
}

const questionsMock = [
    {
        id: '101',
        active: true,
        name: 'question-101',
        questionNumber: '001',
        visibilities: [
            {
                id: '1',
                questionId: '101',
                channelId: 'storefrontChannelTypeId',
            },
        ],
    },
    {
        id: '102',
        active: false,
        name: 'question-102',
        questionNumber: '002',
        visibilities: [
            {
                id: '2',
                questionId: '202',
                channelId: 'storefrontChannelTypeId',
            },
        ],
    },
];
const variantProductMocks = [
    {
        id: '201',
        active: true,
        name: 'question-101.1',
        questionNumber: '001.1',
        parentId: '101',
        visibilities: [
            {
                id: '1',
                questionId: '101',
                channelId: 'storefrontChannelTypeId',
            },
        ],
    },
    {
        id: '202',
        active: true,
        name: 'question-101.2',
        questionNumber: '001.2',
        parentId: '101',
        visibilities: [
            {
                id: '2',
                questionId: '202',
                channelId: 'storefrontChannelTypeId',
            },
        ],
    },
];
questionsMock.has = (id) => {
    return questionsMock.some((item) => {
        return item.id === id;
    });
};

const questionMock = {
    visibilities: [
        { id: '01', questionId: '101', channelId: 'apiChannelTypeId' },
        {
            id: '02',
            questionId: '101',
            channelId: 'storefrontChannelTypeId',
        },
    ],
};

let repositoryFactoryDeleteMock;

async function createWrapper({ channel, questions } = {}) {
    repositoryFactoryDeleteMock = jest.fn(async () => {});

    const wrapper = mount(
        await wrapTestComponent('sw-channel-detail-questions', {
            sync: true,
        }),
        {
            global: {
                stubs: {
                    'mt-card': {
                        template: '<div class="mt-card"><slot></slot><slot name="grid"></slot></div>',
                    },
                    'sw-container': {
                        template: `
                        <div class="sw-container">
                            <slot></slot>
                        </div>
                    `,
                    },
                    'sw-card-section': {
                        template: `
                        <div class="sw-card-section">
                            <slot></slot>
                        </div>
                    `,
                    },
                    'sw-entity-listing': {
                        props: [
                            'items',
                            'allowEdit',
                            'allowDelete',
                        ],
                        template: `
                        <div class="sw-entity-listing">
                            <template v-for="item in items">
                                <slot name="actions" v-bind="{ item }"></slot>
                            </template>
                        </div>
                    `,
                        data() {
                            return {
                                selection: {},
                            };
                        },
                        methods: {
                            resetSelection() {
                                this.selection = {};
                            },
                        },
                    },
                    'sw-empty-state': {
                        template: `
                        <div class="sw-empty-state">
                            <slot></slot>
                            <slot name="actions"></slot>
                        </div>
                    `,
                    },
                    'sw-pagination': true,
                    'sw-simple-search-field': true,
                    'sw-channel-questions-assignment-modal': true,
                    'sw-context-menu-item': true,
                    'sw-extension-component-section': true,
                    'sw-ignore-class': true,
                    'sw-checkbox-field': true,
                    'router-link': true,
                    'sw-question-variant-info': true,
                },
                provide: {
                    repositoryFactory: {
                        create: (entity) => {
                            return {
                                create: async () => {},
                                search: async () => {
                                    if (entity === 'question') {
                                        const entityCollection = questions ?? [];
                                        entityCollection.criteria = mockCriteria();
                                        entityCollection.total = questions.length;

                                        return entityCollection;
                                    }

                                    return [];
                                },
                                delete: repositoryFactoryDeleteMock,
                                syncDeleted: async () => {},
                                saveAll: async () => {},
                            };
                        },
                    },
                },
            },
            props: {
                channel: channel ?? {
                    id: 'storefrontChannelTypeId',
                },
            },
        },
    );

    function getCreateButton() {
        return wrapper.findByText('button', 'sw-channel.detail.questions.buttonAddProducts');
    }

    function getEntityListing() {
        return wrapper.getComponent('.sw-entity-listing');
    }

    return { wrapper, getCreateButton, getEntityListing };
}

describe('src/module/sw-channel/view/sw-channel-detail-questions', () => {
    beforeEach(() => {
        global.activeAclRoles = [];
    });

    it('should get questions successful', async () => {
        const { wrapper } = await createWrapper({
            channel: { id: 'apiChannelTypeId' },
            questions: questionsMock,
        });
        await flushPromises();

        expect(wrapper.getComponent('.sw-entity-listing').props('items')).toEqual(questionsMock);
    });

    it('should delete question successful', async () => {
        const { wrapper } = await createWrapper({
            channel: { id: 'apiChannelTypeId' },
            questions: questionsMock,
        });
        await wrapper.getComponent('.sw-entity-listing').vm.$emit('selection-change', {
            101: questionMock,
        });
        await flushPromises();

        wrapper.vm.questionVisibilityRepository.delete = jest.fn(() => Promise.resolve());
        wrapper.vm.getProducts = jest.fn(() => Promise.resolve());

        await wrapper.vm.onDeleteProduct(questionMock);

        expect(wrapper.vm.getDeleteId(questionMock)).toBe('01');
        expect(wrapper.vm.getProducts).toHaveBeenCalled();

        wrapper.vm.questionVisibilityRepository.delete.mockRestore();
        wrapper.vm.getProducts.mockRestore();
    });

    it('should delete question failed', async () => {
        const { wrapper } = await createWrapper({
            channel: { id: 'apiChannelTypeId' },
            questions: questionsMock,
        });

        await wrapper.setData({
            searchTerm: 'Awesome Product',
        });

        wrapper.vm.questionVisibilityRepository.delete = jest.fn(() => Promise.reject(new Error('Error')));
        wrapper.vm.createNotificationError = jest.fn();

        await wrapper.vm.onDeleteProduct(questionMock);

        expect(wrapper.vm.getDeleteId(questionMock)).toBe('01');
        expect(wrapper.vm.createNotificationError).toHaveBeenCalledTimes(1);

        wrapper.vm.questionVisibilityRepository.delete.mockRestore();
        wrapper.vm.createNotificationError.mockRestore();
    });

    it('should get delete id correctly', async () => {
        const { wrapper } = await createWrapper({
            channel: { id: 'apiChannelTypeId' },
        });

        const deleteId = wrapper.vm.getDeleteId(questionMock);

        expect(deleteId).toBe('01');
    });

    it('should get questions when changing search term', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();
        wrapper.vm.getProducts = jest.fn();

        await wrapper.setData({
            page: 2,
        });

        expect(wrapper.vm.page).toBe(2);

        await wrapper.vm.onChangeSearchTerm('Awesome Product');

        expect(wrapper.vm.searchTerm).toBe('Awesome Product');
        expect(wrapper.vm.questionCriteria.term).toBe('Awesome Product');
        expect(wrapper.vm.page).toBe(1);
        expect(wrapper.vm.getProducts).toHaveBeenCalledTimes(1);
        wrapper.vm.getProducts.mockRestore();
    });

    it('should get questions when changing page', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();
        wrapper.vm.getProducts = jest.fn();
        expect(wrapper.vm.questionCriteria.sortings).toEqual([]);
        wrapper.vm.questions.criteria = mockCriteria();

        await wrapper.vm.onChangePage({ page: 2, limit: 25 });

        expect(wrapper.vm.page).toBe(2);
        expect(wrapper.vm.limit).toBe(25);
        expect(wrapper.vm.questionCriteria.sortings).toEqual([
            { field: 'name', naturalSorting: false, order: 'ASC' },
        ]);
        expect(wrapper.vm.getProducts).toHaveBeenCalledTimes(1);
        wrapper.vm.getProducts.mockRestore();
    });

    it('should be able to add questions in empty state', async () => {
        global.activeAclRoles = ['channel.editor'];
        const { wrapper, getCreateButton } = await createWrapper();
        await flushPromises();

        await wrapper.setData({ questions: [], searchTerm: null });

        expect(getCreateButton().attributes('disabled')).toBeUndefined();
    });

    it('should not be able to add questions in empty state', async () => {
        const { wrapper, getCreateButton } = await createWrapper();
        await flushPromises();

        await wrapper.setData({ questions: [], searchTerm: null });

        expect(getCreateButton().attributes('disabled')).toBeDefined();
    });

    it('should be able to add questions in filled state', async () => {
        global.activeAclRoles = ['channel.editor'];
        const { wrapper, getCreateButton } = await createWrapper();
        await flushPromises();

        await wrapper.setData({
            questions: questionsMock,
            searchTerm: 'Awesome Product',
        });

        expect(getCreateButton().attributes('disabled')).toBeUndefined();
    });

    it('should not be able to add questions in filled state', async () => {
        const { wrapper, getCreateButton } = await createWrapper();
        await flushPromises();

        await wrapper.setData({
            questions: questionsMock,
            searchTerm: 'Awesome Product',
        });

        expect(getCreateButton().attributes('disabled')).toBeDefined();
    });

    it('should be able to delete question', async () => {
        global.activeAclRoles = ['channel.deleter'];
        const { wrapper, getEntityListing } = await createWrapper();
        await flushPromises();

        await wrapper.setData({ questions: questionsMock });

        expect(getEntityListing().props('allowDelete')).toBe(true);
    });

    it('should not be able to delete question', async () => {
        const { wrapper, getEntityListing } = await createWrapper();
        await flushPromises();

        await wrapper.setData({ questions: questionsMock });

        expect(getEntityListing().props('allowDelete')).toBe(false);
    });

    it('should be able to edit question', async () => {
        global.activeAclRoles = ['question.editor'];
        const { wrapper, getEntityListing } = await createWrapper();
        await flushPromises();

        await wrapper.setData({ questions: questionsMock });

        expect(getEntityListing().props('allowEdit')).toBe(true);
    });

    it('should not be able to edit question', async () => {
        const { wrapper, getEntityListing } = await createWrapper();
        await flushPromises();

        await wrapper.setData({ questions: questionsMock });

        expect(getEntityListing().props('allowEdit')).toBe(false);
    });

    it('should turn on add questions modal', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();

        await wrapper.vm.openAddProductsModal();

        const modal = wrapper.find('sw-channel-questions-assignment-modal-stub');

        expect(wrapper.vm.showProductsModal).toBe(true);
        expect(modal.exists()).toBeTruthy();
    });

    it('should add questions successful', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();
        wrapper.vm.saveProductVisibilities = jest.fn(() => Promise.resolve());

        await wrapper.setData({ questions: questionsMock });
        await wrapper.vm.onAddProducts([
            { id: '103', active: true, questionNumber: '003' },
        ]);

        expect(wrapper.vm.saveProductVisibilities).toHaveBeenCalledWith(
            expect.arrayContaining([
                expect.objectContaining({ questionId: '103' }),
            ]),
        );

        wrapper.vm.saveProductVisibilities.mockRestore();
    });

    it('should add questions failed', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();
        wrapper.vm.saveProductVisibilities = jest.fn(() => Promise.resolve());

        await expect(wrapper.vm.onAddProducts([])).rejects.toEqual();

        expect(wrapper.vm.showProductsModal).toBe(false);
        expect(wrapper.vm.saveProductVisibilities).not.toHaveBeenCalled();

        wrapper.vm.saveProductVisibilities.mockRestore();
    });

    it('should save question visibilities successful', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();
        wrapper.vm.questionVisibilityRepository.saveAll = jest.fn(() => Promise.resolve());

        await wrapper.vm.saveProductVisibilities([]);

        expect(wrapper.vm.questionVisibilityRepository.saveAll).not.toHaveBeenCalled();

        wrapper.vm.questionVisibilityRepository.saveAll.mockRestore();
    });

    it('should save question visibilities failed', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();
        wrapper.vm.questionVisibilityRepository.saveAll = jest.fn(() => {
            return Promise.reject(new Error('Whoops!'));
        });

        const getError = async () => {
            try {
                await wrapper.vm.saveProductVisibilities([
                    {
                        visibility: 30,
                        questionId: 'questionId',
                        channelId: 'channelId',
                        channel: {},
                        _isNew: true,
                    },
                ]);

                throw new Error('Method should have thrown an error');
            } catch (error) {
                return error;
            }
        };

        expect((await getError()).message).toBe('Whoops!');

        wrapper.vm.questionVisibilityRepository.saveAll.mockRestore();
    });

    it('should not be able to delete variants which have inherit visibility', async () => {
        const { wrapper } = await createWrapper({
            questions: [
                ...questionsMock,
                ...variantProductMocks,
            ],
        });
        await flushPromises();

        expect(wrapper.vm.isProductRemovable(variantProductMocks[0])).toBe(false);
        expect(wrapper.vm.isProductRemovable(variantProductMocks[1])).toBe(true);
    });

    it('should render loading state when loading question entities', async () => {
        const { wrapper } = await createWrapper();

        expect(wrapper.getComponent('.mt-card').attributes('is-loading')).toBe('true');
        expect(wrapper.find('.sw-empty-state').exists()).toBe(false);
    });

    it('should render empty state when questions are loaded and empty', async () => {
        const { wrapper } = await createWrapper();
        await flushPromises();

        expect(wrapper.getComponent('.mt-card').attributes('is-loading')).toBeUndefined();
        expect(wrapper.find('.sw-empty-state').exists()).toBe(true);
    });

    it('should return filters from filter registry', async () => {
        const { wrapper } = await createWrapper();

        expect(wrapper.vm.assetFilter).toEqual(expect.any(Function));
    });
});
