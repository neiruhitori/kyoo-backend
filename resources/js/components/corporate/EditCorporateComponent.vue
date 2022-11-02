<template>
  <div style="margin: 0 auto; max-width: 560px;" class="mb-4">
    <div class="mb-4 row align-items-center">
      <h3 class="mb-0 col">Edit {{ corporate.name }}</h3>

      <div class="col text-right">
        <img :src="logoUrl" alt="Company Logo" style="height: 36px" v-if="!!logoUrl">
      </div>
    </div>

    <div :class="`alert alert-${alert.type}`" role="alert" v-if="alert.show">
      {{ alert.message }}
    </div>

    <form method="POST" @submit.prevent="handleFormSubmit">
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
        <a href="/admin/corporate" class="btn btn-outline-secondary mr-2">Kembali</a>
        <button type="submit" class="btn btn-warning">Perbarui</button>
      </div>
    </form>
  </div>
</template>

<script>
import MapComponent from '../../../views/components/MapComponent.vue'

export default {
  components: { MapComponent },

  props: {
    corporate: Object
  },

  watch: {
    'mapConfig.markerLatLng'(latLng) {
      this.lat = latLng[0]
      this.long = latLng[1]
    }
  },

  mounted() {
    this.setSelectedCorporate(this.corporate?.id)
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
      id: null,
      name: '',
      email: '',
      phone: '',
      address: '',
      provinceId: null,
      regencyId: null,
      lat: null,
      long: null,
      logo: null,
      logoUrl: '',
      fieldMap: {
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
        id,
        name,
        email,
        phone,
        regencyId,
        address,
        lat,
        long,
        logo
      } = this

      const corporateData = {
        name,
        email,
        mobile_phone: phone,
        regency_id: regencyId,
        address,
        lat,
        long,
        logo
      }

      try {
        await this.updateCorporate(id, corporateData)

        this.showAlert('Berhasil memperbarui corporate', 'success')
        this.setSelectedCorporate(id)
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

    async setSelectedCorporate(id) {
      const corporate = await this.fetchCorporateById(id)

      this.id = corporate.id
      this.name = corporate.name
      this.email = corporate.email
      this.phone = corporate.mobile_phone
      this.email = corporate.email
      this.address = corporate.address
      this.regencyId = corporate.regency.id
      this.provinceId = corporate.province.id
      this.lat = corporate.lat
      this.long = corporate.long

      this.logoUrl = this.getStoragePath(corporate.logo)

      const coords = [this.lat, this.long]
      this.mapConfig.center = coords
      this.mapConfig.markerLatLng = coords

      this.setRegenciesByProvince(this.provinceId)
    },

    getErrorText(errors) {
      const [field] = Object.keys(errors)
      const [message] = errors[field]

      const keyword = field.replace('_', ' ')

      return this.fieldMap[keyword]
       ? message.replace(keyword, this.fieldMap[keyword])
       : message
    },

    getStoragePath(path) {
      return _asset + 'storage/' + path
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

    async setProvinces() {
      this.provinces = await this.fetchProvinces()
    },

    async setRegenciesByProvince(provinceId) {
      this.regencies = await this.fetchRegenciesByProvince(provinceId)
    },

    fetchCorporateById(id) {
      return axios.get(`/admin/corporate/${id}`)
        .then(res => res.data)
    },

    fetchProvinces() {
      return axios.get("/api/allProvince")
        .then(res => res.data?.data)
    },

    fetchRegenciesByProvince(provinceId) {
      return axios.get(`/api/regency/${provinceId}`)
        .then(res => res.data?.data)
    },

    updateCorporate(id, data) {
      data._method = 'PATCH'

      return axios.post(
        `/admin/corporate/${id}`, data,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      ).then(res => res.data)
    },
  }
}
</script>
