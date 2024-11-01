<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm mới Semester</strong>
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
                <select class="form-control" v-model="semester.product_id">
                  <option value="" disabled>Chọn sản phẩm</option>
                  <option :value="product.id" v-for="(product,index) in products" :key="index">{{product.name}}</option>
                </select>
              </div>
            </div> 
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Tên kỳ học</label>
                <input type="text" v-model="semester.name" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Thời gian bắt đầu</label>
                <input type="date" v-model="semester.start_date" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Thời gian kết thúc</label>
                <input type="date" v-model="semester.end_date" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select v-model="semester.status" class="form-control" id="">
                  <option value="" disabled>Chọn trạng thái</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="checksemester"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="{name: 'Danh Sách Các Kỳ Học'}">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
            <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="action" ok-variant="primary">
            <div v-html="message">
            </div>
          </b-modal>
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
    name: 'Add-Semester',
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
            modal: () => this.exitModal()
        },
        semester: {
          name: '',
          start_date: '',
          end_date: '',
          status: '',
          product_id:'',
        },
        message: "",
        modal: false,
        lms_exist: '',
        products:[],
      }
    },
    created(){
      u.a().get('/api/all/products/').then((response)=>{
        this.products = response.data;
      })
    },
    methods: {
      checksemester(){
        let msg = ""
        let validate = true
        if (this.semester.product_id == ''){
          validate = false
          msg += "(*) Sản phẩm là bắt buộc! <br/>"
        }
        if (this.semester.name == ''){
          validate = false
          msg += "(*) Tên kỳ học là bắt buộc! <br/>"
        }
        if (this.semester.start_date == ''){
          validate = false
          msg += "(*) Thời gian bắt đầu kỳ học là bắt buộc! <br/>"
        }
        if (this.semester.end_date == ''){
          validate = false
          msg += "(*) Thời gian kết thúc kỳ học là bắt buộc! <br/>"
        }
        if (this.semester.start_date > this.semester.end_date){
          validate = false
          msg += "(*) Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc <br/>"
        }
        if (this.semester.status == ''){
          validate = false
          msg += "(*) Trạng thái kỳ học là bắt buộc! <br/>"
        }

        if (!validate){
          msg = `Dữ liệu kỳ học chưa hợp lệ <br/>-----------------------------------<br/><br/><p class="text-danger">${msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          this.message = msg
          this.modal = true
        }
        else {
          this.addSemester()
        }
      },
      addSemester() {
        // console.log(this.semester);
        const data = {
          lms: this.semester.sem_id
        }
        this.storeSemester()
      },
      storeSemester(){
        axios.post(`/api/semesters/`, this.semester).then(response => {
          // this.$router.push('/semesters')
          this.html.modal.message = "Thêm mới kỳ học thành công!"
          this.html.modal.display = true
        });
      },
      exitModal(){
        this.$router.push('/semesters')
      },
      resetAll(){
        this.semester.name = ''
        this.semester.product_id = ''
        this.semester.start_date = ''
        this.semester.end_date = ''
        this.semester.status = ''
      }
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
