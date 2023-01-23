import http from '../utils/http'

export function getPromotions(branchId)  {
  return http.get(`/branch/${branchId}/promotions`)
    .then(res => res.data)
}