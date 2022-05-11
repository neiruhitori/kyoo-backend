import http from '../utils/http'

export function fetchSlotsByServiceId(serviceId, date = new Date()) {
    return http.post('slot', {
        service_id: serviceId,
        date
    }).then(res => (res.data?.data))
}