<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thêm mới vùng</strong>
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
                      <label class="control-label">Mã vùng</label>
                      <input class="form-control" v-model="region.hrm_id" type="text">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Tên vùng</label>
                      <input type="text" v-model="region.name" class="form-control" >
                    </div>
                  </div>
                   <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Email</label>
                      <input class="form-control" v-model="region.email" name="email" type="text" v-validate="'required|email'">
                      <span v-show="errors.has('email')" class="error-inform line">
                        <i v-show="errors.has('email')" class="fa fa-warning"></i>
                        <span v-show="errors.has('email')" class="error help is-danger">Email không đúng định dạng</span>
                      </span>
                    </div>
                   </div> 
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select v-model="region.status" id="" class="form-control">
                        <option value="" disabled>Chọn trạng thái</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Không Hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="addRegion"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" @click="resetInputValue"><i class="fa fa-ban"></i> Hủy</button>
                    <button class="apax-btn full warning" type="reset" @click="back"><i class="fa fa-sign-out"></i> Quay lại</button>
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
  import inp from '../../../components/Input'
  import file from '../../../components/File'

  export default {
    name: 'Edit-Region',
    components: {
      inp,
      file,
    },
    data() {
      return {
        action: {
          modal: () => this.exitModal()
        },
        html: {
          modal: {
            title: 'Thông báo',
            message: '',
            class: 'modal-success',
            display: false
          }
        },
        region: {
          hrm_id: '',
          name: '',
          status: '',
          email:'',
        }
      }
    },
    created(){

    },
    methods: {
      addRegion(){
        const region = {
          name: this.region.name,
          hrm_id: this.region.hrm_id,
          status: this.region.status,
          email: this.region.email,
        }
        if(region.name == '' || region.hrm_id == '' || region.status === ''){
          alert("Tên vùng, mã vùng, trạng thái, email giám đốc vùng không được để trống")
          return false
        }else {
          this.storeRegion(region);
        }
      },
      storeRegion(region){
        let uri = `/api/regions/`
        u.a().post(uri, region).then(response =>{
          let rs = response.data 
          if(rs){
            this.html.modal.message = "Thêm mới vùng thành công"
            this.html.modal.display = true
          }else{
            alert('Email giám đốc vùng không tồn tại trên hệ thống')
          }
        })
      },
      exitModal(){
        this.$router.push('/regions')
      },
      resetInputValue(){
        this.region.name = ''
        this.region.status = ''
        this.region.hrm_id = ''
      },
      back() {
        this.$router.push('/regions')
      }
    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
