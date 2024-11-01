<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Ngày Nghỉ Lễ</strong>
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
                      <label class="control-label">Tên</label>
                      <input class="form-control" :value="holiday.name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Từ ngày</label>
                      <input class="form-control" :value="holiday.start_date" type="date" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Đến ngày</label>
                      <input class="form-control" type="date" :value="holiday.end_date" readonly>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Khu vực áp dụng</label>
                      <input class="form-control" :value="holiday.zone[0].name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Sản phẩm</label>
                      <div class="form-check-group"> 
                          <div class="form-check-left" v-for="(product,index) in products">
                              <input type="checkbox" :value="product.id" class="form-check-input" v-model="holiday.products" disabled />
                              <label class="form-check-label left-check">{{product.name}}</label>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" :value="filterStatus(holiday.status)" readonly>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link class="apax-btn full warning" :to="'/holidays'">
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
    name: 'Holiday-Detail',
    data() {
      return {
        holiday: {
          name: '',
          lms_proc_id: '',
          status: '',
          products:[],
        },
        products: [],
      }
    },
    created(){
      let uri = '/api/publicHolidays/'+this.$route.params.id;
			axios.get(uri).then((response) => {
        this.holiday = response.data;
        console.log(`this.holiday ${JSON.stringify(this.holiday)}`)
      });
      axios.get(`/api/products`).then(response => {
        this.products = response.data.data
      })
    },
    methods: {
       filterStatus(value){
        return value == 1? "Hoạt động": "Không hoạt động"
      }
    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
.form-check-left {
    float: left;
    display: block;
    width: 125px;
}
.form-check-label {
    margin-left: 15px;
}
</style>
