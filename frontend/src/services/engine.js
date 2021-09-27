import Vue from 'vue'
import config from "@/config";
import axios from 'axios'

if(!Vue.prototype.$api) {
    Vue.use({
        install(Vue) {
            Vue.prototype.$api = axios.create({
                baseURL: config.URL
            })
        }
    })
}
const api = Vue.prototype.$api;
export {
    api
}