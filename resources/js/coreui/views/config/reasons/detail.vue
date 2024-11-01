<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Chi Tiết Thông Tin Lý Do</strong>
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
                      <label class="control-label">Loại lý do</label>
                      <input class="form-control" :value="regionType" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Mô tả</label>
                      <input class="form-control" :value="reason.description" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" :value="regionStatus" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer">
              <div class="row">
                <div class="col-sm-12 col-sm-offset-3 text-right">
                  <router-link class="apax-btn full warning" :to="'/reasons'">
                    <i class="fa fa-sign-out"></i> Quay lại
                  </router-link>
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
    name: 'Reason-Detail',
    data() {
      return {
        reason: {
          description: '',
          status: '',
          type: ''
        }
      }
    },
    created() {
      let uri = '/api/reasons/' + this.$route.params.id;
      axios.get(uri).then((response) => {
        this.reason = response.data;
        console.log(`this.reason ${JSON.stringify(this.reason)}`)
      });
    },
    methods: {

    },
    computed: {
      regionType(){
        return this.reason.type == 0? "Pending":"WithDraw"
      },
      regionStatus(){
        return this.reason.status == 1? "Hoạt động":"Không hoạt động"
      }
    }
  }
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
</style>
