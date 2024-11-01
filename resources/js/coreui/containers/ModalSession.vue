<template>
	<modal name="tool-bar2" @before-close="beforeClose" width="90%" height="96%" :draggable="true" :adaptive="true" :scrollable="true" :resizable="true" classes="tools-bar">
      <div class="tool-bar-frame modal-primary ada-modal">
        <header class="modal-header">
          <h5 class="modal-title">Công cụ hỗ trợ - Tính só buổi học</h5>
          <button @click="$modal.hide('tool-bar2')" class="close">×</button>
        </header>
        <div class="modal-body tool-bar">
          <div class="col-md-12">
            <div class="row tool-frame last-date active">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Chọn Trung Tâm</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="branches"
                            label="name"
                            :searchable="true"
                            v-model="branch"
                            @change="selectBranch"
                            :disabled="false"
                            :onChange="selectBranch"
                            > 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Chọn Lớp Học</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="classes"
                            label="cls_name"
                            :searchable="true"
                            v-model="selectedclass"
                            @change="selectClass"
                            :disabled="false"
                            :onChange="selectClass"> 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Các Ngày Học Trong Tuần</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="class_date_list"
                            :class="'special'"
                            label="name"
                            multiple
                            v-model="classdates"
                            @change="selectClassdays"
                            :disabled="false"
                            :onChange="selectClassdays"> 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Chọn Sản Phẩm</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="products"
                            label="name"
                            :searchable="true"
                            v-model="product"
                            @change="selectProduct"
                            :disabled="false"
                            :onChange="selectProduct"> 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Ngày Bắt Đầu Học</label>
                        </div>
                        <div class="col-md-8">
                          <datepicker
                            v-model="start_date"
                            :readonly="false"
                            :lang="'en'"
                            :bootstrapStyling=true
                            :full=false
                            format="YYYY-MM-DD"
                            placeholder="Chọn ngày bắt đầu học"
                            input-class="form-control bg-white"
                            class="time-picker"
                            @change="calculateDoneSessions"
                          ></datepicker>
                        </div>
                      </div>
                    </div>
                    <div class="form-group  tool-input last-date">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="filter-label control-label">Nhập ngày học cuối</label>
                        </div>
                        <div class="col-md-8">
                          <datepicker
                            v-model="end_date"
                            :readonly="false"
                            :lang="'en'"
                            :bootstrapStyling=true
                            :full=false
                            placeholder="Chọn ngày kết thúc"
                            input-class="form-control bg-white"
                            class="time-picker"
                            @change="calculateDoneSessions"
                          ></datepicker>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group ">
                      <ul class="ada-scrolling-list" id="holidays">
                        <li class="row">
                          <span class="item-content col-md-12">Các ngày nghỉ lễ (Từ ngày -> Đến ngày)</span>
                        </li>
                        <li class="row" v-for="(holiday, index) in holidays" :key="index">
                          <span class="item-content col-md-12"><i class="fa fa-check"></i><input class="input-full-lenght" type="text" :value="`${holiday.start_date} - ${holiday.end_date} (${ holiday.name })`" readonly /></span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-4">                    
                    <div class="form-group ">
                      <ul class="ada-scrolling-list" id="learning-days">
                        <li class="row">
                          <span class="item-content col-md-12" v-show="total_session > 0"><i class="fa fa-star"></i> Tổng số buổi học: {{ total_session }}</span>
                        </li>
                        <li class="row" v-for="(learningdate, index) in learningdates" :key="index">
                          <span class="item-content col-md-12"><i class="fa fa-bookmark"></i><input class="input-full-lenght" type="text" :value="` Buổi thứ ${(index + 1)} kế tiếp: ${learningdate}`" readonly /></span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="modal-footer">
          <button type="button" @click="calculateDoneSessions" class="ada-btn success"><i class="fa fa-calendar-check-o"></i> Tính số buổi học</button>
          <button type="button" @click="$modal.hide('tool-bar2')" class="ada-btn danger">Close</button>
        </footer>
      </div>
    </modal>
</template>

<script>
import u from '../utilities/utility'
import a from '../utilities/authentication'
import abt from '../views/components/Button'
import profile from '../views/components/Profile'
import selection from 'vue-select'
import moment from 'moment'
import cookies from 'vue-cookies'
import datepicker from 'vue2-datepicker'
	export default {
	  name: 'ModalSession',
	  components: {
    abt,
    profile,
    selection,
    datepicker,
  },
  data() {
    return {
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
      classdays: [2],
      useholidays: 1,
      class_date_list: [
        {id: 0, name: '0 - Sunday'},
        {id: 1, name: '1 - Monday'},
        {id: 2, name: '2 - Tuesday'},
        {id: 3, name: '3 - Wednesday'},
        {id: 4, name: '4 - Thursday'},
        {id: 5, name: '5 - Friday'},
        {id: 6, name: '6 - Saturday'}],
      classdates: [{id: 2, name: '2 - Tuesday'}],
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
      usingGuideUrl: ''
    }
  },
  created(){
    const session = u.session()
    this.checkUserRole(session)
    this.user = session.user
    this.user_id = this.user.id
    this.user_name = this.user.name
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
      // if (val.length < 2 || val.length > 4) {
      //   valid = false
      //   markup = 'danger'
      //   message = 'Số lượng buổi học thường là 2 buổi trên 1 tuần.'
      // } else if (val.length !== 2) {
      //   markup = 'caution'
      //   message = 'Lưu ý, mỗi tuần thường chỉ có 2 buổi học.'
      // }
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
    calculateDoneSessions() {
      if (this.start_date && this.end_date && this.classdays.length && this.holidates.length) {
        const start_date = this.moment(this.start_date).format('YYYY-MM-DD')
        const end_date = this.moment(this.end_date).format('YYYY-MM-DD')
        const info = u.calSessions(start_date, end_date, this.holidates, this.classdays)
        this.total_session = info.total
        this.learningdates = info.dates
        this.last_date_result = ''
      }
    },
    beforeClose() {
      
    },
    onDrawStartDate(e) {
      let date = e.date
      // if (this.current_start_date > date.getTime()) {
      //   e.allowSelect = false
      // }
    },
    
    selectStartDate(start_date) {
      u.log('START DATE', start_date)
    },
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
  }
}
</script>