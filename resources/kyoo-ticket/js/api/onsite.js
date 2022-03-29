import http from '../utils/http'

export function getOnsiteById(id) {
    return http.get(`direct-queue/${id}`)
        .then(res => res.data)
}

export function createOnsite(data) {
    return http.post('direct-queue', data)
    .then(res => res.data)
}