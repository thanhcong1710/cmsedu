<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-id-card" /> <b class="uppercase">Thêm nhân viên mới</b>
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
                        <branch-select-component
                                label="Trung tâm"
                                v-model="form.branch_id"
                                track-by="id"
                                :required="true"
                                :multiple="false"
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                                v-model="form.name"
                                label="Tên nhân viên"
                                required
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                                v-model="form.email"
                                label="Địa chỉ email"
                                :disabled="html.config.edit"
                                v-validate="'required|email'"
                                required
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                                v-model="form.hrm_id"
                                label="Mã nhân viên"
                                required
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                                v-model="form.accounting_id"
                                label="Mã Cyber (EC)"
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
                        <text-component
                                v-model="form.password"
                                label="Mật khẩu"
                                :disabled="html.config.edit"
                                default-value=""
                                input-type="password"
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
                        <select-component
                                label="Vị trí nhân viên"
                                v-model="form.role_id"
                                :options="roles"
                                @input="changeRole"
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                                label="Mã EC Leader (nếu có)"
                                v-model="form.superior_id"
                                :class="isEc"
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
                                :on-click="handleSaveNext"
                                :disabled="!this.enableSaveButton"
                                :icon="html.config.save_icon"
                                label="Lưu và nhập tiếp"
                                markup="warning"
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
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="handleGoBack" ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
          <div v-html="message">
          </div>
        </b-modal>
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal1" @ok="callback" ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
          <div v-html="message">
          </div>
        </b-modal>
      </div>
    </div>
  </div>
</template>
<script>
  import u from '../../utilities/utility'
  import TextComponent from '../../components/form/elements/Text'
  import Date from '../../components/form/elements/Date'
  import Number from '../../components/form/elements/Number'
  import apaxbtn from '../../components/Button'
  import SelectComponent from '../../components/form/elements/Select'
  import BranchSelectComponent from '../base/common/branch-select'

  export default {
    name      : 'AddDiscountCode',
    components: {
      TextComponent, Date, Number, apaxbtn, SelectComponent,BranchSelectComponent
    },
    props: {},
    data () {
      const model  = u.m('discount-code').page
      u.set(model, {
        title: 'Tạo mới cộng tác viên',
        form : {
          name   : '',
          password  : '',
          email  : '',
          phone  : '',
          accounting_id  : '',
          superior_id  : '',
          hrm_id  : '',
          status : '1',
          branch_id : '',
          role_id : '999999999',
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
        save_label   : 'Lưu và thoát',
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
          label: 'Đang làm việc',
        },
        {
          value: '0',
          label: 'Đã nghỉ việc',
        },
      ],
      model.roles = []
      model.isEc  = 'hidden'
      model.modal = false
      model.modal1 = false
      model.message = ''
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
        //this.getDiscountCode(this.$route.params.id)
      }
      let that = this
      u.a().get(`/api/all/get-roles`).then(response =>{
        that.roles = response.data
      })
    },
    methods: {
      callback(){
          this.form.name   = ''
          this.form.password  = ''
          this.form.email  = ''
          this.form.phone  = ''
          this.form.accounting_id  = ''
          this.form.superior_id  = ''
          this.form.hrm_id  = ''
      },
      changeRole(value){
        if (value == 68)
          this.isEc = 'block'
        else
          this.isEc = 'hidden'
      },
      validateForm () {
        const { form } = this
        // eslint-disable-next-line camelcase
        const { name, email, password, status,phone,hrm_id,accounting_id,superior_id} = form
        // eslint-disable-next-line camelcase
        return (name && email && hrm_id) || false
      },
      handleGoBack () {
        this.$router.go(-1)
      },
      handleSave () {
        if (!this.validate(this.form))
          return false

        this.html.loading = true
        const url         = this.$route.params.id ? `/api/user-add/${this.$route.params.id}/edit` : '/api/user-add/add'

        u.p(url, this.form)
                .then((response) => {
                  this.html.loading  = false
                  if (response.error_code == 0){
                    this.message = 'Thêm mới thành viên thành công'
                    this.modal = true
                  }
                  else {
                    this.$notify({
                      group   : 'apax-atc',
                      title   : 'Có lỗi xảy ra!',
                      type    : 'danger',
                      duration: 3000,
                      text    : `<br/>-----------------------------------------------<br/>${response.message}`,
                    })
                  }
                })
      },
      handleSaveNext () {
        if (!this.validate(this.form))
          return false

        this.html.loading = true
        const url         = this.$route.params.id ? `/api/user-add/${this.$route.params.id}/edit` : '/api/user-add/add'

        u.p(url, this.form)
                .then((response) => {
                  this.html.loading  = false
                  if (response.error_code == 0){
                    this.message = 'Thêm mới thành viên thành công'
                    this.modal1 = true
                  }
                  else {
                    this.$notify({
                      group   : 'apax-atc',
                      title   : 'Có lỗi xảy ra!',
                      type    : 'danger',
                      duration: 3000,
                      text    : `<br/>-----------------------------------------------<br/>${response.message}`,
                    })
                  }
                })
      },
      validate (params) {
        let message = ''
        let status  = true

        if (_.get(params, 'email') && !u.isEmailValid(_.get(params, 'email'))){
          message += `Email không đúng định dạng<br/>`
          status  = false
        }
        if (isNaN(_.get(params, 'phone'))) {
          message += `Số điện thoại không hợp lệ<br/>`
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
