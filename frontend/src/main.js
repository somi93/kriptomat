import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import VueMeta from 'vue-meta'
Vue.use(VueMeta)

Vue.config.productionTip = false

import store from './store'

new Vue({
    vuetify,
    router,
    store,
    render: h => h(App)
}).$mount('#app')
