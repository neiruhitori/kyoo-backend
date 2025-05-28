<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800" v-once>{{ t('Appointment List') }} {{ branch.name }}</h1>
        </div>

        <div
            :class="`alert ${alert.type === 'success' ? 'alert-success' : 'alert-danger'}`"
            role="alert"
            v-if="isShowAlert"
        >
            {{ t(alert.message) }}
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ t('Active Appointment') }}</h6>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <a href="/cs/appointments/create" class="btn btn-primary">
                                <span class="fas fa-plus"></span>&nbsp;
                                {{ t('Add') }}
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th> {{ t('Queue Number') }}</th>
                                        <th> {{ t('Booking Code') }}</th>
                                        <th> {{ t('Customer Name') }}</th>
                                        <th> {{ t('Status') }}</th>
                                        <th> {{ t('Service') }}</th>
                                        <th class="text-center"> {{ t('Time Slot') }}</th>
                                        <th class="text-center"> {{ t('Action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-if="!activeAppointments.length">
                                        <td colspan="7" class="text-center"> {{ t('No data') }}</td>
                                    </tr>

                                    <tr
                                        v-for="appointment in activeAppointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ appointment.number }}</td>
                                        <td>{{ appointment.booking_code }}</td>
                                        <td>
                                            <a href="javascript:void(0)" @click="showCustomerDetail(appointment)">{{ appointment.name }}</a>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary" v-if="appointment.status === 'book'">
                                                {{ t('Book') }}
                                            </span>

                                            <span class="badge badge-success" v-else-if="appointment.status === 'check in'">
                                                {{ t('Attend') }}
                                            </span>

                                            <span class="badge badge-info" v-else-if="appointment.status === 'served'">
                                                {{ t('Serve') }}
                                            </span>
                                        </td>
                                        <td>{{ appointment.service.name }}</td>
                                        <td class="text-center">{{ appointment.slot.start_time }} - {{ appointment.slot.end_time }}</td>

                                        <td class="d-flex justify-content-center">
                                            <button
                                                class="btn btn-success mr-1"
                                                @click="onCheckIn(appointment.id)"
                                                v-if="appointment.status === 'book'"
                                            >
                                                Check In
                                            </button>

                                            <button
                                                class="btn btn-secondary"
                                                @click="onNoShow(appointment.id)"
                                                v-if="appointment.status === 'book'"
                                            >
                                            {{ t('No Show') }}
                                            </button>

                                            <button
                                                v-else-if="appointment.status === 'check in'"
                                                class="btn btn-primary"
                                                @click="onServed(appointment.id)"
                                            >
                                            {{ t('Serve') }}
                                            </button>

                                            <button
                                                v-else-if="appointment.status === 'served'"
                                                class="btn btn-success"
                                                @click="onEndServed(appointment.id)"
                                            >
                                            {{ t('End Serve') }}
                                            </button>

                                            <button
                                                v-if="['check in', 'book'].includes(appointment.status)"
                                                class="btn btn-danger ml-1"
                                                @click="onCancel(appointment.id)"
                                            >
                                            {{ t('Cancel') }}
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
                        <h6 class="m-0 font-weight-bold text-primary">{{ t('Appointment Completed') }}</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ t('Queue Number') }}</th>
                                        <th>{{ t('Booking Code') }}</th>
                                        <th>{{ t('Customer Name') }}</th>
                                        <th class="text-center">{{ t('Served Time') }}</th>
                                        <th>{{ t('Service') }}</th>
                                        <th class="text-center">{{ t('Time Slot') }}</th>
                                        <th>{{ t('Status') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-if="!finishAppointments.length">
                                        <td colspan="7" class="text-center">{{ t('No data') }}</td>
                                    </tr>

                                    <tr
                                        v-for="appointment in finishAppointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ appointment.number }}</td>
                                        <td>{{ appointment.booking_code }}</td>
                                        <td>
                                            <a href="javascript:void(0)" @click="showCustomerDetail(appointment)">{{ appointment.name }}</a>
                                        </td>
                                        <td class="text-center">{{ appointment.served_time }}</td>
                                        <td>{{ appointment.service.name }}</td>
                                        <td class="text-center">
                                            {{ appointment.slot.start_time }} - {{ appointment.slot.end_time }}
                                        </td>
                                        <td>
                                            <span class="badge badge-success" v-if="appointment.status === 'end served'">
                                                {{ t('End Served') }}
                                            </span>

                                            <span class="badge badge-warning" v-else-if="appointment.status === 'no show'">
                                                {{ t('No Show') }}
                                            </span>

                                            <span class="badge badge-danger" v-else-if="appointment.status === 'canceled'">
                                                {{ t('Cancelled') }}
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
                        <h5 class="modal-title">{{ t('Cancel Appointment') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isShowModal = false">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p>{{ t('Are you sure you want to cancel the appointment') }} <strong>{{ selectedAppointment.number }}</strong>?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="isShowModal = false">{{ ('No') }}</button>
                        <button type="button" class="btn btn-danger" @click="confirmCancel(selectedAppointment.id)">{{ t('Yes, Cancel the Appointment') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowCustomer">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ t('Customer Details') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isShowCustomer = false">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-md-4">{{ t('Name') }}</div>
                            <div class="text-dark">{{ customerDetail.name }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">{{ t('Email') }}</div>
                            <div class="text-dark">{{ customerDetail.email }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">{{ t('Phone Number') }}</div>
                            <div class="text-dark">{{ customerDetail.phone }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">{{ t('Notes') }}</div>
                            <div class="text-dark">{{ customerDetail.notes }}</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="isShowCustomer = false">{{ t('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import audioRecorder from "../utils/audio-recorder"
import ID from "../../lang/id.json";
import EN from "../../lang/en.json";
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
        }, 
        lang: {
            type: String,
        }
    },

    data() {
        return {
            activeAppointments: [],
            finishAppointments: [],
            messages: {
                    en: EN,
                    id: ID,
                    },
            currentLocale: this.lang || "en",

            isShowModal: false,
            selectedAppointment: null,

            isShowCustomer: false,
            customerDetail: {
                name: '',
                email: '',
                phone: '',
                notes: ''
            }, 

            isShowAlert: false,
            alert: {
                type: 'success',
                message: ''
            },
        }
    },
   
    created(){
        Echo.channel(`event_appointment_queue_general.${this.branch.id}`)
      .listen('QueueAppointmentStatus', () => {
        this.getQueues();
      });
    },
    mounted() {
        this.getQueues()
        Echo.channel(`event_appointment_queue_general.${this.branch.id}`)
        .listen("AppointmentQueue", () => {
            this.getQueues(); 
        })
    },

    methods: {
        t(key, params = {}) {
         const translation = this.messages[this.currentLocale] && this.messages[this.currentLocale][key];
            if (translation) {
                 return translation.replace(/\{(\w+)\}/g, (_, param) => params[param] || "");
            } else {
                 return key;
            }
        },
        async getQueues() {
            const finishedAppointmentStatus = ['end served', 'no show', 'canceled']

            const response = await axios.get('/cs/appointments')
            this.finishAppointments = response.data.data.filter(v => {
                return finishedAppointmentStatus.includes(v.status)
            }).sort((a, b) => {
                if (a.number > b.number) return -1
                if (a.number < b.number) return 1

                return 0
            })

            this.activeAppointments = response.data.data.filter(v => {
                return !finishedAppointmentStatus.includes(v.status)
            })
        },

        async onCheckIn(id) {
            try {
                await axios.patch(`/cs/appointments/${id}/checkin`)

                this.showAlert('Appointment successfully updated')
            } catch (e) {
                this.showAlert(
                    e.response.data?.message || 'Failed to update the appointment',
                    'danger'
                )
            }

            this.getQueues()
        },

        async onNoShow(id) {
            try {
                await axios.patch(`/cs/appointments/${id}/no-show`)

                this.showAlert('Appointment successfully updated')
            } catch (e) {
                this.showAlert(
                    e.response.data?.message || 'Failed to update the appointment',
                    'danger'
                )
            }

            this.getQueues()
        },

        async onServed(id) {
            try {
                await axios.patch(`/cs/appointments/${id}/served`)

                this.showAlert('Appointment served')

                audioRecorder.start()
            } catch (e) {
                this.showAlert(
                    e.response.data?.message || 'Failed to serve the appointment',
                    'danger'
                )
            }

            this.getQueues()
        },

        async onEndServed(id) {
            try {
                await axios.patch(`/cs/appointments/${id}/end-served`)

                this.showAlert('Appointment ended')
            } catch (e) {
                this.showAlert(
                    e.response.data?.message || 'Failed to end the appointment',
                    'danger'
                )
            }

            await this.getQueues()

            const appointment = this.finishAppointments.find(v => v.id === id)
            appointment.message = await audioRecorder.stop()

            this.saveAudio(appointment)
        },

        async onCancel(id) {
            this.selectedAppointment = this.activeAppointments.find(v => v.id === id)
            this.isShowModal = true
        },

        async confirmCancel(id) {
            try  {
                await axios.patch(`/cs/appointments/${id}/cancel`)
    
                this.isShowModal = false
                this.showAlert('Appointment canceled')
            } catch (e) {
                this.isShowModal = false
                this.showAlert(
                    e.response.data?.message || 'Failed to cancel the appointment',
                    'danger'
                )
            }

            this.getQueues()
        },

        showAlert(message, type = 'success') {
            this.alert = { type, message }
            this.isShowAlert = true

            setTimeout(() => {
                this.isShowAlert = false
                this.alert = { type, message: '' }
            }, 3000)
        },

        showCustomerDetail({ name, email, phone, notes }) {
            this.customerDetail.name = name
            this.customerDetail.email = email
            this.customerDetail.phone = phone
            this.customerDetail.notes = notes

            this.isShowCustomer = true
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