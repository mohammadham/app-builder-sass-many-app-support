<template>
  <PageBar :title="currentLocale === 'fa' ? 'تنظیمات قالب' : 'Template Configuration'">
    <template v-if="!loading && templateName" v-slot:subtitle>
      <v-chip size="small" color="primary" variant="tonal">
        {{ templateName }}
      </v-chip>
    </template>
  </PageBar>
  
  <ScreenLock
    class="loader-page-container"
    v-if="loading"
    :is-secondary="true"
  />
  
  <v-container fluid v-else>
    <!-- Warning if locked fields exist -->
    <v-alert
      v-if="isLocked && lockedFields.length > 0"
      type="info"
      variant="tonal"
      class="mb-4"
      data-testid="locked-fields-alert"
    >
      <template v-slot:title>
        {{ currentLocale === 'fa' ? 'فیلدهای قفل‌شده' : 'Locked Fields' }}
      </template>
      {{ currentLocale === 'fa' 
        ? 'برخی از فیلدها بعد از اولین ذخیره قفل شده‌اند و قابل ویرایش نیستند.' 
        : 'Some fields are locked after first save and cannot be modified.' 
      }}
    </v-alert>

    <!-- Dynamic Form -->
    <DynamicForm
      v-if="schema && schema.length > 0"
      :schema="schema"
      :initial-data="configData"
      :locked-fields="lockedFields"
      :is-locked="isLocked"
      :loading="isSaving"
      @submit="handleSaveConfig"
      data-testid="template-config-form"
    />

    <!-- No schema available -->
    <v-alert
      v-else
      type="warning"
      variant="tonal"
      data-testid="no-schema-alert"
    >
      {{ currentLocale === 'fa' 
        ? 'این قالب هنوز دارای فرم تنظیمات نیست.' 
        : 'This template does not have a configuration form yet.' 
      }}
    </v-alert>
  </v-container>
</template>

<script>
import PageBar from "@/components/blocks/PageBar.vue";
import ScreenLock from "@/components/blocks/ScreenLock.vue";
import DynamicForm from "@/components/DynamicForm.vue";
import axios from "axios";

export default {
  name: 'TemplateConfig',
  components: { DynamicForm, ScreenLock, PageBar },
  data() {
    return {
      loading: true,
      isSaving: false,
      schema: [],
      configData: {},
      lockedFields: [],
      isLocked: false,
      templateName: '',
      currentLocale: 'en'
    }
  },
  watch: {
    '$route.params.uid': function() {
      this.loading = true;
      this.loadConfig();
    },
  },
  methods: {
    async loadConfig() {
      try {
        const response = await axios.get('/private/projects/config', {
          params: {
            uid: this.$route.params.uid
          }
        });

        const data = response.data;
        this.schema = data.schema || [];
        this.configData = data.config_data || {};
        this.lockedFields = data.locked_fields || [];
        this.isLocked = data.is_locked || false;
        
        // Set template name based on locale
        this.templateName = this.currentLocale === 'fa' && data.template_name_fa 
          ? data.template_name_fa 
          : data.template_name;

        this.loading = false;
      } catch (error) {
        this.$store.commit('openSnackbar', error.response?.data?.message || 'Failed to load configuration');
        this.loading = false;
      }
    },

    async handleSaveConfig(formData) {
      this.isSaving = true;
      
      try {
        const response = await axios.post('/private/projects/config/save', {
          uid: this.$route.params.uid,
          config_data: formData
        });

        // Update locked fields if returned
        if (response.data.locked_fields) {
          this.lockedFields = response.data.locked_fields;
          this.isLocked = true;
        }

        this.$store.commit('openSnackbar', {
          message: response.data.message || (this.currentLocale === 'fa' ? 'تنظیمات با موفقیت ذخیره شد' : 'Settings saved successfully'),
          type: 'success'
        });

        // Reload config to get latest state
        await this.loadConfig();

      } catch (error) {
        this.$store.commit('openSnackbar', {
          message: error.response?.data?.message || (this.currentLocale === 'fa' ? 'خطا در ذخیره تنظیمات' : 'Failed to save settings'),
          type: 'error'
        });
      } finally {
        this.isSaving = false;
      }
    }
  },
  mounted() {
    this.currentLocale = this.$store?.state?.locale || localStorage.getItem('locale') || 'en';
    this.loadConfig();
  }
};
</script>

<style scoped>
.loader-page-container {
  min-height: 400px;
}
</style>
