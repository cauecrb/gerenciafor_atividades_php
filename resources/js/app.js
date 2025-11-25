import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import route from 'ziggy-js';

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue');
    return pages[`./Pages/${name}.vue`]();
  },
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) });
    vueApp.use(plugin);
    // eslint-disable-next-line no-undef
    window.route = (name, params = {}, absolute = false) =>
      route(name, params, absolute, Ziggy);
    // Provide to Vue templates
    vueApp.config.globalProperties.route = (name, params = {}, absolute = false) =>
      route(name, params, absolute, Ziggy);
    vueApp.mount(el);
  },
});
