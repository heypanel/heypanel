import { defineComponent } from 'vue';

/**
 * @sw-package checkout
 * @private
 */
export default HeyPanel.Mixin.register(
    'sw-extension-error',
    defineComponent({
        mixins: [HeyPanel.Mixin.getByName('notification')],

        methods: {
            showExtensionErrors(errorResponse) {
                HeyPanel.Service('extensionErrorService')
                    .handleErrorResponse(errorResponse, this)
                    .forEach((notification) => {
                        this.createNotificationError(notification);
                    });
            },
        },
    }),
);
