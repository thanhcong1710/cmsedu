<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật Thông Tin Hạng Học Sinh</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                  </div>
                  <div class="content-detail">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Tên loại xếp hạng</label>
                          <input type="text" class="form-control" v-model="rank.name">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Mô tả loại xếp hạng</label>
                          <input type="text" class="form-control" v-model="rank.description">
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Đối tượng xếp hạng</label>
                          <select class="form-control" v-model="rank.type">
                            <option value="" disabled>Chọn đối tượng</option>
                            <option value="1">Học sinh</option>
                            <option value="0">Nhân viên</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Trạng thái</label>
                          <select class="form-control" v-model="rank.status">
                            <option value="" disabled>Chọn trạng thái</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-sm-12 col-sm-offset-3 text-right">
                          <button class="apax-btn full edit" type="submit" @click="updateRank"><i class="fa fa-save"></i> Lưu</button>
                          <router-link class="apax-btn full warning" :to="'/scores'">
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
  name: 'Edit-Score',
  data () {
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
      rank: {
        name: '',
        description: '',
        status: '',
        type: ''
      }
    }
  },
  methods: {
    reset(){

    },
    exitModal(){
      this.$router.push('/scores')
    },
    updateRank(){
      if(this.rank.name == ''){
        alert("Tên xếp hạng không để trống")
        return false
      }else if(this.rank.type == ''){
        alert("Đối tượng xếp hạng không để trống")
        return false
      }
      else {
        this.saveUpdateRank()
      }
    },
    saveUpdateRank(){
      u.a().put(`/api/ranks/`+this.$route.params.id, this.rank).then(response =>{
        // this.$router.push('/scores')
        this.html.modal.message = "Sửa thành công xếp hạng học sinh !"
        this.html.modal.display = true
      })
    }
  },
  created(){
    u.a().get(`/api/ranks/`+this.$route.params.id).then(response =>{
      this.rank = response.data;
    })
  }
}
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>