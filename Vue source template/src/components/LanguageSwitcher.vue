<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        text
        v-bind="attrs"
        v-on="on"
        data-testid="language-switcher"
      >
        <v-icon left>mdi-web</v-icon>
        {{ currentLanguageName }}
        <v-icon right>mdi-chevron-down</v-icon>
      </v-btn>
    </template>

    <v-list>
      <v-list-item
        v-for="lang in languages"
        :key="lang.code"
        @click="switchLanguage(lang.code)"
        :data-testid="`language-option-${lang.code}`"
      >
        <v-list-item-icon>
          <v-icon v-if="lang.code === currentLanguage" color="primary">
            mdi-check
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ lang.name }}</v-list-item-title>
          <v-list-item-subtitle>
            <v-chip x-small :color="lang.direction === 'rtl' ? 'orange' : 'blue'">
              {{ lang.direction.toUpperCase() }}
            </v-chip>
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LanguageSwitcher',
  
  data() {
    return {
      languages: [],
      currentLanguage: 'en',
      loading: false
    };
  },

  computed: {
    currentLanguageName() {
      const lang = this.languages.find(l => l.code === this.currentLanguage);
      return lang ? lang.name : this.currentLanguage.toUpperCase();
    }
  },

  mounted() {
    // Load current language from localStorage
    const savedLang = localStorage.getItem('app_language') || 'en';
    this.currentLanguage = savedLang;
    
    this.loadLanguages();
  },

  methods: {
    async loadLanguages() {
      try {
        const response = await axios.get('/public/data/languages');
        
        if (response.data.status === 200) {
          this.languages = response.data.data.languages;
        }
      } catch (err) {
        console.error('Failed to load languages:', err);
      }
    },

    switchLanguage(langCode) {
      if (langCode === this.currentLanguage) return;

      this.currentLanguage = langCode;
      localStorage.setItem('app_language', langCode);

      // Find language details
      const lang = this.languages.find(l => l.code === langCode);
      
      if (lang) {
        // Set direction
        document.documentElement.setAttribute('dir', lang.direction);
        document.documentElement.setAttribute('lang', langCode);
        
        // Update i18n locale if available
        if (this.$i18n) {
          this.$i18n.locale = langCode;
        }
        
        // Emit event for parent components
        this.$emit('language-changed', { code: langCode, direction: lang.direction });
        
        // Reload page to apply changes
        window.location.reload();
      }
    }
  }
};
</script>
