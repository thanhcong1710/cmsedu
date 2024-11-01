<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Thông Tin Khu Vực</strong>
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
                      <label class="control-label">Mã Khu Vực</label>
                      <input class="form-control" type="text" v-model="zone.id" readonly >
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Tên Khu Vực </label>
                      <input class="form-control" type="text" v-model="zone.name" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" :value="zone.status |getStatusToName" readonly>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link class="apax-btn full warning" :to="'/zones'">
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
    name: 'Zone-Detail',
    data() {
      return {
        zone: {
          name: '',
          lms_proc_id: '',
          status: ''
        }
      }
    },
    created() {
      let uri = '/api/zones/' + this.$route.params.id;
      axios.get(uri).then((response) => {
        this.zone = response.data;
        console.log(`this.zone ${JSON.stringify(this.zone)}`)
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
