<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-clipboard"></i> <strong>Thêm Mới Quy Định, Pending </strong>
          </div>
          <div class="content-detail">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian pending MIN</label>
                    <input v-model="pending.min_days" class="form-control" type="number">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian pending MAX</label>
                    <input v-model="pending.max_days" class="form-control" type="number">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Vị trí gia hạn pending</label>
                    <select v-model="pending.role_id" class="form-control">
                      <option value="" disabled>Chọn vị trí gia hạn</option>
                      <option :value="role.id" v-for="(role, index) in roles" :key="index">{{role.name}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian bắt đầu hiệu lực</label>
                    <input v-model="pending.start_date" class="form-control" type="date">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian hết hiệu lực</label>
                    <input v-model="pending.expired_date" class="form-control" type="date">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Loại quy định</label>
                    <select name="" class="form-control" v-model="pending.type">
                      <option value="" disabled>Chọn loại quy định</option>
                      <option value="0">Pending</option>
                      <option value="1">Bảo lưu</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <select name="" class="form-control" v-model="pending.status">
                      <option value="" disabled>Chọn trạng thái</option>
                      <option value="0">Không hoạt động</option>
                      <option value="1">Hoạt động</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <div class="row">
                <div class="col-sm-12 col-sm-offset-3 text-right">
                  <button class="apax-btn full edit" type="submit" @click="addPendingRule"><i class="fa fa-save"></i> Lưu</button>
                  <button class="apax-btn full default" type="reset" @click="reset"><i class="fa fa-ban"></i> Hủy</button>
                  <router-link class="apax-btn full warning" :to="'/pending-rules'">
                    <i class="fa fa-sign-out"></i> Quay lại
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <b-modal 
          title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="callback" ok-variant="primary">
            <div v-html="message">
            </div>
          </b-modal>

          <b-modal 
            :title="html.modal.title" 
            :class="html.modal.class" size="sm" 
            v-model="html.modal.display" 
            @ok="action.modal" 
            ok-variant="primary"
        >
         <div v-html="html.modal.message"></div>
      </b-modal>


      </b-col>
    </b-row>
  </div>
</div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'

export default {
  name: 'Add-Pending-Rule',
  data() {
    return {
      html: {
        modal: {
          title: 'Thông Báo',
          class: 'modal-success',
          message: '',
          display:  false
        }
      },
      action: {
        modal: () => this.exitModal()
      },
      pending: {
        min_days: '',
        max_days: '',
        role_id: '',
        start_date: '',
        expired_date: '',
        status: '',
        type: ''
      },
      roles: [],
      message: '',
      modal: false
    }
  },
  created(){
    u.a().get(`/api/all/roles`).then(response =>{
      this.roles = response.data
    })
  },
  methods: {
    callback(){

    },
    reset(){
      this.pending.min_days = '',
      this.pending.max_days = '',
      this.pending.role_id = '',
      this.pending.start_date = '',
      this.pending.expired_date = '',
      this.pending.status = ''
      this.pending.type = ''
    },
    addPendingRule(){
      let msg = ''
      let validate = true
      if (this.pending.min_days == ''){
        validate = false
        msg += "(*) Min day Không được để trống! <br />"
      }
      if (this.pending.min_days < 0){
        validate = false
        msg += "(*) Min day không nhận giá trị âm! <br />"
      }
      if (this.pending.max_days == ''){
        validate = false
        msg += "(*) Max day Không được để trống! <br />"
      }
      if (this.pending.max_days < 0){
        validate = false
        msg += "(*) Max day không nhận giá trị âm! <br />"
      }
      if (this.pending.role_id == ''){
        validate = false
        msg += "(*) Vị trí gia hạn Không được để trống! <br />"
      }
      if (this.pending.start_date == ''){
        validate = false
        msg += "(*) Thời gian bắt đầu không được trống! <br />"
      }
      if (this.pending.expired_date == ''){
        validate = false
        msg += "(*) Thời gian kết thúc không được trống! <br />"
      }
      if (this.pending.start_date > this.pending.expired_date){
        validate = false
        msg += "(*) Ngày bắt đầu phải nhỏ hơn ngày kết thúc! <br />"
      }
      if (this.pending.status == ''){
        validate = false
        msg += "(*) Trạng thái không được trống! <br />"
      }
      if (this.pending.type == ''){
        validate = false
        msg += "(*) Loại quy định không được trống! <br />"
      }

      if (!validate){
          msg = `Dữ liệu kỳ học chưa hợp lệ <br/>-----------------------------------<br/><br/><p class="text-danger">${msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          this.message = msg 
          this.modal = true
      }else{
        this.savePendingRule();    
      }
    },
    savePendingRule(){
      u.a().post(`/api/pendingRegulations`, this.pending).then(response=>{
        // this.$router.push('/pending-rules')
        this.html.modal.message = "Thêm mới thành công quy định Pending!"
        this.html.modal.display = true
      })
    },
    exitModal(){
      this.$router.push('/pending-rules')
    },
  }
}
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
