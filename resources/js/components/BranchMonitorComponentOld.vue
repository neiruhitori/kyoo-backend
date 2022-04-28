<template>
  <div class="container-fluid">
    <loading
      :active.sync="isLoading"
      color="#189DCD"
      :is-full-page="true"
      :width="64"
      :height="64"
    />
    <div class="row mt-5">
      <div class="col-12 text-center">
        <img
          :src="`/storage/${branch.logo}`"
          alt="branch logo"
          class="branch-logo"
        />
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-4">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Counter</th>
                      <th>Customer</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(queue, index) in queues" :key="index">
                      <td>
                        <h3 class="text-primary">
                          {{ queue.queue_no }}
                        </h3>
                      </td>
                      <td>
                        <h3 class="text-warning">
                          {{ queue.workstation_service.workstation.display_id }}
                        </h3>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-8 text-center p-5">
                <img src="/img/logo-color.svg" alt="Kyoo logo" class="my-5" />
                <br />
                <a
                  href="https://play.google.com/store/apps/details?id=com.kyoo.android"
                  target="_blank"
                >
                  <img
                    src="/img/playstore.png"
                    alt=""
                    srcset=""
                    class="img-playstore"
                  />
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Import component
import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";

export default {
  components: {
    Loading,
  },
  props: {
    branch: {
      type: Object,
      required: true,
    },
    branch_id_encrypted: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      isLoading: false,
      queues: [],
    };
  },
  created() {
    Echo.channel(`event_direct_queue_general.${this.branch.id}`).listen(
      "DirectQueue",
      (directQueues) => {
        this.getQueues();
      }
    );
  },
  mounted() {
    this.getQueues();
  },
  methods: {
    async getQueues() {
      this.isLoading = true;
      try {
        const data = await axios.get(
          `/direct-queue/branch/${this.$props.branch_id_encrypted}/list`
        );
        this.queues = data.data.data;
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
  },
};
</script>

<style scoped>
.branch-logo {
  height: 100px;
  width: 100px;
}
</style>
