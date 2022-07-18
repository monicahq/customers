import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { i18nVue } from 'laravel-vue-i18n';
import sentry from './sentry';
import methods from './methods';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, app, props, plugin }) {
    return createApp({
      render: () => h(app, props),
      mounted() {
        this.$nextTick(() => {
          sentry.setContext(this);
        });
      }
    })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(i18nVue, {
        resolve: lang => resolvePageComponent(`../../lang/${lang}.json`, import.meta.glob('../../lang/*.json')),
      })
      .use(sentry, {
        release: import.meta.env.VITE_SENTRY_RELEASE,
      })
      .mixin({ methods: _.assign({ route }, methods) })
      .mount(el);
  },
});

InertiaProgress.init({ color: '#4B5563' });
