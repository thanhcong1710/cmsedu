<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-id-card" /> <b class="uppercase">Thêm mới mã chiết khấu</b>
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
                          label="Mã chiết khấu"
                          required
                          :disabled="html.config.edit"
                        />
                      </div>
                      <div class="col-md-6">
                        <text-component
                          v-model="form.name"
                          label="Tên chiết khấu"
                          required
                        />
                      </div>
                      <div class="col-md-3">
                        <number
                          v-model="form.percent"
                          label="Tỷ lệ chiết khấu "
                          max="100"
                          min="0"
                          :disabled="html.config.edit"
                          default-value="0"
                          required
                        />
                      </div>
                      <div class="col-md-3">
                        <number
                          v-model="form.price"
                          label="Giá gốc gói phí"
                          :disabled="html.config.edit"
                          default-value="0"
                          required
                        />
                      </div>
                      <div class="col-md-3">
                        <number
                          v-model="form.bonus_sessions"
                          label="Buổi học bổng"
                          default-value="0"
                        />
                      </div>
                      <div class="col-md-3">
                        <number
                          v-model="form.bonus_amount"
                          label="Số tiền ứng với học bổng"
                          default-value="0"
                        />
                      </div>
                      <div class="col-md-6">
                        <number
                          v-model="discountPrice"
                          label="Tiền chiết khấu"
                          default-value="0"
                          disabled
                        />
                      </div>
                      <div class="col-md-6">
                        <select-component
                          label="Trạng thái"
                          v-model="form.status"
                          :options="statusOptions"
                          required
                          default-value="0"
                        />
                      </div>
                      <div class="col-md-6">
                        <date
                          v-model="form.start_date"
                          label="Ngày bắt đầu"
                          required
                          :disabled="html.config.edit"
                        />
                      </div>
                      <div class="col-md-6">
                        <date
                          v-model="form.end_date"
                          label="Ngày kết thúc"
                          required
                        />
                      </div>
                      <div class="col-md-6">
                        <label v-b-tooltip.hover
                               title="Chọn gói phí áp dụng"
                               class="control-label">Chọn gói phí áp dụng</label>
                        <!--<tuition-fee-select v-model="form.number_of_months" />-->
                        <div class="form-group">
                          <template v-for="(fee, index) in fee_ids_list">
                            <br/>
                            <div v-if="index == 'ucrea'">
                              <template v-for="(ucrea, u) in fee">
                                <input type="checkbox" :id="u" :value="ucrea.id" v-model="fee_ids_temp">
                                <label>{{ucrea.name}} </label>
                                <br/>
                              </template>
                            </div>
                            <div v-if="index == 'bright'">
                              <template v-for="(bright, b) in fee">
                                <input type="checkbox" :id="b" :value="bright.id" v-model="fee_ids_temp">
                                <label>{{bright.name}} </label>
                                <br/>
                              </template>
                            </div>
                            <div v-if="index == 'black'">
                              <template v-for="(black, bl) in fee">
                                <input type="checkbox" :id="bl" :value="black.id" v-model="fee_ids_temp">
                                <label>{{black.name}} </label>
                                <br/>
                              </template>
                            </div>
                            <div v-if="index == 'summer'">
                              <template v-for="(summer, sm) in fee">
                                <input type="checkbox" :id="sm" :value="summer.id" v-model="fee_ids_temp">
                                <label>{{summer.name}} </label>
                                <br/>
                              </template>
                            </div>
                            <div v-if="index == 'tth'">
                              <template v-for="(tth, th) in fee">
                                <input type="checkbox" :id="th" :value="tth.id" v-model="fee_ids_temp">
                                <label>{{tth.name}} </label>
                                <br/>
                              </template>
                            </div>
                             <div v-if="index == 'accelium'">
                              <template v-for="(accelium, acc) in fee">
                                <input type="checkbox" :id="acc" :value="accelium.id" v-model="fee_ids_temp">
                                <label>{{accelium.name}} </label>
                                <br/>
                              </template>
                            </div>
                          </template>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label v-b-tooltip.hover
                               title="Miền áp dụng"
                               class="control-label">Miền áp dụng</label>
                        <select v-model="form.zone_id" class="form-control" id="">
                          <option value="1" selected>Miền Bắc</option>
                          <option value="2">Miền Nam</option>
                          <option value="0">Tất cả</option>
                        </select>
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
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="handleForm" ok-variant="primary">
          <div v-html="message">
          </div>
        </b-modal>
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
import TuitionFeeSelect from '../../base/common/tuition-fee-select'

export default {
  name      : 'AddDiscountCode',
  components: {
    TextComponent, Date, Number, apaxbtn, SelectComponent,TuitionFeeSelect
  },
  props: {},
  data () {
    const model         = u.m('discount-code').page
    u.set(model, {
      title: 'Tạo mới mã chiết khấu',
      form : {
        name      : '',
        code      : '',
        percent   : 10,
        start_date: '',
        end_date  : '',
        price     : '',
        status    : 0,
        zone_id    : 1,
        fee_ids    : [],
        bonus_sessions:0,
        bonus_amount:0,
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
      { label: 'Chọn trạng thái', disabled: true },
      {
        value: 1,
        label: 'Hoạt động',
      },
      {
        value: 0,
        label: 'Không hoạt động',
      },
    ]
    model.fee_ids_list = []
    model.fee_ids_temp = []
    model.message = ""
    model.modal = false
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
    this.getTuitionFeeList()
    if (this.$route.params.id) {
      this.html.config.edit = true
      this.getDiscountCode(this.$route.params.id)
    }
  },
  methods: {
    validateForm () {
      const { form } = this
      // eslint-disable-next-line camelcase
      const { name, code, start_date, end_date, percent, price } = form
      // eslint-disable-next-line camelcase
      if (this.$route.params.id)
        return (name && code && start_date && end_date && price) || false
      return (name && code && start_date && end_date && percent && price) || false
    },
    handleGoBack () {
      this.$router.push('/discount-code')
    },
    handleForm () {
      this.html.loading = false
    },
    handleSave () {
      this.form.fee_ids = this.fee_ids_temp
      this.html.loading = true
      const url         = this.$route.params.id ? `/api/discount-codes/${this.$route.params.id}/edit` : '/api/discount-codes/add'
      // u.p(url, this.form).then(this.handleGoBack).catch((e) => {
      //
      // })
      u.p(url, this.form)
              .then(response => {
                if (response.code == 23000){
                  this.modal = true
                  if (this.$route.params.id){
                      this.message = 'Lỗi !Không thể cập nhật.'
                  }
                  else{
                      this.message = 'Lỗi !Mã chiết khấu đã tồn tại trong hệ thống.'
                  }
                }
                else
                  this.$router.push('/discount-code')
              })
    },
    getTuitionFeeList () {
      const uri = `/api/tuition-fee/list-name`
      u.g(uri).then((response) => {
        this.fee_ids_list = response.data
      })
    },
    getDiscountCode (id) {
      const uri = `/api/discount-codes/${id}`
      u.g(uri).then((response) => {
        this.form = response.data
        this.fee_ids_temp = this.form.fee_ids.split(',')
      })
    },
  },
}
</script>

<style scoped>
    .action {
        margin-top: 20px;
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        padding-right: 14px;
    }

    .row {
        margin-top: 10px;
    }
</style>
