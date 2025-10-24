<template>
  <div class="dynamic-form-builder">
    <v-row>
      <!-- Form Builder -->
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>
            {{ lang === 'fa' ? 'سازنده فرم' : 'Form Builder' }}
            <v-spacer></v-spacer>
            <v-btn color="primary" size="small" @click="addField">
              <v-icon left>mdi-plus</v-icon>
              {{ lang === 'fa' ? 'افزودن فیلد' : 'Add Field' }}
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-list v-if="schema.fields && schema.fields.length">
              <draggable v-model="schema.fields" item-key="name" handle=".drag-handle">
                <template #item="{element, index}">
                  <v-list-item class="mb-2 border rounded">
                    <template v-slot:prepend>
                      <v-icon class="drag-handle" style="cursor: move;">mdi-drag</v-icon>
                    </template>
                    
                    <v-list-item-title>
                      {{ element.label_fa || element.label_en || element.name }}
                      <v-chip v-if="element.immutable" size="x-small" color="warning" class="ml-2">
                        {{ lang === 'fa' ? 'قفل' : 'Locked' }}
                      </v-chip>
                      <v-chip v-if="element.required" size="x-small" color="error" class="ml-2">*</v-chip>
                    </v-list-item-title>
                    <v-list-item-subtitle>{{ element.type }}</v-list-item-subtitle>
                    
                    <template v-slot:append>
                      <v-btn icon size="small" @click="editField(index)">
                        <v-icon>mdi-pencil</v-icon>
                      </v-btn>
                      <v-btn icon size="small" color="error" @click="removeField(index)">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </template>
                  </v-list-item>
                </template>
              </draggable>
            </v-list>
            <div v-else class="text-center py-8 text-grey">
              {{ lang === 'fa' ? 'هیچ فیلدی اضافه نشده است' : 'No fields added yet' }}
            </div>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="$emit('save', schema)">
              {{ lang === 'fa' ? 'ذخیره فرم' : 'Save Form' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Preview -->
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>{{ lang === 'fa' ? 'پیش‌نمایش' : 'Preview' }}</v-card-title>
          <v-card-text>
            <v-form v-if="schema.fields && schema.fields.length">
              <template v-for="field in schema.fields" :key="field.name">
                <!-- Text Input -->
                <v-text-field
                  v-if="field.type === 'text'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  :placeholder="field.placeholder"
                  :required="field.required"
                  :disabled="field.immutable"
                ></v-text-field>

                <!-- Number Input -->
                <v-text-field
                  v-else-if="field.type === 'number'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  type="number"
                  :required="field.required"
                  :disabled="field.immutable"
                ></v-text-field>

                <!-- URL Input -->
                <v-text-field
                  v-else-if="field.type === 'url'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  type="url"
                  :required="field.required"
                  :disabled="field.immutable"
                ></v-text-field>

                <!-- Color Picker -->
                <v-color-picker
                  v-else-if="field.type === 'color'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  :disabled="field.immutable"
                  mode="hexa"
                  class="mb-4"
                ></v-color-picker>

                <!-- Select -->
                <v-select
                  v-else-if="field.type === 'select'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  :items="field.options || []"
                  :item-title="lang === 'fa' ? 'label_fa' : 'label_en'"
                  item-value="value"
                  :required="field.required"
                  :disabled="field.immutable"
                ></v-select>

                <!-- Boolean Switch -->
                <v-switch
                  v-else-if="field.type === 'boolean'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  :disabled="field.immutable"
                  color="primary"
                ></v-switch>

                <!-- Textarea -->
                <v-textarea
                  v-else-if="field.type === 'textarea'"
                  :label="lang === 'fa' ? field.label_fa : field.label_en"
                  :required="field.required"
                  :disabled="field.immutable"
                  rows="3"
                ></v-textarea>
              </template>
            </v-form>
            <div v-else class="text-center py-8 text-grey">
              {{ lang === 'fa' ? 'پیش‌نمایش در دسترس نیست' : 'No preview available' }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Field Editor Dialog -->
    <v-dialog v-model="fieldDialog" max-width="600" persistent>
      <v-card>
        <v-card-title>
          {{ editingIndex === -1 ? (lang === 'fa' ? 'افزودن فیلد' : 'Add Field') : (lang === 'fa' ? 'ویرایش فیلد' : 'Edit Field') }}
        </v-card-title>
        <v-card-text>
          <v-form ref="fieldForm">
            <v-text-field
              v-model="currentField.name"
              :label="lang === 'fa' ? 'نام فیلد (انگلیسی)' : 'Field Name (English)'"
              :rules="[v => !!v || 'Required']"
              hint="e.g., package_id, app_name"
            ></v-text-field>

            <v-select
              v-model="currentField.type"
              :label="lang === 'fa' ? 'نوع فیلد' : 'Field Type'"
              :items="fieldTypes"
              :rules="[v => !!v || 'Required']"
            ></v-select>

            <v-text-field
              v-model="currentField.label_fa"
              :label="lang === 'fa' ? 'برچسب فارسی' : 'Label (Persian)'"
              :rules="[v => !!v || 'Required']"
            ></v-text-field>

            <v-text-field
              v-model="currentField.label_en"
              :label="lang === 'fa' ? 'برچسب انگلیسی' : 'Label (English)'"
              :rules="[v => !!v || 'Required']"
            ></v-text-field>

            <v-text-field
              v-model="currentField.placeholder"
              :label="lang === 'fa' ? 'متن راهنما' : 'Placeholder'"
            ></v-text-field>

            <v-text-field
              v-model="currentField.default"
              :label="lang === 'fa' ? 'مقدار پیش‌فرض' : 'Default Value'"
            ></v-text-field>

            <v-switch
              v-model="currentField.required"
              :label="lang === 'fa' ? 'الزامی' : 'Required'"
              color="primary"
            ></v-switch>

            <v-switch
              v-model="currentField.immutable"
              :label="lang === 'fa' ? 'قفل بعد از اولین ذخیره' : 'Lock After First Save'"
              color="warning"
            ></v-switch>

            <!-- Options for Select -->
            <div v-if="currentField.type === 'select'">
              <v-divider class="my-4"></v-divider>
              <div class="text-subtitle-2 mb-2">{{ lang === 'fa' ? 'گزینه‌ها' : 'Options' }}</div>
              <v-list>
                <v-list-item v-for="(option, idx) in currentField.options" :key="idx" class="border rounded mb-2">
                  <v-list-item-title>{{ option.label_fa }} / {{ option.label_en }}</v-list-item-title>
                  <v-list-item-subtitle>Value: {{ option.value }}</v-list-item-subtitle>
                  <template v-slot:append>
                    <v-btn icon size="small" color="error" @click="removeOption(idx)">
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </template>
                </v-list-item>
              </v-list>
              <v-btn size="small" @click="addOption">
                <v-icon left>mdi-plus</v-icon>
                {{ lang === 'fa' ? 'افزودن گزینه' : 'Add Option' }}
              </v-btn>
            </div>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="closeFieldDialog">{{ lang === 'fa' ? 'لغو' : 'Cancel' }}</v-btn>
          <v-btn color="primary" @click="saveField">{{ lang === 'fa' ? 'ذخیره' : 'Save' }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Option Editor Dialog -->
    <v-dialog v-model="optionDialog" max-width="400">
      <v-card>
        <v-card-title>{{ lang === 'fa' ? 'افزودن گزینه' : 'Add Option' }}</v-card-title>
        <v-card-text>
          <v-text-field
            v-model="currentOption.value"
            :label="lang === 'fa' ? 'مقدار' : 'Value'"
          ></v-text-field>
          <v-text-field
            v-model="currentOption.label_fa"
            :label="lang === 'fa' ? 'برچسب فارسی' : 'Label (Persian)'"
          ></v-text-field>
          <v-text-field
            v-model="currentOption.label_en"
            :label="lang === 'fa' ? 'برچسب انگلیسی' : 'Label (English)'"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="optionDialog = false">{{ lang === 'fa' ? 'لغو' : 'Cancel' }}</v-btn>
          <v-btn color="primary" @click="saveOption">{{ lang === 'fa' ? 'ذخیره' : 'Save' }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
  name: 'DynamicFormBuilder',
  components: { draggable },
  props: {
    modelValue: {
      type: Object,
      default: () => ({ fields: [] })
    }
  },
  data() {
    return {
      schema: this.modelValue || { fields: [] },
      fieldDialog: false,
      optionDialog: false,
      editingIndex: -1,
      currentField: this.getEmptyField(),
      currentOption: { value: '', label_fa: '', label_en: '' },
      fieldTypes: [
        { title: 'Text', value: 'text' },
        { title: 'Number', value: 'number' },
        { title: 'URL', value: 'url' },
        { title: 'Email', value: 'email' },
        { title: 'Color', value: 'color' },
        { title: 'Select', value: 'select' },
        { title: 'Boolean', value: 'boolean' },
        { title: 'Textarea', value: 'textarea' }
      ],
      lang: localStorage.getItem('app_language') || 'en'
    };
  },
  watch: {
    schema: {
      deep: true,
      handler(val) {
        this.$emit('update:modelValue', val);
      }
    },
    modelValue: {
      deep: true,
      handler(val) {
        this.schema = val || { fields: [] };
      }
    }
  },
  methods: {
    getEmptyField() {
      return {
        name: '',
        type: 'text',
        label_fa: '',
        label_en: '',
        placeholder: '',
        required: false,
        immutable: false,
        default: '',
        options: []
      };
    },
    addField() {
      this.currentField = this.getEmptyField();
      this.editingIndex = -1;
      this.fieldDialog = true;
    },
    editField(index) {
      this.currentField = { ...this.schema.fields[index] };
      this.editingIndex = index;
      this.fieldDialog = true;
    },
    saveField() {
      if (this.editingIndex === -1) {
        this.schema.fields.push({ ...this.currentField });
      } else {
        this.schema.fields[this.editingIndex] = { ...this.currentField };
      }
      this.closeFieldDialog();
    },
    closeFieldDialog() {
      this.fieldDialog = false;
      this.currentField = this.getEmptyField();
      this.editingIndex = -1;
    },
    removeField(index) {
      this.schema.fields.splice(index, 1);
    },
    addOption() {
      this.currentOption = { value: '', label_fa: '', label_en: '' };
      this.optionDialog = true;
    },
    saveOption() {
      if (!this.currentField.options) {
        this.currentField.options = [];
      }
      this.currentField.options.push({ ...this.currentOption });
      this.optionDialog = false;
    },
    removeOption(index) {
      this.currentField.options.splice(index, 1);
    }
  }
};
</script>

<style scoped>
.drag-handle {
  cursor: move;
}
.border {
  border: 1px solid #e0e0e0;
}
</style>
