import coinService from '@/services/coin'
import moment from 'moment'

export default {
    namespaced: true,
    state: {
        coins: [],
        updated: moment().toISOString()
    },
    mutations: {
        setCoins(state, coins) {
            state.coins = state.coins.concat(coins);
            state.updated = moment().toISOString();
        },
        emptyCoins(state) {
            state.coins = [];
        }
    },
    getters: {
        coins(state) {
            return state.coins;
        }
    },
    actions: {
        getCoins({commit, state}, fromCache = true) {
            const returnFromCache = !fromCache && moment().diff(state.updated, 'm') < 1 && state.coins.length;
            const getCoins = (page) => {
                return coinService.market({perPage: 10, page})
                    .then(coins => {
                        commit('setCoins', coins)
                        return state.coins;
                    }).catch((e) => {
                        return Promise.reject(e)
                    })
            }
            const page = returnFromCache ? Math.floor(state.coins.length / 10 + 1) : 1;
            if(!returnFromCache) commit('emptyCoins')
            return getCoins(page);
        },
    }
}
