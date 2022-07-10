require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { i18nVue } from 'laravel-vue-i18n';
import sentry from './sentry';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
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
            .use(i18nVue, {
              resolve: lang => import(`../../lang/${lang}.json`),
            })
            .use(sentry, { release: process.env.MIX_SENTRY_RELEASE })
            .mixin({ methods: _.assign({ route }, require('./methods').default) })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
