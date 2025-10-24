# راهنمای اضافه کردن تمپلیت جدید
# Guide for Adding New Templates

## مقدمه | Introduction

این سند راهنمای کامل برای اضافه کردن تمپلیت‌های جدید به سیستم فروشگاه‌ساز است.
This document provides a complete guide for adding new templates to the app builder system.

---

## مراحل اضافه کردن تمپلیت | Steps to Add a Template

### 1. آماده‌سازی مخزن GitHub | Prepare GitHub Repository

```bash
# Create a new repository for your template
# مخزن جدیدی برای تمپلیت خود ایجاد کنید

# Repository should contain:
# - Flutter/React Native/Web app code
# - Configuration handling code
# - Build scripts

# مخزن باید شامل موارد زیر باشد:
# - کد اپلیکیشن Flutter/React Native/Web
# - کد مدیریت تنظیمات
# - اسکریپت‌های ساخت
```

### 2. طراحی Config Schema | Design Config Schema

فیلدهای مورد نیاز تمپلیت خود را مشخص کنید:
Define the fields needed for your template:

```json
[
  {
    "name": "app_name",
    "label_en": "Application Name",
    "label_fa": "نام اپلیکیشن",
    "type": "text",
    "required": true,
    "immutable": false,
    "placeholder": "My Awesome App",
    "default": ""
  },
  {
    "name": "package_id",
    "label_en": "Package ID",
    "label_fa": "شناسه پکیج",
    "type": "text",
    "required": true,
    "immutable": true,
    "placeholder": "com.example.app",
    "default": ""
  },
  {
    "name": "api_url",
    "label_en": "API Base URL",
    "label_fa": "آدرس پایه API",
    "type": "url",
    "required": true,
    "immutable": false,
    "placeholder": "https://api.example.com",
    "default": ""
  },
  {
    "name": "theme_mode",
    "label_en": "Theme Mode",
    "label_fa": "حالت تم",
    "type": "select",
    "required": true,
    "immutable": false,
    "options": [
      {"value": "light", "label_en": "Light", "label_fa": "روشن"},
      {"value": "dark", "label_en": "Dark", "label_fa": "تاریک"},
      {"value": "auto", "label_en": "Auto", "label_fa": "خودکار"}
    ],
    "default": "auto"
  },
  {
    "name": "enable_analytics",
    "label_en": "Enable Analytics",
    "label_fa": "فعال‌سازی تحلیل‌گر",
    "type": "boolean",
    "required": false,
    "immutable": false,
    "default": true
  }
]
```

### 3. ایجاد تمپلیت در دیتابیس | Create Template in Database

از پنل ادمین استفاده کنید یا مستقیماً در دیتابیس ایجاد کنید:
Use admin panel or directly insert into database:

```sql
INSERT INTO templates (
    uid,
    name_fa,
    name_en,
    description_fa,
    description_en,
    category,
    tags,
    thumbnail,
    github_repo,
    github_branch,
    config_schema,
    status,
    is_primary,
    created_at,
    updated_at
) VALUES (
    'ecommerce_template_001',
    'فروشگاه آنلاین',
    'E-Commerce Template',
    'قالب کامل فروشگاه آنلاین با سبد خرید و پرداخت',
    'Complete e-commerce template with cart and payment',
    'ecommerce',
    '["shop", "cart", "payment", "تجارت الکترونیک"]',
    '/uploads/templates/ecommerce_thumb.png',
    'https://github.com/yourorg/ecommerce-template',
    'main',
    '[...your config schema JSON...]',
    1,
    0,
    UNIX_TIMESTAMP(),
    UNIX_TIMESTAMP()
);
```

### 4. آماده‌سازی تمپلیت کد | Prepare Code Template

تمپلیت شما باید فایل config را از مسیر مشخص بخواند:
Your template should read config from specified path:

#### مثال Flutter | Flutter Example:

