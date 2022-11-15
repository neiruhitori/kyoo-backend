<template>
  <div class="card">
    <div class="card-body">
      <div class="form-group">
        <label for="name">Nama Corporate</label>
        <input type="text" v-model="mName" id="name" class="form-control" autocomplete="off">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" v-model="mEmail" id="email" class="form-control" autocomplete="off">
      </div>

      <div class="form-group">
        <label for="mobile_phone">No. HP</label>
        <input type="tel" v-model="mPhone" id="mobile_phone" class="form-control" autocomplete="off">
      </div>

      <div class="form-group">
        <label for="address">Alamat</label>
        <textarea id="address" v-model="mAddress" class="form-control" style="height: 85px"></textarea>
      </div>

      <div class="form-group">
        <label for="province_id">Provinsi</label>
        <select id="province_id" class="form-control" v-model="mProvinceId" @change="handleProvinceIdChange">
          <option selected disabled></option>
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
        <select id="regency_id" class="form-control" v-model="mRegencyId" @change="handleRegencyIdChange">
          <option selected disabled></option>
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
          <input type="text" v-model="mLat" id="lat" class="form-control" @change="handleLatLngChange">
        </div>

        <div class="col">
          <label for="long">Longitude</label>
          <input type="text" v-model="mLong" id="long" class="form-control" @change="handleLatLngChange">
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
</template>

<script>
import MapComponent from '../../../views/components/MapComponent'

export default {
  components: { MapComponent },

  props: {
    name: String,
    email: String,
    phone: String,
    address: String,
    provinceId: Number,
    regencyId: Number,
    latLng: Array
  },

  mounted() {
    this.setProvinces()

    if (this.regencyId) {
      this.setRegenciesByProvinceId(this.provinceId)
    }

    if (this.latLng.length) this.setMapCenter()
  },

  data() {
    return {
      provinces: [],
      regencies: [],
      mapConfig: {
        zoom: 15,
        center: [-6.2, 106.816666],
        markerLatLng: [-6.2, 106.816666]
      }
    }
  },

  computed: {
    mName: {
      get() { return this.name },
      set(value) {
        return this.$emit('change', {
          field: 'name',
          value: value
        })
      } 
    },
    mEmail: {
      get() { return this.email },
      set(value) {
        return this.$emit('change', {
          field: 'email',
          value: value
        })
      }
    },
    mPhone: {
      get() { return this.phone },
      set(value) {
        return this.$emit('change', {
          field: 'phone',
          value: value
        })
      }
    },
    mAddress: {
      get() { return this.address },
      set(value) {
        return this.$emit('change', {
          field: 'address',
          value: value
        })
      }
    },
    mProvinceId: {
      get() { return this.provinceId },
      set(value) {
        return this.$emit('change', {
          field: 'provinceId',
          value: value
        })
      }
    },
    mRegencyId: {
      get() { return this.regencyId },
      set(value) {
        return this.$emit('change', {
          field: 'regencyId',
          value: value
        })
      }
    },
    mLat: {
      get() { return this.latLng[0] },
      set(value) {
        return this.$emit('change', {
          field: 'latLng',
          value: [value, this.mLong]
        })
      }
    },
    mLong: {
      get() { return this.latLng[1] },
      set(value) {
        return this.$emit('change', {
          field: 'latLng',
          value: [this.mLat, value]
        })
      }
    },
  },

  methods: {
    async setProvinces() {
      this.provinces = await this.fetchProvinces()
    },

    async setRegenciesByProvinceId(provinceId) {
      this.regencies = await this.fetchRegenciesByProvinceId(provinceId)
    },

    setMapCenter() {
      this.mapConfig.center = this.latLng
      this.mapConfig.markerLatLng = this.latLng
    },

    handleProvinceIdChange(e) {
      this.setRegenciesByProvinceId(e.target.value)

      this.$emit('change', {
        field: 'provinceId',
        value: parseInt(e.target.value)
      })
    },

    handleRegencyIdChange(e) {
      this.$emit('change', {
        field: 'regencyId',
        value: parseInt(e.target.value)
      })
    },

    handleMapClick(latLng) {
      this.$emit('change', {
        field: 'latLng',
        value: latLng
      })

      this.mapConfig.markerLatLng = latLng
    },

    handleMarkerMove(latLng) {
      this.$emit('change', {
        field: 'latLng',
        value: latLng
      })

      this.mapConfig.markerLatLng = latLng
    },

    handleLatLngChange() {
      this.setMapCenter()
    },

    handleFileChange(e) {
      this.$emit('change', {
        field: 'logo',
        value: e.target.files[0]
      })
    },

    fetchProvinces() {
      return axios.get("/api/allProvince")
        .then(res => res.data?.data)
    },

    fetchRegenciesByProvinceId(provinceId) {
      return axios.get(`/api/regency/${provinceId}`)
        .then(res => res.data?.data)
    },
  }
}
</script>
