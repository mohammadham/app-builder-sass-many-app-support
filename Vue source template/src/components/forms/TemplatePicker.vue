<template>
  <div>
    <v-progress-circular v-if="loading" indeterminate color="primary" class="mx-auto d-block" />
    
    <v-row v-else>
      <v-col
        v-for="(item, index) in templates"
        :key="item.id || index"
        cols="3"
        md="3"
        sm="3"
      >
        <div class="d-flex justify-center align-center flex-column">
          <v-img
            :src="item.thumbnail || item.image"
            alt=""
            class="border rounded-lg w-100 mb-3 cursor-pointer"
            :style="item.id === selectedTemplateId && `border-color: rgb(var(--v-theme-primary))!important; border-width: 3px;`"
            @click="selectTemplate(item)"
            cover
            height="120"
          >
            <template v-slot:placeholder>
              <div class="d-flex align-center justify-center fill-height">
                <v-progress-circular indeterminate color="grey-lighten-5" />
              </div>
            </template>
          </v-img>
          <div class="text-caption font-weight-medium mb-3 text-center">
            {{ getCurrentLocaleName(item) }}
          </div>
          <v-chip
            v-if="item.is_primary"
            size="x-small"
            color="primary"
            variant="tonal"
            class="mb-2"
          >
            {{ currentLocale === 'fa' ? 'پیش‌فرض' : 'Default' }}
          </v-chip>
        </div>
      </v-col>
    </v-row>

    <v-alert
      v-if="!loading && templates.length === 0"
      type="info"
      variant="tonal"
      class="mt-4"
    >
      {{ currentLocale === 'fa' ? 'هیچ قالبی موجود نیست' : 'No templates available' }}
    </v-alert>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'TemplatePicker',
  props: {
    template: {
      type: Number,
      default: 0
    },
    templateId: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      loading: true,
      templates: [],
      selectedTemplateId: null,
      selectedTemplateIndex: 0,
      currentLocale: 'en'
    }
  },
  watch: {
    templateId: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.selectedTemplateId = newVal;
        }
      }
    }
  },
  methods: {
    async loadTemplates() {
      try {
        const response = await axios.get('/public/data/templates');
        this.templates = response.data.templates || [];
        
        // Select primary template by default if none selected
        if (!this.selectedTemplateId && this.templates.length > 0) {
          const primaryTemplate = this.templates.find(t => t.is_primary === 1);
          if (primaryTemplate) {
            this.selectTemplate(primaryTemplate);
          } else {
            this.selectTemplate(this.templates[0]);
          }
        }
        
        this.loading = false;
      } catch (error) {
        console.error('Failed to load templates:', error);
        this.loading = false;
      }
    },

    selectTemplate(template) {
      this.selectedTemplateId = template.id;
      // برای سازگاری با سیستم قدیمی، اگر is_primary است index 0 را ارسال کنیم
      const oldTemplateIndex = template.is_primary ? 0 : template.id;
      
      this.$emit('change', oldTemplateIndex);
      this.$emit('change-id', template.id);
      this.$emit('template-selected', {
        id: template.id,
        index: oldTemplateIndex,
        name: template.name_en,
        name_fa: template.name_fa
      });
    },

    getCurrentLocaleName(item) {
      // Check if it's old template format (has 'name' field)
      if (item.name) {
        return item.name;
      }
      // New template format
      return this.currentLocale === 'fa' && item.name_fa 
        ? item.name_fa 
        : item.name_en;
    }
  },
  mounted() {
    this.currentLocale = this.$store?.state?.locale || localStorage.getItem('locale') || 'en';
    this.loadTemplates();
  }
};
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}

.cursor-pointer:hover {
  transform: scale(1.05);
}
</style>