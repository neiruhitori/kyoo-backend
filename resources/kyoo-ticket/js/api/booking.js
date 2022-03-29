import { createAppointment, getAppointmentById } from './appointment'
import { createExhibition, getExhibitionById } from './exhibition'
import { createOnsite, getOnsiteById } from './onsite'

export function getBooking(queueType, id) {
    if (queueType === 'appointment') {
        return getAppointmentById(id)
    }

    if (queueType === 'exhibition') {
        return getExhibitionById(id)
    }

    if (queueType === 'onsite') {
        return getOnsiteById(id)
    }
}

export function createBooking(queueType, data) {
    if (queueType === 'appointment') {
        return createAppointment(data)
    }

    if (queueType === 'exhibition') {
        return createExhibition(data)
    }

    if (queueType === 'onsite') {
        return createOnsite(data)
    }
}