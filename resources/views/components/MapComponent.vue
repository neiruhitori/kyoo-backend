<template>
  <l-map style="height: 300px" :zoom="zoom" :center="center" @click="mapClicked">
    <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
    <l-marker
      :lat-lng="markerLatLng"
      draggable
      @update:lat-lng="markerMoved"
    />
  </l-map>
</template>

<script>
import L from 'leaflet'
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet'

import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

export default {
  props: {
    zoom: Number,
    center: Array,
    markerLatLng: Array
  },

  components: {
    LMap,
    LTileLayer,
    LMarker,
  },

  data() {
    return {
      url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    }
  },

  methods: {
    mapClicked(e) {
      this.$emit('map-click', [e.latlng.lat, e.latlng.lng])
    },

    markerMoved(e) {
      this.$emit('marker-move', [e.lat, e.lng])
    }
  }
}
</script>