<template>
  <div>
    <loading
      :active.sync="isLoading"
      color="#189DCD"
      :is-full-page="true"
      :width="64"
      :height="64"
    />
    <div class="row">
      <div class="col-md-6 d-flex justify-content-between">
        <div class="text-dark p-3">
          <p>{{ username }}</p>
          <h5 class="font-weight-bold">{{ serviceName }}</h5>
        </div>
      </div>

      <div class="col-md-6 d-flex">
          <div class="card shadow mb-4 mr-1">
            <div class="card-body font-weight-bold rounded p-3" style="width: 11rem;">
              <p>Waiting <i class="ml-4 text-success bi bi-people-fill"></i></p>
              <h6>{{ count?.waiting || '0' }}</h6>
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
        <h4>{{ t('Queue List') }}</h4>
      </div>
      <div class="col-md-6 mb-2 d-flex align-items-center">
          <input
            type="text"
            class="form-control"
            placeholder="Search.."
            @input="debounceSearch"/>
          <a
           href="javascript:void(0)" @click="showCreateModal()"
           class="btn btn-primary ml-2" style="white-space: nowrap; background-color:#3787EA ;">
               {{ this.t('Create New Queue') }}
             </a>
      </div>
      
      <div class="col-md-6 mb-3">
        <div class="card shadow rounded">
          <div class="card-body pt-0 table-responsive" style="max-height: 65vh;">
          <table class="table table-borderless">
            <thead>
              <tr class="sticky-top" style="background-color: white;">
                <th scope="col">No</th>
                <th scope="col">{{ t('Name') }}</th>
                <th scope="col">{{ t('Code') }}</th>
                <th scope="col">{{ t('Services') }}</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="queues.length > 0">
                      <tr
                        v-for="(queue, index) in queues"
                        :key="index"
                        @click="selectQueue(queue.queue_no)"
                        class="pointer"
                      >
                        <td>{{ queue.queue_no }}
                        </td>
                        <td style="max-width: 13rem;">
                            <div v-if="queue.name">
                              <!-- <a href="javascript:void(0)" @click="showCustomerDetail(queue)"> -->
                                {{ queue.name }}
                              <!-- </a> -->
                            </div>
                            <div v-else>
                                -
                            </div>
                        </td>
                        <td>
                           {{ queue.booking_code.toUpperCase()}}
                        </td>
                        <td>
                          {{ queue.service.name }}
                        </td>
                        <td>

                          <div class="d-flex align-items-center">

                          <span
                            style="
                                  border-radius: 20px;
                                  background-color: #3B82F61A;
                                  color: #3787EA ;
                                  padding: 2px 15px;
                              "
                            v-show="queue.status == 'served'"
                            >{{ t('Served') }}</span
                          >
                          <span
                            style="
                                  border-radius: 20px;
                                  background-color: #FEF7DC;
                                  color: #E29E15;
                                  padding: 2px 15px;
                              "
                            v-show="queue.status == 'requeue'"
                            >{{ t('Requeue') }}</span>
                            <span
                            class="badge badge-warning ml-1"
                            v-if="queue.requeue_count > 0 && queue.status !== 'requeue'"
                            >U</span>
                          </div>
                        </td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="5">
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
      <div class="col-md-6 mb-3">
        <div class="card shadow rounded">
          <div class="card-header">
            {{ t('Current Queue') }}
          </div>
          <div class="card-body">
            <div class=" d-flex flex-wrap justify-content-between" v-if="queue_detail">
              <div style="width: 20rem;" class="mb-3">
                <div class="d-flex">
                  <h6 class="mr-1 text-primary" style="background-color: #E6F3FF; 
                                                      padding: 0.2rem 0.7rem; 
                                                      border-radius: 2rem;
                                                      align-content: center;">
                    {{queue_detail.queue_no }}
                  </h6>
                  -
                  <h6  class="ml-1 font-weight-bold" style=" padding: 0.3rem 0rem;">{{ queue_detail.name || '' }}</h6>
                </div>
                <p>{{ queue_detail.service.name || ''}}</p>
                <a href="javascript:void(0)" @click="showCustomerDetail(queue_detail)">{{ t('See Customer Detail') }}</a>
              </div>

              <div v-if="auth.branch.branch_type.is_premium">
                <div class="d-flex">
                  <small class="d-none text-danger mr-2 font-weight-bold mb-2 align-content-center" 
                          style="font-size: 12px;"
                          ref="overSla">
                    <i class="bi bi-exclamation-circle ml-1"></i> Over SLA</small>
                  <p class="text-dark text-right font-weight-bold mb-2">{{ this.t('Service Duration') }}</p>
                </div>

                <div class="d-flex justify-content-between" style="max-width: 200px; gap: 20px;">
                  <div class="text-center" style="width: 60px;">
                    <h4 class="h3 font-weight-bold mb-0 text-dark border" ref="coundownHours">{{ clock.hours || '00' }}</h4>
                    <span>{{ this.t('Hour') }}</span>
                  </div>
                  <div class="text-center">
                    <h4 class="h3 font-weight-bold mb-0 text-dark border" ref="coundownMinutes">{{ clock.minutes || '00' }}</h4>
                    <span>{{ this.t('Minutes') }}</span>
                  </div>
                  <div class="text-center">
                    <h4 class="h3 font-weight-bold mb-0 text-dark border" ref="coundownSeconds">{{ clock.seconds || '00' }}</h4>
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

            <div >
                <div v-if="queue_detail">
             <template v-if="!isOnTransfer">
                  <div class="d-flex mb-3" v-if="!isOnServed">
                    <button
                      class="btn btn-primary fullwidth mb-2 mr-2"
                      style="background-color: #3787EA;"
                      @click="serving_directly ? onServed() : onCall()"
                      :disabled="!selected_queue || selected_queue.status"
                    >
                       {{ this.t('Call Queue') }}
                    </button>
                    <button @click="showCallerModal()" class="btn btn-outline-primary mb-2">
                      <i class="bi bi-calculator"></i>
                    </button >
                  </div>

                  <template v-else>
                    <div class="row">
                      <div class="col-md-4">
                        <button
                          class="btn btn-outline-info fullwidth mb-3"
                          @click="onRecall"
                        > <i class="bi bi-volume-up"></i>
                        <span class="text-dark">{{ t('Recall') }}</span>
                        </button>
                      </div>
                      <div class="col-md-4">
                        <button
                          class="btn btn-outline-secondary fullwidth mb-3"
                          @click="onRequeue"
                          :disabled="
                            onServedQueue &&
                            onServedQueue.requeue_count >= max_requeue
                          "
                        >
                         <i class="bi bi-list-ul"></i>
                         <span class="text-dark"> {{ t('Requeue') }}</span>
                        </button>
                      </div>
                      <div class="col-md-4">
                        <button
                          class="btn btn-outline-warning fullwidth mb-3"
                          @click="onTransfer"
                          :disabled="!allow_transfer"
                        >
                        <i class="bi bi-telephone-outbound"></i>
                        <span class="text-dark">{{ t('Transfer') }}</span>
                        </button>
                      </div>
                      <div class="col-md-4">
                        <button
                          class="btn btn-outline-success fullwidth mb-3"
                          @click="onEndServed"
                          :disabled="recallCounter > max_recall"
                        >
                        <i class="bi bi-check-circle"></i>
                        <span class="text-dark"> {{ t('End Serve') }}</span>
                        </button>
                      </div>
                      <div class="col-md-4">
                        <button
                          class="btn btn-outline-danger fullwidth mb-3"
                          @click="onNoShow"
                        >
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="text-dark"> {{ t('No Show') }}</span>
                        </button>
                      </div>
                      <div class="col-md-4" :class="{'d-none': isServed()}">
                        <button
                          class="btn btn-outline-primary fullwidth mb-3"
                          @click="onServed()"
                        >
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="text-dark">{{ t('Start') }}</span>
                        </button>
                      </div>
                    </div>
                    </template>
                </template>
                
                <div class="p-3" 
                style="background-color: #E6F3FF; border-radius: 10px;"
                v-if="accessible_features.includes('CRM Interaction')"
                :class="{'d-none': !isOnServed}">
                  <!-- <div v-if="accessible_features.includes('CRM Interaction')"> -->
                  <b class="text-primary mt-5"> {{ t('Sub Service') }}</b>
                  <hr class="mt-2 mb-2">
                  <div class="row">
                    <div class="col-md-12" >
                      <select class="custom-select" v-model="selected_sub_service">
                        <option selected value="">--{{ t('SELECT SUB SERVICE') }}--</option>
                        <option v-for="subService in filteredSubServices" :key="subService.id" :value="subService.id">
                          {{ subService.name }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                </div>
            </div>

          </div>
        </div>
      </div>

    </div>

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
                              class="badge badge-pill badge-secondary p-2"
                              v-show="customerDetail.status == 'waiting'"
                              >{{ t('Waiting') }}</span>

                            <span
                              class="badge badge-pill badge-info  p-2"
                              v-show="customerDetail.status == 'served'"
                              >{{ t('Being Served') }}</span
                            >
                            <span
                              class="badge badge-pill badge-warning p-2"
                              v-show="customerDetail.status == 'requeue'"
                              >{{ t('Requeue') }}</span>
                            <span
                              class="badge badge-pill badge-danger p-2"
                              v-show="customerDetail.status == 'no show'"
                              >{{ t('No Show') }}</span
                            >
                            <span
                              class="badge badge-pill badge-success p-2"
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
                              <p class="mb-1">{{ t('Date of Birth') }}</p>
                              <div class="text-dark font-weight-bold">{{ customerDetail.dateOfBirth || '-' }}</div>
                            </div>
                          </div>
                        </div>

                          <div class="row g-2">
                            <div class="col-md-6 mb-2">
                              <div class="border rounded p-2">
                                <p class="mb-1">{{ t('Address') }}</p>
                                <div class="text-dark font-weight-bold">{{ customerDetail.address || '-' }}</div>
                              </div>
                            </div>
                            <div class="col-md-6 mb-2">
                              <div class="border rounded p-2">
                                <p class="mb-1">{{ t('Reason for Visit') }}</p>
                                <div class="text-dark font-weight-bold">{{ customerDetail.reasonForVisit || '-' }}</div>
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
         <!-- Show Queue detail -->
    <!-- <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isShowQueue">
             <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" style="max-width: 900px;">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ t('Queue Details') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isShowQueue = false">
                         <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
 
                     <div class="modal-body">
                         <div class="row justify-content-between mb-4">
                          <div class="col-md-7">
                              <h6>Nomor Antrian</h6>
                              <h5 class="text-dark font-weight-bold">{{ queue_detail.queue_no }}</h5>
                          </div>
                          <div class="col-md-4 text-right align-content-center">
                            <span
                              class="badge badge-pill badge-secondary p-2"
                              v-show="queue_detail.status == 'waiting'"
                              >{{ t('Waiting') }}</span>

                            <span
                              class="badge badge-pill badge-info  p-2"
                              v-show="queue_detail.status == 'served'"
                              >{{ t('Being Served') }}</span
                            >
                            <span
                              class="badge badge-pill badge-warning p-2"
                              v-show="queue_detail.status == 'requeue'"
                              >{{ t('Requeue') }}</span>
                            <span
                              class="badge badge-pill badge-danger p-2"
                              v-show="queue_detail.status == 'no show'"
                              >{{ t('No Show') }}</span
                            >
                            <span
                              class="badge badge-pill badge-success p-2"
                              v-show="queue_detail.status == 'end served'"
                              >{{ t('End Served') }}</span
                            >
                          </div>
                         </div>
 
                         <div class="row justify-content-between mb-2 p-2">
                            <div class="col-md-3 mb-3">
                               <div class="border rounded p-2">
                                 <p class="mb-1">{{ t('Date') }}</p>  
                                 <div class="text-dark font-weight-bold">{{ queue_detail.date }}</div>
                               </div>
                            </div>
                             <div class="col-md-3 mb-3">
                               <div class="border rounded p-2">
                                 <p class="mb-1">{{ t('Service') }}</p>  
                                 <div class="text-dark font-weight-bold">{{ queue_detail.service.name }}</div>
                               </div>
                            </div>
                            <div class="col-md-3 mb-3">
                               <div class="border rounded p-2">
                                 <p class="mb-1">{{ t('Booking Code') }}</p>  
                                 <div class="text-dark font-weight-bold">{{ queue_detail.booking_code.toUpperCase() }}</div>
                               </div>
                            </div>
                             <div class="col-md-3 mb-3">
                               <div class="border rounded p-2">
                                 <p class="mb-1">{{ t('Counter') }}</p>  
                                 <div class="text-dark font-weight-bold">{{ this.workstation.id == queue_detail.workstation_id ? this.workstation.label : '-' }}</div>
                               </div>
                            </div>
                            <div class="col-md-12 text-center py-4" style="background-color:#E6F3FF;">
                              <template v-if="qrCodeImage" >
                                  <div>
                                    <p>Scan disini untuk melihat status antrian</p>
                                    <div v-html="qrCodeImage"></div>
                                  </div>
                              </template>
                            </div>
                         </div>
                         
                     </div>
 
                     <div class="modal-footer">
                         <button type="button" class="btn btn-danger" data-dismiss="modal" @click="isShowQueue = false">{{ t('Close') }}</button>
                     </div>
                 </div>
             </div>
         </div> -->
         <!-- Show Create Modal -->
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
                                <label for="workstation_service_id">{{ t('Service') }}</label>
                                <select name="workstation_service_id" class="form-control" v-model="form.workstation_service_id">
                                        <option v-for="service in serviceList" :key="service.id" :value="service.id">{{ service.name }}</option>
                                </select>
                                <small class="text-danger" v-if="errors.workstation_service_id">{{ errors.workstation_service_id }}</small>
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
         <!-- Show Transfer Modal -->
     <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="isOnTransfer">
             <div class="modal-dialog modal-lg modal-dialog-centered">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ t('Transfer Queue') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="isOnTransfer = false">
                         <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
 
                     <form @submit.prevent="onSubmitTransfer">
                     <div class="modal-body">
                         <div class="row mb-2">
                             <div class="col-md-3 p-3 border border-rounded mb-3">
                               <h6>{{ t('Queue Number') }}</h6>
                               <h5 class="font-weight-bolder text-dark">{{ selected_queue }}</h5>
                             </div>
                             <div class="col-md-1 d-flex justify-content-center align-items-center">
                                <i class="bi bi-arrow-left-right"></i>
                             </div>
                            <div class="col">
                                <input
                                      type="hidden"
                                      name="queue_no"
                                      v-model="selected_queue"
                                    />
                                    <div class="form-group">
                                      <label for="services">{{ t('Select Service at the Counter') }}</label>
                                      <select
                                        name="workstation_service_id"
                                        id="workstation_service_id"
                                        class="form-control"
                                      >
                                        <option
                                          v-for="workstationService in workstationServices"
                                          :key="workstationService.id"
                                          :value="workstationService.id"
                                        >
                                          {{ workstationService.service.name }}
                                        </option>
                                      </select>
                                    </div>
                                </div>
                          </div>
                        </div>
                        
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-danger" data-dismiss="modal" @click="isOnTransfer = false">{{ t('Close') }}</button>
                          <button class="btn btn-primary" type="submit"
                          :disabled="isLoading">{{ t('Save') }}</button>
                        </div>
                    </form>
                 </div>
             </div>
         </div>
         <!-- Show Modal Call -->
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
                            <template v-if="callerLayer == 1">
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
                            </template>
                            <template v-if="callerLayer == 2">
                              <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('A')">A</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('B')">B</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('C')">C</button>
                            </div>
                            <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('D')">D</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('E')">E</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('F')">F</button>
                            </div>
                            <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('G')">G</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('I')">I</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('J')">J</button>
                            </div>
                            <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('K')">K</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('L')">L</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('M')">M</button>
                            </div>
                            <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('N')">N</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('O')">O</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('P')">P</button>
                            </div>
                            </template>
                            <div class="col-md-12 px-0 d-flex justify-content-center" style="gap: 1rem;">
                                <button class="btn btn-outline-primary w-75" @click="handleSwitch">{{ callerLayer == 1 ? 'ABC' : '123' }}</button>
                                <button class="btn btn-outline-primary w-75" @click="handleInputCaller('0')">0</button>
                                <button class="btn btn-outline-danger w-75" @click="handleClearCaller">Clear</button>
                            </div>
                         </div>
                         
                     </div>
                 </div>
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
// Toast component
import VueToast from "vue-toast-notification";
import "vue-toast-notification/dist/theme-sugar.css";
import moment from 'moment';
import ID from "../../lang/id.json";
import EN from "../../lang/en.json";

Vue.use(VueToast);

export default {
  components: {
    Loading,
  },

  props: {
    max_recall: {
      type: Number,
      default: 0
    },
    max_requeue: {
      type: Number,
      default: 0
    },
    allow_transfer: {
      type: Boolean,
      default: false
    },
    serving_directly: {
      type: Boolean,
      default: false
    },
    auth: {
      type: Object,
      required: true
    },
    accessible_features: {
      type: Array,
      required: true
    },
    sub_services: {
      type: Array,
    },
    workstation: {
      type: Object,
      required: true
    },
    lang:{
      type: String,
    }
  },

  data() {
    return {
      isLoading: true,
      isSubmit: false,
      callerLayer: 1,
      username: this.auth.name.replace(/^.*?_/, ''),
      queue_detail: null,
      inputValue: '',
      qrCodeImage: '',
      count: null,
      keyword: "",
      currentLocale: this.lang || "en",
      messages: {
              en: EN,
              id: ID
      },
      debounce: null,
      recallCounter: 0,
      queues: [],
      selected_queue: "",
      selected_sub_service: "",
      filteredSubServices: [],
      isOnServed: false,
      onServedQueue: {},
      isOnTransfer: false,
      workstationServices: [],
      manualInput: false,
      clock: {
        hours: 0,
        minutes: 0,
        seconds: 0
      },
      isShowCaller: false,
      isShowCustomer: false,
      isShowQueue: false,
      isShowCreate: false,
      customerDetail: {
        name: '',
        email: '',
        phone: '',
        dateOfBirth: '',
        address: '',
        bookingCode: '',
        reasonForVisit: ''
      },
      form:{
        name:'',
        phone:'',
        workstation_service_id:'',
        vct_id:this.auth.id
      },
      errors:{},
      timer: 0,
      timerInterval: null,
      audioChunks: [],
      mediaRecorder: null,
    };
  },

  async mounted () {
    await this.getQueues();
    const [selected_queue] = this.queues.filter(v => v.status === 'served');
    if (selected_queue) {
      this.selectQueue(selected_queue.queue_no);
      this.timer = Math.floor(moment().diff(moment(selected_queue.called_at)) / 1000)
      this.startTimer(selected_queue)
    }
  },
  computed: {
    serviceName() {
      return (this.workstation?.workstation_service || [])
        .map(item => item.service?.name)
        .filter(name => !!name)
        .join(', ');
    },
    serviceList() {
      return (this.workstation?.workstation_service || [])
        .map(item => {
          const service = item.service;
          if (service && service.name) {
            return {
              id: item.id,
              name: service.name,
            };
          }
          return null;
        })
        .filter(item => item !== null);
    },
     formattedDate() {
      return moment(this.lastLogin).format('D MMM YYYY') 
    },
    formattedTime() {
      return moment(this.lastLogin).format('HH:mm') 
    }
  },


  created () {
    Echo.private(`event_direct_queue.${this.auth.branch_id}`).listen(
      "VCTDirectQueue",
      (data) => {
        this.$toast.open({
          message: "Antrian baru ditambahkan",
          type: "info",
          duration: 3000,
          dismissible: true,
        });
        this.getQueues();
      }
    );

    Echo
      .channel(`event_direct_queue_general.${this.auth.branch_id}`)
      .listen('QueueStatusUpdated', () => {
        this.getQueues();
      });

    this.initVoiceRecorder()
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
    async initVoiceRecorder() {
      const media = await navigator.mediaDevices.getUserMedia({
        audio: true,
        video: false
      })

      this.mediaRecorder = new MediaRecorder(media)

      this.mediaRecorder.addEventListener('dataavailable', this.handleDataAvailable)
    },

    isServed() {
        return this.queues.filter(queue => queue.queue_no === this.selected_queue && queue.called_at).length > 0;
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

    async getQueues() {
      this.isLoading = true;
      const data = await axios.get(`/cs/directQueue?keyword=${this.keyword}`);
      this.queues = data.data.data;
      this.selected_sub_service = '';
      this.count = data.data.count
      this.isLoading = false;

      let selected_queue = this.queues.find(v => v.status === 'served');
      if (!selected_queue && this.queues.length) {
        selected_queue = this.queues[0];
      }
      this.selectQueue(selected_queue?.queue_no || '');
    },
    async getQRCode(queue_id) {
      const data = await axios.get(`/cs/directQueue/getQRCode/${queue_id}`);
      this.qrCodeImage = data.data.image
    },

    debounceSearch(event) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.keyword = event.target.value;
        this.getQueues();
      }, 500);
    },

    selectQueue(queue_no) {
      if(this.isOnServed){
        return
      }
      this.manualInput = true;
      this.selected_queue = queue_no;

      const current_queue = this.queues.filter(v => v.queue_no === queue_no)[0] || null
      this.queue_detail = current_queue;
      if (current_queue && current_queue.status === 'served') {
        this.isOnServed = true
        this.onServedQueue = current_queue
      }
    },

    async onCall() {
      const [selected_queue] = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      if (
        selected_queue &&
        selected_queue.status &&
        selected_queue.status != "waiting" &&
        selected_queue.status != "served" &&
        selected_queue.status != "requeue"
      ) {
        alert("Status antrian salah");
        return;
      }

      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onCall", {
          queue_no: this.selected_queue,
          service_id: selected_queue.service_id,
          is_skip: this.manualInput,
          workstation_id: this.workstation.id
        });

        this.onServedQueue = queue.data.data;
        this.isOnServed = true;
        this.getQueues();

      } catch (error) {
        this.getQueues();
        console.error(error);
      }
      this.isLoading = false;
    },

    async onServed() {
      const [selected_queue] = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );

      if (
        selected_queue &&
        selected_queue.status &&
        selected_queue.status != "waiting" &&
        selected_queue.status != "served" &&
        selected_queue.status != "requeue"
      ) {
        alert("Status antrian salah");
        return;
      }

      this.isLoading = true;
      try {
        const queue = await axios.post("/cs/directQueue/onServed", {
          queue_no: this.selected_queue,
          service_id: selected_queue.service_id,
          is_skip: this.manualInput,
          workstation_id: this.workstation.id,
          waiting_duration: Math.floor(moment().diff(moment(selected_queue.created_at)) / 1000),
selected_queue: selected_queue.created_at
        });

        this.onServedQueue = queue.data.data;
        this.isOnServed = true;

      const filteredSubServices = this.sub_services.filter(
        (subService) => subService.pivot.service_id === selected_queue.service_id
      );

      this.filteredSubServices = filteredSubServices;

        this.getQueues();

        if (
          this.auth.branch.branch_type.is_premium &&
          this.accessible_features.includes('Voice Recording') &&
          this.mediaRecorder?.state === 'inactive'
        ) {
          this.audioChunks = []
          this.mediaRecorder.start()
        }

        this.startTimer(selected_queue)
      } catch (error) {
        this.getQueues();
        console.error(error);
      }
      this.isLoading = false;
    },

    async onRecall() {
      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;

      try {
        const queue = await axios.post("/cs/directQueue/onRecall", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id
        });
        this.onServedQueue = queue.data.data;

        await this.getQueues();

        if (this.onServedQueue.recall_count >= this.max_recall) {
          this.selected_queue = this.queues[0]?.queue_no || "";
          this.isOnServed = false;

          this.resetTimer()
        } else {
          this.isOnServed = true;
        }
        this.recalCounter++;
      } catch (error) {
        alert(error.response.data.message);
      }

      this.isLoading = false;
    },

    async onEndServed() {
      const [selected_queue] = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );

      this.isLoading = true;

      try {
        if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
          this.mediaRecorder.stop()
          this.mediaRecorder.addEventListener('stop', this.handleRecorderStop)
        }

        const queue = await axios.post("/cs/directQueue/onEndServed", {
          queue_no: this.selected_queue,
          service_id: selected_queue.service_id,
          sub_service_id: this.selected_sub_service,
          serving_duration: selected_queue.called_at ? Math.floor(moment().diff(moment(selected_queue.called_at)) / 1000) : 0
        });

        this.resetTimer()

        this.onServedQueue = null;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }

      this.isLoading = false;
    },

    async onRequeue() {
      if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
        this.mediaRecorder.stop()
      }
      this.resetTimer()

      const selected_queue = this.queues.find(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;

      try {
        const queue = await axios.post("/cs/directQueue/onRequeue", {
          queue_no: this.selected_queue,
          service_id: selected_queue.service_id
        });

        if (this.onServedQueue.requeue_count >= this.max_requeue) {
          this.isOnServed = false;
        } else {
          this.isOnServed = true;
        }
        this.onServedQueue = null;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }

      this.isLoading = false;
    },

    async onNoShow() {
      if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
        this.mediaRecorder.stop()
      }
      this.resetTimer()

      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === this.selected_queue
      );
      this.isLoading = true;

      try {
        const queue = await axios.post("/cs/directQueue/onNoShow", {
          queue_no: this.selected_queue,
          service_id: selected_queue[0].service_id
        });

        this.onServedQueue = null;
        this.isOnServed = false;
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }

      this.isLoading = false;
    },

    async onTransfer() {
      this.isLoading = true;

      try {
        const workstationServices = await axios.get(
          "/cs/directQueue/allWorkstationServices"
        );
        this.workstationServices = workstationServices.data.data;
        this.isOnTransfer = true;
      
      } catch (error) {
        alert(error.response.data.message);
      }

      this.isLoading = false;
    },

    async onSubmitTransfer(e) {
      this.isLoading = true;
      if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
        this.mediaRecorder.stop()
      }
      this.resetTimer()

      const selected_queue = this.queues.filter(
        (queue) => queue.queue_no === e.target.queue_no.value
      );

      try {
        const data = {
          queue_no: e.target.queue_no.value,
          workstation_service_id: e.target.workstation_service_id.value,
          service_id: selected_queue[0].service_id,
          serving_duration: selected_queue.called_at ? Math.floor(moment().diff(moment(selected_queue.called_at)) / 1000) : 0,
          sub_service_id: this.selected_sub_service,
        };
        await axios.post("/cs/directQueue/onTransfer", data);
        this.isOnServed = false;
        this.isOnTransfer = false;
        this.selected_queue = "";
        this.getQueues();
      } catch (error) {
        alert(error.response.data.message);
      }

      this.isLoading = false;
    },

    showCustomerDetail({name, email, phone, status, service ,booking_code, appointment_onsite}) {
        this.customerDetail.name = name
        this.customerDetail.email = email
        this.customerDetail.phone = phone
        this.customerDetail.dateOfBirth =appointment_onsite ? 
                                          moment(appointment_onsite.date_of_birth).format('D MMM YYYY') :
                                          null
        this.customerDetail.address = appointment_onsite?.address
        this.customerDetail.bookingCode = appointment_onsite ? appointment_onsite.booking_code : booking_code;
        this.customerDetail.reasonForVisit = appointment_onsite?.reason_for_visit
        this.customerDetail.status = status
        this.customerDetail.service = service.name

        this.isShowCustomer = true
    },
    showQueueDetail(queue_detail) {
      if(this.qrCodeImage){
        this.qrCodeImage = ''
      }
        this.queue_detail.date = queue_detail.appointment_onsite ? 
                                moment(queue_detail.appointment_onsite.date).format('d MMM YYYY') :
                                moment(queue_detail.created_at).format('d MMM YYYY')
        this.getQRCode(queue_detail.id)
        this.isShowQueue = true
    },

    showCreateModal(){
      this.form.workstation_service_id = this.serviceList.length > 0 ? this.serviceList[0].id : ''
      this.isShowCreate = true
    },

    showCallerModal(){
      this.isShowCaller = true
    },

    handleSwitch(){
      if (this.callerLayer == 1) {
        this.callerLayer = 2;
      }else{
        this.callerLayer = 1;
      }
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
      const [selected_queue] = this.queues.filter(v => v.queue_no === this.inputValue);
      if(!selected_queue){
        this.errors.queueNumber = 'Queue Not Found';
        return;
      }
      this.selected_queue = selected_queue.queue_no
      this.closeCallerModal()
      this.onCall()
    },

    closeCreateModal(){
       this.isShowCreate = false
       this.resetForm()
    },

    closeCallerModal(){
       this.isShowCaller = false
       this.inputValue = ''
    },

    resetForm(){
       this.form.name = '';
       this.form.phone = '';
       this.form.workstation_service_id = '';
    },

     validateForm() {
        this.errors = {};

        if (!this.form.name) {
          this.errors.name = 'Name is required.';
        }

        if (!this.form.phone) {
          this.errors.phone = 'Phone is required.';
        }

        if (!this.form.workstation_service_id) {
          this.errors.workstation_service_id = 'Please select a service.';
        }

        return Object.keys(this.errors).length === 0;
      },


    async createData(){
      if (!this.validateForm()) {
            return; 
          }
      this.isLoading = true;
      this.isSubmit = true
      try{
        await axios.post("/cs/directQueue", this.form);
        this.closeCreateModal()
        this.getQueues()
      } catch (error) {
        alert(error.response.data.message);
      }
        this.isLoading = false;
        this.isSubmit = false
    },

    handleDataAvailable(e) {
      this.audioChunks.push(e.data)
    },

    handleRecorderStop() {
      const audioFileReader = new FileReader()

      audioFileReader.readAsDataURL(new Blob(this.audioChunks, {
        type: 'audio/mpeg'
      }))

      const self = this
      audioFileReader.onloadend = async function () {
        const selectedQueue = self.queues.find(q => q.queue_no === self.selected_queue)

        await axios.post('/cs/voice-recorder', {
          customer_name: selectedQueue.name,
          duration: self.timer,
          message: audioFileReader.result.split(',')[1],
          branch_id: self.auth.branch_id,
          branch_name: self.auth.branch.name,
          workstation_id: self.workstation.id,
          workstation_name: self.workstation.label,
          vct_id: self.auth.id,
          vct_name: self.auth.name,
          queue_id: selectedQueue.id,
          queue_type: 'direct_queue',
          queue_no: selectedQueue.queue_no
        }, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('accessToken')}`
          }
        })

        self.audioChunks = []
      }

      this.mediaRecorder.removeEventListener('dataavailable', this.handleDataAvailable)
      this.mediaRecorder.removeEventListener('stop', this)
    }
  },

  watch: {
    selected_queue(next) {
      if (next != this.onServedQueue?.queue_no) {
        this.isOnServed = false;
      }
    },
  },
};
</script>

<style>
.pointer {
  cursor: pointer;
}
.btn-outline-primary {
  color: #3787EA ;
  border-color: #3787EA ;
}

.btn-outline-primary:hover {
  color: #fff;
  background-color: #3787EA ;
  border-color: #3787EA ;
}
.btn-primary {
  color: #fff;
  background-color: #3787EA;
  border-color: #3787EA;
}
</style>
