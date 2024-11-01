<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-id-card" /> <b class="uppercase">Thêm mới cộng tác viên</b>
            <div class="card-actions">
              <a
                href="skype:thanhcong1710?chat"
                target="_blank"
              >
                <small className="text-muted"><i class="fa fa-skype" /></small>
              </a>
            </div>
          </div>
          <div
            v-show="html.loading"
            class="ajax-load content-loading"
          >
            <div class="load-wrapper">
              <div class="loader" />
              <div
                class="loading-text cssload-loader"
              >
                {{ html.loading.content }}
              </div>
            </div>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6">
                        <text-component
                          v-model="form.code"
                          label="Mã cộng tác viên"
                          required
                          :disabled="html.config.edit"
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                          v-model="form.school"
                          label="Tên cộng tác viên"
                          required
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                                v-model="form.name"
                                label="Tên người đại diện"
                                :disabled="html.config.edit"
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                          v-model="form.address"
                          label="Địa chỉ"
                          :disabled="html.config.edit"
                          default-value=""
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                          v-model="form.phone"
                          label="Số điện thoại"
                          :disabled="html.config.edit"
                        />
                      </div>
                      <div class="col-md-6">
                        <select-component
                          label="Trạng thái"
                          v-model="form.status"
                          :options="statusOptions"
                          default-value="1"
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                          v-model="form.email"
                          label="Email"
                          :disabled="html.config.edit"
                          v-validate="'required|email'"
                        />
                      </div>
                      <div class="col-md-6">
                        <apaxbtn
                          :on-click="handleSave"
                          :disabled="!this.enableSaveButton"
                          :icon="html.config.save_icon"
                          :label="html.config.save_label"
                          :markup="html.config.save_markup"
                        />
                        <apaxbtn
                          :on-click="html.dom.action.cancel"
                          :disabled="html.dom.disabled.cancel"
                          :icon="html.config.cancel_icon"
                          :label="html.config.cancel_label"
                          :title="html.config.cancel_title"
                          :markup="html.config.cancel_markup"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>
<script>
import u from '../../../utilities/utility'
import TextComponent from '../../../components/form/elements/Text'
import Date from '../../../components/form/elements/Date'
import Number from '../../../components/form/elements/Number'
import apaxbtn from '../../../components/Button'
import SelectComponent from '../../../components/form/elements/Select'

export default {
  name      : 'AddDiscountCode',
  components: {
    TextComponent, Date, Number, apaxbtn, SelectComponent,
  },
  props: {},
  data () {
    const model         = u.m('discount-code').page
    u.set(model, {
      title: 'Tạo mới cộng tác viên',
      form : {
        code   : '',
        address: '',
        school : '',
        name   : '',
        phone  : null,
        email  : '',
        status : '1',
      },
    }, 0)
    model.html.dom      = {
      disabled: {
        save  : true,
        cancel: false,
      },
      action: {
        save  : this.handleSave,
        reset : this.resetForm,
        cancel: this.handleGoBack,
      },
    }
    model.html.config   = {
      style        : '',
      clear_date   : true,
      cancel_icon  : 'fa-sign-out',
      cancel_label : 'Thoát',
      cancel_markup: 'error',
      save_icon    : 'fa-save',
      save_label   : 'Lưu',
      save_markup  : 'success',
      reset_icon   : 'fa-recycle',
      reset_label  : 'Hủy',
      edit         : false,
    }
    model.calling       = false
    model.data          = {}
    model.html.loading  = false
    model.statusOptions = [
      {
        label   : 'Chọn trạng thái', disabled: true, value   : '',
      },
      {
        value: '1',
        label: 'Hoạt động',
      },
      {
        value: '0',
        label: 'Không hoạt động',
      },
    ]
    return model
  },
  watch   : {},
  computed: {
    discountPrice () {
      const { price, percent } = this.form
      return parseFloat((percent * price) / 100).toFixed(2)
    },
    enableSaveButton () {
      return this.validateForm()
    },
  },
  created () {
    if (this.$route.params.id) {
      this.html.config.edit = true
      this.getDiscountCode(this.$route.params.id)
    }
  },
  methods: {
    validateForm () {
      const { form } = this
      // eslint-disable-next-line camelcase
      const { name, code, address, phone, status, school } = form
      // eslint-disable-next-line camelcase
      return (code && school) || false
    },
    handleGoBack () {
      this.$router.go(-1)
    },
    handleSave () {
      if (!this.validate(this.form))
        return false

      this.html.loading = true
      const url         = this.$route.params.id ? `/api/collaborator/${this.$route.params.id}/edit` : '/api/collaborator/add'

      u.p(url, this.form)
        .then((response) => {
          if (response.error_code === 0)
            return this.$router.go(-1)
          else
            this.html.loading  = false
            this.$notify({
              group   : 'apax-atc',
              title   : 'Có lỗi xảy ra!',
              type    : 'danger',
              duration: 3000,
              text    : `<br/>-----------------------------------------------<br/>${response.message}`,
            })
        })
    },
    validate (params) {
      let message = ''
      let status  = true
      if (isNaN(_.get(params, 'phone'))) {
        message += `Số điện thoại không hợp lệ<br/>`
        status  = false
      }

      if (_.get(params, 'email') && !u.isEmailValid(_.get(params, 'email'))){
        message += `Email không đúng định dạng<br/>`
        status  = false
      }

      if (!status) {
        this.$notify({
          group   : 'apax-atc',
          title   : 'Có lỗi xảy ra!',
          type    : 'danger',
          duration: 3000,
          text    : `<br/>-----------------------------------------------<br/>${message}`,
        })
      }
      return status
    },
  },
}
</script>

<style scoped>
    .row {
        margin-top: 10px;
    }
</style>
