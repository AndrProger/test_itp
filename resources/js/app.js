/**
 * @file: app.js
 * @description: Основной файл JavaScript приложения с настройкой Inertia.js и Vue.js
 * @dependencies: Vue 3, Inertia.js, Vite
 * @created: 2024-01-01
 */

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// --- Vuex и vuex-persist ---
import { createStore } from 'vuex';
import VuexPersistence from 'vuex-persist';

const vuexLocal = new VuexPersistence({
  storage: window.localStorage,
  key: 'shinka-filters',
});

const store = createStore({
  state: {
    filters: {
      name: '',
      hasImage: false,
      roomsCount: [],
      areaMin: null,
      areaMax: null,
      sortBy: 'id',
      sortDirection: 'desc',
    },
    page: 1,
  },
  mutations: {
    setFilters(state, filters) {
      state.filters = { ...state.filters, ...filters };
    },
    setPage(state, page) {
      state.page = page;
    },
    clearFilters(state) {
      state.filters = {
        name: '',
        hasImage: false,
        roomsCount: [],
        areaMin: null,
        areaMax: null,
        sortBy: 'id',
        sortDirection: 'desc',
      };
      state.page = 1;
    },
  },
  plugins: [vuexLocal.plugin],
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });
        vueApp.use(plugin);
        vueApp.use(store);
        // vueApp.use(ZiggyVue, Ziggy);
        vueApp.mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
