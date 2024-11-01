<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Chi Tiết Thông Số Thiết Lập Cấu Hình</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">

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
    name: 'Setting-Detail',
    data() {
      return {
        setting: {
          name: '',
          lms_proc_id: '',
          status: ''
        }
      }
    },
    created() {
      let uri = '/api/settings/' + this.$route.params.id;
      axios.get(uri).then((response) => {
        this.setting = response.data;
        console.log(`this.setting ${JSON.stringify(this.setting)}`)
      });
    },
    methods: {

    }
  }
</script>

<style scoped>
</style>
