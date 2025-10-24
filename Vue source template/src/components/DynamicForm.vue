<template>
  <v-form ref="formRef" v-model="valid" @submit.prevent="handleSubmit">
    <v-row>
      <v-col
        v-for="field in schema"
        :key="field.name"
        :cols="12"
        :md="field.type === 'textarea' ? 12 : 6"
      >
        <!-- Text Input -->
        <v-text-field
          v-if="field.type === 'text'"
          v-model="formData[field.name]"
          :label="getFieldLabel(field)"
          :placeholder="field.placeholder"
          :required="field.required"
          :rules="getValidationRules(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          variant="outlined"
          density="comfortable"
          :data-testid="`field-${field.name}`"
        />

        <!-- Number Input -->
        <v-text-field
          v-else-if="field.type === 'number'"
          v-model.number="formData[field.name]"
          :label="getFieldLabel(field)"
          :placeholder="field.placeholder"
          :required="field.required"
          :rules="getValidationRules(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          type="number"
          variant="outlined"
          density="comfortable"
          :data-testid="`field-${field.name}`"
        />

        <!-- URL Input -->
        <v-text-field
          v-else-if="field.type === 'url'"
          v-model="formData[field.name]"
          :label="getFieldLabel(field)"
          :placeholder="field.placeholder"
          :required="field.required"
          :rules="getValidationRules(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          type="url"
          variant="outlined"
          density="comfortable"
          :data-testid="`field-${field.name}`"
        />

        <!-- Email Input -->
        <v-text-field
          v-else-if="field.type === 'email'"
          v-model="formData[field.name]"
          :label="getFieldLabel(field)"
          :placeholder="field.placeholder"
          :required="field.required"
          :rules="getValidationRules(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          type="email"
          variant="outlined"
          density="comfortable"
          :data-testid="`field-${field.name}`"
        />

        <!-- Color Picker -->
        <div v-else-if="field.type === 'color'">
          <v-label class="mb-2">{{ getFieldLabel(field) }}</v-label>
          <v-color-picker
            v-model="formData[field.name]"
            :disabled="isFieldLocked(field)"
            mode="hex"
            show-swatches
            :data-testid="`field-${field.name}`"
          />
          <v-text-field
            v-model="formData[field.name]"
            :disabled="isFieldLocked(field)"
            :hint="isFieldLocked(field) ? getLockedHint() : ''"
            :persistent-hint="isFieldLocked(field)"
            variant="outlined"
            density="compact"
            class="mt-2"
            :data-testid="`field-${field.name}-input`"
          />
        </div>

        <!-- Select Dropdown -->
        <v-select
          v-else-if="field.type === 'select'"
          v-model="formData[field.name]"
          :label="getFieldLabel(field)"
          :items="getSelectOptions(field)"
          :required="field.required"
          :rules="getValidationRules(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          variant="outlined"
          density="comfortable"
          :data-testid="`field-${field.name}`"
        />

        <!-- Boolean Switch -->
        <v-switch
          v-else-if="field.type === 'boolean'"
          v-model="formData[field.name]"
          :label="getFieldLabel(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          color="primary"
          :data-testid="`field-${field.name}`"
        />

        <!-- Textarea -->
        <v-textarea
          v-else-if="field.type === 'textarea'"
          v-model="formData[field.name]"
          :label="getFieldLabel(field)"
          :placeholder="field.placeholder"
          :required="field.required"
          :rules="getValidationRules(field)"
          :disabled="isFieldLocked(field)"
          :hint="isFieldLocked(field) ? getLockedHint() : ''"
          :persistent-hint="isFieldLocked(field)"
          variant="outlined"
          rows="4"
          :data-testid="`field-${field.name}`"
        />
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <v-col cols="12">
        <v-btn
          type="submit"
          color="primary"
          size="large"
          :loading="loading"
          :disabled="!valid"
          block
          data-testid="dynamic-form-submit"
        >
          {{ submitText || (currentLocale === 'fa' ? 'ذخیره تنظیمات' : 'Save Settings') }}
        </v-btn>
      </v-col>
    </v-row>
  </v-form>
</template>

<script>
export default {
  name: 'DynamicForm',
  props: {
    schema: {
      type: Array,
      required: true,
      default: () => []
    },
    initialData: {
      type: Object,
      default: () => ({})
    },
    lockedFields: {
      type: Array,
      default: () => []
    },
    isLocked: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    submitText: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      valid: false,
      formData: {},
      currentLocale: 'en'
    }
  },
  watch: {
    initialData: {
      immediate: true,
      handler(newVal) {
        if (newVal && Object.keys(newVal).length > 0) {
          this.formData = { ...newVal }
        } else {
          this.initializeFormData()
        }
      }
    },
    schema: {
      immediate: true,
      handler() {
        if (Object.keys(this.formData).length === 0) {
          this.initializeFormData()
        }
      }
    }
  },
  mounted() {
    this.currentLocale = this.$store?.state?.locale || localStorage.getItem('locale') || 'en'
  },
  methods: {
    initializeFormData() {
      const data = {}
      this.schema.forEach(field => {
        if (field.default !== undefined) {
          data[field.name] = field.default
        } else if (field.type === 'boolean') {
          data[field.name] = false
        } else if (field.type === 'number') {
          data[field.name] = 0
        } else {
          data[field.name] = ''
        }
      })
      this.formData = data
    },

    getFieldLabel(field) {
      const locale = this.currentLocale
      if (locale === 'fa' && field.label_fa) {
        return field.label_fa
      }
      return field.label_en || field.label_fa || field.name
    },

    getSelectOptions(field) {
      if (!field.options || !Array.isArray(field.options)) {
        return []
      }
      
      const locale = this.currentLocale
      return field.options.map(opt => ({
        value: opt.value,
        title: locale === 'fa' && opt.label_fa ? opt.label_fa : opt.label_en
      }))
    },

    isFieldLocked(field) {
      // اگر کل فرم قفل است
      if (this.isLocked) {
        return false // اگر قفل کلی است، همه فیلدها غیرفعال نیستند
      }
      
      // اگر فیلد immutable است و در لیست قفل‌شده‌ها هست
      if (field.immutable && this.lockedFields.includes(field.name)) {
        return true
      }
      
      return false
    },

    getLockedHint() {
      return this.currentLocale === 'fa' 
        ? 'این فیلد بعد از اولین ذخیره قابل تغییر نیست'
        : 'This field cannot be changed after first save'
    },

    getValidationRules(field) {
      const rules = []
      
      if (field.required) {
        rules.push(v => !!v || (this.currentLocale === 'fa' ? 'این فیلد الزامی است' : 'This field is required'))
      }
      
      if (field.type === 'url') {
        rules.push(v => {
          if (!v) return true
          try {
            new URL(v)
            return true
          } catch {
            return this.currentLocale === 'fa' ? 'آدرس URL معتبر نیست' : 'Invalid URL'
          }
        })
      }
      
      if (field.type === 'email') {
        rules.push(v => {
          if (!v) return true
          const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
          return pattern.test(v) || (this.currentLocale === 'fa' ? 'ایمیل معتبر نیست' : 'Invalid email')
        })
      }
      
      return rules
    },

    handleSubmit() {
      if (this.valid) {
        this.$emit('submit', this.formData)
      }
    },

    resetForm() {
      this.$refs.formRef?.reset()
      this.initializeFormData()
    }
  }
}
</script>

<style scoped>
.v-label {
  font-size: 14px;
  font-weight: 500;
  color: rgba(0, 0, 0, 0.6);
}
</style>