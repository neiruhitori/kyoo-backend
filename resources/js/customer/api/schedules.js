import http from '../utils/http';

export function fetchSchedulesByBranchId(branchId) {
    return http.get(`branch/${branchId}/schedules`)
        .then(res => res.data)
}