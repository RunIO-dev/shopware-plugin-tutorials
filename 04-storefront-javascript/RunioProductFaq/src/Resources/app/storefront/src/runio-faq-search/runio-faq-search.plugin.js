const { PluginBaseClass } = window;

export default class RunioFaqSearchPlugin extends PluginBaseClass {
    static options = {
        inputSelector: '.runio-faq-search-input',
        itemSelector: '.runio-faq-item',
        emptySelector: '.runio-faq-empty',
        statusSelector: '.runio-faq-status',
        statusText: '%visible% of %total% questions match',
    };

    init() {
        this.input = this.el.querySelector(this.options.inputSelector);
        this.items = [...this.el.querySelectorAll(this.options.itemSelector)];
        this.empty = this.el.querySelector(this.options.emptySelector);
        this.status = this.el.querySelector(this.options.statusSelector);
        this.openBefore = null;

        this.input.addEventListener('input', this.onInput.bind(this));
    }

    onInput() {
        const term = this.input.value.trim().toLowerCase();

        // Remember what the customer had open, so clearing the search restores it
        if (term && !this.openBefore) {
            this.openBefore = this.items.map((item) => item.open);
        }

        let visible = 0;

        this.items.forEach((item, index) => {
            const match = item.textContent.toLowerCase().includes(term);

            item.hidden = !match;
            item.open = term ? match : this.openBefore?.[index] ?? item.open;

            if (match) {
                visible++;
            }
        });

        if (!term) {
            this.openBefore = null;
        }

        this.empty.hidden = visible > 0;

        // Screen readers announce changes to this role="status" element
        this.status.textContent = term ? this.options.statusText
            .replace('%visible%', visible)
            .replace('%total%', this.items.length) : '';

        this.$emitter.publish('runioFaqSearch/filtered', { term, visible });
    }
}
