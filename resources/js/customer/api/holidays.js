import http from '../utils/http';

export function fetchHolidays() {
    return http.get('holidays')
        .then(res => res.data)
}