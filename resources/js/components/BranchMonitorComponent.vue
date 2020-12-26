<template>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-12 text-center">
                <img src="/img/logo.svg" alt="branch logo" />
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
                                        <tr
                                            v-for="(queue, index) in queues"
                                            :key="index"
                                        >
                                            <td>
                                                <h3 class="text-primary">
                                                    {{ queue.queue_no }}
                                                </h3>
                                            </td>
                                            <td>
                                                <h3 class="text-warning">
                                                    {{
                                                        queue
                                                            .workstation_service
                                                            .workstation
                                                            .display_id
                                                    }}
                                                </h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-8 text-center p-5">
                                <img
                                    src="/img/logo-color.svg"
                                    alt="Kyoo logo"
                                    class="my-5"
                                />
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
export default {
    props: {
        branch_id: {
            type: Number,
            required: true
        },
        branch_id_encrypted: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            queues: []
        };
    },
    async mounted() {
        const data = await axios.get(
            `/direct-queue/branch/${this.$props.branch_id_encrypted}/list`
        );
        this.queues = data.data.data;
    }
};
</script>

<style></style>
