<template>
  <div class="templates-gallery">
    <!-- Header -->
    <v-container>
      <v-row class="my-8">
        <v-col cols="12" class="text-center">
          <h1 class="text-h3 font-weight-bold mb-4">{{ $t('key_browse_templates') }}</h1>
          <p class="text-h6 text-grey-darken-1">{{ $t('key_choose_template_subtitle') }}</p>
        </v-col>
      </v-row>
    </v-container>

    <v-container fluid>
      <!-- Filters -->
      <v-row class="mb-4">
        <v-col cols="12" md="4">
          <v-text-field
            v-model="searchQuery"
            :label="$t('key_search')"
            prepend-inner-icon="mdi-magnify"
            outlined
            dense
            clearable
            @input="filterTemplates"
            data-testid="template-search"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="4">
          <v-select
            v-model="selectedCategory"
            :items="categories"
            :label="$t('key_category')"
            outlined
            dense
            clearable
            @change="filterTemplates"
            data-testid="category-filter"
          ></v-select>
        </v-col>

        <v-col cols="12" md="4">
          <v-select
            v-model="viewMode"
            :items="viewModes"
            :label="$t('key_view')"
            outlined
            dense
            data-testid="view-mode"
          >
            <template v-slot:item="{ item }">
              <v-icon class="mr-2">{{ item.icon }}</v-icon>
              {{ item.title }}
            </template>
            <template v-slot:selection="{ item }">
              <v-icon class="mr-2">{{ item.icon }}</v-icon>
              {{ item.title }}
            </template>
          </v-select>
        </v-col>
      </v-row>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      </div>

      <!-- Templates Grid/List -->
      <v-row v-else-if="filteredTemplates.length > 0">
        <v-col
          v-for="template in filteredTemplates"
          :key="template.uid"
          :cols="viewMode === 'grid' ? 12 : 12"
          :md="viewMode === 'grid' ? 4 : 12"
        >
          <v-card
            :class="['template-card', { 'list-view': viewMode === 'list' }]"
            hover
            :data-testid="`template-card-${template.uid}`"
          >
            <div class="template-card-content">
              <v-img
                :src="template.thumbnail || '/default-template.png'"
                :aspect-ratio="viewMode === 'grid' ? 16/9 : 3/1"
                class="template-thumbnail"
                cover
              >
                <div v-if="template.is_primary" class="primary-badge">
                  <v-chip color="primary" size="small">{{ $t('key_primary') }}</v-chip>
                </div>
              </v-img>

              <v-card-text>
                <h3 class="text-h6 mb-2">
                  {{ $i18n.locale === 'fa' ? template.name_fa : template.name_en }}
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-3">
                  {{ $i18n.locale === 'fa' ? template.description_fa : template.description_en }}
                </p>

                <!-- Tags -->
                <div class="mb-3">
                  <v-chip
                    v-for="(tag, index) in parseTags(template.tags)"
                    :key="index"
                    size="small"
                    class="mr-2 mb-2"
                  >
                    {{ tag }}
                  </v-chip>
                </div>

                <!-- Category Badge -->
                <v-chip
                  v-if="template.category"
                  color="secondary"
                  size="small"
                  class="mb-2"
                >
                  {{ template.category }}
                </v-chip>
              </v-card-text>

              <v-card-actions>
                <v-btn
                  color="primary"
                  variant="flat"
                  @click="selectTemplate(template)"
                  :data-testid="`select-template-${template.uid}`"
                >
                  {{ $t('key_select_template') }}
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                  icon="mdi-information"
                  variant="text"
                  @click="viewDetails(template)"
                  :data-testid="`view-details-${template.uid}`"
                ></v-btn>
              </v-card-actions>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <v-icon size="64" color="grey">mdi-package-variant</v-icon>
        <p class="text-h6 mt-4 text-grey">{{ $t('key_no_templates_found') }}</p>
      </div>
    </v-container>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'TemplatesGallery',

  data() {
    return {
      loading: true,
      templates: [],
      filteredTemplates: [],
      searchQuery: '',
      selectedCategory: null,
      categories: [],
      viewMode: 'grid',
      viewModes: [
        { value: 'grid', title: this.$t('key_grid_view'), icon: 'mdi-view-grid' },
        { value: 'list', title: this.$t('key_list_view'), icon: 'mdi-view-list' }
      ]
    };
  },

  mounted() {
    this.loadTemplates();
    this.loadCategories();
  },

  methods: {
    async loadTemplates() {
      this.loading = true;
      try {
        const response = await axios.get('/public/data/templates');
        if (response.data.status === 200) {
          this.templates = response.data.data.templates;
          this.filteredTemplates = this.templates;
        }
      } catch (error) {
        console.error('Failed to load templates:', error);
      } finally {
        this.loading = false;
      }
    },

    async loadCategories() {
      try {
        const response = await axios.get('/public/data/categories');
        if (response.data.status === 200) {
          this.categories = response.data.data.categories.map(cat => ({
            value: cat,
            title: cat
          }));
        }
      } catch (error) {
        console.error('Failed to load categories:', error);
      }
    },

    filterTemplates() {
      let filtered = this.templates;

      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(t => 
          t.name_en.toLowerCase().includes(query) ||
          t.name_fa.toLowerCase().includes(query) ||
          t.description_en.toLowerCase().includes(query) ||
          t.description_fa.toLowerCase().includes(query) ||
          (t.tags && t.tags.toLowerCase().includes(query))
        );
      }

      // Category filter
      if (this.selectedCategory) {
        filtered = filtered.filter(t => t.category === this.selectedCategory);
      }

      this.filteredTemplates = filtered;
    },

    parseTags(tags) {
      if (!tags) return [];
      try {
        return JSON.parse(tags);
      } catch {
        return tags.split(',').map(t => t.trim());
      }
    },

    selectTemplate(template) {
      // Redirect to create project with selected template
      this.$router.push(`/private/apps?template=${template.uid}`);
    },

    viewDetails(template) {
      // Navigate to template detail page
      this.$router.push(`/templates/${template.uid}`);
    }
  }
};
</script>

<style scoped>
.templates-gallery {
  min-height: 100vh;
  padding: 20px 0;
}

.template-card {
  height: 100%;
  transition: transform 0.2s, box-shadow 0.2s;
}

.template-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.template-card.list-view {
  display: flex;
}

.template-card.list-view .template-card-content {
  display: flex;
  width: 100%;
}

.template-card.list-view .v-img {
  max-width: 300px;
}

.template-thumbnail {
  position: relative;
}

.primary-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}
</style>
