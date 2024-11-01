<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Sửa Phòng Học </strong>
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
                      <input type="text" v-model="room.room_name" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <select class="form-control" id="" v-model="room.branch_id">
                        <option value="">Trung tâm</option>
                        <option :value="branch.id" v-for="(branch, index) in branches" :key="index">{{branch.name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng Thái</label>
                      <select v-model="room.status" class="form-control" id="">
                        <option selected value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Loại phòng học</label>
                      <select v-model="room.type" class="form-control" id="">
                        <option value="1">Lấy từ hệ thống</option>
                        <option value="0">Nhập thủ công</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="updateRoom"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="'/rooms'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Edit-Room',
    data() {
      return {
        //Set default checked
        branches: [],
        product: '0',
        status: '0',
        area: '0',

        room: {
          room_name: '',
          branch_id: '',
          status: '',
          type: ''
        },
        rooms: []
      }
    },
    created(){

      let uri = '/api/rooms/'+this.$route.params.id+'/edit';
      axios.get(uri).then((response) => {
        this.room = response.data;
      })
      u.a().get(`/api/reports/branches/`).then(response =>{
        this.branches = response.data;
     })
    },
    methods: {
      resetAll(){
        let uri = '/api/rooms/'+this.$route.params.id+'/edit';
          axios.get(uri).then((response) => {
          this.room = response.data;
        })
      },
      updateRoom(){
        let uri = `/api/rooms/`+this.$route.params.id;
        axios.put(uri, this.room).then((response) => {
          if(response.data.success == true) {
            alert(response.data.data);
            this.$router.push('/rooms')
          }else {
            alert(response.data.message);
          }
          
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
