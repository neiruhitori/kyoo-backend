import http from '../utils/http';

export async function fetchServicesCategoryByBranchId(branchId) {
    const { data: { data } } = await http.get(`service-category/branch/${branchId}`)

    return data
}
