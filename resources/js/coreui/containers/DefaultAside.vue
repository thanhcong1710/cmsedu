<template>
  <b-tabs>
    <b-tab>
      <template slot="title">
        <div v-b-tooltip.hover title="Các công cụ trợ giúp" class="tools-bar tab">
          <i class='icon-wrench'></i>
        </div>
      </template>
      <b-list-group class="list-group-accent">
        <b-list-group-item class="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
          Công cụ hỗ trợ
        </b-list-group-item>
        <div class="sidebar-content">
          <ul class="tools-list">
            <li class="tool item button">
              <div class="tool name" v-b-tooltip.hover title="Công cụ tính ngày học cuối theo ngày bắt đầu và số buổi học">
                <a href="/lab" target="_blank" class="out-page"><i class="icon-chemistry"></i> Mở trang Lab</a>
              </div>
            </li>
            <li class="tool item button" @click="$modal.show('tool-bar1')">
              <div class="tool name" v-b-tooltip.hover title="Công cụ tính ngày học cuối theo ngày bắt đầu và số buổi học">
                <i class="icon-magic-wand"></i> Tính ngày học cuối
              </div>
            </li>
            <li class="tool item button" @click="$modal.show('tool-bar2')">
              <div class="tool name" v-b-tooltip.hover title="Công cụ tính số buổi học giữa ngày bắt đầu và kết thúc">
                <i class="icon-puzzle"></i> Tính số buổi học
              </div>
            </li>
            <li class="tool item button" @click="$modal.show('tool-bar3')">
              <div class="tool name" v-b-tooltip.hover title="Công cụ tính chuyển phí">
                <i class="icon-calculator"></i> Tính chuyển phí
              </div>
            </li>
            <li class="tool item button">
              <div class="tool name" v-b-tooltip.hover title="Xem thời khóa biểu lịch học">
                <i class="icon-calendar"></i> Xem thời khóa biểu
              </div>
            </li>
            <li class="tool item button" @click="$modal.show('tool-bar4')">
              <div class="tool name" v-b-tooltip.hover title="Xem danh sách nhân viên trung tâm">
                <i class="icon-book-open"></i> Danh sách nhân viên
              </div>
            </li>
          </ul>
        </div>
        <ModalSession />
        <ModalUserBranch />
        <ModalTuition />
        <ModalEndDate />
      </b-list-group>
    </b-tab>
    <b-tab>
      <template slot="title">
        <div v-b-tooltip.hover title="Tài liệu hướng dẫn" class="manual-doc tab">
          <i class='icon-question'></i>
        </div>
      </template>
      <b-list-group class="list-group-accent">
        <b-list-group-item class="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
          Hướng dẫn sử dụng
        </b-list-group-item>
        <div class="sidebar-content">
          <ul class="manual-list">
            <li class="doc item button" :class="tongleManualCEO" @click="showManualCEO">
              <div class="doc name" v-b-tooltip.hover title="Tài liệu HDSD cho TK Giám Đốc Trung Tâm">
                <i class="fa fa-file-word-o"></i> HDSD TK Giám Đốc TT
              </div>
            </li>
            <li class="doc item button" :class="tongleManualCM" @click="showManualCM">
              <div class="doc name" v-b-tooltip.hover title="Tài liệu HDSD cho TK CS CS Leader">
                <i class="fa fa-file-word-o"></i> HDSD TK CS CS Leader
              </div>
            </li>
            <li class="doc item button" :class="tongleManualEC" @click="showManualEC">
              <div class="doc name" v-b-tooltip.hover title="Tài liệu HDSD cho TK EC Leader và EC">
                <i class="fa fa-file-word-o"></i> HDSD TK EC Leader và EC
              </div>
            </li>
            <li class="doc item button" :class="tongleManualAC" @click="showManualAC">
              <div class="doc name" v-b-tooltip.hover title="Tài liệu HDSD cho Kế toán hội sở">
                <i class="fa fa-file-word-o"></i> HDSD TK Kế toán hội sở
              </div>
            </li>
            <li class="doc item button" :class="tongleManualTC" @click="showManualTC">
              <div class="doc name" v-b-tooltip.hover title="Tài liệu HDSD cho Giáo viên">
                <i class="fa fa-file-word-o"></i> HDSD cho Giáo viên
              </div>
            </li>
          </ul>
        </div>
      </b-list-group>
    </b-tab>
    <b-tab>
      <template slot="title">
        <div v-b-tooltip.hover title="Cập nhật hồ sơ nhân viên" class="update-profile tab">
          <i class='icon-briefcase'></i>
        </div>
      </template>
      <b-list-group class="list-group-accent">
        <b-list-group-item class="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
          Cập nhật tài khoản
        </b-list-group-item>
        <div class="sidebar-content">
          <employee-profile :user="user" />
        </div>
      </b-list-group>
    </b-tab>
    <b-tab>
      <template slot="title">
        <div v-b-tooltip.hover title="Thông báo" class="change-pass tab">
          <i class='icon-notebook'></i>
        </div>
      </template>
      <b-list-group class="list-group-accent">
        <b-list-group-item class="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
          Bạn có 5 ghi chú
        </b-list-group-item>
        <div class="sidebar-content">
          <ul class="noted-list">
            <li class="noted item">
              <div class="noted notification frame" v-b-tooltip.hover title="Hệ thống thông báo">
                <div class="noted notification title">
                  <i class="icon-star"></i> <span>Hệ thống xin thông báo</span>
                </div>
                <div class="noted notification content">
                  Mật khẩu của bạn đã quá hạn hiệu lực, vì lý do bảo mật xin vui lòng thay đổi mật khẩu mới.
                </div>
              </div>
            </li>
            <li class="noted item">
              <div class="noted message frame" v-b-tooltip.hover title="Tin nhắn từ Nguyễn Văn Nam - EC">
                <div class="noted message title">
                  <i class="icon-speech"></i> <span>Tin nhắn từ Nguyễn Văn Nam</span>
                </div>
                <div class="noted message content">
                  Xếp phê duyệt giùm em học sinh Trần Kiều Trang chuyển trung tâm với ạ.
                </div>
              </div>
            </li>
            <li class="noted item">
              <div class="noted confirm link" v-b-tooltip.hover title="Phê duyệt chuyển trung tâm cho Trần Kiều Trang">
                <router-link class="nav-item nav-dropdown" to="#">
                  <i class="icon-paper-plane"></i> Phê duyệt Trần Kiều Trang - chuyển trung tâm
                </router-link>
              </div>
            </li>
            <li class="noted item">
              <div class="noted enrolment link" v-b-tooltip.hover title="Xếp lớp cho Trương Ngọc Ánh">
                <router-link class="nav-item nav-dropdown" to="#">
                  <i class="icon-rocket"></i> <span>Xếp lớp - Trương Ngọc Ánh</span>
                </router-link>
              </div>
            </li>
            <li class="noted item">
              <div class="noted charging link" v-b-tooltip.hover title="Thu phí học sinh Nguyễn Văn Kiên">
                <router-link class="nav-item nav-dropdown" to="#">
                  <i class="cui-dollar"></i> <span>Thu phí - Nguyễn Văn Kiên</span>
                </router-link>
              </div>
            </li>
          </ul>
        </div>
      </b-list-group>
    </b-tab>
    <b-tab>
      <template slot="title">
        <div v-b-tooltip.hover title="Thiết lập cấu hình" class="setting-config tab">
          <i class='icon-settings'></i>
        </div>
      </template>
      <b-list-group class="list-group-accent">
        <b-list-group-item class="list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small">
          Thiết lập cấu hình
        </b-list-group-item>
        <div class="sidebar-content">
          <ul class="configs-list">
            <li class="config item button" :class="classFullScreen" @click="fullscreen">
              <div class="config input" v-b-tooltip.hover :title="msgFullScreen">
                <i :class='iconFullScreen'></i> {{ titleFullScreen }}
              </div>
            </li>
            <li class="config item button" :class="classNightMode" @click="nightmode">
              <div class="config input" v-b-tooltip.hover :title="msgNightMode">
                <i :class='iconNightMode'></i> {{ titleNightMode }}
              </div>
            </li>
            <li class="config item button" :class="classCollapseMenu" @click="sidebarMinimize">
              <div class="config input" v-b-tooltip.hover :title="msgCollapseMenu">
                <i :class='iconCollapseMenu'></i> {{ titleCollapseMenu }}
              </div>
            </li>
            <li class="config item button" @click="sendMail">
              <div class="config input" v-b-tooltip.hover title="Gửi email yêu cầu khối CNVH trợ giúp">
                <i class='icon-support'></i> Gửi email yêu cầu hỗ trợ
              </div>
            </li>
            <li class="config item button" @click="logout">
              <div class="config input" v-b-tooltip.hover title="Đăng xuất ra khỏi tài khoản">
                <i class='icon-logout'></i> Đăng xuất ra
              </div>
            </li>
          </ul>
        </div>
      </b-list-group>
    </b-tab>
  </b-tabs>

