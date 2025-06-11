<template>
    <div>
    <loading
      :active.sync="isLoading"
      color="#189DCD"
      :is-full-page="true"
      :width="64"
      :height="64"
    />
        <div class="row mb-3">
                <div class="col-md-6 d-flex justify-content-between">
                <div class="text-dark p-3">
                <p>{{ username }}</p>
                <h5 class="font-weight-bold">{{ this.workstation.label }}</h5>
                </div>
            </div>

            <div class="col-md-6 d-flex">
                <div class="card shadow mb-4 mr-1">
                    <div class="card-body font-weight-bold rounded p-3" style="width: 11rem;">
                    <p>Check In <i class="ml-4 text-success bi bi-people-fill"></i></p>
                    <h6>{{ count?.check_in || '0' }}</h6>
                    </div>
                </div>
                <div class="card shadow mb-4 mr-1">
                    <div class="card-body font-weight-bold rounded p-3" style="width: 11rem;">
                    <p>Served <i class="ml-4 text-primary bi bi-check-circle"></i></p>
                    <h6>{{ count?.end_served || '0' }}</h6>
                    </div>
                </div>
                <div class="card shadow mb-4 mr-1">
                    <div class="card-body font-weight-bold rounded p-3" style="width: 11rem;">
                    <p>No Show <i class="ml-4 text-danger bi bi-clock"></i></p>
                    <h6>{{ count?.no_show || '0' }}</h6>
                    </div>
                </div>
                <div class="card shadow mb-4 mr-1">
                    <div class="card-body font-weight-bold rounded p-3" style="width: 11rem;">
                    <p>{{ formattedTime }}</p>
                    <h6>{{ formattedDate }}</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-2 d-flex align-items-center justify-content-between">
                <h4>{{ this.t('Queue List') }}</h4>
            </div>
            <div class="col-md-6 mb-2 d-flex align-items-center">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search.."
                    @input="debounceSearch"/>
                <a
                href="javascript:void(0)" @click="showCreateModal()"
                class="btn btn-primary ml-2" style="white-space: nowrap;">
                     {{ this.t('Create New Queue') }}
                    </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow rounded">
                    <div class="card-body pt-0 table-responsive" style="max-height: 65vh;">
                    <table class="table table-borderless">
                        <thead>
                        <tr class="sticky-top" style="background-color: white;">
                            <th scope="col">No</th>
                            <th scope="col">{{ t('Code') }}</th>
                            <th scope="col">{{ t('Name') }}</th>
                            <th scope="col">{{ t('Services') }}</th>
                            <th scope="col">{{ t('Time Slot') }}</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-if="filteredAppointments.length > 0">
                                <tr
                                    v-for="appointment in filteredAppointments"
                                    :key="appointment.id"
                                    @click="selectQueue(appointment.number)"
                                    class="pointer"
                                >
                                    <td>{{ appointment.number }}</td>
                                    <td>
                                        {{ appointment.booking_code.toUpperCase()}}
                                    </td>
                                    <td>
                                        {{ appointment.name }}
                                    </td>
                                    <td>
                                        {{ appointment.service.name }}
                                    </td>
                                    <td>
                                        {{ appointment.slot ? appointment.slot.start_time + ` - ` + appointment.slot.end_time : '-' }}
                                    </td>
                                    <td>

                                    <div class="d-flex align-items-center">

                                    <span
                                        style="
                                            border-radius: 20px;
                                            background-color: #149D521A;
                                            color: #149D52 ;
                                            padding: 2px 15px;
                                        "
                                        v-show="appointment.status == 'check in'"
                                        >{{ t('Attend') }}</span
                                    >
                                    <span
                                        style="
                                            border-radius: 20px;
                                            background-color: #3B82F61A;
                                            color: #3787EA ;
                                            padding: 2px 15px;
                                        "
                                        v-show="appointment.status == 'served'"
                                        >{{ t('Served') }}</span
                                    >
                                    <span
                                        style="
                                            border-radius: 20px;
                                            background-color: #FEF7DC;
                                            color: #E29E15;
                                            padding: 2px 15px;
                                        "
                                        v-show="appointment.status == 'book'"
                                        >{{ t('Booked') }}</span>
                                    </div>
                                    </td>
                                    <td class="d-flex justify-content-center">
                                            <button
                                                class="btn btn-outline-success mr-1"
                                                @click="onCheckIn(appointment.id)"
                                                v-if="appointment.status === 'book'"
                                            >
                                                Check In
                                            </button>

                                            <button
                                                class="btn btn-outline-danger"
                                                @click="onNoShow(appointment.id)"
                                                v-if="appointment.status === 'book'"
                                            >
                                            {{ t('No Show') }}
                                            </button>
                                        </td>
                                </tr>
                                </template>
                                <tr v-else>
                                <td colspan="7">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="/img/list.png" alt="" style="max-height: 21rem;">
                                    </div>
                                </td>
                                </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow rounded">
                <div class="card-header">
                    {{ t('Current Queue') }}
                </div>
                <div class="card-body">
                    <div v-if="queue_detail">
                    <div style="width: 20rem;" class="mb-4">
                        <div class="d-flex">
                        <h6 class="mr-1 text-primary" style="background-color: #E6F3FF; 
                                                            padding: 0.2rem 0.7rem; 
                                                            border-radius: 2rem;
                                                            align-content: center;">
                            {{queue_detail.number }}
                        </h6>
                        -
                        <h6  class="ml-1 font-weight-bold" style=" padding: 0.3rem 0rem;">{{ queue_detail.name || '' }}</h6>
                        </div>
                        <p class="mb-2">{{ queue_detail.service.name || ''}}</p>
                        <a href="javascript:void(0)" @click="showCustomerDetail(queue_detail)">{{ t('See Customer Detail') }}</a>
                    </div>
                    <hr class="mt-4 mb-4">
                    <div class="d-flex flex-column align-items-center" v-if="auth.branch.branch_type.is_premium">
                        <div class="d-flex">
                        <small class="d-none text-danger mr-2 font-weight-bold mb-2 align-content-center" 
                                style="font-size: 12px;"
                                ref="overSla">
                            <i class="bi bi-exclamation-circle ml-1"></i> Over SLA</small>
                        <p class="text-dark text-right font-weight-bold mb-2">{{ this.t('Service Duration') }}</p>
                        </div>

                        <div class="d-flex justify-content-between" style="max-width: 300px; gap: 20px;">
                        <div class="text-center" style="width: 75px;">
                            <h3 class="h3 font-weight-bold mb-0 text-dark border" ref="coundownHours">{{ clock.hours || '00' }}</h3>
                            <span>{{ this.t('Hour') }}</span>
                        </div>
                        <div class="text-center" style="width: 75px;">
                            <h3 class="h3 font-weight-bold mb-0 text-dark border" ref="coundownMinutes">{{ clock.minutes || '00' }}</h3>
                            <span>{{ this.t('Minutes') }}</span>
                        </div>
                        <div class="text-center" style="width: 75px;">
                            <h3 class="h3 font-weight-bold mb-0 text-dark border" ref="coundownSeconds">{{ clock.seconds || '00' }}</h3>
                            <span>{{ this.t('Seconds') }}</span>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div v-else class="d-flex justify-content-center">
                    <div class="text-center">
                        <img src="/img/emptystate.png" alt="">
                        <p class="text-dark font-weight-bold">Tidak ada antrian saat ini</p>
                        <p>Sistem akan memperbarui daftar secara otomatis ketika masuk</p>
                        </div>
                    </div>
                    
                    <hr class="mt-4 mb-4">

                    <div>
                    <div v-if="queue_detail">
                    <template>
                        <div class="d-flex mb-3" v-if="queue_detail.status == 'book'">
                            <button
                            class="btn btn-success fullwidth mb-2 mr-2"
                            @click="onCheckIn(queue_detail.id)"
                            :disabled="!selected_queue || queue_detail.status == 'served'"
                            >
                            {{ this.t('Check In') }}
                            </button>
                            <button @click="isShowCaller = true" class="btn btn-outline-primary mb-2">
                            <i class="bi bi-calculator"></i>
                            </button >
                        </div>

                        <template v-else-if="queue_detail.status == 'check in'">
                            <div class="row">
                            <div class="col-md-6">
                                <button
                                class="btn btn-outline-primary fullwidth mb-3"
                                @click="onServed(queue_detail.id)"
                                >
                                <i class="bi bi-check-circle"></i>
                                <span class="text-dark"> {{ t('Serve') }}</span>
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button
                                class="btn btn-outline-danger fullwidth mb-3"
                                @click="onCancel(queue_detail.id)"
                                >
                                <i class="bi bi-box-arrow-right"></i>
                                <span class="text-dark"> {{ t('Cancel') }}</span>
                                </button>
                            </div>
                            </div>
                        </template>

                        <template v-else-if="queue_detail.status == 'served'">
                            <div class="row">
                            <div class="col-md-12">
                                <button
                                class="btn btn-outline-success fullwidth mb-3"
                                @click="onEndServed(queue_detail.id)"
                                >
                                <i class="bi bi-check-circle"></i>
                                <span class="text-dark"> {{ t('End Serve') }}</span>
                                </button>
                            </div>
                            </div>
                        </template>
                    </template>
                    </div>
                    </div>

                </div>
                </div>
            </div>

        </div>

        <!-- Show Modal -->
         <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowCustomer">
             <div class="modal-dialog modal-dialog-centered" style="max-width: 900px;">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ t('Customer Details') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isShowCustomer = false">
                         <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
 
                     <div class="modal-body">
                         <div class="row justify-content-between mb-4">
                          <div class="col-md-7">
                              <h4 class="text-dark font-weight-bold">{{ customerDetail.name }}</h4>
                              <div class="d-flex">{{ t('Services') }}:<p class="ml-2 text-dark font-weight-bold"> {{ customerDetail.service }}</p></div>
                          </div>
                          <div class="col-md-4 text-right align-content-center">
                            <span
                              class="badge badge-pill badge-warning p-2"
                              v-show="customerDetail.status == 'book'"
                              >{{ t('Booked') }}</span>

                            <span
                              class="badge badge-pill badge-success p-2"
                              v-show="customerDetail.status == 'check in'"
                              >{{ t('Check In') }}</span>

                            <span
                              class="badge badge-pill badge-primary  p-2"
                              v-show="customerDetail.status == 'served'"
                              >{{ t('Served') }}</span
                            >
                            <span
                              class="badge badge-pill badge-danger p-2"
                              v-show="customerDetail.status == 'no show'"
                              >{{ t('No Show') }}</span
                            >
                            <span
                              class="badge badge-pill badge-secondary p-2"
                              v-show="customerDetail.status == 'end served'"
                              >{{ t('End Served') }}</span
                            >
                          </div>
                         </div>


                        <div class="row mb-2">
                          <div class="col-md-3 mb-2">
                            <div class="border rounded p-2">
                              <p class="mb-1">{{ t('Phone') }}</p>
                              <div class="text-dark font-weight-bold">{{ customerDetail.phone }}</div>
                            </div>
                          </div>
                          <div class="col-md-3 mb-2">
                            <div class="border rounded p-2">
                              <p class="mb-1">{{ t('Email') }}</p>
                              <div class="text-dark font-weight-bold">{{ customerDetail.email || '-' }}</div>
                            </div>
                          </div>
                          <div class="col-md-3 mb-2">
                            <div class="border rounded p-2">
                              <p class="mb-1">{{ t('Booking Code') }}</p>
                              <div class="text-dark font-weight-bold">{{ customerDetail.bookingCode.toUpperCase() }}</div>
                            </div>
                          </div>
                          <div class="col-md-3 mb-2">
                            <div class="border rounded p-2">
                              <p class="mb-1">{{ t('Date') }}</p>
                              <div class="text-dark font-weight-bold">{{ customerDetail.date || '-' }}</div>
                            </div>
                          </div>
                        </div>

                          <div class="row g-2">
                            <div class="col-md-12 mb-2">
                              <div class="border rounded p-2">
                                <p class="mb-1">{{ t('Notes') }}</p>
                                <div class="text-dark font-weight-bold">{{ customerDetail.notes || '-' }}</div>
                              </div>
                            </div>
                          </div>

                         
                     </div>
 
                     <div class="modal-footer">
                         <button type="button" class="btn btn-danger" data-dismiss="modal" @click="isShowCustomer = false">{{ t('Close') }}</button>
                     </div>
                 </div>
             </div>
         </div>
        <!-- Create Modal -->
        <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowCreate">
             <div class="modal-dialog modal-lg modal-dialog-centered">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ t('Create Queue') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeCreateModal">
                         <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
 
                     <div class="modal-body">
                         <div class="row mb-2">
                            <div class="col-md-12 mb-3" v-if="errors.create">
                                <div class="alert alert-danger">
                                    {{ errors.create }}
                                </div>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="name">{{ t('Date') }}</label>
                                <input type="date" name="name" v-model="form.date"
                                :min="minDate" class="form-control" value="">
                                <small class="text-danger" v-if="errors.date">{{ errors.date }}</small>
                             </div>
                              <div class="col-md-6 mb-3">
                                <label for="service_id">{{ t('Service') }}</label>
                                <select name="service_id" class="form-control" v-model="form.service_id">
                                        <option v-for="service in serviceList" :key="service.id" :value="service.id">{{ service.name }}</option>
                                </select>
                                <small class="text-danger" v-if="errors.service_id">{{ errors.service_id }}</small>
                             </div>
                             <div class="col-md-6 mb-3">
                                <label for="slot_id">{{ t('Time Slot') }}</label>
                                <select name="slot_id" class="form-control" v-model="form.slot_id">
                                        <option v-for="slot in this.slotAvailable" :key="slot.id" :value="slot.id">
                                            {{ slot.start_time + ` - ` + slot.end_time + ` (`+ slot.filledSlot + `/` + slot.max_slots + `)` }}
                                        </option>
                                </select>
                                <small class="text-danger" v-if="errors.slot">{{ errors.slot}}</small>
                             </div>
                             <div class="col-md-6 mb-3">
                                <label for="name">{{ t('Name') }}</label>
                                <input type="text" name="name" v-model="form.name" class="form-control" value="">
                                <small class="text-danger" v-if="errors.name">{{ errors.name }}</small>
                             </div>
                             <div class="col-md-6 mb-3">
                                <label for="name">{{ t('Phone') }}</label>
                                <input type="number" name="phone" v-model="form.phone" class="form-control" value="">
                                <small class="text-danger" v-if="errors.phone">{{ errors.phone }}</small>
                             </div>
                             <div class="col-md-6 mb-3">
                                <label for="email">{{ t('Email') }}</label>
                                <input type="email" name="email" v-model="form.email" class="form-control" value="">
                                <small class="text-danger" v-if="errors.email">{{ errors.email }}</small>
                             </div>
                             <div class="col-md-12 mb-3">
                                <label for="notes">{{ t('Notes') }}</label>
                                <input type="notes" name="notes" v-model="form.notes" class="form-control" value="">
                             </div>
                         </div>
                     </div>

                     <div class="modal-footer">
                         <button type="button" class="btn btn-outline-danger" data-dismiss="modal" @click="closeCreateModal">{{ t('Close') }}</button>
                         <button type="button" class="btn btn-primary" :disabled="isSubmit" data-dismiss="modal" @click="createData">{{ t('Save') }}</button>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Caller Modal -->
         <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowCaller">
             <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" style="max-width: 25rem;">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ t('Call Queue Number') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeCallerModal">
                         <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
 
                     <div class="modal-body">
                         <div class="row justify-content-end mb-3 px-4 ">
                          <div class="col-md-12 text-right">
                              <h6>Masukkan Nomor Antrian</h6>
                              <div class="d-flex justify-content-end align-items-center mt-3" style="gap: 1.5rem;">
                                <h4 class="font-weight-bolder m-0">{{ this.inputValue || '' }}</h4>
                                <button class="btn btn-primary" :disabled="this.inputValue == ''" @click="handleCaller">
                                  <i class="bi bi-volume-up"></i>
                                </button>
                              </div>
                              <small class="text-danger" v-if="errors.queueNumber">{{ errors.queueNumber }}</small>
                          </div>
                         </div>

                         <div class="row mx-5 my-3 border rounded p-3" style="background-color: #E6F3FF; gap: 1rem;">
                           
                              <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                  <button class="btn btn-outline-primary w-75" @click="handleInputCaller('1')">1</button>
                                  <button class="btn btn-outline-primary w-75" @click="handleInputCaller('2')">2</button>
                                  <button class="btn btn-outline-primary w-75" @click="handleInputCaller('3')">3</button>
                              </div>
                              <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                  <button class="btn btn-outline-primary w-75"  @click="handleInputCaller('4')">4</button>
                                  <button class="btn btn-outline-primary w-75"  @click="handleInputCaller('5')">5</button>
                                  <button class="btn btn-outline-primary w-75"  @click="handleInputCaller('6')">6</button>
                              </div>
                              <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                  <button class="btn btn-outline-primary w-75"  @click="handleInputCaller('7')">7</button>
                                  <button class="btn btn-outline-primary w-75"  @click="handleInputCaller('8')">8</button>
                                  <button class="btn btn-outline-primary w-75"  @click="handleInputCaller('9')">9</button>
                              </div>
                            <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('0')">0</button>
                                <button class="btn btn-outline-danger w-75" @click="handleClearCaller">Clear</button>
                            </div>
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
    </div>
