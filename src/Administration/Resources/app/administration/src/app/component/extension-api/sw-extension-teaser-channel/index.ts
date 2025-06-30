import template from './sw-extension-teaser-channel.html.twig';
import './sw-extension-teaser-channel.scss';

interface TeaserChannelConfig {
    positionId: string;
    channel: {
        title: string;
        description: string;
        iconName: string;
    };
    popoverComponent: {
        component: string;
        src: string;
        props: {
            label: string;
            locationId: string;
            variant: string;
        };
    };
}

/**
 * @sw-package innovation
 *
 * @private
 * @description A teaser channel for upselling service only, no public usage
 * @example-type dynamic
 * @component-example
 * <sw-extension-teaser-channel />
 */
export default HeyPanel.Component.wrapComponentConfig({
    template,

    computed: {
        teaserChannels(): TeaserChannelConfig[] {
            return HeyPanel.Store.get('teaserPopover').channels || [];
        },
    },
});
