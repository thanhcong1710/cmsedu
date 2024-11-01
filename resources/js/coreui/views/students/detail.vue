<template>
  <div class="animated fadeIn apax-form apax-show-detail">
    <div class="row">
      <div class="col-12">
        <b-card header-tag="header">
          <div slot="header">
            <i class="fa fa-drivers-license"></i> <b class="uppercase">Thông tin hồ sơ học sinh - {{student.name}}</b>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">
                <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
            </div>
					</div>
          <div class="panel main-content">
            <div class="avatar-div">
              <div class="row">
                <div class="col-4">
                  <div>
                    <img :src="`${student.avatar}`" class="avatar">
                  </div>
                </div>
                <div class="col-8">
                  <div class="row">
                    <div class="col-12">
                      <div class="avata-info" id="avata-info" style="margin-top:20px">
                        <strong>Tên: {{student.name}}</strong>
                        <p>Lớp: {{student.class_name}}</p>
                      </div>
                    </div>
                  </div>
                  <div class="row" v-show="student.attached_file && student.attached_file.length">
                    <div class="col-12">
                      <a :href="student.attached_file" >Download File đính kèm</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="page-content">
              <div class="row">
                <div class="col-md-12">
                  <div class="tab-base">
                    <ul class="nav nav-tabs" role="tablist">
                      <li :class="{forActive: html.dom.tab.current === index}" :key="index" v-for="(tab, index) in html.dom.tab.list" class="nav-item">
                        <a class="nav-link" data-toggle="tab" :href="`#tab-${index}`" role="tab" :aria-controls="`tab-${index}`" aria-expanded="true" aria-selected="true" @click="html.dom.tab.current = index">{{tab.text}}</a>
                      </li>
                    </ul>
                    <div class="tab-content" id="page-control">
                      <div :class="html.dom.tab.current === 0 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-0" role="tabpanel">
                        <tabinfo :data="student" />
                      </div>
                      <div :class="html.dom.tab.current === 1 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-1" role="tabpanel">
                        <tabupdate :data="student" :list="tabs.update" />
                      </div>
                      <div :class="html.dom.tab.current === 2 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-2" role="tabpanel">
                        <tabcontract :list="tabs.contract" />
                      </div>
                      <div :class="html.dom.tab.current === 3 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-3" role="tabpanel">
                        <tabenrolment :student="student" :reports="student.trial_reports" :list="tabs.enrolment" />
                      </div>
                      <div :class="html.dom.tab.current === 4 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-4" role="tabpanel">
                        <tabpayment :list="tabs.payment" />
                      </div>
                      <div :class="html.dom.tab.current === 5 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-5" role="tabpanel">
                        <tabtuition :list="tabs.tuition_transfer" />
                      </div>
                      <div :class="html.dom.tab.current === 6 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-6" role="tabpanel">
                        <tabclass :list="tabs.class_transfer" />
                      </div>
                      <div :class="html.dom.tab.current === 9 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-9" role="tabpanel">
                        <tabbranchtransfer :list="tabs.branch_transfer" />
                      </div>
                      <div :class="html.dom.tab.current === 7 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-7" role="tabpanel">
                        <tabreserve :list="tabs.recerve" />
                      </div>
                      <div :class="html.dom.tab.current === 8 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-8" role="tabpanel">
                        <tabwithdraw :list="tabs.withdrawal_fees" />
                      </div>
                      <div :class="html.dom.tab.current === 10 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-10" role="tabpanel">
                        <tabcares :list="tabs.cares" />
                      </div>
                      <div :class="html.dom.tab.current === 11 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-11" role="tabpanel">
                        <taball :list="tabs.all" />
                      </div>
                      <div :class="html.dom.tab.current === 12 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-12" role="tabpanel">
                        <tabattendances :list="tabs.attendances" />
                      </div>
                      <div :class="html.dom.tab.current === 13 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-13" role="tabpanel">
                        <tabupload :list="tabs.upload" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row button-group outdown">
                  <button class="btn btn-sm btn-danger come-back button-back" @click="back()">Quay lại</button>
                  <router-link v-if="hasPermissionUpsert" class="btn btn-sm btn-success come-back" v-bind:to="{name: 'Cập Nhật Thông Tin Học Sinh', params: {id: student.id}}"><i class="fa fa-pencil"></i> Sửa thông tin</router-link>
                  <router-link v-if="hasPermissionChangeDate" style="margin-left: 5px;" class="btn btn-sm btn-warning come-back" v-bind:to="changeBackDate(student.id)"><i class="fa fa-pencil"></i> Sửa ngày xếp lớp</router-link>
                </div>
              </div>
            </div>
          </div>
          <!-- <b-modal size="lg" title="NHẬN XÉT CỦA GIÁO VIÊN" id="commentTrialModel" hide-header class="add-branchs" @ok="teacherComment" v-model="commentTrialModel">
            <h5 class="title-modal-fix">Trial Report</h5>
            <b-container fluid >
              <b-row class="mb-1">
                <b-col cols="12">
                  <b-row>
                    <div class="table-responsive scrollable">
                        <div class="table-responsive scrollable">
                          <table class="table table-striped table-bordered apax-table">
                            <thead>
                              <tr class="text-sm">
                                <th>Buổi</th>
                                <th>Người nhận xét</th>
                                <th>Nội dung</th>
                                <th>File đính kèm</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td>{{student.created_at}}</td>
                                <td>Người tạo học sinh</td>
                                <td>
                                  <button class="btn btn-success"><i class="fa fa-file-word-o"></i> Trích xuất</button>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                  </b-row>
                </b-col>
              </b-row>
            </b-container>
            <div slot="modal-footer" class="w-100">
              <b-btn size="sm"
                      class="float-right"
                      variant="primary"
                      @click="cancelTrialComment()">
                Hủy
              </b-btn>
            </div>
          </b-modal>
          <b-modal size="lg" title="NHẬN XÉT CỦA GIÁO VIÊN" id="commentModel" hide-header class="add-branchs" @ok="teacherComment" v-model="commentModel">
            <h5 class="title-modal-fix">Nhận xét của giáo viên</h5>
            <b-container fluid >
              <b-row class="mb-1">
                <b-col cols="12">
                  <b-row>
                    <div class="table-responsive scrollable">
                      <table class="table table-striped table-bordered apax-table">
                        <thead>
                          <tr class="text-sm">
                            <th>STT</th>
                            <th width="150">Người nhận xét</th>
                            <th>Nội dung</th>
                            <th width="100">File đính kèm</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>{{student.created_at}}</td>
                            <td>Người tạo học sinh</td>
                            <td>
                              <button class="btn btn-success"><i class="fa fa-file-word-o"></i></button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </b-row>
                </b-col>
              </b-row>
            </b-container>
            <div slot="modal-footer" class="w-100">
              <b-btn size="sm"
                      class="float-right"
                      variant="primary"
                      @click="cancelComment()">
                Hủy
              </b-btn>
            </div>
          </b-modal> -->
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import u from '../../utilities/utility'
import tabinfo from './tab-info'
import tabrank from './tab-rank'
import tabupdate from './tab-update'
import tabcontract from './tab-contract'
import tabenrolment from './tab-enrolment'
import tabpayment from './tab-payment'
import tabtuition from './tab-tuition-transfer'
import tabclass from './tab-class-transfer'
import tabreserve from './tab-reserve'
import tabbranchtransfer from './tab-branch-transfer'
import tabwithdraw from './tab-withdrawal-fees'
import tabcares from './tab-cares'
import taball from './tab-all'
import tabattendances from './tab-attendances'
import tabupload from './tab-upload'
// import tabsemester from './tab-semester'

