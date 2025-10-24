<template>
  <PageBar :title="$tr('project', 'key_358')" is-back/>
  <PageLoading v-if="loading"/>
  <v-container fluid v-else>
    <v-row>
      <v-col md="4" sm="12" cols="12">
        <div class="border rounded pa-4">
          <div class="text-h6">
            {{ subscribe.created_at }}
          </div>
          <div class="text-body-2 text-blue-grey-darken-1">
            {{ $tr('project', 'key_22') }}
          </div>
        </div>
      </v-col>
      <v-col md="4" sm="12" cols="12">
        <div class="border rounded pa-4">
          <div class="text-h6">
            {{ subscribe.expires_at }}
          </div>
          <div class="text-body-2 text-blue-grey-darken-1">
            {{ subscribe.is_active ? $tr('project', 'key_379') : $tr('project', 'key_380') }}
          </div>
        </div>
      </v-col>
      <v-col md="4" sm="12" cols="12">
        <div class="border rounded pa-4">
          <div :class="`text-h6 ${subscribe.is_active ? 'text-success' : 'text-red-accent-3'}`">
            {{ subscribe.is_active ? $tr('project', 'key_377') : $tr('project', 'key_378') }}
          </div>
          <div class="text-body-2 text-blue-grey-darken-1">
            {{ $tr('project', 'key_381') }}
          </div>
        </div>
      </v-col>
      <v-col md="12" sm="12" cols="12">
        <div class="border rounded">
          <v-list-item
            :to="`/private/apps/${subscribe.app.uid}/main`"
            lines="two"
          >
            <template v-slot:prepend>
              <SquircleImage
                :src="subscribe.app.icon"
                :size="48"
                class="mr-5"
              />
            </template>
            <v-list-item-title class="font-weight-medium">{{ subscribe.app.name }}</v-list-item-title>
            <v-list-item-subtitle>{{ subscribe.app.link }}</v-list-item-subtitle>
            <template v-slot:append>
              <v-list-item-action>
                <v-icon icon="mdi-chevron-right"/>
              </v-list-item-action>
            </template>
          </v-list-item>
        </div>
      </v-col>
      <v-col md="12" sm="12" cols="12">
        <div class="d-flex justify-space-between align-center mb-3">
          <div class="text-body-1 font-weight-medium">
            {{ $tr('project', 'key_382') }}
          </div>
          <v-btn variant="flat" color="red-accent-3" :disabled="!subscribe.is_active" @click="isOpenModel = true">
            {{ $tr('project', 'key_383') }}
          </v-btn>
        </div>
        <v-list
          v-if="list.length"
          item-props
          density="comfortable"
        >
          <div v-for="(transaction) in list">
            <v-list-item
              lines="two"
              density="default"
            >
              <template v-slot:prepend>
                <v-avatar size="38" :image="transaction.method.logo"/>
              </template>
              <v-list-item-title>{{ transaction.method.name }}</v-list-item-title>
              <v-list-item-subtitle>ID: {{ transaction.uid }}</v-list-item-subtitle>
              <template v-slot:append>
                <v-list-item-action>
                  <div class="d-flex justify-end align-end flex-column">
                    <v-list-item-title class="font-weight-medium">
                      {{ transaction.amount }} {{ $store.state.config.currency }}
                    </v-list-item-title>
                    <v-list-item-subtitle>{{ transaction.created_at }}</v-list-item-subtitle>
                  </div>
                </v-list-item-action>
              </template>
            </v-list-item>
            <v-divider/>
          </div>
        </v-list>
        <div v-else>
          {{ $tr('project', 'key_384') }}
        </div>
        <div class="text-center mt-5" v-if="list.length !== total">
          <v-btn variant="text" color="primary" :loading="loadingMore" @click="getTransactionList">
            {{ $tr('project', 'key_26') }}
          </v-btn>
        </div>
      </v-col>
    </v-row>
  </v-container>
  <v-dialog
    max-width="560"
    :close-on-content-click="false"
    :persistent="true"
    :no-click-animation="true"
    v-model="isOpenModel"
  >
    <CancelSubscribe
      :uid="$route.params.subscribe_uid"
      @close="isOpenModel = false"
      @success-cancel="successCancel"
    />
  </v-dialog>
</template>

<script>
import PageLoading from "@/components/blocks/PageLoading.vue";
import PageBar from "@/components/blocks/PageBar.vue";
import profileService from "@/services/profile/profile.service";
import SquircleImage from "@/components/blocks/SquircleImage.vue";
import CancelSubscribe from "@/components/modals/CancelSubscribe.vue";

export default {
  name: 'Transactions',
  components: {CancelSubscribe, SquircleImage, PageBar, PageLoading},
  data: () => ({
    loading: true,
    offset: 0,
    total: 0,
    list: [],
    loadingMore: false,
    subscribe: {
      uid: "",
      created_at: "",
      expires_at: "",
      price: "",
      app: {
        name: "",
        uid: "",
        icon: "",
        link: ""
      },
      is_active: false
    },
    isOpenModel: false
  }),
  watch: {

  },
  methods: {
    getTransactionList() {
      this.loadingMore = true;
      profileService.getTransactions(this.offset, this.$route.params.subscribe_uid).then((res) => {
        const data = res.data;
        this.list = this.list.concat(data.list)
        this.subscribe = data.subscribe;
        this.total = data.total;
        this.offset = this.offset + this.$store.state.offset;
        this.loading = false;
        this.loadingMore = false;
      }).catch(e => {
        this.$store.commit('openSnackbar', e);
        this.loadingMore = false;
      });
    },
    successCancel() {
      this.subscribe.is_active = false;
      this.isOpenModel = false;
    }
  },
  beforeMount() {
    this.getTransactionList();
  }
};
</script>
