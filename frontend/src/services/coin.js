import {api} from './engine'

export default {
    market: (params) => api.get('/api/market', {params}).then(response => response.data),
    coin: (id) => api.get(`/api/coin/${id}`).then(response => response.data),
    search: (params) => api.get(`/api/coin/find`, {params}).then(response => response.data),
    chart: (id, params) => api.get(`/api/chart/${id}`, {params}).then(response => response.data),
}