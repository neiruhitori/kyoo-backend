<template>
   <div style="margin: 0 auto; max-width: 560px;" class="mb-4">
    <h3 class="mb-3">Tambah Corporate</h3>

    <div :class="`alert alert-${alert.type}`" role="alert" v-if="alert.show">
      {{ alert.message }}
    </div>

    <div class="step-menu-container">
      <div :class="`step-menu ${step >= 1 && 'step-menu-active'}`">
        1. Pilih Cabang
      </div>

      <div :class="`step-menu ${step >= 2 && 'step-menu-active'}`">
        2. Informasi Corporate
      </div>

      <div :class="`step-menu ${step >= 3 && 'step-menu-active'}`">
        3. Akun Corporate
      </div>
    </div>

    
    <div v-if="step === 1">
      <corporate-branch-form
        :branch="branch"
        @change="handleBranchChange"
      />

      <div class="text-right mt-3">
        <button type="button" class="btn btn-primary" @click="stepForward" :disabled="!branch">Selanjutnya</button>
      </div>
    </div>

    <div v-if="step === 2">
      <corporate-info-form
        :name="corporate.name"
        :email="corporate.email"
        :address="corporate.address"
        :phone="corporate.phone"
        :province-id="corporate.provinceId"
        :regency-id="corporate.regencyId"
        :lat-lng="corporate.latLng"
        @change="handleCorporateChange"
      />

      <div class="text-right mt-3">
        <button type="button" class="btn btn-outline-secondary mr-2" @click="stepBack">Kembali</button>
        <button type="button" class="btn btn-primary" @click="stepForward">Selanjutnya</button>
      </div>
    </div>

    <div v-if="step === 3">
      <form @submit.prevent="handleSubmit" method="POST">
        <corporate-account-form
          :name="user.name"
          :email="user.email"
          :phone="user.phone"
          @change="handleUserChange"
        />

        <div class="text-right mt-3">
          <button type="button" class="btn btn-outline-secondary mr-2" @click="stepBack">Kembali</button>
          <button type="submit" id="submit-button" class="btn btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import CorporateBranchForm from './CorporateBranchForm';
import CorporateInfoForm from './CorporateInfoForm';
import CorporateAccountForm from './CorporateAccountForm';

export default {
  components: {
    CorporateBranchForm,
    CorporateInfoForm,
    CorporateAccountForm
  },

  data() {
    return {
      step: 1,
      alert: {
        message: '',
        show: false,
        type: 'success'
      },
      corporate: {
        name: '',
        email: '',
        phone: '',
        address: '',
        provinceId: null,
        regencyId: null,
        latLng: [],
        logo: null,
      },
      user: {
        name: '',
        email: '',
        phone: '',
      },
      branch: null,
      fieldMap: {
        'users.phone': 'No. HP user',
        'users.email': 'Email user',
        'users.name': 'Nama user',
        'corporates.name': 'Nama corporate',
        'corporates.mobile phone': 'No. HP corporate',
        'corporates.email': 'Email corporate',
        'corporates.regency id': 'Kota',
        'branch_id': 'Cabang'
      }
    }
  },

  methods: {
    handleBranchChange(branch) {
      this.branch = branch

      const latLng = branch.lat && branch.long
        ? [branch.lat, branch.long]
        : []

      this.corporate.name = branch.name
      this.corporate.email = branch.email
      this.corporate.phone = branch.mobile_phone
      this.corporate.address = branch.address
      this.corporate.provinceId = branch.province.id
      this.corporate.regencyId = branch.regency.id
      this.corporate.latLng = latLng
    },

    handleCorporateChange({ field, value }) {
      this.corporate[field] = value

      if (field == 'provinceId') this.corporate.regencyId = null
    },

    handleUserChange({ field, value }) {
      this.user[field] = value
    },

    async handleSubmit(e) {
      const lat = this.corporate.latLng.length
        ? this.corporate.latLng[0]
        : null
      const long = this.corporate.latLng.length
        ? this.corporate.latLng[1]
        : null
      
      const corporateData = new FormData()
      
      corporateData.append('corporates[name]', this.corporate.name)
      corporateData.append('corporates[email]', this.corporate.email)
      corporateData.append('corporates[mobile_phone]', this.corporate.phone)
      corporateData.append('corporates[regency_id]', this.corporate.regencyId)
      corporateData.append('corporates[address]', this.corporate.address)
      if (lat && long) {
        corporateData.append('corporates[lat]', lat)
        corporateData.append('corporates[long]', long)
      }
      if (this.corporate.logo) corporateData.append('corporates[logo]', this.corporate.logo)
      corporateData.append('users[name]', this.user.name)
      corporateData.append('users[email]', this.user.email)
      corporateData.append('users[phone]', this.user.phone)
      corporateData.append('branch_id', this.branch.id)

      try {
        await this.storeCorporate(corporateData)

        this.showAlert('Berhasil menambahkan corporate', 'success')
        this.resetForm()
      } catch (err) {
        let message = 'Something went wrong'

        if (err.response.data?.errors) {
          const { errors } =  err.response.data

          message = this.getErrorText(errors)
        } else if (err.response.data?.message) {
          message = err.response.data.message
        }

        this.showAlert(message, 'danger')
      }
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

    getErrorText(errors) {
      const [field] = Object.keys(errors)
      const [message] = errors[field]

      const keyword = field.replace('_', ' ')

      return this.fieldMap[keyword]
       ? message.replace(keyword, this.fieldMap[keyword])
       : message
    },

    resetForm() {
      this.step = 1

      this.corporate = {
        name: '',
        email: '',
        phone: '',
        address: '',
        provinceId: null,
        regencyId: null,
        latLng: [],
        logo: null,
      }

      this.user = {
        name: '',
        email: '',
        phone: '',
      }

      this.branch = null
    },

    stepForward() {
      this.step++
      return false
    },

    stepBack() {
      this.step--
      return false
    },

    storeCorporate(data) {
      return axios.post(
        "/admin/corporate/copy", data,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      ).then(res => res.data)
    }
  }
}
</script>

<style scoped>
  .step-menu-container {
    width: 100;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    column-gap: 1.25rem;
    margin-bottom: 1.25rem;
  }

  .step-menu {
    flex: 1;
    padding: .5rem 0;
    font-weight: bold;
    color: #AAAAAA;
  }

  .step-menu.step-menu-active {
    color: var(--primary);
    border-bottom: 4px solid var(--primary);
  }
</style>
