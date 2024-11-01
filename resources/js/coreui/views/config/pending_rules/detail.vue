<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-clipboard"></i> <strong>Chi Tiết Nội Dung Quy Định, Pending Mới</strong>
          </div>
          <div class="content-detail">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian pending min</label>
                    <input class="form-control" :value="pendingRule.min_days" readonly type="number">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian pending max</label>
                    <input class="form-control" :value="pendingRule.max_days" readonly type="number">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Vị trí gia hạn pending</label>
                    <select  :value="pendingRule.role_id" readonly class="form-control">
                        <option :value="role.id" v-for="(role, index) in roles">{{role.name}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian bắt đầu hiệu lực</label>
                    <input :value="pendingRule.start_date" class="form-control" readonly type="date">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian hết hiệu lực</label>
                    <input :value="pendingRule.expired_date" class="form-control" readonly type="date">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Loại pending</label>
                    <input type="text" :value="pendingRule.type|pendingTypeToString" class="form-control" readonly >
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <div class="row">
                <div class="col-sm-12 col-sm-offset-3 text-right">
                  <router-link class="apax-btn full warning" :to="'/pending-rules'">
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
  name: 'Detail-Pending-Rule',
  data() {
    return {
      pendingRule: {
        days: '',
        role_id: '',
        start_date: '',
        expired_date: ''
      },
      roles: []
    }
  },
  created(){
    u.a().get(`/api/pendingRegulations/${this.$route.params.id}`).then(response =>{
      this.pendingRule = response.data
    })
    u.a().get(`/api/all/roles`).then(response =>{
      this.roles = response.data
    })
  },
  methods: {
  },
  filters: {
    pendingTypeToString(v){
      return v == 1? "Bảo lưu":"Pending"
    }
  }
}
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
