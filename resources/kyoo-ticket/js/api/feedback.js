import http from '../utils/http'

export function createFeedback(queueType, id, data) {
    if (queueType === 'onsite') {
        return http.post(`direct-queue/${id}/feedback`, data)
            .then(res => res.data)
    }
}