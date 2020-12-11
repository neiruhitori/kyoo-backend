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
                        <!-- START DIRECT QUEUE CALLER -->
                        <div class="col-md-4">
                            <b class="text-primary">Direct Queue Caller</b>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="search-by"
                                            >Input Queue No</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Input here"
                                            v-model="selected_queue"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="!isOnCall">
                                    <button
                                        class="btn btn-primary fullwidth mb-2"
                                        @click="onCall"
                                        :disabled="!selected_queue"
                                    >
                                        CALL
                                    </button>
                                </div>
                                <template v-else>
                                    <div class="col-md-6">
                                        <button
                                            class="btn btn-info fullwidth mb-2"
                                            @click="onRecall"
                                        >
                                            RECALL
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button
                                            class="btn btn-success fullwidth mb-2"
                                            @click="onDone"
                                        >
                                            DONE
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button
                                            class="btn btn-secondary fullwidth mb-2"
                                            @click="onRequeue"
                                        >
                                            REQUEUE
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button
                                            class="btn btn-danger fullwidth mb-2"
                                            @click="onUnattend"
                                        >
                                            UNATTEND
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        <button
                                            class="btn btn-warning fullwidth mb-2"
                                            @click="onTransfer"
                                        >
                                            TRANSFER
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <!-- END DIRECT QUEUE CALLER -->
                        <!-- START DIRECT QUEUE LIST -->
                        <div class="col-md-8">
                            <b class="text-primary">Direct Queue List</b>
                            <hr />
                            <div class="row">
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
                            </div>
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
                                                v-for="queue in queues"
                                                :key="queue.id"
                                                @click="
                                                    selectQueue(queue.queue_no)
                                                "
                                                class="pointer"
                                            >
                                                <td>{{ queue.queue_no }}</td>
                                                <td>{{ queue.name }}</td>
                                                <td>
                                                    {{ queue.service.name }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary"
                                                        v-show="
                                                            queue.status ==
                                                                'waiting'
                                                        "
                                                        >Waiting</span
                                                    >
                                                    <span
                                                        class="badge badge-info"
                                                        v-show="
                                                            queue.status ==
                                                                'call'
                                                        "
                                                        >On Call</span
                                                    >
                                                    <span
                                                        class="badge badge-warning"
                                                        v-show="
                                                            queue.status ==
                                                                'requeue'
                                                        "
                                                        >Re-queue</span
                                                    >
                                                    <span
                                                        class="badge badge-danger"
                                                        v-show="
                                                            queue.status ==
                                                                'unattend'
                                                        "
                                                        >Unattend</span
                                                    >
                                                    <span
                                                        class="badge badge-success"
                                                        v-show="
                                                            queue.status ==
                                                                'done'
                                                        "
                                                        >Done</span
                                                    >
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else>
                                            <td colspan="3">
                                                <p class="text-center">
                                                    No Data Found
                                                </p>
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
import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";

export default {
    components: {
        Loading
    },
    data() {
        return {
            isLoading: true,
            keyword: "",
            debounce: null,
            queues: [],
            selected_queue: "",
            isOnCall: false
        };
    },
    mounted() {
        this.getQueues();
    },
    methods: {
        async getQueues() {
            this.isLoading = true;
            const data = await axios.get(
                `/cs/directQueue?keyword=${this.keyword}`
            );
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
        },
        async onCall() {
            this.isLoading = true;
            try {
                await axios.post("/cs/directQueue/onCall", {
                    queue_no: this.selected_queue
                });
                this.isOnCall = true;
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        async onRecall() {
            this.isLoading = true;
            try {
                await axios.post("/cs/directQueue/onRecall", {
                    queue_no: this.selected_queue
                });
                this.isOnCall = false;
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        async onDone() {
            this.isLoading = true;
            try {
                await axios.post("/cs/directQueue/onDone", {
                    queue_no: this.selected_queue
                });
                this.isOnCall = false;
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        async onRequeue() {
            this.isLoading = true;
            try {
                await axios.post("/cs/directQueue/onRequeue", {
                    queue_no: this.selected_queue
                });
                this.isOnCall = false;
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        async onUnattend() {
            this.isLoading = true;
            try {
                await axios.post("/cs/directQueue/onUnattend", {
                    queue_no: this.selected_queue
                });
                this.isOnCall = false;
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        onTransfer() {
            alert("on onTransfer");
            this.isOnCall = false;
        }
    },
    watch: {
        selected_queue() {
            this.isOnCall = false;
        }
    }
};
</script>

<style>
.pointer {
    cursor: pointer;
}
</style>
