import template from './sw-question-image.html.twig';
import './sw-question-image.scss';

/**
 * @sw-package framework
 *
 * @private
 * @description Component which renders an image.
 * @status ready
 * @example-type code-only
 * @component-example
 * <sw-image :item="item" isCover="true"></sw-image>
 */
export default {
    template,

    emits: [
        'sw-question-image-cover',
        'sw-question-image-delete',
    ],

    props: {
        mediaId: {
            type: String,
            required: true,
        },

        /**
         * @experimental stableVersion:v6.8.0 feature:SPATIAL_BASES
         */
        isSpatial: {
            type: Boolean,
            required: false,
            default: false,
        },

        /**
         * @experimental stableVersion:v6.8.0 feature:SPATIAL_BASES
         */
        isArReady: {
            type: Boolean,
            required: false,
            default: false,
        },

        isCover: {
            type: Boolean,
            required: false,
            default: false,
        },

        isPlaceholder: {
            type: Boolean,
            required: false,
            default: false,
        },

        showCoverLabel: {
            type: Boolean,
            required: false,
            // eslint-disable-next-line vue/no-boolean-default
            default: true,
        },
    },

    computed: {
        questionImageClasses() {
            return {
                'is--placeholder': this.isPlaceholder,
                'is--cover': this.isCover && this.showCoverLabel,
                'is--spatial': this.isSpatial,
            };
        },
    },
};
