/**
 * @sw-package framework
 */

/**
 * @private
 */
HeyPanel.Filter.register('fileSize', (value: number, locale: string) => {
    if (!value) {
        return '';
    }

    return HeyPanel.Utils.format.fileSize(value, locale);
});

/* @private */
export {};
