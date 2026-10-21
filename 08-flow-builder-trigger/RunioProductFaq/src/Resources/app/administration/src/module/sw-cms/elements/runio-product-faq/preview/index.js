import './runio-cms-el-preview-product-faq.scss';

// The small picture in the "choose an element" dialog
export default {
    template: `
        <div class="runio-cms-el-preview-product-faq">
            <strong>{{ $t('runio-product-faq.cms.label') }}</strong>
            <span class="runio-cms-el-preview-product-faq__row"></span>
            <span class="runio-cms-el-preview-product-faq__row"></span>
            <span class="runio-cms-el-preview-product-faq__row"></span>
        </div>
    `,
};
