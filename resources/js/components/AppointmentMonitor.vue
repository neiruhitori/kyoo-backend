<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800" v-once>Daftar Appointment {{ branch.name }}</h1>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Antrian Aktif</h6>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <a href="/cs/appointments/create" class="btn btn-primary">
                                Tambah Appointment
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nomor Antrian</th>
                                        <th>Kode Booking</th>
                                        <th class="text-center">Waktu</th>
                                        <th>Nama</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-if="!activeAppointments.length">
                                        <td colspan="7" class="text-center">Data tidak ditemukan</td>
                                    </tr>

                                    <tr
                                        v-for="appointment in activeAppointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ appointment.number }}</td>
                                        <td>{{ appointment.booking_code }}</td>
                                        <td class="text-center">{{ appointment.slot.start_time }} - {{ appointment.slot.end_time }}</td>
                                        <td>{{ appointment.name }}</td>
                                        <td>{{ appointment.service.name }}</td>
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

                                        <td class="text-center">
                                            <div v-if="appointment.status === 'book'">
                                                <button
                                                    class="btn btn-success mr-1"
                                                    data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="Check In"
                                                    @click="onCheckIn(appointment.id)"
                                                >
                                                    Check In
                                                </button>

                                                <button
                                                    class="btn btn-danger"
                                                    data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="Tidak Hadir"
                                                    @click="onNoShow(appointment.id)"
                                                >
                                                    Tidak Hadir
                                                </button>
                                            </div>

                                            <button
                                                v-else-if="appointment.status === 'check in'"
                                                class="btn btn-primary"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="Layani"
                                                @click="onServed(appointment.id)"
                                            >
                                                Layani
                                            </button>

                                            <button
                                                v-else-if="appointment.status === 'served'"
                                                class="btn btn-success"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="Layanan Selesai"
                                                @click="onEndServed(appointment.id)"
                                            >
                                                Layanan Selesai
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
                        <h6 class="m-0 font-weight-bold text-primary">Riwayat Antrian</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nomor Antrian</th>
                                        <th>Kode Booking</th>
                                        <th class="text-center">Waktu</th>
                                        <th>Nama</th>
                                        <th>Layanan</th>
                                        <th class="text-center">Waktu Dilayani</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-if="!pastAppointments.length">
                                        <td colspan="7" class="text-center">Data tidak ditemukan</td>
                                    </tr>

                                    <tr
                                        v-for="appointment in pastAppointments"
                                        :key="appointment.id"
                                    >
                                        <td>{{ appointment.number }}</td>
                                        <td>{{ appointment.booking_code }}</td>
                                        <td class="text-center">
                                            {{ appointment.slot.start_time }} - {{ appointment.slot.end_time }}
                                        </td>
                                        <td>{{ appointment.name }}</td>
                                        <td>{{ appointment.service.name }}</td>
                                        <td class="text-center">{{ appointment.served_time }}</td>
                                        <td>
                                            <span class="badge badge-success" v-if="appointment.status === 'end served'">
                                                Selesai Dilayani
                                            </span>

                                            <span class="badge badge-danger" v-else-if="appointment.status === 'no show'">
                                                Tidak Hadir
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
    </div>
</template>

<script>
export default {
    props: {
        branch: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            activeAppointments: [],
            pastAppointments: []
        }
    },

    mounted() {
        this.getQueues()
    },

    methods: {
        async getQueues() {
            const { data } = await axios.get('/cs/appointments')

            this.pastAppointments = data.filter(v => {
                return v.status === 'end served' || v.status === 'no show'
            }).sort((a, b) => {
                if (a.created_at > b.created_at) {
                    return -1
                }

                if (a.created_at < b.created_at) {
                    return 1
                }

                return 0
            })

            this.activeAppointments = data.filter(v => {
                return v.status !== 'end served' && v.status !== 'no show'
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

            this.getQueues()
        },

        async onEndServed(id) {
            await axios.patch(`/cs/appointments/${id}/end-served`)

            this.getQueues()
        }
    }
}
</script>