import * as Sentry from '@sentry/vue';
import { BrowserTracing } from "@sentry/tracing";
import { Inertia } from '@inertiajs/inertia';

let activated = false;

const xsrfToken = () => {
  const xsrfToken = document.cookie.match(new RegExp('(^|;\\s*)(XSRF-TOKEN)=([^;]*)'));
  return xsrfToken ? decodeURIComponent(xsrfToken[3]) : null;
};

const install = (app, options) => {
  if (typeof SentryConfig !== 'undefined' && SentryConfig.dsn !== '') {
    Sentry.init({
      dsn: SentryConfig.dsn,
      tunnel: '/sentry/tunnel',
      environment: SentryConfig.environment || null,
      release: options.release || '',
      sendDefaultPii: SentryConfig.sendDefaultPii || false,
      tracesSampleRate: SentryConfig.tracesSampleRate || 0.0,
      integrations: [
        SentryConfig.tracesSampleRate > 0 ? new BrowserTracing() : null,
      ],
      transportOptions: {
        headers: { 'X-XSRF-TOKEN': xsrfToken() },
      },
    });
    app.mixin(Sentry.createTracingMixins({ trackComponents: true }))
    activated = true;
  }
};

const setContext = (vm) => {
  if (activated && typeof vm.$page !== 'undefined') {
    if (vm.$page.props.user) {
      Sentry.setUser({ id: vm.$page.props.user.id });
    }
    Sentry.setTag('page.component', vm.$page.component);
    vm.$once(
      'hook:destroyed',
      Inertia.on('success', (event) => {
        Sentry.setTag('page.component', event.detail.page.component);
      })
    );
  }
};

export default {
  install,
  setContext,
};
