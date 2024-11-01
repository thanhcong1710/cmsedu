<template>
  <ul class="form-list">
    <li
      v-if="message"
      :class="class_message"
    >
      {{ message }}
    </li>
    <li class="form item">
      <div
        class="form"
        v-b-tooltip.hover
        title="Ảnh đại diện"
      >
        <file
          :label="'Tải lên ảnh đại diện'"
          :name="'upload_avatar'"
          :field="'avatar'"
          :type="'img'"
          :link="validFile(employee.avatar)"
          :on-change="uploadAvatar"
          :title="'Tải lên 1 file ảnh đại diện với định dạng ảnh là: jpg, jpeg, png, gif.'"
        />
      </div>
    </li>
    <li class="form item">
      <div
        class="form input"
        v-b-tooltip.hover
        title="Họ và tên nhân viên"
      >
        <i class="cui-bookmark" />
        <input
          type="text"
          v-model="employee.full_name"
          placeholder="Tên nhân viên"
        >
      </div>
    </li>
    <li class="form item">
      <div
        class="form input"
        v-b-tooltip.hover
        title="Số di động liên hệ"
      >
        <i class="cui-phone" />
        <input
          type="text"
          v-model="employee.phone"
          placeholder="Số điện thoại"
        >
      </div>
    </li>
    <li class="form item">
      <div
        class="form input"
        v-b-tooltip.hover
        title="Nhập vào mật khẩu hiện tại"
      >
        <i class="icon-key" />
        <input
          type="password"
          v-model="employee.old_password"
          placeholder="Mật khẩu cũ"
        >
      </div>
    </li>
    <li class="form item">
      <div
        class="form input"
        v-b-tooltip.hover
        title="Nhập vào mật khẩu mới trên 6 ký tự"
      >
        <i class="icon-lock-open" />
        <input
          type="password"
          v-model="employee.new_password"
          placeholder="Mật khẩu mới"
        >
      </div>
    </li>
    <li class="form item">
      <div
        class="form input"
        v-b-tooltip.hover
        title="Xác nhận lại mật khẩu mới"
      >
        <i class="icon-lock" />
        <input
          type="password"
          v-model="employee.confirm_password"
          placeholder="Xác nhận lại"
        >
      </div>
    </li>
    <li class="buttons item">
      <div class="form buttons">
        <button
          v-b-tooltip.hover
          title="Cập nhật thông tin tài khoản"
          @click="submit"
        >
          <i class="fa fa-save" />Cập nhật
        </button>
        <button
          v-b-tooltip.hover
          title="Khôi phục lại thông tin ban đầu"
          @click="reset"
        >
          <i class="fa fa-refresh" />Làm lại
        </button>
      </div>
    </li>
  </ul>
</template>

<script>
import file from '../components/File'
import u from '../../utilities/utility'
export default {
  name      : 'EmployeeProfile',
  components: { file },
  props     : ['user'],
  data () {
    return {
      employee: {
        full_name                 : _.get(this, 'user.name'),
        phone                     : _.get(this, 'user.phone'),
        avatar                    : _.get(this, 'user.avatar'),
        old_password              : '',
        new_password              : '',
        confirm_password          : '',
        action_self_update_profile: true,
      },
      message      : '',
      class_message: '',
    }
  },
  created () {
  },
  methods: {
    uploadAvatar (file) {
      this.employee.avatar = file
    },
    validFile (file) {
      let resp = file && (typeof file === 'string') ? file : ''
      if (file && typeof file === 'object')
        resp = `${file.type},${file.data}`

      return resp
    },
    validate () {
      if (!this.employee.full_name)
        return 'Họ tên không được để trống'

      if (this.employee.old_password || this.employee.new_password || this.employee.confirm_password) {
        if (!this.employee.old_password)
          return 'Xin vui lòng nhập mật khẩu hiện tại để xác nhận thông tin bảo mật!'

        if (!this.employee.new_password)
          return 'Xin vui lòng nhập mật khẩu mới!'

        if (this.employee.new_password < 6)
          return 'Mật khẩu mới phải dài trên 8 ký tự, xin vui lòng nhập lại!'

        if (!this.employee.confirm_password)
          return 'Xin vui lòng xác nhận lại mật khẩu!'
      }

      if (this.employee.new_password !== this.employee.confirm_password)
        return 'Mật khẩu mới và thông tin xác nhận không khớp nhau, xin vui lòng kiểm tra lại!'

      return ''
    },
    reset () {
      this.employee.full_name        = _.get(this, 'user.name')
      this.employee.phone            = _.get(this, 'user.phone')
      this.employee.avatar           = _.get(this, 'user.avatar')
      this.employee.old_password     = ''
      this.employee.new_password     = ''
      this.employee.confirm_password = ''
    },
    submit () {
      const validate = this.validate()
      if (validate) {
        this.message       = validate
        this.class_message = 'message'
        return
      }

      this.message       = 'Đang xử lý yêu cầu, xin vui lòng đợi trong giây lát...'
      this.class_message = 'loading'
      u.p(`/api/users/${this.user.id}/update-users-profile`, this.employee).then((response) => {
        if (response.success) {
          this.class_name = 'success'
          this.message    = `Cập nhật thành công!`
          alert(this.message)
        } else {
          this.class_name = 'danger'
          this.message    = `Cập nhật thất bại! ${response.message}`
          alert(this.message)
        }
      })
    },
  },
}
</script>

<style scoped>
  .message{
    color: red;
  }
  .loading{
    color: #000;
  }

</style>
