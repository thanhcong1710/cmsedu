<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thông Tin Ký Hiệu Thu Phí</strong>
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
                      <label class="control-label">Ký hiệu</label>
                      <input class="form-control" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Tên ký hiệu</label>
                      <input class="form-control" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Mã Gói phí</label>
                      <input class="form-control" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" readonly>
                    </div>
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
    name: 'TypeCharge-Detail',
    data() {
      return {
        typecharge: {
          name: '',
          lms_proc_id: '',
          status: ''
        }
      }
    },
    created() {
      let uri = '/api/type-charges/' + this.$route.params.id;
      axios.get(uri).then((response) => {
        this.typecharge = response.data;
        console.log(`this.typecharge ${JSON.stringify(this.typecharge)}`)
      });
    },
    methods: {

    }
  }
</script>

<style scoped>
</style>
