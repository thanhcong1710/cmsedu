<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật Lý Do Bảo Lưu, Pending</strong>
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
                      <!-- <input type="text" v-model="reason.type" class="form-control"> -->
                      <select name="" id="" class="form-control" v-model="reason.type">
                        <option :value="0">Pending</option>
                        <option :value="1">Withdraw</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="reason.status">
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
                    <button class="apax-btn full edit" type="submit" @click="updateReason"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetall"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="'/reasons'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>

      <b-modal 
              :title="html.modal.title" 
              :class="html.modal.class" size="sm" 
              v-model="html.modal.display" 
              @ok="action.modal" 
              ok-variant="primary"
          >
           <div v-html="html.modal.message"></div>
        </b-modal>

    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Edit-reason',
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
        reasons: []
      }
    },
    created() {

      let uri = '/api/reasons/' + this.$route.params.id + '/edit';
      axios.get(uri).then((response) => {
        this.reason = response.data;
      })
      // let uri = '/api/reasons/'+this.$route.params.id;
      // axios.get(uri).then((response) => {
      // 	this.reason = response.data;
      // 	console.log(`this.reason ${JSON.stringify(this.reason)}`)
      // });
    },
    methods: {
      resetall(){
        this.reason.description = ''
        this.reason.status = ''
        this.reason.type = ''
      },
      exitModal(){
        this.$router.push('/reasons')
      },
      updateReason() {
        let uri = `/api/reasons/` + this.$route.params.id;
        axios.put(uri, this.reason).then((response) => {
          // this.$router.push('/reasons')
          this.html.modal.message = 'Sửa thành công lý do bảo lưu, Pending'
          this.html.modal.display = true
        })
      },
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
