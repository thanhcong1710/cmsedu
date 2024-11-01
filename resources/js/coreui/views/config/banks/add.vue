<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thêm Ngân Hàng $</strong>
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
                      <label class="control-label">Tên Ngân Hàng <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" v-model="banks.name">
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Tên viết tắt <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" v-model="banks.alias">
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Logo <i>(định dạng .png hoặc .jpg)</i></label>
                      <input id="fileLogo" type="file" class="form-control" accept="image/*"  @change="uploadFile"> <br>
                      <div>
                        <img :src="this.previewImg" style="width:150px;" />
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Ghi chú</label>
                      <input type="text" class="form-control" v-model="banks.note">
                    </div>
                  </div>
                  
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="btn btn-success" type="submit" @click="addBanks">Lưu</button>
                    <button class="btn btn-default" type="reset" @click="resetAll">Hủy</button>
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
          note: '',
          logo: {}
        },
        previewImg: '',
      }
    },
    created(){  

    },
    methods: {
      addBanks() {
        if( this.banks.name != '' && this.banks.alias != '' ) {
          const fd = new FormData();
          fd.append('name',this.banks.name);
          fd.append('alias',this.banks.alias);
          fd.append('note',this.banks.note);
          fd.append('logo',this.banks.logo);
          
          u.a().post(`/api/banks`, fd).then(response => {
            alert('Thêm thành công!');
            this.$router.push('/banks')
          });
        } else {
          alert('Vui lòng nhập đầy đủ thông tin (Tên ngân hàng, Tên viết tắt)!');
        }
        
      },
      resetAll(){
        this.banks.name = '';
        this.banks.alias = '';
        this.banks.note = '';
        this.previewImg  = '';
        $('#fileLogo').val('');
      },

      uploadFile(e) {
          let images = e.target.files[0];
              this.banks.logo = images;
          let reader = new FileReader();
          reader.readAsDataURL(images);
          reader.onload = (e) => {
              this.previewImg = e.target.result;
          };
       }
    }
  }
</script>


