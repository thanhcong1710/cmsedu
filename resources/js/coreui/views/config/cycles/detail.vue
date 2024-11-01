<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật Thông Tin Kỳ Xếp Hạng Nhân Viên</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                </div>
                  <div class="content-detail">
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Tên kỳ</label>
                          <input type="text" readonly class="form-control" v-model="score.name">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Tháng</label>
                          <input type="text" readonly class="form-control" v-model="score.month">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Năm</label>
                          <input type="text" readonly class="form-control" v-model="score.year">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Status</label>
                          <select class="form-control" v-model="score.status" disabled>
                            <option value="">Trạng thái</option>
                            <option value="0">Không hoạt động</option>
                            <option value="1">Hoạt động</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-sm-12 col-sm-offset-3 text-right">
                          <router-link class="apax-btn full warning" :to="'/cycles'">
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
  name: 'Cycle-Detail',
  data () {
    return {
      score: {
        name: '',
        month: '',
        status: '',
        year: ''
      }
    }
  },
  created(){
    u.a().get('/api/scores/'+this.$route.params.id).then(response =>{
      this.score = response.data
    })
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