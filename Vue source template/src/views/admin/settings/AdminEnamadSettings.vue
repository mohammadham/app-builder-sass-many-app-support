<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        <span>{{ $t('key_enamad_settings') }}</span>
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
            <v-col cols="12">
              <v-switch
                v-model="settings.enamad_enabled"
                :label="$t('key_enable_enamad')"
                color="success"
                true-value="1"
                false-value="0"
                data-testid="enamad-enabled-switch"
              ></v-switch>
            </v-col>

            <v-col cols="12">
              <v-textarea
                v-model="settings.enamad_code"
                :label="$t('key_enamad_code')"
                :placeholder="$t('key_enamad_code_placeholder')"
                outlined
                rows="5"
                :disabled="settings.enamad_enabled !== '1'"
                data-testid="enamad-code-textarea"
                dir="ltr"
              >
                <template v-slot:append>
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-icon v-bind="attrs" v-on="on">mdi-information</v-icon>
                    </template>
                    <span>{{ $t('key_enamad_code_help') }}</span>
                  </v-tooltip>
                </template>
              </v-textarea>
            </v-col>

            <v-col cols="12" v-if="settings.enamad_enabled === '1' && settings.enamad_code">
              <v-divider class="my-4"></v-divider>
              <h3 class="mb-4">{{ $t('key_preview') }}</h3>
              <div v-html="settings.enamad_code" class="enamad-preview"></div>
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
          data-testid="save-enamad-settings"
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
  name: 'AdminEnamadSettings',
  
  data() {
    return {
      loading: true,
      saving: false,
      error: null,
      success: null,
      settings: {
        enamad_enabled: '0',
        enamad_code: ''
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
        const response = await axios.get('/private/admin/settings/enamad');
        
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
        const response = await axios.post('/private/admin/settings/enamad', this.settings);
        
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

<style scoped>
.enamad-preview {
  border: 1px solid #ddd;
  padding: 16px;
  border-radius: 4px;
  background-color: #f9f9f9;
}
</style>
