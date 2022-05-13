import { createAppointment, getAppointmentById } from './appointment'
import { createExhibition, getExhibitionById } from './exhibition'
import { createOnsite, getOnsiteById } from './onsite'
import http from '../utils/http'

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

export function searchBookingByBookingCode(bookingCode) {
    return http.get('search', {
        params: {
            booking_code: bookingCode
        }
    })
        .then(res => res.data)
}