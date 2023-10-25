import { useQuery } from 'react-query'

import { fetchServicesCategoryByBranchId } from '../api/serviceCategories'

export default function useBranchServicesCategories(branchId) {
    return useQuery(
        ['branches.service.categories', branchId],
        () => fetchServicesCategoryByBranchId(branchId)
    )
}
