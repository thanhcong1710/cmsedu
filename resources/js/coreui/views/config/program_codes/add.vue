<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-clipboard"></i> <strong>Thêm Mới Mã Quy Chiếu</strong>
          </div>
          <div class="content-detail">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Mã</label>
                    <input class="form-control" v-model="programCode.code" type="text">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Mô tả</label>
                    <input class="form-control" v-model="programCode.description" type="text">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Sản phẩm</label>
                    <select name="" id="" v-model="programCode.product_id" class="form-control">
                      <option value="">Chọn sản phẩm</option>
                      <option :value="product.id" v-for="(product, index) in products" :key="index">{{product.name}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <select name="" id="" class="form-control" v-model="programCode.status">
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
                  <button class="apax-btn full edit" type="submit" @click="addProgramCode"><i class="fa fa-save"></i> Lưu</button>
                  <button class="apax-btn full default" type="reset" @click="resetInputValue"><i class="fa fa-ban"></i> Hủy</button>
                  <router-link class="apax-btn full warning" :to="'/program-codes'">
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
  name: 'Add-Program-Code',
  data() {
    return {
      action: {
        modal: () => this.exitModal()
      },
      html: {
        modal: {
          class: '',
          display: false,
          title: 'Thông báo',
          message: ''
        }
      },
      products: [],
      programCode: {
        code: '',
        description: '',
        status: 1,
        product_id: ''
      }
    }
  },
  created(){
    u.a().get(`/api/all/products`).then(response =>{
      this.products = response.data;
    })
  },
  methods: {
    addProgramCode(){
      const programCode = {
        code: this.programCode.code,
        description: this.programCode.description,
        status: this.programCode.status,
        product_id: this.programCode.product_id
      }
      if(programCode.code == '' ||programCode.status == ''){
        alert("Tên và khoảng trắng không được trống !")
      }else {
        this.storeProgramCode(programCode)
      }
    },
    storeProgramCode(programCode){
      u.a().post(`/api/programCodes`, programCode).then(response=>{
        let rs = response.data 
          if(rs){
            this.html.modal.message = "Thêm mới mã quy chiếu thành công"
            this.html.modal.display = true
          }
      })
    },
    resetInputValue(){
      this.programCode.status = ''
      this.programCode.code = ''
      this.programCode.description = ''
      this.programCode.product_id = ''
    },
    exitModal(){
      this.$router.push('/program-codes')
    },
  }
}
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
