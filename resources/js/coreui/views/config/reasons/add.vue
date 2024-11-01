<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Mới Lý Do Withdraw, Pending</strong>
              <div class="card-actions">
                <a href="skype:live:c7a5d68adf8682ff?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Tên lý do</label>
                      <input type="text" v-model="reason.description" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Loại lý do</label>
                      <select class="form-control" v-model="reason.type">
                        <option value="" disabled>Chọn lý do</option>
                        <option value="0">Pending</option>
                        <option value="1">WithDraw</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="reason.status">
                        <option value="" disabled>Chọn trạng thái</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="addReason"><i class="fa fa-save"></i> Lưu</button>
                    <router-link class="apax-btn full warning" :to="'/reasons'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>

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
    name: 'Add-reason',
    data() {
      return {
        html: {
          modal: {
            title: 'Thông Báo',
            class: 'modal-success',
            display: false,
            message: ''
          }
        },
        action: {
          modal: () => this.exitModal(),
        },
        reason: {
          type: '',
          description: '',
          status: ''
        },
        message: '',
        modal: false
      }
    },
    methods: {
      exitModal(){
        this.$router.push('/reasons')
      },
      addReason() {
        let msg = ""
        let validate = true

        if (this.reason.type == ''){
          validate = false
          msg += "(*) Loại lý do không được trống! <br/>"
        }
        if (this.reason.status == ''){
          validate = false
          msg += "(*) Trạng thái không được trống! <br/>"
        }
        if (this.reason.description == ''){
          validate = false
          msg += "(*) Tên lý do không được trống! <br/>"
        }

        if (!validate){
          msg = `Dữ liệu kỳ học chưa hợp lệ <br/>-----------------------------------<br/><br/><p class="text-danger">${msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          this.message = msg 
          this.modal.display = true
        }else{
          u.a().post(`/api/reasons`, this.reason).then(response => {
          // this.$router.push('/reasons')
          // this.reason.push(response.data.reason);
          this.html.modal.message = 'Thêm mới thành công lý do bảo lưu, Pending'
          this.html.modal.display = true

          });
        }

      },
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
