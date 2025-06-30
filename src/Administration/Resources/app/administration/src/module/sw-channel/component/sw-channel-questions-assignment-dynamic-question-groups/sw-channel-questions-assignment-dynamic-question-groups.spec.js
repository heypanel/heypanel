/**
 * @sw-package discovery
 */

import { mount } from '@vue/test-utils';

async function getError(method, ...args) {
    try {
        await method(...args);

        throw new Error('Method should have thrown an error');
    } catch (error) {
        return error;
    }
}

const questionStreamsMock = [
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
questionStreamsMock.total = 3;

const questionsMock = [
    {
        id: 1,
        name: 'Gaming chair',
    },
    {
        id: 2,
        name: 'Gaming desk',
    },
];

let repositoryFactoryMock;

async function createWrapper() {
    repositoryFactoryMock = {
        search: () => {
            return Promise.resolve();
        },
        get: () => {
            return Promise.resolve();
        },
    };

    return mount(await wrapTestComponent('sw-channel-questions-assignment-dynamic-question-groups', { sync: true }), {
        global: {
            stubs: {
                'mt-card': {
                    template: '<div><slot></slot><slot name="grid"></slot></div>',
                },
                'sw-card-section': true,
                'sw-simple-search-field': true,
                'sw-empty-state': true,
                'sw-entity-listing': true,
                'sw-pagination': true,
            },
            provide: {
                repositoryFactory: {
                    create: () => {
                        return repositoryFactoryMock;
                    },
                },
            },
        },
        props: {
            channel: {
                id: 1,
                name: 'Headless',
            },
            containerStyle: {},
        },
    });
}

describe('src/module/sw-channel/component/sw-channel-questions-assignment-dynamic-question-groups', () => {
    it('should get question streams when component got created', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.getProductStreams = jest.fn(() => {
            return Promise.resolve();
        });

        wrapper.vm.createdComponent();

        expect(wrapper.vm.getProductStreams).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.questionStreamColumns).toEqual(
            expect.arrayContaining([
                expect.objectContaining({ property: 'name' }),
            ]),
        );

        wrapper.vm.getProductStreams.mockRestore();
    });

    it('should get question streams successful', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.questionStreamRepository.search = jest.fn(() => {
            return Promise.resolve(questionStreamsMock);
        });

        await wrapper.vm.getProductStreams();

        expect(wrapper.vm.questionStreams).toEqual(
            expect.arrayContaining([
                expect.objectContaining({ name: 'Low prices' }),
                expect.objectContaining({ name: 'Standard prices' }),
                expect.objectContaining({ name: 'High prices' }),
            ]),
        );
        expect(wrapper.vm.total).toBe(3);

        wrapper.vm.questionStreamRepository.search.mockRestore();
    });

    it('should get question streams failed', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.questionStreamRepository.search = jest.fn(() => {
            return Promise.reject();
        });

        await wrapper.vm.getProductStreams();

        expect(wrapper.vm.questionStreams).toEqual(expect.arrayContaining([]));
        expect(wrapper.vm.total).toBe(0);

        wrapper.vm.questionStreamRepository.search.mockRestore();
    });

    it('should get question streams when searching', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.getProductStreams = jest.fn(() => {
            return Promise.resolve();
        });

        await wrapper.setData({
            page: 2,
        });

        expect(wrapper.vm.page).toBe(2);

        await wrapper.vm.onSearch('Standard prices');

        expect(wrapper.vm.term).toBe('Standard prices');
        expect(wrapper.vm.page).toBe(1);
        expect(wrapper.vm.getProductStreams).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.questionStreamCriteria.term).toBe('Standard prices');

        wrapper.vm.getProductStreams.mockRestore();
    });

    it('should get question streams when paginating', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.getProductStreams = jest.fn(() => {
            return Promise.resolve();
        });

        wrapper.vm.onPaginate({ page: 2, limit: 5 });

        expect(wrapper.vm.page).toBe(2);
        expect(wrapper.vm.limit).toBe(5);
        expect(wrapper.vm.getProductStreams).toHaveBeenCalledTimes(1);

        wrapper.vm.getProductStreams.mockRestore();
    });

    it('should open question stream correctly', async () => {
        const wrapper = await createWrapper();

        window.open = jest.fn();
        wrapper.vm.$router.resolve = jest.fn(() => ({ href: 'href' }));

        wrapper.vm.onOpen(questionStreamsMock[1]);

        expect(wrapper.vm.$router.resolve).toHaveBeenCalledWith(
            expect.objectContaining({
                name: 'sw.question.stream.detail',
                params: expect.objectContaining({ id: 2 }),
            }),
        );
        expect(window.open).toHaveBeenCalledWith('href', '_blank');

        wrapper.vm.$router.resolve.mockRestore();
        window.open.mockClear();
    });

    it('should call to get questions from question streams when selecting question streams', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.getProductsFromProductStreams = jest.fn(() => {
            return Promise.resolve(questionsMock);
        });

        await wrapper.vm.onSelect({ 1: questionStreamsMock[0] });

        expect(wrapper.vm.getProductsFromProductStreams).toHaveBeenCalledTimes(1);
        expect(wrapper.emitted()['selection-change'][0]).toEqual(
            expect.arrayContaining([
                questionsMock,
                'groupProducts',
            ]),
        );

        wrapper.vm.getProductsFromProductStreams.mockRestore();
    });

    it('should call to show error notification when selecting question streams', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.createNotificationError = jest.fn();
        wrapper.vm.getProductsFromProductStreams = jest.fn(() => {
            return Promise.reject(new Error('Whoops!'));
        });

        await wrapper.vm.onSelect({ 1: questionStreamsMock[0] });

        expect(wrapper.vm.getProductsFromProductStreams).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.createNotificationError).toHaveBeenCalledWith(expect.objectContaining({ message: 'Whoops!' }));

        wrapper.vm.getProductsFromProductStreams.mockRestore();
        wrapper.vm.createNotificationError.mockRestore();
    });

    it('should exit the function when selecting question streams', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.onSelect({});

        expect(wrapper.emitted()['selection-change'][0]).toEqual(
            expect.arrayContaining([
                [],
                'groupProducts',
            ]),
        );
    });

    it('should get questions from question streams successful', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.getProducts = jest.fn(() => {
            return Promise.resolve(questionsMock);
        });

        await wrapper.vm.getProductsFromProductStreams({ 1: questionStreamsMock[0] }).then((values) => {
            expect(values.flat()).toEqual(
                expect.arrayContaining([
                    expect.objectContaining({ name: 'Gaming chair' }),
                    expect.objectContaining({ name: 'Gaming desk' }),
                ]),
            );
        });

        wrapper.vm.getProducts.mockRestore();
    });

    it('should get questions from question streams failed', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.getProducts = jest.fn(() => {
            return Promise.reject(new Error('Whoops!'));
        });

        expect(
            (
                await getError(wrapper.vm.getProductsFromProductStreams, {
                    1: questionStreamsMock[0],
                })
            ).message,
        ).toBe('Whoops!');

        wrapper.vm.getProducts.mockRestore();
    });

    it('should get question stream filter successful', async () => {
        const wrapper = await createWrapper();

        const questionStreamFilterMock = {
            operator: 'OR',
            queries: [],
            type: 'multi',
        };

        wrapper.vm.questionStreamRepository.get = jest.fn(() => {
            return Promise.resolve({
                apiFilter: [
                    questionStreamFilterMock,
                ],
            });
        });

        await wrapper.vm.getProductStreamFilter(1);

        expect(wrapper.vm.questionStreamFilter).toEqual(
            expect.arrayContaining([
                expect.objectContaining(questionStreamFilterMock),
            ]),
        );

        wrapper.vm.questionStreamRepository.get.mockRestore();
    });

    it('should get question stream filter failed', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.questionStreamRepository.get = jest.fn(() => {
            throw new Error('Whoops!');
        });

        expect((await getError(wrapper.vm.getProductStreamFilter, 1)).message).toBe('Whoops!');

        expect(wrapper.vm.questionStreamFilter).toEqual(expect.arrayContaining([]));

        wrapper.vm.questionStreamRepository.get.mockRestore();
    });

    it('should get questions successful', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.questionRepository.search = jest.fn(() => {
            return Promise.resolve(questionsMock);
        });

        await wrapper.vm.getProducts().then((questions) => {
            expect(questions).toEqual(
                expect.arrayContaining([
                    expect.objectContaining({ name: 'Gaming chair' }),
                    expect.objectContaining({ name: 'Gaming desk' }),
                ]),
            );
        });

        wrapper.vm.questionRepository.search.mockRestore();
    });

    it('should get questions failed', async () => {
        const wrapper = await createWrapper();

        wrapper.vm.questionRepository.search = jest.fn(() => {
            throw new Error('Whoops!');
        });

        expect((await getError(wrapper.vm.getProducts)).message).toBe('Whoops!');

        wrapper.vm.questionRepository.search.mockRestore();
    });
});
