<template>
  <v-card>
    <v-toolbar color="transparent">
      <v-toolbar-title>
        {{ !this.id ? $tr('admin', 'key_47') : $tr('admin', 'key_48') }}
      </v-toolbar-title>
      <v-spacer/>
      <v-btn
        icon="mdi-window-close"
        color="red-accent-3"
        density="default"
        @click="this.$emit('close')"
      />
    </v-toolbar>
    <v-divider/>
    <v-container fluid>
      <v-row class="mt-3">
        <v-col md="12" sm="12" cols="12" class="py-0">
          <v-text-field
            :label="$tr('admin', 'key_39')"
            variant="outlined"
            density="comfortable"
            v-model="new_count"
          ></v-text-field>
        </v-col>
        <v-col md="6" sm="12" cols="12" class="py-0">
          <v-text-field
            :label="`${$tr('admin', 'key_40')}, ${$store.state.config.currency}`"
            variant="outlined"
            density="comfortable"
            v-model="new_price"
          ></v-text-field>
        </v-col>
        <v-col md="6" sm="12" cols="12" class="py-0">
          <v-text-field
            :label="`${$tr('admin', 'key_41')}, ${$store.state.config.currency}`"
            variant="outlined"
            density="comfortable"
            v-model="new_save"
          ></v-text-field>
        </v-col>
        <v-col md="12" sm="12" cols="12" class="py-0">
          <v-text-field
            :label="$tr('admin', 'key_121')"
            variant="outlined"
            density="comfortable"
            v-model="new_api_id"
          ></v-text-field>
        </v-col>
      </v-row>
    </v-container>
    <v-divider/>
    <v-container fluid class="d-flex justify-end align-center">
      <v-btn flat color="primary" :loading="loading" @click="saveAction">
        {{ $tr('project', 'key_173') }}
      </v-btn>
    </v-container>
  </v-card>
</template>

<script>
import plansService from "@/services/plans/plans.service";

export default {
  name: 'ChangePlan',
  props: {
    id: {
      type: Number,
    },
    price: {
      type: String,
      default: ""
    },
    save: {
      type: String,
      default: ""
    },
    count: {
      type: Number,
      default: 1
    },
    api_id: {
      type: String,
      default: ""
    },
  },
  data: () => ({
    new_price: "",
    new_save: "",
    new_count: 1,
    loading: false,
    new_api_id: ""
  }),
  methods: {
    saveAction() {
      this.loading = true;
      if (this.id) {
        // update
        plansService.updatePlan(this.id, {
          count: this.new_count,
          price: this.new_price,
          save: this.new_save,
          api_id: this.new_api_id
        }).then(() => {
          this.$emit("on-success");
          this.loading = false;
        }).catch(e => {
          this.$store.commit('openSnackbar', e);
          this.loading = false;
        });
      } else {
        // create
        plansService.createPlan({
          count: this.new_count,
          price: this.new_price,
          save: this.new_save,
          api_id: this.new_api_id
        }).then(() => {
          this.$emit("on-success");
          this.loading = false;
        }).catch(e => {
          this.$store.commit('openSnackbar', e);
          this.loading = false;
        });
      }
    }
  },
  beforeMount() {
    if (this.id) {
      this.new_price = this.price;
      this.new_save = this.save;
      this.new_count = this.count;
      this.new_api_id = this.api_id;
    }
  }
};
</script>
