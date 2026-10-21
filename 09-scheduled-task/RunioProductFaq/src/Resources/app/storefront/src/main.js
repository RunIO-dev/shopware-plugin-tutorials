// Loaded only on pages that contain the FAQ, so other pages don't download it
window.PluginManager.register(
    'RunioFaqSearch',
    () => import('./runio-faq-search/runio-faq-search.plugin'),
    '[data-runio-faq-search]',
);
