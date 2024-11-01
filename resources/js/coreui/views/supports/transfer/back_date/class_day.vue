<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>CẬP NHẬT LẠI NGÀY XẾP LỚP CHO HỌC SINH</strong>
                        </div>
                        <div class="apax-show-detail" v-if="student">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><b>THÔNG TIN HỌC SINH</b></label>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Trung tâm</label>
                                        <input class="form-control" type="text" :value="student.branch_name" readonly disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Tên học sinh</label>
                                        <input class="form-control" :value="student.std_name" readonly disabled/>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Mã CMS</label>
                                                <input class="form-control" type="text" :value="student.cms_id" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Mã Cyber</label>
                                                <input class="form-control" type="text" :value="student.cyber_code" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Mã Phiếu ĐK</label>
                                                <input class="form-control" type="text" :value="student.accounting_id" readonly disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Sản phẩm</label>
                                                <input class="form-control" type="text" :value="student.product_name" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-sm-8">
                                            <div class="form-group">
                                                <label class="control-label">Lớp học</label>
                                                <input class="form-control" type="text" :value="student.cls_name" readonly disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Ngày bắt đầu học</label>
                                                <input class="form-control" type="text" :value="student.enrolment_start_date" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Ngày kết thúc</label>
                                                <input class="form-control" type="text" :value="student.enrolment_last_date" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Ngày học</label>
                                                <input class="form-control" type="text" :value="convertName(student.class_day)" readonly disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Giá gốc gói phí</label>
                                                <input class="form-control" type="text" :value="convert(student.tuition_fee_price)" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Giá sau chiết khấu</label>
                                                <input class="form-control" type="text" :value="convert(student.must_charge)" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Số phí còn phải thu</label>
                                                <input class="form-control" type="text" :value="student.debt_amount" readonly disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Số buổi được học</label>
                                                <input class="form-control" type="text" :value="student.real_sessions" readonly disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Tổng số buổi theo gói phí</label>
                                                <input class="form-control" type="text" :value="student.total_sessions" readonly disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label"><b>NỘI DUNG THAY ĐỔI</b></label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Chọn ngày bắt đầu học</label>
                                                <datepicker
                                                        :value="month"
                                                        @input="this.changeMonth"
                                                        placeholder="Chọn tháng"
                                                        format="YYYY-MM-DD"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Ngày kết thúc học</label>
                                                <input class="form-control" type="text" :value="end_date" readonly disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <ApaxButton
                                                    customClass="apax-btn full edit"
                                                    :onClick="save"
                                            >Lưu
                                            </ApaxButton>
                                            <ApaxButton
                                                    customClass="apax-btn error full"
                                                    :onClick="quit"
                                            >Thoát
                                            </ApaxButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="callback" ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
            <div v-html="message">
            </div>
        </b-modal>
    </div>
</template>

<script>
  import u from '../../../../utilities/utility'
  import Datepicker from '../../../base/common/DatePicker'
  import { getDate } from '../../../base/common/utils'
  import ApaxButton from '../../../components/Button'
  export default {
    name: 'Edit-Charge',
    components: {
      Datepicker,ApaxButton
    },
    data () {
      return {
        tabs: {} ,
        student: {} ,
        mydate: new Date(),
        month    : null,
        end_date: null,
        modal:false,
        message:'Cập nhật thành công',

      }
    },
    created () {
      // return false
      this.start()
    },
    methods:{
      callback(){
        this.start()
      },
      start(){
        var sId = this.$route.params.id
        u.a().get(`/api/students/detail-new/${sId}`).then((response) => {
          this.student = response.data.data
          this.tabs = response.data.data.tabs
          if (this.student){
            var start_date = response.data.data.enrolment_start_date
            this.month = new Date(start_date)
          }
        })
      },
      quit(){
        //this.$router.go(-1)
          this.$router.push('/supports/list')
      },
      save(){
        const params  = {
          last_date: this.end_date,
          start_date: getDate(this.month),
          contract_id:this.student.contract_id,
        }
        if(!this.validate(params)){
          return false
        }
        const nextCf = confirm("Bạn có muốn Lưu ngày xếp lớp đã thay đổi?");
        if (nextCf === true) {
          u.apax.$emit('apaxLoading', true)
          u.p(`/api/support/change-enrolment-start-date`, params).then((res) => {
            this.modal = true
            //this.end_date = res
            //this.quit()
            //this.start()
          }).finally(() => {
            u.apax.$emit('apaxLoading', false)
          })
        }

      },
      convert(value){
        let val = (value/1).toFixed(0).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
      },
      convertName(value){
        return u.getDayNameVi(value)
      },
      changeMonth(value){
        this.month = value
        const params  = {
          branch_id: this.student.branch_id,
          total_session: this.student.total_sessions,
          real_sessions: this.student.real_sessions,
          debt_amount: this.student.debt_amount,
          class_day: this.student.class_day,
          start_date      :getDate(value),
          contract_id:this.student.contract_id,
          student_id:this.student.student_id,
        }
        u.apax.$emit('apaxLoading', true)
        u.g(`/api/support/change-enrolment-start-date`, params).then((res) => {
          if (res.code == 1){
            return this.$notify({
              group: 'apax-atc',
              title: 'Có lỗi xảy ra!!!',
              type: 'danger',
              duration: 3000,
              text: `<br/>-----------------------------------------------<br/>${res.msg}`
            })
          }
          else
            this.end_date = res.msg

        }).finally(() => {
          u.apax.$emit('apaxLoading', false)
        })
      },
      validate(params) {
        let message = "";
        let status = true;
        if (!_.get(params, 'last_date')) {
          message += `Bạn chưa thay đổi ngày bắt đầu học<br/>Ngày kết thúc không hợp lệ<br/>`;
          status = false
        }

        if (!status) {
          this.$notify({
            group: 'apax-atc',
            title: 'Có lỗi xảy ra!',
            type: 'danger',
            duration: 3000,
            text: `<br/>-----------------------------------------------<br/>${message}`
          })
        }
        return status
      },
    },
  }
</script>

<style scoped>

</style>
