<template>
    <div style="overflow: hidden;">
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
                class="col-md-12"
                v-bind:style="[background]"
            >
                <div class="d-flex justify-content-center w-full" style="height: 100%">
                    <div class="col-md-6 d-flex justify-content-between flex-column align-items-center">
                        <div class="col-md-10 my-5">
                            <div class="my-5" style="font-family: 'Manrope', sans-serif;">
                                <h2 class="label">Check-in</h2>
                                <span class="label">(Jika sudah membuat temu janji)</span>
                            </div>
                            <form
                                class="mt-4 col"
                                @submit.prevent="onSubmitBookingCode"
                            >
                                <label for="booking_code">
                                    <div class="d-flex justify-content-between p-3 bg-light rounded w-full">
                                        <div class="d-flex flex-column align-items-start mr-4">
                                            <label class="text-success" style="font-size: 12px;">Masukkan Kode Booking</label>
                                            <input
                                                class="form-control p-0"
                                                style="width: 15rem; height: 30px; font-size: 18px; border: 0; background: transparent;"
                                                v-model="formData.booking_code"
                                                id="booking_code"
                                                placeholder="XYBJLL"
                                            />
                                        </div>
                                        <button
                                            type="submit"
                                            class="btn px-4"
                                            v-bind:style="[button_checkin_style]"
                                            style="font-family: 'Manrope', sans-serif;"
                                        >
                                            Check-in
                                        </button>
                                    </div>
                                </label>
                                <div v-if="isErrorBooking" class="text-white d-flex justify-content-center" style="font-size: 12px;" :style="{'margin': errorMessage === 'Kode booking wajib diisi' ? '0 280px 0 0' : '0 248px 0 0'}">
                                    {{ errorMessage }}
                                </div>
                            </form>
                        </div>
                        <div class="powered-by mb-3">
                            <img v-bind:src="webkiosk_logo" alt="logo-kyoo" height="60"/>
                        </div>
                    </div>

                    <div class="col-md-5" v-if="activeTab == 'menus'">
                        <div class="border border-light m-3 pb-5" style="height: 95%; border-radius: 20px;">
                            <div class="d-flex flex-column align-items-center" style="height: 100%">
                                <div class="my-5" style="font-family: 'Manrope', sans-serif;">
                                    <h5 class="label">
                                        Pilih Layanan
                                    </h5>
                                    <span class="label">
                                        (Kunjungan langsung)
                                    </span>
                                </div>
                                <div class="col px-5 overflow-auto">
                                    <template v-for="workstation in this.workstationServices">
                                        <button
                                            class="btn btn-lg d-block py-3 mb-3"
                                            style="width: 100%; height: 85px; font-family: 'Manrope', sans-serif;"
                                            v-if="workstation.service.is_show_webkiosk"
                                            @click="onClickWorkstation(workstation.id)"
                                            v-bind:style="[button_style]"
                                        >
                                            {{ workstation.service.name }}
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5" v-if="activeTab == 'result'">
                        <div class="border border-light m-3" style="height: 95%; border-radius: 20px;">
                            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 100%; font-family: 'Manrope', sans-serif;">
                                <div id="result-section">
                                    <h5 class="label" style="margin-bottom: 15px; margin-top: 5px;">
                                        Silahkan Foto atau Cetak
                                        <br>
                                        Nomer Antrian
                                    </h5>
                                    <div style="margin-bottom: 25px; position: relative;">
                                        <div class="d-flex flex-column align-items-center justify-content-center" style="position: absolute; width: 100%;">
                                            <div style="padding-top: 20.38px;">
                                                <img v-bind:src="branch_logo" alt="logo-kyoo" height="26"/>
                                            </div>
                                            <div class="wrapper-queue-number">
                                                <span style="font-size: 20px; color: #132D58;margin-bottom: 0.5rem;text-align: center; font-weight: bolder;">Nomor Antrian Anda</span>
                                                <h2 style="font-weight: bold;font-size: 64px;color: #21965E;text-align: center;">
                                                    {{ responseQueue.queue_no ? responseQueue.queue_no : "0000"}}
                                                </h2>
                                                <span style="font-size: 12px;color: rgb(122, 122, 122);margin-bottom: 0.5rem;text-align: center;">Mohon tunggu sampai nomor <br> antrian Anda dipanggil</span>
                                            </div>
                                            <div class="wrapper-service-card">
                                                <h4 style="font-weight: bold;font-size: 16px;color: #132D58;text-align: center;"> {{ workstationServices.find((service) => service.id == responseQueue.workstation_service_id).service.name }} </h4>
                                                <span style="font-size: 12px;color: #132D58;margin-bottom: 0.1rem;text-align: center; font-weight: 600;">{{ currentDay }}, {{ currentFormattedDate }}</span>
                                                <span style="font-size: 12px;color: #132D58;margin-bottom: 0.1rem;text-align: center; font-weight: 600;">{{ currentTimes }} {{ branch.timezone }}</span>
                                                <span style="font-size: 12px;color: #132D58;margin-bottom: 0.1rem;text-align: center; font-weight: 600;">Sisa Antrian : {{ responseQueue.total_waiting }}</span>
                                            </div>
                                        </div>
                                        <svg width="263" height="403" viewBox="0 0 263 403" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 15C0 6.71573 6.71573 0 15 0H248C256.284 0 263 6.71573 263 15V244.493H0V15Z" fill="#F9FAFB"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0 281.013H11.4585V262.513V244.013H-1.90735e-06C7.01681 248.491 11.4585 255.118 11.4585 262.513C11.4585 269.908 7.01681 276.535 0 281.013Z" fill="#F9FAFB"/>
                                        <rect width="240.083" height="37" transform="matrix(1 0 0 -1 11.4585 281.013)" fill="#F9FAFB"/>
                                        <line y1="-1" x2="240.083" y2="-1" transform="matrix(1 0 0 -1 11.4585 262.513)" stroke="#999999" stroke-width="2" stroke-dasharray="8 8"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M263 281.013H251.542V262.513V244.013H263C255.983 248.491 251.542 255.118 251.542 262.513C251.542 269.908 255.983 276.535 263 281.013Z" fill="#F9FAFB"/>
                                        <path d="M0 387.293C0 395.577 6.71573 402.293 15 402.293H248C256.284 402.293 263 395.577 263 387.293V280.533H0V387.293Z" fill="#F9FAFB"/>
                                        </svg>
                                    </div>

                                    <button
                                        class="btn button-print"
                                        @click="print()"
                                        v-bind:style="[button_style]"
                                        style="font-family: 'Manrope', sans-serif;"
                                    >
                                        Cetak
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
import { format } from "date-fns";
import { id } from "date-fns/locale";
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
        this.initCurrentDate();
        this.getAuth();
        await this.getWorkStations();
        this.updateCurrentDate();
    },

    computed: {
        currentTimes() {
            return format(this.currentDate, "HH:mm", {
                locale: id,
            });
        },

        currentDay() {
            return format(this.currentDate, "EEEE", {
                locale: id,
            });
        },

        currentFormattedDate() {
            return format(this.currentDate, "dd MMMM yyyy", {
                locale: id,
            });
        },
    },

    data() {
        return {
            isLoading: true,
            auth: null,
            branch_logo: this.layout_config.ticket_logo
                ? `/storage/${this.layout_config.ticket_logo}`
                : `/img/logo-color.svg`,
            webkiosk_logo: this.layout_config.logo
                ? `/storage/${this.layout_config.logo}`
                : `/img/logo-color.svg`,
            workstationServices: [],
            activeTab: "menus",
            selectedWorkstation: null,
            selectedTypeQueue: null,
            background:
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
            button_style: {
                color: this.layout_config.font_color || "#fafcff",
                "background-color":
                    this.layout_config.button_background_color || "#114077",
                "border-color":
                    this.layout_config.botton_border_color || "#fafcff",
                "font-weight": "700"
            },
            button_checkin_style: {
                color: this.layout_config.font_checkin_color || "#fafcff",
                "background-color":
                    this.layout_config.button_checkin_background_color || "#114077",
                "border-color":
                    this.layout_config.button_checkin_border_color || "#fafcff"
            },
            label_style: {
                color: this.layout_config.font_color || "#fafcff"
            },
            formData: {
                name: "",
                phone: "",
                workstation_service_id: "",
                vct_id: "",
                booking_code: "",
                user_id: ""
            },
            responseQueue: {},
            isError: false,
            isErrorBooking: false,
            errorMessage: "",
            errorTimeout: null,
            currentDate: new Date(),
        };
    },
    methods: {
        initCurrentDate() {
            let currentDate = new Date();
            const branchTimeZone = this.branch.timezone;

            let timezone = 7;
            if(branchTimeZone === 'WITA') {
                timezone = 8;
            } else if (branchTimeZone === 'WIT') {
                timezone = 9;
            }
            currentDate.setUTCHours(timezone);

            this.currentDate = currentDate;
        },
        getAuth() {
            this.auth = JSON.parse(localStorage.getItem('auth'));
            this.formData["user_id"] = this.auth.id;
        },
        async getWorkStations() {
            this.isLoading = true;

            try {
                const workstationServices = await axios.get(
                    `/device/directQueue/allWorkstationServices/${this.branch.id}`
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
            this.handleCreateOnsiteQueue();
        },
        onSubmitBookingCode() {
            if(this.formData.booking_code == "") {
                this.setErrorBooking("Kode booking wajib diisi");
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
                booking_code: "",
                user_id: this.auth.id
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
                    this.setErrorBooking("Kode booking tidak ditemukan");
                    console.error(error);
                });
        },
        handleCreateOnsiteQueue() {
            this.isLoading = true;

            this.formData["workstation_service_id"] = this.selectedWorkstation;
            this.formData["vct_id"] = this.auth.id;

            axios
                .post("/device/directQueue/store", this.formData)
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
                    this.setError("Mohon Maaf, Tidak Bisa Mengambil Antrian");
                    console.error(error);
                });
        },
        print() {
            const printWindow = window.open('', '_blank');

            const branchLogo = this.branch_logo;
            const serviceNumber = this.responseQueue.queue_no;
            const serviceName = this.workstationServices.find((service) => service.id == this.responseQueue.workstation_service_id).service.name;
            const branchName = this.branch.name;
            const currentDay = this.currentDay;
            const currentFormattedDate = this.currentFormattedDate;
            const currentTimes = this.currentTimes;
            const timezone = this.branch.timezone;
            const queueRemaining = this.responseQueue.total_waiting;

            const convertImageToBlackAndWhite = (imgSrc) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;

                    for (let i = 0; i < data.length; i += 4) {
                        const grayscale = data[i] * 0.2126 + data[i + 1] * 0.7152 + data[i + 2] * 0.0722;
                        data[i] = grayscale;
                        data[i + 1] = grayscale;
                        data[i + 2] = grayscale;
                    }

                    ctx.putImageData(imageData, 0, 0);
                    const convertedImgSrc = canvas.toDataURL();
                    // Print with converted image
                    printWithImage(convertedImgSrc);
                };

                img.src = imgSrc;
            };

            const printWithImage = (imgSrc) => {
                printWindow.document.open();
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                    <title>Print</title>
                    <style>
                        @page {
                                size: 80mm 100mm;
                        }
                        body {
                            width: 80mm;
                            height: 100mm;
                            margin: 0;
                            display: inline-grid;
                            justify-content: center;
                            align-items: center;
                            text-align: center;
                        }
                        </style>
                    </head>
                    <body>
                        <div style="margin-bottom: 25px; position: relative;">
                            <div class="d-flex flex-column align-items-center justify-content-center" style="position: absolute; width: 100%; top:0; bottom: 0; height: 100%;">
                                <div style="padding-top: 20.38px;">
                                    <img src="${imgSrc}" alt="logo-kyoo" height="26"/>
                                </div>
                                <div class="wrapper-queue-number" style="margin: 20.25px 0 23.72px;">
                                    <span style="font-size: 20px; text-align: center; font-weight: bolder;margin: 0 0 8px;">Nomor Antrian Anda</span>
                                    <h2 style="margin: 0 0 8px; font-weight: bold;font-size: 64px;text-align: center;">
                                        ${serviceNumber}
                                    </h2>
                                    <span style="margin: 0 0 8px;font-size: 12px;text-align: center;">Mohon tunggu sampai nomor <br> antrian Anda dipanggil</span>
                                </div>
                                <div class="wrapper-service-card" style="padding: 75px 28px;">
                                    <h4 style="font-weight: bold;font-size: 16px;text-align: center; margin: 0;"> ${serviceName} </h4>
                                    <span style="font-size: 12px;margin: 0 0 8px;text-align: center; font-weight: 600;">${currentDay}, ${currentFormattedDate}</span>
                                    <br>
                                    <span style="font-size: 12px;margin: 0 0 8px;text-align: center; font-weight: 600;">${currentTimes} ${timezone}</span>
                                    <br>
                                    <span style="font-size: 12px;margin: 0 0 8px;text-align: center; font-weight: 600;">Sisa Antrian : ${queueRemaining}</span>
                                </div>
                            </div>
                            <svg width="263" height="403" viewBox="0 0 263 403" fill="none" xmlns="http://www.w3.org/2000/svg" style="background-color: #ffffff;">
                                <path d="M0 15C0 6.71573 6.71573 0 15 0H248C256.284 0 263 6.71573 263 15V244.493H0V15Z" fill="#ffffff"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 281.013H11.4585V262.513V244.013H-1.90735e-06C7.01681 248.491 11.4585 255.118 11.4585 262.513C11.4585 269.908 7.01681 276.535 0 281.013Z" fill="#ffffff"/>
                                <rect width="240.083" height="37" transform="matrix(1 0 0 -1 11.4585 281.013)" fill="#ffffff"/>
                                <line y1="-1" x2="240.083" y2="-1" transform="matrix(1 0 0 -1 11.4585 262.513)" stroke="#000000" stroke-width="2" stroke-dasharray="8 8"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M263 281.013H251.542V262.513V244.013H263C255.983 248.491 251.542 255.118 251.542 262.513C251.542 269.908 255.983 276.535 263 281.013Z" fill="#ffffff"/>
                                <path d="M0 387.293C0 395.577 6.71573 402.293 15 402.293H248C256.284 402.293 263 395.577 263 387.293V280.533H0V387.293Z" fill="#ffffff"/>
                            </svg>
                        </div>
                    </body>
                    </html>
                `);

                printWindow.onload = function () {
                    printWindow.print();
                    printWindow.close();
                };

                printWindow.document.close();
            };

            convertImageToBlackAndWhite(branchLogo);
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
        },
        setErrorBooking(message) {
            this.isErrorBooking = true;
            this.errorMessage = message;

            if (this.errorTimeout) {
                clearTimeout(this.errorTimeout);
            }

            this.errorTimeout = setTimeout(() => {
                this.isErrorBooking = false;
                this.errorMessage = "";
            }, 3000);
        },
        updateCurrentDate() {
            const self = this;

            setInterval(function () {
                self.initCurrentDate();
            }, 5000);
        },
    }
};
</script>

<style scoped>
.overflow-auto::-webkit-scrollbar {
    display: none;
}

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
    height: 5.313rem;
}

.button-print {
    width: 15rem;
    height: 42.86px;
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
    color: white;
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
    padding-top: 20.25px;
    padding-bottom: 23.72px;
}
.wrapper-service-card {
    display: flex;
    align-items: center;
    flex-direction: column;
    padding: 1.75rem;
    justify-content: center;
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
