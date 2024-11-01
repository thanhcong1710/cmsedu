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
              <strong>BC20 - Báo cáo hiệu số Vận hành - Chi tiết</strong>
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
                <div class="col-md-4">
                  <BranchSelect v-model="selectedBranches" />
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                  :on-clean="resetPrintInfo"
                  :on-export="printReportBC20B"
                />
              </div>
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
import BranchSelect from './common/branch-select'
import ActionReport from './common/action-report'
import { getDate } from './common/utils'

export default {
  name      : 'Report20b',
  components: {
    Datepicker,
    BranchSelect,
    ActionReport,
  },
  data () {
    return {
      branches        : [],
      selectedBranches: [],
      lang            : 'en',
      from_date       : '',
      to_date         : '',
      results         : [],
    }
  },
  created () {
    u.a().get(`/api/zones`).then((response) => {
      this.zones = response.data
    })
    u.a().get(`/api/get-all-regions`).then((response) => {
      this.regions = response.data
    })
    u.a().get(`/api/reports/branches`).then((response) => {
      this.branches = response.data
    })
    this.checkRole()
    this.getDefaultDate()
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
          if (branch_id) {
            this.selectedBranche_name = this.branches[0].name
            this.selectedBranches.push(branch_id)
          }
        }
      })
    },
    resetBranches () {
      this.zone           = ''
      this.region         = ''
      this.disabledZone   = false
      this.disabledRegion = false
    },
    viewPrintInfo () {
      const data = {
        branches: this.selectedBranches,
        fromDate: moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        toDate  : moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
      }
      u.a().post(`/api/reports/form-20`, data).then((response) => {
        this.results = response.data
      })
    },
    resetPrintInfo () {
      this.selectedBranches = ''
      this.from_date        = ''
      this.to_date          = ''
    },
    printReportBC20B () {
      const params = this.getParamsSearch()
      const br     = _.get(params, 'branches', []).toString() || '_'
      const pd     = _.get(params, 'products', []).toString() || '_'
      const pro    = '_'
      const fd     = params.from_date || '_'
      const td     = params.to_date || '_'

      const p = `/api/exel/print-report-bc20b/${br}/${fd}/${td}`
      window.open(p, '_blank')
      if (extraWindow)
        extraWindow.location.reload()
    },
    getParamsSearch () {
      return {
        branches : this.selectedBranches.map((obj) => obj.id),
        limit    : _.get(this, 'pagination.limit'),
        page     : _.get(this, 'pagination.cpage'),
        from_date: getDate(this.from_date),
        to_date  : getDate(this.to_date),
      }
    },
    getDefaultDate () {
      const from_date  =  new Date()
      this.from_date   = moment(from_date, 'DD/MM/YYYY').format('YYYY-MM-01')
      const endOfMonth = moment().endOf('month').format('YYYY-MM-DD')
      this.to_date     = endOfMonth
    },
  },
}
</script>

<style scoped>
</style>
