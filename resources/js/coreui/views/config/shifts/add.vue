<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Ca Học Mới</strong>
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
                            <option value="" disabled>Chọn khu vực</option>
                            <option :value="zone.id" v-for="(zone,ind) in zones" :key="ind">{{zone.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Trạng thái</label>
                          <select class="form-control" v-model="shift.status">
                            <option value="" disabled>Chọn trạng thái</option>
                            <option value="0">Không hoạt động</option>
                            <option value="1">Hoạt động</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-sm-12 col-sm-offset-3 text-right">
                          <button class="apax-btn full edit" type="submit" @click="addShift"><i class="fa fa-save"></i> Lưu</button>
                          <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
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
  name: 'Add-Shift',
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
  created(){
    u.a().get('/api/zones/').then(response =>{
      this.zones = response.data;
    })
  },
  methods: {
    addShift(){
      const shift = {
        name: this.shift.name,
        start_time: this.shift.start_time,
        end_time: this.shift.end_time,
        zone_id: this.shift.zone_id,
        status: this.shift.status,
        type: this.shift.type, 
      }
      if(shift.name == ''){
        alert("Tên không được để trống !")
        return false
      }else if(shift.start_time == ''){
        alert("Thời gian bắt đầu không được để trống !")
        return false
      }else if(shift.end_time == ''){
        alert("Thời gian kết thúc không được để trống !")
        return false
      }else if(shift.zone_id == ''){
        alert("Vùng không được để trống !")
        return false
      }else if(shift.status == ''){
        alert("Trạng thái không được để trống !")
        return false
      }
      else {
        this.saveShift()    
      }
    },
    saveShift(){
      let uri = `/api/shifts/`
      u.a().post(uri, this.shift).then(response =>{
        // this.$router.push('/shifts')
        this.html.modal.message = "Thêm mới thành công ca học!"
        this.html.modal.display = true
      })
    },
    exitModal(){
        this.$router.push('/shifts')
    },
    resetAll(){
      this.shift.name = ''
      this.shift.start_time = ''
      this.shift.end_time = ''
      this.shift.status = ''
      this.shift.zone_id = ''
    }
  }
}
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>