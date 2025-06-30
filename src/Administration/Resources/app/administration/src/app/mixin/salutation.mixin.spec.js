/**
 * @sw-package framework
 */
import 'src/app/mixin/salutation.mixin';
import { mount } from '@vue/test-utils';

async function createWrapper() {
    return mount(
        {
            template: `
            <div class="sw-mock">
              <slot></slot>
            </div>
        `,
            mixins: [
                HeyPanel.Mixin.getByName('salutation'),
            ],
            data() {
                return {
                    name: 'sw-mock-field',
                };
            },
        },
        {
            attachTo: document.body,
        },
    );
}

describe('src/app/mixin/salutation.mixin.ts', () => {
    let wrapper;
    let originalFilterGetByName;

    beforeEach(async () => {
        if (originalFilterGetByName) {
            Object.defineProperty(HeyPanel.Filter, 'getByName', {
                value: jest.fn(() => {
                    return jest.fn(() => 'Salutation filter result');
                }),
            });
        } else {
            originalFilterGetByName = HeyPanel.Filter.getByName;
        }

        wrapper = await createWrapper();

        await flushPromises();
    });

    it('should be a Vue.js component', () => {
        expect(wrapper.vm).toBeTruthy();
    });

    it('should compute correct salutationFilter value', () => {
        const result = wrapper.vm.salutationFilter();

        expect(result).toBe('Salutation filter result');
        expect(HeyPanel.Filter.getByName).toHaveBeenCalledWith('salutation');
    });

    it('should return the correct salutation filter for entity with fallback snippet', () => {
        const result = wrapper.vm.salutation('question', 'myFallbackSnippet');

        expect(result).toBe('Salutation filter result');
        expect(HeyPanel.Filter.getByName).toHaveBeenCalledWith('salutation');
    });
});
