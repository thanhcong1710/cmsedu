<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
        <loader :active="processing" :spin="spin" :text="text" :duration="duration"/>
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Mới Giáo Viên</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                  </div>
                  <div class="content-detail">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Trung tâm</label>
                          <select class="form-control" v-model="teacher.branch_id">
                            <option value="" disabled>Chọn trung tâm</option>
                            <option :value="branch.id" v-for="(branch,ind) in branches" :key="ind">{{branch.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Tên giáo viên</label>
                          <input class="form-control" v-model="teacher.ins_name" type="text">
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Trạng thái</label>
                          <select class="form-control" v-model="teacher.status">
                            <option value="" disabled>Chọn trạng thái</option>
                            <option value="0">Không hoạt động</option>
                            <option value="1">Hoạt động</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Loại giáo viên</label>
                          <select class="form-control" v-model="teacher.is_head_teacher">
                            <option value="0">Teacher</option>
                            <option value="1">Head Teacher</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Mã nhân sự</label>
                          <input class="form-control" v-model="teacher.hrm_id" type="text">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Email</label>
                          <input class="form-control" v-model="teacher.email" name="email" type="text" v-validate="'required|email'">
                           <span v-show="errors.has('email')" class="error-inform line">
                            <i v-show="errors.has('email')" class="fa fa-warning"></i>
                            <span v-show="errors.has('email')" class="error help is-danger">Email là bắt buộc</span>
                          </span>
                        </div>
                      </div>
                       <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Điện thoại</label>
                          <input class="form-control" v-model="teacher.phone" type="text">
                        </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-sm-12 col-sm-offset-3 text-right">
                          <button class="apax-btn full edit" type="submit" @click="addTeacher"><i class="fa fa-save"></i> Lưu</button>
                          <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                          <router-link class="apax-btn full warning" :to="'/teachers'">
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
import inp from '../../../components/Input'
import file from '../../../components/File'
import loader from "../../../components/Loading";

export default {
  name: 'Add-Teacher',
  components: {
     inp,
    file,
  },
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
        modal: () => this.exitModal(),
      },
      teacher: {
        ins_name: '',
        status: '',
        branch_id: '',
        type: 1,
        is_head_teacher:0,
        email:'',
        hrm_id:'',
        phone:''
      },
      branches: [],
      processing: false,
      spin: "mini",
      duration: 500,
      text: "Đang tải dữ liệu..."
    }
  },
  created(){
     u.a().get(`/api/reports/branches/`).then(response =>{
      this.branches = response.data;
     })
  },
  methods: {
    addTeacher(){
      const teacher = {
        branch_id: this.teacher.branch_id,
        ins_name: this.teacher.ins_name,
        status: this.teacher.status,
        type: 1,
        is_head_teacher:this.teacher.is_head_teacher,
        email:this.teacher.email,
        hrm_id: this.teacher.hrm_id,
        phone: this.teacher.phone
      }
      if(teacher.branch_id == ''){
        alert("Vui lòng chọn trung tâm !")
        return false
      }else if(teacher.ins_name == ''){
        alert("Vui lòng nhập tên giáo viên !")
        return false
      }else if(teacher.status === ''){
        alert("Vui lòng chọn trạng thái !")
        return false
      }else if(teacher.email == ''){
        alert("Vui lòng nhập email giáo viên !")
        return false
      }else if(teacher.phone == ''){
        alert("Vui lòng nhập điện thoại giáo viên !")
        return false
      }
      else {
        this.saveTeacher()    
      }
    },
    saveTeacher(){
      let uri = `/api/teachers/`
       this.processing = true;
      u.a().post(uri, this.teacher).then(response =>{
        // this.$router.push('/shifts')
        this.html.modal.message = "Thêm mới thành công giáo viên!"
        this.html.modal.display = true
        this.processing = false;
      })
    },
    exitModal(){
        this.$router.push('/teachers')
    },
    resetAll(){
      this.teacher.ins_name = ''
      this.teacher.status = ''
      this.teacher.branch_id = ''
      this.teacher.email = ''
    },

  }
}
</script>

<style scoped>
.card-body {
    padding: 15px;
}
.error-inform{
  color:red;
}
</style>