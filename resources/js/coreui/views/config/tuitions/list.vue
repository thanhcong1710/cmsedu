<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-card header-tag="header">
        <div slot="header">
          <i class="fa fa-diamond" /> <b class="uppercase">Gói Học Phí</b>
        </div>
        <div class="panel">
          <div class="row">
            <div class="col-md-5 title-bold">
              <b-card header-tag="header">
                <div slot="header">
                  <i class="fa fa-list-alt" /> <b class="uppercase">Danh Sách Sản Phẩm / Gói phí</b>
                </div>
                <div class="panel listing-frame">
                  <div class="row">
                    <div class="form-group list">
                      <label class="control-label tight">Chọn sản phẩm</label>
                      <select
                        class="form-control tight"
                        v-model="product_id"
                        @change="selectProduct(product_id)"
                      >
                        <option value="">
                          Chọn sản phẩm
                        </option>
                        <option
                          :value="product.id"
                          v-for="(product, index) in products"
                          :key="index"
                        >
                          {{ product.name }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div
                      class="form-group list"
                      v-show="tuitions.length"
                    >
                      <label class="control-label tight">Chọn gói phí</label>
                      <ul
                        class="form-control tight"
                        id="tuitions"
                      >
                        <li class="row the-first">
                          <span class="item-tuition-id col-md-3">ID</span>
                          <span class="item-tuition-name col-md-8">Tên Gói Phí</span>
                        </li>
                        <li
                          class="row"
                          :class="{'selected-tuition-fee' : check(tuition.id)}"
                          v-for="(tuition, index) in tuitions"
                          :key="index"
                          @click="selectTuition(tuition.id)"
                        >
                          <span class="item-tuition-id col-md-3">{{ tuition.id }}</span>
                          <span
                            class="item-tuition-name col-md-8"
                            :class="{'text-danger' : check(tuition.id)}"
                          ><i class="fa fa-money" /> {{ tuition.name }}</span>
                        </li>
                      </ul>
                    </div>
                    <abt
                      :markup="'error'"
                      :icon="'fa fa-plus'"
                      label="Chọn Trung Tâm Áp Dụng"
                      title="Chọn các trung tâm được áp dụng Gói Phí"
                      v-if="disableAdd == false"
                      :on-click="addBranches"
                    />
                  </div>
                  <div
                    class="row"
                    v-show="listSelectedBranchs.length"
                  >
                    <ul class="applied-branches">
                      <li class="row the-first">
                        <span class="item-branch-id col-md-3">ID</span>
                        <span class="item-branch-name col-md-8">Tên Trung Tâm Áp Dụng</span>
                      </li>
                      <li
                        v-for="(branch, index) in listSelectedBranchs"
                        :key="index"
                        class="row"
                        @click="removeBranch(branch.id)"
                      >
                        <span class="item-branch-id col-md-3">{{ branch.id }}</span>
                        <span class="item-branch-name col-md-8"><i class="fa fa-remove" /> {{ branch.name }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </b-card>
            </div>
            <div class="col-md-7 title-bold">
              <b-card header-tag="header">
                <div slot="header">
                  <i class="fa fa-table" /> <b class="uppercase">Thông tin thiết lập gói phí</b>
                </div>
                <div class="panel options-frame">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Gói Sản Phẩm:</span>
                        <span class="col-md-8 fline"><input
                          v-model="product.name"
                          class="form-control"
                          type="text"
                          readonly
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Tên gói phí:</span>
                        <span class="col-md-8 fline"><input
                          v-model="tuition.name"
                          class="form-control"
                          type="text"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Mã gói phí Cyber:</span>
                        <span class="col-md-8 fline"><input
                          v-model="tuition.accounting_id"
                          class="form-control"
                          type="text"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Số buổi học:</span>
                        <span class="col-md-8 fline"><input
                          type="number"
                          class="form-control"
                          v-model="tuition.session"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Số tháng học:</span>
                        <span class="col-md-8 fline"><input
                          type="number"
                          class="form-control"
                          v-model="tuition.number_of_months"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Giá niêm yết:</span>
                        <span class="col-md-8 fline"><input
                          type="text"
                          class="form-control"
                          v-model="tuition.price"
                          @change="formatPrice(tuition.price)"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Giá thực thu:</span>
                        <span class="col-md-8 fline"><input
                          type="text"
                          class="form-control"
                          v-model="tuition.receivable"
                          @change="formatReceivable(tuition.receivable)"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Chiết khấu:</span>
                        <span class="col-md-8 fline"><input
                          type="text"
                          v-model="tuition.discount"
                          @change="formatDiscount(tuition.discount)"
                          class="form-control"
                          plasceholder="Nếu nhập giá trị nhỏ hơn 100 sẽ tính là % còn nếu lớn hơn 1000 thì sẽ tính là tiền"
                        ></span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Ngày có hiệu lực:</span>
                        <span class="col-md-8 fline">
                          <datePicker
                            id="available-date"
                            class="form-control calendar"
                            :value="tuition.available_date"
                            v-model="tuition.available_date"
                            placeholder="Chọn ngày bắt đầu có hiệu lực"
                            lang="lang"
                          />
                        </span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Ngày hết hiệu lực:</span>
                        <span class="col-md-8 fline">
                          <datePicker
                            id="expired-date"
                            class="form-control calendar"
                            :value="tuition.expired_date"
                            v-model="tuition.expired_date"
                            placeholder="Chọn ngày hết hiệu lực"
                            lang="lang"
                          />
                        </span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Phân Loại</span>
                        <span class="col-md-8 fline">
                          <select
                            class="form-control"
                            v-model="tuition.type"
                          >
                            <option value="">Chọn loại đóng phí</option>
                            <option value="0">Đóng phí một lần</option>
                            <option value="1">Đóng phí nhiều lần</option>
                          </select>
                        </span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Trạng thái</span>
                        <span class="col-md-8 fline">
                          <select
                            class="form-control"
                            v-model="tuition.status"
                          >
                            <option value="">Chọn loại đóng phí</option>
                            <option value="0">Không hoạt động</option>
                            <option value="1">Đang hoạt động</option>
                          </select>
                          <!-- <c-switch type="text" variant="danger" on="On" id="tuitionStatus" off="Off" :pill="true" v-model="tuition.status" :checked="parseInt(checkStatus, 10) === 1"/> -->
                        </span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Tương ứng UCREA</span>
                        <span class="col-md-8 fline">
                          <vue-select
                            label="name"
                            multiple
                            :options="ucrea_tuitions"
                            v-model="ucrea_selected"
                            placeholder="Chọn gói phí UCREA tương ứng"
                            :searchable="true"
                            :on-change="selectUCREATuition"
                            language="en-US"
                          />
                        </span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Tương ứng BRIGHT IG</span>
                        <span class="col-md-8 fline">
                          <vue-select
                            label="name"
                            multiple
                            :options="bright_ig_tuitions"
                            v-model="bright_ig_selected"
                            placeholder="Chọn gói phí BRIGHT-IG tương ứng"
                            :searchable="true"
                            :on-change="selectAprilTuition"
                            language="en-US"
                          />
                        </span>
                      </div>
                      <div class="row">
                        <span class="col-md-4 tline title-bold txt-right">Tương ứng BLACK HOLE</span>
                        <span class="col-md-8 fline">
                          <vue-select
                            label="name"
                            multiple
                            :options="black_hole_tuitions"
                            v-model="black_hole_selected"
                            placeholder="Chọn gói phí BLACK HOLE tương ứng"
                            :searchable="true"
                            :on-change="selectCDItuition"
                            language="en-US"
                          />
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel-footer">
                  <br>
                  <abt
                    :markup="'success'"
                    :icon="'fa fa-save'"
                    label="Lưu lại"
                    title="Cập nhật thông tin Gói Phí"
                    v-if="disableSave == false"
                    :on-click="saveTuitions"
                  />
                  <abt
                    :markup="'warning'"
                    :icon="'fa fa-recycle'"
                    label="Nhập lại"
                    title="Nhập lại gói phí mới"
                    v-if="disableReset == false"
                    :on-click="resetTuitions"
                  />
                </div>
              </b-card>
            </div>
            <b-modal
              title="Thông Báo"
              :class="classModal"
              size="lg"
              v-model="modal"
              @ok="modal=false"
              ok-variant="primary"
            >
              <div v-html="message" />
            </b-modal>
            <b-modal
              title="Chọn các trung tâm được áp dụng gói phí"
              class="modal-success"
              size="lg"
              v-model="show"
              @ok="saveBranchs"
              @cancel="cancelBranchs"
              ok-variant="success"
            >
              <div class="table-responsive header-filter">
                <table
                  class="table table-bordered apax-table"
                  id="table_list"
                >
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>ID</th>
                      <th>Mã LMS</th>
                      <th>Tên Trung Tâm</th>
                      <th>Khu Vực</th>
                      <th>Vùng</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="filter">
                      <td class="checkbox-item filter-row">
                        <input
                          id="iCheck"
                          @click="checkAll('#iCheck')"
                          name="CheckAll"
                          type="checkbox"
                        >
                      </td>
                      <td class="id-filter filter-row">
                        <input
                          type="text"
                          placeholder="ID"
                          @input="search('id', $event.target.value)"
                          :v-model="id_filter"
                        >
                      </td>
                      <td class="lms-filter filter-row">
                        <input
                          type="text"
                          placeholder="Mã LMS"
                          @input="search('lms', $event.target.value)"
                          :v-model="lms_filter"
                        >
                      </td>
                      <td class="name-filter filter-row">
                        <input
                          type="text"
                          placeholder="Tên trung tâm"
                          @input="search('name', $event.target.value)"
                          :v-model="name_filter"
                        >
                      </td>
                      <td class="zone-filter filter-row">
                        <select
                          @change="find"
                          v-model="zone_filter"
                        >
                          <option value="0">
                            Khu vực
                          </option>
                          <option
                            v-for="(zone, index) in zones"
                            :key="index"
                            :value="zone.id"
                          >
                            {{ zone.name }}
                          </option>
                        </select>
                      </td>
                      <td class="region-filter filter-row">
                        <select
                          @change="find"
                          v-model="region_filter"
                        >
                          <option value="0">
                            Vùng
                          </option>
                          <option
                            v-for="(region, index) in regions"
                            :key="index"
                            :value="region.id"
                          >
                            {{ region.name }}
                          </option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="table-responsive scrollable table-content">
                <table class="table table-bordered table-striped apax-table content-detail">
                  <tbody>
                    <tr
                      v-for="(branch, index) in branches"
                      :key="index"
                    >
                      <td
                        class="checkbox-item"
                        @click="marking(branch)"
                      >
                        <input
                          v-model="listTemporary"
                          :value="branch"
                          :id="'branch_'+branch.id"
                          type="checkbox"
                          readonly
                        >
                      </td>
                      <td
                        class="id-filter"
                        @click="marking(branch)"
                      >
                        {{ branch.id }}
                      </td>
                      <td
                        class="lms-filter"
                        @click="marking(branch)"
                      >
                        {{ branch.lms }}
                      </td>
                      <td
                        class="name-filter"
                        @click="marking(branch)"
                      >
                        {{ branch.name }}
                      </td>
                      <td
                        class="zone-filter"
                        @click="marking(branch)"
                      >
                        {{ branch.zone }}
                      </td>
                      <td
                        class="region-filter"
                        @click="marking(branch)"
                      >
                        {{ branch.region }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- <vue-good-table
                :columns="columns"
                :rows="rows"
                :search-options="{
                  enabled: true,
                }"
                @on-select-all="selectAll"
                @on-row-click="toggleSelectRow"
                :select-options="{
                  enabled: true,
                  selectionInfoClass: 'info-custom'
                }"
                :pagination-options="{
                  enabled: true,
                  perPage: 100,
                }"
                styleClass="vgt-table striped bordered"/> -->
            </b-modal>
          </div>
        </div>
      </b-card>
    </div>
  </div>
