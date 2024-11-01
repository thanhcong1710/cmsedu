<template>
  <div class="app flex-row align-items-center apax-html">
    <div class="container-fluid">  
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting1" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting1" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Copy log_contracts_history sang contracts</h4>
            </div>
           <div class="col-md-3">
              <input class="form-control" v-model="tool1.log_id" placeholder="ID log_contract_history">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="copyContractLog">Copy</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting2" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting2" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Convert dữ liệu contracts</h4>
            </div>
           <div class="col-md-3">
              <input class="form-control" v-model="tool2.cms_id" placeholder="Nhập mã CMS học sinh">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="convertContract">Xử lý</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting3" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting3" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Recall API Cyber tạo contract</h4>
            </div>
           <div class="col-md-3">
              <input class="form-control" v-model="tool3.contract_id" placeholder="Nhập ID contract">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="recallCyberCreateContract">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting4" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting4" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Recall API Cyber xếp lớp summer</h4>
            </div>
           <div class="col-md-3">
              <input class="form-control" v-model="tool4.contract_id" placeholder="Nhập ID contract">
            </div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool4.enrolment_start_date" placeholder="Nhập ngày bắt đầu yyyy-mm-dd">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="recallCyberEnrolmentSummer">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting5" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting5" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Thêm học sinh vào report_full_fee_active</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_5.cms_id" placeholder="Nhập mã cms học sinh">
            </div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_5.contract_id" placeholder="Contract_id được tính">
            </div>
            <div class="col-md-3">
              <date-picker style="width:100%;"
                  v-model="tool_5.report_month"
                  :lang="datepickerOptions.lang"
                  format="YYYY-MM"
                  type="month" 
                  placeholder="Chọn tháng"
                  not-before="2019-06-01"
                  :not-after="tool_5.not_after">
              </date-picker>
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="insertReportFullFeeActive">Cập nhật</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting6" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting6" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Cập nhật ngày học cuối</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_6.cms_id" placeholder="Nhập mã cms học sinh">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool6">Cập nhật</button>
            </div>
            <p style="color:red"><i>(Không nhập mã cms tương ứng quét cập nhật tất cả học sinh)</i></p>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting7" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting7" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Cập nhật Renew Report</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_7.cms_id" placeholder="Nhập mã cms học sinh">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool7">Cập nhật</button>
            </div>
            <p style="color:red"><i>(Không nhập mã cms tương ứng quét cập nhật tất cả học sinh)</i></p>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting8" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting8" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Recall API Cyber chuyển trung tâm</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_8.contract_id" placeholder="Nhập mã id contract">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool8">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting9" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting9" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Withdraw học sinh quá hạn</h4></div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool9">Withdraw</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting10" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting10" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Recall API Cyber xếp lớp</h4>
            </div>
           <div class="col-md-3">
              <input class="form-control" v-model="tool_10.contract_id" placeholder="Nhập ID contract">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="recallCyberCreatEnrolement">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting11" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting11" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Gửi tin nhắn Sms</h4>
            </div>
           <div class="col-md-3">
              <input class="form-control" v-model="tool_11.phone" placeholder="Nhập các số điện thoại cách nhau bởi dấu  ,">
            </div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_11.content" placeholder="Nhập nội dung">
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="actionTool11">Gửi tin nhắn</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting12" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting12" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Call Cyber  từ bảng tạm</h4>
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="actionTool12(1)">Call Học Viên </button>
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="actionTool12(2)">Call Gói Phí </button>
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="actionTool12(3)">Call Xếp Lớp </button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting13" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting13" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Tools call API học viên của cyber</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_13.cms_id" placeholder="Nhập mã CMS">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool13">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting14" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting14" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Tools call tạo mới API học sinh của LMS</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_14.cms_id" placeholder="Nhập mã CMS">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool14">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting15" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting15" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Tools call edit API học sinh của LMS</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_15.cms_id" placeholder="Nhập mã CMS">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool15">Call API</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
           <div v-show="flags.requesting16" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting16" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Tools Xử lý  bảo lưu Online</h4></div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool16">Xử Lý</button>
            </div>
          </div>  
        </div>
      </b-card>
      <b-card>
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12"><h4>Tools Xử lý chuyển all trung tâm</h4></div>
            <div class="col-md-3">
              <a href="/api/process-transfer-all-branch" target="blank">
                <button class="btn btn-success">Xử Lý</button>
              </a>  
                </div>
          </div>
        </div> 
      </b-card>
      <b-card>
        <div class="col-md-12">
          <div class="row">
           <div v-show="flags.requesting17" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting17" class="loading-text cssload-loader">Đang xử lý ...</div>
            </div>
          </div>
          </div>
          <div class="row">
            <div class="col-md-12"><h4>Tools cập nhật mã voucher vào contract</h4></div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_17.contract_id" placeholder="Nhập contrat ID">
            </div>
            <div class="col-md-3">
              <input class="form-control" v-model="tool_17.coupon" placeholder="Nhập mã coupon">
            </div>
            <div class="col-md-3">
              <button class="btn btn-success" @click="actionTool17">Xử lý</button>
            </div>
          </div>  
        </div>
      </b-card>
    </div>
  </div>
