import cfg from '@/config'
import {api} from '@/services/engine'
import userService from "@/services/user";

const guest = {
    id: null,
    name: null,
    email: null,
    state: 0,
    profiles: [],
    permissions: [],
    roles: []
};

export default {
    namespaced: true,
    state: {
        id: null,
        name: null,
        email: null,
        state: 0,
        profiles: [],
        permissions: [],
        roles: [],
        favorites: []
    },
    mutations: {
        set(state, user) {
            state.id = user.id;
            state.name = user.name;
            state.email = user.email;
            state.state = user.state;
            state.profiles = user.profiles;
            state.contributor = user.contributor;
            state.permissions = user.permissions || [];
            state.roles = user.roles || [];
        },
        setFavorites(state, favorites) {
            state.favorites = favorites;
        },
        addFavorite(state, favorite) {
            state.favorites.push(favorite.data);
        },
        removeFavorite(state, favorite) {
            const index = state.favorites.findIndex(item => item.name === favorite.data.name);
            if (index > -1) state.favorites.splice(index, 1);
        }
    },
    getters: {
        authenticated(state) {
            return !!state.id;
        },
        initials(state) {
            return state.name ?
                state.name
                    .split(' ')
                    .filter(word => word.trim().length)
                    .map(word => word.trim()[0])
                    .join('') :
                ''
        },
        roles(state) {
            return state.roles;
        },
        permissions(state) {
            return state.roles
                .filter(role => role.permissions)
                .map(role => role.permissions)
                .reduce((carry, perm) => carry.concat(perm), []);
        },
        can(state, getters) {
            return permissions => {
                let hasAbility = false;
                let allowedAbilities = Array.isArray(permissions) ? permissions : [permissions];
                getters.permissions.forEach(perm => {
                    if (allowedAbilities.find(ability => ability === perm.name)) {
                        hasAbility = true
                    }
                });
                return hasAbility;
            }
        },
        is(state) {
            return roles => {
                let isRole = false;
                let allowedRoles = Array.isArray(roles) ? roles : [roles];
                state.roles.forEach(role => {
                    if (role.slug === 'administrator1' || (allowedRoles.find(r => r.toLowerCase() === role.name.toLowerCase()))) {
                        isRole = true
                    }
                });
                return isRole
            }
        }
    },
    actions: {
        getFavorites({commit}) {
            return userService.favorites({user_id: 1})
                .then(favorites => {
                    commit('setFavorites', favorites)
                    return favorites;
                }).catch((e) => {
                    return Promise.reject(e)
                })
        },
        login({dispatch}, credentials) {
            return api.post('/auth/login', {
                'client_id': cfg.client_id,
                'client_secret': cfg.client_secret,
                'username': credentials.username,
                'password': credentials.password,
                'grant_type': 'password'
            }, {
                withCredentials: true
            }).then(response => {
                let accessToken = response.data.access_token;
                localStorage.setItem('btc_access_token', accessToken);
                return dispatch('get');
            }).catch((e) => {
                return Promise.reject(e)
            })
        },
        get({commit}) {
            return api.get('/1.1/me')
                .then(response => {
                    commit('set', response.data);
                    return response.data
                }).catch((err) => {
                    return Promise.reject(err)
                });
        },
        logout({commit}) {
            return api.post('/auth/logout', {}, {withCredentials: true})
                .then(() => {
                    localStorage.removeItem('btc_access_token');
                    commit('set', guest)
                    return guest;
                }).catch(err => Promise.reject(err));
        }
    }
}
