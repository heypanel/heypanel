/**
 * @sw-package framework
 */
import type { CurrencyOptions } from 'src/core/service/utils/format.utils';

const { currency } = HeyPanel.Utils.format;

/**
 * @private
 */
HeyPanel.Filter.register(
    'currency',
    (value: string | boolean, format: string, decimalPlaces: number, additionalOptions: CurrencyOptions) => {
        if (
            (!value || value === true) &&
            (!HeyPanel.Utils.types.isNumber(value) || HeyPanel.Utils.types.isEqual(value, NaN))
        ) {
            return '-';
        }

        if (HeyPanel.Utils.types.isEqual(parseInt(value, 10), NaN)) {
            return value;
        }

        return currency(parseFloat(value), format, decimalPlaces, additionalOptions);
    },
);
