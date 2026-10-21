// The settings panel of the element
export default {
    template: `
        <div class="runio-cms-el-config-product-faq">
            <sw-entity-single-select
                v-model:value="element.config.product.value"
                entity="product"
                :label="$t('runio-product-faq.cms.labelProduct')"
                :help-text="$t('runio-product-faq.cms.helpProduct')"
                show-clearable-button
            />
        </div>
    `,

    mixins: [Shopware.Mixin.getByName('cms-element')],

    created() {
        this.initElementConfig('runio-product-faq');
    },
};
