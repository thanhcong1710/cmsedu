<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Cập Nhật School Grade</strong>
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
                      <input class="form-control" type="text" v-model="grade.name">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="grade.status">
                        <option :value="1">Hoạt động</option>
                        <option :value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click="updateGrade"><i class="fa fa-save"></i> Lưu</button>
                    <router-link class="apax-btn full warning" :to="'/grades'">
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
    name: 'Edit-grade',
    data() {
      return {
        //Set default checked
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
        product: '0',
        status: '0',
        area: '0',

        grade: {
          lms_proc_id: '',
          name: '',
          status: ''
        },
        grades: []
      }
    },
    created() {

      let uri = '/api/schoolGrades/' + this.$route.params.id + '/edit';
      axios.get(uri).then((response) => {
        this.grade = response.data;
      })
    },
    methods: {
      updateGrade() {
        let uri = `/api/schoolGrades/` + this.$route.params.id;
        axios.put(uri, this.grade).then((response) => {
          // this.$router.push('/grades')
          this.html.modal.message = "Sửa thành công School Grade!"
          this.html.modal.display = true
        })
      },
      exitModal(){
        this.$router.push('/grades')
      },
      resetAll() {
        this.grade.name = ''
        this.grade.lms_proc_id = ''
        this.grade.price = ''
        this.grade.receivable = ''
      }
    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
