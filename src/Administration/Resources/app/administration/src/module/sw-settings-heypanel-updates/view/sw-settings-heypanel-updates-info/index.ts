import './sw-heypanel-updates-info.scss';
import template from './sw-heypanel-updates-info.html.twig';

const { Component } = HeyPanel;

/**
 * @sw-package framework
 * @private
 */
export default Component.wrapComponentConfig({
    template,

    props: {
        changelog: {
            type: String,
            required: true,
        },
        isLoading: {
            type: Boolean,
        },
    },
});
