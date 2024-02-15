import { useQuery } from 'react-query'

import { fetchServiceByBranchId } from '../api/services'

export default function useBranchServices(branchId, params) {
    return useQuery(
        ['branches.services', branchId],
        () => fetchServiceByBranchId(branchId, params)
    )
}
