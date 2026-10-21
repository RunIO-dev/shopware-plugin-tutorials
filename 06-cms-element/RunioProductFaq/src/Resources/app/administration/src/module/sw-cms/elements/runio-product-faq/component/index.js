const { Criteria } = Shopware.Data;

// What the element looks like inside the layout editor
export default {
    template: `
        <div class="runio-cms-el-product-faq">
            <h3>{{ $t('runio-product-faq.cms.title') }}</h3>
            <p v-for="faq in faqs" :key="faq.id">▸ {{ faq.question }}</p>
            <p v-if="!faqs.length">{{ $t('runio-product-faq.cms.empty') }}</p>
        </div>
    `,

    inject: ['repositoryFactory'],

    mixins: [Shopware.Mixin.getByName('cms-element')],

    data() {
        return {
            faqs: [],
        };
    },

    computed: {
        productId() {
            return this.element.config?.product?.value;
        },
    },

    watch: {
        productId: {
            handler: 'loadFaqs',
            immediate: true,
        },
    },

    created() {
        this.initElementConfig('runio-product-faq');
    },

    methods: {
        async loadFaqs() {
            const productId = this.productId;

            if (!productId) {
                this.faqs = [];
                return;
            }

            const criteria = new Criteria(1, 25)
                .addFilter(Criteria.equals('productId', productId))
                .addFilter(Criteria.equals('active', true))
                .addSorting(Criteria.sort('position'));

            try {
                const faqs = await this.repositoryFactory.create('runio_product_faq').search(criteria);

                // Ignore an older answer when the product was changed meanwhile
                if (productId === this.productId) {
                    this.faqs = faqs;
                }
            } catch {
                // For example, the user may not read questions (ACL)
                this.faqs = [];
            }
        },
    },
};
