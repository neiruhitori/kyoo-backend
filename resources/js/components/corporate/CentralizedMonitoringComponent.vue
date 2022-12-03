<template>
  <div>
    <div class="mb-4">
      <h3>Monitoring Terpusat</h3>
    </div>

    <form class="mb-4" @submit.prevent="handleFilter">
      <div class="form-row align-items-end">
        <div class="col-auto">
          <label for="deparmentId">Cabang</label>
          <select
            class="form-control"
            style="width: 180px;"
            autocomplete="off"
            v-model="branchId"
          >
            <option :value="null">Semua Cabang</option>
            <option
              v-for="branchOption in branchOptions"
              :key="branchOption.value"
              :value="branchOption.value"
            >
              {{ branchOption.label }}
            </option>
          </select>
        </div>

        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </div>
    </form>

    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center">
            <label for="refreshInterval" class="mr-3 mb-0" style="white-space: nowrap;">Refresh / menit</label>
            <select
              id="refreshInterval"
              class="form-control"
              autocomplete="off"
              style="max-width: 70px"
              v-model="refreshDuration"
            >
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="15">15</option>
            </select>
          </div>

          <div class="ml-3 badge badge-secondary" style="font-size: .875rem;">
            <span class="fas fa-redo"></span>
            <span class="ml-2">{{ lastRefreshedAt }}</span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0" v-if="isBranchTable">
            <thead>
              <tr>
                <th rowspan="2" class="align-middle">Cabang</th>
                <th rowspan="2" class="align-middle text-right">Menunggu</th>
                <th rowspan="2" class="align-middle text-right">Dilayani</th>
                <th rowspan="2" class="align-middle text-right">Tidak Hadir</th>
                <th colspan="3" class="text-center">Waktu Tunggu</th>
                <th colspan="3" class="text-center">Waktu Melayani</th>
              </tr>

              <tr>
                <th class="text-center">Saat Ini</th>
                <th class="text-center">Rata-Rata</th>
                <th class="text-center">Terlama</th>

                <th class="text-center">Saat Ini</th>
                <th class="text-center">Rata-Rata</th>
                <th class="text-center">Terlama</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="dt in data" :key="dt.id">
                <td>
                  <span
                    :class="'d-inline-block mr-2 ' + (dt.is_today_open ? 'bg-success' : 'bg-danger')"
                    style="width: 1rem; height: 1rem;"
                  ></span>

                  <a href="javascript:void(0)" @click="() => handleBranchClick(dt.id)">
                    {{ dt.name }}
                  </a>
                </td>
                <td class="text-right">{{ dt.totalWaiting }}</td>
                <td class="text-right">{{ dt.totalServed }}</td>
                <td class="text-right">{{ dt.totalNoShow }}</td>
                <td class="text-center">{{ dt.nowWaitingDuration }}</td>
                <td class="text-center">{{ dt.avgWaitingDuration }}</td>
                <td class="text-center">{{ dt.maxWaitingDuration }}</td>
                <td class="text-center">{{ dt.nowServingDuration }}</td>
                <td class="text-center">{{ dt.avgServingDuration }}</td>
                <td class="text-center">{{ dt.maxServingDuration }}</td>
              </tr>

              <tr v-if="!data.length">
                <td colspan="10" class="text-center">Tidak ada data</td>
              </tr>
            </tbody>
          </table>

          <table class="table table-bordered table-striped mb-0" v-else>
            <thead>
              <tr>
                <th rowspan="2" class="align-middle">Layanan</th>
                <th rowspan="2" class="align-middle">Departemen</th>
                <th rowspan="2" class="align-middle text-right">Menunggu</th>
                <th rowspan="2" class="align-middle text-right">Dilayani</th>
                <th rowspan="2" class="align-middle text-right">Tidak Hadir</th>
                <th colspan="3" class="text-center">Waktu Tunggu</th>
                <th colspan="3" class="text-center">Waktu Melayani</th>
              </tr>

              <tr>
                <th class="text-center">Saat Ini</th>
                <th class="text-center">Rata-Rata</th>
                <th class="text-center">Terlama</th>

                <th class="text-center">Saat Ini</th>
                <th class="text-center">Rata-Rata</th>
                <th class="text-center">Terlama</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="dt in data" :key="dt.id">
                <td>{{ dt.name }}</td>
                <td>{{ dt.department.name }}</td>
                <td class="text-right">{{ dt.totalWaiting }}</td>
                <td class="text-right">{{ dt.totalServed }}</td>
                <td class="text-right">{{ dt.totalNoShow }}</td>
                <td class="text-center">{{ dt.nowWaitingDuration }}</td>
                <td class="text-center">{{ dt.avgWaitingDuration }}</td>
                <td class="text-center">{{ dt.maxWaitingDuration }}</td>
                <td class="text-center">{{ dt.nowServingDuration }}</td>
                <td class="text-center">{{ dt.avgServingDuration }}</td>
                <td class="text-center">{{ dt.maxServingDuration }}</td>
              </tr>

              <tr v-if="!data.length">
                <td colspan="10" class="text-center">Tidak ada data</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    corporate: Object
  },

  mounted() {
    this.setData(this.branchId)
    this.setBranchOptions()

    setTimeout(() => {
      this.handleTimeout()
    }, this.refreshDuration * 60000)
  },

  data() {
    return {
      branchId: null,
      isBranchTable: true,
      data: [],
      branchOptions: [],
      refreshedAt: new Date(),
      refreshDuration: 5
    }
  },

  computed: {
    lastRefreshedAt() {
      return moment(this.refreshedAt).format('HH:mm:ss')
    },
  },

  methods: {
    async setData(branchId) {
      this.setLastRefreshedAt()

      if (branchId) {
        this.data = await this.fetchServiceByBranchId(branchId)
        this.isBranchTable = false
      } else {
        this.data = await this.fetchBranches()
        this.isBranchTable = true
      }

      this.data = this.data.map(v => {
        v.nowWaitingDuration = this.formatTime(v.nowWaitingDuration)
        v.maxWaitingDuration = this.formatTime(v.maxWaitingDuration)
        v.avgWaitingDuration = this.formatTime(v.avgWaitingDuration)
        v.nowServingDuration = this.formatTime(v.nowServingDuration)
        v.maxServingDuration = this.formatTime(v.maxServingDuration)
        v.avgServingDuration = this.formatTime(v.avgServingDuration)

        return v
      })
    },

    async setBranchOptions() {
      const branches = await this.fetchCorporateBranches(this.corporate.id)

      this.branchOptions = branches.map(v => {
        return {
          label: v.name,
          value: v.id
        }
      })
    },

    formatTime(seconds) {
      let hours = Math.floor(seconds / 3600),
          minutes = Math.floor((seconds % 3600) / 60),
          secs = Math.floor(seconds % 3600 % 60)
      
      if (hours < 10) hours = '0' + hours
      if (minutes < 10) minutes = '0' + minutes
      if (secs < 10) secs = '0' + secs

      return hours + ':' + minutes + ':' + secs
    },

    handleTimeout() {
      this.setData(this.branchId)

      setTimeout(() => {
        this.handleTimeout()
      }, this.refreshDuration * 60000)
    },

    handleFilter() {
      this.setData(this.branchId)
    },

    handleBranchClick(branchId) {
      this.branchId = branchId
      this.setData(branchId)
    },

    setLastRefreshedAt() {
      this.refreshedAt = new Date()
    },

    fetchBranches() {
      return axios.get('/admin-corporate/monitoring/branches').then(res => res.data)
    },

    fetchServiceByBranchId(branchId) {
      return axios.get(`/admin-corporate/monitoring/branches/${branchId}/services`).then(res => res.data)
    },

    fetchCorporateBranches(corporateId) {
      return axios.get(`/api/corporates/${corporateId}/branches`, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
          Accept: 'application/json'
        }
      }).then(res => res.data)
    }
  }
}
</script>