export default {
  components: {
    tabinfo,
    tabrank,
    tabupdate,
    tabcontract,
    tabenrolment,
    tabpayment,
    tabtuition,
    tabclass,
    tabreserve,
    // tabsemester,
    // tabpending,
    tabwithdraw,
    tabbranchtransfer,
    tabcares,
    taball,
    tabattendances,
    tabupload
  },
  data() {
    const model = u.m('students').page
      model.html.dom = {
        modal: {
          display: false,
          title: 'Thông Báo',
          class: 'modal-success',
          message: '',
          done: () => this.callback()
        },
        button: {
          cancel: {
            label: 'Thoát',
            icon: 'fa-sign-out',
            title: 'Thoát form thêm học sinh',
            markup: 'warning',
            disabled: false,
            action: () => this.exitForm()
          }
        },
        tab: {
          list: [
            {
              name: 'tab-1',
              text: 'Thông tin HS'
            },
            {
              name: 'tab-2',
              text: 'LS cập nhật'
            },
            {
              name: 'tab-3',
              text: 'LS Nhập học'
            },
            {
              name: 'tab-4',
              text: 'LS đăng ký lớp học'
            },
            {
              name: 'tab-5',
              text: 'LS thu phí'
            },
            {
              name: 'tab-6',
              text: 'LS chuyển phí'
            },
            {
              name: 'tab-7',
              text: 'LS chuyển lớp'
            },
            {
              name: 'tab-8',
              text: 'LS bảo lưu'
            },
            {
              name: 'tab-9',
              text: 'LS rút phí'
            },
            {
              name: 'tab-10',
              text: 'LS chuyển TT'
            },
            {
              name: 'tab-11',
              text: 'LS Chăm sóc'
            },
            {
              name: 'tab-12',
              text: 'Đang chờ duyệt'
            },
            {
              name: 'tab-12',
              text: 'LS điểm danh'
            },
            {
              name: 'tab-13',
              text: 'Hồ sơ lưu trữ'
            },
          ],
          current: 0
        }
      }
      model.student = {}
      model.tabs = {
        contract: [],
        enrolment: [],
        // pending: [],
        reserve: [],
        update: [],
        charge: [],
        rank: [],
        class_transfer: [],
        tuition_transfer: [],
        semester_transfer: [],
        withdrawal_fees:[],
        attendances:[],
        cares:[],
        all:[],
        upload:[],
      }
      return model
  },

  computed: {
    hasPermissionUpsert(){
      return [84].indexOf(parseInt(this.session.user.role_id)) === -1
    },
    hasPermissionChangeDate(){
      return 0
      // if (u.r.super_administrator == this.session.user.role_id)
      //   return 1
      // else
      //   return 0
    },
  },

  created() {
    u.g(`${this.html.page.url.apis}${this.$route.params.id}`).then((response) => {
      this.student = response.student
      this.tabs = response.tabs
      this.html.loading.action = false
    })
  },
  methods: {
    back () {
      this.$router.go(-1)
    },
    changeBackDate(value){
        return `/supports/class_day/${value}`
    },
  }
}
</script>

