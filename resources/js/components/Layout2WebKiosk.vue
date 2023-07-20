<template>
    <div class="row monitor-container">
        <loading
            :active.sync="isLoading"
            color="#189DCD"
            :is-full-page="true"
            :width="64"
            :height="64"
        />
        <div
            class="col-md-6 wrapper-center"
            v-bind:style="[primary_background]"
        >
            <h2 class="label">Scan Kode QR</h2>
            <img v-bind:src="qr" class="qr-image" alt="qr-code" width="250" />
        </div>
        <div
            class="col-md-6 wrapper-center"
            v-bind:style="[secondary_background]"
        >
            <div class="wrapper-center" v-if="activeTab == 'menus'">
                <h5 class="label">
                    Silahkan Ambil Antrian
                </h5>
                <button
                    class="btn btn-lg btn-primary button-length"
                    v-for="workstation in this.workstationServices"
                    @click="onClickWorkstation(workstation.id)"
                    v-bind:style="[button_style]"
                >
                    {{ workstation.service.name }}
                </button>
            </div>

            <form
                id="filterForm"
                class="mb-4"
                @submit.prevent="onSubmitForm"
                v-if="activeTab == 'form'"
            >
                <div class="form-row align-items-start wrapper-form">
                    <div>
                        <h5 class="label">Kirim Nomor Antrian ke WA</h5>
                        <p :style="isError ? {'font-size': '9px', 'color': 'red'} : {'font-size': '9px'}">Untuk Antrian WA wajib mengisi Nama dan No Whatsapp</p>
                    </div>
                    <div class="col-12">
                        <label for="name">Nama</label>
                        <input
                            type="text"
                            class="form-control"
                            style="width: 15rem;"
                            v-model="formData.name"
                        />
                    </div>
                    <div class="col-12">
                        <label for="phone">No Whatsapp</label>
                        <input
                            type="tel"
                            class="form-control"
                            style="width: 15rem;"
                            v-model="formData.phone"
                        />
                    </div>
                    <div class="col-12">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            v-bind:style="[button_style]"
                        >
                            Ambil Antrian
                        </button>
                    </div>
                </div>
            </form>

            <div class="wrapper-center" v-if="activeTab == 'type_queue'" id="action-section">
                <button
                    class="btn btn-lg btn-primary button-length"
                    @click="onClickQueueType('wa')"
                    v-bind:style="[button_style]"
                    v-if="this.active_menus.includes('wa') && this.is_allow_wa"
                >
                    Kirim Nomor Antrian Ke WA
                </button>

                <button
                    class="btn btn-lg btn-primary button-length"
                    @click="onClickQueueType('foto')"
                    v-bind:style="[button_style]"
                    v-if="this.active_menus.includes('photo')"
                >
                    Foto Nomor Antrian
                </button>

                <button
                    class="btn btn-lg btn-primary button-length"
                    @click="onClickQueueType('print')"
                    v-bind:style="[button_style]"
                    v-if="this.active_menus.includes('print')"
                >
                    Cetak
                </button>
            </div>

            <div v-if="activeTab == 'result'" id="result-section">
                <h5 class="label" style="margin-bottom: 25px;">
                    Silahkan Foto Nomer Antrian
                </h5>
                <div class="wrapper-success-card">
                    <div class="wrapper-queue-number">
                        <span style="font-size: 1rem;color: rgb(122, 122, 122);margin-bottom: 0.5rem;text-align: center;">Nomor Antrian</span>
                        <h2 style="font-weight: 700;font-size: 3.625rem;color: rgb(16, 60, 124);text-align: center;">
                            {{ responseQueue.queue_no ? responseQueue.queue_no : "0000"}}
                        </h2>
                    </div>
                    <div class="seprator" />
                    <div class="wrapper-service-card">
                        <h4> {{ workstationServices.find((service) => service.id == responseQueue.workstation_service_id).service.name }} </h4>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="powered-by">
            <span>Powered by</span>
            <img src="/img/logo-color.svg" alt="logo-kyoo" height="22"/>
        </div>
        <div
            class="reset-button"
            v-bind:style="[button_style]"
            @click="onReset()"
        >
            <i class="fas fa-undo"></i>
        </div>
    </div>
</template>

<script>
// Import component
import Vue from "vue";
// Loading component
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";

