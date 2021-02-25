<template>
  <div class="row">
    <loading
      :active.sync="isLoading"
      color="#189DCD"
      :is-full-page="true"
      :width="64"
      :height="64"
    />
    <div class="col-md-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">
            Direct Queue Monitor
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 text-right">
              <a href="/cs/directQueue/create" class="btn btn-primary"
                >Create Direct Queue</a
              >
            </div>
          </div>
          <div class="row">
            <!-- START DIRECT QUEUE CALLER -->
            <div class="col-md-4">
              <b class="text-primary">Direct Queue Caller</b>
              <hr />
              <div class="row">
                <div class="col-md-12" v-if="!isOnTransfer">
                  <div class="form-group">
                    <label for="search-by">Input Queue No</label>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Input here"
                      v-model="selected_queue"
                    />
                  </div>
                </div>
                <div class="col-md-12" v-else>
                  <h5>Transfer Queue: {{ selected_queue }}</h5>
                  <form @submit.prevent="onSubmitTransfer">
                    <input
                      type="hidden"
                      name="queue_no"
                      v-model="selected_queue"
                    />
                    <div class="form-group">
                      <label for="services">select workstation service</label>
                      <select
                        name="workstation_service_id"
                        id="workstation_service_id"
                        class="form-control"
                      >
                        <option
                          v-for="workstationService in workstationServices"
                          :key="workstationService.id"
                          :value="workstationService.id"
                        >
                          {{ workstationService.service.name }}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <button
                            class="btn btn-danger fullwidth"
                            type="button"
                            @click="isOnTransfer = false"
                          >
                            CANCEL
                          </button>
                        </div>
                        <div class="col-md-6">
                          <button
                            class="btn btn-success fullwidth"
                            type="submit"
                          >
                            SAVE
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <template v-if="!isOnTransfer">
                  <div class="col-md-12" v-if="!isOnServed">
                    <button
                      class="btn btn-primary fullwidth mb-2"
                      @click="onServed"
                      :disabled="!selected_queue || selected_queue.status"
                    >
                      Served
                    </button>
                  </div>
                  <template v-else>
                    <div class="col-md-6">
                      <button
                        class="btn btn-info fullwidth mb-2"
                        @click="onRecall"
                        :disabled="
                          onServedQueue &&
                          onServedQueue.recall_count >= max_recall
                        "
                      >
                        RECALL
                      </button>
                    </div>
                    <div class="col-md-6">
                      <button
                        class="btn btn-success fullwidth mb-2"
                        @click="onEndServed"
                      >
                        END SERVED
                      </button>
                    </div>
                    <div class="col-md-6">
                      <button
                        class="btn btn-secondary fullwidth mb-2"
                        @click="onRequeue"
                        :disabled="
                          onServedQueue &&
                          onServedQueue.requeue_count >= max_requeue
                        "
                      >
                        REQUEUE
                      </button>
                    </div>
                    <div class="col-md-6">
                      <button
                        class="btn btn-danger fullwidth mb-2"
                        @click="onNoShow"
                      >
                        NO SHOW
                      </button>
                    </div>
                    <div class="col-md-12">
                      <button
                        class="btn btn-warning fullwidth mb-2"
                        @click="onTransfer"
                        :disabled="!allow_transfer"
                      >
                        TRANSFER
                      </button>
                    </div>
                  </template>
                </template>
              </div>
            </div>
            <!-- END DIRECT QUEUE CALLER -->
            <!-- START DIRECT QUEUE LIST -->
            <div class="col-md-8">
              <b class="text-primary">Direct Queue List</b>
              <hr />
              <!-- <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="search-by"
                                            >Search By Queue No or Name</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Search"
                                            @input="debounceSearch"
                                        />
                                    </div>
                                </div>
                            </div> -->
              <div class="row">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Queue No</th>
                      <th>Name</th>
                      <th>Service Name</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-if="queues.length > 0">
                      <tr
                        v-for="(queue, index) in queues"
                        :key="index"
                        @click="selectQueue(queue.queue_no)"
                        class="pointer"
                      >
                        <td>{{ queue.queue_no }}</td>
                        <td>{{ queue.name || "-" }}</td>
                        <td>
                          {{ queue.workstation_service.service.name }}
                        </td>
                        <td>
                          <span
                            class="badge badge-secondary"
                            v-show="queue.status == 'waiting'"
                            >Waiting</span
                          >
                          <span
                            class="badge badge-info"
                            v-show="queue.status == 'served'"
                            >On Served</span
                          >
                          <span
                            class="badge badge-warning"
                            v-show="queue.status == 'requeue'"
                            >Re-queue</span
                          >
                          <span
                            class="badge badge-danger"
                            v-show="queue.status == 'no show'"
                            >No Show</span
                          >
                          <span
                            class="badge badge-success"
                            v-show="queue.status == 'end served'"
                            >End Served</span
                          >
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="3">
                        <p class="text-center">No Data Found</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- END DIRECT QUEUE LIST -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Import component
