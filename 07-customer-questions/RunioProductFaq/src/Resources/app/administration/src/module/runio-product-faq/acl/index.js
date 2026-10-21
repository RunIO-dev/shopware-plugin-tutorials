Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'catalogues',
    key: 'runio_product_faq',
    roles: {
        viewer: {
            privileges: ['runio_product_faq:read', 'product:read'],
            dependencies: [],
        },
        editor: {
            privileges: ['runio_product_faq:update'],
            dependencies: ['runio_product_faq.viewer'],
        },
        creator: {
            privileges: ['runio_product_faq:create'],
            dependencies: ['runio_product_faq.viewer', 'runio_product_faq.editor'],
        },
        deleter: {
            privileges: ['runio_product_faq:delete'],
            dependencies: ['runio_product_faq.viewer'],
        },
    },
});
