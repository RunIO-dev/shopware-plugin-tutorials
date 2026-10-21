import template from './runio-product-faq-detail.html.twig';

const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Shopware.Component.register('runio-product-faq-detail', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [Shopware.Mixin.getByName('notification')],

    props: {
        faqId: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            faq: null,
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    metaInfo() {
        return { title: this.$createTitle(this.faq?.question) };
    },

    computed: {
        faqRepository() {
            return this.repositoryFactory.create('runio_product_faq');
        },

        ...mapPropertyErrors('faq', ['productId', 'question', 'answer']),
    },

    watch: {
        faqId() {
            this.loadFaq();
        },
    },

    created() {
        this.loadFaq();
    },

    methods: {
        async loadFaq() {
            if (!this.faqId) {
                this.faq = this.faqRepository.create();
                this.faq.position = 0;
                this.faq.active = true;

                return;
            }

            this.isLoading = true;

            try {
                this.faq = await this.faqRepository.get(this.faqId);
            } catch {
                this.createNotificationError({ message: this.$t('runio-product-faq.detail.messageLoadError') });
            } finally {
                this.isLoading = false;
            }
        },

        async onSave() {
            this.isLoading = true;

            try {
                await this.faqRepository.save(this.faq);
                this.isSaveSuccessful = true;
                this.createNotificationSuccess({ message: this.$t('runio-product-faq.detail.messageSaveSuccess') });

                if (!this.faqId) {
                    this.$router.push({ name: 'runio.product.faq.detail', params: { id: this.faq.id } });

                    return;
                }

                await this.loadFaq();
            } catch {
                this.createNotificationError({ message: this.$t('runio-product-faq.detail.messageSaveError') });
            } finally {
                this.isLoading = false;
            }
        },
    },
});
