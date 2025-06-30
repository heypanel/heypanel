/**
 * @sw-package buyers-experience
 */
import RecentlySearchService from 'src/app/service/recently-search.service';

describe('app/service/recently-search.service.js', () => {
    let recentlySearchService;

    beforeEach(async () => {
        recentlySearchService = new RecentlySearchService();
    });

    it('get method should return empty array', async () => {
        const data = recentlySearchService.get('userId');

        expect(data).toEqual([]);
    });

    it('get method should return items from localStorage', async () => {
        const items = [{ entity: 'question', id: 'questionId' }];

        localStorage.setItem(recentlySearchService._key('userId'), JSON.stringify(items));

        const data = recentlySearchService.get('userId');

        expect(data).toEqual(items);
    });

    it('add method should save item into localStorage', async () => {
        localStorage.removeItem(recentlySearchService._key('userId'));

        const items = [{ entity: 'question', id: 'questionId', payload: {} }];

        recentlySearchService.add('userId', 'question', 'questionId');

        const actualStorage = JSON.parse(localStorage.getItem(recentlySearchService._key('userId')));

        expect(actualStorage).toHaveLength(1);
        expect(actualStorage[0].timestamp).toBeTruthy();

        delete actualStorage[0].timestamp;

        expect(actualStorage).toEqual(items);
    });

    it('add method should add up to maximum stack', async () => {
        const maximumStack = recentlySearchService._maxStackSize();

        for (let i = 0; i < maximumStack; i += 1) {
            recentlySearchService.add('userId', 'question', `questionId-${i}`);
        }

        const data = recentlySearchService.get('userId');

        expect(data).toHaveLength(maximumStack);
    });

    it('add method should push newst item to the top', async () => {
        const maximumStack = recentlySearchService._maxStackSize();

        for (let i = 0; i < maximumStack; i += 1) {
            recentlySearchService.add('userId', 'question', `questionId-${i}`);
        }

        recentlySearchService.add('userId', 'question', 'questionId-new');

        const data = recentlySearchService.get('userId');

        expect(data).toHaveLength(maximumStack);
        expect(data[0].entity).toBe('question');
        expect(data[0].id).toBe('questionId-new');
    });

    it('add method should pop oldest out of stack', async () => {
        const maximumStack = recentlySearchService._maxStackSize();

        recentlySearchService.add('userId', 'question', 'questionId-old');

        for (let i = 0; i < maximumStack; i += 1) {
            recentlySearchService.add('userId', 'question', `questionId-${i}`);
        }

        const data = recentlySearchService.get('userId');

        const foundOldProduct = data.find((item) => item.id === 'questionId-old');

        expect(foundOldProduct).toBeUndefined();
    });

    it('add method should move duplicated item to the top', async () => {
        const maximumStack = recentlySearchService._maxStackSize();

        recentlySearchService.add('userId', 'question', 'questionId-unique');

        const uniqueProduct = recentlySearchService.get('userId')[0];

        for (let i = 0; i < maximumStack; i += 1) {
            recentlySearchService.add('userId', 'question', `questionId-${i}`);
        }

        // Sleep 1 milisecond
        await new Promise((resolve) => {
            setTimeout(resolve, 1);
        });

        recentlySearchService.add('userId', uniqueProduct.entity, uniqueProduct.id);

        const data = recentlySearchService.get('userId');

        const foundOldProducts = data.filter((item) => item.id === uniqueProduct.id);

        expect(foundOldProducts).toBeTruthy();
        expect(foundOldProducts).toHaveLength(1);

        const foundOldProduct = foundOldProducts[0];

        expect(foundOldProduct).toEqual(data[0]);
        expect(foundOldProduct.entity).toEqual(uniqueProduct.entity);
        expect(foundOldProduct.id).toEqual(uniqueProduct.id);
        expect(foundOldProduct.timestamp).toBeGreaterThanOrEqual(uniqueProduct.timestamp);
    });
});
