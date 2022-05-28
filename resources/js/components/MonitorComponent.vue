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
            Monitor Antrian Onsite
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 text-right">
              <a
                href="/cs/directQueue/create"
                class="btn btn-primary"
              >
                Daftar Antrian Onsite
              </a>
            </div>
          </div>
          <div class="row">
            <!-- START DIRECT QUEUE CALLER -->
            <div class="col-md-4">
              <div class="mb-5" v-if="auth.branch.branch_type.is_premium">
                <p class="text-primary font-weight-bold mb-0">Waktu Layanan</p>
                <hr class="mt-2 mb-2">

                <div class="d-flex justify-content-between" style="max-width: 160px; gap: 8px;">
                  <div class="text-center">
                    <h4 class="h3 font-weight-bold mb-0 text-dark">{{ clock.hours || '00' }}</h4>
                    <span>Jam</span>
                  </div>
                  <div class="text-center">
                    <h4 class="h3 font-weight-bold mb-0 text-dark">{{ clock.minutes || '00' }}</h4>
                    <span>Menit</span>
                  </div>
                  <div class="text-center">
                    <h4 class="h3 font-weight-bold mb-0 text-dark">{{ clock.seconds || '00' }}</h4>
                    <span>Detik</span>
                  </div>
                </div>
              </div>

              <b class="text-primary">Pemanggil Antrian Onsite</b>
              <hr class="mt-2 mb-2">
              <div class="row">
                <div class="col-md-12" v-if="!isOnTransfer">
                  <div class="form-group">
                    <label for="search-by">Masukkan No. Antrian</label>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Ketik Disini"
                      v-model.lazy="selected_queue"
                      @change="manualInput = true"
                    />
                  </div>
                </div>
                <div class="col-md-12" v-else>
                  <h5>Transfer Antrian: {{ selected_queue }}</h5>
                  <form @submit.prevent="onSubmitTransfer">
                    <input
                      type="hidden"
                      name="queue_no"
                      v-model="selected_queue"
                    />
                    <div class="form-group">
                      <label for="services">Pilih Layanan di Meja</label>
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
                            Batal
                          </button>
                        </div>
                        <div class="col-md-6">
                          <button
                            class="btn btn-success fullwidth"
                            type="submit"
                          >
                            Simpan
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
                      Layani
                    </button>
                  </div>

                  <template v-else>
                      <div class="col-md-6">
                        <button
                          class="btn btn-info fullwidth mb-2"
                          @click="onRecall"
                        >
                          Panggil Ulang
                        </button>
                      </div>
                      <div class="col-md-6">
                        <button
                          class="btn btn-success fullwidth mb-2"
                          @click="onEndServed"
                          :disabled="recallCounter > max_recall"
                        >
                          Layanan Berakhir
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
                          Antri Ulang
                        </button>
                      </div>
                      <div class="col-md-6">
                        <button
                          class="btn btn-danger fullwidth mb-2"
                          @click="onNoShow"
                        >
                          Tidak Hadir
                        </button>
                      </div>
                      <div class="col-md-12">
                        <button
                          class="btn btn-warning fullwidth mb-2"
                          @click="onTransfer"
                          :disabled="!allow_transfer"
                        >
                          Transfer Antrian
                        </button>
                      </div>
                    </template>
                </template>
              </div>
            </div>
            <!-- END DIRECT QUEUE CALLER -->
            <!-- START DIRECT QUEUE LIST -->
            <div class="col-md-8">
              <b class="text-primary">Daftar Antrian Onsite</b>
              <hr class="mt-2 mb-2">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-8">
                        <label
                          for="search-by"
                        >
                          Cari menggunakan No. Antrian atau Nama
                        </label>
                      </div>
                      <div class="col-md-4">
                        <input
                          type="text"
                          class="form-control"
                          placeholder="Cari"
                          @input="debounceSearch"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <table class="table">
                  <thead>
                    <tr>
                      <th>No. Antrian</th>
                      <th>Kode Unik</th>
                      <th>Nama</th>
                      <th>Nama Layanan</th>
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
                        <td>{{ queue.booking_code.toUpperCase() }}</td>
                        <td>{{ queue.name || "-" }}</td>
                        <td>
                          {{ queue.service.name }}
                        </td>
                        <td>
                          <span
                            class="badge badge-secondary"
                            v-show="queue.status == 'waiting'"
                            >Menunggu</span
                          >
                          <span
                            class="badge badge-info"
                            v-show="queue.status == 'served'"
                            >Sedang Dilayani</span
                          >
                          <span
                            class="badge badge-warning"
                            v-show="queue.status == 'requeue'"
                            >Antri Ulang</span
                          >
                          <span
                            class="badge badge-danger"
                            v-show="queue.status == 'no show'"
                            >Tidak Hadir</span
                          >
                          <span
                            class="badge badge-success"
                            v-show="queue.status == 'end served'"
                            >Layanan Selesai</span
                          >
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="3">
                        <p class="text-center">Tidak Ada Data</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- END DIRECT QUEUE LIST -->
          </div>
        </div>
        <div class="card-footer text-muted">v1.0.2</div>
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
      recallCounter: 0,
      queues: [],
      selected_queue: "",
      isOnServed: false,
      onServedQueue: {},
      isOnTransfer: false,
      workstationServices: [],
      manualInput: false,
      clock: {
        hours: 0,
        minutes: 0,
        seconds: 0
      },
      timer: 0,
      timerInterval: null
    };
  },
  async mounted() {
    await this.getQueues();

    const [selected_queue] = this.queues.filter(v => v.status === 'served');
    if (selected_queue) {
      this.selectQueue(selected_queue.queue_no);
      this.timer = Math.floor((Date.now() - new Date(selected_queue.called_at)) / 1000)
      this.startTimer()
    }
  },
  created() {
    Echo.private(`event_direct_queue.${this.auth.branch_id}`).listen(
      "VCTDirectQueue",
      (data) => {
        this.$toast.open({
          message: "Antrian baru ditambahkan",
          type: "info",
          duration: 3000,
          dismissible: true,
        });
        this.getQueues();
      }
    );
  },
  methods: {
    startTimer() {
      this.timerInterval = setInterval(() => {
        this.timer++

        const seconds = Math.floor(this.timer % 3600 % 60)
        const minutes = Math.floor(this.timer % 3600 / 60)
        const hours = Math.floor(this.timer / 3600)

        this.clock.seconds = seconds < 10 ? '0' + seconds : seconds
        this.clock.minutes = minutes < 10 ? '0' + minutes : minutes
        this.clock.hours = hours < 10 ? '0' + hours : hours
      }, 1000)
    },

    resetTimer() {
      clearInterval(this.timerInterval)
      this.timer = 0
      this.clock.hours = 0
      this.clock.minutes = 0
      this.clock.seconds = 0
    },

    async getQueues() {
      this.isLoading = true;
      const data = await axios.get(`/cs/directQueue?keyword=${this.keyword}`);
      this.queues = data.data.data;

      this.isLoading = false;

      this.selectQueue(this.queues.length ? this.queues[0].queue_no : '');
    },

    debounceSearch(event) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.keyword = event.target.value;
        this.getQueues();
      }, 500);
    },

    selectQueue(queue_no) {
      this.manualInput = false;
      this.selected_queue = queue_no;

      const current_queue = this.queues.filter(v => v.queue_no === queue_no)[0] || null
      if (current_queue && current_queue.status === 'served') {
        this.isOnServed = true
        this.onServedQueue = current_queue
      }
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
        alert("Status antrian salah");
        return;
      }

      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onServed", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id,
          is_skip: this.manualInput,
        });

        this.onServedQueue = queue.data.data;
        this.isOnServed = true;

        this.getQueues();

        this.startTimer()
      } catch (error) {
        this.getQueues();
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
  
    async onRecall() {
      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onRecall", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id
        });
        this.onServedQueue = queue.data.data;

        await this.getQueues();

        if (this.onServedQueue.recall_count >= this.max_recall) {
          this.selected_queue = this.queues[0]?.queue_no || "";
          this.isOnServed = false;
        } else {
          this.isOnServed = true;
        }
        this.recalCounter++;
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },

    async onEndServed() {
      this.resetTimer()

      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onEndServed", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id
        });
        this.onServedQueue = null;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
    async onRequeue() {
      this.resetTimer()

      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onRequeue", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id
        });
        if (this.onServedQueue.requeue_count >= this.max_requeue) {
          this.isOnServed = false;
        } else {
          this.isOnServed = true;
        }
        this.onServedQueue = null;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        console.log({ error });
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },
  
    async onNoShow() {
      this.resetTimer()

      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onNoShow", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id
        });
        this.onServedQueue = null;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        console.log({ error });
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
      this.resetTimer()

      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === e.target.queue_no.value
      );
      this.isLoading = true;
      try {
        const data = {
          queue_no: e.target.queue_no.value,
          workstation_service_id: e.target.workstation_service_id.value,
          service_id: selected_queue[0].service_id
        };
        await axios.post("/cs/directQueue/onTransfer", data);
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
    selected_queue(next) {
      if (next != this.onServedQueue?.queue_no) {
        this.isOnServed = false;
      }
    },
  },
};
</script>

<style>
.pointer {
  cursor: pointer;
}
</style>
