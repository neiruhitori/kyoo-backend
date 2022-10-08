<template>
    <div>
        <div v-for="(week, i) in weeks" :key="i" class="calendar-row">
            <div v-for="day in week" :key="day.getDate()" class="calendar-cell">
                {{ day.getDate() }}
                
                <div v-if="isClosed(day) || isHoliday(day)">
                    Tutup
                </div>

                <div v-else v-for="slot in getSelectedSlots(day)" class="slot slot-success">
                    {{ `${slot.start_time}-${slot.end_time}` }}
                    <span class="slot-description">{{ getAvailableSlot(slot.id, day) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            const days = this.getVisibleDays()
            this.weeks = this.chunk(days, 7)
        },

        data() {
            return {
                weeks: [],
                schedules: [
                    {
                        day: 'sunday',
                        status: 'closed'
                    }
                ],
                holidays: [
                    { date: '2022-10-19' }
                ],
                slots: [
                    {
                        id: 1,
                        day: 'monday',
                        start_time: '08:00',
                        end_time: '11:00',
                        max_slots: 15
                    },
                    {
                        id: 2,
                        day: 'monday',
                        start_time: '18:30',
                        end_time: '22:00',
                        max_slots: 8
                    },
                    {
                        id: 3,
                        day: 'tuesday',
                        start_time: '08:00',
                        end_time: '17:00',
                        max_slots: 30
                    },
                    {
                        id: 4,
                        day: 'saturday',
                        start_time: '08:00',
                        end_time: '13:30',
                        max_slots: 15
                    }
                ],
                appointmentSlots: [
                    {
                        date: '2022-10-08',
                        slot_id: 4,
                        available_slot: 0,
                    },
                    {
                        date: '2022-09-26',
                        slot_id: 1,
                        available_slot: 0,
                    },
                    {
                        date: '2022-10-11',
                        slot_id: 3,
                        available_slot: 2,
                    }
                ]
            }
        },

        methods: {
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

            firstVisibleDay() {
                return moment().startOf('month').startOf('week').toDate()
            },

            endVisibleDay() {
                return moment().endOf('month').endOf('week').toDate()
            },

            getVisibleDays() {
                const days = []

                let currentDate = this.firstVisibleDay()
                const endDate = this.endVisibleDay()

                while (currentDate < endDate) {
                    days.push(currentDate)

                    currentDate = moment(currentDate).add(1, 'd').toDate()
                }
                
                return days
            },

            getSelectedSlots(date) {
                return this.slots.filter(v => {
                    return v.day === moment(date).format('dddd').toLowerCase()
                })
            },

            getAvailableSlot(slotId, date) {
                const slot = this.appointmentSlots.find(v => {
                    return v.date === moment(date).format('YYYY-MM-DD') &&
                        v.slot_id === slotId
                })

                if (typeof slot === 'undefined') {
                    return this.slots.find(v => v.id === slotId).max_slots +
                        ' slot tersedia'
                }

                if (slot.available_slot === 0) {
                    return 'Penuh'
                }

                return slot.available_slot + ' slot tersedia'
            },

            isClosed(date) {
                const day = moment(date).format('dddd').toLowerCase()
                const schedule = this.schedules.find(v => v.day === day)
            
                return schedule && schedule.status === 'closed'
            },

            isHoliday(date) {
                const formattedDate = moment(date).format('YYYY-MM-DD')

                return !!this.holidays.find(v => v.date === formattedDate)
            }
        }
    }
</script>

<style>
    .calendar-row {
        display: flex;
        background-color: gray;
        height: 140px;
    }

    .calendar-cell {
        background-color: white;
        flex: 1 1 0%;
        border: 1px solid black;
    }

    .slot {
        border: 1px solid gray;
        border-radius: 4px;
        background-color: lightgray;
        padding: 4px;
        display: flex;
    }

    .slot-description {
        margin-left: auto;
        display: inline-block;
    }
</style>
