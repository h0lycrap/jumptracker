import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import axios from "axios";
import vuetify from './plugins/vuetify';
import auth from './auth';
import VueAuth from '@websanova/vue-auth';
import VueAxios from 'vue-axios';
import VueRouter from 'vue-router';
import 'es6-promise/auto';




Vue.config.productionTip = false;
Vue.prototype.$http = axios;

// Set Vue router
Vue.router = router
Vue.use(VueRouter)

Vue.use(VueAxios, axios)
//axios.defaults.baseURL = `http://127.0.0.1:8000/api`
axios.defaults.baseURL = `http://urtjumptracker.herokuapp.com/api`
Vue.use(VueAuth, auth);

const app = new Vue({
    el: '#app',
    components: { App },
    router,
    vuetify,
});
