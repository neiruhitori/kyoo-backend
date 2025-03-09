<template>
  <div style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
      <h3 class="mb-0">{{ this.t('Terms and Conditions') }}</h3>
    </div>

    <div
        class="alert alert-danger"
        role="alert"
        v-if="alert.isShow"
    >
        {{ alert.message }}
    </div>

    <div class="bg-white" v-if="isEdit">
      <wysiwyg v-model="termsConditions" />
    </div>

    <div v-else class="card">
      <div class="card-body">
        <div v-if="isContentEmpty">
          <p class="text-center mb-0" style="margin: 0 auto;">
            {{ this.t('You do not have Terms and Conditions yet') }}
          </p>
        </div>

        <div v-else-if="isLoading">
          Loading...
        </div>

        <div v-else v-html="termsConditions"></div>
      </div>
    </div>

    <div class="mt-3">
      <div v-if="isEdit">
        <button class="btn btn-secondary mr-1" @click="exitEdit">
          {{ this.t('Cancel') }}
        </button>
        <button class="btn btn-warning" @click="saveTermsConditions">
          <span class="fas fa-check"></span>
          {{ this.t('Save') }}
        </button>
      </div>

      <div v-else-if="isContentEmpty">
        <button class="btn btn-warning" @click="enterEdit">
          {{ this.t('Add') }}
        </button>
      </div>

      <button v-else-if="!isLoading" class="btn btn-secondary" @click="enterEdit">Edit</button>
    </div>
  </div>
</template>

<script>
  import wysiwyg from 'vue-wysiwyg'
  import id from "../../../lang/id.json";
  import en from "../../../lang/en.json";


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
    props: {
    locale: {
      type: String,
    },
  },
    mounted() {
      this.setTermsConditions()
    },

    data() {
      return {
        termsConditions: "",
        isEdit: false,
        isContentEmpty: false,
        isLoading: false,
        messages : { id, en },
        currentLocale: this.locale || "en", // Default locale
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
      t(key, params = {}) {
      const translation =
        this.messages[this.currentLocale][key] || key;

      // Ganti placeholder seperti {number} dengan value dari params
      return translation.replace(/\{(\w+)\}/g, (_, param) => params[param] || "");
    },

      exitEdit() {
        this.isEdit = false

        // Revert terms & conditions to original
        this.setTermsConditions()
      },

      async setTermsConditions() {
        try {
          this.isLoading = true

          this.termsConditions = await this.fetchTermsConditions()
        } catch (error) {
          // this.showAlert(error.response.data?.message)
          this.termsConditions = []
        }

        if (!this.termsConditions.length) {
          this.isContentEmpty = true
        } else {
          this.isContentEmpty = false
        }

        this.isLoading = false
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