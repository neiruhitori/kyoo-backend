<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="mb-0">Syarat & Ketentuan</h3>

      <div>
        <div v-if="isEdit">
          <button class="btn btn-outline-secondary mr-1" @click="exitEdit">
            Batal
          </button>
          <button class="btn btn-warning" @click="saveTermsConditions">
            <span class="fas fa-check"></span>
            Simpan
          </button>
        </div>

        <div v-else-if="isContentEmpty">
          <button class="btn btn-warning" @click="enterEdit">
            <span class="fas fa-plus mr-1"></span>
            Tambah
          </button>
        </div>

        <button v-else class="btn btn-secondary" @click="enterEdit">Edit</button>
      </div>
    </div>

    <div
        class="alert alert-danger"
        role="alert"
        v-if="alert.isShow"
    >
        {{ alert.message }}
    </div>

    <div class="card">
      <div class="card-body">
        <wysiwyg v-if="isEdit" v-model="termsConditions" />

        <div v-else-if="isContentEmpty">
          <p class="text-center mb-0" style="width: 500px; margin: 0 auto;">
            Anda belum memiliki Syarat & Ketentuan. Tambahkan Syarat & Ketentuan
            dengan menekan tombol <strong>Tambah</strong> diatas
          </p>
        </div>

        <div v-else v-html="termsConditions"></div>
      </div>
    </div>
  </div>
</template>

<script>
  import wysiwyg from 'vue-wysiwyg'

  Vue.use(wysiwyg, {
    hideModules: {
      "justifyLeft": true,
      "justifyCenter": true,
      "justifyRight": true,
      "headings": true,
      "code": true,
      "image": true,
      "table": true,
      "removeFormat": true,
      "separator": true,
    }
  })

  document.addEventListener('paste', function (e) {
    e.preventDefault();
    document.execCommand("insertHTML", false, e.clipboardData.getData("text/plain"));
  });

  export default {
    mounted() {
      this.setTermsConditions()
    },

    computed: {
      isContentEmpty() {
        return !this.termsConditions.length
      }
    },

    data() {
      return {
        termsConditions: "",
        isEdit: false,
        alert: {
          isShow: false,
          message: '',
        },
      }
    },

    methods: {
      enterEdit() {
        this.isEdit = true
      },

      exitEdit() {
        this.isEdit = false

        // Revert terms & conditions to original
        this.setTermsConditions()
      },

      async setTermsConditions() {
        try {
          this.termsConditions = await this.fetchTermsConditions()
        } catch (error) {
          // this.showAlert(error.response.data?.message)
          this.termsConditions = []
        }
      },

      async saveTermsConditions() {
        try {
          await this.updateTermsConditions()
          this.exitEdit()
        } catch (error) {
          this.showAlert(error.response.data?.message)
        }
      },

      showAlert(message) {
        this.alert.isShow = true
        this.alert.message = message

        setTimeout(() => {
          this.closeAlert()
        }, 3000)
      },

      closeAlert() {
        this.alert.isShow = false,
        this.alert.message = ''
      },

      async fetchTermsConditions() {
        try {
          const { data: termsConditions } = await axios.get('/admin-branch/branch-configuration/terms-conditions/get')
  
          return termsConditions.body
        } catch (error) {
          throw error
        }
      },

      async updateTermsConditions() {
        try {
          await axios.put('/admin-branch/branch-configuration/terms-conditions', {
            body: this.termsConditions
          })
        } catch (error) {
          throw error
        }
      }
    }
  }
</script>