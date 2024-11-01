<template>
	<modal name="tool-bar3" @before-close="beforeClose" width="90%" height="96%" :draggable="true" :adaptive="true" :scrollable="true" :resizable="true" classes="tools-bar">
      <div class="tool-bar-frame modal-primary ada-modal">
        <header class="modal-header">
          <h5 class="modal-title">Công cụ hỗ trợ - Tính Chuyển Phí</h5>
          <button @click="$modal.hide('tool-bar3')" class="close">×</button>
        </header>
        <div class="modal-body tool-bar">
          <div class="col-md-12">
            <div class="row tool-frame tuition-transfer active">
              <div class="col-md-12">
                <div class="content apax-form">
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Chọn Gói Phí Chuyển</label>
                                    <selection
                                      :options="tuitions"
                                      label="name"
                                      :searchable="true"
                                      v-model="tuition"
                                      @change="selectTuition"
                                      :disabled="false"
                                      :onChange="selectTuition"
                                      > 
                                    </selection>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Chọn Trung Tâm Nhận</label>
                                    <selection
                                      :options="branches"
                                      label="name"
                                      :searchable="true"
                                      v-model="branchTransfer"
                                      @change="selectBranchTransfer"
                                      :disabled="false"
                                      :onChange="selectBranchTransfer"
                                      > 
                                    </selection>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Số Tiền Chuyển</label>
                                    <input class="form-control" type="text" v-model="tfamnt" @change="calculateTransfer"/>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="control-label">Số Buổi Chuyển</label>
                                    <input class="form-control" type="number" v-model="tfsess" @change="calculateTransfer"/>
                                </div>-->
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Chọn Sản Phẩm Nhận</label>
                                    <selection
                                      :options="products"
                                      label="name"
                                      :searchable="true"
                                      v-model="productTransfer"
                                      @change="selectProductTransfer"
                                      :disabled="false"
                                      :onChange="selectProductTransfer"> 
                                    </selection>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Thông Tin Gói Phí Chuyển</label>
                                    <div class="detail-info">
                                        Tên: {{ tftfif.name }}<br/>
                                        ID Gói Phí: {{ tftfif.id }}<br/>
                                        Product: {{ tftfif.product_name }}<br/>
                                        Số Buổi: {{ tftfif.session }}<br/>
                                        Giá Gốc: {{ tftfif.price | formatMoney }}<br/>
                                        Triết Khấu: {{ tftfif.discount | formatMoney }}<br/>
                                        Thực Thu: {{ (tftfif.price - tftfif.discount) | formatMoney }}<br/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Thông Tin Gói Phí Nhận</label>
                                    <div class="detail-info">
                                        Tên: {{ rctfif.name }}<br/>
                                        ID Gói Phí: {{ rctfif.id }}<br/>
                                        Product: {{ rctfif.product_name }}<br/>
                                        Số Buổi: {{ rctfif.session }}<br/>
                                        Giá Gốc: {{ rctfif.price | formatMoney }}<br/>
                                        Triết Khấu: {{ rctfif.discount | formatMoney }}<br/>
                                        Thực Thu: {{ (rctfif.price - rctfif.discount) | formatMoney }}<br/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Tiền Đã Chuyển</label>
                                    <div class="detail-info">
                                        {{ tfamnt | formatMoney }}
                                    </div>
                                    <label class="control-label">Đơn Giá Gói Phí Nhận</label>
                                    <div class="detail-info">
                                        {{ sptfif | formatMoney }}
                                    </div>
                                    <label class="control-label">Kết Quả Tính Toán</label>
                                    <div v-if="specia === 0" class="detail-info">
                                        {{ tfamnt | formatMoney }} / {{ sptfif | formatMoney }} = {{
                                        Math.round(tfamnt/sptfif, 2) }}
                                    </div>
                                    <div v-if="specia === 1" class="detail-info">
                                        Chuyển ngang buổi từ HN - HCM gói April: {{ sstfif }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Buổi Chuyển Được</label>
                                    <div class="detail-info">
                                        {{ sstfif }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="modal-footer">
          <button type="button" @click="calculateTransfer" class="ada-btn error"><i class="fa fa-retweet"></i> Tính chuyển phí</button>
          <button type="button" @click="$modal.hide('tool-bar3')" class="ada-btn danger">Close</button>
        </footer>
      </div>
    </modal>
</template>

<script>
import u from '../utilities/utility'
import a from '../utilities/authentication'
import selection from 'vue-select'
import cookies from 'vue-cookies'
import moment from 'moment'
export default {
	  name: 'ModalTuition',
	  components: {
    selection,
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
  
    calculateTransfer() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.tuition-transfer').addClass('active')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.error').addClass('active')
      this.calcTransfer()
    },
    beforeClose() {
      
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
    prepare() {
      const z = u.calcDoneSessions(this.begin, this.end, this.holidays, this.classdays)
      this.dates2 = z.dates
      this.total = z.total
    },
    doSelect(val) {
      u.log('Select', val)
    },
  }
}
</script>