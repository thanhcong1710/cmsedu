<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Semester</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Sản phẩm</label>
                      <select class="form-control" v-model="semester.product_id" readonly>
                        <option value="" disabled>Chọn sản phẩm</option>
                        <option :value="product.id" v-for="(product,index) in products" :key="index">{{product.name}}</option>
                      </select>
                    </div>
                  </div> 
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Semester</label>
                      <input type="text" v-model="semester.name" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Thời gian bắt đầu</label>
                      <input type="date" v-model="semester.start_date" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Thời gian kết thúc</label>
                      <input type="date" v-model="semester.end_date" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select v-model="semester.status" class="form-control" id="" readonly>
                        <option :value="1">Hoạt động</option>
                        <option :value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link class="apax-btn full warning" :to="'/semesters'">
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
    name: 'Semester-Detail',
    data() {
      return {
        semester: {
          name: '',
          lms_proc_id: '',
          status: '',
          product_id:'',
        },
        products:[],
      }
    },
    created(){
      u.a().get('/api/all/products/').then((response)=>{
        this.products = response.data;
      })
      let uri = '/api/semesters/'+this.$route.params.id;
			axios.get(uri).then((response) => {
        this.semester = response.data;
        console.log(`this.semester ${JSON.stringify(this.semester)}`)
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
