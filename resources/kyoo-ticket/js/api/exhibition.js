import http from '../utils/http'

export function createExhibition(data) {
    return http.post('exhibition', data)
        .then(res => res.data)
}

export function getExhibitionById(id) {
    return http.get(`exhibition/${id}`)
        .then(res => res.data)
}
