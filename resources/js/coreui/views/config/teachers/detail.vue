<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thông tin giáo viên</strong>
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
                <label class="control-label">Tên giáo viên</label>
                <input v-model="this.teacher.ins_name" readonly type="text" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Trung tâm làm việc</label>
                <input v-model="this.teacher.branch_name" readonly type="text" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select v-model="this.teacher.status" disabled class="form-control" id="">
                  <option :value="1">Hoạt động</option>
                  <option :value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Loại giáo viên</label>
                <select class="form-control" disabled v-model="teacher.is_head_teacher">
                  <option value="0">Teacher</option>
                  <option value="1">Head Teacher</option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Mã nhân sự</label>
                <input v-model="teacher.hrm_id" 
                      type="text" class="form-control" disabled placeholder="Mã nhân sự">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Email</label>
                <input id="tEmail"
                      v-model="teacher.email" type="email" 
                      :readonly="( html.show_email == true )"
                      class="form-control" placeholder="Email">
                      <!-- :readonly="( html.show_email == true )" -->
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Điện thoại</label>
                <input class="form-control" v-model="teacher.phone" type="text" disabled>
              </div>
            </div>
          </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link class="apax-btn full warning" :to="'/teachers'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
          <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="callback" ok-variant="primary">
            <div v-html="message">
            </div>
          </b-modal>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'teacher-Detail',
    data() {
      return {
        teacher: {
          ins_name: '',
          status: '',
          branch_name: '',
          is_head_teacher: '',
          branch_id: '',
          hrm_id: '',
          email: '',
          phone: '',
        },
        loading: {
          content: 'Đang tải dữ liệu...',
        },
        actions: {
          
        },
        html: {
          show_email: false
        },
        error: 0,
        modal:false,
        message: ''
      }
    },
    created(){
      let uri = '/api/teachers/'+this.$route.params.id;
			axios.get(uri).then((response) => {
        this.teacher = response.data[0];
        if( this.teacher.email !== null ) {
          this.html.show_email = true;
        }
        if(this.teacher.is_head_teacher == null) {
          this.teacher.is_head_teacher = 0;
        }
        console.log(this.teacher);
			});
    },
    methods: {

      callback() {
        this.modal = false
      },

      validEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
      },

      savex(){

        let validated = true
        let alert_msg = ''
        if (this.teacher.email == null) {
          validated = false
          alert_msg+= '(*) Trường Email là bắt buộc<br/>'
        }
        if (this.teacher.email != null && this.validEmail(this.teacher.email) == false) {
          validated = false
          alert_msg+= '(*) Định dạng email không hợp lệ<br/>'
        }

        if (!validated) {
          alert_msg = `Dữ liệu Teacher chưa hợp lệ:<br/>-----------------------------------<br/><br/><p class="text-danger">${alert_msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          this.message = alert_msg
          this.modal = true
        } else {
          var term = new Object();
          term.branch_id = this.teacher.branch_id;
          term.teacher_id = this.teacher.id;
          term.is_head_teacher = this.teacher.is_head_teacher;
          term.status = this.teacher.status;
          term.email  = this.teacher.email;
          const cf = confirm("Bạn có chắc chắn sửa thông tin không? ")
          if(cf){
             axios.put(`/api/termTeacherBranches/${this.teacher.term_id}`, term).then(response =>{
            if( this.teacher.email !== null ) {
              this.html.show_email = true;
              if(response.data.success == true) {
                alert(response.data.data.message);
                this.$router.push('/teachers')
              }else {
                alert(response.data.message);
              }
              
            }
              console.log(response.data);
            })
          }
        }
      }
    }

  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
