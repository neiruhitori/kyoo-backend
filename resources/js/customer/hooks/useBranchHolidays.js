import { useQuery } from 'react-query'

import { fetchHolidaysByBranchId } from '../api/holidays'

export default function useBranchHolidays(branchId) {
    return useQuery(
        ['branches.holidays', branchId],
        () => fetchHolidaysByBranchId(branchId)
    )
}