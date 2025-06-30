/**
 * @sw-package discovery
 */
HeyPanel.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'cms',
    roles: {
        viewer: {
            privileges: [
                'category:read',
                'category_translation:read',
                'cms_page:read',
                'cms_section:read',
                'cms_block:read',
                'cms_slot:read',
                'landing_page:read',
                'media:read',
                'media_folder:read',
                'media_default_folder:read',
                'channel:read',
                'delivery_time:read',
                'question:read',
                'question_media:read',
                'question_sorting:read',
                'property_group:read',
                'property_group_option:read',
                'question_cross_selling:read',
                'question_cross_selling_assigned_questions:read',
                'question_manufacturer:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'cms_page:update',
                'cms_section:create',
                'cms_section:update',
                'cms_section:delete',
                'cms_block:create',
                'cms_block:update',
                'cms_block:delete',
                'cms_slot:create',
                'cms_slot:update',
                'cms_slot:delete',
                HeyPanel.Service('privileges').getPrivileges('media.creator'),
                'currency:read',
                'question_stream:read',
                'question_manufacturer:read',
                'category:update',
                'landing_page:update',
            ],
            dependencies: [
                'cms.viewer',
            ],
        },
        creator: {
            privileges: [
                'cms_page:create',
            ],
            dependencies: [
                'cms.viewer',
                'cms.editor',
            ],
        },
        deleter: {
            privileges: [
                'cms_page:delete',
            ],
            dependencies: [
                'cms.viewer',
            ],
        },
    },
});
