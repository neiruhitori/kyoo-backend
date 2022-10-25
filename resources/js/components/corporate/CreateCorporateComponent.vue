<template>
  <div style="margin: 0 auto; max-width: 600px;" class="mb-4">
    <h3 class="mb-3">Tambah Corporate</h3>

    <div class="card">
      <div class="card-body">
        <form action="">
          <div class="form-group">
            <label for="name">Nama Corporate</label>
            <input type="text" v-model="name" class="form-control" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" v-model="email" class="form-control" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="mobile_phone">No. HP</label>
            <input type="tel" v-model="phone" class="form-control" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="address">Alamat</label>
            <textarea name="address" v-model="address" class="form-control" style="height: 85px"></textarea>
          </div>

          <div class="form-group">
            <label for="province_id">Provinsi</label>
            <select name="province_id" class="form-control" v-model="provinceId" @change="handleProvinceChange(provinceId)">
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
            <select name="regency_id" class="form-control" v-model="regencyId">
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
              <label for="province_id">Latitude</label>
              <input type="text" v-model="lat" class="form-control" @change="handleLatLngChange">
            </div>

            <div class="col">
              <label for="province_id">Longitude</label>
              <input type="text" v-model="long" class="form-control" @change="handleLatLngChange">
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
        </form>
      </div>
    </div>

    <div class="text-right mt-3">
      <button class="btn btn-primary">Selanjutnya</button>
    </div>
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
      name: '',
      email: '',
      phone: '',
      address: '',
      provinceId: null,
      regencyId: null,
      lat: null,
      long: null,
      provinces: [],
      regencies: [],
      logo: null,
      mapConfig: {
        zoom: 15,
        center: [-6.2, 106.816666],
        markerLatLng: [-6.2, 106.816666]
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

    setMarkerLatLng(latLng) {
      this.mapConfig.markerLatLng = latLng
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
    }
  }
}
</script>
