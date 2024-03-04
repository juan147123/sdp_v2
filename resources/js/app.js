import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import VueSweetalert2 from "vue-sweetalert2";
import PrimeVue from 'primevue/config';
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import ToastService from 'primevue/toastservice';
import * as env from '../enviroments/enviroments';

alertify.set("notifier", "position", "top-right");
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
            app.use(plugin);
            app.use(PrimeVue);
            app.use(ToastService);
            app.use(VueSweetalert2);
            app.use(ZiggyVue, Ziggy);
            app.config.globalProperties.$env = env;
            app.mount(el);
            return app;
    },
    progress: {
        color: '#4B5563',
    },
});
