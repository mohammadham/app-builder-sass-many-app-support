-- Translation keys for ZarinPal, E-namad, Landing Page, and Templates Gallery
-- Execute this SQL after setting up the database

-- ZarinPal Settings
INSERT INTO translations (lang_code, translation_key, translation_value, section) VALUES
('fa', 'key_zarinpal_settings', 'تنظیمات زرین‌پال', 'frontend'),
('en', 'key_zarinpal_settings', 'ZarinPal Settings', 'frontend'),
('fa', 'key_zarinpal_info', 'اطلاعات درگاه زرین‌پال', 'frontend'),
('en', 'key_zarinpal_info', 'ZarinPal Gateway Information', 'frontend'),
('fa', 'key_zarinpal_description', 'زرین‌پال یک درگاه پرداخت ایرانی برای دریافت پرداخت به ریال و تومان است', 'frontend'),
('en', 'key_zarinpal_description', 'ZarinPal is an Iranian payment gateway for receiving payments in Rial and Toman', 'frontend'),
('fa', 'key_enable_zarinpal', 'فعال‌سازی زرین‌پال', 'frontend'),
('en', 'key_enable_zarinpal', 'Enable ZarinPal', 'frontend'),
('fa', 'key_sandbox_mode', 'حالت آزمایشی (Sandbox)', 'frontend'),
('en', 'key_sandbox_mode', 'Sandbox Mode', 'frontend'),
('fa', 'key_sandbox_warning', 'توجه: در حالت آزمایشی، هیچ پرداخت واقعی انجام نمی‌شود', 'frontend'),
('en', 'key_sandbox_warning', 'Warning: In sandbox mode, no real payment will be processed', 'frontend'),
('fa', 'key_merchant_id', 'کد پذیرنده (Merchant ID)', 'frontend'),
('en', 'key_merchant_id', 'Merchant ID', 'frontend'),
('fa', 'key_merchant_id_placeholder', 'مانند: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx', 'frontend'),
('en', 'key_merchant_id_placeholder', 'e.g., xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx', 'frontend'),
('fa', 'key_merchant_id_help', 'کد پذیرنده را از پنل زرین‌پال دریافت کنید', 'frontend'),
('en', 'key_merchant_id_help', 'Get your Merchant ID from ZarinPal panel', 'frontend'),
('fa', 'key_callback_url', 'آدرس بازگشت (Callback URL)', 'frontend'),
('en', 'key_callback_url', 'Callback URL', 'frontend'),
('fa', 'key_supported_currencies', 'ارزهای پشتیبانی شده', 'frontend'),
('en', 'key_supported_currencies', 'Supported Currencies', 'frontend'),
('fa', 'key_rial', 'ریال', 'frontend'),
('en', 'key_rial', 'Rial', 'frontend'),
('fa', 'key_toman', 'تومان', 'frontend'),
('en', 'key_toman', 'Toman', 'frontend');

-- E-namad Settings
INSERT INTO translations (lang_code, translation_key, translation_value, section) VALUES
('fa', 'key_enamad_settings', 'تنظیمات اینماد', 'frontend'),
('en', 'key_enamad_settings', 'E-namad Settings', 'frontend'),
('fa', 'key_enable_enamad', 'نمایش اینماد', 'frontend'),
('en', 'key_enable_enamad', 'Show E-namad', 'frontend'),
('fa', 'key_enamad_code', 'کد اینماد', 'frontend'),
('en', 'key_enamad_code', 'E-namad Code', 'frontend'),
('fa', 'key_enamad_code_placeholder', 'کد HTML اینماد را از سایت enamad.ir کپی کنید', 'frontend'),
('en', 'key_enamad_code_placeholder', 'Copy the HTML code from enamad.ir', 'frontend'),
('fa', 'key_enamad_code_help', 'برای دریافت کد اینماد به سایت enamad.ir مراجعه کنید', 'frontend'),
('en', 'key_enamad_code_help', 'Visit enamad.ir to get your E-namad code', 'frontend'),
('fa', 'key_preview', 'پیش‌نمایش', 'frontend'),
('en', 'key_preview', 'Preview', 'frontend');

