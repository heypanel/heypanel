/**
 * @sw-package discovery
 */

import template from './sw-channel-detail-hreflang.html.twig';
import { MtText } from '@heypanel-ag/meteor-component-library';

const { Criteria } = HeyPanel.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    components: {
        MtText
    },
    props: {
        // eslint-disable-next-line vue/require-prop-types
        channel: {
            required: true,
        },

        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    computed: {
        domainCriteria() {
            const criteria = new Criteria(1, 25);
            criteria.addFilter(Criteria.equals('channelId', this.channel.id));

            return criteria;
        },
    },
};
