import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);

export default new Router({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'index',
            component: () => import('@/pages/index')
        },
        {
            path: '/coin/:id',
            name: 'coin-id',
            component: () => import('@/pages/coin/_id')
        }
    ]
})