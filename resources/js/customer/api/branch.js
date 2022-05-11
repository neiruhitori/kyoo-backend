import http from '../utils/http'

export async function fetchBranch(id) {
    const { data: { data } } = await http.get(`branch/${id}`)

    return data
}