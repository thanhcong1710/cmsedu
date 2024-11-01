<!--
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 *  Apax ERP System
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */-->

<template>
  <div class="app">
    <AppHeader fixed>
      <div id="digit">
        <span>{{clock.hours}}</span><span>:</span>
        <span>{{clock.minutes}}</span>
      </div>
      <SidebarToggler class="d-lg-none" display="md" mobile />
      <b-link class="navbar-brand" to="/dashboard">
        <!-- <img class="navbar-brand-full" src="images/brand/logo.svg" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src="images/brand/sygnet.svg" width="30" height="30" alt="CoreUI Logo"> -->
      </b-link>
      <SidebarToggler class="d-md-down-none" display="lg" />
      <div style="display: flex; width: calc(100% - 274px);">
        <div class="quick-links-bar-1" style="flex: 1; padding-right: 10px">
          <!--<router-link v-b-tooltip.hover class="quick-link" title="Thêm học sinh mới" to="/students/add-student" v-if="show.student">-->
          <!--<i class='icon-user-follow'></i> Thêm Học Sinh-->
          <!--</router-link>-->
          <!--<router-link v-b-tooltip.hover class="quick-link" title="Nhập học cho học sinh" to="/contracts/add" v-if="show.contract">-->
          <!--<i class='icon-power'></i> Nhập Học-->
          <!--</router-link>-->
          <!--&lt;!&ndash; <router-link v-b-tooltip.hover class="quick-link" title="Tái tục cho học sinh" to="/recharges/add">-->
          <!--<i class='icon-reload'></i> Tái Phí-->
          <!--</router-link> &ndash;&gt;-->
          <!--<router-link v-b-tooltip.hover class="quick-link" title="Tái phí cho học sinh" to="/recharges/add" v-if="show.recharge">-->
          <!--<i class='icon-credit-card'></i> Tái phí-->
          <!--</router-link>-->
          <!--<router-link v-b-tooltip.hover class="quick-link" title="Xếp lớp cho học sinh" to="/enrolments" v-if="show.enrolment">-->
          <!--<i class='icon-rocket'></i> Xếp Lớp-->
          <!--</router-link>-->
          <!--<router-link v-b-tooltip.hover class="quick-link" title="Điểm danh cho học sinh" to="/attendances" v-if="show.attendance">-->
          <!--<i class='icon-note'></i> Điểm danh-->
          <!--</router-link>-->
          <Notification />
        </div>
      <div class="account-information-">
        <div class="account-detail">
          <div class="account-name">Xin chào! <span class="user-name"><b>{{ user_name }}</b> - <b>{{ user_account }}</b>, Mã: {{ user_hrm }}{{ user_accounting_id }}</span></div>
          <div class="branch-info">Vị trí <span class="user-info"><b>{{ user_title }}</b>, <b>{{ branch_name }}</b> </span></div>
        </div>
        <div class="quick-logout" @click="logout">
          Đăng xuất <i class="icon-logout"></i>
        </div>
      </div>
      </div>
      <AsideToggler class="d-none d-lg-block" />
      <AsideToggler class="d-lg-none" mobile />
    </AppHeader>
    <div v-show="!enable_system">
      <NotificationSystem @enableChange="enableSystemChange"/>
    </div>
    <template v-if="enable_system">
    <div class="app-body">
      <AppSidebar fixed>
        <SidebarHeader/>
        <SidebarForm/>
        <SidebarNav :navItems="nav"></SidebarNav>
        <SidebarFooter/>
        <SidebarMinimizer/>
      </AppSidebar>
      <main class="main">
        <div v-show="loading.action" class="ajax-load content-loading" style="position: fixed">
          <div class="load-wrapper">
            <div class="loader"></div>
            <div v-show="loading.action" class="loading-text cssload-loader">
              {{ loading.content }}
            </div>
          </div>
        </div>
        <breadcrumb :list="list"/>
        <div class="container-fluid">
          <router-view></router-view>
        </div>
      </main>
      <AppAside fixed>
        <!--aside-->
        <DefaultAside/>
      </AppAside>
    </div>
    <TheFooter>
      <!--footer-->
      <div>
        Copyright <a href="https://crm.cmsedu.vn">&copy;CMS Edu</a>
        <span class="ml-1"> 2018 all rights reserved.</span>
      </div>
      <div class="ml-auto">
        <span class="mr-1">Powered by</span>
        <a href="https://staging.cmsedu.vn">Ada Framework</a>
      </div>
    </TheFooter>
    <b-modal
      title="THÔNG BÁO"
      class="modal-primary"
      size="lg"
      v-model="modal_login"
      ok-variant="primary"
      ok-only
      :no-close-on-backdrop="true"
      :no-close-on-esc="true"
      :hide-header-close="true"
    >
      <div v-html="modal_login_message" style="color: #333"></div>
    </b-modal>
    </template>
  </div>
