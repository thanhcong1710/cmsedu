<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Công cụ chuyển trung tâm</strong>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Nhập mã học viên ( vd: HV-012xx )</label>
                      <input class="form-control" v-model="student.accounting_id" type="text">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trung tâm chuyển</label>
                      <input class="form-control" v-model="student.from_branch_id" type="text">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trung tâm nhận</label>
                      <input class="form-control" v-model="student.to_branch_id" type="text">
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="save"><i class="fa fa-save"></i> Lưu</button>
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
        student: {
          accounting_id: '',
          from_branch_id: '',
          to_branch_id: ''
        }
      }
    },
    methods: {
      save(){
        const data = {
          accounting_id: this.student.accounting_id,
          from_branch_id: this.student.from_branch_id,
          to_branch_id: this.student.to_branch_id
        }
        if(data.accounting_id == '' || data.from_branch_id == '' || data.to_branch_id == ''){
          alert("Mã học viên, Trung tâm nhận, Trung tâm chuyển không được để trống.")
          return false
        }else {
          this.saveData()
        }
      },
      saveData(){
        u.a().post(`/api/tool/branch_transfer`, this.student).then(response=>{
          // this.$router.push('/discounts-forms')
          //this.html.modal.message = "Thêm mới thành công Hình thức giảm trừ!"
          //this.html.modal.display = true
        })
      },
      exitModal(){
        console.log("DSAJKDJKSAKDASJDJKSAJKD")
        this.$router.go()
        //this.$router.push('/discounts-forms')
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