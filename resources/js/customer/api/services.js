import http from '../utils/http';

export function fetchServiceById(serviceId, params) {
    return http.get(`service/${serviceId}`, {
        params: {
            queue_type: params.queueType,
            date: params.date
        }
    })
        .then(res => (res.data?.data))
}

export function fetchServiceByBranchId(branchId, params) {
    if (params.queueType === 'appointment-onsite') {
        return http.get(`appointment-onsite/direct-queue-by-branch/${branchId}`, {
            params: {
                date: params.date,
                day: params.day
            }
        })
            .then(res => (res.data?.data))
    }

    if (params.queueType === 'onsite') {
        return http.get(`direct-queue-by-branch/${branchId}`)
            .then(res => (res.data?.data))
    }

    return http.get(`service/branch/${branchId}`, {
        params: {
            queue_type: params.queueType,
            date: params.date,
            service_category_id: params.serviceCategoryId
        }
    })
        .then(res => (res.data?.data))
}
