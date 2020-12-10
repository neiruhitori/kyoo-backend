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
                        <div class="col-md-4">
                            waiting
                        </div>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="queue in queues"
                                            :key="queue.id"
                                        >
                                            <td>{{ queue.queue_no }}</td>
                                            <td>{{ queue.name }}</td>
                                            <td>{{ queue.service.name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
        Loading
    },
    data() {
        return {
            isLoading: true,
            keyword: "",
            debounce: null,
            queues: []
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
        }
    }
};
</script>

<style></style>
