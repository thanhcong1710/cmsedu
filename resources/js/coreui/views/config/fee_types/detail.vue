<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thông tin loại thu phí</strong>
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
                      <label class="control-label">Tên loại thu phí</label>
                      <input class="form-control" type="text" readonly v-model="feetype.name">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Gói phí áp dụng</label>
                      <input class="form-control" type="text" readonly v-model="feetype.lms_proc_id" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Mã LMS sản phẩm</label>
                      <input class="form-control" type="text" readonly v-model="feetype.lms_proc_id" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Danh mục cha</label>
                      <input class="form-control" type="text" readonly v-model="feetype.lms_proc_id" readonly>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <input class="form-control" type="text" readonly v-model="feetype.lms_proc_id" readonly>
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
    name: 'Fee-Type-Detail',
    data() {
      return {
        feetype: {
          name: '',
          lms_proc_id: '',
          status: ''
        }
      }
    },
    created(){
      let uri = '/api/feetypes/'+this.$route.params.id;
			axios.get(uri).then((response) => {
				this.feetype = response.data;
				console.log(`this.feetype ${JSON.stringify(this.feetype)}`)
			});
    },
    methods: {

    }
  }
</script>

<style scoped>
</style>
