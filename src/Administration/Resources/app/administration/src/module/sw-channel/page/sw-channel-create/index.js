/**
 * @sw-package discovery
 */

import template from './sw-channel-create.html.twig';

const utils = HeyPanel.Utils;

const insertIdIntoRoute = (to, from, next) => {
    if (to.name.includes('sw.channel.create') && !to.params.id) {
        to.params.id = utils.createId();
    }

    next();
};

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    beforeRouteEnter: insertIdIntoRoute,

    beforeRouteUpdate: insertIdIntoRoute,

    computed: {
        allowSaving() {
            return this.acl.can('channel.creator');
        },
    },

    methods: {
        createdComponent() {
            if (!this.$route.params.typeId) {
                return;
            }

            if (!HeyPanel.Store.get('context').isSystemDefaultLanguage) {
                HeyPanel.Store.get('context').resetLanguageToDefault();
            }

            this.channel = this.channelRepository.create();
            this.channel.typeId = this.$route.params.typeId;
            this.channel.active = false;

            this.$super('createdComponent');
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({
                name: 'sw.channel.detail',
                params: { id: this.channel.id },
            });
        },

        onSave() {
            this.$super('onSave');
        },
    },
};
