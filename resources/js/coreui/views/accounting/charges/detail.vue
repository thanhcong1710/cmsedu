<template>
  <div class="animated fadeIn apax-form" id="charg-add" :class="html.class.ready">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i> <b class="uppercase">Thông tin học sinh</b>
          </div>
          <div id="student-information" class="filter content">
            <div class="col-12 pad-no content-detail">
              <div class="row">
                <div class="col-md-6">
                  <label class="control-label">Tên Học Sinh</label>
                  <div class="input-group mar-btm">
                    <input class="form-control" type="text" readonly v-model="item.student_name">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Mã CMS</label>
                    <input class="form-control" type="text" readonly :value="item.student_crm_id">
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <label class="control-label">Số Điện Thoại</label>
                  <div class="input-group mar-btm">
                    <input class="form-control" type="text" readonly v-model="item.student_phone">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Tên Phụ Huynh</label>
                    <input class="form-control" type="text" readonly :value="item.parent_name">
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Số Di Động</label>
                    <input class="form-control" type="text" readonly :value="item.parent_mobile">
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input class="form-control" type="text" readonly :value="item.parent_email">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Trung tâm</label>
                    <input class="form-control" type="text" readonly :value="item.branch_name">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">EC</label>
                    <input class="form-control" type="text" readonly :value="item.ec_name">
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">EC Leader</label>
                    <input class="form-control" type="text" readonly :value="item.ec_leader_name">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Mã Bản Ghi Nhập Học</label>
                    <input class="form-control" type="text" readonly :value="item.code">
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Người Lập Bản Ghi Nhập Học</label>
                    <input class="form-control" type="text" readonly :value="item.contract_creator_name">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Thời Gian Lập Bản Ghi Nhập Học</label>
                    <input class="form-control" type="text" readonly :value="formatTime(item.created_at)">
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Tổng Tiền Giảm Trừ</label>
                    <input class="form-control" type="text" readonly :value="formatAmount(item.total_discount)">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Sản Phẩm</label>
                    <input class="form-control" type="text" readonly :value="item.product_name">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Chương Trình</label>
                    <input class="form-control" type="text" readonly :value="item.program_name">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Gói Phí</label>
                    <input class="form-control" type="text" readonly :value="item.tuition_fee_name">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label">Giá Thực Thu Phí</label>
                    <input class="form-control" type="text" readonly :value="formatAmount(item.must_charge)">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Chi tiết các khoản giảm trừ</label>
                    <textarea class="form-control discount-description" :class="html.class.description" type="text" v-html="item.description"></textarea>
                  </div>
                  <div class="button-bar" v-if="item.payload === 1 && item.debt > 0 && item.type > 0">
                    <!-- <div v-if="item.type > 0">
                      <ApaxButton
                        :markup="success"
                        @click="print(item)"
                      ><i class="fa fa-print"></i> In Phiếu Thu
                      </ApaxButton>
                    </div> -->
                    <!-- <div v-if="item.payload === 1 && item.debt > 0 && item.type > 0">
                      <ApaxButton
                        :markup="error"
                        :onClick="quit"
                        :disabled="disableQuit"
                      ><i class="fa fa-bell-slash"></i> Bỏ Cọc
                      </ApaxButton>
                    </div>
                    <div v-else-if="item.payload === 1 && item.debt > 0 && item.type === 0">
                      <ApaxButton
                        :markup="error"
                        :disabled="disableAlreadyQuit"
                      ><i class="fa fa-bell-slash"></i> Đã Bỏ Cọc
                      </ApaxButton>
                    </div> -->
                  </div>
                  <div>
                    <ApaxButton
                      :onClick="exitForm"
                      ><i class="fa fa-share-square-o"></i> Thoát
                    </ApaxButton>
                  </div>
                </div>
              </div>
            </div>
            <div class="hidden">
              <paymentBill :infor="html.print" />
            </div>
          </div>
        </b-card>
      </div>
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-money"></i> <b class="suitcase">Thông tin thu tiền</b>
          </div>
          <div id="charge-information" class="content-detail">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Số tiền phải đóng</label>
                  <input class="form-control" type="text" readonly :value="formatAmount(item.must_charge)">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Tổng số đã thu</label>
                  <input class="form-control" type="text" readonly :value="formatAmount(item.total_charged)">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Số tiền đóng</label>
                  <input class="form-control" type="text" readonly :value="formatAmount(item.amount)">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Công nợ</label>
                  <input class="form-control" type="text" readonly :value="formatAmount(item.debt_amount)">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label class="control-label">Hình thức đóng phí</label>
                  <select class="form-control" readonly :value="item.payload">
                    <option value="0">1 lần</option>
                    <option value="1">Nhiều lần</option>
                  </select>
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group">
                  <label class="control-label">Lần đóng</label>
                  <input class="form-control" type="text" readonly :value="item.charge_time">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Phương thức thanh toán</label>
                  <select class="form-control" v-model="item.method" readonly>
                    <option value="0">Tiền mặt</option>
                    <option value="1">Chuyển khoản</option>
                    <option value="2">Thẻ tín dụng ngân hàng</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Ghi chú</label>
                  <textarea class="form-control" rows="1" v-model="item.note" readonly></textarea>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="control-label">Ngày thu phí</label><br/>
                  <input class="form-control" type="text" readonly :value="chargedDate()">
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-history"></i> <b class="suitcase">Lịch sử đóng phí</b>
          </div>
          <div id="charge-history" class="content-detail">
            <table class="table table-responsive-sm table-striped apax-table">
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Thời gian tạo</th>
                  <th>Người thu phí</th>
                  <th>Sản phẩm</th>
                  <th>Gói phí</th>
                  <th>Số phải đóng</th>
                  <th>Tổng đã đóng</th>
                  <th>Số đã thu</th>
                  <th>Công nợ</th>
                  <th>Lần đóng</th>
                  <th>Phương thức</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(record, ind) in item.history" :key="ind">
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{ind + 1}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{formatTime(record.created_at)}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{record.creator_name}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{record.product_name}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{record.tuition_fee_name}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{formatAmount(record.must_charge)}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{formatAmount(record.total)}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{formatAmount(record.amount)}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{formatAmount(record.debt)}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{record.count}}</router-link></td>
                  <td :class="record.current"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="record.current === 'current' ? '#' : `/charges/${record.id}`">{{record.method | paymentType}}</router-link></td>
                </tr>
              </tbody>
            </table>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import moment from 'moment'
