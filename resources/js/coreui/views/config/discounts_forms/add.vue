<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-clipboard"></i> <strong>Thêm Mới Hình Thức Giảm Trừ</strong>
          </div>
          <div class="content-detail">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Tên giảm trừ</label>
                    <input class="form-control" v-model="discount.name" type="text">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Mô tả</label>
                    <input class="form-control" v-model="discount.description" type="text">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <select name="" v-model="discount.status" class="form-control">
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
                  <button class="apax-btn full edit" type="submit" @click="addDiscount"><i class="fa fa-save"></i> Lưu</button>
                  <button class="apax-btn full default" type="reset" @click="reset"><i class="fa fa-ban"></i> Hủy</button>
                  <router-link class="apax-btn full warning" :to="'/discounts-forms'">
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
  name: 'Add-Discount',
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
      discount: {
        name: '',
        description: '',
        status: ''
      }
    }
  },
  methods: {
    addDiscount(){
      const discount = {
        name: this.discount.name,
        description: this.discount.description,
        status: this.discount.status
      }
     if(discount.name == '' || discount.description == '' || discount.status == ''){
      alert("Tên, trạng thái và mô tả giảm trừ không được để trống")
      return false
     }else {
        this.saveDiscount()  
     }
    },
    saveDiscount(){
      u.a().post(`/api/discounts`, this.discount).then(response=>{
      // this.$router.push('/discounts-forms')
      this.html.modal.message = "Thêm mới thành công Hình thức giảm trừ!"
      this.html.modal.display = true
     })
    },
    exitModal(){
      this.$router.push('/discounts-forms')
    },
    reset(){
      this.discount.name = ''
      this.discount.description = ''
      this.discount.status = ''
    }
  }
}
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
