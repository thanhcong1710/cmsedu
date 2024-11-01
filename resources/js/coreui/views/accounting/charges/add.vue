<template>
  <div class="animated fadeIn apax-form" id="charg-add" :class="html.class.ready" @click="checkingOnClick">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i> <b class="uppercase">Thông tin học sinh </b>
          </div>
          <div v-show="loading1" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="loading1" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ trong chốc lát...</div>
            </div>
          </div>
          <div id="student-information" class="filter content">
            <div class="col-12 pad-no" :class="html.class.display.search.branch">
              <label class="control-label">Chọn trung tâm cần thao tác dữ liệu</label>
              <searchBranch
                :onSelectBranch="selectBranch">
              </searchBranch>
            </div>
            <div class="col-12 pad-no" :class="html.class.display.search.student">
              <input type="hidden" v-model="cache.branch" />
              <label class="control-label">Tìm kiếm học sinh theo mã LMS hoặc Tên</label>
              <searchStudent
                :suggestStudents="findStudents"
                :onSelectStudent="selectStudent">
              </searchStudent><br/>
            </div>
            <div class="col-12 pad-no content-detail">
              <div class="row">
                <div class="col-md-4 col-sm-6">
                  <label class="control-label">Tên Học Sinh</label>
                  <div class="input-group mar-btm">
                    <input class="form-control" type="text" readonly v-model="item.student_name">
                  </div>
                </div>
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Mã CMS</label>
                    <input class="form-control" type="text" readonly :value="item.student_crm_id">
                  </div>
                </div>
                <div class="col-md-4 col-sm-6">
                  <label class="control-label">Số Điện Thoại</label>
                  <div class="input-group mar-btm">
                    <input class="form-control" type="text" readonly v-model="item.student_phone">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Tên Phụ Huynh</label>
                    <input class="form-control" type="text" readonly :value="item.parent_name">
                  </div>
                </div>
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Số Di Động</label>
                    <input class="form-control" type="text" readonly :value="item.parent_mobile">
                  </div>
                </div>
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input class="form-control" type="text" readonly :value="item.student_email">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Trung tâm</label>
                    <input class="form-control" type="text" readonly :value="item.branch_name">
                  </div>
                </div>
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">EC</label>
                    <input class="form-control" type="text" readonly :value="item.ec_name">
                  </div>
                </div>
                <div class="col-md-4 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">EC Leader</label>
                    <input class="form-control" type="text" readonly :value="item.ec_leader_name">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Người Lập Bản Ghi Nhập Học</label>
                    <input class="form-control" type="text" readonly :value="item.creator_name">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Thời Gian Lập Bản Ghi Nhập Học</label>
                    <input class="form-control" type="text" readonly :value="formatTime(item.created_at)">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Tổng Tiền Giảm Trừ</label>
                    <input class="form-control" type="text" readonly :value="formatAmount(item.final_discount)">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Sản Phẩm</label>
                    <input class="form-control" type="text" readonly :value="item.product_name">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Chương Trình</label>
                    <input class="form-control" type="text" readonly :value="item.program_name">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Gói Phí</label>
                    <input class="form-control" type="text" readonly :value="item.tuition_fee_name">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Chi tiết các khoản giảm trừ</label>
                    <textarea class="form-control discount-description" :class="html.class.description" type="text" v-html="item.description"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-money"></i> <b class="suitcase">Thông tin thu tiền</b>
          </div>
          <div v-show="loading2" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="loading2" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ trong chốc lát...</div>
            </div>
          </div>
          <div id="charge-information" class="content-detail">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Số tiền phải đóng</label>
                  <input class="form-control" type="text" readonly :value="formatAmount(must_charge)">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Tổng tiền đã thu</label>
                  <input class="form-control" type="text" readonly v-model="total_charge">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Số tiền thu</label>
                  <input class="form-control" type="text" v-model="charge_amount">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Công nợ</label>
                  <input class="form-control" type="text" readonly v-model="dept_amount">
                </div>
              </div>
              <div :class="html.class.payload">
                <div class="form-group">
                  <label class="control-label">Hình thức đóng phí</label>
                  <select class="form-control" readonly v-model="item.payload">
                    <option value="0">1 lần</option>
                    <option value="1">Nhiều lần</option>
                  </select>
                </div>
              </div>
              <div :class="html.class.charge_time">
                <div class="form-group">
                  <label class="control-label">Lần đóng</label>
                  <input class="form-control" type="text" :disabled="html.disable.charge_time" v-model="item.charge_time">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Phương thức đóng phí</label>
                  <select class="form-control" v-model="item.method" @change="selectMethod">
                    <option value="0">Tiền mặt</option>
                    <option value="1">Chuyển khoản</option>
                    <option value="2">Quẹt thẻ tín dụng</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group" v-show="showNote">
                  <label class="control-label">Ghi chú</label>
                  <textarea class="form-control" rows="1" v-model="item.note"></textarea>
                </div>
                <div class="form-group" v-show="showBank">
                  <label class="control-label">Ngân hàng</label>
                  <select class="form-control" v-model="item.note" @change="selectBank">
                    <option value="">Chọn ngân hàng quẹt thẻ</option>
                    <option v-for="(bank, index) in banks" :key="index" :value="bank.id">{{ `${bank.name} (${bank.alias})` }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Ngày thu phí</label><br/>
                  <datePicker
                    id="charge-date"
                    class="form-control calendar"
                    :value="charge_date"
                    v-model="charge_date"
                    :placeholder="defaultDate"
                    lang="en-US"
                  >
                  </datePicker>
                </div>
              </div>
            </div>
          </div>
          <b-modal title="THÔNG BÁO" :class="html.class.modal" v-model="modal" @ok="closeModal" :ok-variant="html.variant">
            <div v-html="html.message">
            </div>
          </b-modal>
          <div slot="footer">
            <apaxButton
              :markup="html.markup.save"
              :disabled="html.disable.save"
              :onClick="saveForm"
              ><i class="fa fa-save"></i> Lưu
            </apaxButton>
            <!-- <apaxButton
              :markup="html.markup.reset"
              :disabled="html.disable.reset"
              :onClick="resetForm"
              ><i class="fa fa-ban"></i> Hủy
            </apaxButton> -->
            <apaxButton
              :onClick="exitForm"
              ><i class="fa fa-share-square-o"></i> Thoát
            </apaxButton>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import moment from 'moment'
import u from '../../../utilities/utility'
import datePicker from 'vue2-datepicker'
import searchStudent from '../../../components/StudentSearch'
import searchBranch from '../../../components/SearchBranchForTransfer'
import apaxButton from '../../../components/Button'

export default {
  name: 'Add-Charge',

  components: {
    searchBranch,
    searchStudent,
    datePicker,
    apaxButton
  },

  data () {
    return {
      item: {},
      banks: [],
      defaultDate: this.moment().format('YYYY-MM-DD'),
      charge_date: this.moment().format('YYYY-MM-DD'),
      loading1: false,
      loading2: false,
      current_debt: 0,
      html: {
        variant: '',
        message: '',
        class: {
          modal: 'modal-primary',
          display: {
            search: {
              branch: 'hidden',
              student: 'hidden'
            }
          },
          payload: 'col-md-3',
          charge_time: 'hidden',
          ready: '',
          description: ''
        },
        markup: {
          save: 'success',
          reset: 'error'
        },
        disable: {
          save: true,
          reset: true,
          charge_time: true
        }
      },
      cache: {
        branch: 0,
        student: 0,
        item: {}
      },
      temp: {},
      modal: false,
      completed: false,
      showNote: false,
      showBank: false,
      method: 0,
      must_charge: 0,
      dept_amount: 0,
      total_charge: 0,
      charge_amount: 0
    }
  },

  computed: {

  },

  created () {
    this.start()
  },

  watch: {
    charge_amount: function (val) {
      if (this.item.must_charge) {
        const value = u.fmc(val)
        const total_charged_amount = parseInt(this.item.must_charge, 10) - parseInt(this.current_debt, 10)
        const suma = value.n + total_charged_amount
        const debt = parseInt(this.item.must_charge) - parseInt(suma, 10)
        u.log('Calculating', value, this.item.total_charged, this.item.must_charge, this.item.debt_amount, total_charged_amount, suma, debt)
        if (suma > parseInt(this.item.must_charge, 10)) {
          this.charge_amount = this.formatAmount(parseInt(this.item.must_charge, 10) - parseInt(total_charged_amount, 10))
          this.charge_amount = value.n > 1000 && value.n % 1000 > 0 ? this.formatAmount(((value.n / 1000) + 1) * 1000) : this.formatAmount(parseInt(this.item.must_charge, 10))
          if (this.charge_amount > 1000) {
            this.html.disable.save = false
            this.html.disable.reset = false
          } else {
            this.html.disable.save = true
            this.html.disable.reset = true
          }
        } else {
          if (this.charge_amount > 1000) {
            this.html.disable.save = false
            this.html.disable.reset = false
          } else {
            this.html.disable.save = true
            this.html.disable.reset = true
          }
          this.item.debt_amount = debt
          this.charge_amount = value.s
          this.dept_amount = this.formatAmount(debt)
        }
      }
    }
  },

  methods: {
    findStudents(keyword) {
      return new Promise((resolve, reject) => {
        u.a().get(`/api/charges/students/${keyword}/${this.cache.branch}`)
        .then((response) => {
          let resp = response.data.data
          resp = resp.length ? resp : [{ label: 'Không tìm thấy', branch_name: 'Không có kết quả nào phù hợp' }]
          resolve(resp)
        }).catch(e => u.log('Exeption', e))
      })
    },
    formatAmount: (num) => num && num >= 1000 ? u.currency(num, 'đ') : '0đ',
    formatTime: (inputtime) => inputtime ? moment(inputtime).format('YYYY/MM/DD - HH:mm:ss') : '',
    start() {
      // u.log('Start')
      u.g(`/api/banks/all/data`).then((response) => {
        this.banks = response
      }).catch(e => u.log('Exeption', e))
      this.showNote = true
      if (u.authorized()) {
        this.html.class.display.search.branch = 'display'
      } else {
        this.html.class.display.search.student = 'display'
        this.cache.branch = u.session().user.branch_id
      }
    },
    selectMethod() {
      if (parseInt(this.item.method, 0) === 2) {
        this.showNote = false
        this.showBank = true
      } else {
        this.showNote = true
        this.showBank = false
        this.item.note = ''
      }
    },
    selectBranch(branch) {
      if (branch.id) {
        this.cache.branch = branch.id
        this.html.class.display.search.student = 'display'
      }
    },
    selectBank() {
      if (this.item && parseInt(this.item.method,10) === 2 && this.item.note !== '') {
        const value = u.fmc(this.charge_amount)
        if (value.n > 1000) {
          this.html.disable.save = false
        } else {
          this.html.disable.save = true
        }
      }
    },
    selectStudent(data) {
      this.item = null
      this.item = data
      this.cache.item = data
      this.cache.student = data.student_id
      const dept = parseInt(data.debt_amount) === 0 && parseInt(data.charge_time) === 1 ? parseInt(data.must_charge) : parseInt(data.debt_amount)
      this.current_debt = dept
      this.item.debt_amount = dept
      this.item.note = ''
      this.dept_amount = this.formatAmount(dept)
      this.item.method = 0
      this.charge_amount = 0
      if (this.item.payload === 1) {
        this.html.class.payload = 'col-md-2'
        this.html.class.charge_time = 'col-md-1'
        this.html.disable.charge_time = false
      }
      this.must_charge = data.must_charge
      this.total_charge = this.formatAmount(data.total_charged)
      this.html.class.ready = 'apax-show-detail'
      this.html.class.description = 'content-ready'
    },
    checkingOnClick() {
      const charge_num = u.fmc(this.charge_amount)
      this.charge_amount = charge_num.s
      if (parseInt(this.item.method, 10) === 2 && this.item.note !== '') {
        if (charge_num.n > 1000) {
          this.html.disable.save = false
        } else {
          this.html.disable.save = true
        }
      } else if (parseInt(this.item.method, 10) === 2 && this.item.note === '') {
        this.html.disable.save = true
      } else if (parseInt(this.item.method, 10) != 2) {
        if (charge_num.n > 1000) {
          this.html.disable.save = false
        } else {
          this.html.disable.save = true
        }
      }
    },
    saveForm() {
      const charge_num = u.fmc(this.charge_amount)
      const total_charged_amount = parseInt(this.item.must_charge, 10) - parseInt(this.current_debt, 10)
      if ((parseInt(charge_num.n, 10) + parseInt(total_charged_amount, 10)) > parseInt(this.item.must_charge, 10)) {
        const new_charge_num = u.fmc(u.rd(parseInt(this.item.must_charge, 10)) - u.rd(parseInt(total_charged_amount, 10)))
        this.charge_amount = new_charge_num.s
        this.html.class.modal = 'modal-danger'
        this.html.variant = 'danger'
        this.html.message = 'Số tiền đóng phí không thể vượt quá số tiền phải nộp.'
        this.modal = true
      } else if (this.charge_amount % 1000 > 0 || this.charge_amount < 1000) {
        this.html.class.modal = 'modal-danger'
        this.html.variant = 'danger'
        this.html.message = 'Số tiền đóng phí không thể lẻ dưới 1000đ.'
        this.modal = true
        this.html.disable.save = true
      } else if (parseInt(this.item.method, 10) === 2 && this.item.note === '') {
        this.html.class.modal = 'modal-danger'
        this.html.variant = 'danger'
        this.html.message = 'Vui lòng chọn ngân hàng thanh toán trên thẻ tín dụng.'
        this.modal = true
        this.html.disable.save = true
      } else {
        const total_charged = parseInt(charge_num.n, 10) + parseInt(total_charged_amount, 10)
        // this.item.total_charged = total_charged
        this.total_charge = this.formatAmount(total_charged)
        const data = this.item
        data.charge_time = parseInt(this.item.charge_time),
        data.update = {
          charge_amount: charge_num.n,
          debt_amount: parseInt(this.item.debt_amount),
          total_charged: total_charged,
          method: this.item.method,
          charge_date: this.moment(this.charge_date).format('YYYY-MM-DD'),
          note: this.item.note ? this.item.note : ''
        }
        this.loading1 = true
        this.loading2 = true
        u.p('/api/charges/add', data)
        .then((response) => {
          if (response && response.done) {
            this.loading1 = false
            this.loading2 = false
            this.completed = true
            const msg = `Bản ghi thu phí ngày hôm nay ${moment(new Date()).format('DD/MM/YYYY')} <br/>Đã được lưu thành công:<br/>----------------------------------------------<br/>Số tiền phải đóng: ${this.formatAmount(parseInt(data.must_charge))}<br/>Số tiền vừa đóng: ${this.formatAmount(parseInt(data.update.charge_amount))}<br/>Tổng số tiền đã đóng: ${this.formatAmount(parseInt(data.total_charged) + parseInt(data.update.charge_amount))}<br/>----------------------------------------------<br/>Số công nợ còn lại: ${this.formatAmount(parseInt(data.update.debt_amount))}`
            this.html.class.modal = 'modal-success'
            this.html.variant = 'success'
            this.html.message = msg
            this.modal = true
            // this.html.disable.save = true
          } else {
            this.loading1 = false
            this.loading2 = false
            this.html.class.modal = 'modal-danger'
            this.html.variant = 'danger'
            this.html.message = 'Không kết nối được với máy chủ, bản ghi thu phí chưa được lưu!'
            this.modal = true
            this.html.disable.save = false
          }
        }).catch(e => u.log('Exeption', e))
      }
    },
    closeModal() {
      if (this.completed) {
        this.exitForm()
      } else this.modal = false
    },
    resetForm() {
      this.charge_amount = 0
      const suma = parseInt(this.item.total_charged)
      const debt = parseInt(this.item.must_charge) - suma
      this.html.disable.save = true
      this.html.disable.reset = true
      this.dept_amount = this.formatAmount(debt)
    },
    exitForm() {
      this.$router.push('/charges')
    }
  }

}

</script>

<style scoped language="scss">
textarea.discount-description {
  height: 123px;
  border: 1px solid #e9c7c7;
  background: #f7ebeb;
  color: #c82727;
}
textarea.content-ready {
  background: #f4f9ff;
  color: #165792;
  border: 1px solid #e1edff;
  text-shadow: 0 1px 1px #FFF;
  box-shadow: 0 -1px 0 #a1b7da;
}
</style>
