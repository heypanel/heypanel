/**
 * @module app/service/search-type
 * @sw-package inventory
 */

/**
 *
 * @memberOf module:core/service/search-type
 * @constructor
 * @method createSearchTypeService
 * @returns {Object}
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function createSearchTypeService() {
    const typeStore = {
        question: {
            entityName: 'question',
            placeholderSnippet: 'sw-question.general.placeholderSearchBar',
            listingRoute: 'sw.question.index',
        },
        category: {
            entityName: 'category',
            placeholderSnippet: 'sw-category.general.placeholderSearchBar',
            listingRoute: 'sw.category.index',
        },
        landing_page: {
            entityName: 'landing_page',
            placeholderSnippet: 'sw-landing-page.general.placeholderSearchBar',
            listingRoute: 'sw.category.index',
        },
        member: {
            entityName: 'member',
            placeholderSnippet: 'sw-member.general.placeholderSearchBar',
            listingRoute: 'sw.member.index',
        },
        order: {
            entityName: 'order',
            placeholderSnippet: 'sw-order.general.placeholderSearchBar',
            listingRoute: 'sw.order.index',
        },
        media: {
            entityName: 'media',
            placeholderSnippet: 'sw-media.general.placeholderSearchBar',
            listingRoute: 'sw.media.index',
        },
        cms_page: {
            entityName: 'cms_page',
            placeholderSnippet: 'sw-cms.general.placeholderSearchBar',
            listingRoute: 'sw.cms.index',
            hideOnGlobalSearchBar: true,
        },
        member_group: {
            entityName: 'member_group',
            placeholderSnippet: 'sw-settings-member-group.general.placeholderSearchBar',
            listingRoute: 'sw.settings.member.group.index',
            hideOnGlobalSearchBar: true,
        },
        question_manufacturer: {
            entityName: 'question_manufacturer',
            placeholderSnippet: 'sw-manufacturer.general.placeholderSearchBar',
            listingRoute: 'sw.manufacturer.index',
            hideOnGlobalSearchBar: true,
        },
        newsletter_recipient: {
            entityName: 'newsletter_recipient',
            placeholderSnippet: 'sw-newsletter-recipient.general.placeholderSearchBar',
            listingRoute: 'sw.newsletter.recipient.index',
            hideOnGlobalSearchBar: true,
        },
        payment_method: {
            entityName: 'payment_method',
            placeholderSnippet: 'sw-settings-payment.general.placeholderSearchBar',
            listingRoute: 'sw.settings.payment.index',
            hideOnGlobalSearchBar: true,
        },
        shipping_method: {
            entityName: 'shipping_method',
            placeholderSnippet: 'sw-settings-shipping.general.placeholderSearchBar',
            listingRoute: 'sw.settings.shipping.index',
            hideOnGlobalSearchBar: true,
        },
        question_stream: {
            entityName: 'question_stream',
            placeholderSnippet: 'sw-question-stream.general.placeholderSearchBar',
            listingRoute: 'sw.question.stream.index',
            hideOnGlobalSearchBar: true,
        },
        promotion: {
            entityName: 'promotion',
            placeholderSnippet: 'sw-promotion-v2.list.placeholderSearchBar',
            listingRoute: 'sw.promotion.v2.index',
            hideOnGlobalSearchBar: true,
        },
        property_group: {
            entityName: 'property_group',
            placeholderSnippet: 'sw-property.general.placeholderSearchBar',
            listingRoute: 'sw.property.index',
            hideOnGlobalSearchBar: true,
        },
        channel: {
            entityName: 'channel',
            placeholderSnippet: 'sw-channel.general.placeholderSearchBar',
            listingRoute: 'sw.channel.index',
            hideOnGlobalSearchBar: true,
        },
    };

    let $typeStore = {};

    $typeStore = {
        all: {
            entityName: '',
            placeholderSnippet: '',
            listingRoute: '',
        },
        ...typeStore,
    };

    return {
        getTypeByName,
        upsertType,
        getTypes,
        removeType,
    };

    function getTypeByName(type) {
        return $typeStore[type];
    }

    function upsertType(name, configuration) {
        $typeStore[name] = { ...$typeStore[name], ...configuration };
    }

    function getTypes() {
        return $typeStore;
    }

    function removeType(name) {
        delete $typeStore[name];
    }
}