export default {
    components: {
        Loading
    },
    props: {
        branch: {
            type: Object,
            required: true
        },
        auth: {
            type: Object,
            required: true
        },
        layout_config: {
            type: Object,
            required: true
        },
        qr: {
            type: String,
            required: true
        },
        is_allow_wa: {
            type: Number,
            required: true
        },
        active_menus: {
            type: Array,
            required: true
        }
    },

    async mounted() {
        await this.getWorkStations();
    },

    data() {
        return {
            isLoading: true,
            branch_logo: this.branch
                ? `/storage/${this.branch.logo}`
                : `/img/logo-color.svg`,
            workstationServices: [],
            activeTab: "menus",
            selectedWorkstation: null,
            selectedTypeQueue: null,
            primary_background:
                this.layout_config.primary_background_type == "color"
                    ? {
                          "background-color": this.layout_config
                              .primary_background_color
                      }
                    : {
                          "background-image": `url('/storage/${this.layout_config.primary_background_image}')`,
                          "background-repeat": "no-repeat",
                          "background-size": "cover"
                      },
            secondary_background:
                this.layout_config.secondary_background_type == "color"
                    ? {
                          "background-color": this.layout_config
                              .secondary_background_color
                      }
                    : {
                          "background-image": `url('/storage/${this.layout_config.secondary_background_image}')`,
                          "background-repeat": "no-repeat",
                          "background-size": "cover"
                      },
            button_style: {
                color: this.layout_config.font_color || "#fafcff",
                "background-color":
                    this.layout_config.button_background_color || "#114077",
                "border-color":
                    this.layout_config.botton_border_color || "#fafcff"
            },
            label_style: {
                color: this.layout_config.font_color || "#fafcff"
            },
            formData: {
                name: "",
                phone: "",
                workstation_service_id: "",
                vct_id: ""
            },
            responseQueue: {},
            isError: ""
        };
    },
    methods: {
        async getWorkStations() {
            this.isLoading = true;

            try {
                const workstationServices = await axios.get(
                    "/device/directQueue/allWorkstationServices"
                );

                this.workstationServices =
                    workstationServices.status == 200
                        ? workstationServices.data.data
                        : [];
            } catch (error) {
                alert(error.response.data.message);
            }

            this.isLoading = false;
        },
        onClickWorkstation(id) {
            this.selectedWorkstation = id;
            this.activeTab = "form";
        },
        onSubmitForm() {
            this.activeTab = "type_queue";
        },
        onClickQueueType(type) {
            if(type == "wa" && (this.formData.name =="" || this.formData.phone == "")) {
                this.activeTab = "form";
                this.isError = true;
                return;
            }

            this.handleCreateOnsiteQueue(type);
        },
        onReset() {
            this.selectedWorkstation = null;
            this.selectedTypeQueue = null;
            this.activeTab = "menus";
            this.isError = false;
            this.formData = {
                name: "",
                phone: "",
                workstation_service_id: "",
                vct_id: ""
            }
        },
        handleCreateOnsiteQueue(type) {
            this.formData["workstation_service_id"] = this.selectedWorkstation;
            this.formData["vct_id"] = this.auth.id;

            axios
                .post("/device/directQueue/store", this.formData)
                .then(response => {
                    this.responseQueue = response.data.data;

                    this.activeTab = "result";
                    if(type =="print") {
                        this.print();
                    }
                })
                .catch(error => {
                    alert("Tolong kontak admin untuk tindakan lebih lanjut");
                    console.error(error);
                });
        },
        print() {
            const printWindow = window.open('', '_blank');

            const serviceNumber = this.responseQueue.queue_no;
            const serviceName = this.workstationServices.find((service) => service.id == this.responseQueue.workstation_service_id).service.name;
            const branchName = this.branch.name;
            const createdAt = moment(this.responseQueue.created_at).format('DD/MM/YYYY HH:mm:ss')

            printWindow.document.open();
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                <title>Print</title>
                <style>
                    @page {
                            size: 80mm 80mm;
                    }
                    body {
                        width: 80mm;
                        height: 80mm;
                        margin: 0;
                        display: inline-grid;
                        justify-content: center;
                        align-items: center;
                        text-align: center;
                    }
                    </style>
                </head>
                <body>
                    <span style="font-size: 24px; font-style: normal; font-weight: 500; line-height: normal;">${branchName}</span>
                    <span style="font-size: 64px; font-style: normal; font-weight: 800; line-height: normal;">${serviceNumber}</span>
                    <div style="display: grid;gap: 10px;">
                        <span style="font-size: 20px; font-style: normal; font-weight: 400; line-height: normal;">${serviceName}</span>
                        <span style="font-size: 12px; font-style: normal; font-weight: 400; line-height: normal;">${createdAt}</span>
                    </div>
                </body>
                </html>
            `);

            // Wait for the content to load
            printWindow.onload = function () {
                printWindow.print();
                printWindow.close();
            };

            printWindow.document.close();
        }
    }
};
</script>

<style scoped>
.monitor-container {
    display: flex;
    justify-content: center;
    text-align: center;
    font-family: sans-serif;
    height: 100vh;
}

.wrapper-center {
    display: grid;
    align-content: center;
    justify-content: center;
    justify-items: center;
    gap: 20px;
}

.qr-image {
    border: 4px solid black;
    border-radius: 15px;
}
.wrapper-form {
    display: grid;
    text-align: initial;
    gap: 20px;
}

.button-length {
    width: 15rem;
}

.reset-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    text-align: center;
    line-height: 50px;
    cursor: pointer;
}

.reset-button i {
    font-size: 15px;
}

.label {
    color: black;
}

.wrapper-success-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    filter: drop-shadow(rgba(0, 0, 0, 0.1) 0px 7px 40px);
    background-color: white;
    border-radius: 12px;
}
.wrapper-queue-number {
    display: flex;
    align-items: center;
    flex-direction: column;
    padding: 1.75rem;
}
.wrapper-service-card {
    display: flex;
    padding: 1.75rem;
    justify-content: center;
}
.seprator {
    border-top: 2px dashed rgb(144, 183, 241);
    width: 100%;
}

.powered-by {
    font-size: 0.875rem;
    color: white;
    margin-top: auto;
    position: fixed;
    bottom: 10px;
}
</style>
