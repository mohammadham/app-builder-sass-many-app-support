<template>
  <v-container fluid>
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span>{{ $t('key_languages_management') }}</span>
        <v-btn color="primary" @click="refreshData">
          <v-icon left>mdi-refresh</v-icon>
          {{ $t('key_refresh') }}
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-alert v-if="error" type="error" dismissible @click:close="error = null">
          {{ error }}
        </v-alert>

        <v-alert v-if="success" type="success" dismissible @click:close="success = null">
          {{ success }}
        </v-alert>

        <v-data-table
          :headers="headers"
          :items="languages"
          :loading="loading"
          class="elevation-1"
          data-testid="languages-table"
        >
          <!-- Language Name -->
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-chip :color="item.is_default ? 'primary' : 'default'" small class="mr-2">
                {{ item.code.toUpperCase() }}
              </v-chip>
              <span>{{ item.name }}</span>
            </div>
          </template>

          <!-- Direction -->
          <template v-slot:item.direction="{ item }">
            <v-chip :color="item.direction === 'rtl' ? 'orange' : 'blue'" small>
              {{ item.direction.toUpperCase() }}
            </v-chip>
          </template>

          <!-- Status -->
          <template v-slot:item.status="{ item }">
            <v-switch
              v-model="item.status"
              :true-value="1"
              :false-value="0"
              color="success"
              :loading="item.updating"
              @change="updateLanguageStatus(item)"
              :data-testid="`language-status-${item.code}`"
            ></v-switch>
          </template>

          <!-- Default -->
          <template v-slot:item.is_default="{ item }">
            <v-btn
              :color="item.is_default ? 'success' : 'default'"
              :disabled="item.is_default === 1"
              small
              @click="setDefaultLanguage(item)"
              :data-testid="`language-default-${item.code}`"
            >
              <v-icon left small>{{ item.is_default ? 'mdi-star' : 'mdi-star-outline' }}</v-icon>
              {{ item.is_default ? $t('key_default') : $t('key_set_default') }}
            </v-btn>
          </template>

          <!-- Actions -->
          <template v-slot:item.actions="{ item }">
            <v-btn
              color="primary"
              small
              :to="`/admin/translations?lang=${item.code}`"
              :data-testid="`edit-translations-${item.code}`"
            >
              <v-icon left small>mdi-translate</v-icon>
              {{ $t('key_translations') }}
            </v-btn>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminLanguagesList',
  
  data() {
    return {
      languages: [],
      loading: false,
      error: null,
      success: null,
      headers: [
        { text: this.$t('key_language'), value: 'name', sortable: true },
        { text: this.$t('key_direction'), value: 'direction', sortable: true },
        { text: this.$t('key_status'), value: 'status', sortable: false },
        { text: this.$t('key_default'), value: 'is_default', sortable: false },
        { text: this.$t('key_actions'), value: 'actions', sortable: false }
      ]
    };
  },

  mounted() {
    this.loadLanguages();
  },

  methods: {
    async loadLanguages() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/private/admin/languages/list');
        
        if (response.data.status === 200) {
          this.languages = response.data.data.languages.map(lang => ({
            ...lang,
            updating: false
          }));
        } else {
          this.error = 'Failed to load languages';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to load languages';
      } finally {
        this.loading = false;
      }
    },

    async updateLanguageStatus(item) {
      item.updating = true;

      try {
        const response = await axios.post('/private/admin/languages/update', {
          id: item.id,
          status: item.status
        });

        if (response.data.status === 200) {
          this.success = this.$t('key_language_updated');
          setTimeout(() => this.success = null, 3000);
        } else {
          // Revert status
          item.status = item.status === 1 ? 0 : 1;
          this.error = 'Failed to update language';
        }
      } catch (err) {
        // Revert status
        item.status = item.status === 1 ? 0 : 1;
        this.error = err.response?.data?.messages?.error || 'Failed to update language';
      } finally {
        item.updating = false;
      }
    },

    async setDefaultLanguage(item) {
      try {
        const response = await axios.post('/private/admin/languages/update', {
          id: item.id,
          is_default: 1
        });

        if (response.data.status === 200) {
          this.success = this.$t('key_default_language_set');
          setTimeout(() => this.success = null, 3000);
          // Reload to reflect changes
          await this.loadLanguages();
        } else {
          this.error = 'Failed to set default language';
        }
      } catch (err) {
        this.error = err.response?.data?.messages?.error || 'Failed to set default language';
      }
    },

    refreshData() {
      this.loadLanguages();
    }
  }
};
</script>
