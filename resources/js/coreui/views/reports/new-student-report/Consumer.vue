<template>
  <div
    class="wrapper"
  >
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
            header-tag="header"
            footer-tag="footer"
          >
            <header-report title="Báo cáo học sinh mới" />
            <div class="content-detail">
              <div class="row">
                <div class="col-md-12">
                  <select-branch
                    :value="state.branches"
                    @input="actions.changeBranches"
                    track-by="id"
                  />
                </div>
              </div>
              <div
                class="row"
              >
                <div
                  class="col-md-6"
                  style="margin-top: 16px"
                >
                  <multiselect
                    placeholder="Chọn loại báo cáo"
                    v-model="type"
                    :options="reportTypeOptions"
                    label="label"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :hide-selected="true"
                    :multiple="false"
                    :searchable="false"
                    track-by="value"
                  />
                </div>
                <div
                  class="col-md-6"
                  style="margin-top: 16px"
                  v-if="type && type.value === 0"
                >
                  <datepicker
                    style="width:100%;"
                    :value="state.dateRange"
                    @input="actions.changeDateRange"
                    range
                    placeholder="Chọn từ ngày - đến ngày"
                  />
                </div>
                <div
                  class="col-md-6"
                  style="margin-top: 16px"
                  v-else
                >
                  <datepicker
                    style="width:100%;"
                    :value="state.month"
                    @input="actions.changeMonth"
                    placeholder="Chọn tháng"
                    type="month"
                    format="YYYY-MM"
                  />
                </div>
              </div>
              <action-report
                :on-search="search"
                :on-clean="actions.resetFilter"
                :on-export="handleExport"
              />
            </div>
          </b-card>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-card
            header-tag="header"
            footer-tag="footer"
          >
            <div slot="header">
              <i class="fa fa-file-text pdt-10" />
              <strong>Báo cáo - Danh sách học sinh mới {{ type && type.value === 0 ? "hàng ngày": "hàng tháng"
              }}</strong>
            </div>
            <div class="content-detail">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="text-sm">
                      <th>STT</th>
                      <th>Mã CRM</th>
                      <th>Mã Cyber</th>
                      <th>Học sinh</th>
                      <th>Trung tâm</th>
                      <th>Sản phẩm</th>
                      <th>Lớp học</th>
                      <th>Gói phí</th>
                      <th>Ngày đóng phí</th>
                      <th>Số tiền phải đóng</th>
                      <th>Số tiền đã đóng</th>
                      <th>Công nợ</th>
                      <th>EC</th>
                      <th>CS</th>
                      <th>Trạng thái</th>
                    </tr>
                  </thead>
                  <tbody v-if="state.students">
                    <template v-for="(student, index) in state.students">
                      <template v-for="(contract, pos) in student.contracts">
                        <tr :key="contract.id">
                          <td
                            :rowspan="student.contracts.length"
                            v-if="pos===0"
                          >
                            {{ getSTT(index) }}
                          </td>
                          <td
                            :rowspan="student.contracts.length"
                            v-if="pos===0"
                          >
                            {{ student.crm_id }}
                          </td>
                          <td
                            :rowspan="student.contracts.length"
                            v-if="pos===0"
                          >
                            {{ student.accounting_id }}
                          </td>
                          <td
                            :rowspan="student.contracts.length"
                            v-if="pos===0"
                          >
                            {{ student.name }}
                          </td>
                          <td>{{ contract.branch_name }}</td>
                          <td>{{ contract.product_name }}</td>
                          <td>{{ contract.class_name }}</td>
                          <td>{{ contract.tuition_fee_name }}</td>
                          <td>{{ contract.charge_date }}</td>
                          <td>{{ contract.must_charge|formatCurrency2 }}</td>
                          <td>{{ contract.total_charged|formatCurrency2 }}</td>
                          <td>{{ contract.debt_amount|formatCurrency2 }}</td>
                          <td>{{ contract.ec_name }}</td>
                          <td>{{ contract.cs_name }}</td>
                          <td>{{ getStatus(contract.status) }}</td>
                        </tr>
                      </template>
                    </template>
                  </tbody>
                </table>
              </div>
              <paging-report
                :on-change="search"
                v-model="pagination"
                :total="pagination.total"
              />
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import SelectBranch from '../common/branch-select'
import HeaderReport from '../common/header-report'
import multiselect from 'vue-multiselect'
import ActionReport from '../common/action-report'
import Datepicker from '../common/DatePicker'
import PagingReport from '../common/PagingReport'
import { contractStatus, reportTypeOptions } from '../../../utilities/constants'

export default {
  components: {
    SelectBranch, HeaderReport, multiselect, ActionReport, Datepicker, PagingReport,
  },
  props: {
    actions: Object,
    state  : Object,
  },
  data () {
    return {
      type      : reportTypeOptions[0],
      reportTypeOptions,
      pagination: {},
    }
  },

  watch: {
    'state.total': function (newValue, oldValue) {
      if (newValue !== oldValue)
        this.pagination.total = newValue
    },
  },
  methods: {
    getSTT (index) {
      return (parseInt(this.pagination.cpage) - 1) * parseInt(this.pagination.limit) + index + 1
    },
    getStatus (code) {
      return contractStatus[code] || ''
    },
    search () {
      const type = _.get(this, 'type.value', -1)

      this.actions.search(type,
        { page: this.pagination.cpage, limit: this.pagination.limit })
    },
    handleExport () {
      const type = _.get(this, 'type.value', -1)
      this.actions.handleExport(type)
    },
  },
}
</script>
