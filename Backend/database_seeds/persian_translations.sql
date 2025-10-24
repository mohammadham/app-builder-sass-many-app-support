-- Insert Persian translations into database
-- This SQL should be executed after migrations

INSERT INTO `translations` (`lang_code`, `translation_key`, `translation_value`, `section`) VALUES
-- Auth
('fa', 'key_login', 'ورود', 'frontend'),
('fa', 'key_register', 'ثبت‌نام', 'frontend'),
('fa', 'key_logout', 'خروج', 'frontend'),
('fa', 'key_email', 'ایمیل', 'frontend'),
('fa', 'key_password', 'رمز عبور', 'frontend'),
('fa', 'key_forgot_password', 'فراموشی رمز عبور؟', 'frontend'),

-- Common
('fa', 'key_save', 'ذخیره', 'frontend'),
('fa', 'key_save_all', 'ذخیره همه', 'frontend'),
('fa', 'key_cancel', 'انصراف', 'frontend'),
('fa', 'key_delete', 'حذف', 'frontend'),
('fa', 'key_edit', 'ویرایش', 'frontend'),
('fa', 'key_create', 'ایجاد', 'frontend'),
('fa', 'key_refresh', 'بروزرسانی', 'frontend'),
('fa', 'key_search', 'جستجو', 'frontend'),
('fa', 'key_actions', 'عملیات', 'frontend'),
('fa', 'key_status', 'وضعیت', 'frontend'),
('fa', 'key_name', 'نام', 'frontend'),
('fa', 'key_description', 'توضیحات', 'frontend'),
('fa', 'key_loading', 'در حال بارگذاری...', 'frontend'),
('fa', 'key_no_data', 'اطلاعاتی موجود نیست', 'frontend'),

-- Templates
('fa', 'key_templates', 'قالب‌ها', 'frontend'),
('fa', 'key_template_config', 'تنظیمات قالب', 'frontend'),
('fa', 'key_select_template', 'انتخاب قالب', 'frontend'),
('fa', 'key_template_name', 'نام قالب', 'frontend'),
('fa', 'key_template_description', 'توضیحات قالب', 'frontend'),
('fa', 'key_category', 'دسته‌بندی', 'frontend'),
('fa', 'key_tags', 'برچسب‌ها', 'frontend'),

-- Languages
('fa', 'key_languages_management', 'مدیریت زبان‌ها', 'frontend'),
('fa', 'key_translations_editor', 'ویرایشگر ترجمه‌ها', 'frontend'),
('fa', 'key_language', 'زبان', 'frontend'),
('fa', 'key_select_language', 'انتخاب زبان', 'frontend'),
('fa', 'key_direction', 'جهت', 'frontend'),
('fa', 'key_default', 'پیش‌فرض', 'frontend'),
('fa', 'key_set_default', 'تنظیم به عنوان پیش‌فرض', 'frontend'),
('fa', 'key_translations', 'ترجمه‌ها', 'frontend'),
('fa', 'key_language_updated', 'زبان با موفقیت بروزرسانی شد', 'frontend'),
('fa', 'key_default_language_set', 'زبان پیش‌فرض با موفقیت تنظیم شد', 'frontend'),
('fa', 'key_translation_saved', 'ترجمه با موفقیت ذخیره شد', 'frontend'),
('fa', 'key_translations_saved', 'ترجمه ذخیره شد', 'frontend'),
('fa', 'key_no_changes', 'تغییری برای ذخیره وجود ندارد', 'frontend'),
('fa', 'key_section', 'بخش', 'frontend'),
('fa', 'key_key', 'کلید', 'frontend'),
('fa', 'key_value', 'مقدار', 'frontend'),
('fa', 'key_modified', 'تغییر یافته', 'frontend'),

-- Projects
('fa', 'key_projects', 'پروژه‌ها', 'frontend'),
('fa', 'key_create_project', 'ایجاد پروژه', 'frontend'),
('fa', 'key_project_name', 'نام پروژه', 'frontend'),
('fa', 'key_website_url', 'آدرس وبسایت', 'frontend'),
('fa', 'key_app_name', 'نام برنامه', 'frontend'),
('fa', 'key_package_id', 'شناسه بسته', 'frontend'),

-- Config Fields
('fa', 'key_orientation', 'جهت صفحه', 'frontend'),
('fa', 'key_theme_color', 'رنگ تم', 'frontend'),
('fa', 'key_pull_to_refresh', 'کشیدن برای بروزرسانی', 'frontend'),
('fa', 'key_enable_gps', 'فعال‌سازی GPS', 'frontend'),
('fa', 'key_enable_camera', 'فعال‌سازی دوربین', 'frontend'),
('fa', 'key_enable_microphone', 'فعال‌سازی میکروفن', 'frontend'),

-- Messages
('fa', 'key_success', 'موفقیت', 'frontend'),
('fa', 'key_error', 'خطا', 'frontend'),
('fa', 'key_warning', 'هشدار', 'frontend'),
('fa', 'key_info', 'اطلاعات', 'frontend'),
('fa', 'key_confirm', 'تأیید', 'frontend'),

-- Dashboard
('fa', 'key_dashboard', 'داشبورد', 'frontend'),
('fa', 'key_home', 'خانه', 'frontend'),
('fa', 'key_settings', 'تنظیمات', 'frontend'),
('fa', 'key_profile', 'پروفایل', 'frontend'),

-- Validation
('fa', 'key_field_required', 'این فیلد الزامی است', 'frontend'),
('fa', 'key_invalid_email', 'ایمیل نامعتبر است', 'frontend'),
('fa', 'key_invalid_url', 'آدرس URL نامعتبر است', 'frontend'),
('fa', 'key_field_locked', 'این فیلد قفل شده و قابل تغییر نیست', 'frontend'),

-- Config
('fa', 'key_config_saved', 'تنظیمات با موفقیت ذخیره شد', 'frontend'),
('fa', 'key_config_locked', 'تنظیمات قفل شده است', 'frontend'),
('fa', 'key_config_locked_message', 'برخی فیلدها بعد از اولین ذخیره قابل تغییر نیستند', 'frontend');
