<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thông tin danh mục đơn vị cơ sở</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
               <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã đơn vị cơ sở</label>
                <input type="text" :value="branch.id" class="form-control" readonly>
              </div>
            </div>
            <!--<div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã LMS</label>
                <input type="text" :value="branch.brch_id" class="form-control" readonly>
              </div>
            </div>-->
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã EFFECT</label>
               <input type="text" :value="branch.accounting_id" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên trung tâm</label>
                <input type="text" :value="branch.name" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Ngày khai trương</label>
                <input type="date" :value="branch.opened_date" class="form-control" readonly>
              </div>
            </div>
            <!-- <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã vùng</label>
                <input type="text" :value="branch.region_id" class="form-control" readonly>
              </div>
            </div> -->
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã khu vực</label>
                <input type="text" :value="branch.zone_id" class="form-control" readonly> 
              </div>
            </div>
            <!-- <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên Vùng</label>
                <input type="text" class="form-control" readonly v-model="branch.region_name">
              </div>
            </div> -->
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="text" v-model="branch.email" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Hot line</label>
                <input type="text" v-model="branch.phone" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select v-model="branch.status" class="form-control" id="" readonly>
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
                    <router-link :to="'/branches'" class="apax-btn full warning"><i class="fa fa-sign-out"></i> Quay Lại</router-link>
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
    name: 'branch-Detail',
    data() {
      return {
        branch: {
          name: '',
          lms_proc_id: '',
          status: ''
        }
      }
    },
    created(){
      let uri = '/api/branches/'+this.$route.params.id;
			axios.get(uri).then((response) => {
        this.branch = response.data;
        console.log(`this.branch ${JSON.stringify(this.branch)}`)
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
