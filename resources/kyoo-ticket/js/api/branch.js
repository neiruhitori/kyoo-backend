import http from '../utils/http'

export function fetchBranch(id) {
    return http.get(`branch/${id}`)
        .then(res => (res.data?.data))
}