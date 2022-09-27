<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800" v-once>Daftar Appointment {{ branch.name }}</h1>
        </div>

        <div
            :class="`alert ${alert.type === 'success' ? 'alert-success' : 'alert-danger'}`"
            role="alert"
            v-if="isShowAlert"
        >
            {{ alert.message }}
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Appointment Aktif</h6>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <a href="/cs/appointments/create" class="btn btn-primary">
                                <span class="fas fa-plus"></span>&nbsp;
                                Tambah
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nomor Antrian</th>
                                        <th>Kode Booking</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Layanan</th>
                                        <th class="text-center">Waktu</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-if="!activeAppointments.length">
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>

                                    <tr
                                        v-for="appointment in activeAppointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ appointment.number }}</td>
                                        <td>{{ appointment.booking_code }}</td>
                                        <td>{{ appointment.name }}</td>
                                        <td>
                                            <span class="badge badge-primary" v-if="appointment.status === 'book'">
                                                Dibooking
                                            </span>

                                            <span class="badge badge-success" v-else-if="appointment.status === 'check in'">
                                                Hadir
                                            </span>

                                            <span class="badge badge-info" v-else-if="appointment.status === 'served'">
                                                Dilayani
                                            </span>
                                        </td>
                                        <td>{{ appointment.service.name }}</td>
                                        <td class="text-center">{{ appointment.slot.start_time }} - {{ appointment.slot.end_time }}</td>

                                        <td class="d-flex justify-content-center">
                                            <div v-if="appointment.status === 'book'">
                                                <button
                                                    class="btn btn-success mr-1"
                                                    @click="onCheckIn(appointment.id)"
                                                >
                                                    Check In
                                                </button>

                                                <button
                                                    class="btn btn-secondary"
                                                    @click="onNoShow(appointment.id)"
                                                >
                                                    Tidak Hadir
                                                </button>
                                            </div>

                                            <button
                                                v-else-if="appointment.status === 'check in'"
                                                class="btn btn-primary"
                                                @click="onServed(appointment.id)"
                                            >
                                                Layani
                                            </button>

                                            <button
                                                v-else-if="appointment.status === 'served'"
                                                class="btn btn-success"
                                                @click="onEndServed(appointment.id)"
                                            >
                                                Layanan Selesai
                                            </button>

                                            <button
                                                v-if="['check in', 'book'].includes(appointment.status)"
                                                class="btn btn-danger ml-2"
                                                @click="onCancel(appointment.id)"
                                            >
                                                Batal
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Appointment Selesai</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nomor Antrian</th>
                                        <th>Kode Booking</th>
                                        <th>Customer</th>
                                        <th class="text-center">Waktu Dilayani</th>
                                        <th>Layanan</th>
                                        <th class="text-center">Waktu</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-if="!finishAppointments.length">
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>

                                    <tr
                                        v-for="appointment in finishAppointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ appointment.number }}</td>
                                        <td>{{ appointment.booking_code }}</td>
                                        <td>{{ appointment.name }}</td>
                                        <td class="text-center">{{ appointment.served_time }}</td>
                                        <td>{{ appointment.service.name }}</td>
                                        <td class="text-center">
                                            {{ appointment.slot.start_time }} - {{ appointment.slot.end_time }}
                                        </td>
                                        <td>
                                            <span class="badge badge-success" v-if="appointment.status === 'end served'">
                                                Selesai Dilayani
                                            </span>

                                            <span class="badge badge-warning" v-else-if="appointment.status === 'no show'">
                                                Tidak Hadir
                                            </span>

                                            <span class="badge badge-danger" v-else-if="appointment.status === 'canceled'">
                                                Dibatalkan
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Batalkan Appointment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isShowModal = false">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan appointment <strong>{{ selectedAppointment.number }}</strong>?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="isShowModal = false">Tidak</button>
                        <button type="button" class="btn btn-danger" @click="confirmCancel(selectedAppointment.id)">Ya, Batalkan Appointment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import audioRecorder from "../utils/audio-recorder"

export default {
    props: {
        branch: {
            type: Object,
            required: true
        },
        workstation: {
            type: Object,
            required: true
        },
        vct: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            activeAppointments: [],
            finishAppointments: [],

            isShowModal: false,
            selectedAppointment: null,

            isShowAlert: false,
            alert: {
                type: '',
                message: ''
            },
        }
    },

    mounted() {
        this.getQueues()
    },

    methods: {
        async getQueues() {
            const finishedAppointmentStatus = ['end served', 'no show', 'canceled']

            const { data } = await axios.get('/cs/appointments')

            this.finishAppointments = data.filter(v => {
                return finishedAppointmentStatus.includes(v.status)
            }).sort((a, b) => {
                if (a.number > b.number) {
                    return -1
                }

                if (a.number < b.number) {
                    return 1
                }

                return 0
            })

            this.activeAppointments = data.filter(v => {
                return !finishedAppointmentStatus.includes(v.status)
            })
        },

        async onCheckIn(id) {
            await axios.patch(`/cs/appointments/${id}/checkin`)

            this.getQueues()
        },

        async onNoShow(id) {
            await axios.patch(`/cs/appointments/${id}/no-show`)

            this.getQueues()
        },

        async onServed(id) {
            await axios.patch(`/cs/appointments/${id}/served`)

            audioRecorder.start()
                .then(() => {
                    console.log('Recording audio...')
                })

            this.getQueues()
        },

        async onEndServed(id) {
            const { data } = await axios.patch(`/cs/appointments/${id}/end-served`)

            this.getQueues()

            data.message = await audioRecorder.stop()

            this.saveAudio(data)
        },

        async onCancel(id) {
            this.selectedAppointment = this.activeAppointments.find(v => v.id === id)
            this.isShowModal = true
        },

        async confirmCancel(id) {
            try  {
                await axios.patch(`/cs/appointments/${id}/cancel`)
    
                this.isShowModal = false
                this.showAlert('Appointment dibatalkan')
            } catch (e) {
                this.isShowModal = false
                this.showAlert('danger', e.response.data?.message || 'Pembatalan appointment gagal')
            }

            this.getQueues()
        },

        showAlert(type = 'success', message) {
            this.alert = { type, message }
            this.isShowAlert = true

            setTimeout(() => {
                this.isShowAlert = false
                this.alert = { type, message: '' }
            }, 2500)
        },

        saveAudio(appointment)  {
            const self = this

            const fileReader = new FileReader()

            fileReader.readAsDataURL(appointment.message)
            
            fileReader.onloadend = async function () {
                await axios.post('/cs/voice-recorder', {
                    customer_name: appointment.name,
                    branch_id: self.branch.id,
                    branch_name: self.branch.name,
                    workstation_id: self.workstation.id,
                    workstation_name: self.workstation.label,
                    vct_id: self.vct.id,
                    vct_name: self.vct.name,
                    queue_id: appointment.id,
                    queue_type: 'appointment',
                    queue_no: appointment.number,
                    message: fileReader.result.split(',')[1],
                    duration: Math.floor(moment().diff(moment(appointment.served_time)) / 1000)
                })
            }
        }
    }
}
</script>

<style>
    .modal {
        background-color: rgba(0, 0, 0, .5);
    }
</style>