</template>

<script>
import select from 'vue-select'
import numberic from 'vue-numeric'
import datePicker from 'vue2-datepicker'
import u from '../../../utilities/utility'
import abt from '../../../components/Button'
import cSwitch from '../../../components/Switch'

export default {
  name: 'ListTuition',
  data () {
    return {
      // Set default checked
      showButton         : false,
      show               : false,
      modal              : false,
      selectedProduct    : '',
      product            : {},
      products           : [],
      status             : '0',
      area               : '0',
      zones              : [],
      zone               : '',
      regions            : [],
      tuitions           : [],
      branches           : [],
      black_hole_tuitions: [],
      black_hole_selected: [],
      bright_ig_tuitions : [],
      bright_ig_selected : [],
      ucrea_tuitions     : [],
      ucrea_selected     : [],
      product_id         : '',
      id_filter          : '',
      name_filter        : '',
      lms_filter         : '',
      ceo_filter         : '',
      zone_filter        : '',
      region_filter      : '',
      classModal         : '',
      tuition            : {
        id              : 0,
        name            : '',
        branch_id       : '',
        product_id      : '',
        session         : '',
        price           : '',
        discount        : '',
        receivable      : '',
        available_date  : '',
        status          : '',
        zone_id         : '',
        expired_date    : '',
        type            : '',
        accounting_id   : '',
        number_of_months: '',
      },
      disableAdd  : true,
      disableSave : true,
      disableReset: true,
      columns     : [
        {
          label        : 'ID',
          field        : 'id',
          filterOptions: { enabled: true },
        },
        {
          label        : 'Tên Trung Tâm',
          field        : 'name',
          filterOptions: { enabled: true },
        },
        {
          label        : 'Khu Vực',
          field        : 'zone',
          filterOptions: { enabled: true },
        },
        {
          label        : 'Vùng',
          field        : 'region',
          filterOptions: { enabled: true },
        },
        {
          label        : 'Giám Đốc Điều Hành',
          field        : 'ceo',
          filterOptions: { enabled: true },
        },
      ],
      rows               : [],
      listTemporary      : [],
      listSelectedBranchs: [],
      tuition_selected   : '',
      message            : '',
      hasPermission      : false,
    }
  },
  components: {
    abt,
    cSwitch,
    numberic,
    'vue-select': select,
    datePicker,
  },
  created () {
    this.checkPermission()
    u.g(`/api/settings/tuitions/config/default`).then((response) => {
      this.branches            = response.branches
      this.products            = response.products
      this.regions             = response.regions
      this.zones               = response.zones
      this.listTemporary       = []
      this.ucrea_tuitions      = response.ucrea_tuitions
      this.bright_ig_tuitions  = response.bright_ig_tuitions
      this.black_hole_tuitions = response.black_hole_tuitions
    })
  },
  methods: {
    reset () {
      this.showButton          = false
      this.tuition_selected    = ''
      this.listTemporary       = []
      this.listSelectedBranchs = []
      this.tuition             = {
        name          : '',
        branch_id     : '',
        product_id    : '',
        session       : '',
        price         : '',
        discount      : '',
        receivable    : '',
        available_date: '',
        status        : '',
        zone_id       : '',
        expired_date  : '',
        type          : '',
      }
    },
    search (name = '', value = '') {
      if (value.length > 3 && (value.length % 3) === 0) {
        const data = {
          id    : name === 'id' && value !== '' ? value : '',
          lms   : name === 'lms' && value !== '' ? value : '',
          name  : name === 'name' && value !== '' ? value : '',
          zone  : parseInt(this.zone_filter),
          region: parseInt(this.region_filter),
        }
        u.g(`/api/settings/branches/${JSON.stringify(data)}`).then((response) => {
          this.branches = response
          setTimeout(() => {
            this.checking()
          }, 10)
        })
      }
    },
    find () {
      const data = {
        id    : name === 'id' && value !== '' ? value : '',
        lms   : name === 'lms' && value !== '' ? value : '',
        name  : name === 'name' && value !== '' ? value : '',
        zone  : parseInt(this.zone_filter),
        region: parseInt(this.region_filter),
      }
      u.g(`/api/settings/branches/${JSON.stringify(data)}`).then((response) => {
        this.branches = response
        setTimeout(() => {
          this.checking()
        }, 10)
      })
    },
    amount (str) {
      let resp = 0
      if (str) {
        const txt = str.toString()
        if (txt.indexOf('đ') > 0)
          resp = txt ? parseInt(txt.replace('đ', '').replace(/,/g, ''), 10) : 0
        else if (txt.indexOf('%') > 0)
          resp = txt ? parseFloat(txt.replace('%', '').replace(/,/g, '')) : 0
        else
          resp = str
      }
      return resp
    },
    formatPrice (x) {
      this.tuition.price = this.formatCurrency(x)
    },
    formatReceivable (x) {
      this.tuition.receivable = this.formatCurrency(x)
    },
    formatDiscount (x) {
      this.tuition.discount = this.formatCurrency(x)
    },
    checkStatus () {
      const checked = parseInt(this.tuition.status, 10) === 1
      $('#tuitionStatus').prop('checked', checked)
    },
    formatCurrency (x) {
      const v      = this.amount(x)
      let resp     = ''
      let number   = 0
      let currency = 'đ'
      if (parseInt(v) < 100) {
        number   = parseFloat(v)
        currency = '%'
        resp     = `${number}${currency}`
      } else {
        number = parseInt(v)
        resp   = number > 0 ? `${number.toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2)}${currency}` : 0
      }
      return resp
    },
    selectTuition (id) {
      this.showButton = true
      if (id) {
        this.tuition_selected = id
        u.g(`/api/tuitions/select/${id}/load/`).then((response) => {
          this.tuition             = response.tuition
          this.tuition.price       = this.formatCurrency(response.tuition.price)
          this.tuition.discount    = this.formatCurrency(response.tuition.discount)
          this.tuition.receivable  = this.formatCurrency(response.tuition.receivable)
          this.checkStatus()
          this.listSelectedBranchs = response.branches || []
          this.listTemporary       = response.branches || []
          this.black_hole_selected = response.black_hole_selected
          this.bright_ig_selected  = response.bright_ig_selected
          this.ucrea_selected      = response.ucrea_selected
          setTimeout(() => {
            this.checking()
          }, 100)
        })
      }
    },
    selectCDItuition (data) {
      this.black_hole_selected = data
    },
    selectAprilTuition (data) {
      this.bright_ig_selected = data
    },
    selectUCREATuition (data) {
      this.ucrea_selected = data
    },
    check (id) {
      if (this.tuition_selected == id) return true
      else return false
    },
    marking (val) {
      const checkedItems = this.listSelectedBranchs.filter((item) => item.id === val.id)
      if (checkedItems.length)
        this.listSelectedBranchs = this.listSelectedBranchs.filter((item) => item.id !== val.id)
      else
        this.listSelectedBranchs.push(val)

      setTimeout(() => {
        this.checking()
      }, 10)
    },
		  selectProduct (val) {
      this.tuition_selected    = ''
      this.tuition             = {
        name          : '',
        branch_id     : '',
        product_id    : '',
        session       : '',
        price         : '',
        discount      : '',
        available_date: '',
        status        : '',
        zone_id       : '',
        expired_date  : '',
        type          : '',
      }
      this.black_hole_selected = []
      this.bright_ig_selected  = []
      this.ucrea_selected      = []
      this.listTemporary       = []
      this.listSelectedBranchs = []
      if (val) {
        u.g(`/api/settings/tuitions/select/product/${val}`).then((response) => {
          this.product  = response.product
          this.tuitions = response.tuitions

          if (this.hasPermission) {
            this.disableAdd   = false
            this.disableSave  = false
            this.disableReset = false
          } else {
            this.disableAdd   = true
            this.disableSave  = true
            this.disableReset = true
          }
        })
      }
    },
    checking () {
      $.each(this.branches, (index, val) => {
        let search = []
        if (this.listSelectedBranchs.length)
          search = this.listSelectedBranchs.filter((item) => parseInt(item.id, 10) === parseInt(val.id, 10))

        u.log('AAAAA', search.length)
        if (search.length)
          $(`#branch_${val.id}`).prop('checked', true)
        else
          $(`#branch_${val.id}`).prop('checked', false)
      })
    },
    addBranches () {
      this.find()
      this.show = true
    },
    updateTuitions () {
      const t = []
      for (const i in this.listSelectedBranchs)
        t.push(this.listSelectedBranchs[i].id)

      const p            = {
        name          : '',
        branch_id     : Array,
        product_id    : '',
        session       : '',
        price         : '',
        discount      : '',
        available_date: '',
        status        : '',
        zone_id       : '',
        expired_date  : '',
        type          : '',
      }
      p.name             = this.tuition.name
      p.session          = this.tuition.session
      p.number_of_months = this.tuition.number_of_months
      p.product_id       = this.product_id
      p.branch_id        = t
      p.price            = parseInt(this.amount(this.tuition.price))
      p.receivable       = parseInt(this.amount(this.tuition.receivable))
      p.discount         = parseFloat(this.amount(this.tuition.discount))
      p.available_date   = this.tuition.available_date
      p.expired_date     = this.tuition.expired_date
      p.type             = this.tuition.type
      p.status           = this.tuition.status
      u.a().put(`/api/tuitionFees/${this.tuition.id}`, p).then((response) => {
        alert('Thay đổi thành công!')
      })
    },
    saveTuitions () {
      let msg      = ''
      let validate = true
      if (this.tuition.name === '') {
        validate = false
        msg      += '(*) Tên gói phí là bắt buộc! <br/>'
      }
      if (this.tuition.accounting_id === '') {
        validate = false
        msg      += '(*) Mã gói phí Cyber là bắt buộc! <br/>'
      }
      if (this.tuition.session === '') {
        validate = false
        msg      += '(*) Số buổi gói phí là bắt buộc! <br/>'
      }
      if (this.tuition.number_of_months === null || this.tuition.number_of_months === '') {
        validate = false
        msg      += '(*) Số tháng của gói phí là bắt buộc! <br/>'
      }
      if (parseInt(this.tuition.session) < 0) {
        validate = false
        msg      += '(*) Số buổi gói phí phải lớn hơn 0! <br/>'
      }
      if (this.product_id === '') {
        validate = false
        msg      += '(*) Sản phẩm không được để trống! <br/>'
      }
      if (this.tuition.price === '') {
        validate = false
        msg      += '(*) Giá gói phí không được để trống! <br/>'
      }
      if (this.tuition.receivable === '') {
        validate = false
        msg      += '(*) Giá thực thu không được để trống! <br/>'
      }
      // if (parseFloat(this.amount(this.tuition.discount)) == ''){
      //   validate = false
      //   msg += "(*) Triết khấu không được để trống! <br />"
      // }
      if (this.tuition.available_date === '') {
        validate = false
        msg      += '(*) Ngày bắt đầu không được để trống! <br />'
      }
      if (this.tuition.expired_date === '') {
        validate = false
        msg      += '(*) Ngày kết thúc không được để trống! <br />'
      }
      if (this.tuition.type === '') {
        validate = false
        msg      += '(*) Loại đóng phí không được để trống! <br />'
      }
      if (this.listSelectedBranchs.length === 0) {
        validate = false
        msg      += '(*) Chưa chọn trung tâm được áp dụng gói phí! <br />'
      }
      // if (this.tuition.status == ''){
      //   validate = false
      //   msg += "(*) Trạng thái không được để trống! <br />"
      // }
      if (!validate) {
        msg             = `Dữ liệu gói phí chưa hợp lệ <br/>-----------------------------------<br/><br/><p class="text-danger">${msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
        this.classModal = 'modal-primary'
        this.message    = msg
        this.modal      = true
      } else {
        const t = []
        for (const i in this.listSelectedBranchs)
          t.push(this.listSelectedBranchs[i].id)

        // u.log(this.ucrea_selected)
        // u.log(this.bright_ig_selected)
        // u.log(this.black_hole_selected)
        const p            = {}
        p.black_hole       = this.black_hole_selected
        p.bright           = this.bright_ig_selected
        p.ucrea            = this.ucrea_selected
        p.name             = this.tuition.name
        p.session          = this.tuition.session
        p.number_of_months = parseInt(this.tuition.number_of_months)
        p.product_id       = this.product_id
        p.branch_id        = t
        p.price            = parseInt(this.amount(this.tuition.price))
        p.receivable       = parseInt(this.amount(this.tuition.receivable))
        p.discount         = parseFloat(this.amount(this.tuition.discount))
        p.available_date   = this.moment(this.tuition.available_date).format('YYYY-MM-DD')
        p.expired_date     = this.moment(this.tuition.expired_date).format('YYYY-MM-DD')
        p.type             = this.tuition.type
        p.status           = parseInt(this.tuition.status, 10)
        p.accounting_id    = this.tuition.accounting_id
        if (parseInt(this.tuition.id, 10) > 0) {
          u.a().put(`/api/tuitionFees/${this.tuition.id}`, p).then((response) => {
            this.classModal = 'modal-success'
            this.message    = 'Thông tin gói phí đã được cập nhật thành công!'
            this.modal      = true
          })
        } else {
          u.a().post(`/api/tuitionFees`, p).then((response) => {
            this.classModal = 'modal-success'
            this.message    = 'Thông tin gói phí đã được cập nhật thành công!'
            this.modal      = true
          })
        }
      }
    },
    saveBranchs () {
      $(`td.id-filter input`).val('')
      $(`td.lms-filter input`).val('')
      $(`td.name-filter input`).val('')
      this.id_filter     = ''
      this.lms_filter    = ''
      this.name_filter   = ''
      this.zone_filter   = 0
      this.region_filter = 0
      this.show          = false
    },
    cancelBranchs () {
      this.listTemporary = this.listSelectedBranchs
      $(`td.id-filter input`).val('')
      $(`td.lms-filter input`).val('')
      $(`td.name-filter input`).val('')
      this.id_filter     = ''
      this.lms_filter    = ''
      this.name_filter   = ''
      this.zone_filter   = 0
      this.region_filter = 0
      this.show          = false
    },
    removeBranch (id) {
      const result = confirm('Bạn có chắc chắn là muốn xoá trung tâm này?')
      if (result) {
        this.listSelectedBranchs = this.listSelectedBranchs.filter((x) => x.id !== id)
        this.listTemporary       = this.listSelectedBranchs
      } return false
    },
    resetTuitions () {
      this.tuition_selected    = ''
      const ex_id              = parseInt(this.tuition.id, 10)
      this.tuition             = {
        id            : ex_id,
        name          : '',
        branch_id     : '',
        product_id    : '',
        session       : '',
        price         : '',
        discount      : '',
        available_date: '',
        status        : '',
        zone_id       : '',
        expired_date  : '',
        type          : '',
      }
      this.black_hole_selected = []
      this.bright_ig_selected  = []
      this.ucrea_selected      = []
      this.listTemporary       = []
      this.listSelectedBranchs = []
      u.a().get(`/api/products/${this.product.id}/tuitions`).then((response) => {
        this.tuitions = response.data
      })
    },
    checkAll (id) {
      if (this.branches) {
        if ($(id).prop('checked')) {
          $.each(this.branches, (index, val) => {
            $(`#branch_${val.id}`).prop('checked', true)
          })
          this.branches.map((item) => {
            const check = this.listSelectedBranchs.filter((itm) => itm.id === item.id)
            if (check.length === 0)
              this.listSelectedBranchs.push(item)

            return item
          })
        } else {
          $.each(this.branches, (index, val) => {
            $(`#branch_${val.id}`).prop('checked', false)
          })
          const newlist            = []
          const exlist             = this.listSelectedBranchs
          exlist.map((item) => {
            const check = this.branches.filter((itm) => itm.id === item.id)
            if (check.length === 0)
              newlist.push(item)

            return item
          })
          this.listSelectedBranchs = newlist
        }
        setTimeout(() => {
          this.checking()
        }, 10)
      }
    },
    checkPermission () {
      const roles = [u.r.admin, u.r.super_administrator]
      if (roles.indexOf(parseInt(u.session().user.role_id)) > -1)
        this.hasPermission = true
      else
        this.hasPermission = false
    },
  },
}
</script>

