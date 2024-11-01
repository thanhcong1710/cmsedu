<template>
	<b-modal size="lg" 
      ok-variant="success"
      title="Yêu cầu đổi mật khẩu" 
      class="modal-success" 
      v-model="update" 
      @ok="update = false" 
      @close="update = false">
      <div class="ada-form users-profile" id="update-profile">
        <b-card header-tag="header">
          <div slot="header">
            <i class="fa fa-user"></i> <b class="uppercase">{{this.message.title}}</b>
          </div>
          <div class="panel">
            <div class="row">
              <div class="col-md-9 mt-2">
                <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Mật khẩu cũ:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="old_password" class="form-control" type="password"/>
                  </div>
                </div>

              </div>
            </div>

            <div class="row">
              <div class="col-md-9 mt-2">
                <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Mật khẩu mới:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="password" class="form-control" type="password"/>
                  </div>
                </div>

              </div>
            </div>

            <div class="row">
              <div class="col-md-9 mt-2">
                <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Nhập lại mật khẩu:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="password_confirm" class="form-control" type="password"/>
                  </div>
                </div>

              </div>
            </div>

            <br/>
            <div class="row">
              <div class="col-md-3 mt-2">
                <abt
                  :markup="'success'"
                  :icon="'fa fa-save'"
                  :customClass="'update-button'"
                  label="Lưu"
                  title="Cập nhật thông tin tài khoản người dùng!"
                  :disabled=false
                  :onClick="changePassword"
                  >
                </abt>
              </div>
              <div class="col-md-9 mb-2">
                <div :class="`alert-message alert-${class_name}`" role="alert" v-show="message.length">
                  <div v-html="error_msg"></div>
                </div>
              </div>
            </div><br/>
          </div>
        </b-card>  
      </div>
    </b-modal>

</template>

<script>

import u from '../utilities/utility'
import a from '../utilities/authentication'
import cookies from 'vue-cookies'
import abt from '../components/Button'
import moment from 'moment'

export default {
	name: "AutoChangePassword",
	data() {
		return {
			update: false,
			class_name: 'danger',
			user_name: '',
			old_password: '',
			password_confirm : '',
			password: '',
			message: {
				title: '',
				length: false,
				error: '',
			},
			error_msg: '',
		}
		
	},
	components: {
		abt
	},
	created() {
		// this.checkChangePassword()
	},
	methods: {
		checkChangePassword() {
			
			var dataUser = localStorage.getItem('__uf');
			var obj = JSON.parse(dataUser);
			if( obj.first_change_password == 0 ) {
				this.message.title = "Đăng nhập lần đầu. Vui lòng đổi mật khẩu!";
				this.update = true;
			} else {
				var dateCompare = (60*60*24*30);
				var last_change_password = parseInt((new Date(obj.last_change_password_date).getTime() / 1000).toFixed(0));
				var currentTime = parseInt((new Date().getTime() / 1000).toFixed(0));
				if( last_change_password < ( currentTime - dateCompare ) ) {
					this.message.title = "Vui lòng đổi mật khẩu vì lý do bảo mật! ( yêu cầu sau mỗi 30 ngày )";
					this.update = true;
				}
			}
		},
		changePassword() {
			if( this.old_password != '' && this.password != '' && this.password_confirm != '' ) {
				if( this.password.length >= 6 ) {
					if( this.password == this.password_confirm ) {
						let url = `api/auth/auto_change_password`
						var data = {
							'old_password' : this.old_password,
							'password' : this.password,
							'password_confirm' : this.password_confirm
						};
						u.a().post(url,data).then(response => {
							if(response.data.success == true) {
								this.update = false;
								alert('Cập nhật mật khẩu thành công!');
								a.logout(this.$router)
							}else {
								this.message.length = true;
								this.error_msg = response.data.message;
							}
						});
					}else {
						this.message.length = true;
						this.error_msg = 'Mật khẩu không trùng khớp';
					}
				} else {
					this.message.length = true;
					this.error_msg = 'Mật khẩu phải lớn hơn 6 ký tự và không được trùng mật khẩu cũ';
				}
			} else {
				this.message.length = true;
				this.error_msg = 'Vui lòng nhập đầy đủ thông tin!';
			}
		},
	}
}

</script>
<style scoped lang="scss">
  #update-profile, #update-profile .card-header, #update-profile .card-body, #update-profile .card-body b, #update-profile .card-body input {
    color: #555;
  }
  .alert-message.alert-danger {
    margin: 0!important;
  }
  .modal-body.tool-bar {
    height:calc(100% - 60px);
  }
  .tool-input {
    display:none;
  }
  .tool-input.current {
    display:block;
  }
  ul.dropdown-menu {
    height: 230px!important;
  }
</style>