</template>

<script>
import u from '../utilities/utility'
import a from '../utilities/authentication'
import file from '../views/components/File'
import abt from '../views/components/Button'
import profile from '../views/components/Profile'
import EmployeeProfile from '../views/components/EmployeeProfile'
import AutoChangePassword  from '../views/components/AutoChangePassword.vue'
import selection from 'vue-select'
import moment from 'moment'
import cookies from 'vue-cookies'
import datepicker from 'vue2-datepicker'
import HeaderDropdown from './DefaultHeaderDropdown.vue'
import HeaderDropdownAccnt from './DefaultHeaderDropdownAccnt.vue'
import HeaderDropdownTasks from './DefaultHeaderDropdownTasks.vue'
import HeaderDropdownNotif from './DefaultHeaderDropdownNotif.vue'
import HeaderDropdownMssgs from './DefaultHeaderDropdownMssgs.vue'
import ModalSession from './ModalSession.vue'
import ModalUserBranch from './ModalUserBranch.vue'
import ModalTuition from './ModalTuition.vue'
import ModalEndDate from './ModalEndDate.vue'
import { Switch as cSwitch } from '@coreui/vue'

export default {
  name: 'DefaultAside',
  components: {
    EmployeeProfile,
    abt,
    file,
    profile,
    selection,
    datepicker,
    HeaderDropdown,
    HeaderDropdownAccnt,
    HeaderDropdownTasks,
    HeaderDropdownNotif,
    HeaderDropdownMssgs,
    AutoChangePassword,
    cSwitch,
    ModalSession,
    ModalUserBranch,
    ModalTuition,
    ModalEndDate
  },
  data() {
    return {
      update: false,
      user_id: '',
      message: '',
      user_zone: '',
      user_name: '',
      user_email: '',
      user_branch: '',
      class_name: '',
      user_phone: '',
      superior_id: '',
      user_account: '',
      user_avatar: '',
      modal: false,
      tftfid: 0,
      tfamnt: 0,
      brchid: 0,
      prodid: 0,
      tftfif: '',
      rctfif: '',
      sptfif: 0,
      sstfif: 0,
      tfsess: 0,
      lang: 'en',
      item: '',
      list: [],
      class: {},
      user_list: [],
      total_session: 0,
      user_superior_id: '',
      last_date_result: '',
      branch: '',
      learningdate: '',
      learningdates: [],
      date2: '',
      dates2: [],
      total: '',
      product: '',
      classid: 55,
      branchid: 0,
      productid: 0,
      tuition: '',
      classes: [],
      holidays: [],
      branches: [],
      tuitions: [],
      holidates: [],
      tuitionids: [],
      tuition_id: [],
      productTransfer: '',
      branchTransfer: '',
      selectedclass: '',
      products: [
        {id: 1, name: 'iGarten'},
        {id: 2, name: 'April'},
        {id: 3, name: 'CDI 4.0'}
      ],
      classdays: [2,4],
      useholidays: 1,
      class_date_list: [
        {id: 0, name: '0 - Sunday'},
        {id: 1, name: '1 - Monday'},
        {id: 2, name: '2 - Tuesday'},
        {id: 3, name: '3 - Wednesday'},
        {id: 4, name: '4 - Thursday'},
        {id: 5, name: '5 - Friday'},
        {id: 6, name: '6 - Saturday'}],
      classdates: [{id: 2, name: '2 - Tuesday'},{id: 4, name: '4 - Thursday'}],
      start_date: this.moment().format('YYYY-MM-DD'),
      end_date: '',
      begin: '',
      end: '',
      specia: 0,
      sessions: 24,
      user: {
        id: '',
        name: '',
        title: '',
        email: '',
        avatar: null
      },
      collapseMenu: false,
      classCollapseMenu: '',
      iconCollapseMenu: 'icon-options-vertical',
      msgCollapseMenu: 'Thu nhỏ trình đơn về dạng biểu tượng',
      titleCollapseMenu: 'Thu nhỏ cột trình đơn',
      classNightMode: '',
      iconNightMode: 'cui-moon',
      msgNightMode: 'Chuyển sang giao diện ban đêm',
      titleNightMode: 'Giao diện xem ban đêm',
      classFullScreen: '',
      iconFullScreen: 'icon-size-fullscreen',
      msgFullScreen: 'Chuyển sang chế độ xem toàn màn hình',
      titleFullScreen: 'Giao diện toàn màn hình',
      I_am_leader: false,
      user_start_date: '',
      checkSuperiorShow: true,
      checkIsSuperiorShow: false,
      usingGuide: false,
      usingGuideUrl: '',
      tongleManualCEO: '',
      tongleManualCM: '',
      tongleManualAC: '',
      tongleManualEC: '',
      tongleManualTC: '',
    }
  },
  created(){
    const session = u.session()
    this.checkUserRole(session)
    this.user = session.user
    this.user_id = this.user.id
    this.user_name = this.user.name
    this.user_title = this.user.title
    this.user_phone = this.user.phone
    this.user_email = this.user.email
    this.user_avatar = this.user.avatar
    this.user_hrm_id = this.user.hrm_id
    this.user_account = this.user.nick
    this.user_zone = this.user.zone
    this.user_branch = this.user.branch
    this.user_start_date = this.user.start_date
    this.user_superior_id = this.user.superior_id
    if (this.user.hrm_id === this.user.superior_id) {
      this.I_am_leader = true
    }
    if (u.role(this.user.role_id) === 'ec_leader') {
      this.checkSuperiorShow = false
      this.checkIsSuperiorShow = true
    }
    const information = session.info
    this.user_list = information.users_list
    const branch_ids = this.user.branch_id.split(',')
    const branch_id = branch_ids[0]
    this.branch = information.branches[0]
    this.branchid = this.user.branch_id
    this.branches = information.branches
    this.products = information.products
    this.holidays = information.holidays
    this.tuitions = information.tuitions
    this.classes = information.classes
    if (u.chk.boss() && u.role(this.user.role_id) === 'ec') {
      this.update = true
      this.class_name = 'danger'
      this.message = 'Tài khoản của bạn chưa được cập nhật thông tin mã HRM (Mã G) thủ trưởng, xin vui lòng bổ sung thông tin này trong hồ sơ tài khoản của bạn để kích hoạt các chức năng của phần mềm.'
    }
  },
  methods: {
    sendMail() {
      const who = `${this.user.branch} - ${this.user.title} ${this.user.name} (${this.user.nick}) Yêu Cầu Hỗ Trợ`
      const msg = `Kính gửi các anh chị khối Công Nghệ Vận Hành,\n\nTôi, ${this.user.name} (${this.user.nick}) là ${this.user.title} thuộc trung tâm ${this.user.branch} cần được trợ giúp về phần mềm CRM.`
      window.open(`mailto:erp.support@apaxenglish.com?subject=${who}&body=${msg}`);
    },
    checkUserRole(data){
      const role_id = data.user.role_id
      // console.log('test rollllll', role_id);
      if(role_id ===83){
        this.usingGuide = true
      }else if(role_id ===55 ||role_id ===56 ){
        this.usingGuide = true
      }else if(role_id ===68 ||role_id ===69){
        this.usingGuide = true
      }
    },
    uploadFile(file, param = null) {
      if (param) {
        this.user.avatar = file
      }
    },
    showManualAC(){
      u.apax.$emit('apaxPopup', {
        on: true,
        content: `<iframe width="100%" height="500px" src='/static/doc/html/HDSD_CRM_Vai_tro_thu_ngan.html'></iframe>`,
        title: 'Hướng dẫn sử dụng cho tài khoản người dùng Kế toán hội sở',
        class: 'modal-success',
        size: 'lg',
        variant: 'apax-ok'
      })
    },
    resetTongleManual() {
      this.tongleManualCEO = ''
      this.tongleManualCM = ''
      this.tongleManualEC = ''
      this.tongleManualAC = ''
      this.tongleManualTC = ''
    },
    showManualCM(){
      this.resetTongleManual()
      this.tongleManualCM = 'active'
      u.apax.$emit('apaxPopup', {
        on: true,
        content: `<iframe width="100%" height="500px" src='/static/doc/html/HDSD_CRM_Vai_tro_CM_OM.html'></iframe>`,
        title: 'Hướng dẫn sử dụng cho tài khoản người dùng CS CS Leader',
        class: 'modal-success',
        size: 'lg',
        hidden: () => this.resetTongleManual(),
        confirm: {
            primary: {
              button: 'OK',
              action: () => this.resetTongleManual(),
            }
        },
        variant: 'apax-ok'
      })
    },
    showManualEC(){
      this.resetTongleManual()
      this.tongleManualEC = 'active'
      u.apax.$emit('apaxPopup', {
        on: true,
        content: `<iframe width="100%" height="500px" src='/static/doc/html/HDSD_CRM_Vai_tro_EC_EC_Leader.html'></iframe>`,
        title: 'Hướng dẫn sử dụng cho tài khoản người dùng EC/EC Leader',
        class: 'modal-success',
        size: 'lg',
        hidden: () => this.resetTongleManual(),
        confirm: {
            primary: {
              button: 'OK',
              action: () => this.resetTongleManual(),
            }
        },
        variant: 'apax-ok'
      })
    },
    showManualTC(){
      this.resetTongleManual()
      this.tongleManualTC = 'active'
      u.apax.$emit('apaxPopup', {
        on: true,
        content: `<iframe width="100%" height="500px" src='/static/doc/html/Software_Manual_CRM_System_Teachers.html'></iframe>`,
        title: 'Software Manual CRM System for Giáo viên',
        class: 'modal-success',
        size: 'lg',
        hidden: () => this.resetTongleManual(),
        confirm: {
            primary: {
              button: 'OK',
              action: () => this.resetTongleManual(),
            }
        },
        variant: 'apax-ok'
      })
    },
    showManualCEO(){
      this.resetTongleManual()
      this.tongleManualCEO = 'active'
      u.apax.$emit('apaxPopup', {
        on: true,
        content: `<iframe width="100%" height="500px" src='/static/doc/html/HDSD_CRM_Vai_tro_GDTT_GDV.html'></iframe>`,
        title: 'Hướng dẫn sử dụng cho tài khoản người dùng Giám Đốc Trung Tâm',
        class: 'modal-success',
        size: 'lg',
        hidden: () => this.resetTongleManual(),
        confirm: {
            primary: {
              button: 'OK',
              action: () => this.resetTongleManual(),
            }
        },
        variant: 'apax-ok'
      })
    },
    fullscreen(){
      if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
        this.iconFullScreen = 'icon-size-actual'
        this.classFullScreen = 'active'
        this.titleFullScreen = 'Giao diện màn hình thường'
        this.msgFullScreen = 'Trở lại giao diện màn hình thường'
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
      } else {
        this.iconFullScreen = 'icon-size-fullscreen',
        this.classFullScreen = ''
        this.titleFullScreen = 'Giao diện toàn màn hình'
        this.msgFullScreen = 'Chuyển sang chế độ xem toàn màn hình'
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        }
      }
    },
    nightmode(){
      const x = document.getElementById('total-general-frame').className
      if (x === '' || x === null) {
        this.iconNightMode = 'cui-sun',
        this.classNightMode = 'active'
        this.titleNightMode = 'Giao diện bình thường'
        this.msgNightMode = 'Trở về giao diện màu bình thường'
        document.getElementById('total-general-frame').className = 'night-mode'
      } else {
        this.iconNightMode = 'cui-moon',
        this.classNightMode = ''
        this.titleNightMode = 'Giao diện xem ban đêm'
        this.msgNightMode = 'Chuyển sang giao diện xem ban đêm'
        document.getElementById('total-general-frame').className = ''
      }
    },
    downloadUsingGuide(){
      // console.log('test', this.user.role_id);
      const role_id = this.user.role_id
      var p2 = `/api/users/download-using-guide/${role_id}`;
      window.open(p2, '_blank');
    },
    calculatingEndate() {

    },
    selectTuition(val) {
      this.tftfid = val.id
      this.calcTransfer()
    },
    selectBranchTransfer(val) {
      this.brchid = val.id
      this.calcTransfer()
    },
    selectProductTransfer(val) {
      this.prodid = val.id
      this.calcTransfer()
    },
    logout() {
      a.logout(this.$router)
    },
    updateProfile() {
      this.update = true
    },
    selectBranch(branch) {
      this.branchid = branch.id
      u.g(`${u.url.host}/api/settings/branch/class/${branch.id}`)
      .then((response) => {
        this.classes = response.classes
      }).catch(e => u.log('Exeption', e))
    },
    selectProduct(product) {
      this.productid = product.id
      if (this.productid && this.classid) {
        u.g(`/api/settings/holidates/${this.classid}/${this.productid}`)
        .then(response => {
          this.holidays = response.holidays
          this.holidates = response.holidates
        })
      }
    },
    selectClass(classinfo) {
      this.classid = classinfo.id
      if (this.productid && this.classid) {
        u.g(`/api/settings/holidates/${this.classid}/${this.productid}`)
        .then(response => {
          this.holidays = response.holidays
          this.holidates = response.holidates
        })
      }
    },
    selectClassdays(val) {
      let valid = true
      let markup = ''
      let message = ''
      if (val.length < 2 || val.length > 4) {
        valid = false
        markup = 'danger'
        message = 'Số lượng buổi học thường là 2 buổi trên 1 tuần.'
      } else if (val.length !== 2) {
        markup = 'caution'
        message = 'Lưu ý, mỗi tuần thường chỉ có 2 buổi học.'
      }
      if (valid) {
        this.classdays = []
        val.map(itm=>this.classdays.push(itm.id))
      }
      if (message !== '') {
        this.$notify({
          group: 'apax-atc',
          title: 'Lưu Ý',
          type: markup,
          duration: 700,
          text: message
        })
      }
    },
    calculateLastDate() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.last-date').addClass('active')
      $('.tool-bar .tool-input.current').removeClass('current')
      $('.tool-bar .tool-input.session-number').addClass('current')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.primary').addClass('active')
      if (this.start_date && this.sessions && this.classdays.length && this.holidates.length) {
        const start_date = this.moment(this.start_date).format('YYYY-MM-DD')
        const info = u.getRealSessions(parseInt(this.sessions, 10), this.classdays, this.holidates, start_date)
        this.last_date_result = info.end_date
        this.learningdates = info.dates
        this.total_session = 0
      }
    },
    calculateSession() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.last-date').addClass('active')
      $('.tool-bar .tool-input.current').removeClass('current')
      $('.tool-bar .tool-input.last-date').addClass('current')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.success').addClass('active')
      if (this.start_date && this.end_date && this.classdays.length && this.holidates.length) {
        const start_date = this.moment(this.start_date).format('YYYY-MM-DD')
        const end_date = this.moment(this.end_date).format('YYYY-MM-DD')
        const info = u.calcDoneSessions(start_date, end_date, this.holidates, this.classdays)
        this.total_session = info.total
        this.learningdates = info.dates
        this.last_date_result = ''
      }
    },
    calculateTransfer() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.tuition-transfer').addClass('active')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.error').addClass('active')
      this.calcTransfer()
    },
    viewUsersListing() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.user-list').addClass('active')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.caution').addClass('active')
    },
    beforeClose() {

    },
    changeSetting() {
      // this.$notify({
      //   group: 'apax-atc',
      //   title: 'Lưu Ý',
      //   type: 'info dark',
      //   duration: 100000,
      //   text: 'Xin vui lòng chọn loại khách hàng!'
      // })

      // u.apax.$emit('apaxModal', {
      //   body: '<b style="color:red">content body modal</b>',
      //   title: 'title  of modal',
      //   class: 'modal-primary',
      //   bopen:() => {
      //     u.log('MODAL BEFORE OPENED', 'Method emit before open modal successful!')
      //   },
      //   opened(){
      //     u.log('MODAL EVENT OPENED', 'function emit successful!')
      //   },
      //   bclose(){
      //     u.log('MODAL BEFORE CLOSED', 'function emit successful!')
      //   },
      //   closed:() => {
      //     u.log('MODAL EVENT CLOSED', 'function emit successful!')
      //   }
      // })
      // this.$modal.show('apax-modal')

      // u.apax.$emit('apaxPopup', {
      //   on: true,
      //   show(){ u.log('POPUP HAS BEEN SHOWED') },
      //   content: '<b style="color:green">content body popup</b>',
      //   title: 'title  of popup',
      //   class: 'modal-primary',
      //   size: 'lg',
      //   close(){ u.log('POPUP HAS BEEN CLOSED') },
      //   action: () => { u.log('POPUP ACTION OK') },
      //   variant: 'apax-ok'
      // })
    },
    saveProfile() {
      const data = {
        id: this.user.id,
        full_name: this.user_name,
        action_self_update_profile: true,
        // email: this.user_email,
        phone: this.user_phone,
        avatar: this.user.avatar,
        is_leader: this.I_am_leader,
        // hrm_id: this.user_hrm_id,
        // username: this.user_account,
        start_date: this.user_start_date,
        superior_id: this.user_superior_id
      }
      let valid = true
      let msg = ''
      if (data.full_name === '') {
        valid = false
        msg += '<i style="color:red">Tên nhân viên là trường bắt buộc không thể để trống.</i><br>'
      }
      // if (data.hrm_id === '') {
      //   valid = false
      //   msg += '<i style="color:red">Mã HRM nhân viên là trường bắt buộc không thể để trống.</i><br>'
      // }
      if (data.start_date === '') {
        valid = false
        msg += '<i style="color:red">Ngày bắt đầu làm việc là trường bắt buộc không thể để trống.</i><br>'
      }
      // if (data.username === '') {
      //   valid = false
      //   msg += '<i style="color:red">Tài khoản đăng nhập là trường bắt buộc không thể để trống.</i><br>'
      // }
      // if (data.email === '') {
      //   valid = false
      //   msg += '<i style="color:red">Email nhân viên là trường bắt buộc không thể để trống.</i><br>'
      // } else if (u.vld.email(String(data.email).toLowerCase()) && String(data.email).toLowerCase().indexOf('apaxenglish.com') === -1) {
      //   valid = false
      //   msg += '<i style="color:red">Email nhân viên không hợp lệ vui lòng kiểm tra đây phải là mail của ApaxEnglish.</i><br>'
      // }
      if (valid) {
        const user = this.user
        u.p(`/api/users/${user.id}/update-users-profile`, data).then(response => {
          console.log(response)
          if(response.success == true) {
            this.class_name = 'success'
            alert('Thông tin tài khoản đã được cập nhật thành công!')
            this.message = `Thông tin tài khoản người dùng <b>${data.full_name} (${data.username})</b> đã được cập nhật thành công!`
            a.logout(this.$router)
          } else {
              this.class_name = 'danger'
              this.message = response.message
          }
        })
      } else {
        this.class_name = 'danger'
        this.message = msg
      }
    },
    uploadAvatar(file, param = null) {
      if (param) {
        this.user[param] = file
      }
    },
    validFile(file) {
      let resp = file && (typeof file === 'string') ? file : ''
      if (file && typeof file === 'object') {
        resp = `${file.type},${file.data}`
      }
      return resp
    },
    imageChanged(e) {
      var fileReader = new FileReader();
      fileReader.readAsDataURL(e.target.files[0])
      fileReader.onload = (e) => {
        this.user.avatar = e.target.result
      }
    },
    onDrawStartDate(e) {
      let date = e.date
      // if (this.current_start_date > date.getTime()) {
      //   e.allowSelect = false
      // }
    },
    sidebarToggle(e) {
      e.preventDefault();
      document.body.classList.toggle("sidebar-hidden");
    },
    sidebarMinimize(e) {
      if (!this.collapseMenu) {
        this.collapseMenu = true
        this.classCollapseMenu = 'active'
        this.iconCollapseMenu = 'icon-list'
        this.titleCollapseMenu = 'Mở rộng cột trình đơn'
        this.msgCollapseMenu = 'Mở rộng cột trình đơn về dạng đầy đủ'
      } else {
        this.collapseMenu = false
        this.classCollapseMenu = ''
        this.iconCollapseMenu = 'icon-options-vertical'
        this.titleCollapseMenu = 'Thu nhỏ cột trình đơn'
        this.msgCollapseMenu = 'Thu nhỏ trình đơn về dạng biểu tượng'
      }
      e.preventDefault();
      document.body.classList.toggle("sidebar-minimized");
    },
    mobileSidebarToggle(e) {
      e.preventDefault();
      document.body.classList.toggle("sidebar-mobile-show");
    },
    asideToggle(e) {
      e.preventDefault();
      document.body.classList.toggle("aside-menu-hidden");
    },
    selectStartDate(start_date) {
      u.log('START DATE', start_date)
    },
    changePassword() {
      this.modal = true
    },

    // load() {
    //   u.g(`/api/settings/holidays/${this.classid}/${this.productid}`)
    //       .then(response => {
    //     const data = response
    //     if (parseInt(this.useholidays) === 1) {
    //       this.list = data.holidays
    //     } else {
    //       this.list = []
    //     }
    //     const start = this.start
    //     const sessions = parseInt(this.sessions, 10)
    //     // this.classdays = data.classdays.length ? data.classdays : this.classdates.split(',')
    //     // this.classdates = this.classdays.join(',')
    //     this.classdays = this.classdates.split(',')
    //     this.begin = this.start
    //     const d1 = '2018-04-12'
    //     // console.log(`data.holidays: ${JSON.stringify(data.holidays)}\n\n\n`)
    //     const dc = u.checkInDaysRange(d1, data.holidays)
    //     // console.log(`Check the date "${d1}" in holidays is: ${dc}\n\n\n\n\n`)
    //     u.log('USE HOLIDAYS', parseInt(this.useholidays), this.list)
    //     let x = null
    //     let z = null
    //     if (parseInt(this.useholidays) === 1) {
    //       u.log('xxxxx')
    //       x = u.getRealSessions(sessions, this.classdays, data.holidays, start)
    //       u.log('Result last date: ', x)
    //       this.end = x.end_date
    //       this.dates1 = x.dates
    //     } else {
    //       u.log('zzzzz')
    //       this.holidays = []
    //       x = u.getRealSessions(sessions, this.classdays, [], start)
    //       u.log('Result last date: ', x)
    //       this.end = x.end_date
    //       this.dates1 = x.dates
    //     }
    //     if (parseInt(this.useholidays) === 1) {
    //       u.log('xxxxx')
    //       this.holidays = data.holidays
    //       z = u.calcDoneSessions(this.begin, this.end, data.holidays, this.classdays)
    //       u.log('Result done sess: ', z)
    //       this.dates2 = z.dates
    //       this.total = z.total
    //     } else {
    //       u.log('zzzzz')
    //       this.holidays = []
    //       z = u.calcDoneSessions(this.begin, this.end, [], this.classdays)
    //       u.log('Result done sess: ', z)
    //       this.dates2 = z.dates
    //       this.total = z.total
    //     }
    //   }).catch(e => console.log(e));
    // },
    calcTransfer() {
      if (this.tftfid && this.tfamnt && this.brchid && this.prodid) {
        const params = {
          tftfid: this.tftfid,
          tfamnt: this.tfamnt,
          brchid: this.brchid,
          prodid: this.prodid,
          tfsess: this.tfsess
        }
        u.g(`/api/scope/test/transfer/${JSON.stringify(params)}`)
          .then(response => {
            u.log('response', response)
            this.tftfif = response.transfer_tuition_fee
            this.rctfif = response.receive_tuition_fee
            this.sptfif = response.single_price
            this.sstfif = response.sessions
            this.specia = response.special
        }).catch(e => console.log(e))
      }
    },
    prepare() {
      const z = u.calcDoneSessions(this.begin, this.end, this.holidays, this.classdays)
      this.dates2 = z.dates
      this.total = z.total
    },
    doSelect(val) {
      u.log('Select', val)
    },
    withDraw(){
      var x = confirm("Bạn có chắc withdraw ?");

      if (x)
        u.a().get(`/api/daily-checking-withdraw-status`).then(response => {
          console.log(response);
        })
      else
        return false;
    },
    showModal(){
      this.modal = true
    }
  }
}
</script>
<style>
  .aside-menu-off-canvas .aside-menu{
    z-index: 1022
  }
</style>
