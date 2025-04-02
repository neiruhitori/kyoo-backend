import { useEffect, useRef } from 'react'
import mapboxgl from '!mapbox-gl'

export default function BranchMap(props) {
    mapboxgl.accessToken = 'pk.eyJ1Ijoic3VwcG9ydGt5b28iLCJhIjoiY204dGxjNzF1MDFhbDJrb3AxeWljenpmcCJ9.Di2l0rvbaJwiT8vWaLwcJw'

    const mapContainer = useRef(null)
    const map = useRef(null)

    useEffect(() => {
        if (map.current) return

        map.current = new mapboxgl.Map({
            container: mapContainer.current,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: props.center,
            zoom: 14
        })

        new mapboxgl.Marker()
            .setLngLat(props.marker)
            .addTo(map.current);
    })

    return <div ref={mapContainer} className="map-container" style={{
        height: '160px'
    }} />
}