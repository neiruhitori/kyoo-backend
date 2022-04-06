import http from '../utils/http'

export function getOnsiteById(id) {
    return http.get(`direct-queue/${id}`)
        .then(res => res.data)
}

export function createOnsite(data) {
    const now = new Date()
    const storageKey = 'onsite-' + now.getDate() + (now.getMonth() + 1) + now.getFullYear()
    const prevKey = 'onsite-' + (now.getDate() - 1) + (now.getMonth() + 1) + now.getFullYear()

    if (localStorage.getItem(storageKey) >= 3) {
        localStorage.removeItem(prevKey)

        return {
            success: false,
            message: 'Batas pembuatan antrian onsite telah terlampaui. Silahkan coba lagi besok'
        }
    }

    const storageValue = parseInt(localStorage.getItem(storageKey)) + 1 || 1
    localStorage.setItem(storageKey, storageValue)

    return http.post('direct-queue', data)
        .then(res => res.data)
}