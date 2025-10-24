<template>
  <PageBar :title="$tr('menu', 'key_44')"/>
  <PageLoading v-if="loading"/>
  <template v-else>
    <Placeholder v-if="!list.length">
      <template v-slot:icon>
        <CrownIcon :size="56"/>
      </template>
      <template v-slot:title>
        {{ $tr('project', 'key_375') }}
      </template>
      <template v-slot:subtitle>
        {{ $tr('project', 'key_376') }}
      </template>
    </Placeholder>
    <v-container fluid v-else>
      <v-list
        item-props
        density="comfortable"
      >
        <div v-for="(subscribe) in list">
          <v-list-item
            lines="two"
            density="default"
            :to="`/private/profile/transactions/${subscribe.uid}`"
          >
            <template v-slot:prepend>
              <SquircleImage
                :src="subscribe.app.icon"
                :size="48"
                class="mr-5"
              />
            </template>
            <v-list-item-title class="font-weight-medium">
              {{ subscribe.app.name }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ subscribe.is_active ? $tr('project', 'key_379') : $tr('project', 'key_380') }} {{ subscribe.expires_at }}
            </v-list-item-subtitle>
            <template v-slot:append>
              <v-list-item-action>
                <div class="d-flex justify-end align-end flex-column">
                  <div class="text-h6">
                    {{ subscribe.price }} {{ $store.state.config.currency }}
                  </div>
                  <v-chip
                    density="compact"
                    variant="tonal"
                    :color="subscribe.is_active ? 'success' : 'red-accent-3'"
                  >
                    <span class="text-caption">
                      {{ subscribe.is_active ? $tr('project', 'key_377') : $tr('project', 'key_378') }}
                    </span>
                  </v-chip>
                </div>
              </v-list-item-action>
            </template>
          </v-list-item>
          <v-divider/>
        </div>
      </v-list>
      <div class="text-center mt-5" v-if="list.length !== total">
        <v-btn variant="text" color="primary" :loading="loadingMore" @click="getSubscribesList">
          {{ $tr('project', 'key_26') }}
        </v-btn>
      </div>
    </v-container>
  </template>
</template>

<script>
import PageBar from "@/components/blocks/PageBar.vue";
import PageLoading from "@/components/blocks/PageLoading.vue";
import profileService from "@/services/profile/profile.service";
import Placeholder from "@/components/blocks/Placeholder.vue";
import BillIcon from "@/components/icons/BillIcon.vue";
import CrownIcon from "@/components/icons/CrownIcon.vue";
import SquircleImage from "@/components/blocks/SquircleImage.vue";

export default {
  name: 'Subscribe',
  components: {SquircleImage, CrownIcon, BillIcon, Placeholder, PageLoading, PageBar},
  data: () => ({
    loading: true,
    offset: 0,
    total: 0,
    list: [],
    loadingMore: false
  }),
  watch: {

  },
  methods: {
    getSubscribesList() {
      this.loadingMore = true;
      profileService.getSubscribes(this.offset).then((res) => {
        const data = res.data;
        this.list = this.list.concat(data.list)
        this.total = data.total;
        this.offset = this.offset + this.$store.state.offset;
        this.loading = false;
        this.loadingMore = false;
      }).catch(e => {
        this.$store.commit('openSnackbar', e);
        this.loadingMore = false;
      });
    }
  },
  beforeMount() {
    this.getSubscribesList();
  }
};
</script>
