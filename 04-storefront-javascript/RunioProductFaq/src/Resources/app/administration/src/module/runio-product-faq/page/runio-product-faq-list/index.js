import template from './runio-product-faq-list.html.twig';

const { Criteria } = Shopware.Data;

Shopware.Component.register('runio-product-faq-list', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [Shopware.Mixin.getByName('notification')],

    data() {
        return {
            faqs: null,
            isLoading: true,
        };
    },

    metaInfo() {
        return { title: this.$createTitle() };
    },

    computed: {
        faqRepository() {
            return this.repositoryFactory.create('runio_product_faq');
        },

        columns() {
            return [
                { property: 'question', label: 'runio-product-faq.list.columnQuestion', routerLink: 'runio.product.faq.detail', primary: true },
                { property: 'product.translated.name', label: 'runio-product-faq.list.columnProduct' },
                { property: 'position', label: 'runio-product-faq.list.columnPosition', align: 'right' },
                { property: 'active', label: 'runio-product-faq.list.columnActive', align: 'center' },
            ];
        },

        criteria() {
            const criteria = new Criteria(1, 25);
            criteria.addAssociation('product');
            criteria.addSorting(Criteria.sort('position', 'ASC'));

            return criteria;
        },
    },

    created() {
        this.getList();
    },

    methods: {
        async getList() {
            this.isLoading = true;
            this.faqs = await this.faqRepository.search(this.criteria);
            this.isLoading = false;
        },

        onDeleteFailed() {
            this.createNotificationError({ message: this.$t('runio-product-faq.list.messageDeleteError') });
        },
    },
});
