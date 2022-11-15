<template>
  <div>
    <div v-if="branch">
      <div class="mb-2">Cabang yang dipilih</div>

      <div class="card mb-2 branch-card">
        <div class="p-3 d-flex align-items-center">
          <div class="text-center mr-4" style="width: 50px;">
            <img :src="branch.logo" alt="" style="width: auto; height: auto; max-width: 50px; max-height: 50px;" />
          </div>

          <div style="flex: 1 1 0%">
            <div>
              <strong>{{ branch.name }}</strong>
            </div>
            <p class="mb-0">{{ branch.regency.name }}</p>
          </div>

          <div class="ml-3">
            <a href="javascript:void(0)" @click="handleChangeClick">Ubah</a>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <form @submit.prevent="handleFormSubmit" class="mb-4">
        <div class="form-group mb-0">
          <label for="">Cari Cabang</label>
          <input type="text" v-model="name" class="form-control" placeholder="Ketik nama cabang...">
        </div>

        <div class="mt-2" v-if="noItem">
          <small>Cabang tidak ditemukan.</small>
        </div>
      </form>

      <div class="mb-3" v-if="loading">
        <span class="fas fa-spinner fa-spin mr-1"></span> Loading...
      </div>

      <div class="card mb-2 branch-card" v-for="branch in branches" :key="branch.id" @click="handleBranchClick(branch)">
        <div class="p-3 d-flex align-items-center">
          <div class="text-center mr-4" style="width: 50px;">
            <img :src="branch.logo" alt="" style="width: auto; height: auto; max-width: 50px; max-height: 50px;" />
          </div>

          <div>
            <div>
              <strong>{{ branch.name }}</strong>
            </div>
            <p class="mb-0">{{ branch.regency.name }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    branch: Object
  },

  data() {
    return {
      name: '',
      branches: [],
      noItem: false,
      loading: false,
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

    handleBranchClick(branch) {
      this.$emit('change', branch)
      this.name = ''
      this.branches = []
    },

    handleChangeClick() {
      this.$emit('change', null)
    },

    async setBranchByBranchId(branchId) {
      this.branch = await this.fetchBranchById(branchId)
    },

    showNoItem() {
      this.noItem = true
    },

    hideNoItem() {
      this.noItem = false
    },

    fetchBranchesByName(name) {
      return axios.get(`/admin/corporate/branch`, {
        params: { name }
      }).then(res => res.data)
    },
  }
}
</script>

<style scoped>
  .branch-card {
    cursor: pointer;
  }

  .branch-card:hover {
    border-color: #189DCD;
  }
</style>