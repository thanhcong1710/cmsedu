<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Mới School Grade</strong>
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
                      <label class="control-label">Tên school grade</label>
                      <input class="form-control" v-model="grade.name" type="text">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Mô tả</label>
                      <input class="form-control" v-model="grade.description" type="text">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="grade.status">
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
                    <button class="apax-btn full edit" type="submit" @click="addGrade"><i class="fa fa-save"></i> Lưu</button>
                    <button class="apax-btn full default" type="reset" @click="resetAll"><i class="fa fa-ban"></i> Hủy</button>
                    <router-link class="apax-btn full warning" :to="{name: 'Danh Sách Các Hạng Học Sinh'}">
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
    name: 'Add-School-Grade',
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
            modal: () => this.exitModal()
        },
        grade: {
          name: '',
          description: '',
          status: ''
        }
      }
    },
    created() {
    },
    methods: {
      addGrade() {
        const grade = {
          name: this.grade.name,
          description: this.grade.description,
          status: this.grade.status
        }
        if(grade.name == ''){
          alert("Tên không để trống!")
          return false
        }else if(grade.status == ''){
          alert("Trạng thái không để trống!")
          return false
        }
        else {
          this.checkCreateGrade(grade.name);
        }
      },
      checkCreateGrade(name){
        u.a().get(`/api/grades/check-create-grade/${name}`).then(response => {
          let rs = response.data
          if(rs == 0){
            alert("Tên xếp hạng đã tồn tại")
            return false
          }else {
            this.saveGrade()
          }
        });
      },
      saveGrade(){
        axios.post(`/api/schoolGrades`, this.grade).then(response => {
          // this.$router.push('/grades')
          this.html.modal.message = "Thêm mới thành công kỳ xếp hạng nhân viên!"
          this.html.modal.display = true
        });
      },
      exitModal(){
        this.$router.push('/grades')
      },
      resetAll() {
        this.grade.name = ''
        this.grade.description = ''
        this.grade.status = ''
        
      }
    }
  }
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
</style>
