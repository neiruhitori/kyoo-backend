import http from '../utils/http'

export function getTermConditionByBranchId(branchId) {
  return http.get(`branch/${branchId}/term-condition`)
    .then(res => res.data)
}