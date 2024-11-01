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
                          <input type="text" class="form-control" v-model="score.name">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Tháng</label>
                          <!-- <input type="text" class="form-control" v-model="score.month"> -->
                          <select name="" class="form-control" v-model="score.month">
                            <option value="" disabled>Chọn Tháng</option>
                            <option :value="month" v-for="(month, i) in months">{{ month }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Năm</label>
                          <!-- <input type="text" class="form-control" v-model="score.year"> -->
                          <select name="" class="form-control" v-model="score.year">
                            <option value="" disabled>Chọn năm</option>
                            <option :value="year" v-for="(year, i) in years">{{ year }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Trạng thái</label>
                          <select class="form-control" v-model="score.status">
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
                          <button class="apax-btn full edit" type="submit" @click="updateScore"><i class="fa fa-save"></i> Lưu</button>
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
  name: 'Edit-Cycle',
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
          modal: () => this.exitModal()
      },
      years: [2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030],
      months: [1,2,3,4,5,6,7,8,9,10,11,12],
      score: {}
    }
  },
  methods: {
    updateScore(){
      u.a().put(`/api/scores/`+this.$route.params.id, this.score).then(response=>{
        // this.$router.push('/cycles')
        this.html.modal.message = "Sửa thành công kỳ xếp hạng nhân viên!"
        this.html.modal.display = true
      })
    },
    exitModal(){
      this.$router.push('/cycles')
    },
  },
  created(){
    u.a().get(`/api/scores/`+this.$route.params.id).then(response =>{
      this.score = response.data;
    })
  }
}
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>