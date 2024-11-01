<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập nhật mẫu tin nhắn</strong>
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
                      <label class="control-label">Loại</label>
                      <select class="form-control" v-model="template.type">
                        <option value="0">SMS</option>
                        <option value="1">Email</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Tên</label>
                      <input type="text" class="form-control" v-model="template.title">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Nội dung tin nhắn</label>
                      <textarea class="form-control" v-model="template.content"></textarea>
                    </div>
                  </div>
                  
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng Thái</label>
                      <select v-model="template.status" class="form-control" id="">
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
                    <button class="apax-btn full edit" type="submit" @click="addTemplate"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="'/sms/template/list'">
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
    name: 'Add-template',
    components: {
    },
    data() {
      return {
        template: {
          type: 0,
          title: '',
          status: 1,
          content: '',
        },
        processing: false,
      }
    },
    created(){ 
      this.processing = true
         u.g(`/api/sms_template/${this.$route.params.id}`).then(data => {
            this.processing = false
            this.template = data
            console.log(data)
        }).catch(e => {
            this.processing = false
        }) 
    },
    methods: {
      addTemplate() {
        if(this.template.title == ''){
          alert('Tên mẫu tin nhắn không để trống')
          return false
        }else if(this.template.content == ''){
          alert('Nội dung mẫu tin nhắn không để trống')
          return false
        }
        u.p(`/api/sms/template/update/${this.$route.params.id}`, this.template).then(response => {
          alert('Cập nhật mẫu tin nhắn thành công')
          this.exitModal()
        });
      },
      exitModal(){
          this.$router.push('/sms/template/list')
      },
      resetAll(){
        localtion.reload();
      }
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
