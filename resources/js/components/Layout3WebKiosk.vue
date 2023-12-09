<template>
    <div>
        <div v-if="isError" class="alert-error">
            <strong>Error:</strong> {{ errorMessage }}
        </div>
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
                <div class="gap-1">
                    <h2 class="label">Check-in</h2>
                    <span class="label">(Jika sudah membuat temu janji)</span>
                </div>
                <form
                    class="mb-4"
                    @submit.prevent="onSubmitBookingCode"
                >
                    <div style="display: flex; gap: 20px">
                        <textarea
                            class="form-control"
                            style="width: 15rem;"
                            v-model="formData.booking_code"
                            placeholder="Masukkan Kode Booking, contoh : XYBJLL"
                        >
                        </textarea>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            v-bind:style="[button_style]"
                        >
                            Check-in
                        </button>
                    </div>
                </form>
            </div>
            <div
                class="col-md-6 wrapper-center"
                v-bind:style="[secondary_background]"
            >
                <div class="wrapper-center" v-if="activeTab == 'menus'">
                    <div class="gap-1">
                        <h5 class="label">
                            Pilih Layanan
                        </h5>
                        <span class="label">
                            (Kunjungan langsung)
                        </span>
                    </div>
                    <template v-for="workstation in this.workstationServices">
                        <button
                            class="btn btn-lg btn-primary button-length"
                            v-if="workstation.service.is_show_webkiosk"
                            @click="onClickWorkstation(workstation.id)"
                            v-bind:style="[button_style]"
                        >
                            {{ workstation.service.name }}
                        </button>
                    </template>
                </div>

                <form
                    id="filterForm"
                    class="mb-4"
                    @submit.prevent="onSubmitForm"
                    v-if="activeTab == 'form'"
                >
                    <div class="form-row align-items-start wrapper-form">
                        <div>
                            <h5 class="label">Masukkan Informasi Antrian</h5>
                            <p style="font-size: 9px">Untuk Antrian WA wajib mengisi Nama dan No Whatsapp</p>
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
                        Silahkan Foto atau Cetak Nomer Antrian
                    </h5>
                    <div class="wrapper-success-card" style="margin-bottom: 25px;">
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

                    <button
                        class="btn btn-lg btn-primary button-length"
                        @click="print()"
                        v-bind:style="[button_style]"
                    >
                        Cetak
                    </button>
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
        is_allow_wa: {
            type: Boolean,
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
                vct_id: "",
                booking_code: ""
            },
            responseQueue: {},
            isError: false,
            errorMessage: "",
            errorTimeout: null,
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
                this.setError("Untuk Antrian WA wajib mengisi Nama dan No Whatsapp");
                return;
            }

            this.handleCreateOnsiteQueue(type);
        },
        onSubmitBookingCode() {
            if(this.formData.booking_code == "") {
                this.setError("Kode booking wajib diisi");
                return;
            }

            this.handleCreateOnsiteQueueByBookingCode();
        },
        onReset() {
            this.selectedWorkstation = null;
            this.selectedTypeQueue = null;
            this.activeTab = "menus";
            this.isError = false;
            this.errorMessage = "";
            this.errorTimeout = null;
            this.formData = {
                name: "",
                phone: "",
                workstation_service_id: "",
                vct_id: "",
                booking_code: ""
            }
        },
        handleCreateOnsiteQueueByBookingCode() {
            this.isLoading = true;

            this.formData["vct_id"] = this.auth.id;

            axios
                .post("/device/directQueueByBookingCode/store", this.formData)
                .then(response => {
                    this.responseQueue = response.data.data;

                    this.isLoading = false;
                    this.activeTab = "result";

                    if (this.activeTab == "result") {
                        setTimeout(() => {
                            this.onReset();
                        }, 20000);
                    }
                })
                .catch(error => {
                    this.isLoading = false;
                    this.setError("Kode booking tidak ditemukan");
                    console.error(error);
                });
        },
        handleCreateOnsiteQueue(type) {
            this.isLoading = true;

            this.formData["workstation_service_id"] = this.selectedWorkstation;
            this.formData["vct_id"] = this.auth.id;

            axios
                .post("/device/directQueue/store", this.formData)
                .then(response => {
                    this.responseQueue = response.data.data;

                    this.isLoading = false;
                    this.activeTab = "result";
                    if(type == "print") {
                        this.print();
                    }

                    if (this.activeTab == "result") {
                        setTimeout(() => {
                            this.onReset();
                        }, 20000);
                    }
                })
                .catch(error => {
                    this.isLoading = false;
                    this.setError("Mohon Maaf, Tidak Bisa Mengambil Antrian");
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
        },
        setError(message) {
            this.isError = true;
            this.errorMessage = message;

            if (this.errorTimeout) {
                clearTimeout(this.errorTimeout);
            }

            this.errorTimeout = setTimeout(() => {
                this.isError = false;
                this.errorMessage = "";
            }, 3000);
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

.gap-1 {
    gap: 1px !important;
}

textarea{
    resize: none;
    text-align: center;
}
textarea::placeholder{
    text-align: center;
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

.alert-error {
  padding: 10px;
  border: 1px solid;
  border-radius: 4px;
  margin-top: 10px;
  position: fixed;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  width: 80%;
  max-width: 500px; /* Adjust the width as needed */
  color: #721c24;
  background-color: #f8d7da;
  border-color: #f5c6cb;
}
</style>
