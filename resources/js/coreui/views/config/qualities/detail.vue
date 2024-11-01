<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Sách</strong>
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
                      <label class="control-label">Tên đầu sách</label>
                      <input class="form-control" :value="book.name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Sản phẩm</label>
                      <select :value="book.product_id" class="form-control" disabled>
                        <option value="">Sản phẩm</option>
                        <option :value="product.id" v-for="(product, index) in products" :key="index">{{product.name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Từ ngày</label>
                      <input v-model="book.start_date" class="form-control" type="date" disabled>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Đến ngày</label>
                      <input v-model="book.end_date" class="form-control" type="date" disabled>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" :value="book.status|getStatusToName" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <div class="row">
                <div class="col-sm-12 col-sm-offset-3 text-right">
                  <router-link class="apax-btn full warning" :to="'/books'">
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
  import moment from 'moment'

  export default {
    name: 'Book-Detail',
    data() {
      return {
        book: {
          name: '',
          product_id: '',
          status: '',
          start_date:'',
          end_date:''
        },
        products: []
      }
    },
    created(){
      let uri = '/api/books/'+this.$route.params.id;
			axios.get(uri).then((response) => {
        this.book = response.data;
        this.book.start_date = moment(String(this.book.start_date)).format('Y-MM-DD');
        this.book.end_date = moment(String(this.book.end_date)).format('Y-MM-DD');
        
			});
      axios.get('/api/products').then(response =>{
        this.products = response.data.data
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
