/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let metaUser = document.querySelector('meta[name=user]');
if(metaUser && metaUser.content) {
    window.user = JSON.parse(metaUser.content)
    if(window.user.api_token) window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.user.api_token;
}


window.Vue = require('vue');
Vue.prototype.$http = axios;
Vue.prototype.$user = window.user;

const AppRoot = require('./components/rpac/RpacRoot.vue').default;

import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/ru-RU'
Vue.use(ElementUI, { locale });

const router = require('./router/rpac').default;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const rpac = new Vue({
    el: '#rpac',
    router,
    render: h => h(AppRoot),
});
