import template from './sw-landing-page-view.html.twig';

const { Mixin } = HeyPanel;

/**
 * @sw-package discovery
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['acl'],

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        isLoading: {
            type: Boolean,
            required: true,
            default: false,
        },
    },

    computed: {
        landingPage() {
            return HeyPanel.Store.get('swCategoryDetail').landingPage;
        },

        cmsPage() {
            return HeyPanel.Store.get('cmsPage').currentPage;
        },
    },
};
