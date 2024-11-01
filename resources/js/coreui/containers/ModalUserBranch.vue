<template>
	<modal name="tool-bar4" @before-close="beforeClose" width="90%" height="96%" :draggable="true" :adaptive="true" :scrollable="true" :resizable="true" classes="tools-bar">
      <div class="tool-bar-frame modal-primary ada-modal">
        <header class="modal-header">
          <h5 class="modal-title">Danh sách nhân viên trong trung tâm</h5>
          <button @click="$modal.hide('tool-bar4')" class="close">×</button>
        </header>
        <div class="modal-body tool-bar">
          <div class="col-md-12">
            <div class="row tool-frame user-list active">
              <div class="col-md-12">
                <div class="content apax-form">
                    <div class="col-md-12 pad-no">
                      <div class="table-responsive scrollable">
                        <table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
                          <thead>
                            <tr>
                              <th>STT</th>
                              <th>Tên Nhân Viên</th>
                              <th>Mã Nhân Viên</th>
                              <th>Tài Khoản</th>
                              <th>Chức Vụ</th>
                              <th>Phone</th>
                              <th>Email</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item, index) in user_list" v-bind:key="index">
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{index+1}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.full_name}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.hrm_id}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.username}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.title}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                <a v-b-tooltip.hover class="link-me" :title="`Gọi cho ${item.full_name}`" :href="`tel:${item.phone}`">{{item.phone}}</a>
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                <a v-b-tooltip.hover class="link-me" :title="`Email cho ${item.full_name}`" :href="`mailto:${item.email}`">{{item.email}}</a>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </modal>
</template>

<script>
import u from '../utilities/utility'
import a from '../utilities/authentication'
import moment from 'moment'
export default {
	  name: 'ModalUserBranch',
	  components: {
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
    
    beforeClose() {
      
    }
  }
}
</script>