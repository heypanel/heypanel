/**
 * @sw-package discovery
 */

import './service/export-template.service';
import './question-export-templates';
import './service/domain-link.service';
import './service/channel-favorites.service';
import './component/structure/sw-admin-menu-extension';
import './acl';

import defaultSearchConfiguration from './default-search-configuration';

const { Module } = HeyPanel;

/* eslint-disable max-len, sw-deprecation-rules/private-feature-declarations */
HeyPanel.Component.register(
    'sw-channel-defaults-select',
    () => import('./component/sw-channel-defaults-select'),
);
HeyPanel.Component.register('sw-channel-modal', () => import('./component/sw-channel-modal'));
HeyPanel.Component.register('sw-channel-modal-grid', () => import('./component/sw-channel-modal-grid'));
HeyPanel.Component.register('sw-channel-modal-detail', () => import('./component/sw-channel-modal-detail'));
HeyPanel.Component.register('sw-channel-detail-domains', () => import('./component/sw-channel-detail-domains'));
HeyPanel.Component.register(
    'sw-channel-detail-hreflang',
    () => import('./component/sw-channel-detail-hreflang'),
);
HeyPanel.Component.register('sw-channel-detail', () => import('./page/sw-channel-detail'));
HeyPanel.Component.extend(
    'sw-channel-create',
    'sw-channel-detail',
    () => import('./page/sw-channel-create'),
);
HeyPanel.Component.register('sw-channel-list', () => import('./page/sw-channel-list'));
HeyPanel.Component.register('sw-channel-detail-base', () => import('./view/sw-channel-detail-base'));
HeyPanel.Component.register('sw-channel-detail-questions', () => import('./view/sw-channel-detail-questions'));
HeyPanel.Component.register('sw-channel-detail-analytics', () => import('./view/sw-channel-detail-analytics'));
HeyPanel.Component.extend(
    'sw-channel-create-base',
    'sw-channel-detail-base',
    () => import('./view/sw-channel-create-base'),
);
HeyPanel.Component.register(
    'sw-channel-detail-question-comparison',
    () => import('./view/sw-channel-detail-question-comparison'),
);
HeyPanel.Component.register(
    'sw-channel-detail-question-comparison-preview',
    () => import('./view/sw-channel-detail-question-comparison-preview'),
);
HeyPanel.Component.register(
    'sw-channel-questions-assignment-modal',
    () => import('./component/sw-channel-questions-assignment-modal'),
);
HeyPanel.Component.register(
    'sw-channel-questions-assignment-single-questions',
    () => import('./component/sw-channel-questions-assignment-single-questions'),
);
HeyPanel.Component.register(
    'sw-channel-questions-assignment-dynamic-question-groups',
    () => import('./component/sw-channel-questions-assignment-dynamic-question-groups'),
);
HeyPanel.Component.register(
    'sw-channel-question-assignment-categories',
    () => import('./component/sw-channel-question-assignment-categories'),
);
HeyPanel.Component.register('sw-channel-menu', () => import('./component/structure/sw-channel-menu'));
/* eslint-enable max-len, sw-deprecation-rules/private-feature-declarations */

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Module.register('sw-channel', {
    type: 'core',
    name: 'channel',
    title: 'sw-channel.general.titleMenuItems',
    description: 'The module for managing Channels.',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#14D7A5',
    icon: 'regular-server',
    entity: 'channel',

    searchMatcher: (regex, labelType, manifest) => {
        const match = labelType.toLowerCase().match(regex);

        if (!match) {
            return false;
        }

        return [
            {
                name: manifest.name,
                icon: manifest.icon,
                color: manifest.color,
                label: labelType,
                entity: manifest.entity,
                route: manifest.routes.list,
                privilege: manifest.routes.list?.meta.privilege,
            },
        ];
    },

    routes: {
        detail: {
            component: 'sw-channel-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'sw.channel.list',
                privilege: 'channel.viewer',
            },
            redirect: {
                name: 'sw.channel.detail.base',
            },
            children: {
                base: {
                    component: 'sw-channel-detail-base',
                    path: 'base',
                    meta: {
                        parentPath: 'sw.channel.list',
                        privilege: 'channel.viewer',
                    },
                },
                questions: {
                    component: 'sw-channel-detail-questions',
                    path: 'questions',
                    meta: {
                        parentPath: 'sw.channel.list',
                        privilege: 'channel.viewer',
                    },
                },
                questionComparison: {
                    component: 'sw-channel-detail-question-comparison',
                    path: 'question-comparison',
                    meta: {
                        parentPath: 'sw.channel.list',
                        privilege: 'channel.viewer',
                    },
                },
                analytics: {
                    component: 'sw-channel-detail-analytics',
                    path: 'analytics',
                    meta: {
                        parentPath: 'sw.channel.list',
                        privilege: 'channel.viewer',
                    },
                },
            },
        },

        create: {
            component: 'sw-channel-create',
            path: 'create/:typeId',
            redirect: {
                name: 'sw.channel.create.base',
            },
            children: {
                base: {
                    component: 'sw-channel-create-base',
                    path: 'base',
                    meta: {
                        parentPath: 'sw.channel.list',
                        privilege: 'channel.creator',
                    },
                },
            },
        },

        list: {
            component: 'sw-channel-list',
            path: 'list',
            meta: {
                privilege: 'channel.viewer',
            },
        },
    },

    defaultSearchConfiguration,
});
