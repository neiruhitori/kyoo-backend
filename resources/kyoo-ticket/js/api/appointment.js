import http from '../utils/http'

export function createAppointment(data) {
    return http.post('appointment', data)
        .then(res => res.data)
}

export function getAppointmentById(id) {
    return http.get(`appointment/${id}`)
        .then(res => res.data)
}