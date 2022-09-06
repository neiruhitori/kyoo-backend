import http from '../utils/http';

export function fetchHolidaysByBranchId(branchId) {
    return http.get(`branch/${branchId}/holiday`)
        .then(res => res.data.data)
}