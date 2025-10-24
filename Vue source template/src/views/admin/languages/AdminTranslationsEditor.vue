<template>
  <v-container fluid>
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <div>
          <span>{{ $t('key_translations_editor') }}</span>
          <v-chip class="ml-2" :color="currentLanguage?.direction === 'rtl' ? 'orange' : 'blue'" small>
            {{ currentLanguage?.name || langCode.toUpperCase() }}
          </v-chip>
        </div>
        <div>
          <v-btn color="success" @click="saveAllChanges" :loading="saving" class="mr-2">
            <v-icon left>mdi-content-save</v-icon>
            {{ $t('key_save_all') }}
          </v-btn>
          <v-btn color="primary" @click="refreshData">
            <v-icon left>mdi-refresh</v-icon>
            {{ $t('key_refresh') }}
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text>
        <!-- Filters -->
        <v-row class="mb-4">
          <v-col cols="12" md="4">
            <v-select
              v-model="langCode"
              :items="languages"
              item-text="name"
              item-value="code"
              :label="$t('key_select_language')"
              @change="loadTranslations"
              data-testid="language-select"
            >
              <template v-slot:item="{ item }">
                <v-chip small class="mr-2">{{ item.code.toUpperCase() }}</v-chip>
                {{ item.name }}
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" md="4">
            <v-select
              v-model="section"
              :items="sections"
              :label="$t('key_section')"
              @change="loadTranslations"
              data-testid="section-select"
            ></v-select>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="search"
              :label="$t('key_search')"
              append-icon="mdi-magnify"
              @input="debouncedSearch"
              clearable
              data-testid="search-input"
            ></v-text-field>
          </v-col>
        </v-row>

        <v-alert v-if="error" type="error" dismissible @click:close="error = null">
          {{ error }}
        </v-alert>

        <v-alert v-if="success" type="success" dismissible @click:close="success = null">
          {{ success }}
        </v-alert>

        <!-- Translations Table -->
        <v-data-table
          :headers="headers"
          :items="filteredTranslations"
          :loading="loading"
          :search="search"
          class="elevation-1"
          :items-per-page="50"
          data-testid="translations-table"
        >
          <!-- Translation Key -->
          <template v-slot:item.translation_key="{ item }">
            <code class="text-caption">{{ item.translation_key }}</code>
          </template>

          <!-- Translation Value -->
          <template v-slot:item.translation_value="{ item }">
            <v-textarea
              v-model="item.translation_value"
              :rows="2"
              auto-grow
              dense
              outlined
              hide-details
              @change="markAsModified(item)"
              :data-testid="`translation-input-${item.translation_key}`"
            ></v-textarea>
          </template>

          <!-- Modified Indicator -->
          <template v-slot:item.modified="{ item }">
            <v-icon v-if="item.modified" color="warning">mdi-pencil</v-icon>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <v-btn
              v-if="item.modified"
              color="success"
              icon
              small
              @click="saveTranslation(item)"
              :loading="item.saving"
              :data-testid="`save-translation-${item.translation_key}`"
            >
              <v-icon>mdi-content-save</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';
import _ from 'lodash';

export default {
  name: 'AdminTranslationsEditor',
  
  data() {
    return {
      languages: [],
      langCode: 'en',
      section: 'frontend',
      sections: [
        { text: 'Frontend', value: 'frontend' },
        { text: 'Backend', value: 'backend' },
        { text: 'Email', value: 'email' }
      ],
      translations: [],
      search: '',
      loading: false,
      saving: false,
      error: null,
      success: null,
      headers: [
        { text: this.$t('key_key'), value: 'translation_key', sortable: true, width: '30%' },
        { text: this.$t('key_value'), value: 'translation_value', sortable: false, width: '50%' },
        { text: this.$t('key_modified'), value: 'modified', sortable: false, width: '10%' },
        { text: this.$t('key_actions'), value: 'actions', sortable: false, width: '10%' }
      ]
    };
  },

  computed: {
    currentLanguage() {
      return this.languages.find(l => l.code === this.langCode);
    },

    filteredTranslations() {
      if (!this.search) return this.translations;
      
      const searchLower = this.search.toLowerCase();
      return this.translations.filter(t => 
        t.translation_key.toLowerCase().includes(searchLower) ||
        t.translation_value.toLowerCase().includes(searchLower)
      );
    },

    modifiedTranslations() {
      return this.translations.filter(t => t.modified);
    }
  },

  mounted() {
    this.loadLanguages();
    
    // Get lang from query
    const queryLang = this.$route.query.lang;
    if (queryLang) {
      this.langCode = queryLang;
    }
  },

  methods: {
    debouncedSearch: _.debounce(function() {
      // Search is handled by computed property
    }, 300),

    async loadLanguages() {
      try {
        const response = await axios.get('/private/admin/languages/list');
        
        if (response.data.status === 200) {
          this.languages = response.data.data.languages;
          this.loadTranslations();
        }
      } catch (err) {
        this.error = 'Failed to load languages';
      }
    },

    async loadTranslations() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/private/admin/translations/list', {
          params: {
            lang_code: this.langCode,
            section: this.section
          }
        });
        
        if (response.data.status === 200) {
          this.translations = response.data.data.translations.map(t => ({
            ...t,
            modified: false,
            saving: false,
            originalValue: t.translation_value
          }));
        } else {
          this.error = 'Failed to load translations';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to load translations';
      } finally {
        this.loading = false;
      }
    },

    markAsModified(item) {
      item.modified = item.translation_value !== item.originalValue;
    },

    async saveTranslation(item) {
      item.saving = true;

      try {
        const response = await axios.post('/private/admin/translations/update', {
          lang_code: this.langCode,
          translation_key: item.translation_key,
          translation_value: item.translation_value,
          section: this.section
        });

        if (response.data.status === 200) {
          item.modified = false;
          item.originalValue = item.translation_value;
          this.success = this.$t('key_translation_saved');
          setTimeout(() => this.success = null, 2000);
        } else {
          this.error = 'Failed to save translation';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to save translation';
      } finally {
        item.saving = false;
      }
    },

    async saveAllChanges() {
      if (this.modifiedTranslations.length === 0) {
        this.error = this.$t('key_no_changes');
        return;
      }

      this.saving = true;

      try {
        const translationsData = this.modifiedTranslations.map(t => ({
          lang_code: this.langCode,
          translation_key: t.translation_key,
          translation_value: t.translation_value,
          section: this.section
        }));

        const response = await axios.post('/private/admin/translations/bulk_update', {
          translations: translationsData
        });

        if (response.data.status === 200) {
          // Mark all as saved
          this.modifiedTranslations.forEach(t => {
            t.modified = false;
            t.originalValue = t.translation_value;
          });
          
          this.success = `${response.data.data.success_count} ${this.$t('key_translations_saved')}`;
          setTimeout(() => this.success = null, 3000);
        } else {
          this.error = 'Failed to save translations';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to save translations';
      } finally {
        this.saving = false;
      }
    },

    refreshData() {
      this.loadTranslations();
    }
  }
};
</script>

<style scoped>
code {
  background-color: #f5f5f5;
  padding: 2px 6px;
  border-radius: 3px;
}
</style>
