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

// import u from '../utilities/utility'

import Vue from 'vue'
import Router from 'vue-router'

// Import - Component
import User from './pages/users'
import Page from './pages/pages'
import Student from './pages/students'
import Supports from './pages/supports'
import Config from './pages/config'
import Report from './pages/reports'
import Operating from './pages/operating'
import Dashboard from './pages/dashboard'
import Accounting from './pages/accounting'
import Container from '../containers/AccessContainer'
import Authentication from '../utilities/authentication'
import Sms from './pages/sms'
Vue.use(Router)

export default new Router({
  mode           : 'history',
  linkActiveClass: 'open active',
  scrollBehavior : () => ({ y: 0 }),
  routes         : [
    Page.lab,
    Page.login,
    Page.sso_login,
    Page.switch_system,
    Page.login_backup,
    Page.tools,
    Page.forgot,
    Page.confirm,
    Page.print_issue,
    Page.print_reserve,
    Page.print_recycle,
    Page.print_contract,
    Page.print_register,
    Page.print_class_transfer,
    Page.print_trial_register,
    Page.print_branch_transfer,
    Page.print_tuition_transfer,
    Page.print_tuition_withdraw,
    Page.import_std,
    Page.import_user,
    Page.print_feedback,
    Page.import_contract,
    Page.import_payment,
    Page.import_class,
    Page.migrate,
    Page.import_discount_codes,
    Page.import_reserve,
    {
      path       : '/',
      beforeEnter: Authentication.checkAuthorize,
      redirect   : '/dashboard',
      name       : 'Trang Chủ',
      component  : Container,
      children   : [
        Dashboard.router,
        User.router,
        Student.router,
        Operating.router,
        Accounting.router,
        Report.router,
        Config.router,
        Supports.router,
        Sms.router,
      ],
    },
  ],
})
