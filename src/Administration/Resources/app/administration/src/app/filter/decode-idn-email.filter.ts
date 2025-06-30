/**
 * @sw-package framework
 */
import { toUnicode } from 'punycode/';

/**
 * @private
 */
HeyPanel.Filter.register('decode-idn-email', (value: string) => {
    return toUnicode(value);
});