<style scoped>
  .test {
    margin-top: 10px;
  }
  .test .padding-fix {
    padding: 17px;
  }
  .title-bold {
    font-weight: bold;
  }
  .add-btn {
    float: right;
    margin: 10px 0;
  }
  .form-group.list {
    width: 100%;
    float: left;
    overflow: hidden;
  }
  select.tight {
    width: 100%;
  }
  ul.tight, ul.applied-branches {
    list-style: none;
    padding: 0 0 0 10px;
    border: 0;
    float: left;
    height: auto;
    width: 100%;
    overflow-y: auto;
    max-height: 296px;
    margin: 0;
  }
  ul.tight li, ul.applied-branches li {
    list-style: none;
    border: 0;
    margin: 5px 0;
    -webkit-box-shadow: 0 0.5px 0px #555;
    box-shadow: 0 0.5px 0px #777;
    cursor: pointer;
    font-weight: 500;
  }
  ul.tight li:nth-child(2), ul.applied-branches li:nth-child(2) {
    margin: 45px 0 5px 0;
  }
  ul.tight li span, ul.applied-branches li span {
    padding: 8px 0 10px;
  }
  ul.applied-branches {
    padding: 0 0 0 10px;
  }
  ul.applied-branches li .item-branch-id, ul.tight li .item-tuition-id {
    text-align: right;
    margin: 0 5% 0 0;
  }
  .tline {
    padding: 5px 0 0 0;
  }
  ul.tight li.selected-tuition-fee, ul.tight li.selected-tuition-fee span, ul.tight li.selected-tuition-fee .text-danger {
    color: #e60008!important;
    text-shadow: 0 1px 1px #ded6d6;
    font-weight: 500!important;
  }
  ul.tight li.selected-tuition-fee {
    border-bottom: 1px solid #e60008;
    box-shadow: 0 1px 0px #ded6d6;
  }
  ul.tight li:hover, ul.applied-branches li:hover {
    color: #a50005;
    box-shadow: 0 0.5px 0px rgb(165, 2, 2);
  }
  ul.tight li:active, ul.applied-branches li:active {
    color: #500003;
    text-shadow: 0 -1px 0 #FFF;
    box-shadow: 0 0.5px 0px rgb(87, 0, 0);
  }
  li.row.the-first {
    border-bottom: 1px solid rgb(47, 80, 117);
    width: 96%;
    position: absolute;
    margin: 0 4px 0 0;
    padding: 5px 0 0;
    background: #fff;
    z-index: 999;
  }
  li.row.the-first span {
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
  }
  .fline {
    margin: 0 0 10px 0;
  }
  .fline input {
    border: none;
    border-bottom: 1px solid #bbb;
    padding: 0 0 0 10px;
    height: 25px;
  }
  .header-filter {
    overflow: hidden;
    padding: 10px 0 0 0;
    height: 83px;
    z-index: 999;
    margin: 0 14px -11px 9px;
    position: relative;
    border-bottom: 1px solid #999;
  }
  .table-content {
    max-height: 300px;
  }
  .header-filter table {
    margin:0 !important;
  }
  table tr td.filter-row {
    padding: 5px 0 7px!important;
    background:#31577d;
    vertical-align: none;
  }
  table tr td.checkbox-item {
    width:5%
  }
  table.content-detail tr td {
    font-weight: 500!important;
    cursor: pointer;
  }
  table tr td.id-filter {
    width:7%
  }
  table tr td.id-filter input {
    width: 36px;
    height: 22px;
    padding: 0 8px;
  }
  table tr td.lms-filter {
    width:20%;
  }
  table tr td.lms-filter input {
    width:135px;
    height: 22px;
    padding: 0 8px;
  }
  table tr td.name-filter {
    width:44%;
  }
  table tr td.name-filter input {
    width: 300px;
    height: 22px;
    padding: 0 8px;
  }
  table tr td.zone-filter {
    width:12%;
  }
  table tr td.zone-filter select {
    width: 65px;
    height: 20px;
  }
  table tr td.region-filter {
    width:12%
  }
  table tr td.region-filter select {
    width: 60px;
    height: 20px;
  }
  table tr td.ceo-filter {
    width:30%;
  }
  table tr td.ceo-filter input {
    width:200px;
    padding: 0 8px;
  }
  ::-webkit-scrollbar {
    width: 5px;
  }
  ::-webkit-scrollbar-track {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
      -webkit-border-radius: 10px;
      border-radius: 10px;
      width:5px;
  }
  ::-webkit-scrollbar-thumb {
      background-color: rgb(255, 86, 86);
	    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .2) 25%,
											  transparent 25%,
											  transparent 50%,
											  rgba(255, 255, 255, .2) 50%,
											  rgba(255, 255, 255, .2) 75%,
											  transparent 75%,
											  transparent);
      width:5px;
  }
  ::-webkit-scrollbar-thumb:window-inactive {
    background: rgba(155, 155, 155, 0.4);
    width:5px;
  }
  .info-register {
    border: 1px solid #d7d6d6;
    padding: 10px;
  }
  #class_tuitions input {
    border: none;
    border-bottom: 0.5px solid #ccc;
    height: 29px;
    font-size: 10px
  }
  .status-active {
    border: 1px solid #becdbf;
    border-radius: 4px;
    padding: 3px;
    background-color: #2e9fff;
    color: #fff;
  }
  .options-frame {
    height:705px;
  }
  .listing-frame {
    height:754px;
  }
  .title-search {
    text-align: right;
    padding: 5px 0 0 0;
    font-size: 13px;
    font-weight: 500;
  }

  .col-4 {
    padding: 0px 0px 8px 8px;
  }
  #class_infor {
    padding: 10px 10px;
  }
  #table_list th, td {
    font-size: 0.8rem;
  }
  .fix-boder {
    border: 1px solid #223b54;
  }
  .txt-right {
    text-align: right
  }
  .row-boss {
    padding: 20px;
  }
  .fix-form {
    width: 30% !important;
  }
  .btn-gray {
    background-color: #6a6a6a;
  }
  .scroll-tb {
    max-height: 350px;
    overflow-y: auto;
  }
  .remove-branch {
    cursor: pointer;
  }
  .remove-branch:hover {
    background-color: #b4b1b1;
    border: 2px solid #b4b1b1;
    border-radius: 3px;
  }
  .title-modal-fix {
    margin-bottom: 20px;
    margin-top: 5px
  }
  .fixed-input {
    font-size: 14px;
    color: #737373;
  }
  #tuition span{
	cursor: pointer;
  }
</style>

