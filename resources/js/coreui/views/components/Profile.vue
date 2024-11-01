<template>
  <div class="ada-form users-profile" :id="id" :class="custom_class">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-user"></i> <b class="uppercase">Đổi mật khẩu tài khoản người dùng {{ profile }}</b>
      </div>
      <div class="panel">
        <div class="row">
          <div class="col-md-4 mt-2">
            <label class="control-label tight bold"><b>Email:</b></label>
          </div>
          <div class="col-md-8 mb-2">
            <input v-model="email" class="form-control" type="text" readonly/>
          </div>
        </div>
        <div v-show="mine" class="row">
          <div class="col-md-4 mt-2">
            <label class="control-label tight bold"><b>Mật Khẩu Cũ:</b></label>
          </div>
          <div class="col-md-8 mb-2">
            <input v-model="password.old" class="form-control" type="password"/>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mt-2">
            <label class="control-label tight bold"><b>Mật Khẩu Mới:</b></label>
          </div>
          <div class="col-md-8 mb-2">
            <input v-model="password.new" class="form-control" type="password"/>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mt-2">
            <label class="control-label tight bold"><b>Xác Nhận Lại:</b></label>
          </div>
          <div class="col-md-8 mb-2">
            <input v-model="password.confirm" class="form-control" type="password"/>
          </div>
        </div><br/>
        <div class="row">
          <div class="col-md-4 mt-2">
            <abt
              :markup="'success'"
              :icon="'fa fa-save'"
              :customClass="'update-button'"
              label="Đổi Mật Khẩu"
              title="Đổi mật khẩu tài khoản người dùng!"
              :disabled="disabled.save"
              :onClick="saveProfile"
              >
            </abt>
          </div>
          <div class="col-md-8 mb-2">
            <div :class="`alert-message alert-${class_name}`" role="alert" v-show="message.length">
              <div v-html="message"></div>
            </div>
          </div>
        </div><br/>
      </div>
    </b-card>  
  </div>
</template>

<script>

import u from '../../utilities/utility'
import abt from './Button'

export default {
  name: 'bracnh-filter',
  data () {
    return {
      password: {
        old: '',
        new: '',
        confirm: ''
      },
      disabled: {
        save: false,
        cancel: false
      },
      message: '',
      role_name: '',
      class_name: ''
    }
  },
  props: {
    id: {
      type: String,
      default: null
    },
    custom_class: {
      type: String,
      default: null
    },
    mine: {
      type: Boolean,
      default: false
    },
    user: {
      type: String,
      default: null
    },
    email: {
      type: String,
      default: null
    },
    profile: {
      type: String,
      default: ''
    }
  },
  created () {
  },
  components: {
    abt
  },
  methods: {
    saveProfile() {
      this.class_name = ''
      this.message = ''
      let valid = true
      if (this.mine && this.password.old === '') {
        valid = false
        this.class_name = 'danger'
        this.message = 'Xin vui lòng nhập mật khẩu hiện tại để xác nhận thông tin bảo mật!'
      }
      if (this.email === '' || (this.email.indexOf('@') < 1 && this.email.indexOf('.') < 3)) {
        valid = false
        this.class_name = 'danger'
        this.message = 'Xin vui lòng nhập địa chỉ email sẽ nhận mật khẩu mới!'
      }
      if (this.password.new.length < 6) {
        valid = false
        this.class_name = 'danger'
        this.message = 'Mật khẩu mới phải dài trên 8 ký tự, xin vui lòng nhập lại!'
      }
      if (this.password.new === '' && this.password.confirm === '') {
        valid = false
        this.class_name = 'danger'
        this.message = 'Xin vui lòng nhập mật khẩu mới và xác nhận lại thông tin!'
      } else if (this.password.new !== this.password.confirm) {
        valid = false
        this.class_name = 'danger'
        this.message = 'Mật khẩu mới và thông tin xác nhận không khớp nhau, xin vui lòng kiểm tra lại!'
      }
      if (valid) {
        let really = true
        really = confirm('Bạn có thực sự muốn cập nhật lại thông tin tài khoản này?')
        if (really) {
          this.class_name = 'success'
          this.message = 'Đang xử lý yêu cầu, xin vui lòng đợi trong giây lát...'
          this.password.user = this.user
          this.password.mine = this.mine
          this.password.email = this.email
          u.p(`/api/users/update/account/password`, this.password).then(response => {
            if (response.success) {
              this.class_name = 'success'
              this.message = `Mật khẩu đã được cập nhật thành công!`
              alert(this.message)
            } else {
              this.class_name = 'danger'
              this.message = `Cập nhật thất bại! ${response.message}`
              alert(this.message)
            }
          })
        }
      } else {
        alert(this.message)
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .ada-form.users-profile i, .ada-form.users-profile b {
    color: #555!important
  }
  .ada-form.users-profile .row label.control-label {
    text-align: right;
    width: 100%;
    margin: 0;
  }
  .ada-form.users-profile .row input {
  }
  .ada-form .update-button {
    margin: 16px 0 0 0;
    float: right;
  }
  h5.modal-title {
    text-shadow:0 1px 1px #111;
  }
</style>
