/**
 * @sw-package framework
 */

HeyPanel.Filter.register('date', (value: string, options: Intl.DateTimeFormatOptions = {}): string => {
    if (!value) {
        return '';
    }

    return HeyPanel.Utils.format.date(value, options);
});

/**
 * @private
 */
export default {};
