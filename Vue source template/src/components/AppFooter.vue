<template>
  <v-footer class="bg-grey-lighten-5 mt-12" data-testid="app-footer">
    <v-container>
      <v-row>
        <v-col cols="12" md="4" class="text-center text-md-left">
          <v-img
            :src="$store.state.config.logo"
            max-width="120"
            class="mb-4"
          ></v-img>
          <p class="text-body-2 text-grey-darken-1">
            {{ $t('key_footer_description') }}
          </p>
        </v-col>

        <v-col cols="12" md="4" class="text-center">
          <h3 class="text-h6 mb-4">{{ $t('key_quick_links') }}</h3>
          <div class="d-flex flex-column">
            <router-link to="/templates" class="text-decoration-none mb-2">
              {{ $t('key_templates') }}
            </router-link>
            <router-link to="/private/apps" class="text-decoration-none mb-2">
              {{ $t('key_my_apps') }}
            </router-link>
            <router-link to="/private/profile" class="text-decoration-none mb-2">
              {{ $t('key_profile') }}
            </router-link>
          </div>
        </v-col>

        <v-col cols="12" md="4" class="text-center text-md-right">
          <h3 class="text-h6 mb-4">{{ $t('key_contact_us') }}</h3>
          <div class="d-flex flex-column align-center align-md-end">
            <v-btn
              v-for="social in socials"
              :key="social.name"
              :icon="social.icon"
              variant="text"
              :href="social.url"
              target="_blank"
              class="mb-2"
            ></v-btn>
          </div>
          
          <!-- E-namad Badge -->
          <div v-if="enamadEnabled && enamadCode" class="mt-4 enamad-container" data-testid="enamad-badge">
            <div v-html="enamadCode"></div>
          </div>
        </v-col>
      </v-row>

      <v-divider class="my-4"></v-divider>

      <v-row>
        <v-col cols="12" class="text-center">
          <p class="text-body-2 text-grey">
            © {{ new Date().getFullYear() }} {{ $store.state.config.site_name }}. {{ $t('key_all_rights_reserved') }}
          </p>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AppFooter',

  data() {
    return {
      enamadEnabled: false,
      enamadCode: '',
      socials: [
        { name: 'Twitter', icon: 'mdi-twitter', url: '#' },
        { name: 'Facebook', icon: 'mdi-facebook', url: '#' },
        { name: 'Instagram', icon: 'mdi-instagram', url: '#' },
        { name: 'LinkedIn', icon: 'mdi-linkedin', url: '#' }
      ]
    };
  },

  mounted() {
    this.loadSiteSettings();
  },

  methods: {
    async loadSiteSettings() {
      try {
        const response = await axios.get('/public/data/site_settings');
        if (response.data) {
          this.enamadEnabled = response.data.enamad_enabled === '1';
          this.enamadCode = response.data.enamad_code || '';
        }
      } catch (error) {
        console.error('Failed to load site settings:', error);
      }
    }
  }
};
</script>

<style scoped>
.enamad-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.enamad-container >>> img {
  max-width: 100px;
}
</style>
