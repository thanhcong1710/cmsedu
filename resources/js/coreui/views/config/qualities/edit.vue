<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'
  import moment from 'moment'

  export default {
    name: 'Edit-Room',
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
        book: {
          name: '',
          product_id: '',
          status: '',
          start_date:'',
          end_date : ''
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
      resetAll(){

      },
      exitModal(){
          this.$router.push('/books')
      },
      updateBook(){
        const book = {
          name: this.book.name,
          product_id: this.book.product_id,
          status: this.book.status,
          start_date: this.book.start_date,
          end_date: this.book.end_date
        }
        if(book.name == ''){
          alert('Tên sách không để trống')
          // this.html.modal.display = true
          return false
        }else if(book.product_id == ''){
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
          this.saveUpdateBook()
        }
      },
      saveUpdateBook(){
        let uri = `/api/books/`+this.$route.params.id;
        axios.put(uri, this.book).then((response) => {
          // this.$router.push('/books')
          this.html.modal.display = true
          this.html.modal.message = "Sửa sách thành công"
        })
      }
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Chất Lượng Contact </strong>
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
                      <label class="control-label">Tiêu chí</label>
                      <input type="text" class="form-control" v-model="quality.title">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Điểm</label>
                      <input v-model="quality.score" class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng Thái</label>
                      <select v-model="quality.status" class="form-control" id="">
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
                    <button class="apax-btn full edit" type="submit" @click="addQuality"><i class="fa fa-save"></i> Lưu</button>
                    <router-link class="apax-btn full warning" :to="'/qualities'">
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
    name: 'Add-quality',
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
        quality: {
          title: '',
          score: '',
          status: '',
        }
      }
    },
    created(){ 
       let uri = '/api/qualities/'+this.$route.params.id;
        u.a().get(uri).then((response) => {
          this.quality = response.data;
          
        }); 
    },
    methods: {
      addQuality() {
        const quality = {
          title: this.quality.title,
          score: this.quality.score,
          status: this.quality.status,
        }
        if(quality.title == ''){
          alert('Tiêu chí không để trống')
          return false
        }else if(quality.score == ''){
          alert('Điểm không để trống')
          return false
        }else if(quality.status == ''){
          alert('Trạng thái không để trống')
          return false
        }
        else {
          this.saveQuality()
        }
      },
      saveQuality(){
        u.a().post(`/api/qualities/`+this.$route.params.id, this.quality).then(response => {
          this.html.modal.display = true
          this.html.modal.message = "Cập nhật chất lượng contact  thành công"
        });
      },
      exitModal(){
          this.$router.push('/qualities')
      },
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
