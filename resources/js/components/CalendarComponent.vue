<template>
    <div>
        <div class="text-center">
            <div class="mb-4 d-inline-flex align-items-center">
                <div class="text-left" style="width: 64px;">
                    <button v-if="!isNoPrevMonth()" class="btn btn-secondary" @click="handlePrevClick">
                        <span class="fas fa-angle-left"></span>
                    </button>
                </div>

                <div class="h4 mb-0" style="width: 186px;">{{ getMonth() }} {{ getYear() }}</div>

                <div class="text-right" style="width: 64px;">
                    <button class="btn btn-secondary" @click="handleNextClick">
                        <span class="fas fa-angle-right"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="d-flex border-left border-bottom border-top">
            <div
                class="col border-right p-2 text-center font-weight-bold"
                v-for="dayName in dayNames"
                :key="dayName"
            >
                {{ t(dayName) }}
            </div>
        </div>

        <div class="border-top">
            <div
                v-for="(week, i) in weeks"
                :key="i"
                class="d-flex border-left border-bottom"
            >
                <div
                    v-for="day in week"
                    :key="day.getDate()"
                    :class="`col border-right p-2 ${isDateOffRange(day) && 'bg-grey'} ${isToday(day) && 'bg-blue'}`"
                    style="height: 200px;"
                >
                    <div class="d-flex mb-2">
                        <div>{{ day.getDate() }}</div>

                        <div v-if="isClosed(day) || isHoliday(day)" class="ml-auto">
                            <div class="badge badge-danger text-wrap">
                                {{ `Tutup${ isHoliday(day) ? ': ' + getHolidayName(day) : '' }` }}
                            </div>
                        </div>
                    </div>

                    <div v-if="!isClosed(day) && !isHoliday(day)">
                        <div
                            v-for="slot in getSelectedSlots(day)"
                            :key="slot.id"
                            :class="`alert ${isSlotFull(slot.service.id, day) ? 'alert-warning' : 'alert-success'} p-1`"
                            style="cursor: pointer;"
                            @click="handleServiceClick(slot.service.id, day)"
                        >
                            <div class="text-dark font-weight-bold">{{ slot.service.name }}</div>
                            <p v-html="getSlotText(slot.service.id, day)" class="text-dark mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="text-dark">{{ selectedAppointment.service.name }}</h4>

                            <button type="button" class="close" aria-label="Close" @click="isShowModal = false" style="width: 40px; height: 40px;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div>
                            <small class="mb-2 d-inline-block text-dark font-weight-bold">Daftar Slot Waktu:</small>

                            <div v-for="slot in selectedAppointment.slots" :key="slot.id" class="d-flex justify-content-between mb-2">
                                <span>{{ slot.start_time }}-{{ slot.end_time }}</span>

                                <div class="text-danger" v-if="slot.available_slots === 0">
                                    Slot penuh
                                </div>

                                <div class="text-dark" v-else-if="slot.filled_slots !== 0">
                                    <strong>{{ slot.filled_slots }}/{{ slot.max_slots }}</strong> slot terisi
                                </div>

                                <div class="text-dark" v-else>
                                    <strong>{{ slot.max_slots }}</strong> slot tersedia
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { getVisibleDays } from '../utils/date'
    import ID from "../../lang/id.json";
    import EN from "../../lang/en.json";

    export default {
        props: {
            date: String,
            slots: Array,
            holidays: Array,
            schedules: Array,
            appointmentSlots: Array,
            lang: String,
        },

        mounted() {
            const days = getVisibleDays(this.selectedDate)
            this.weeks = this.chunk(days, 7)
            // console.log(this.lang);
            
        },

        watch: {
            selectedDate (newDate) {
                const days = getVisibleDays(newDate)
                this.weeks = this.chunk(days, 7)
            }
        },

        data() {
            return {
                selectedDate: this.date,
                dayNames: [
                    'Sunday',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday'
                ],
                monthNames: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                weeks: [],
                isShowModal: false,
                selectedAppointment: null,
                messages: {
                    en: EN,
                    id: ID,
                    },
            }
        },

        methods: {
            t(key, params = {}) {
              const translation = this.messages[this.lang] && this.messages[this.lang][key];
                if (translation) {
                    return translation.replace(/\{(\w+)\}/g, (_, param) => params[param] || "");
                } else {
                    return key;
                }
            },
            getSelectedSlots(currentDate) {
                return this.slots.filter(v => {
                    return v.day === moment(currentDate).format('dddd').toLowerCase()
                })
            },

            getSlotText(serviceId, currentDate) {
                const appointmentSlot = this.appointmentSlots.find(v => {
                    return v.date === moment(currentDate).format('YYYY-MM-DD') &&
                        v.service_id === serviceId
                })

                const slot = this.slots.find(v => {
                    return serviceId === v.service.id &&
                        v.day === moment(currentDate).format('dddd').toLowerCase()
                })

                if (typeof appointmentSlot === 'undefined') {
                    return `${slot.max_slots} ${this.t('slots available')}`;
                }

                if (slot.max_slots - appointmentSlot.filled_slots === 0) {
                    return this.t('Slot Full')
                }

                return `${appointmentSlot.filled_slots}/${slot.max_slots} ${this.t('slots occupied')}`
            },

            getHolidayName(currentDate) {
                const formattedDate = moment(currentDate).format('YYYY-MM-DD')

                return this.holidays.find(v => v.date === formattedDate).name
            },

            getMonth() {
                return this.t(this.monthNames[moment(this.selectedDate).format('M') - 1])
            },

            getYear() {
                return moment(this.selectedDate).format('YYYY')
            },

            isClosed(currentDate) {
                const day = moment(currentDate).format('dddd').toLowerCase()
                const schedule = this.schedules.find(v => v.day === day)
            
                return schedule && schedule.status === 'closed'
            },

            isHoliday(currentDate) {
                const formattedDate = moment(currentDate).format('YYYY-MM-DD')

                return !!this.holidays.find(v => v.date === formattedDate)
            },

            isDateOffRange(currentDate) {
                const formattedDate = moment(currentDate).format('YYYY-MM-DD')

                return formattedDate < moment(this.selectedDate).startOf('month').format('YYYY-MM-DD') ||
                    formattedDate > moment(this.selectedDate).endOf('month').format('YYYY-MM-DD')
            },

            isToday(date) {
                return moment().format('YYYY-MM-DD') === moment(date).format('YYYY-MM-DD')
            },

            isSlotFull(serviceId, date) {
                const appointmentSlot = this.appointmentSlots.find(v => {
                    return (
                        v.service_id === serviceId &&
                        v.date === moment(date).format('YYYY-MM-DD')
                    )
                })

                if (!appointmentSlot) return false

                const slot = this.slots.find(v => {
                    return (
                        v.service.id === serviceId &&
                        v.day === moment(date).format('dddd').toLowerCase()
                    )
                })

                return appointmentSlot.filled_slots === slot.max_slots
            },

            isNoPrevMonth() {
                return moment().format('YYYY-MM') === moment(this.selectedDate).format('YYYY-MM')
            },

            handlePrevClick(e) {
                this.selectedDate = moment(this.selectedDate)
                    .startOf('month')
                    .add(-1, 'month')
                    .toDate()
                
                this.$emit('navigate', this.selectedDate)
            },

            handleNextClick(e) {
                this.selectedDate = moment(this.selectedDate)
                    .startOf('month')
                    .add(1, 'month')
                    .toDate()
                
                this.$emit('navigate', this.selectedDate)
            },

            async getAppointmentSlotByServiceId(serviceId, date) {
                const { data: appointmentSlot } = await axios.get(`/cs/appointment/slots/${serviceId}`, {
                    params: {
                        date: moment(date).format('YYYY-MM-DD')
                    }
                })

                return appointmentSlot
            },

            async handleServiceClick(serviceId, date) {
                const appointmentSlot = await this.getAppointmentSlotByServiceId(serviceId, date)

                const slot = this.slots.find(v => {
                    return v.service.id === serviceId &&
                        v.day === moment(date).format('dddd').toLowerCase()
                })

                this.selectedAppointment = {
                    service: slot.service,
                    date: moment(date).toDate(),
                    slots: slot.slots.map(v => {
                        let filledSlots = 0

                        const as = appointmentSlot.find(as => as.slot_id === v.id)
                        if (as) filledSlots = as.filled_slots

                        return {
                            id: v.id,
                            start_time: v.start_time,
                            end_time: v.end_time,
                            filled_slots: filledSlots,
                            available_slots: v.max_slots - filledSlots,
                            max_slots: v.max_slots
                        }
                    })
                }
                this.isShowModal = true
            },

            chunk(days, chunkSize) {
                return days.reduce((acc, v, i) => {
                    const chunkIndex = Math.floor(i / chunkSize)

                    if (!acc[chunkIndex]) {
                        acc[chunkIndex] = []
                    }
                    
                    acc[chunkIndex].push(v)
                    
                    return acc
                }, [])
            },
        }
    }
</script>

<style scoped>
    .bg-grey {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .bg-blue {
        background-color: #dbecfe;
    }
</style>
