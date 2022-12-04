<template>
  <div style="margin: 0 auto; max-width: 560px;" class="mb-4">
    <h3 class="mb-3">Tambah Corporate</h3>

    <div :class="`alert alert-${alert.type}`" role="alert" v-if="alert.show">
      {{ alert.message }}
    </div>

    <div class="step-menu-container">
      <div :class="`step-menu ${step >= 1 && 'step-menu-active'}`">
        1. Informasi Corporate
      </div>

      <div :class="`step-menu ${step >= 2 && 'step-menu-active'}`">
        2. Akun Corporate
      </div>
    </div>

    <form method="POST" @submit.prevent="handleFormSubmit">
      <div v-if="step === 1">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label for="name">Nama Corporate</label>
              <input type="text" v-model="name" id="name" class="form-control" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" v-model="email" id="email" class="form-control" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="mobile_phone">No. HP</label>
              <input type="tel" v-model="phone" id="mobile_phone" class="form-control" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="address">Alamat</label>
              <textarea id="address" v-model="address" class="form-control" style="height: 85px"></textarea>
            </div>

            <div class="form-group">
              <label for="province_id">Provinsi</label>
              <select id="province_id" class="form-control" v-model="provinceId" @change="handleProvinceChange(provinceId)">
                <option
                  v-for="province in provinces"
                  :key="province.id"
                  :value="province.id"
                >
                  {{ province.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="regency_id">Kota</label>
              <select id="regency_id" class="form-control" v-model="regencyId">
                <option
                  v-for="regency in regencies"
                  :key="regency.id"
                  :value="regency.id"
                >
                  {{ regency.name }}
                </option>
              </select>
            </div>

            <div class="row mb-3">
              <div class="col">
                <label for="lat">Latitude</label>
                <input type="text" v-model="lat" id="lat" class="form-control" @change="handleLatLngChange">
              </div>

              <div class="col">
                <label for="long">Longitude</label>
                <input type="text" v-model="long" id="long" class="form-control" @change="handleLatLngChange">
              </div>
            </div>

            <div class="mb-3">
              <map-component
                :zoom="mapConfig.zoom"
                :center="mapConfig.center"
                :marker-lat-lng="mapConfig.markerLatLng"
                @map-click="handleMapClick"
                @marker-move="handleMarkerMove"
              />
            </div>

            <div class="form-group">
              <label for="logo">Logo Corporate</label>
              <input type="file" @change="handleFileChange" class="form-control">
            </div>
          </div>
        </div>

        <div class="text-right mt-3">
          <button type="button" class="btn btn-primary" @click="stepForward">Selanjutnya</button>
        </div>
      </div>

      <div v-if="step === 2">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label for="userName">Nama User</label>
              <input type="text" v-model="user.name" id="userName" class="form-control" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="userEmail">Email</label>
              <input type="email" v-model="user.email" id="userEmail" class="form-control" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="userPhone">No. HP</label>
              <input type="tel" v-model="user.phone" id="userPhone" class="form-control" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="text-right mt-3">
          <button type="button" class="btn btn-outline-secondary mr-2" @click="stepBack">Kembali</button>
          <button type="submit" class="btn btn-warning">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import MapComponent from '../../../views/components/MapComponent.vue'

export default {
  components: { MapComponent },

  watch: {
    'mapConfig.markerLatLng'(latLng) {
      this.lat = latLng[0]
      this.long = latLng[1]
    }
  },

  mounted() {
    this.setProvinces()
  },

  data () {
    return {
      mapConfig: {
        zoom: 15,
        center: [-6.2, 106.816666],
        markerLatLng: [-6.2, 106.816666]
      },
      provinces: [],
      regencies: [],
      step: 1,
      name: '',
      email: '',
      phone: '',
      address: '',
      provinceId: null,
      regencyId: null,
      lat: null,
      long: null,
      logo: null,
      user: {
        name: '',
        email: '',
        phone: '',
      },
      fieldMap: {
        'users.phone': 'No. HP user',
        'users.email': 'Email user',
        'users.name': 'Nama user',
        name: 'Nama corporate',
        'mobile phone': 'No. HP corporate',
        email: 'Email corporate',
        'regency id': 'Kota'
      },
      alert: {
        message: '',
        show: false,
        type: 'success'
      }
    }
  },

  methods: {
    handleMapClick(latLng) {
      this.setMarkerLatLng(latLng)
    },

    handleMarkerMove(latLng) {
      this.setMarkerLatLng(latLng)
    },

    handleProvinceChange(provinceId) {
      this.setRegenciesByProvince(provinceId)
    },

    handleFileChange(e) {
      this.logo = e.target.files[0]
    },

    handleLatLngChange() {
      this.mapConfig.center = [this.lat, this.long]
      this.mapConfig.markerLatLng = [this.lat, this.long]
    },

    async handleFormSubmit() {
      const {
        name,
        email,
        phone,
        regencyId,
        address,
        lat,
        long,
        logo,
        user
      } = this

      const corporateData = new FormData()
      
      corporateData.append('name', name)
      corporateData.append('email', email)
      corporateData.append('mobile_phone', phone)
      corporateData.append('regency_id', regencyId)
      corporateData.append('address', address)
      if (lat && long) {
        corporateData.append('lat', lat)
        corporateData.append('long', long)
      }
      if (logo) corporateData.append('logo', logo)
      corporateData.append('users[name]', user.name)
      corporateData.append('users[email]', user.email)
      corporateData.append('users[phone]', user.phone)

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

    setMarkerLatLng(latLng) {
      this.mapConfig.markerLatLng = latLng
    },

    stepForward() {
      this.step++
    },

    stepBack() {
      this.step--
    },

    getErrorText(errors) {
      const [field] = Object.keys(errors)
      const [message] = errors[field]

      const keyword = field.replace('_', ' ')

      return this.fieldMap[keyword]
       ? message.replace(keyword, this.fieldMap[keyword])
       : message
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

    resetForm() {
      this.regencies = []
      this.step = 1
      this.name = ''
      this.email = ''
      this.phone = ''
      this.address = ''
      this.provinceId = null
      this.regencyId = null
      this.lat = null
      this.long = null
      this.logo = null
      this.user = {
        name: '',
        email: '',
        phone: ''
      }
    },

    async setProvinces() {
      this.provinces = await this.fetchProvinces()
    },

    async setRegenciesByProvince(provinceId) {
      this.regencies = await this.fetchRegenciesByProvince(provinceId)
    },

    fetchProvinces() {
      return axios.get("/api/allProvince")
        .then(res => res.data?.data)
    },

    fetchRegenciesByProvince(provinceId) {
      return axios.get(`/api/regency/${provinceId}`)
        .then(res => res.data?.data)
    },

    storeCorporate(data) {
      return axios.post(
        "/admin/corporate", data,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      ).then(res => res.data)
    },

    storeUser(data) {
      return axios.post("/admin/corporate/user", data)
        .then(res => res.data)
    }
  }
}
</script>

<style scoped>
  .step-menu-container {
    width: 100;
    display: grid;
    grid-template-columns: 1fr 1fr;
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
