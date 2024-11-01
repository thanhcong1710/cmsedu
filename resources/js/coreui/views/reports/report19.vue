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
              <strong>BC19 - Báo cáo số học sinh trong lớp</strong>
              <div class="card-actions">
                <a
                  href="skype:thanhcong1710?chat"
                  target="_blank"
                >  <small className="text-muted"><i class="fa fa-skype" /></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="row">
                <div class="col-md-8">
                  <BranchSelect v-model="selectedBranches" />
                </div>
                <div class="col-md-4">
                  <datepicker
                    v-model="date"
                    :readonly="false"
                    :lang="lang"
                    :bootstrap-styling="true"
                    placeholder="Chọn ngày"
                    input-class="form-control bg-white"
                    class="time-picker"
                  />
                </div>
              </div>
              <div class="row">
                <ActionReport
                  :on-clean="resetPrintInfo"
                  :on-search="viewPrintInfo"
                  :on-export="printReportBC19"
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
                      <th>Trung tâm</th>
                      <th>Lớp</th>
                      <th>Sản phẩm</th>
                      <th>Chương trình</th>
                      <th>Số học sinh trong lớp</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in results"
                      :key="index"
                    >
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.branch_name }}</td>
                      <td>{{ item.class_name }}</td>
                      <td>{{ item.product_name }}</td>
                      <td>{{ item.program_name }}</td>
                      <td>{{ item.student_in_class }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <PagingReport
                :on-change="viewPrintInfo"
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
import u from '../../utilities/utility'
import Datepicker from 'vue2-datepicker'
import moment from 'moment'
import ActionReport from './common/action-report'
import BranchSelect from './common/branch-select'
import PagingReport from './common/PagingReport'
import { getDate } from './common/utils'

export default {
  name      : 'Report19',
  components: {
    Datepicker,
    ActionReport,
    BranchSelect,
    PagingReport,
  },
  data () {
    return {
      date            : moment().format('YYYY-MM-DD'),
      branches        : [],
      lang            : 'vi',
      selectedBranches: [],
      student_number  : 5,
      results         : [],
      accessLoadData  : false,
      pagination      : {},

    }
  },
  created () {
    u.a().get(`/api/reports/branches`).then((response) => {
      this.branches = response.data
    })
    this.checkRole()
  },
  methods: {
    checkRole () {
      u.a().get(`/api/reports/check-role`).then((response) => {
        const rs = response.data
        // console.log('checko role', rs);
        if (rs === 1) {
          this.role_branch          = true
          this.disabledSelectBranch = false
        } else {
          this.role_branch          = false
          this.disabledSelectBranch = true
          const branch_id           = this.branches[0].id
          const branch_name         = this.branches[0].name
          if (branch_id) {
            this.selectedBranches.push(branch_id)
            this.selectedBranche_name = branch_name
          }
        }
        this.accessLoadData = true
        this.viewPrintInfo()
      })
    },

    viewPrintInfo () {
      u.apax.$emit('apaxLoading', true)
      u.a().post(`/api/reports/form-19`, this.getParamsSearch()).then((response) => {
        this.results          = response.data.data.list
        this.pagination.total = response.data.data.total_record
        u.apax.$emit('apaxLoading', false)
      })
    },
    getParamsSearch () {
      return {
        branches: this.selectedBranches,
        limit   : this.pagination.limit,
        page    : this.pagination.cpage,
        date    : this.date,
      }
    },
    resetPrintInfo () {
      this.selectedBranches = ''
      this.date             = ''
    },
    printReportBC19 () {
      const params    = this.getParamsSearch()
      const branchIds = params.branches.map((obj) => obj.id).toString()
      const p         = `/api/exel/print-report-bc19/${branchIds || '_'}/_/${params.date || '_'}`
      window.open(p, '_blank')
    },
  },
}
</script>

<style scoped>
</style>
