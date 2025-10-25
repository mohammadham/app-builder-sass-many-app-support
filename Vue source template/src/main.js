// Plugins
import { registerPlugins } from '@/plugins';
// Components
import App from './App.vue';
// Composables
import { createApp } from 'vue';
import router from "@/router";
import store from "@/store";
import {getLocalValue} from "@/lib/local";
import  "./assets/css/app.css";
import i18n, { setupI18n } from '@/plugins/i18n';

// Setup i18n and mount app
setupI18n().then(() => {
  const app = createApp(App)
    .use(router)
    .use(store)
    .use(i18n);

  app.config.globalProperties.$tr = getLocalValue;

  registerPlugins(app);

  app.mount('#app');
});
