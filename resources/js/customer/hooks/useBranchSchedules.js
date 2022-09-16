import { useQuery } from 'react-query'

import { fetchSchedulesByBranchId } from '../api/schedules'

export default function useBranchSchedules(branchId) {
    return useQuery(
        ['branches.schedules', branchId],
        () => fetchSchedulesByBranchId(branchId)
    )
}