-- Landing Page Settings
INSERT INTO translations (lang_code, translation_key, translation_value, section) VALUES
('fa', 'key_landing_settings', 'تنظیمات صفحه اصلی', 'frontend'),
('en', 'key_landing_settings', 'Landing Page Settings', 'frontend'),
('fa', 'key_hero_section', 'بخش اصلی (Hero)', 'frontend'),
('en', 'key_hero_section', 'Hero Section', 'frontend'),
('fa', 'key_hero_title', 'عنوان اصلی', 'frontend'),
('en', 'key_hero_title', 'Hero Title', 'frontend'),
('fa', 'key_hero_subtitle', 'توضیحات', 'frontend'),
('en', 'key_hero_subtitle', 'Subtitle', 'frontend'),
('fa', 'key_cta_button', 'متن دکمه', 'frontend'),
('en', 'key_cta_button', 'CTA Button Text', 'frontend');

-- Templates Gallery
INSERT INTO translations (lang_code, translation_key, translation_value, section) VALUES
('fa', 'key_browse_templates', 'مرور قالب‌ها', 'frontend'),
('en', 'key_browse_templates', 'Browse Templates', 'frontend'),
('fa', 'key_choose_template_subtitle', 'قالب مناسب برای ساخت اپلیکیشن خود را انتخاب کنید', 'frontend'),
('en', 'key_choose_template_subtitle', 'Choose the perfect template for your application', 'frontend'),
('fa', 'key_search', 'جستجو', 'frontend'),
('en', 'key_search', 'Search', 'frontend'),
('fa', 'key_category', 'دسته‌بندی', 'frontend'),
('en', 'key_category', 'Category', 'frontend'),
('fa', 'key_view', 'نمایش', 'frontend'),
('en', 'key_view', 'View', 'frontend'),
('fa', 'key_grid_view', 'نمای شبکه‌ای', 'frontend'),
('en', 'key_grid_view', 'Grid View', 'frontend'),
('fa', 'key_list_view', 'نمای لیستی', 'frontend'),
('en', 'key_list_view', 'List View', 'frontend'),
('fa', 'key_primary', 'اصلی', 'frontend'),
('en', 'key_primary', 'Primary', 'frontend'),
('fa', 'key_select_template', 'انتخاب این قالب', 'frontend'),
('en', 'key_select_template', 'Select This Template', 'frontend'),
('fa', 'key_no_templates_found', 'قالبی یافت نشد', 'frontend'),
('en', 'key_no_templates_found', 'No templates found', 'frontend');

-- Footer
INSERT INTO translations (lang_code, translation_key, translation_value, section) VALUES
('fa', 'key_footer_description', 'ساخت اپلیکیشن موبایل در چند دقیقه', 'frontend'),
('en', 'key_footer_description', 'Build your mobile app in minutes', 'frontend'),
('fa', 'key_quick_links', 'لینک‌های سریع', 'frontend'),
('en', 'key_quick_links', 'Quick Links', 'frontend'),
('fa', 'key_templates', 'قالب‌ها', 'frontend'),
('en', 'key_templates', 'Templates', 'frontend'),
('fa', 'key_my_apps', 'اپ‌های من', 'frontend'),
('en', 'key_my_apps', 'My Apps', 'frontend'),
('fa', 'key_profile', 'پروفایل', 'frontend'),
('en', 'key_profile', 'Profile', 'frontend'),
('fa', 'key_contact_us', 'تماس با ما', 'frontend'),
('en', 'key_contact_us', 'Contact Us', 'frontend'),
('fa', 'key_all_rights_reserved', 'تمامی حقوق محفوظ است', 'frontend'),
('en', 'key_all_rights_reserved', 'All rights reserved', 'frontend');

-- Common
INSERT INTO translations (lang_code, translation_key, translation_value, section) VALUES
('fa', 'key_settings_saved', 'تنظیمات با موفقیت ذخیره شد', 'frontend'),
('en', 'key_settings_saved', 'Settings saved successfully', 'frontend');
