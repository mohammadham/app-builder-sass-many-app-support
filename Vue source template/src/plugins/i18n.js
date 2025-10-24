import { createI18n } from 'vue-i18n';
import axios from 'axios';

/**
 * Load translations from API
 */
async function loadTranslations(locale, section = 'frontend') {
  try {
    const response = await axios.get('/public/data/translations', {
      params: { lang: locale, section: section }
    });

    if (response.data.status === 200) {
      return {
        messages: response.data.data.translations,
        direction: response.data.data.direction
      };
    }
  } catch (err) {
    console.error('Failed to load translations:', err);
  }
  
  return { messages: {}, direction: 'ltr' };
}

/**
 * Get initial locale from localStorage or browser
 */
function getInitialLocale() {
  // 1. Check localStorage
  const savedLang = localStorage.getItem('app_language');
  if (savedLang) return savedLang;

  // 2. Check browser language
  const browserLang = navigator.language.split('-')[0];
  return browserLang || 'en';
}

/**
 * English fallback messages
 */
const fallbackMessages = {
  en: {
    // Auth
    key_login: 'Login',
    key_register: 'Register',
    key_logout: 'Logout',
    key_email: 'Email',
    key_password: 'Password',
    key_forgot_password: 'Forgot Password?',
    
    // Common
    key_save: 'Save',
    key_save_all: 'Save All',
    key_cancel: 'Cancel',
    key_delete: 'Delete',
    key_edit: 'Edit',
    key_create: 'Create',
    key_refresh: 'Refresh',
    key_search: 'Search',
    key_actions: 'Actions',
    key_status: 'Status',
    key_name: 'Name',
    key_description: 'Description',
    key_loading: 'Loading...',
    key_no_data: 'No data available',
    
    // Templates
    key_templates: 'Templates',
    key_template_config: 'Template Configuration',
    key_select_template: 'Select Template',
    
    // Languages
    key_languages_management: 'Languages Management',
    key_translations_editor: 'Translations Editor',
    key_language: 'Language',
    key_select_language: 'Select Language',
    key_direction: 'Direction',
    key_default: 'Default',
    key_set_default: 'Set as Default',
    key_translations: 'Translations',
    key_language_updated: 'Language updated successfully',
    key_default_language_set: 'Default language set successfully',
    key_translation_saved: 'Translation saved successfully',
    key_translations_saved: 'translations saved',
    key_no_changes: 'No changes to save',
    key_section: 'Section',
    key_key: 'Key',
    key_value: 'Value',
    key_modified: 'Modified',
    
    // Projects
    key_projects: 'Projects',
    key_create_project: 'Create Project',
    key_project_name: 'Project Name',
    key_website_url: 'Website URL',
    
    // Messages
    key_success: 'Success',
    key_error: 'Error',
    key_warning: 'Warning',
    key_info: 'Info'
  }
};

// Create i18n instance
const i18n = createI18n({
  legacy: false, // Use Composition API mode
  locale: getInitialLocale(),
  fallbackLocale: 'en',
  messages: fallbackMessages,
  globalInjection: true,
  missingWarn: false,
  fallbackWarn: false
});

/**
 * Load and merge translations from API
 */
export async function setupI18n() {
  const locale = getInitialLocale();
  
  // Load translations from API
  const { messages, direction } = await loadTranslations(locale);
  
  // Merge with fallback
  const mergedMessages = {
    ...fallbackMessages[locale] || fallbackMessages.en,
    ...messages
  };
  
  // Set messages
  i18n.global.setLocaleMessage(locale, mergedMessages);
  i18n.global.locale.value = locale;
  
  // Set HTML attributes
  document.documentElement.setAttribute('lang', locale);
  document.documentElement.setAttribute('dir', direction);
  
  return i18n;
}

/**
 * Switch language dynamically
 */
export async function switchLanguage(locale) {
  const { messages, direction } = await loadTranslations(locale);
  
  // Merge with fallback
  const mergedMessages = {
    ...fallbackMessages[locale] || fallbackMessages.en,
    ...messages
  };
  
  i18n.global.setLocaleMessage(locale, mergedMessages);
  i18n.global.locale.value = locale;
  
  // Update HTML attributes
  document.documentElement.setAttribute('lang', locale);
  document.documentElement.setAttribute('dir', direction);
  
  // Save to localStorage
  localStorage.setItem('app_language', locale);
}

export default i18n;
