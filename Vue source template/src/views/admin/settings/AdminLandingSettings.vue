<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        <span>{{ $t('key_landing_settings') }}</span>
      </v-card-title>

      <v-card-text>
        <v-alert v-if="error" type="error" dismissible @click:close="error = null">
          {{ error }}
        </v-alert>

        <v-alert v-if="success" type="success" dismissible @click:close="success = null">
          {{ success }}
        </v-alert>

        <v-form v-if="!loading">
          <v-row>
            <!-- Hero Title -->
            <v-col cols="12">
              <h3 class="mb-4">{{ $t('key_hero_section') }}</h3>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="settings.hero_title_en"
                :label="$t('key_hero_title') + ' (English)'"
                outlined
                data-testid="hero-title-en"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="settings.hero_title_fa"
                :label="$t('key_hero_title') + ' (فارسی)'"
                outlined
                data-testid="hero-title-fa"
                dir="rtl"
              ></v-text-field>
            </v-col>

            <!-- Hero Subtitle -->
            <v-col cols="12" md="6">
              <v-textarea
                v-model="settings.hero_subtitle_en"
                :label="$t('key_hero_subtitle') + ' (English)'"
                outlined
                rows="3"
                data-testid="hero-subtitle-en"
              ></v-textarea>
            </v-col>

            <v-col cols="12" md="6">
              <v-textarea
                v-model="settings.hero_subtitle_fa"
                :label="$t('key_hero_subtitle') + ' (فارسی)'"
                outlined
                rows="3"
                data-testid="hero-subtitle-fa"
                dir="rtl"
              ></v-textarea>
            </v-col>

            <!-- CTA Button -->
            <v-col cols="12" md="6">
              <v-text-field
                v-model="settings.hero_cta_en"
                :label="$t('key_cta_button') + ' (English)'"
                outlined
                data-testid="hero-cta-en"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="settings.hero_cta_fa"
                :label="$t('key_cta_button') + ' (فارسی)'"
                outlined
                data-testid="hero-cta-fa"
                dir="rtl"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-form>

        <div v-else class="text-center py-5">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          :loading="saving"
          @click="saveSettings"
          data-testid="save-landing-settings"
        >
          {{ $t('key_save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminLandingSettings',
  
  data() {
    return {
      loading: true,
      saving: false,
      error: null,
      success: null,
      settings: {
        hero_title_en: '',
        hero_title_fa: '',
        hero_subtitle_en: '',
        hero_subtitle_fa: '',
        hero_cta_en: '',
        hero_cta_fa: ''
      }
    };
  },

  mounted() {
    this.loadSettings();
  },

  methods: {
    async loadSettings() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/private/admin/settings/landing');
        
        if (response.data.status === 200) {
          this.settings = response.data.data.settings;
        } else {
          this.error = 'Failed to load settings';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to load settings';
      } finally {
        this.loading = false;
      }
    },

    async saveSettings() {
      this.saving = true;
      this.error = null;
      this.success = null;

      try {
        const response = await axios.post('/private/admin/settings/landing', this.settings);
        
        if (response.data.status === 200) {
          this.success = this.$t('key_settings_saved');
          setTimeout(() => this.success = null, 3000);
        } else {
          this.error = 'Failed to save settings';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to save settings';
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>