</template>

<script>
import audioRecorder from "../utils/audio-recorder"
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import ID from "../../lang/id.json";
import EN from "../../lang/en.json";
export default {
    components: {
        Loading,
    },
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
        },
        auth: {
            type: Object,
            required: true
        },
        services: {
            type: Array,
            required: true
        },
    },

    data() {
        return {
            activeAppointments: [],
            finishAppointments: [],
            username: this.auth.name.replace(/^.*?_/, '').replace(/^./, c => c.toUpperCase()),
            count: null,
            selected_queue: "",
            slotAvailable:[],
            onServedQueue: {},
            isOnServed: false,
            queue_detail: null,
            isLoading : false,
            messages: {
                    en: EN,
                    id: ID,
                    },
            debounce: null,
            currentLocale: this.lang || "en",
            isShowModal: false,
            isSubmit: false,
            isShowCaller: false,
            inputValue: '',
            keyword: "",
            selectedAppointment: null,
            isShowCustomer: false,
            skipWatch: false,
            customerDetail: {
                name: '',
                email: '',
                bookingCode: '',
                phone: '',
                notes: '',
                status:'',
                service:'',
                date:'',
            }, 
            timer: 0,
            clock: {
                hours: 0,
                minutes: 0,
                seconds: 0
            },
            isShowCreate: false,
            form:{
                name:'',
                phone:'',
                email:'',
                notes:'',
                slot_id:'',
                service_id:'',
                date:'',
                user_id:this.auth.id
            },
            errors:{},
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
   async mounted() {
        await this.getQueues()
        const [selected_queue] = this.activeAppointments.filter(v => v.status === 'served');
        if (selected_queue) {
            this.selectQueue(selected_queue.number, true);
            this.timer = Math.floor(moment().diff(moment(selected_queue.called_at)) / 1000)
            this.startTimer(selected_queue)
        }
        Echo.channel(`event_appointment_queue_general.${this.branch.id}`)
        .listen("AppointmentQueue", () => {
            this.getQueues(); 
        })
    },
    computed: {
        filteredAppointments() {
        if (!this.keyword) return this.activeAppointments;

        return this.activeAppointments.filter((appointment) => {
            return (
                String(appointment.number).includes(this.keyword) ||
                appointment.name.toLowerCase().includes(this.keyword) ||
                appointment.booking_code.toLowerCase().includes(this.keyword) 
                );
            });
        },
        formattedDate() {
            return moment(this.lastLogin).format('D MMM YYYY') 
        },
        formattedTime() {
            return moment(this.lastLogin).format('HH:mm') 
        },
        serviceName() {
            return (this.services || [])
                .map(item => item?.name)
                .filter(name => !!name)
                .join(', ');
        },
        serviceList() {
            return (this.services || [])
                .map(service => {
                if (service.id && service.name) {
                    return {
                    id: service.id,
                    name: service.name,
                    };
                }
                return null;
                })
                .filter(service => service !== null);
        },
         minDate() {
            return moment().format('YYYY-MM-DD');
        }
    },
    watch: {
        selected_queue(next) {
            if (next != this.onServedQueue?.queue_no) {
                this.isOnServed = false;
            }
        },
       'form.service_id'(val) {
            if (!this.skipWatch) this.handleFormChange();
        },
        'form.date'(val) {
            if (!this.skipWatch) this.handleFormChange();
        },
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
        selectQueue(queue_no, manualSelect = false) {
            if(this.isOnServed && !manualSelect){
                return
            }
            this.selected_queue = queue_no;

            const current_queue = this.activeAppointments.filter(v => v.number === queue_no)[0] || null
            this.queue_detail = current_queue;
            if (current_queue && current_queue.status === 'served') {
                this.isOnServed = true
                this.onServedQueue = current_queue
            }
        },
        async getQueues() {
            this.isLoading = true;
            const finishedAppointmentStatus = ['end served', 'no show', 'canceled']
            
            const data = await axios.get('/cs/appointments')
            this.count = data.data.count
            this.finishAppointments = data.data.data.filter(v => {
                return finishedAppointmentStatus.includes(v.status)
            }).sort((a, b) => {
                if (a.number > b.number) return -1
                if (a.number < b.number) return 1

                return 0
            })

            this.activeAppointments = data.data.data.filter(v => {
                return !finishedAppointmentStatus.includes(v.status)
            })
                this.isLoading = false;
                let selected_queue = this.activeAppointments.find(v => v.status === 'served');
                if (!selected_queue) {
                    selected_queue = this.activeAppointments.find(v => v.status === 'check in');
                }
                if (!selected_queue && this.activeAppointments.length) {
                    selected_queue = this.activeAppointments[0];
                }
                this.selectQueue(selected_queue?.number || '', true);
            // console.log(this.activeAppointments)
        },
        startTimer(selected_queue) {
            const slaDuration = selected_queue?.service?.sla_duration * 60;

            this.timerInterval = setInterval(() => {
                this.timer++

                const seconds = Math.floor(this.timer % 3600 % 60)
                const minutes = Math.floor(this.timer % 3600 / 60)
                const hours = Math.floor(this.timer / 3600)

                if (!!slaDuration && this.timer >= slaDuration) {
                const coundownHours = this.$refs.coundownHours;
                coundownHours.classList.add('text-danger');
                coundownHours.classList.remove('text-dark');

                const coundownMinutes = this.$refs.coundownMinutes;
                coundownMinutes.classList.add('text-danger');
                coundownMinutes.classList.remove('text-dark');

                const coundownSeconds = this.$refs.coundownSeconds;
                coundownSeconds.classList.add('text-danger');
                coundownSeconds.classList.remove('text-dark');

                const over_sla = this.$refs.overSla;
                over_sla.classList.add('d-block');
                over_sla.classList.remove('d-none');
                }

                this.clock.seconds = seconds < 10 ? '0' + seconds : seconds
                this.clock.minutes = minutes < 10 ? '0' + minutes : minutes
                this.clock.hours = hours < 10 ? '0' + hours : hours
            }, 1000)
            },

            resetTimer() {
                clearInterval(this.timerInterval)
                this.timer = 0
                this.clock.hours = 0
                this.clock.minutes = 0
                this.clock.seconds = 0

                const coundownHours = this.$refs.coundownHours;
                coundownHours.classList.add('text-dark');
                coundownHours.classList.remove('text-danger');

                const coundownMinutes = this.$refs.coundownMinutes;
                coundownMinutes.classList.add('text-dark');
                coundownMinutes.classList.remove('text-danger');

                const coundownSeconds = this.$refs.coundownSeconds;
                coundownSeconds.classList.add('text-dark');
                coundownSeconds.classList.remove('text-danger');

                const over_sla = this.$refs.overSla;
                over_sla.classList.add('d-none');
                over_sla.classList.remove('d-block');
            },
            async fetchSlot(date,service_id){
               try {
                 const slot = await axios.post("/api/slot", {
                    date: date,
                    service_id: service_id,
                });
                this.slotAvailable = slot.data.data
                } catch (e) {
                    this.showAlert(
                        e.response.data?.message || 'Failed to fetch time slot',
                        'danger'
                    )
                } 
            },
             async handleFormChange() {
                if (this.form.service_id && this.form.date) {
                    await this.fetchSlot(this.form.date, this.form.service_id)
                    this.form.slot_id = this.slotAvailable ? this.slotAvailable[0].id : ''
                }
            },
             showCreateModal(){
                const today = moment().format('YYYY-MM-DD')
                this.form.service_id = this.serviceList.length > 0 ? this.serviceList[0].id : ''
                this.form.date = today
                this.isShowCreate = true
                this.skipWatch = true

                if(this.errors){
                    this.errors = {}
                }

                if(this.form.service_id){
                    this.fetchSlot(today,this.form.service_id).then(() => {
                        this.form.slot_id = this.slotAvailable ? this.slotAvailable[0].id : ''
                        this.skipWatch = false
                    })
                }
            },
            closeCallerModal(){
                this.isShowCaller = false
                this.inputValue = ''
            },
            handleInputCaller(value){
                this.inputValue += value
            },
              handleClearCaller(){
                this.inputValue = ''
                if(this.errors.queueNumber){
                    this.errors.queueNumber = ''
                }
            },
            handleCaller(){
                if(this.inputValue == '') return;
                this.errors = {}
                const [selected_queue] = this.activeAppointments.filter(v => v.number == this.inputValue);
                if(!selected_queue){
                    this.errors.queueNumber = 'Queue Not Found';
                    return;
                }
                this.selected_queue = selected_queue.number
                // console.log(selected_queue.status)
                this.closeCallerModal()
                if(selected_queue.status == 'book'){
                    this.onCheckIn(selected_queue.id)
                }else if(selected_queue.status == 'check in'){
                    this.onServed(selected_queue.id)
                }
            },

            closeCreateModal(){
                this.isShowCreate = false
                this.resetForm()
            },
            resetForm(){
                this.form.name = '';
                this.form.email = '';
                this.form.notes = '';
                this.form.phone = '';
            },
            validateForm() {
                this.errors = {};

                if (!this.form.slot_id) {
                this.errors.slot = 'Please select a time slot';
                }
                const selectedSlot = this.slotAvailable.find(slot => slot.id == this.form.slot_id)
                if (selectedSlot.filledSlot >= selectedSlot.max_slots) {
                    this.errors.slot = 'This time slot is not available';
                }

                if (!this.form.service_id) {
                this.errors.service_id = 'Please select a service';
                }
                if (!this.form.name) {
                this.errors.name = 'Name is required.';
                }
                if (!this.form.email) {
                this.errors.email = 'Email is required.';
                }
                else {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(this.form.email)) {
                        this.errors.email = 'Email format is invalid.';
                    }
                }
                if (!this.form.phone) {
                this.errors.phone = 'Phone is required.';
                }

                return Object.keys(this.errors).length === 0;
            },
        debounceSearch(event) {
                clearTimeout(this.debounce);
                this.debounce = setTimeout(() => {
                    this.keyword = event.target.value.trim().toLowerCase()
                }, 500);
            },
        async createData(){
                if (!this.validateForm()) {
                        return; 
                    }
                this.isLoading = true;
                this.isSubmit = true
                if(this.errors){
                    this.errors = {}
                }
                try{
                    await axios.post("/api/appointment", this.form).then((data) => {
                        if(!data.data.success){
                            this.errors.create = data.data.message
                            return
                        }
                        this.closeCreateModal()
                        // this.getQueues()
                    })
                } catch (error) {
                    alert(error.response.data.message);
                }
                    this.isLoading = false;
                    this.isSubmit = false
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
            const [selected_queue] = this.activeAppointments.filter(
                (appointment) => appointment.id === id
            );

            if (
                selected_queue &&
                selected_queue.status &&
                selected_queue.status != "check in"
            ) {
                alert("Status antrian salah");
                return;
            }

            this.isLoading = true;
            try {
                await axios.patch(`/cs/appointments/${id}/served`)

                this.showAlert('Appointment served')
                this.startTimer(selected_queue)
                audioRecorder.start()
                this.isOnServed = true;
            } catch (e) {
                this.showAlert(
                    e.response.data?.message || 'Failed to serve the appointment',
                    'danger'
                )
            }

           await this.getQueues()
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
            this.resetTimer()
            this.isOnServed = false
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
            this.isLoading = true
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
            this.isLoading = false

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

        showCustomerDetail({ name, email, phone, notes, date, booking_code, status, service }) {
            this.customerDetail.name = name
            this.customerDetail.email = email
            this.customerDetail.phone = phone
            this.customerDetail.notes = notes
            this.customerDetail.date = date ? 
                                        moment(date).format('D MMM YYYY') :
                                        null
            this.customerDetail.bookingCode = booking_code
            this.customerDetail.status = status
            this.customerDetail.service = service.name

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
    .btn-primary {
        color: #fff;
        background-color: #3787EA;
        border-color: #3787EA;
    }
</style>