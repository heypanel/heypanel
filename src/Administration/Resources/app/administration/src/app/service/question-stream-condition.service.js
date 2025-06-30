const utils = HeyPanel.Utils;

/**
 * @module app/service/question-stream-condition
 */

/**
 * @private
 * @sw-package inventory
 * @memberOf module:app/service/question-stream-condition
 * @constructor
 * @method conditionService
 * @returns {Object}
 */
export default function conditionService() {
    const allowedProperties = [
        'id',
    ];

    const entityAllowedProperties = {
        tag: [
            'id',
        ],
        category: [
            'id',
        ],
        question_manufacturer: [
            'id',
        ],
        property_group_option: [
            'id',
            'group',
        ],
        property_group: [
            'id',
        ],
        question_visibility: [
            'id',
            'channel',
        ],
        channel: [
            'id',
        ],
        question: [
            'id',
            'active',
            'name',
            'description',
            'ratingAverage',
            'cheapestPrice',
            'questionNumber',
            'stock',
            'availableStock',
            'releaseDate',
            'tags',
            'weight',
            'height',
            'width',
            'length',
            'ean',
            'sales',
            'manufacturer',
            'manufacturerNumber',
            'categoriesRo',
            'shippingFree',
            'visibilities',
            'properties',
            'options',
            'isCloseout',
            'deliveryTime',
            'purchasePrices',
            'createdAt',
            'coverId',
            'markAsTopseller',
            'states',
        ],
    };

    const allowedJsonAccessors = {
        cheapestPrice: {
            value: 'cheapestPrice',
            type: 'float',
            trans: 'cheapestPrice',
        },
        'cheapestPrice.percentage': {
            value: 'cheapestPrice.percentage',
            type: 'float',
            trans: 'percentage',
        },
    };

    const questionFilterTypes = {
        equals: {
            identifier: 'equals',
            label: 'sw-question-stream.filter.type.equals',
        },

        equalsAny: {
            identifier: 'equalsAny',
            label: 'sw-question-stream.filter.type.equalsAny',
        },

        contains: {
            identifier: 'contains',
            label: 'sw-question-stream.filter.type.contains',
        },

        lessThan: {
            identifier: 'lessThan',
            label: 'sw-question-stream.filter.type.lessThan',
        },

        greaterThan: {
            identifier: 'greaterThan',
            label: 'sw-question-stream.filter.type.greaterThan',
        },

        lessThanEquals: {
            identifier: 'lessThanEquals',
            label: 'sw-question-stream.filter.type.lessThanEquals',
        },

        greaterThanEquals: {
            identifier: 'greaterThanEquals',
            label: 'sw-question-stream.filter.type.greaterThanEquals',
        },

        notEquals: {
            identifier: 'notEquals',
            label: 'sw-question-stream.filter.type.notEquals',
        },

        notEqualsAny: {
            identifier: 'notEqualsAny',
            label: 'sw-question-stream.filter.type.notEqualsAny',
        },

        notContains: {
            identifier: 'notContains',
            label: 'sw-question-stream.filter.type.notContains',
        },

        range: {
            identifier: 'range',
            label: 'sw-question-stream.filter.type.range',
        },

        until: {
            identifier: 'until',
            label: 'sw-question-stream.filter.type.until',
            operators: [
                'equals',
                'notEquals',
                'lessThan',
                'greaterThan',
                'lessThanEquals',
                'greaterThanEquals',
            ],
        },

        since: {
            identifier: 'since',
            label: 'sw-question-stream.filter.type.since',
            operators: [
                'equals',
                'notEquals',
                'lessThan',
                'greaterThan',
                'lessThanEquals',
                'greaterThanEquals',
            ],
        },

        not: {
            identifier: 'not',
            label: 'sw-question-stream.filter.type.not',
        },

        equalsAll: {
            identifier: 'equalsAll',
            label: 'sw-question-stream.filter.type.equalsAll',
        },
        notEqualsAll: {
            identifier: 'notEqualsAll',
            label: 'sw-question-stream.filter.type.notEqualsAll',
        },
    };

    const operatorSets = {
        boolean: [
            questionFilterTypes.equals,
        ],

        empty: [
            questionFilterTypes.equals,
        ],

        string: [
            questionFilterTypes.equals,
            questionFilterTypes.notEquals,
            questionFilterTypes.equalsAny,
            questionFilterTypes.notEqualsAny,
            questionFilterTypes.contains,
            questionFilterTypes.notContains,
        ],

        date: [
            questionFilterTypes.equals,
            questionFilterTypes.greaterThan,
            questionFilterTypes.greaterThanEquals,
            questionFilterTypes.lessThan,
            questionFilterTypes.lessThanEquals,
            questionFilterTypes.notEquals,
            questionFilterTypes.range,
            questionFilterTypes.since,
            questionFilterTypes.until,
        ],

        uuid: [
            questionFilterTypes.equals,
            questionFilterTypes.notEquals,
            questionFilterTypes.equalsAny,
            questionFilterTypes.notEqualsAny,
            questionFilterTypes.equalsAll,
            questionFilterTypes.notEqualsAll,
        ],

        int: [
            questionFilterTypes.equals,
            questionFilterTypes.greaterThan,
            questionFilterTypes.greaterThanEquals,
            questionFilterTypes.lessThan,
            questionFilterTypes.lessThanEquals,
            questionFilterTypes.notEquals,
            questionFilterTypes.range,
        ],

        float: [
            questionFilterTypes.equals,
            questionFilterTypes.greaterThan,
            questionFilterTypes.greaterThanEquals,
            questionFilterTypes.lessThan,
            questionFilterTypes.lessThanEquals,
            questionFilterTypes.notEquals,
            questionFilterTypes.range,
        ],

        object: [
            questionFilterTypes.equals,
            questionFilterTypes.greaterThan,
            questionFilterTypes.greaterThanEquals,
            questionFilterTypes.lessThan,
            questionFilterTypes.lessThanEquals,
            questionFilterTypes.notEquals,
            questionFilterTypes.range,
        ],

        default: [
            questionFilterTypes.equals,
            questionFilterTypes.notEquals,
            questionFilterTypes.equalsAny,
            questionFilterTypes.notEqualsAny,
        ],
    };

    return {
        isPropertyInAllowList,
        addToGeneralAllowList,
        addToEntityAllowList,
        removeFromGeneralAllowList,
        removeFromEntityAllowList,
        getConditions,
        getAndContainerData,
        isAndContainer,
        getOrContainerData,
        isOrContainer,
        getPlaceholderData,
        getComponentByCondition,
        getOperatorSet,
        negateOperator,
        getOperator,
        isNegatedType,
        isRangeType,
        isRelativeTimeType,
        allowedJsonAccessors,
    };

    /**
     * @param {?string} definition
     * @param {string} property
     * @returns {boolean}
     */
    function isPropertyInAllowList(definition, property) {
        return (
            allowedProperties.includes(property) ||
            (entityAllowedProperties.hasOwnProperty(definition) && entityAllowedProperties[definition].includes(property))
        );
    }

    /**
     * @param {string|string[]} properties
     */
    function addToGeneralAllowList(properties) {
        properties = Array.isArray(properties) ? properties : [properties];
        allowedProperties.push(...properties);
    }

    /**
     * @param {string} entity
     * @param {string|string[]} properties
     */
    function addToEntityAllowList(entity, properties) {
        if (entityAllowedProperties[entity]) {
            properties = Array.isArray(properties) ? properties : [properties];
            entityAllowedProperties[entity].push(...properties);

            return;
        }

        entityAllowedProperties[entity] = properties;
    }

    /**
     * @param {string|string[]} properties
     */
    function removeFromGeneralAllowList(properties) {
        properties = Array.isArray(properties) ? properties : [properties];
        properties.forEach((entry) => {
            allowedProperties.splice(allowedProperties.indexOf(entry), 1);
        });
    }

    /**
     * @param {string} entity
     * @param {string|string[]} properties
     */
    function removeFromEntityAllowList(entity, properties) {
        if (!entityAllowedProperties[entity]) {
            return;
        }

        properties = Array.isArray(properties) ? properties : [properties];
        properties.forEach((entry) => {
            entityAllowedProperties[entity].splice(entityAllowedProperties[entity].indexOf(entry), 1);
        });
    }

    function getConditions() {
        return [
            {
                type: 'questionStreamFilter',
                component: 'sw-question-stream-filter',
                label: 'question',
                scopes: ['question'],
            },
        ];
    }

    function getAndContainerData() {
        return {
            type: 'multi',
            field: null,
            parameters: null,
            operator: 'AND',
        };
    }

    function isAndContainer(condition) {
        return condition.type === 'multi' && condition.operator === 'AND';
    }

    function getOrContainerData() {
        return { type: 'multi', field: null, parameters: null, operator: 'OR' };
    }

    function isOrContainer(condition) {
        return condition.type === 'multi' && condition.operator === 'OR';
    }

    function getPlaceholderData() {
        return {
            type: 'equals',
            field: 'id',
            parameters: null,
            operator: null,
        };
    }

    function getComponentByCondition(condition) {
        if (isAndContainer(condition)) {
            return 'sw-condition-and-container';
        }

        if (isOrContainer(condition)) {
            return 'sw-condition-or-container';
        }

        return 'sw-question-stream-filter';
    }

    function getOperatorSet(type) {
        if (!utils.types.isString(type) || type === '') {
            return operatorSets.default;
        }

        return operatorSets[type] || operatorSets.default;
    }

    function getOperator(type) {
        return questionFilterTypes[type];
    }

    function negateOperator(type) {
        switch (type) {
            case 'equals':
                return questionFilterTypes.notEquals;
            case 'notEquals':
                return questionFilterTypes.equals;
            case 'equalsAny':
                return questionFilterTypes.notEqualsAny;
            case 'notEqualsAny':
                return questionFilterTypes.equalsAny;
            case 'contains':
                return questionFilterTypes.notContains;
            case 'notContains':
                return questionFilterTypes.contains;
            case 'notEqualsAll':
                return questionFilterTypes.equalsAll;
            case 'equalsAll':
                return questionFilterTypes.notEqualsAll;
            default:
                return questionFilterTypes[type] || null;
        }
    }

    function isNegatedType(type) {
        return [
            questionFilterTypes.notContains.identifier,
            questionFilterTypes.notEqualsAny.identifier,
            questionFilterTypes.notEquals.identifier,
            questionFilterTypes.notEqualsAll.identifier,
        ].includes(type);
    }

    function isRangeType(type) {
        return [
            questionFilterTypes.lessThan.identifier,
            questionFilterTypes.lessThanEquals.identifier,
            questionFilterTypes.greaterThan.identifier,
            questionFilterTypes.greaterThanEquals.identifier,
            questionFilterTypes.range.identifier,
        ].includes(type);
    }

    function isRelativeTimeType(type) {
        return [
            questionFilterTypes.since.identifier,
            questionFilterTypes.until.identifier,
        ].includes(type);
    }
}