</template>

<script>
  import u from '../../utilities/utility'
  import datePicker from 'vue2-datepicker'
  export default {
    name: 'Tools',
    components: {
      datePicker,
    },
    data() {
      return {
        tool1:{
          contract_id:'',
        },
        tool2:{
          cms_id:'',
        },
        tool3:{
          contract_id:'',
        },
        tool4:{
          contract_id:'',
          enrolment_start_date:'',
        },
        tool_5:{
          cms_id:'',
          contract_id:'',
          report_month:'',
          not_after:'',
        },
        tool_6:{
          cms_id:'',
        },
        tool_7:{
          cms_id:'',
        },
        tool_8:{
          contract_id:'',
        },
        tool_10:{
          contract_id:'',
        },
        tool_11:{
          phone:'',
          content:'',
        },
        tool_13:{
          cms_id:'',
        },
        tool_14:{
          cms_id:'',
        },
        tool_15:{
          cms_id:'',
        },
        tool_17:{
          contract_id:'',
          coupon:'',
        },
        flags: {
          requesting1: false,
          requesting2: false,
          requesting3: false,
          requesting4: false,
          requesting5: false,
          requesting6: false,
          requesting7: false,
          requesting8: false,
          requesting9: false,
          requesting10: false,
          requesting11: false,
          requesting12: false,
          requesting13: false,
          requesting14: false,
          requesting15: false,
          requesting16: false,
        },
        datepickerOptions: {
          closed: true,
          value: "",
          minDate: new Date('2018-10-01'),
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: [
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
            ],
            pickers: ["", "", "7 ngày trước", "30 ngày trước"]
          }
        },
      }
    },
    created() {
      const session = u.session().user
      if (parseInt(session.role_id) != u.r.super_administrator) {
        this.$router.push('/')
      }
    },
    watch: {},
    computed: {},
    methods: {
      copyContractLog(){
        const delStdConf = confirm("Bạn có chắc rằng muốn copy log_contracts_history sang contracts không?");
        if (delStdConf === true) {
          if(this.tool1.log_id!=''){
            this.flags.requesting1 = true
            u.g(`/api/tools/copy-log-contract/`+this.tool1.log_id).then(resp => {
              this.flags.requesting1 = false
              alert('Copy thành công')
            }).catch(e => {
              this.flags.requesting1 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          }
        }  
      },
      convertContract(){
        const delStdConf = confirm("Bạn có chắc rằng muốn convert dữ liệu contracts của học sinh này không?");
        if (delStdConf === true) {
          if(this.tool2.cms_id!=''){
            this.flags.requesting2 = true
            u.g(`/api/tools/convert-contract/`+this.tool2.cms_id).then(resp => {
              this.flags.requesting2 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting2 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          }
        }  
      },
      recallCyberCreateContract(){
        const delStdConf = confirm("Bạn có chắc rằng muốn CALL Lai API cyber contract này không?");
        if (delStdConf === true) {
          if(this.tool3.contract_id!=''){
            this.flags.requesting3 = true
            u.g(`/api/tools/recall-cyber-create-contract/`+this.tool3.contract_id).then(resp => {
              this.flags.requesting3 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting3 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          }
        }  
      },
      recallCyberEnrolmentSummer(){
        const delStdConf = confirm("Bạn có chắc rằng muốn CALL Lai API cyber Summer?");
        if (delStdConf === true) {
          this.flags.requesting4 = true
          u.g(`/api/tools/recall-cyber-enrolment-summer?contract_id=`+this.tool4.contract_id+`&enrolment_start_date=`+this.tool4.enrolment_start_date).then(resp => {
            this.flags.requesting4 = false
            alert('Xử lý thành công')
          }).catch(e => {
            this.flags.requesting4 = false
            alert('Có lỗi xảy ra. Vui lòng thử lại sau')
          })
        }  
      },
      insertReportFullFeeActive(){
        if(this.tool_5.cms_id !='' && this.tool_5.report_month !=''){
          const conF = confirm("Bạn chắc chắn muốn thêm học sinh vào report_full_fee_active");
          if (conF === true) {
            let day = this.getDate(this.tool_5.report_month)
            this.flags.requesting5 = true;
            u.g(`/api/tools/insert-report-full-fee-active/`+this.tool_5.cms_id+`/`+day+`/`+this.tool_5.contract_id).then(resp => {
                this.flags.requesting5 = false
                alert('Cập nhật dữ liệu thành công!')
              }).catch(e => {
                this.flags.requesting5 = false
                alert('Có lỗi xảy ra. Vui lòng thử lại sau!')
              })
          }
        }
      },
      getDate(date) {
        let day = date instanceof Date && !isNaN(date.valueOf()) ? date : new Date()
        if (day instanceof Date && !isNaN(day.valueOf())) {
          var year = day.getFullYear()
          var month = (day.getMonth() + 1).toString()
          var formatedMonth = month.length === 1 ? "0" + month : month
          return `${year}-${formatedMonth}`
        }
        return "";
      },
      actionTool6(){
        const conF = confirm("Bạn chắc chắn muốn cập nhật ngày học cuối của học sinh");
        if (conF === true) {
          this.flags.requesting6 = true;
          u.g(`/api/jobs/update?cms_id=`+this.tool_6.cms_id).then(resp => {
              this.flags.requesting6 = false
              alert('Cập nhật dữ liệu thành công!')
            }).catch(e => {
              this.flags.requesting6 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau!')
            })
        }
      },
      actionTool7(){
        const conF = confirm("Bạn chắc chắn muốn cập nhật renew_report của học sinh");
        if (conF === true) {
          this.flags.requesting7 = true;
          u.g(`/api/jobs/update-tools-renew-report?cms_id=`+this.tool_7.cms_id).then(resp => {
              this.flags.requesting7 = false
              alert('Cập nhật dữ liệu thành công!')
            }).catch(e => {
              this.flags.requesting7 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau!')
            })
        }
      },
      actionTool8(){
        if(this.tool_8.contract_id !='' ){
          const delStdConf = confirm("Bạn có chắc rằng muốn CALL lại API chuyển trung tâm?");
          if (delStdConf === true) {
            this.flags.requesting8 = true
            u.g(`/api/tools/recall-cyber-branch-transfer?contract_id=`+this.tool_8.contract_id).then(resp => {
              this.flags.requesting8 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting8 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          } 
        } 
      },
      actionTool9(){
        const delStdConf = confirm("Bạn có chắc rằng muốn withdraw học sinh quá hạn?");
        if (delStdConf === true) {
          this.flags.requesting9 = true
          u.g(`/api/tools/withdraw_all`).then(resp => {
            this.flags.requesting9 = false
            alert('Xử lý thành công')
          }).catch(e => {
            this.flags.requesting9 = false
            alert('Có lỗi xảy ra. Vui lòng thử lại sau')
          })
        } 
      },
      recallCyberCreatEnrolement(){
        const delStdConf = confirm("Bạn có chắc rằng muốn CALL lại API xếp lớp contract này không?");
        if (delStdConf === true) {
          if(this.tool_10.contract_id!=''){
            this.flags.requesting10 = true
            u.g(`/api/tools/recall-cyber-create-enrolment/`+this.tool_10.contract_id).then(resp => {
              this.flags.requesting10 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting10 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          }
        }  
      },
      actionTool11(){
        if(this.tool_11.phone && this.tool_11.content){
          const delStdConf = confirm("Bạn có chắc rằng muốn gửi tin nhắn sms");
          if (delStdConf === true) {
            this.flags.requesting11 = true
            let params = {phone: this.tool_11.phone,content:this.tool_11.content}
            u.p(`/api/tools/send_sms`,params).then(resp => {
              this.flags.requesting11 = false
              this.tool_11.phone=""
              this.tool_11.content=""
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting11 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          } 
        }
      },
      actionTool12(type){
        if(type==1){
          this.flags.requesting12 = true
          u.g(`/api/tools/call_cyber_hocvien`).then(resp => {
            this.flags.requesting12 = false
            alert('Xử lý thành công')
          }).catch(e => {
            this.flags.requesting12 = false
            alert('Có lỗi xảy ra. Vui lòng thử lại sau')
          })
        }else if(type==2){
          this.flags.requesting12 = true
          u.g(`/api/tools/call_cyber_goiphi`).then(resp => {
            this.flags.requesting12 = false
            alert('Xử lý thành công')
          }).catch(e => {
            this.flags.requesting12 = false
            alert('Có lỗi xảy ra. Vui lòng thử lại sau')
          })
        }else{
          this.flags.requesting12 = true
          u.g(`/api/tools/call_cyber_xeplop`).then(resp => {
            this.flags.requesting12 = false
            alert('Xử lý thành công')
          }).catch(e => {
            this.flags.requesting12 = false
            alert('Có lỗi xảy ra. Vui lòng thử lại sau')
          })
        }
      },
      actionTool13(){
        if(this.tool_13.cms_id !='' ){
          const delStdConf = confirm("Bạn có chắc rằng muốn call API học viên của cyber?");
          if (delStdConf === true) {
            this.flags.requesting13 = true
            u.g(`/api/tools/action_tool_13?cms_id=`+this.tool_13.cms_id).then(resp => {
              this.flags.requesting13 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting13 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          } 
        } 
      },
      actionTool14(){
        if(this.tool_14.cms_id !='' ){
          const delStdConf = confirm("Bạn có chắc rằng muốn  call tạo mới API học sinh của LMS?");
          if (delStdConf === true) {
            this.flags.requesting14 = true
            u.g(`/api/tools/action_tool_14?cms_id=`+this.tool_14.cms_id).then(resp => {
              this.flags.requesting14 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting14 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          } 
        } 
      },
      actionTool15(){
        if(this.tool_15.cms_id !='' ){
          const delStdConf = confirm("Bạn có chắc rằng muốn  call edit API học sinh của LMS?");
          if (delStdConf === true) {
            this.flags.requesting15 = true
            u.g(`/api/tools/action_tool_15?cms_id=`+this.tool_15.cms_id).then(resp => {
              this.flags.requesting15 = false
              alert('Xử lý thành công')
            }).catch(e => {
              this.flags.requesting15 = false
              alert('Có lỗi xảy ra. Vui lòng thử lại sau')
            })
          } 
        } 
      },
      actionTool16(){
        this.flags.requesting16 = true
        u.g(`/api/process_reserve_transfer_online`).then(resp => {
          this.flags.requesting16 = false
          alert('Xử lý thành công')
        }).catch(e => {
          this.flags.requesting16 = false
          alert('Có lỗi xảy ra. Vui lòng thử lại sau')
        })
      }, 
      actionTool17(){
        if(this.tool_17.contract_id !='' && this.tool_17.coupon !=''){
          this.flags.requesting17 = true
          u.p(`/api/process_update_coupon`,this.tool_17).then(resp => {
            this.flags.requesting17 = false
            alert(resp.message)
          }).catch(e => {
            this.flags.requesting17 = false
            alert('Có lỗi xảy ra. Vui lòng thử lại sau')
          })
        }
      } 
    }
  }
</script>

<style scoped language="scss">
  .time-picker {
    width: 300px;
    height: 80px;
  }

  #process {
    width: 0;
  }

  .custom-modal {
    padding: 10px 15px;
  }
</style>
