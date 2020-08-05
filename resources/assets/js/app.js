import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import axios from "axios";
import vuetify from './plugins/vuetify';
import store from "./store";
import VeeValidate from 'vee-validate';//

Vue.use(VeeValidate); 

Vue.config.productionTip = false;
Vue.prototype.$http = axios;

const app = new Vue({
    el: '#app',
    components: { App },
    router,
    vuetify,
    store,
});
