<template>
  <PageBar :title="$tr('admin', 'key_8')" :is-menu="false"/>
  <PageLoading v-if="loading"/>
  <template v-else>
    <Placeholder v-if="!list.length">
      <template v-slot:icon>
        <MoneyTransferIcon :size="56"/>
      </template>
      <template v-slot:title>
        {{ $tr('admin', 'key_53') }}
      </template>
      <template v-slot:subtitle>
        {{ $tr('admin', 'key_53') }}
      </template>
    </Placeholder>
    <v-container fluid v-else>
      <div class="text-body-1 font-weight-medium">
        {{ `${$tr('admin', 'key_52')}: ${total}` }}
      </div>
      <v-list
        item-props
        density="comfortable"
      >
        <div v-for="(transaction) in list">
          <v-list-item
            lines="two"
            density="default"
            :to="`/admin/transactions/subscribe/${transaction.subscribe_external_id}`"
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
          <v-divider inset/>
        </div>
      </v-list>
      <div class="text-center mt-5" v-if="list.length !== total">
        <v-btn variant="text" color="primary" :loading="loadingMore" @click="getTransactions">
          {{ $tr('project', 'key_26') }}
        </v-btn>
      </div>
    </v-container>
  </template>
</template>

<script>
import PageLoading from "@/components/blocks/PageLoading.vue";
import PageBar from "@/components/blocks/PageBar.vue";
import adminSubscribeService from "@/services/subscribe/admin.subscribe.service";

export default {
  name: 'AdminTransactions',
  components: {PageBar, PageLoading},
  data: () => ({
    loading: true,
    list: [],
    offset: 0,
    loadingMore: false
  }),
  methods: {
    getTransactions() {
      this.loadingMore = true;
      adminSubscribeService.getTransactions(this.offset).then((res) => {
        const data = res.data;
        this.offset = this.offset + this.$store.state.offset;
        this.total = data.total;
        this.list = this.list.concat(data.list)
        this.loading = false;
        this.loadingMore = false;
      }).catch(e => {
        this.$store.commit('openSnackbar', e);
        this.loading = false;
        this.loadingMore = false;
      });
    }
  },
  beforeMount() {
    this.getTransactions();
  }
};
</script>
<script setup>
import Placeholder from "@/components/blocks/Placeholder.vue";
import MoneyTransferIcon from "@/components/icons/MoneyTransferIcon.vue";
</script>
