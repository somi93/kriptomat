import {api} from './engine'

export default {
    alerts: (params) => api.get('/api/coin/get-alert', {params}).then(response => response.data),
    addAlert: (params) => api.post('/api/coin/alert', params).then(response => response.data),
    removeAlert: (id, params) => api.put(`/api/coin/alert/${id}`, params).then(response => response.data),
    favorites: (params) => api.get('/api/coin/get-favorite', {params}).then(response => response.data),
    addFavorite: (params) => api.post('/api/coin/favorite', params).then(response => response.data),
    removeFavorite: (id, params) => api.put(`/api/coin/favorite/${id}`, params).then(response => response.data)
}