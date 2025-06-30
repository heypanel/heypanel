/**
 * @sw-package framework
 */

import { defineComponent } from 'vue';

type SalutationFilterEntityType = {
    salutation: {
        id: string;
        salutationKey: string;
        displayName: string;
    };
    title: string;
    firstName: string;
    lastName: string;
    [key: string]: unknown;
};

/**
 * @private
 */
export default HeyPanel.Mixin.register(
    'salutation',
    defineComponent({
        computed: {
            salutationFilter(): (entity: SalutationFilterEntityType, fallbackSnippet: string) => string {
                return HeyPanel.Filter.getByName('salutation');
            },
        },

        methods: {
            salutation(entity: SalutationFilterEntityType, fallbackSnippet = '') {
                return this.salutationFilter(entity, fallbackSnippet);
            },
        },
    }),
);
