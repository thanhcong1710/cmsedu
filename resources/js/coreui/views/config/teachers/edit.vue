<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật Thông Tin Giáo Viên IELTS</strong>
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
                          <select class="form-control" v-model="teacher.branch_id" >
                            <option value="" disabled>Chọn khu vực</option>
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
                          <button class="apax-btn full edit" type="submit" @click="updateTeacher"><i class="fa fa-save"></i> Lưu</button>
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
      </div> 
    </div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'
import inp from '../../../components/Input'
import file from '../../../components/File'

export default {
  name: 'Edit-Teacher',
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
          is_head_teacher:0,
          email:'',
          phone:'',
        },
        branches: []
    }
  },
  created(){
     u.a().get(`/api/reports/branches/`).then(response =>{
      this.branches = response.data;
     })
    let uri = `/api/teachers/`+this.$route.params.id+'/edit'
    u.a().get(uri).then(response =>{
      this.teacher = response.data;
    })
  },
  methods: {
    updateTeacher(){
      if(this.teacher.branch_id == ''){
        alert("Vui lòng chọn trung tâm !")
        return false
      }else if(this.teacher.ins_name == ''){
        alert("Vui lòng nhập tên giáo viên !")
        return false
      }else if(this.teacher.status === ''){
        alert("Vui lòng chọn trạng thái !")
        return false
      }else if(this.teacher.phone == ''){
        alert("Vui lòng nhập điện thoại giáo viên !")
        return false
      }else if(this.teacher.email === '' || !this.teacher.email){
          alert("Vui lòng nhập email giáo viên !")
          return false
        }else{
          let uri = `/api/teachers/`+this.$route.params.id;
          axios.put(uri, this.teacher).then((response) => {
            if(response.data.success == true) {
              alert(response.data.data);
              this.$router.push('/teachers')
            }else {
              alert(response.data.message);
            }
            
          })
          
        }
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