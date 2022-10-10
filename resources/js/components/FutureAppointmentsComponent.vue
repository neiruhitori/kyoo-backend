<template>
    <div>
        <div class="d-flex mb-4">
            <div class="btn-group btn-group-toggle align-items-end ml-auto">
                <label for="table" :class="`btn btn-outline-secondary ${selectedView === 'table' && 'active'}`">
                    <input type="radio" id="table" v-model="selectedView" value="table"> <span class="fas fa-list"></span>
                </label>

                <label for="grid" :class="`btn btn-outline-secondary ${selectedView === 'grid' && 'active'}`">
                    <input type="radio" id="grid" v-model="selectedView" value="grid"> <span class="fas fa-border-all"></span>
                </label>
            </div>
        </div>

        <div :class="`alert alert-${alert.type}`" role="alert" v-if="alert.isShow">
            {{ alert.message }}
        </div>

        <div class="card mb-4" v-if="selectedView === 'table'">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    Appointment yang Akan Datang
                </h6>
            </div>

            <div class="card-body">
                <form id="filterForm" class="mb-4" @submit.prevent="handleFilterSubmit">
                    <div class="form-row align-items-end">
                        <div class="col-auto">
                            <label for="deparmentId">Tanggal</label>
                            <input
                                type="date"
                                class="form-control"
                                style="width: 180px;"
                                v-model="selectedDate"
                            />
                        </div>
        
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nomor Antrian</th>
                            <th>Kode Booking</th>
                            <th>Status</th>
                            <th>Customer</th>
                            <th class="text-center">Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="appointment in appointments"
                            :key="appointment.id"
                        >
                            <td>{{ appointment.number }}</td>
                            <td>{{ appointment.booking_code }}</td>
                            <td>
                                <span class="badge badge-primary" v-if="appointment.status === 'book'">Dibooking</span>
                                <span class="badge badge-primary" v-if="appointment.status === 'check in'">Check In</span>
                                <span class="badge badge-secondary" v-if="appointment.status === 'no show'">Tidak Hadir</span>
                                <span class="badge badge-warning" v-if="appointment.status === 'served'">Dilayani</span>
                                <span class="badge badge-success" v-if="appointment.status === 'end served'">Selesai</span>
                                <span class="badge badge-danger" v-if="appointment.status === 'canceled'">Dibatalkan</span>
                            </td>
                            <td>{{ appointment.name }}</td>
                            <td class="text-center">{{ `${appointment.slot.start_time} - ${appointment.slot.end_time}` }}</td>
                        </tr>

                        <tr v-if="!appointments.length">
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4" v-if="selectedView === 'grid'">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    Daftar Appointment
                </h6>
            </div>

            <div class="card-body">
                <CalendarComponent
                    :date="selectedDate"
                    :schedules="schedules"
                    :holidays="holidays"
                    :slots="serviceSlots"
                    :appointmentSlots="appointmentSlots"
                    @navigate="handleDateRangeChange"
                />
            </div>
        </div>
    </div>
</template>

<script>
import CalendarComponent from './CalendarComponent'
import { getFirstVisibleDay, getEndVisibleDay } from '../utils/date'

export default {
    props: {
        schedules: Array,
        slots: Array,
    },

    components: {
        CalendarComponent
    },

    data () {
        return {
            appointments: [],
            selectedDate: moment().format('YYYY-MM-DD'),
            alert: {
                isShow: false,
                message: '',
                type: 'success'
            },
            selectedView: 'table',
            serviceSlots: [],
            appointmentSlots: [],
            holidays: [],
        }
    },

    mounted () {
        this.setFutureAppointmentsByDate(this.selectedDate)
        this.handleDateRangeChange(this.selectedDate)

        this.serviceSlots = this.groupSlots(this.slots)
    },

    methods: {
        async getFutureAppointmentsByDate(date) {
            try {
                const { data: appointments } = await axios.get('/cs/appointment/future/get', {
                    params: { date }
                })

                return appointments
            } catch (e) {
                this.showAlert(e.response.data.message, 'danger')

                return []
            }
        },

        async getAppointmentSlots(from, to) {
            try {
                const { data: appointmentSlots } = await axios.get('/cs/appointment/slots', {
                    params: { from, to }
                })

                return appointmentSlots
            } catch (e) {
                this.showAlert(e.response.data.message, 'danger')

                return []
            }
        },

        async getHolidays(from, to) {
            try {
                const { data: holidays } = await axios.get('/cs/holidays', {
                    params: { from, to }
                })

                return holidays
            } catch (e) {
                this.showAlert(e.response.data.message, 'danger')

                return []
            }
        },

        async setFutureAppointmentsByDate(date) {
            this.appointments = await this.getFutureAppointmentsByDate(date)
        },

        async setAppointmentSlots(from, to) {
            this.appointmentSlots = await this.getAppointmentSlots(
                moment(from).format('YYYY-MM-DD'),
                moment(to).format('YYYY-MM-DD')
            )
        },

        async setHolidays(from, to) {
            this.holidays = await this.getHolidays(
                moment(from).format('YYYY-MM-DD'),
                moment(to).format('YYYY-MM-DD')
            )
        },

        handleFilterSubmit() {
            this.setFutureAppointmentsByDate(this.selectedDate)
        },

        handleDateRangeChange(date) {
            const startVisibleDay = getFirstVisibleDay(date)
            const endVisibleDay = getEndVisibleDay(date)

            this.setAppointmentSlots(startVisibleDay, endVisibleDay)
            this.setHolidays(startVisibleDay, endVisibleDay)
        },

        showAlert(message, type) {
            this.alert.type = type
            this.alert.message = message
            this.alert.isShow = true

            setTimeout(() => {
                this.alert = {
                    isShow: false,
                    type: 'success',
                    message: ''
                }
            }, 3000)
        },

        groupSlots(slots) {
            const results = []

            slots.forEach(slot => {
                const prevSlotIdx = results.findIndex(v => {
                    return v.service.id === slot.service_id &&
                        v.day === slot.day
                })

                const slotStruct = {
                    id:  slot.id,
                    start_time: slot.start_time,
                    end_time: slot.end_time,
                    max_slots: slot.max_slots,
                }

                if (prevSlotIdx == -1) {
                    results.push({
                        day: slot.day,
                        service: slot.service,
                        max_slots: slot.max_slots,
                        slots: [slotStruct]
                    })
                } else {
                    results[prevSlotIdx].max_slots += slot.max_slots
                    results[prevSlotIdx].slots.push(slotStruct)
                }
            })

            return results
        },
    },
}
</script>