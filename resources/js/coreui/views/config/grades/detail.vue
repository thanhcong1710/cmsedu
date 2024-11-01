<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Thông Tin School Grade</strong>
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
                      <input class="form-control" type="text" readonly v-model="grade.name">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" :value="grade.status | getStatusToName" readonly>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
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
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'grade-School-Grade',
    data() {
      return {
        grade: {
          name: '',
          lms_proc_id: '',
          status: ''
        }
      }
    },
    created() {
      let uri = '/api/schoolGrades/' + this.$route.params.id;
      axios.get(uri).then((response) => {
        this.grade = response.data;
        console.log(`this.grade ${JSON.stringify(this.grade)}`)
      });
    },
    methods: {

    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
