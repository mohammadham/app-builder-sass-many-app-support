<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        <span>{{ $t('key_zarinpal_settings') }}</span>
      </v-card-title>

      <v-card-text>
        <v-alert v-if="error" type="error" dismissible @click:close="error = null">
          {{ error }}
        </v-alert>

        <v-alert v-if="success" type="success" dismissible @click:close="success = null">
          {{ success }}
        </v-alert>

        <v-alert type="info" class="mb-4">
          <strong>{{ $t('key_zarinpal_info') }}</strong><br>
          {{ $t('key_zarinpal_description') }}
        </v-alert>

        <v-form v-if="!loading">
          <v-row>
            <v-col cols="12">
              <v-switch
                v-model="settings.zarinpal_enabled"
                :label="$t('key_enable_zarinpal')"
                color="success"
                true-value="1"
                false-value="0"
                data-testid="zarinpal-enabled-switch"
              ></v-switch>
            </v-col>

            <v-col cols="12">
              <v-switch
                v-model="settings.zarinpal_sandbox"
                :label="$t('key_sandbox_mode')"
                color="warning"
                true-value="1"
                false-value="0"
                :disabled="settings.zarinpal_enabled !== '1'"
                data-testid="zarinpal-sandbox-switch"
              ></v-switch>
              <v-alert v-if="settings.zarinpal_sandbox === '1'" type="warning" dense class="mt-2">
                {{ $t('key_sandbox_warning') }}
              </v-alert>
            </v-col>

            <v-col cols="12">
              <v-text-field
                v-model="settings.zarinpal_merchant_id"
                :label="$t('key_merchant_id')"
                :placeholder="$t('key_merchant_id_placeholder')"
                outlined
                :disabled="settings.zarinpal_enabled !== '1'"
                data-testid="zarinpal-merchant-id"
                dir="ltr"
              >
                <template v-slot:append>
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-icon v-bind="attrs" v-on="on">mdi-information</v-icon>
                    </template>
                    <span>{{ $t('key_merchant_id_help') }}</span>
                  </v-tooltip>
                </template>
              </v-text-field>
            </v-col>

            <v-col cols="12">
              <v-alert type="info" outlined>
                <strong>{{ $t('key_callback_url') }}:</strong><br>
                <code dir="ltr">{{ callbackUrl }}</code>
              </v-alert>
            </v-col>

            <v-col cols="12">
              <v-divider class="my-4"></v-divider>
              <h3 class="mb-4">{{ $t('key_supported_currencies') }}</h3>
              <v-chip color="success" class="mr-2">IRR ({{ $t('key_rial') }})</v-chip>
              <v-chip color="success">IRT ({{ $t('key_toman') }})</v-chip>
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
          data-testid="save-zarinpal-settings"
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
  name: 'AdminZarinPalSettings',
  
  data() {
    return {
      loading: true,
      saving: false,
      error: null,
      success: null,
      settings: {
        zarinpal_enabled: '0',
        zarinpal_sandbox: '0',
        zarinpal_merchant_id: ''
      }
    };
  },

  computed: {
    callbackUrl() {
      return window.location.origin + '/public/ipn/zarinpal';
    }
  },

  mounted() {
    this.loadSettings();
  },

  methods: {
    async loadSettings() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/private/admin/settings/zarinpal');
        
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
        const response = await axios.post('/private/admin/settings/zarinpal', this.settings);
        
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
code {
  background-color: #f5f5f5;
  padding: 8px 12px;
  border-radius: 4px;
  display: inline-block;
  font-family: monospace;
}
</style>
