import http from '../utils/http'

export function getAppointmentOnsiteById(id) {
    return http.get(`appointment-onsite/${id}`)
        .then(res => res.data)
}

export function createAppointmentOnsite(data) {
    return http.post('appointment-onsite', data)
        .then(res => res.data)
}

export function fetchAppointmentOnsiteSlots(branchId, params) {
    return http.get(`appointment-onsite/${branchId}/slots`, {
        params: {
            date: params.date,
            day: params.day,
            service_id: params.serviceId
        }
    })
        .then(res => (res.data?.data))
}
