import http from '../utils/http';

export function fetchServiceById(serviceId) {
    return http.get(`service/${serviceId}`)
        .then(res => (res.data?.data))
}

export function fetchServiceByBranchId(branchId, params) {
    if (params.queueType === 'onsite') {
        return http.get(`direct-queue-by-branch/${branchId}`)
            .then(res => (res.data?.data))
    }

    return http.get(`service/branch/${branchId}`, {
        params: {
            queue_type: params.queueType,
            date: params.date
        }
    })
        .then(res => (res.data?.data))
}