import './acl';
import './page/runio-product-faq-list';
import './page/runio-product-faq-detail';

Shopware.Module.register('runio-product-faq', {
    type: 'plugin',
    name: 'runio-product-faq',
    title: 'runio-product-faq.general.title',
    description: 'runio-product-faq.general.description',
    color: 'var(--sw-color-module-green-default)',
    icon: 'regular-comments',
    entity: 'runio_product_faq',

    routes: {
        list: {
            component: 'runio-product-faq-list',
            path: 'list',
            meta: { privilege: 'runio_product_faq.viewer' },
        },
        create: {
            component: 'runio-product-faq-detail',
            path: 'create',
            meta: { parentPath: 'runio.product.faq.list', privilege: 'runio_product_faq.creator' },
        },
        detail: {
            component: 'runio-product-faq-detail',
            path: 'detail/:id',
            meta: { parentPath: 'runio.product.faq.list', privilege: 'runio_product_faq.viewer' },
            props: {
                default: (route) => ({ faqId: route.params.id.toLowerCase() }),
            },
        },
    },

    navigation: [{
        id: 'runio-product-faq',
        label: 'runio-product-faq.general.title',
        path: 'runio.product.faq.list',
        parent: 'sw-catalogue',
        privilege: 'runio_product_faq.viewer',
        position: 100,
    }],
});
