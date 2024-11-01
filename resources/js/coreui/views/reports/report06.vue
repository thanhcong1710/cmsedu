<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
            header-tag="header"
            footer-tag="footer"
          >
            <div slot="header">
              <strong>BC06 - Báo cáo tổng số học sinh</strong>
              <div class="card-actions">
                <a
                  href="skype:thanhcong1710?chat"
                  target="_blank"
                >
                  <small className="text-muted"><i class="fa fa-skype" /></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="row">
                <div class="col-6">
                  <BranchSelect v-model="selectedBranches" />
                </div>
                <div class="col-6">
                  <ProductSelect v-model="selectedProducts" />
                </div>
              </div>
              <div class="row">
                <div
                  class="col-4"
                  style="margin-top: 10px"
                >
                  <StudentTypeSelect v-model="student_types_selected" />
                </div>
                <div
                  class="col-4"
                  style="margin-top: 10px"
                >
                  <datepicker
                    v-model="from_date"
                    :readonly="false"
                    :lang="lang"
                    :bootstrap-styling="true"
                    placeholder="Chọn ngày bắt đầu"
                    input-class="form-control bg-white"
                    class="time-picker"
                  />
                </div>
                <div
                  class="col-4"
                  style="margin-top: 10px"
                >
                  <datepicker
                    v-model="to_date"
                    :lang="lang"
                    input-class="form-control bg-white"
                    placeholder="Chọn ngày kết thúc"
                    :bootstrap-styling="true"
                    class="time-picker"
                  />
                </div>
              </div>
              <div class="row">
                <ActionReport
                  :on-search="viewPrintInfo"
                  :on-clean="resetPrintInfo"
                  :on-export="exportExcel"
                />
              </div>
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
              <strong>Danh sách</strong>
            </div>
            <div class="content-detail">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="text-sm">
                      <th>STT</th>
                      <th>Mã EFFECT</th>
                      <th>Tên học sinh</th>
                      <th>Trung tâm</th>
                      <th>Sản phẩm</th>
                      <th>Chương trình</th>
                      <th>Loại khách hàng</th>
                      <th>Lớp</th>
                      <th>Số buổi còn lại</th>
                      <th>Ngày học cuối</th>
                      <th>EC</th>
                      <th>CM</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in results"
                      :key="index"
                    >
                      <td>{{ index+1 }}</td>
                      <td>{{ item.accounting_id }}</td>
                      <td>{{ item.student_name }}</td>
                      <td>{{ item.branch_name }}</td>
                      <td>{{ item.product_name }}</td>
                      <td>{{ item.program_name }}</td>
                      <td>{{ item.student_type_name }}</td>
                      <td>{{ item.class_name }}</td>
                      <td />
                      <td />
                      <td>{{ item.ec_name }}</td>
                      <td>{{ item.cm_name }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <PagingReport
              :on-change="viewPrintInfo"
              v-model="pagination"
              :total="pagination.total"
            />
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import Datepicker from 'vue2-datepicker'
import moment from 'moment'
import BranchSelect from './common/branch-select'
import ProductSelect from './common/product-select'
import StudentTypeSelect from './common/student-type-select'
import ActionReport from './common/action-report'
import { getDate } from './common/utils'
import PagingReport from './common/PagingReport'

export default {
  name      : 'Report06',
  components: {
    ProductSelect,
    Datepicker,
    StudentTypeSelect,
    BranchSelect,
    ActionReport,
    PagingReport,
  },
  data () {
    return {
      selectedProducts      : [],
      selectedPrograms      : [],
      products              : [],
      programs              : [],
      program               : '',
      branches              : [],
      selectedBranches      : [],
      student_types_selected: [],
      lang                  : 'en',
      from_date             : '',
      to_date               : '',
      results               : [],
      pagination            : {},
    }
  },
  created () {
    u.a().get(`/api/reports/branches`).then((response) => {
      this.branches = response.data
    })
    this.checkRole()
    this.getDefaultDate()
  },
  mounted () {
    this.viewPrintInfo()
  },
  methods: {
    checkRole () {
      u.a().get(`/api/reports/check-role`).then((response) => {
        const rs = response.data
        if (rs === 1) {
          this.role_branch          = true
          this.disabledSelectBranch = false
        } else {
          this.role_branch          = false
          this.disabledSelectBranch = true
          const selected_branch_id  = this.branches[0].id
          if (selected_branch_id)
            this.selectedBranches.push(selected_branch_id)

          this.selectedBranche_name = this.branches[0].name
        }
      })
    },
    getParamsSearch () {
      return {
        branch_ids       : this.selectedBranches.map((obj) => obj.id),
        product_ids      : this.selectedProducts.map((obj) => obj.id),
        customer_type_ids: this.student_types_selected.map((obj) => obj.id),
        limit            : this.pagination.limit,
        page             : this.pagination.cpage,
        from_date        : getDate(this.from_date),
        to_date          : getDate(this.to_date),
      }
    },
    viewPrintInfo () {
      u.apax.$emit('apaxLoading', true)
      u.a().post(`/api/reports/form-06`, this.getParamsSearch())
        .then((response) => {
          this.results          = response.data.list
          this.pagination.total = response.data.total
          u.apax.$emit('apaxLoading', false)
        })
    },
    resetPrintInfo () {
      this.selectedBranches = ''
      this.selectedProducts = ''
      this.selectedPrograms = ''
    },
    exportExcel () {
      const data        = this.getParamsSearch()
      data.tk           = u.token()
      const data_string = JSON.stringify(data)
      const p           = `/api/exel/print-report-bc06/${data_string}`
      window.open(p, '_blank')
    },

    getDefaultDate () {
      const from_date =  new Date()
      this.from_date  = moment(from_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
    },
  },
}
</script>

<style scoped>
    .card-header .back-btn{
        font-size: 14px;
        padding: 4px 10px;
        color: #fff;
        text-shadow: none;
        text-transform: none;
        text-decoration: none;
        float: right;
        position: absolute;
        right: 34px;
        top: 14px;
        line-height: 23px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

</style>
<style>
  .content-loading{
    position: fixed;
  }
</style>