import Vue from "vue";
// Loading component
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
// Toast component
import VueToast from "vue-toast-notification";
import "vue-toast-notification/dist/theme-sugar.css";

Vue.use(VueToast);

export default {
  components: {
    Loading,
  },
  props: {
    max_recall: {
      type: Number,
      default: 0,
    },
    max_requeue: {
      type: Number,
      default: 0,
    },
    allow_transfer: {
      type: Boolean,
      default: false,
    },
    auth: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isLoading: true,
      keyword: "",
      debounce: null,
      queues: [],
      selected_queue: "",
      isOnServed: false,
      onServedQueue: {},
      isOnTransfer: false,
      workstationServices: [],
    };
  },
  mounted() {
    this.getQueues();
  },
  created() {
    Echo.private(`event_direct_queue.${this.auth.id}`).listen(
      "VCTDirectQueue",
      (directQueues) => {
        this.$toast.open({
          message: "New queue created",
          type: "info",
          duration: 3000,
          dismissible: true,
        });
        this.getQueues();
      }
    );
  },
  methods: {
    async getQueues() {
      this.isLoading = true;
      const data = await axios.get(`/cs/directQueue?keyword=${this.keyword}`);
      this.queues = data.data.data;
      this.isLoading = false;
    },
    debounceSearch(event) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.keyword = event.target.value;
        this.getQueues();
      }, 500);
    },
    selectQueue(queue_no) {
      this.selected_queue = queue_no;
      this.onServed();
    },
    async onServed() {
      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      if (
        selected_queue[0]?.status != "waiting" &&
        selected_queue[0]?.status != "served" &&
        selected_queue[0]?.status != "requeue"
      ) {
        alert("Queue status incorrect");
        return;
      }
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onServed", {
          queue_no: this.selected_queue,
        });
        this.onServedQueue = queue.data.data;
        this.isOnServed = true;
        this.getQueues();
      } catch (error) {
        this.getQueues();
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onRecall() {
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onRecall", {
          queue_no: this.selected_queue,
        });
        this.onServedQueue = queue.data.data;
        if (this.onServedQueue.recall_count >= this.max_recall) {
          this.isOnServed = false;
        } else {
          this.isOnServed = true;
        }
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onEndServed() {
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onEndServed", {
          queue_no: this.selected_queue,
        });
        this.onServedQueue = queue.data.data;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onRequeue() {
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onRequeue", {
          queue_no: this.selected_queue,
        });
        this.onServedQueue = queue.data.data;
        if (this.onServedQueue.requeue_count >= this.max_requeue) {
          this.isOnServed = false;
        } else {
          this.isOnServed = true;
        }
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onNoShow() {
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onNoShow", {
          queue_no: this.selected_queue,
        });
        this.onServedQueue = queue.data.data;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onTransfer() {
      this.isLoading = true;
      try {
        const workstationServices = await axios.get(
          "/cs/directQueue/workstationServices"
        );
        this.workstationServices = workstationServices.data.data;
        this.isOnTransfer = true;
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onSubmitTransfer(e) {
      this.isLoading = true;
      try {
        const data = {
          queue_no: e.target.queue_no.value,
          workstation_service_id: e.target.workstation_service_id.value,
        };
        const queue = await axios.post("/cs/directQueue/onTransfer", data);
        this.isOnServed = false;
        this.isOnTransfer = false;
        this.selected_queue = "";
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
  },
  watch: {
    selected_queue() {
      this.isOnServed = false;
    },
  },
};
</script>

<style>
.pointer {
  cursor: pointer;
}
</style>
