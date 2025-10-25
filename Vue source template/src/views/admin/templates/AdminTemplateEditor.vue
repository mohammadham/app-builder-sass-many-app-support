<template>
  <PageBar :title="lang === 'fa' ? 'ویرایش قالب' : 'Edit Template'">
    <template v-slot:actions>
      <v-btn variant="text" @click="$router.back()">
        {{ lang === 'fa' ? 'بازگشت' : 'Back' }}
      </v-btn>
    </template>
  </PageBar>
  
  <PageLoading v-if="loading"/>
  
  <v-container fluid v-else>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-tabs v-model="tab" bg-color="primary">
            <v-tab value="general">{{ lang === 'fa' ? 'اطلاعات کلی' : 'General Info' }}</v-tab>
            <v-tab value="form">{{ lang === 'fa' ? 'فرم تنظیمات' : 'Settings Form' }}</v-tab>
            <v-tab value="thumbnail">{{ lang === 'fa' ? 'تصویر' : 'Thumbnail' }}</v-tab>
          </v-tabs>

          <v-card-text>
            <v-window v-model="tab">
              <!-- General Info Tab -->
              <v-window-item value="general">
                <v-form ref="form">
                  <v-text-field
                    v-model="template.name_fa"
                    :label="lang === 'fa' ? 'نام فارسی' : 'Name (Persian)'"
                    required
                  ></v-text-field>
                  
                  <v-text-field
                    v-model="template.name_en"
                    :label="lang === 'fa' ? 'نام انگلیسی' : 'Name (English)'"
                    required
                  ></v-text-field>

                  <v-textarea
                    v-model="template.description_fa"
                    :label="lang === 'fa' ? 'توضیحات فارسی' : 'Description (Persian)'"
                    rows="3"
                  ></v-textarea>

                  <v-textarea
                    v-model="template.description_en"
                    :label="lang === 'fa' ? 'توضیحات انگلیسی' : 'Description (English)'"
                    rows="3"
                  ></v-textarea>

                  <v-text-field
                    v-model="template.category"
                    :label="lang === 'fa' ? 'دسته‌بندی' : 'Category'"
                  ></v-text-field>

                  <v-combobox
                    v-model="template.tags"
                    :label="lang === 'fa' ? 'تگ‌ها' : 'Tags'"
                    multiple
                    chips
                    clearable
                  ></v-combobox>

                  <v-text-field
                    v-model="template.github_repo"
                    :label="lang === 'fa' ? 'مخزن GitHub' : 'GitHub Repository'"
                  ></v-text-field>

                  <v-text-field
                    v-model="template.github_branch"
                    :label="lang === 'fa' ? 'برنچ GitHub' : 'GitHub Branch'"
                  ></v-text-field>

                  <v-switch
                    v-model="template.status"
                    :label="lang === 'fa' ? 'فعال' : 'Active'"
                    color="primary"
                  ></v-switch>

                  <v-btn color="primary" @click="saveGeneral" :loading="saving">
                    {{ lang === 'fa' ? 'ذخیره تغییرات' : 'Save Changes' }}
                  </v-btn>
                </v-form>
              </v-window-item>

              <!-- Form Builder Tab -->
              <v-window-item value="form">
                <DynamicFormBuilder 
                  v-model="template.config_schema"
                  @save="saveFormSchema"
                />
              </v-window-item>

              <!-- Thumbnail Tab -->
              <v-window-item value="thumbnail">
                <div class="text-center">
                  <v-img
                    v-if="template.thumbnail"
                    :src="template.thumbnail"
                    max-width="500"
                    class="mx-auto mb-4"
                  ></v-img>
                  <v-file-input
                    v-model="thumbnailFile"
                    :label="lang === 'fa' ? 'انتخاب تصویر' : 'Select Image'"
                    accept="image/*"
                    prepend-icon="mdi-camera"
                    @change="uploadThumbnail"
                  ></v-file-input>
                </div>
              </v-window-item>
            </v-window>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import PageBar from "@/components/blocks/PageBar.vue";
import PageLoading from "@/components/blocks/PageLoading.vue";
import DynamicFormBuilder from "@/components/admin/DynamicFormBuilder.vue";
import axios from "axios";

export default {
  name: "AdminTemplateEditor",
  components: { PageBar, PageLoading, DynamicFormBuilder },
  data() {
    return {
      loading: true,
      saving: false,
      tab: 'general',
      template: {
        id: null,
        name_fa: '',
        name_en: '',
        description_fa: '',
        description_en: '',
        category: '',
        tags: [],
        github_repo: '',
        github_branch: 'main',
        status: true,
        config_schema: { fields: [] },
        thumbnail: null
      },
      thumbnailFile: null,
      lang: localStorage.getItem('app_language') || 'en'
    };
  },
  mounted() {
    this.loadTemplate();
  },
  methods: {
    async loadTemplate() {
      this.loading = true;
      try {
        const response = await axios.get('/admin/templates/detail', {
          params: { id: this.$route.params.id }
        });
        this.template = response.data.template;
        this.template.status = !!this.template.status;
      } catch (error) {
        console.error('Error loading template:', error);
        this.$router.back();
      } finally {
        this.loading = false;
      }
    },
    async saveGeneral() {
      this.saving = true;
      try {
        await axios.post('/admin/templates/update', {
          id: this.template.id,
          name_fa: this.template.name_fa,
          name_en: this.template.name_en,
          description_fa: this.template.description_fa,
          description_en: this.template.description_en,
          category: this.template.category,
          tags: JSON.stringify(this.template.tags),
          github_repo: this.template.github_repo,
          github_branch: this.template.github_branch,
          status: this.template.status ? 1 : 0
        });
        alert(this.lang === 'fa' ? 'تغییرات ذخیره شد' : 'Changes saved successfully');
      } catch (error) {
        console.error('Error saving template:', error);
        alert(this.lang === 'fa' ? 'خطا در ذخیره تغییرات' : 'Error saving changes');
      } finally {
        this.saving = false;
      }
    },
    async saveFormSchema(schema) {
      try {
        await axios.post('/admin/templates/update_schema', {
          id: this.template.id,
          schema: JSON.stringify(schema)
        });
        alert(this.lang === 'fa' ? 'فرم ذخیره شد' : 'Form saved successfully');
      } catch (error) {
        console.error('Error saving form schema:', error);
        alert(this.lang === 'fa' ? 'خطا در ذخیره فرم' : 'Error saving form');
      }
    },
    async uploadThumbnail() {
      if (!this.thumbnailFile) return;

      const formData = new FormData();
      formData.append('id', this.template.id);
      formData.append('thumbnail', this.thumbnailFile);

      try {
        const response = await axios.post('/admin/templates/upload_thumbnail', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.template.thumbnail = response.data.url;
        alert(this.lang === 'fa' ? 'تصویر آپلود شد' : 'Thumbnail uploaded successfully');
      } catch (error) {
        console.error('Error uploading thumbnail:', error);
        alert(this.lang === 'fa' ? 'خطا در آپلود تصویر' : 'Error uploading thumbnail');
      }
    }
  }
};
</script>
