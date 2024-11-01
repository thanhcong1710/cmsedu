<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm sách</strong>
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
                      <label class="control-label">Tên sách</label>
                      <input type="text" class="form-control" v-model="book.name">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Mã sách</label>
                      <input type="text" class="form-control" v-model="book.book_id">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Năm sinh áp dụng</label>
                      <datePickerComponent
                              style="width:100%;"
                              placeholder="Chọn năm"
                              v-model="book.year_apply"
                              type="year"
                              format="YYYY"
                              track-by="YYYY"
                              />
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Sản phẩm</label>
                      <select class="form-control" v-model="book.product_id">
                        <option value="">Chọn sản phẩm</option>
                        <option :value="product.id" v-for="(product, index) in products">{{product.name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Từ ngày</label>
                      <input v-model="book.start_date" class="form-control" type="date">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Đến ngày</label>
                      <input v-model="book.end_date" class="form-control" type="date">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng Thái</label>
                      <select v-model="book.status" class="form-control" id="">
                        <option value="" disabled>Chọn trạng thái</option>
                        <option selected value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="addBook"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="'/books'">
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
  import DatePickerComponent from "../../reports/common/DatePicker.vue"
  export default {
    name: 'Add-book',
    components: {
      DatePickerComponent,
    },
    data() {
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
        products: [],
        book: {
          name: '',
          product_id: '',
          status: '',
          start_date: '',
          end_date: '',
          book_id: '',
          year_apply: '',
        }
      }
    },
    created(){  
     u.a().get(`/api/products/`).then(response =>{
      this.products = response.data.data;
     })
    },
    methods: {
      addBook() {
        const book = {
          name: this.book.name,
          product_id: this.book.product_id,
          status: this.book.status,
          start_date: this.book.start_date,
          end_date: this.book.end_date,
          book_id: this.book.book_id,
          year_apply: this.book.year_apply,
        }
        if(book.name == ''){
          alert('Tên sách không để trống')
          // this.html.modal.display = true
          return false
        }else if(book.book_id == ''){
          alert('Mã sách không để trống')
          return false
        }else if(book.year_apply == ''){
          alert('Năm áp dụng không để trống')
          return false
        }
        else if(book.product_id == ''){
          alert('Sản phẩm không để trống')
          return false
        }else if(book.status == ''){
          alert('Trạng thái không để trống')
          return false
        }else if(book.start_date == ''){
          alert('Ngày bắt đầu không để trống')
          return false
        }else if(book.end_date == ''){
          alert('Ngày kết thúc không để trống')
          return false
        }else if(book.start_date >= book.end_date){
          alert('Ngày bắt đầu phải lớn hơn ngày kết thúc')
          return false
        }
        else {
          this.checkExistBook()
        }
      },
      checkExistBook(){
        const book = {
          name: this.book.name
        }
        u.a().get(`/api/books/${book.name}/check-exist-book`).then(response => {
          const rs = response.data 

          if(rs == 1){
            alert("Tên sách đã tồn tại")
            return false
          }else {
            this.saveBook()
          }
        })
      },
      saveBook(){
        u.a().post(`/api/books`, this.book).then(response => {
          // this.$router.push('/books')
          this.html.modal.display = true
          this.html.modal.message = "Thêm mới sách thành công"
        });
      },
      exitModal(){
          this.$router.push('/books')
      },
      resetAll(){
        this.book.name = ''
        this.book.status = ''
        this.book.product_id = ''
        this.book.start_date = ''
        this.book.end_date = ''
        this.book.year_apply = ''
        this.book.book_id = ''
      }
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
