/**
 * @sw-package framework
 */

import './acl';

const { Component, Module } = HeyPanel;

/** @private */
Component.register(
    'sw-settings-heypanel-updates-requirements',
    () => import('./view/sw-settings-heypanel-updates-requirements'),
);
/** @private */
Component.register('sw-settings-heypanel-updates-plugins', () => import('./view/sw-settings-heypanel-updates-plugins'));
/** @private */
Component.register('sw-settings-heypanel-updates-info', () => import('./view/sw-settings-heypanel-updates-info'));
/** @private */
Component.register('sw-settings-heypanel-updates-index', () => import('./page/sw-settings-heypanel-updates-index'));
/** @private */
Component.register('sw-settings-heypanel-updates-wizard', () => import('./page/sw-settings-heypanel-updates-wizard'));

/**
 * @private
 */
Module.register('sw-settings-heypanel-updates', {
    type: 'core',
    name: 'settings-heypanel-updates',
    title: 'sw-settings-heypanel-updates.general.emptyTitle',
    description: 'sw-settings-heypanel-updates.general.emptyTitle',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'regular-cog',
    favicon: 'icon-module-settings.png',

    routes: {
        wizard: {
            component: 'sw-settings-heypanel-updates-wizard',
            path: 'wizard',
            meta: {
                parentPath: 'sw.settings.index.system',
                privilege: 'system.core_update',
            },
        },
    },

    settingsItem: {
        privilege: 'system.core_update',
        group: 'system',
        to: 'sw.settings.heypanel.updates.wizard',
        icon: 'regular-sync',
    },
});
