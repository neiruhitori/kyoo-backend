import { useQuery } from 'react-query'
import { fetchBranch } from '../api/branch'

export default function useBranch(branchId) {
    return useQuery(
        ['branches', branchId],
        () => fetchBranch(branchId)
    )
}