<style scoped>

.tab-content div {

}

.row .attached-file {
  padding: 0;
  height: 30px;
  display: block;
  width: 100%;
  line-height: 33px;
}
.forActive {
  background: white;
}
/*.active {
  background: white;
}*/

.come-back {
  /*margin-left: 10px;*/
  margin-bottom: 10px;
  margin-top: 10px;
}

#page-content {
  margin: auto;
}

.card-body {
  padding: 5px;
}

.tab-base {
  background: #e4e5e6;
}

#page-control .title {
  /*background: skyblue;*/
  height: 36px;
  line-height: 36px;
  text-align: left;
  text-transform: uppercase;
  border-bottom: 1px solid #c7c7c7;
  padding:0 0 0 10px;
  margin: 0 0 30px 0;
}

.avatar-div {
  padding-bottom: 10px;
  margin: 0 0 5px 0;
}

#avatar-info {
  margin-top: 20px;
  color: red;
}
.special.apax-table tr td {
  padding: 0 10px!important;
  margin: 0!important;
  vertical-align: middle!important;
}
.special.apax-table tr td input {
  width: 100%;
  height: 100%;
  outline: none;
  border: none;
}
.avatar {
  max-width: 120px;
  max-height: 230px;
  margin-left: 10px;
  margin-top: 10px;
  width: auto!important;
  height: auto;
}

.avatar-info {
  margin-top: 20px;
  left: 200px;
}
.tab-base .nav-tabs {
  background: #FFFFFF;
}
.nav-tabs {
  padding: 0;
  border-bottom: 1px solid #b93232!important;
}
.nav-tabs .nav-item {
  background: #f5d4d4;
  color: #b93232;
  text-shadow: 0 -1px 0 #fff;
  border-left: 1px solid #b93232;
  border-top: 1px solid #b93232;
  border-bottom: 1px solid #b93232;
}
.nav-tabs .nav-item a.nav-link {
  color: #b93232;
  font-weight: 500;
}
.nav-tabs .nav-item a.nav-link:hover {
  color: #FFF;
  background: #c90a0a;
  text-shadow: 0 1px 1px rgb(46, 8, 8);
}
.nav-tabs .nav-item:last-child {
  border-right: 1px solid #b93232;
}
.nav-tabs .nav-item.forActive, .nav-tabs .nav-item.forActive:hover {
  outline: none;
  background: #FFF;
  text-shadow: 0 1px 1px #333;
  border-left: 1px solid #b93232;
  border-bottom: 1px solid #FFF;
}
.nav-tabs .nav-item.forActive a.nav-link, .nav-tabs .nav-item.forActive a.nav-link:hover {
  color: #8e1414;
  background: #FFF;
  font-weight: 600;
  text-shadow: 0 1px 1px #c8c7c7;
  outline: none;
  border: none;
}
.tab-base .tab-content {
  padding: 30px 0 0 0;
  margin: 0!important;
  border: none!important;
}
.panel-control.title {
  color: #FFF;
  font-size: 0.9em;
  text-transform: capitalize;
  background: #223b54;
  letter-spacing: 0.6px;
  font-weight: 600;
  text-shadow: 1px 1px 1px #111;
}
.button-group{
  margin: 0 0 10px 30px;
}
.button-back{
  margin-right: 5px;
}
.time-picker{
  display: block;
  width: 250px;
}
.mx-input-icon__calendar{
  background-image: none;
}

</style>
