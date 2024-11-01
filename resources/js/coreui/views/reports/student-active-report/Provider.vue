<template>
  <div>
    <slot
      :state="state"
      :actions="actions"
    />
  </div>
</template>

<script>
import u from '../../../utilities/utility'
import { getDate } from '../common/utils'

export default {
  data () {
    return {
      dateRange: null, month    : null, students : [], total    : 1, branches : null,
    }
  },
  computed: {
    state () {
      return {
        dateRange: this.dateRange,
        month    : this.month,
        branches : this.branches,
        students : this.students,
        total    : this.total,
      }
    },
    actions () {
      return {
        search         : this.search,
        resetFilter    : this.resetFilter,
        changeBranches : this.changeBranches,
        changeDateRange: this.changeDateRange,
        changeMonth    : this.changeMonth,
        handleExport   : this.handleExport,
      }
    },
  },
  mounted () {
    this.search(0, { page: 1, limit: 20 })
  },
  methods: {
    changeDateRange (value) {
      this.dateRange = value
    },
    changeBranches (value) {
      this.branches = value
    },
    changeMonth (value) {
      this.month = value
    },
    search (type, page) {
      const rangeDate = this.convertDateToStartEndDate(type) || {}
      const params    = Object.assign({}, rangeDate, page, { branch_ids: this.branches })
      u.apax.$emit('apaxLoading', true)
      u.g(`/api/students/report-by-date?${new Date().getTime()}`, params).then((res) => {
        this.students = res && res.data
        this.total    = (res && res.total) || 0
      }).finally(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
    handleExport (type) {
      const rangeDate = this.convertDateToStartEndDate(type) || {}
      const params    = Object.assign({}, rangeDate, { branch_ids: this.branches, t: new Date().getTime() })
      u.apax.$emit('apaxLoading', true)
      u.getFile(`/api/export/student-active-report`, params).then(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
    convertDateToStartEndDate (type) {
      const date = type === 0 ? this.dateRange : this.month
      if (!date) return null

      if (type === 0) {
        return {
          start_date: getDate(date[0]),
          end_date  : getDate(date[1]),
        }
      }
      return {
        start_date: getDate(date),
        end_date  : getDate(new Date(date.getFullYear(), date.getMonth() + 1, 0)),
      }
    },
    resetFilter () {
      this.dateRange = null
      this.month     = null
      //this.branches  = null
    },
  },
}
</script>
