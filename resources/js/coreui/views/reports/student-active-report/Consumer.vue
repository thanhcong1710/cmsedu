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
            <header-report title="Báo cáo học sinh đang active" />
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
    </div>
  </div>
</template>

<script>
import SelectBranch from '../common/branch-select'
import HeaderReport from '../common/header-report'
import multiselect from 'vue-multiselect'
import ActionReport from '../common/action-only-report'
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
