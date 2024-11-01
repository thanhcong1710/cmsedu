<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-card header-tag="header">
                <div slot="header">
                    <i class="fa fa-reddit-alien"></i> <b class="uppercase">Công cụ Hỗ trợ</b>
                </div>
                <div class="panel">
                    <div class="row">
                        <div class="col-md-6">
                            <b-card header-tag="header">
                                <div slot="header">
                                    <i class="fa fa-leanpub"></i> <b class="uppercase">Chuyển đổi gói phí</b>
                                </div>
                                <div class="panel form-fields">
                                    <div class="row">
                                        <div class="form-group list col-md-6">
                                            <label class="control-label tight">Trung tâm:</label>
                                            <select @change="selectBranch" class="form-control tight" v-model="branch">
                                                <option value="0">
                                                    Chọn trung tâm
                                                </option>
                                                <option :value="branch.id" v-for="(branch,ind) in branches" :key="ind">
                                                    {{branch.name}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" :class="search.display">
                                        <div class="form-group list col-md-6">
                                        <label class="control-label tight">Tìm kiếm theo mã CMS, Tên học sinh:</label>
                                        <search
                                                :onSearchStudent="searchSender"
                                                :onSelectStudent="selectStudent">
                                        </search>
                                        </div>
                                    </div>
                                    <div class="row" :class="search.display">
                                        <div
                                                class="form-group list col-md-6"
                                                v-show="tuitionFee.length"
                                        >
                                            <label class="control-label tight">Chọn gói phí</label>
                                            <ul
                                                    class="form-control tight"
                                                    id="tuitions"
                                            >
                                                <li class="row the-first">
                                                    <span class="item-tuition-id col-md-3">ID</span>
                                                    <span class="item-tuition-name col-md-8">Tên Gói Phí</span>
                                                </li>
                                                <li
                                                        class="row"
                                                        :class="{'selected-tuition-fee' : check(tuition.id)}"
                                                        v-for="(tuition, index) in tuitionFee"
                                                        :key="index"
                                                        @click="selectTuition(tuition)"
                                                >
                                                    <span class="item-tuition-id col-md-3">{{ tuition.id }}</span>
                                                    <span
                                                            class="item-tuition-name col-md-8"
                                                            :class="{'text-danger' : check(tuition.id)}"
                                                    ><i class="fa fa-money" /> {{ tuition.name }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </b-card>
                        </div>
                        <div class="col-md-6 title-bold">
                            <b-card header-tag="header">
                                <div slot="header">
                                    <i class="fa fa-table" /> <b class="uppercase">Thông tin thiết lập gói phí</b>
                                </div>
                                <div class="panel options-frame">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Gói Sản Phẩm:</span>
                                                <span class="col-md-8 fline"><input
                                                        v-model="product.name"
                                                        class="form-control"
                                                        type="text"
                                                        readonly
                                                ></span>
                                            </div>
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Tên gói phí:</span>
                                                <span class="col-md-8 fline"><input
                                                        v-model="tuition.name"
                                                        class="form-control"
                                                        type="text"
                                                ></span>
                                            </div>
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Giá gốc gói phí:</span>
                                                <span class="col-md-8 fline"><input
                                                        v-model="tuition.tuition_fee_price"
                                                        class="form-control"
                                                        type="text"
                                                ></span>
                                            </div>
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Số buổi học:</span>
                                                <span class="col-md-8 fline"><input
                                                        type="number"
                                                        class="form-control"
                                                        v-model="tuition.real_sessions"
                                                ></span>
                                            </div>
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Số tiền phải đóng:</span>
                                                <span class="col-md-8 fline"><input
                                                        type="number"
                                                        class="form-control"
                                                        v-model="tuition.must_charge"
                                                ></span>
                                            </div>
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Mã giảm giá:</span>
                                                <span class="col-md-8 fline"><input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="tuition.coupon"
                                                ></span>
                                            </div>
                                            <div class="row">
                                                <span class="col-md-4 tline title-bold txt-right">Số tiền còn nợ:</span>
                                                <span class="col-md-8 fline"><input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="tuition.debt_amount"
                                                ></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <br>
                                    <abt
                                            :markup="'success'"
                                            :icon="'fa fa-save'"
                                            label="Lưu lại"
                                            title="Cập nhật thông tin Gói Phí"
                                            :on-click="saveTuitions"
                                    />
                                    <abt
                                            :markup="'warning'"
                                            :icon="'fa fa-recycle'"
                                            label="Nhập lại"
                                            title="Nhập lại gói phí mới"
                                            :on-click="saveTuitions"
                                    />
                                </div>
                            </b-card>
                        </div>
                    </div>
                </div>
            </b-card>
        </div>
        <b-modal title="Thông Báo" :class="classModal" size="lg" v-model="modal" @ok="modal=false" ok-variant="primary">
            <div v-html="message"></div>
        </b-modal>
    </div>
</template>

<script>
  import tree from 'vue-jstree'
  import abt from '../../../components/Button'
  import u from '../../../utilities/utility'
  import search from '../../../components/StudentSearchNew'

  export default {
    name: 'Tuition-Convert',
    components: {
      abt,search,
      tree
    },
    data () {
      return {
        studying:[],
        tuitionFee:[],
        branches: [],
        classModal: '',
        message: '',
        term_program_product: {
          id: '',
          program_code_id: '',
          product_id: '',
          program_id: '',
          status: ''
        },
        modal: false,
        branch: 0,
        disableSave: true,
        session: u.session(),
        msg_type:'caution dark',
        search: {
          link: 0,
          display: 'hidden',
          find: keyword => this.searchSuggestStudent(keyword),
          action: student => this.searchSuggestStudent(student)
        },
        tuition_selected   : '',
        product:{ name : '',},
        tuition            : {
          id              : 0,
          name            : '',
          branch_id       : '',
          product_id      : '',
          session         : '',
          tuition_fee_price: '',
          discount        : '',
          coupon      : '',
          available_date  : '',
          status          : '',
          must_charge         : 0,
          debt_amount    : 0,
          type            : '',
          accounting_id   : '',
          number_of_months: '',
        },
      }
    },
    filters:{
      typeToName: function(value){
        if (value==0) return 'Thường';
        else return 'Vip'
      }
    },
    created(){
      if (u.authorized()) {
        u.a().get('/api/branch/role').then(response => {
          console.log("branches === ",response)
          this.branches = response.data
        })
      }
    },
    methods: {
      saveTuitions(){
        console.log("saveTuitions")
      },
      selectTuition (tuition) {
        if (tuition){
          this.product.name = tuition.product_name
          this.tuition = tuition
        }
        console.log("selectTuition ID===",tuition)
      },
      check (id) {
        if (this.tuition_selected == id) return true
        else return false
      },
      selectStudent(student){
        console.log("student===",student)
        //this.flags.form_loading = true
        let url = '/api/tuition-convert/contracts/sender/' + student.student_id

        u.a().get(url)
          .then((response) => {
            //this.flags.form_loading = false
            if (response.data.data){
              this.tuitionFee = response.data.data
            }
            else
              this.tuitionFee = []
            //let data = (typeof response.data === 'object') ? response.data : JSON.parse(response.data)
          }).catch(e => {
          //this.flags.form_loading = false
        })
      },
      searchSender(student_name){
        let url = '/api/student-search/suggest-sender/' + (student_name?student_name:'_') + '/'+ this.branch
        return new Promise((resolve, reject) => {
          u.a().get(url)
            .then((response) => {
              let resp = response.data.data
              resp = resp.length ? resp : [{ contract_name: 'Không tìm thấy', label: 'Không có kết quả nào phù hợp' }]
              resolve(resp)
            })
        })
      },
      searchSuggestStudent(keyword) {

      },
      selectBranch(){
        u.a().get(`/api/branches/studying/${this.branch}`).then(response =>{
          if (response.data.data.total >0){
            this.search.display = 'show'
          }
          else{
            this.search.display = 'hidden'
          }
          this.tuitionFee = []
          //this.studying = response.data.data;
        })
      },
      updateProgram(){
        const confirmation = confirm("Bạn có chắc là muốn cập nhật ngày học cuối không?")
            if (confirmation){
              u.apax.$emit('apaxLoading', true)
              let formData = {id: this.branch}
              u.p('/api/tool/update-last-date/branch',formData)
                .then(response => {
                  u.apax.$emit('apaxLoading', false)
                  var currentDateWithFormat = new Date().toLocaleString()
                  this.studying.updated_last_date = currentDateWithFormat
                  this.disableSave = true
                  this.$notify({
                    group: 'apax-atc',
                    title: 'Thông báo',
                    type: 'success',
                    duration: 3000,
                    text: 'Cập nhật toàn bộ ngày học cuối thành công.'
                  })
                })
            }
        }
      },
  }
</script>

<style scoped>
    .row {
        line-height: 30px;
    }
    label.apax-title {
        margin:5px 0 3px;
        font-weight: 500;
        color:#333;
    }
    ul.apax-list.select-able li.active {
        background: #414c58;
        font-weight: 500;
        color: #FFF;
        text-shadow: 0 1px 1px #111;
    }
    .txt-right {
        text-align: right
    }
    .tline {
        padding: 3px 0px;
    }
    .title-bold {
        font-weight: bold;
    }
    .fline input {
        border: none;
        border-bottom: 1px solid rgb(224, 224, 224);
        color: #555;
        padding: 0;
        height: 36px;
        width: 100%;
    }
    .form-fields .rline input.last-line {
        margin: 0 0 17px 0;
    }

    ::-webkit-scrollbar {
        width: 5px;
    }
    ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        -webkit-border-radius: 10px;
        border-radius: 10px;
        width:5px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgb(255, 86, 86);
        background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .2) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, .2) 50%,
        rgba(255, 255, 255, .2) 75%,
        transparent 75%,
        transparent);
        width:5px;
    }
    ::-webkit-scrollbar-thumb:window-inactive {
        background: rgba(155, 155, 155, 0.4);
        width:5px;
    }

    ul.tight, ul.applied-branches {
        list-style: none;
        padding: 0 0 0 10px;
        border: 0;
        float: left;
        height: auto;
        width: 100%;
        overflow-y: auto;
        max-height: 296px;
        margin: 0;
    }
    ul.tight li, ul.applied-branches li {
        list-style: none;
        border: 0;
        margin: 5px 0;
        -webkit-box-shadow: 0 0.5px 0px #555;
        box-shadow: 0 0.5px 0px #777;
        cursor: pointer;
        font-weight: 500;
    }
    ul.tight li:nth-child(2), ul.applied-branches li:nth-child(2) {
        margin: 45px 0 5px 0;
    }
    ul.tight li span, ul.applied-branches li span {
        padding: 8px 0 10px;
    }
    ul.applied-branches {
        padding: 0 0 0 10px;
    }
    ul.applied-branches li .item-branch-id, ul.tight li .item-tuition-id {
        text-align: right;
        margin: 0 5% 0 0;
    }
    .tline {
        padding: 5px 0 0 0;
    }
    ul.tight li.selected-tuition-fee, ul.tight li.selected-tuition-fee span, ul.tight li.selected-tuition-fee .text-danger {
        color: #e60008!important;
        text-shadow: 0 1px 1px #ded6d6;
        font-weight: 500!important;
    }
    ul.tight li.selected-tuition-fee {
        border-bottom: 1px solid #e60008;
        box-shadow: 0 1px 0px #ded6d6;
    }
    ul.tight li:hover, ul.applied-branches li:hover {
        color: #a50005;
        box-shadow: 0 0.5px 0px rgb(165, 2, 2);
    }
    ul.tight li:active, ul.applied-branches li:active {
        color: #500003;
        text-shadow: 0 -1px 0 #FFF;
        box-shadow: 0 0.5px 0px rgb(87, 0, 0);
    }
    li.row.the-first {
        border-bottom: 1px solid rgb(47, 80, 117);
        width: 96%;
        position: absolute;
        margin: 0 4px 0 0;
        padding: 5px 0 0;
        background: #fff;
        z-index: 999;
    }
    li.row.the-first span {
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
    }
    .fline {
        margin: 0 0 10px 0;
    }
    .fline input {
        border: none;
        border-bottom: 1px solid #bbb;
        padding: 0 0 0 10px;
        height: 25px;
    }
    .header-filter {
        overflow: hidden;
        padding: 10px 0 0 0;
        height: 83px;
        z-index: 999;
        margin: 0 14px -11px 9px;
        position: relative;
        border-bottom: 1px solid #999;
    }
    .table-content {
        max-height: 300px;
    }
    .header-filter table {
        margin:0 !important;
    }
    table tr td.filter-row {
        padding: 5px 0 7px!important;
        background:#31577d;
        vertical-align: none;
    }
    table tr td.checkbox-item {
        width:5%
    }
    table.content-detail tr td {
        font-weight: 500!important;
        cursor: pointer;
    }
    table tr td.id-filter {
        width:7%
    }
    table tr td.id-filter input {
        width: 36px;
        height: 22px;
        padding: 0 8px;
    }
    table tr td.lms-filter {
        width:20%;
    }
    table tr td.lms-filter input {
        width:135px;
        height: 22px;
        padding: 0 8px;
    }
    table tr td.name-filter {
        width:44%;
    }
    table tr td.name-filter input {
        width: 300px;
        height: 22px;
        padding: 0 8px;
    }
    table tr td.zone-filter {
        width:12%;
    }
    table tr td.zone-filter select {
        width: 65px;
        height: 20px;
    }
    table tr td.region-filter {
        width:12%
    }
    table tr td.region-filter select {
        width: 60px;
        height: 20px;
    }
    table tr td.ceo-filter {
        width:30%;
    }
    table tr td.ceo-filter input {
        width:200px;
        padding: 0 8px;
    }

</style>