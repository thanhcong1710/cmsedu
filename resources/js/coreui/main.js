/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

import Vue from 'vue'
import App from './App'
import uuid from 'vue-uuid'
import router from './router'
import VueVents from 'vue-events'
import VueRouter from 'vue-router'
// import VueLodash from 'vue-lodash'
import VeeValidate from 'vee-validate'
import BootstrapVue from 'bootstrap-vue'
import VueGoodTable from 'vue-good-table'
import Notifications from 'vue-notification'
import 'vue-good-table/dist/vue-good-table.css'
import './utilities/filters'
import './utilities/directives'
import store from './store'
import modal from 'vue-js-modal'

window._ = require('lodash')

Vue.use(uuid)
Vue.use(modal)
Vue.use(VueVents)
Vue.use(VueRouter)
Vue.use(VeeValidate)
Vue.use(VueGoodTable)
Vue.use(BootstrapVue)
Vue.use(Notifications)
// Vue.use(VueLodash, { name: 'lodash' })

/* eslint-disable no-new */
new Vue({
  el        : '#app',
  router,
  store,
  template  : '<App/>',
  components: { App  },
})
