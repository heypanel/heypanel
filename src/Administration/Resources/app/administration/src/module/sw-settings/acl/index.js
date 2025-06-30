/**
 * @sw-package fundamentals@framework
 */
HeyPanel.Service('privileges').addPrivilegeMappingEntry({
    category: 'additional_permissions',
    parent: null,
    key: 'system',
    roles: {
        system_config: {
            privileges: [
                'system_config:read',
                'system_config:update',
                'system_config:create',
                'system_config:delete',
                'channel:read',
                'cms_page:read',
                'question_sorting:read',
                'custom_field:read',
                'custom_field_set_relation:read',
                'question_sorting:create',
                'question_sorting:update',
                'question_sorting:delete',
                'seo_url_template:read',
                'seo_url_template:create',
                'seo_url_template:update',
            ],
            dependencies: [],
        },
    },
});
