<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Phòng Học</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Tên phòng học <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" v-model="room.room_name">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <select class="form-control" v-model="room.branch_id" id="">
                        <option value="">Tất cả</option>
                        <option :value="branch.id" v-for="(branch, index) in branches" :key="index">{{branch.name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng Thái</label>
                      <select v-model="room.status" class="form-control">
                        <option value="" disabled>Chọn trạng thái</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                   <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Loại phòng học</label>
                      <select class="form-control" v-model="room.type" id="">
                        <option value="" disabled>Chọn loại phòng</option>
                        <option value="0">Nhập thủ công</option>
                        <option value="1">Lấy từ hệ thống</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="addRoom"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="'/rooms'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
          <b-modal 
              title="THÔNG BÁO" 
              class="modal-primary" 
              size="sm" 
              v-model="modal" 
              @ok="callback" 
              ok-variant="primary"
          >
            <div v-html="message"></div>
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
    name: 'Add-Room',
    data() {
      return {
        branches: [],
        room: {
          room_name: '',
          branch_id: '',
          status: '',
          type: ''
        },
        message: '',
        modal: false
      }
    },
    created(){  
     u.a().get(`/api/reports/branches/`).then(response =>{
      this.branches = response.data;
     })
    },
    methods: {
      addRoom() {
        // console.log(this.room);
        let msg = ''
        let validate = true
        if (this.room.room_name == ''){
          validate = false
          msg += "(*) Tên phòng học không được để trống! <br />";
        }
        if (this.room.branch_id == ''){
          validate = false
          msg += "(*) Trung tâm không được để trống! <br />";
        }
        if (this.room.status == ''){
          validate = false
          msg += "(*) Trạng thái không được để trống! <br />";
        }
        if (this.room.type == ''){
          validate = false
          msg += "(*) Loại phòng học không được để trống! <br />";
        }

        if (!validate){
          msg = `Dữ liệu kỳ học chưa hợp lệ <br/>-----------------------------------<br/><br/><p class="text-danger">${msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          this.message = msg
          this.modal = true
        }
        else{
          u.a().post(`/api/rooms`, this.room).then(response => {
            if(response.data.success == true) {
                alert(response.data.data);
                this.$router.push('/rooms')
              }else {
                alert(response.data.message);
              }
          });
        }
        //console.log(`Add room ${this.name}`);
      },
      resetAll(){
        this.room.room_name = ''
        this.room.branch_id = ''
        this.room.status = ''
        this.room.type = ''
      }
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
