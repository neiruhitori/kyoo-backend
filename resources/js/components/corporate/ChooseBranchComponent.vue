<template>
  <div style="margin: 0 auto; max-width: 560px;" class="mb-4">
    <div class="mb-2">
      <a :href="`/admin/corporate/${corporate.id}/branch`" style="text-decoration: none;">
        <span class="fas fa-angle-left mr-1"></span>
        Kembali
      </a>
    </div>

    <h3 class="mb-4">Pilih Cabang {{ corporate.name }}</h3>

    <div :class="`alert alert-${alert.type}`" role="alert" v-if="alert.show">
      {{ alert.message }}
    </div>

    <form class="mb-4" @submit.prevent="handleFormSubmit">
      <div class="form-group m-0">
        <input
          type="text"
          class="form-control form-control-lg"
          placeholder="Ketik nama cabang..."
          v-model="name"
        >
      </div>
    </form>

    <div class="mb-3" v-if="branches.length">
      <div class="card">
        <div class="branch-list">
          <div
            v-for="branch in branches"
            :key="branch.id"
            class="branch-list-item d-flex align-items-center p-3"
            @click="showConfirmation(branch)"
          >
            <div class="branch-img mr-3">
              <img :src="branch.logo" alt="">
            </div>

            <div class="branch-content">
              <div class="h5 mb-0">{{ branch.name }}</div>
              <small class="text-muted">{{ branch.regency.name }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-3" v-if="noItem">
      Cabang tidak ditemukan.
    </div>

    <div class="mb-3" v-if="loading">
      Loading...
    </div>

    <div class="modal fade show d-block" tabindex="-1" aria-modal="true" v-if="showModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="card-body">
            <div style="font-size: 1.125rem;">
              Apakah Anda yakin memilih <strong>{{ branch.name }}</strong> sebagai cabang <strong>{{ corporate.name }}</strong>?
            </div>

            <div class="mt-3 text-right">
              <button class="btn btn-outline-secondary mr-1" @click="closeConfirmation">Batal</button>
              <button class="btn btn-warning" @click="confirmBranch(branch)">Pilih</button>
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
    corporate: Object
  },

  data() {
    return {
      name: '',
      branches: [],
      noItem: false,
      loading: false,
      branch: null,
      showModal: false,
      alert: {
        message: '',
        show: false,
        type: 'success'
      }
    }
  },

  methods: {
    async handleFormSubmit() {
      this.loading = true

      this.branches = await this.fetchBranchesByName(this.name)

      this.loading = false

      if (!this.branches.length) {
        this.showNoItem()
      } else {
        this.hideNoItem()
      }
    },

    showNoItem() {
      this.noItem = true
    },

    hideNoItem() {
      this.noItem = false
    },

    showConfirmation(branch) {
      this.showModal = true
      this.branch = branch
    },

    closeConfirmation() {
      this.showModal = false
    },

    showAlert(message, type) {
      this.alert.type = type
      this.alert.message = message
      this.alert.show = true

      setTimeout(() => {
        this.hideAlert()
      }, 3000)
    },

    hideAlert() {
      this.alert = {
        show: false,
        type: 'success',
        message: ''
      }
    },

    async confirmBranch(branch) {
      await this.storeCorporateBranch({
        branch_id: branch.id,
        corporate_id: this.corporate.id
      })

      this.closeConfirmation()
      this.branches = []
      this.name = ''
      this.showAlert('Berhasil menambahkan corporate', 'success')
    },

    fetchBranchesByName(name) {
      return axios.get(`/admin/corporate/${this.corporate.id}/branch/get`, {
        params: { name }
      }).then(res => res.data)
    },

    storeCorporateBranch(data) {
      return axios.post(`/admin/corporate/${this.corporate.id}/branch`, {
        branch_id: data.branch_id,
        corporate_id: data.corporate_id
      })
    },
  }
}
</script>

<style>
  .branch-list .branch-list-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
  }

  .branch-img {
    width: 60px;
    text-align: center;
  }

  .branch-img img {
    width: auto;
    height: auto;
    max-width: 60px;
    max-height: 50px;
  }

  .branch-content {
    flex: 1 1 0%;
  }
</style>
