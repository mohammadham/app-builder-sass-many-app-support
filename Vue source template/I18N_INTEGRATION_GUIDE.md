# راهنمای یکپارچه‌سازی i18n با Vue Application

## گام 1: نصب وابستگی‌ها

```bash
cd "/app/Vue source template"
yarn add vue-i18n@9
```

## گام 2: اصلاح main.js

فایل `/app/Vue source template/src/main.js` را به شکل زیر اصلاح کنید:

```javascript
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import i18n, { setupI18n } from './plugins/i18n'; // اضافه شد

// Setup i18n before creating app
setupI18n().then(() => {
  const app = createApp(App);
  
  app.use(router);
  app.use(vuetify);
  app.use(i18n); // اضافه شد
  
  app.mount('#app');
});
```

## گام 3: اضافه کردن LanguageSwitcher به Layout

در فایل Layout اصلی (مثلاً `App.vue` یا `AppBar.vue`):

```vue
<template>
  <v-app-bar app>
    <v-toolbar-title>My App</v-toolbar-title>
    <v-spacer></v-spacer>
    
    <!-- Language Switcher -->
    <language-switcher></language-switcher>
  </v-app-bar>
</template>

<script>
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';

export default {
  components: {
    LanguageSwitcher
  }
};
</script>
```

## گام 4: استفاده از ترجمه‌ها در کامپوننت‌ها

### در Template:
```vue
<template>
  <div>
    <h1>{{ $t('key_welcome') }}</h1>
    <p>{{ $t('key_description') }}</p>
  </div>
</template>
```

### در Script (Composition API):
```vue
<script setup>
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

console.log(t('key_hello')); // ترجمه
console.log(locale.value); // زبان فعلی
</script>
```

### در Script (Options API):
```vue
<script>
export default {
  methods: {
    getMessage() {
      return this.$t('key_message');
    }
  }
};
</script>
```

## گام 5: اجرای SQL برای ترجمه‌های فارسی

```bash
cd /app/Backend
mysql -u root -p your_database < database_seeds/persian_translations.sql
```

یا از طریق phpMyAdmin محتوای فایل را import کنید.

## گام 6: اضافه کردن Routes برای صفحات ادمین

در `/app/Vue source template/src/router/index.js`:

```javascript
import AdminLanguagesList from '@/views/admin/languages/AdminLanguagesList.vue';
import AdminTranslationsEditor from '@/views/admin/languages/AdminTranslationsEditor.vue';

// در بخش admin routes:
{
  path: '/admin/languages',
  name: 'AdminLanguagesList',
  component: AdminLanguagesList,
  meta: { requiresAdmin: true }
},
{
  path: '/admin/translations',
  name: 'AdminTranslationsEditor',
  component: AdminTranslationsEditor,
  meta: { requiresAdmin: true }
}
```

## گام 7: اضافه کردن به منوی ادمین

در منوی سایدبار ادمین:

```vue
{
  title: 'Languages',
  icon: 'mdi-translate',
  to: '/admin/languages'
},
{
  title: 'Translations',
  icon: 'mdi-text-box-multiple',
  to: '/admin/translations'
}
```

## تست

1. اجرای سرور: `yarn serve`
2. رفتن به `/admin/languages`
3. تغییر وضعیت زبان‌ها
4. رفتن به `/admin/translations` برای ویرایش ترجمه‌ها
5. استفاده از Language Switcher در navbar

## نکات مهم

- ترجمه‌ها از API بارگذاری می‌شوند
- Fallback به انگلیسی در صورت عدم وجود ترجمه
- تغییر خودکار RTL/LTR
- ذخیره زبان در localStorage
- Hot reload برای تغییرات
