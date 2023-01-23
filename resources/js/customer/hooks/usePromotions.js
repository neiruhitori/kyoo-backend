import { useQuery } from 'react-query'

import { getPromotions } from '../api/promotion'

export default function usePromotions(branchId) {
  return useQuery(
    ['branches.promotions', branchId],
    () => getPromotions(branchId)
  )
}