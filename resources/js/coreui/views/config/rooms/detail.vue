<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Phòng Học</strong>
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
                      <label class="control-label">Tên phòng học</label>
                      <input class="form-control" :value="room.room_name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <input class="form-control":value="room.branch_name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" :value="room.status|statusToName" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Loại phòng học</label>
                      <select v-model="room.type" class="form-control" id="" readonly>
                        <option value="1">Lấy từ hệ thống</option>
                        <option value="0">Nhập thủ công</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link class="apax-btn full warning" :to="'/rooms'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
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
    name: 'Room-Detail',
    data() {
      return {
        room: {
          room_name: '',
          status: '',
          type: '',
          branch_name: ''
        }
      }
    },
    created(){
      let uri = '/api/rooms/'+this.$route.params.id;
			axios.get(uri).then((response) => {
        this.room = response.data;
        console.log(`this.room ${JSON.stringify(this.room)}`)
			});
    },
    methods: {

    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
