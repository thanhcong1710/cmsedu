<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter"></i> <b class="uppercase">Chuyển Đổi Người Quản Lý</b>
          </div>
          <div id="filter_content">
            <div class="row margin-auto">
              <div class="col-lg-7">
                <div class="row">
                  <div class="col-12">
                    <b-card header class="card-table-header">
                      <div slot="header">
                         <b class="uppercase text-center">Thông Tin quản lý Cũ</b>
                      </div>
                      <div class="row">
                        <div class="col-7">
                          <div class="row row-mid">
                            <div class="col-lg-3 cm-transfer-st branch_name">Trung tâm</div>
                            <div class="col-lg-9" v-if="role_branch == true">
                              <vue-select
                                  label="name"
                                  :options="branches"
                                  v-model="branch"
                                  :searchable="true"
                                  :onChange="selectManager"
                                  language="zh-CN"
                                  class="branch_input"
                              ></vue-select>
                            </div>
                            <div class="col-9" v-else>
                              <input type="text" class="form-control" v-model="selectedBranche_name" readonly>
                            </div>
                          </div>
                          <div class="row row-mid">
                            <div class="col-lg-3 cm-transfer-st">EC</div>
                            <div class="col-lg-9">
                              <vue-select
                                  label="ec_name"
                                  :options="ecs"
                                  v-model="xec"
                                  :onChange="selectNewEc"
                                  :searchable="true"
                                  language="zh-CN"
                                  class="branch_input"
                                  placeholder="Chọn EC..."
                              ></vue-select>
                            </div>
                          </div>
                          <!--<div class="row">
                            <div class="col-lg-3 cm-transfer-st">CM</div>
                            <div class="col-lg-9">
                              <vue-select
                                  label="cm_name"
                                  :options="cms"
                                  v-model="xcm"
                                  :onChange="selectNewCm"
                                  :searchable="true"
                                  language="zh-CN"
                                  class="branch_input"
                                  placeholder="Chọn CM..."
                              ></vue-select>
                            </div>
                          </div>-->
                        </div>
                        <div class="col-5">
                          <div class="row mb-2">
                            <div class="col-lg-4 cm-transfer-st">Mã CMS</div>
                            <div class="col-lg-8">
                              <input type="text" class="form-control" v-model="lms_id" placeholder="Nhập mã CMS...">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-4 cm-transfer-st">Mã Cyber</div>
                            <div class="col-lg-8">
                              <input type="text" class="form-control" v-model="accounting_id" placeholder="Nhập mã Cyber...">
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-card>
                  </div>
                </div>
              </div>
              <div class="col-lg-5">
                <b-card header class="card-table-header">
                  <div slot="header">
                     <b class="uppercase text-center">Thông Tin quản lý Mới</b>
                  </div>
                  <div class="row row-mid">
                    <div class="col-lg-3 cm-transfer-st">Trung tâm</div>
                    <div class="col-lg-9">
                      <input type="text" class="form-control" v-model="branch.name" readonly>
                    </div>
                  </div>
                  <div class="row row-mid">
                    <div class="col-lg-3 cm-transfer-st">EC</div>
                    <div class="col-lg-9">
                      <vue-select
                          label="ec_name"
                          placeholder="Chọn EC..."
                          :options="new_ecs"
                          v-model="new_ec"
                          :searchable="true"
                          language="zh-CN"
                      ></vue-select>
                    </div>
                  </div>
                  <!--<div class="row">
                    <div class="col-lg-3 cm-transfer-st">CM</div>
                    <div class="col-lg-9">
                      <vue-select
                          label="cm_name"
                          placeholder="Chọn CM..."
                          :options="new_cms"
                          v-model="new_cm"
                          :searchable="true"
                          language="zh-CN"
                      ></vue-select>
                    </div>-->
                  <!--</div>-->
                  <div class="clear-bottom"></div>
                  <div class="col-sm-12 col-sm-offset-3 text-center">
                    <button v-show="onlyView()" class="apax-btn full edit" :disabled="disabledFilter" @click.prevent="filterStudentByClass">Lọc số liệu</button>
                    <button class="apax-btn full default" @click="resetInfo">Bỏ lọc</button>
                  </div>
                </b-card>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <!-- <div :class="ajax_loading ? 'loading' : 'standby'" class="ajax-loader">
          <img src="/static/img/images/loading/mnl.gif">
        </div> -->
        <b-card header>
          <div slot="header">
            <i class="fa fa-list"></i> <b class="uppercase">Danh Sách</b>
            <span class="pull-right"><router-link to="/cm-transfer-history">Lịch sử chuyển đổi quản lý</router-link></span>

          </div>
          <div class="scroll-tb">
            <table class="table table-bordered apax-table" id="table_list">
              <thead>
                <tr>
                  <th style="width:3%">
                    <b-form-checkbox class="check-item" id="select-all" v-model="selectAll" ></b-form-checkbox>
                    <!-- <input id="check-all" @click="checkAll()" name="CheckAll" type="checkbox" /> -->
                  </th>
                  <th>STT</th>
                  <th>Mã CMS</th>
                  <th> Mã Cyber</th>
                  <th>Tên học sinh</th>
                  <!-- <th>Status</th> -->
                  <th> EC</th>
                  <th>CM</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(student, i) in students" :key="i">
                  <td>
                    <b-form-checkbox class="check-item" v-model="temp" @change.native="toggleSelectRow(student.student_id)" :value="student.student_id" number></b-form-checkbox>
                    <!-- <input type="checkbox" @change="toggleSelectRow(student)" :id="'cm_' + i"/> -->
                  </td>
                  <td>{{ i+1 }}</td>
                  <td>{{ student.crm_id }}</td>
                  <td>{{ student.accounting_id }}</td>
                  <td>{{ student.student_name }}</td>
                  <!-- <td>{{ student.status | studentStatus }}</td> -->
                  <td>{{ student.ec_name }}</td>
                  <td>{{ student.cm_name }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="text-center">
            <button class="btn btn-info" @click.prevent="transferCM">Chuyển đổi</button>
            <button @click="resetAllInfor" class="btn btn-primary">Hủy bỏ</button>
          </div>
        </b-card>
        <b-modal title="THÔNG BÁO" class="modal-success" size="sm" v-model="modal" @ok="closeModal" ok-variant="success">
          <div v-html="message">
          </div>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
import u from '../../../utilities/utility'
import axios from 'axios'
import select from 'vue-select'
import moment from 'moment'
export default {
  name: 'cm_transfer',
  components: {
    "vue-select": select

  },

  data () {
    return {
      message: '',
      modal: false,
      branches: [],
      branch: '',
      selected_ec_id: '',
      selected_cm_id: '',
      ecs: [],
      xec: null,
      cms: [],
      xcm: null,
      new_ecs: [],
      new_ec: null,
      new_cms: [],
      new_cm: null,
      lms_id: '',
      accounting_id: '',
      students: [],
      selectedStudents: [],
      selectedBranche_name: '',
      checked_list: [],
      temp: [],
      job_title: '',
      role_branch: '',
      name: ''
    }
  },
  computed: {
    disabledFilter: () =>{
      if(this.selected_ec_id != '' && this.selected_cm_id != ''){
        return false
      }
      return true
    },
    selectAll: {
      get: function () {
        return parseInt(this.checked_list.length) === parseInt(this.students.length)
      },
      set: function (value) {
        const selected_list = []
        if (value) {
          console.log(value);
          this.students.forEach((student) => {
            selected_list.push(student.student_id)

          })
        }
        this.checked_list = selected_list
        this.temp = selected_list
        // console.log('temp', this.temp);
      }
    }
  },
  created() {
    u.a().get(`/api/reports/branches`).then(response => {
      this.branches = response.data
      // console.log('this branches', this.branches);
      this.checkRole()
    })
  },
  methods: {
    onlyView(){
      return u.onlyView(u.session().user.role_id)
    },
    checkRole(){
      u.a().get(`/api/reports/check-role`).then(response => {
        const rs = response.data
        if(rs === 1){
          this.role_branch = true
          // this.disabledSelectBranch = false
          // this.selectManager(branch)
        }else {
          this.role_branch = false
          // this.disabledSelectBranch = true
          this.selectedBranche_name = this.branches[0].name;
          this.branch = this.branches[0]
          const branch = this.branch
          this.selectManager(branch)
          // if(selectedBranch_id){
          //   this.selectedBranches.push(selectedBranch_id)
          // }
        }
      })
    },
    selectManager(data){
      // console.log('test it ===', data);
      const branchId = data.id
      this.branch = data
      if(branchId && branchId != ''){
        u.a().get(`/api/users/${branchId}/get-ec-cm-list`).then(response => {
          console.log('select ecnew', response.data);
          this.ecs = response.data.ecs
          this.cms = response.data.cms
          // this.new_ecs = response.data.ecs
          // this.new_cms = response.data.cms
          // console.log('this ec and cm', this.ecs, this.cms);
        })
      }
    },
    selectedNewManagers(){
      const branch = this.branch.id

      u.a().get(`/api/users/${branch}/get-new-ec-cm-list`).then(response => {
          // console.log('ths result is ', response.data);
          // this.new_ecs = response.data
          // console.log('this ec and cm', response.data);
          this.new_ecs = response.data.ecs
          this.new_cms = response.data.cms
      })
    },
    selectNewManagerByStudent(data){
      // console.log('test the result', data);
      // // const
      // u.a().post(`/api/users/get-new-ec-list`, data).then(response => {
      //     // console.log('ths result is ', response.data);
      //     this.new_ecs = response.data
      //     // console.log('this ec and cm', response.data);
      // })
      // const ec = data
      // const cm = data
      // this.selectNewEc(ec)
      // this.selectNewCm(cm)
    },
    selectNewEc(data){
      this.xec = data;
      const ecId = data && data.ec_id ? data.ec_id : ''
      const branchId = this.branch.id
      if(ecId && ecId != ''){
        this.selected_ec_id = ecId
        // console.log(this.ec);
        u.a().get(`/api/users/${branchId}/${ecId}/get-new-ec-list`).then(response => {
          // console.log('ths result is ', response.data);
          this.new_ecs = response.data
          // console.log('this ec and cm', response.data);
        })
      }
    },
    selectNewCm(data){
      this.xcm = data;
      const cmId = data && data.cm_id ? data.cm_id : ''
      const branchId = this.branch.id
      if(cmId && cmId != ''){
        this.selected_cm_id = cmId
        u.a().get(`/api/users/${branchId}/${cmId}/get-new-cm-list`).then(response => {
          // console.log(response.data.cms);
          this.new_cms = response.data
          this.xcm = data
        })
      }


    },
    filterStudentByClass(){
      const data = {
        branch_id: this.branch.id,
        lms_id: this.lms_id,
        accounting_id: this.accounting_id,
        ec_id: this.selected_ec_id,
        cm_id: this.selected_cm_id,
        lms_id: this.lms_id,
        accounting_id: this.accounting_id
      }

      // console.log(data);
      if(data.lms_id || data.accounting_id || data.ec_id || data.cm_id){
        u.a().post(`/api/users/get-student-list-by-user`, data).then(response => {
          this.students = response.data
        })
        // this.selectNewEc(data)
        // this.selectNewCm(data)
        // console.log('test', data);
        // this.selectedNewManagers()
        this.selectNewManagerByStudent(data)

      }else {

        alert('Vui lòng chọn EC.')

      }
    },
    transferCM(){
      // console.log(this.temp);
      const data = {
        ec_id: this.selected_ec_id ? this.selected_ec_id : '',
        cm_id: this.selected_cm_id ? this.selected_cm_id : '',
        new_ec_id: this.new_ec && this.new_ec.ec_id ? this.new_ec.ec_id : '',
        new_cm_id: this.new_cm && this.new_cm.cm_id ? this.new_cm.cm_id : '',
        branch_id: this.branch.id,
        student_ids: this.temp,
        lms_id: this.lms_id,
        accounting_id: this.accounting_id
      }
      // console.log(data);
      if(data.student_ids == ''){
        alert('Vui lòng chọn học sinh')
        return false
      }else if (!data.new_ec_id && !data.new_cm_id){
        alert('Vui lòng chọn người nhận')
        return false
      } else {
        var conf = confirm('Bạn có chắc chắn thực hiện thao tác này?');
        if(conf) {
          u.a().post(`/api/users/transfer-manager`, data).then(response => {
            this.modal = true
            this.message = "Thêm chuyển đổi quản lý thành công!"
            this.filterStudentByClass();
          }).catch(e => {
            this.modal = true
            this.message = "Có lỗi xảy ra.Vui lòng thử lại!"
          })
        }
      }
    },
    resetInfo(){
      if(this.role_branch === true){
        this.branch = ''
      }
      this.selected_ec_id = ''
      this.selected_cm_id = ''
      this.xec = null
      this.xcm = null
      this.new_ec = null
      this.new_cm = null
      this.lms_id = ''
      this.accounting_id = ''

    },
    resetAllInfor(){
      if(this.role_branch === true){
        this.branch = ''
      }
      this.selected_ec_id = ''
      this.selected_cm_id = ''
      this.xec = null
      this.xcm = null
      this.new_ec = null
      this.new_cm = null
      this.lms_id = ''
      this.accounting_id = ''
      this.temp = ''
      this.students = ''
      this.checked_list = ''

    },
    toggleSelectRow(student) {
      // console.log(this.temp);
        // const student_id = student
        // if(this.temp && this.temp.indexOf(student_id) == '-1'){
        //   this.temp.push(student_id)
        // } else {
        //   this.temp.pop(student_id)

        // };
    },
    resetStudentInfo(){
      // alert('Hủy bỏ thành công')
      if(this.temp){
         this.temp = [];
      }
      // console.log(this.temp);
    },
    closeModal() {
      if (this.completed) {
        this.message = "Chuyển quản lý thành công !";
        this.exitForm()
      } else this.modal = false
    },
  }
}
</script>
<style>
  .cm-transfer-st{
    font-size: 12px;
    margin-top: 8px;
  }
  .width-60{
    width: 60%
  }
  .margin-auto{
    margin: 0 auto;
  }
  .row-mid{
    margin-bottom: 7px;
  }
  .clear-bottom{
    /*margin-bottom: 5px;*/
  }
  .card-table-header .card-header {

    height: 35px;
    line-height: 10px;
  }
  .branch_name{
    width: 50px;
  }
  .selected-tag{
    /*width: 10px;*/
  }
  .branch_input{
    /*width: 230px;*/
  }
  .searchable .dropdown-toggle input{
    width: 60px !important;
  }
</style>