```dart
// lib/config/app_config.dart
class AppConfig {
  static const String appName = "{APP_NAME}";
  static const String packageId = "{PACKAGE_ID}";
  static const String apiUrl = "{API_URL}";
  static const String themeMode = "{THEME_MODE}";
  static const bool enableAnalytics = {ENABLE_ANALYTICS};
}

// Usage in your app
import 'config/app_config.dart';

void main() {
  print(AppConfig.appName);
  runApp(MyApp());
}
```

#### مثال React Native | React Native Example:

```javascript
// config/app-config.json
{
  "appName": "{APP_NAME}",
  "packageId": "{PACKAGE_ID}",
  "apiUrl": "{API_URL}",
  "themeMode": "{THEME_MODE}",
  "enableAnalytics": {ENABLE_ANALYTICS}
}

// Usage
import config from './config/app-config.json';

console.log(config.appName);
```

### 5. تنظیم Build Pipeline | Configure Build Pipeline

#### GitHub Actions Example:

```yaml
# .github/workflows/build.yml
name: Build App

on:
  push:
    branches:
      - 'app-*'

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup Flutter
        uses: subosito/flutter-action@v2
        
      - name: Install dependencies
        run: flutter pub get
        
      - name: Build APK
        run: flutter build apk --release
        
      - name: Upload artifact
        uses: actions/upload-artifact@v2
        with:
          name: app-release.apk
          path: build/app/outputs/flutter-apk/app-release.apk
```

### 6. تست تمپلیت | Test Template

1. از پنل ادمین تمپلیت را فعال کنید
   Enable template from admin panel

2. یک اپلیکیشن تست ایجاد کنید
   Create a test application

3. تنظیمات را پر کنید
   Fill in the configuration

4. Build درخواست کنید
   Request a build

5. نتیجه را بررسی کنید
   Check the result

---

## انواع فیلد | Field Types

### Text
```json
{
  "type": "text",
  "placeholder": "Enter text here",
  "validation": "min:3|max:50"
}
```

### Number
```json
{
  "type": "number",
  "min": 1,
  "max": 100,
  "default": 10
}
```

### URL
```json
{
  "type": "url",
  "placeholder": "https://example.com"
}
```

### Email
```json
{
  "type": "email",
  "placeholder": "user@example.com"
}
```

### Color
```json
{
  "type": "color",
  "default": "#FF5722"
}
```

### Select
```json
{
  "type": "select",
  "options": [
    {"value": "option1", "label_en": "Option 1", "label_fa": "گزینه ۱"},
    {"value": "option2", "label_en": "Option 2", "label_fa": "گزینه ۲"}
  ]
}
```

### Boolean
```json
{
  "type": "boolean",
  "default": true
}
```

### Textarea
```json
{
  "type": "textarea",
  "rows": 4,
  "placeholder": "Enter long text here"
}
```

---

## نکات مهم | Important Notes

### 1. فیلدهای Immutable
فیلدهایی که `"immutable": true` دارند بعد از اولین ذخیره قابل تغییر نیستند.
Fields with `"immutable": true` cannot be changed after first save.

### 2. سازگاری با Build System
تمپلیت شما باید با سیستم build سازگار باشد (CodeMagic، GitHub Actions، etc.)
Your template must be compatible with the build system.

### 3. مدیریت نسخه
از semantic versioning برای نسخه‌های تمپلیت استفاده کنید.
Use semantic versioning for template versions.

### 4. مستندات
مستندات کاملی برای توسعه‌دهندگانی که از تمپلیت استفاده می‌کنند فراهم کنید.
Provide comprehensive documentation for developers using your template.

---

## مثال کامل | Complete Example

برای مشاهده یک مثال کامل، تمپلیت پیش‌فرض سیستم را بررسی کنید:
To see a complete example, check the default system template:

```sql
SELECT * FROM templates WHERE is_primary = 1;
```

---

## پشتیبانی | Support

برای سوالات و مشکلات:
For questions and issues:

- مستندات: [DOCUMENTATION_URL]
- پشتیبانی: [SUPPORT_EMAIL]
- مخزن: [GITHUB_REPO]
