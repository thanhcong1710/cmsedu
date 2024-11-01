<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-clipboard"></i> <strong>Cập Nhật Mã Quy Chiếu</strong>
          </div>
          <div class="content-detail">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Mã</label>
                    <input  v-model="programCode.code" class="form-control" type="text">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Mô tả</label>
                    <input v-model="programCode.description" class="form-control" type="text">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Sản phẩm</label>
                    <select name="" id="" v-model="programCode.product_id" class="form-control">
                      <option value="0">Lựa chọn sản phẩm</option>
                      <option :value="product.id" v-for="(product, index) in products" :key="index">{{product.name}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <select name="" id="" class="form-control" v-model="programCode.status">
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
                  <button class="apax-btn full edit" type="submit" @click="updateProgramCode"><i class="fa fa-save"></i> Lưu</button>
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
  name: 'Edit-Program-Code',
  data() {
    return {
      action: {
        modal: () => this.exitModal()
      },
      html: {
        modal: {
          message: '',
          display:  false,
          title: 'Thông báo',
          class: 'modal-success'
        }
      },
      products: [],
      programCode: {
        code: '',
        description: '',
        status: '',
        product_id: ''
      }
    }
  },
  created(){
    u.a().get(`/api/all/products/`).then(response =>{
      this.products = response.data;
    })
    u.a().get(`/api/programCodes/${this.$route.params.id}`).then(response=>{
      this.programCode = response.data;
    })
  },
  methods: {
    updateProgramCode(){
      u.a().put(`/api/programCodes/${this.$route.params.id}`, this.programCode).then(response =>{
        this.html.modal.message = "Cập nhật mã quy chiếu thành công"
        this.html.modal.display = true
      })
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
