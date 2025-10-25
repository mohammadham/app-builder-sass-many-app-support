<template>
  <PageBar :title="lang === 'fa' ? 'مدیریت قالب‌ها' : 'Templates Management'"/>
  <PageLoading v-if="loading"/>
  <template v-else>
    <Placeholder v-if="!list.length">
      <template v-slot:icon>
        <v-icon size="56">mdi-file-document-multiple-outline</v-icon>
      </template>
      <template v-slot:title>
        {{ lang === 'fa' ? 'قالبی وجود ندارد' : 'No Templates Found' }}
      </template>
      <template v-slot:subtitle>
        {{ lang === 'fa' ? 'اولین قالب خود را ایجاد کنید' : 'Create your first template' }}
      </template>
      <template v-slot:action>
        <v-btn variant="tonal" color="primary" @click="openCreate">
          {{ lang === 'fa' ? 'ایجاد قالب جدید' : 'Create New Template' }}
        </v-btn>
      </template>
    </Placeholder>
    <v-container fluid v-else>
      <v-row class="mt-3">
        <v-col md="12" sm="12" cols="12" class="py-0">
          <div class="w-100 d-flex justify-space-between align-center mb-3">
            <div class="font-weight-medium text-subtitle-2">
              {{ lang === 'fa' ? 'لیست قالب‌ها' : 'Templates List' }}
            </div>
            <v-btn variant="flat" color="primary" @click="openCreate">
              {{ lang === 'fa' ? 'ایجاد قالب جدید' : 'Create New Template' }}
            </v-btn>
          </div>
          
          <v-row>
            <v-col v-for="template in list" :key="template.id" cols="12" sm="6" md="4" lg="3">
              <v-card hover @click="editTemplate(template)">
                <v-img
                  :src="template.thumbnail || 'https://via.placeholder.com/300x200?text=No+Image'"
                  height="200"
                  cover
                >
                  <template v-slot:placeholder>
                    <v-row class="fill-height ma-0" align="center" justify="center">
                      <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </v-row>
                  </template>
                </v-img>
                <v-card-title class="text-h6">
                  {{ lang === 'fa' ? template.name_fa : template.name_en }}
                  <v-chip v-if="template.is_primary" size="x-small" color="success" class="ml-2">
                    {{ lang === 'fa' ? 'اصلی' : 'Primary' }}
                  </v-chip>
                </v-card-title>
                <v-card-subtitle>
                  {{ template.category || (lang === 'fa' ? 'بدون دسته‌بندی' : 'Uncategorized') }}
                </v-card-subtitle>
                <v-card-text>
                  <div class="text-caption" style="height: 60px; overflow: hidden;">
                    {{ lang === 'fa' ? template.description_fa : template.description_en }}
                  </div>
                  <v-chip-group class="mt-2">
                    <v-chip v-for="tag in template.tags.slice(0, 3)" :key="tag" size="x-small">
                      {{ tag }}
                    </v-chip>
                  </v-chip-group>
                </v-card-text>
                <v-card-actions>
                  <v-chip :color="template.status ? 'success' : 'error'" size="small">
                    {{ template.status ? (lang === 'fa' ? 'فعال' : 'Active') : (lang === 'fa' ? 'غیرفعال' : 'Inactive') }}
                  </v-chip>
                  <v-spacer></v-spacer>
                  <v-btn icon size="small" @click.stop="editTemplate(template)">
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn icon size="small" color="error" @click.stop="confirmDelete(template)" v-if="!template.is_primary">
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </template>

  <!-- Create/Edit Dialog -->
  <v-dialog v-model="dialog" max-width="800" persistent>
    <v-card>
      <v-card-title>
        {{ isEdit ? (lang === 'fa' ? 'ویرایش قالب' : 'Edit Template') : (lang === 'fa' ? 'ایجاد قالب جدید' : 'Create New Template') }}
      </v-card-title>
      <v-card-text>
        <v-form ref="form">
          <v-text-field
            v-model="form.name_fa"
            :label="lang === 'fa' ? 'نام فارسی' : 'Name (Persian)'"
            :rules="[v => !!v || (lang === 'fa' ? 'نام فارسی الزامی است' : 'Persian name is required')]"
            required
          ></v-text-field>
          
          <v-text-field
            v-model="form.name_en"
            :label="lang === 'fa' ? 'نام انگلیسی' : 'Name (English)'"
            :rules="[v => !!v || (lang === 'fa' ? 'نام انگلیسی الزامی است' : 'English name is required')]"
            required
          ></v-text-field>

          <v-textarea
            v-model="form.description_fa"
            :label="lang === 'fa' ? 'توضیحات فارسی' : 'Description (Persian)'"
            rows="3"
          ></v-textarea>

          <v-textarea
            v-model="form.description_en"
            :label="lang === 'fa' ? 'توضیحات انگلیسی' : 'Description (English)'"
            rows="3"
          ></v-textarea>

          <v-text-field
            v-model="form.category"
            :label="lang === 'fa' ? 'دسته‌بندی' : 'Category'"
          ></v-text-field>

          <v-combobox
            v-model="form.tags"
            :label="lang === 'fa' ? 'تگ‌ها' : 'Tags'"
            multiple
            chips
            clearable
          ></v-combobox>

          <v-text-field
            v-model="form.github_repo"
            :label="lang === 'fa' ? 'مخزن GitHub' : 'GitHub Repository'"
          ></v-text-field>

          <v-text-field
            v-model="form.github_branch"
            :label="lang === 'fa' ? 'برنچ GitHub' : 'GitHub Branch'"
          ></v-text-field>

          <v-switch
            v-model="form.status"
            :label="lang === 'fa' ? 'فعال' : 'Active'"
            color="primary"
          ></v-switch>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="closeDialog">{{ lang === 'fa' ? 'لغو' : 'Cancel' }}</v-btn>
        <v-btn color="primary" @click="saveTemplate" :loading="saving">
          {{ lang === 'fa' ? 'ذخیره' : 'Save' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Delete Confirmation -->
  <v-dialog v-model="deleteDialog" max-width="400">
    <v-card>
      <v-card-title>{{ lang === 'fa' ? 'تأیید حذف' : 'Confirm Delete' }}</v-card-title>
      <v-card-text>
        {{ lang === 'fa' ? 'آیا از حذف این قالب اطمینان دارید؟' : 'Are you sure you want to delete this template?' }}
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="deleteDialog = false">{{ lang === 'fa' ? 'لغو' : 'Cancel' }}</v-btn>
        <v-btn color="error" @click="deleteTemplate" :loading="deleting">
          {{ lang === 'fa' ? 'حذف' : 'Delete' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import PageBar from "@/components/blocks/PageBar.vue";
import PageLoading from "@/components/blocks/PageLoading.vue";
import Placeholder from "@/components/blocks/Placeholder.vue";
import axios from "axios";

export default {
  name: "AdminTemplatesList",
  components: { PageBar, PageLoading, Placeholder },
  data() {
    return {
      loading: true,
      list: [],
      dialog: false,
      deleteDialog: false,
      isEdit: false,
      saving: false,
      deleting: false,
      form: {
        id: null,
        name_fa: '',
        name_en: '',
        description_fa: '',
        description_en: '',
        category: '',
        tags: [],
        github_repo: '',
        github_branch: 'main',
        status: true
      },
      selectedTemplate: null,
      lang: localStorage.getItem('app_language') || 'en'
    };
  },
  mounted() {
    this.loadTemplates();
  },
  methods: {
    async loadTemplates() {
      this.loading = true;
      try {
        const response = await axios.get('/admin/templates/list');
        this.list = response.data.list;
      } catch (error) {
        console.error('Error loading templates:', error);
      } finally {
        this.loading = false;
      }
    },
    openCreate() {
      this.isEdit = false;
      this.resetForm();
      this.dialog = true;
    },
    editTemplate(template) {
      this.$router.push({ name: 'AdminTemplateEditor', params: { id: template.id } });
    },
    resetForm() {
      this.form = {
        id: null,
        name_fa: '',
        name_en: '',
        description_fa: '',
        description_en: '',
        category: '',
        tags: [],
        github_repo: '',
        github_branch: 'main',
        status: true
      };
    },
    async saveTemplate() {
      if (!this.$refs.form.validate()) return;
      
      this.saving = true;
      try {
        const endpoint = this.isEdit ? '/admin/templates/update' : '/admin/templates/create';
        const data = {
          ...this.form,
          tags: JSON.stringify(this.form.tags),
          status: this.form.status ? 1 : 0
        };
        
        await axios.post(endpoint, data);
        this.closeDialog();
        this.loadTemplates();
      } catch (error) {
        console.error('Error saving template:', error);
        alert(this.lang === 'fa' ? 'خطا در ذخیره قالب' : 'Error saving template');
      } finally {
        this.saving = false;
      }
    },
    closeDialog() {
      this.dialog = false;
      this.resetForm();
    },
    confirmDelete(template) {
      this.selectedTemplate = template;
      this.deleteDialog = true;
    },
    async deleteTemplate() {
      this.deleting = true;
      try {
        await axios.post('/admin/templates/delete', { id: this.selectedTemplate.id });
        this.deleteDialog = false;
        this.loadTemplates();
      } catch (error) {
        console.error('Error deleting template:', error);
        alert(error.response?.data?.message || (this.lang === 'fa' ? 'خطا در حذف قالب' : 'Error deleting template'));
      } finally {
        this.deleting = false;
      }
    }
  }
};
</script>
