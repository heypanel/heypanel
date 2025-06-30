/**
 * @sw-package inventory
 */
HeyPanel.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'settings',
    key: 'tag',
    roles: {
        viewer: {
            privileges: [
                'tag:read',
                'question:read',
                'order:read',
                'member:read',
                'media:read',
                'newsletter_recipient:read',
                'shipping_method:read',
                'landing_page:read',
                'question_tag:read',
                'order_tag:read',
                'member_tag:read',
                'media_tag:read',
                'newsletter_recipient_tag:read',
                'shipping_method_tag:read',
                'landing_page_tag:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'tag:update',
                'question_tag:create',
                'question_tag:update',
                'question_tag:delete',
                'category_tag:create',
                'category_tag:update',
                'category_tag:delete',
                'order_tag:create',
                'order_tag:update',
                'order_tag:delete',
                'member_tag:create',
                'member_tag:update',
                'member_tag:delete',
                'media_tag:create',
                'media_tag:update',
                'media_tag:delete',
                'newsletter_recipient_tag:create',
                'newsletter_recipient_tag:update',
                'newsletter_recipient_tag:delete',
                'shipping_method_tag:create',
                'shipping_method_tag:update',
                'shipping_method_tag:delete',
                'landing_page_tag:create',
                'landing_page_tag:update',
                'landing_page_tag:delete',
            ],
            dependencies: [
                'tag.viewer',
            ],
        },
        creator: {
            privileges: [
                'tag:create',
            ],
            dependencies: [
                'tag.viewer',
                'tag.editor',
            ],
        },
        deleter: {
            privileges: [
                'tag:delete',
                'question_tag:delete',
                'category_tag:delete',
                'order_tag:delete',
                'member_tag:delete',
                'media_tag:delete',
                'newsletter_recipient_tag:delete',
                'shipping_method_tag:delete',
                'landing_page_tag:delete',
            ],
            dependencies: [
                'tag.viewer',
            ],
        },
    },
});