</template>

<script>
import menu from '../_nav'
import u from '../utilities/utility'
import a from '../utilities/authentication'
import { Header as AppHeader, SidebarToggler, Sidebar as AppSidebar, SidebarFooter, SidebarForm, SidebarHeader, SidebarMinimizer, SidebarNav, Aside as AppAside, AsideToggler, Footer as TheFooter, Breadcrumb } from '../components'
import DefaultAside from './DefaultAside'
import Notification from './Notification'
import NotificationSystem from './NotificationSystem'

export default {
  name: 'full',
  components: {
    AsideToggler,
    AppHeader,
    AppSidebar,
    AppAside,
    TheFooter,
    SidebarNav,
    Breadcrumb,
    DefaultAside,
    SidebarForm,
    SidebarFooter,
    SidebarHeader,
    SidebarToggler,
    SidebarMinimizer,
    Notification,
    NotificationSystem
  },
  data () {
    return {
      modal_login:false,
      modal_login_message:'',
      user: {
        id: 0,
        hrm: '',
        name: '',
        title: '',
        email: '',
        branch: '',
        region: '',
        avatar: null
      },
      user_id: 0,
      user_hrm: 0,
      user_name: '',
      user_title: '',
      user_email: '',
      user_phone: '',
      user_account: '',
      branch_name: '',
      region_name: '',
      user_avatar: '',
      loading: {
        action: false,
        content: 'Đang xử lý...'
      },
      nav: menu().items,
      show: {
        student: true,
        contract: true,
        recharge: true,
        enrolment: true,
        attendance: true
      },
      clock : {
        hours : '',
        minutes : '',
        seconds : ''
      },
      enable_system: false
    }
  },
  methods:{
    enableSystemChange(enable) {
      this.enable_system = enable
    },
    logout() {
      a.logout(this.$router)
    },
    checkRole(){
      switch (this.user.role_id) {
        case u.r.ec:
        case u.r.ec_leader:
          this.show.enrolment = false
          this.show.attendance = false
          break
        case u.r.head_cashier:
        case u.r.cashier:
          this.show = {
            student: false,
            contract: false,
            recharge: false,
            enrolment: false,
            attendance: false
          }
          break
        case u.r.teacher:{
          this.show.enrolment = false
          this.show.recharge = false
          this.show.student = false
          this.show.contract = false
          break
        }
        default:
          break
      }
    },
    updateTime() {
        let time = new Date()
        let hours = '0'+time.getHours()
        let minutes = '0'+time.getMinutes()
        let seconds = '0'+time.getSeconds()
        this.clock.hours = hours.slice(-2)
        this.clock.minutes = minutes.slice(-2)
        this.clock.seconds = seconds.slice(-2)
    }
  },
  created() {
    this.modal_login = false
    this.modal_login_message = '<p>Hệ thống CRM sẽ tiến hành nâng cấp hệ thống từ 22h00 ngày 09/08/2020(Chủ nhật) dự kiến mở lại vào 13/08/2020. Trong thời gian nâng cấp trung tâm sẽ không sử dụng được CRM.</p><p>Trung tâm kiểm tra lại các đơn duyệt chuyển phí, chuyển trung tâm, bảo lưu nếu còn thì phê duyệt hết. Sau 12h00 ngày 09/08/2020 tất cả những đơn chưa được duyệt CRM sẽ từ chối tất cả các đơn.</p><p>Các bé chưa nhập học bổng trung tâm cũng bổ sung đầy đủ tránh mất quyền lợi của học sinh.</p>'
    const session = u.session()
    this.user = session.user
    this.user_id = this.user.id
    this.user_hrm = this.user.hrm_id
    this.user_name = this.user.name
    this.user_title = this.user.title
    this.user_email = this.user.email
    this.zone_name = this.user.zone
    this.user_phone = this.user.phone
    this.user_account = this.user.nick
    this.user_avatar = this.user.avatar
    this.branch_name = this.user.branch

    this.checkRole()
    u.apax.$on('apaxLoading', data => {
      this.loading.action = data
    })
    setInterval(this.updateTime, 1000)
    this.updateTime()
  },
  computed: {
    user_accounting_id(){
      return this.user.accounting_id? `, Mã Cyber: ${this.user.accounting_id}`: null
    },
    name () {
      return this.$route.name
    },
    list () {
      return this.$route.matched
    }
  }
}
</script>
<style>
  #digit{
    position: absolute;
    left: 135px;
    display:flex;
  }
  #digit span{
    color: #9c0200;
    font-size: 18px;
    padding: 0 2px;
    font-weight: 600;
    text-shadow: 1px 1px 3px #f97514;
  }
</style>