<template>
  <div class="bg-secondary" style="height: 600px">
    <l-map
      :options="mapConfig.options"
      :center="mapConfig.center"
      :bounds="mapConfig.bounds"
      :padding="mapConfig.padding"
      :max-bounds="mapConfig.maxBounds"
      :min-zoom="mapConfig.minZoom"
      :max-bounds-viscosity="mapConfig.maxBoundsViscosity"
    >
      <l-tile-layer :url="mapConfig.tileLayer" :attribution="mapConfig.attribution"></l-tile-layer>
      <l-marker
        v-for="branch in branches"
        :key="branch.id"
        :lat-lng="[branch.regency.lat, branch.regency.long]"
        :options="mapConfig.markerOptions"
      >
        <l-icon
          :icon-size="markerSize()"
          :icon-anchor="markerAnchor()"
          :icon-url="`http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|${branch.open ? mapConfig.openHex : mapConfig.closedHex}&chf=a,s,ee00FFFF`"
        />

        <l-popup
          :content="branch.name"
          :options="mapConfig.popupOptions"
        />
      </l-marker>
    </l-map>
  </div>
</template>

<script>
import L from 'leaflet'
import { LMap, LTileLayer, LMarker, LPopup, LIcon } from 'vue2-leaflet'

import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    LIcon
  },

  props: {
    branches: Array
  },

  mounted() {
    if (this.branches.length) this.setBounds(this.branches)
  },

  data() {
    return {
      cities: [],
      mapConfig: {
        tileLayer: 'https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png',
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        center: [-2.5, 118],
        bounds: [
          [6.5, 141.5],
          [-11.5, 94.5]
        ],
        maxBounds: [
          [6.5, 141.5],
          [-11.5, 94.5]
        ],
        minZoom: 5,
        padding: [100, 100],
        maxBoundsViscosity: 1,
        options: {
          zoomSnap: 0,
          zoomControl: false,
          attributionControl: false,
          doubleClickZoom: false,
        },
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        openHex: '1cc88a',
        closedHex: 'e74a3b',
        markerOptions: {
          riseOnHover: true,
        },
        popupOptions: {
          closeButton: false,
          closeOnClick: false,
          offset: [0, -37]
        }
      }
    }
  },

  methods: {
    setBounds(branches) {
      this.mapConfig.bounds = branches.map(v => {
        return [v.regency.lat, v.regency.long]
      })
    },

    markerSize() {
      return [this.mapConfig.iconSize, this.mapConfig.iconSize * 1.15]
    },

    markerAnchor() {
      return [this.mapConfig.iconAnchor / 2, this.mapConfig.iconAnchor * 1.15]
    }
  },
}
</script>