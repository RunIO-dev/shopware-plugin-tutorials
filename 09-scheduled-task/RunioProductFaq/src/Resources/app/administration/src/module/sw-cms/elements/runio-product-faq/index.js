Shopware.Component.register('runio-cms-el-product-faq', () => import('./component'));
Shopware.Component.register('runio-cms-el-config-product-faq', () => import('./config'));
Shopware.Component.register('runio-cms-el-preview-product-faq', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'runio-product-faq',
    label: 'runio-product-faq.cms.label',
    component: 'runio-cms-el-product-faq',
    configComponent: 'runio-cms-el-config-product-faq',
    previewComponent: 'runio-cms-el-preview-product-faq',
    defaultConfig: {
        product: {
            source: 'static',
            value: null,
        },
    },
});
