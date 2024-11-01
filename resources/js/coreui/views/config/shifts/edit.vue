<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật Thông Tin Ca Học</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                  </div>
                  <div class="content-detail">
                      <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Tên ca học</label>
                          <input class="form-control" v-model="shift.name" type="text">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Thời gian bắt đầu</label>
                          <input class="form-control" v-model="shift.start_time" type="time">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Thời gian kết thúc</label>
                          <input class="form-control" v-model="shift.end_time" type="time">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Khu vực</label>
                          <select class="form-control" v-model="shift.zone_id">
                            <option :value="zone.id" v-for="(zone,index) in zones">{{zone.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Trạng thái</label>
                          <select class="form-control" v-model="shift.status">
                            <option value="0">Không hoạt động</option>
                            <option value="1">Hoạt động</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-sm-12 col-sm-offset-3 text-right">
                          <button class="apax-btn full edit" type="submit" @click="updateShift"><i class="fa fa-save"></i> Lưu</button>
                          <router-link class="apax-btn full warning" :to="'/shifts'">
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
  name: 'Edit-Shift',
  data () {
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
        modal: () => this.exitModal(),
      },  
      shift: {
        name: '',
        start_time: '',
        end_time: '',
        status: '',
        zone_id: '',
      },
      zones: []
    }
  },
  methods: {
    updateShift(){
      let uri = `/api/shifts/`+this.$route.params.id;
      u.a().put(uri,this.shift).then(response =>{
        this.html.modal.message = "Cập nhật thành công ca học!"
        this.html.modal.display = true
      })
    },
     exitModal(){
        this.$router.push('/shifts')
    },
    resetAll(){

    }
  },
  created(){
    let uri = `/api/shifts/`+this.$route.params.id
    u.a().get(uri).then(response =>{
      this.shift = response.data;
    })

    u.a().get('/api/zones').then(response =>{
      this.zones = response.data
    })
  }
}
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>