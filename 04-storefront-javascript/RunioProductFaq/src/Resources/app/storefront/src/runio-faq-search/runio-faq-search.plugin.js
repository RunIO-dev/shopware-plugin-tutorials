const { PluginBaseClass } = window;

export default class RunioFaqSearchPlugin extends PluginBaseClass {
    static options = {
        inputSelector: '.runio-faq-search-input',
        itemSelector: '.runio-faq-item',
        emptySelector: '.runio-faq-empty',
    };

    init() {
        this.input = this.el.querySelector(this.options.inputSelector);
        this.items = [...this.el.querySelectorAll(this.options.itemSelector)];
        this.empty = this.el.querySelector(this.options.emptySelector);
        this.initiallyOpen = this.items.map((item) => item.open);

        this.input.addEventListener('input', this.onInput.bind(this));
    }

    onInput() {
        const term = this.input.value.trim().toLowerCase();
        let visible = 0;

        this.items.forEach((item, index) => {
            const match = item.textContent.toLowerCase().includes(term);

            item.hidden = !match;
            item.open = term ? match : this.initiallyOpen[index];

            if (match) {
                visible++;
            }
        });

        this.empty.hidden = visible > 0;

        this.$emitter.publish('runioFaqSearch/filtered', { term, visible });
    }
}