import u from '../../../utilities/utility'
import apaxButton from '../../../components/Button'
import ApaxButton from '../../../components/Button'
import paymentBill from '../../base/prints/payment_bill'
import spell from 'written-number'

spell.defaults.lang = 'vi'

export default {
  name: 'Charge-Detail',

  components: {
    ApaxButton,
    paymentBill
  },

  data () {
    return {
      item: {},
      showPrintForm: false,
      success: 'success',
      disableQuit: false,
      disableAlreadyQuit: true,
      error: 'error',
      html: {
        variant: '',
        message: '',
        print: {},
        class: {
          modal: 'modal-primary',
          display: {
          },
          ready: '',
          description: ''
        },
        markup: {
          save: 'success',
          reset: 'error'
        },
      },
      cache: {
        item: {}
      },
      temp: {},
      modal: false
    }
  },

  computed: {

  },

  created () {
    this.start()
  },

  watch: {

  },

  methods: {
    formatAmount: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ',
    formatTime: (inputtime) => inputtime ? moment(inputtime).format('YYYY-MM-DD - HH:mm:ss') : '',
    load(charge_id) {
      u.g(`/api/charges/${charge_id}`)
      .then(response => {
        this.item = response
        this.disableQuit = this.item.type === 0
        this.html.class.ready = 'apax-show-detail'
        this.html.class.description = 'content-ready'
      })
    },
    chargedDate(){
      return this.item.charge_date ? this.item.charge_date : this.moment(this.item.created_at).format('YYYY-MM-DD')
    },
    start() {
     this.load(this.$route.params.id)
    },
    closeModal() {
    },
    exitForm() {
      this.$router.push('/charges')
    },
    quit() {
      u.g(`/api/charges/quit/${this.item.id}`)
      .then(response => {
        this.disableQuit = true
      })
    },
    print(print_data) {
      this.html.print = print_data
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
.button-bar div {
  float: left;
  margin: 0 10px 0 0 ;
}
textarea.content-ready {
  background: #f4f9ff;
  color: #165792;
  border: 1px solid #e1edff;
  text-shadow: 0 1px 1px #FFF;
  box-shadow: 0 -1px 0 #a1b7da;
}
</style>
