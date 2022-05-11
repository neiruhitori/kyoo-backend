import http from '../utils/http'

export function getRegencyById(id) {
    return http.get(`regency/city/${id}`)
        .then(res => (res.data?.data))
}