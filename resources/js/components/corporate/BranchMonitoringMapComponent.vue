<template>
  <div>
    <l-map
      style="height: 600px"
      :options="map.options"
      :center="map.center"
      :bounds="map.bounds"
      :max-bounds="map.bounds"
    >
      <l-tile-layer :url="map.tileLayer" :attribution="attribution"></l-tile-layer>
    </l-map>
  </div>
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
  components: {
    LMap,
    LTileLayer,
    LMarker,
  },

  data() {
    return {
      map: {
        tileLayer: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png',
        attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        center: [-2.5, 118],
        bounds: [
          [6.5, 141.5],
          [-11.5, 94.5]
        ],
        options: {
          zoomSnap: 0,
          dragging: false,
          zoomControl: false,
          attributionControl: false,
          doubleClickZoom: false,
          boxZoom: false,
          scrollWheelZoom: false
        }
      }
    }
  }
}
</script>