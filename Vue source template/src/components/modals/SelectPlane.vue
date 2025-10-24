<template>
  <v-card>
    <v-toolbar color="transparent">
      <div class="d-flex justify-center align-center align-self-center" style="width: 100vw">
        <v-img :src="$store.state.config.logo" width="34" height="34" :aspect-ratio="1"/>
        <v-btn
          icon="mdi-window-close"
          color="red-accent-3"
          style="position: absolute; right: 18px"
          @click="$store.commit('switchOpenPaymentModal')"
        />
      </div>
    </v-toolbar>
    <v-divider/>
    <ScreenLock v-if="loading" is-secondary/>
    <div v-else class="d-flex justify-center align-center">
      <div class="wizard_container flex-column">
        <div class="text-h5 font-weight-medium mt-8 mb-2">
          {{ $tr('project', 'key_364') }}
        </div>
        <div class="text-body-2 mb-12 text-center">
          {{ $tr('project', 'key_365') }}
        </div>
        <div class="w-100">
          <div
            v-for="(plan, index) in list"
            class="border d-flex pa-4 rounded justify-space-between align-center mb-3 cursor-pointer"
            :style="index === selected && `border-color: rgb(var(--v-theme-primary))!important`"
            @click="selected = index"
          >
            <div class="d-flex flex-column">
              <div v-if="plan.count !== 12" class="text-body-1">
                {{ plan.count }} {{ $tr('project', 'key_370') }}
              </div>
              <div v-else class="text-body-1">
                1 {{ $tr('project', 'key_373') }}
              </div>
              <div v-if="!plan.save" class="text-caption text-blue-grey-darken-1">
                {{ $tr('project', 'key_309') }}
              </div>
              <div v-else class="text-caption text-success">
                {{ $tr('project', 'key_310') }} {{ plan.save }} {{ $store.state.config.currency }}
              </div>
            </div>
            <div class="text-body-1 font-weight-bold">
              {{ plan.price }} {{ $store.state.config.currency }}
            </div>
          </div>
          <v-btn
            variant="flat"
            block
            color="primary"
            size="large"
            :loading="actionLoading"
            @click="getPayment"
          >
            {{ $tr('project', 'key_312') }}
          </v-btn>
        </div>
      </div>
    </div>
  </v-card>
</template>

<script>
import ScreenLock from "@/components/blocks/ScreenLock.vue";
import subscribeService from "@/services/subscribe/subscribe.service";

export default {
  name: 'SelectPlane',
  components: {ScreenLock},
  data: () => ({
    loading: true,
    list: [],
    selected: 0,
    actionLoading: false
  }),
  computed: {

  },
  watch: {

  },
  methods: {
    getAllPlans() {
      subscribeService.getPlans().then((res) => {
        this.list = res.data;
        this.loading = false;
      }).catch(e => {
        this.$store.commit('openSnackbar', e);
      })
    },
    getPayment() {
      this.actionLoading = true;
      subscribeService.getPaymentRoute().then((res) => {
        const data = res.data;
        this.startPayment(data.url);
      }).catch(e => {
        this.actionLoading = false;
        this.$store.commit('openSnackbar', e);
      });
    },
    startPayment(url) {
      const plan = this.list[this.selected];
      const link = `${url}?uid=${this.$route.params.uid}`
      subscribeService.createPayment(link, plan.id).then((res) => {
        const data = res.data;
        window.location.replace(data.url);
      }).catch(e => {
        this.actionLoading = false;
        this.$store.commit('openSnackbar', e);
      });
    }
  },
  beforeMount() {
    this.getAllPlans();
  }
};
</script>
