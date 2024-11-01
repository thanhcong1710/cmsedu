<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Chi tiết - {{this.banks.name}}</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Tên Ngân Hàng</label>
                      <input type="text" class="form-control" v-model="banks.name" readonly>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Tên viết tắt</label>
                      <input type="text" class="form-control" v-model="banks.alias" readonly>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Logo</label><br>
                      <img :src="'/static/' + this.banks.logo" style="width:200px;" />
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Ghi chú</label>
                      <input type="text" class="form-control" v-model="banks.note" readonly>
                    </div>
                  </div>
                  
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link class="btn btn-sm btn-danger" :to="'/banks'">
                      Quay lại
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
  import file from '../../../components/File'

  export default {
    name: 'Add-book',
    data() {
      return {
        banks: {
          name: '',
          alias: '',
          logo: {}
        }
      }
    },
    created(){  
        let url = `/api/banks/` + this.$route.params.id;
        u.a().get(url).then(response => {
            this.banks = response.data.data;
            console.log(this.banks);
        });
    },
    methods: {

      resetAll(){
        this.banks.name = '';
        this.banks.alias = '';
        this.banks.note = '';
        $('#fileLogo').val('');
      },
      fileChange(e) {
        let images = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(images);
        reader.onload = (e) => {
            this.banks.logo = e.target.result;
        };
      },

      uploadFile(e) {
          this.banks.logo = e.target.files[0];
       }
    }
  }
</script>


