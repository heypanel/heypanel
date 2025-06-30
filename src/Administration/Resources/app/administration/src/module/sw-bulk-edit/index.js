/**
 * @sw-package framework
 */
import './init/services.init';

/* eslint-disable max-len, sw-deprecation-rules/private-feature-declarations */
HeyPanel.Component.register('sw-bulk-edit-member', () => import('./page/sw-bulk-edit-member'));
HeyPanel.Component.extend(
    'sw-bulk-edit-custom-fields',
    'sw-custom-field-set-renderer',
    () => import('./component/sw-bulk-edit-custom-fields'),
);
HeyPanel.Component.register('sw-bulk-edit-change-type', () => import('./component/sw-bulk-edit-change-type'));
HeyPanel.Component.register(
    'sw-bulk-edit-change-type-field-renderer',
    () => import('./component/sw-bulk-edit-change-type-field-renderer'),
);
HeyPanel.Component.extend(
    'sw-bulk-edit-form-field-renderer',
    'sw-form-field-renderer',
    () => import('./component/sw-bulk-edit-form-field-renderer'),
);
HeyPanel.Component.register(
    'sw-bulk-edit-question-visibility',
    () => import('./component/question/sw-bulk-edit-question-visibility'),
);
HeyPanel.Component.register('sw-bulk-edit-question-media', () => import('./component/question/sw-bulk-edit-question-media'));
HeyPanel.Component.extend(
    'sw-bulk-edit-question-media-form',
    'sw-question-media-form',
    () => import('./component/question/sw-bulk-edit-question-media-form'),
);
HeyPanel.Component.extend(
    'sw-bulk-edit-question-description',
    'sw-text-editor',
    () => import('./component/question/sw-bulk-edit-question-description'),
);
HeyPanel.Component.register('sw-bulk-edit-save-modal', () => import('./component/sw-bulk-edit-save-modal'));
HeyPanel.Component.register('sw-bulk-edit-save-modal-confirm', () => import('./component/sw-bulk-edit-save-modal-confirm'));
HeyPanel.Component.register('sw-bulk-edit-save-modal-process', () => import('./component/sw-bulk-edit-save-modal-process'));
HeyPanel.Component.register('sw-bulk-edit-save-modal-success', () => import('./component/sw-bulk-edit-save-modal-success'));
HeyPanel.Component.register('sw-bulk-edit-save-modal-error', () => import('./component/sw-bulk-edit-save-modal-error'));
/* eslint-enable max-len, sw-deprecation-rules/private-feature-declarations */

const { Module } = HeyPanel;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Module.register('sw-bulk-edit', {
    type: 'core',
    name: 'bulk-edit',
    title: 'sw-bulk-edit.general.mainMenuTitle',
    description: 'sw-bulk-edit.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',

    routes: {
        member: {
            component: 'sw-bulk-edit-member',
            path: 'member',
            meta: {
                parentPath: 'sw.member.index',
            },
            children: {
                save: {
                    component: 'sw-bulk-edit-save-modal',
                    path: 'save',
                    redirect: {
                        name: 'sw.bulk.edit.member.save.confirm',
                    },
                    children: {
                        confirm: {
                            component: 'sw-bulk-edit-save-modal-confirm',
                            path: 'confirm',
                        },
                        process: {
                            component: 'sw-bulk-edit-save-modal-process',
                            path: 'process',
                        },
                        success: {
                            component: 'sw-bulk-edit-save-modal-success',
                            path: 'success',
                        },
                        error: {
                            component: 'sw-bulk-edit-save-modal-error',
                            path: 'error',
                        },
                    },
                },
            },
        },
    },
});
