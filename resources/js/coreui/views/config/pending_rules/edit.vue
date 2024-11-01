<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-clipboard"></i> <strong>Cập Nhật Sản Quy Định, Pending Mới</strong>
          </div>
          <div class="content-detail">
            <div class="panel-body">
             <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian pending MIN</label>
                    <input v-model="pending.min_days" class="form-control" type="number">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian pending MAX</label>
                    <input v-model="pending.max_days" class="form-control" type="number">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Vị trí gia hạn pending</label>
                    <select v-model="pending.role_id" class="form-control">
                      <option value="">Chọn vị trí gia hạn</option>
                      <option :value="role.id" v-for="(role, index) in roles" :key="index">{{role.name}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian bắt đầu hiệu lực</label>
                    <input v-model="pending.start_date" class="form-control" type="date">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Thời gian hết hiệu lực</label>
                    <input v-model="pending.expired_date" class="form-control" type="date">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Loại quy định</label>
                    <select name="" class="form-control" v-model="pending.type">
                      <option value="" disabled>Chọn loại quy định</option>
                      <option value="0">Pending</option>
                      <option value="1">Bảo lưu</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <select name="" class="form-control" v-model="pending.status">
                      <option value="">Chọn trạng thái</option>
                      <option value="0">Không hoạt động</option>
                      <option value="1">Hoạt động</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
             <div class="panel-footer">
              <div class="row">
                <div class="col-sm-12 col-sm-offset-3 text-right">
                  <button class="apax-btn full edit" type="submit" @click="updatePendingRule"><i class="fa fa-save"></i> Lưu</button>
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
  name: 'Edit-Pending-Rule',
  data() {
    return {
      pending: {
        max_days: '',
        min_days: '',
        role_id: '',
        start_date: '',
        expired_date: '',
        type: '',
        status: ''
      },
      roles: []
    }
  },
  created(){
    u.a().get(`/api/pendingRegulations/${this.$route.params.id}`).then(response=>{
      this.pending = response.data
    })
    u.a().get(`/api/all/roles`).then(response =>{
      this.roles = response.data
    })
  },
  methods: {
    updatePendingRule(){
      u.a().put(`/api/pendingRegulations/${this.$route.params.id}`, this.pending).then(response => {
        this.$router.push('/pending-rules')
        });
    },
  }
}
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
