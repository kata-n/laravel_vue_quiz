import Vue from 'vue'
import router from './router.js'
import SocialSharing from 'vue-social-sharing'
import axios from 'axios'
import jQuery from "jquery";

// ここから追加
import MainPage from './components/page/MainPage'
// ここまで追加

window.$ = window.jQuery = jQuery;
require('bootstrap');

Vue.prototype.$http = axios;

Vue.use(SocialSharing);

new Vue({
  router: router,

  // ここから追加
  components: {
    app: MainPage
  }
  // ここまで追加

}).$mount('#app')