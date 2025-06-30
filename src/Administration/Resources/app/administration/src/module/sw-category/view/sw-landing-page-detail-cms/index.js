import template from './sw-landing-page-detail-cms.html.twig';
import './sw-landing-page-detail-cms.scss';

/**
 * @sw-package discovery
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    props: {
        isLoading: {
            type: Boolean,
            required: true,
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
