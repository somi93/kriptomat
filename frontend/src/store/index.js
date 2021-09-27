import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex);

import user from '@/store/user'
import coin from '@/store/coin'

const store = new Vuex.Store({
    modules: {
        user,
        coin
    }
});

export default store;