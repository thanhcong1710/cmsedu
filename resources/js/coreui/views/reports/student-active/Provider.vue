<template>
    <div>
        <slot v-bind="{ state, actions }" />
    </div>
</template>

<script>
    import u from '../../../utilities/utility'
    import { getDate } from '../common/utils'

    export default {
        data () {
        return {
            dateRange: null, month    : null, students : [], total    : [], branches : null, ecc : null,
        }
    },
    computed: {
        state (){
            return {
                dateRange: this.dateRange,
                month    : this.month,
                branches : this.branches,
                students : this.students,
                total    : this.total,
                ecc    : this.ecc,
            }

        },
        actions () {
            return { search: this.search, resetFilter: this.resetFilter,
                changeBranches: this.changeBranches, changeEcc: this.changeEcc,exportEcc: this.exportEcc,
                changeMonth: this.changeMonth, changeDateRange: this.changeDateRange, exportStudentList: this.exportStudentList
            }
        },
    },
    methods: {
        changeMonth(value){
            this.month = value
        },
        changeDateRange(value){
            this.dateRange = value
        },
        exportEcc (type) {
            const rangeDate = this.convertDateToStartEndDate(type, parseInt(type) === 0? this.dateRange:this.month) || {}
            const params    = Object.assign({}, rangeDate, {
                branch_ids: this.getBranchIds(),
                time      : new Date().getTime(),
            })
          if(!this.validate(params)){
            return false
          }
            u.apax.$emit('apaxLoading', true)
            u.getFile('/api/export/student-active-by-date', params).then(() => {
                u.apax.$emit('apaxLoading', false)
            })
        },
        exportStudentList (type) {
            const rangeDate = this.convertDateToStartEndDate(type, parseInt(type) === 0? this.dateRange:this.month) || {}
            const params    = Object.assign({}, rangeDate, {
              branch_ids: this.getBranchIds(),
              time      : new Date().getTime(),
              list      : true,
            })
          if(!this.validate(params)){
            return false
          }
            u.apax.$emit('apaxLoading', true)
            u.getFile('/api/export/student-active-by-date', params).then(() => {
              u.apax.$emit('apaxLoading', false)
            })
        },
        changeBranches (value) {
            this.branches = value
        },
        search (type, date, page) {
            const rangeDate = this.convertDateToStartEndDate(type, date) || {}
            const params    = Object.assign({}, rangeDate, page, {
                branch_ids: this.getBranchIds(),
                time      : new Date().getTime(),
            })
          if(!this.validate(params)){
            return false
          }
            u.apax.$emit('apaxLoading', true)
            u.g(`/api/reports/student-active`, params).then((res) => {
                this.students = res && res.data
                this.total    = res && res.total
            }).finally(() => {
                u.apax.$emit('apaxLoading', false)
            })
        },
        getBranchIds () {
            if (!Array.isArray(this.state.branches))
                return null

            return this.state.branches.map((branch) => branch.id)
        },
        convertDateToStartEndDate (type, date) {
            if (!date) return null
            if (type == 0) {
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
            this.ecc     = null
            this.branches = null
        },
        validate(params) {
            let message = "";
            let status = true;
            if (!_.get(params, 'end_date')) {
              message += `Bạn phải chọn tháng xuất báo cáo<br/>`;
              status = false
            }

            if (!status) {
              this.$notify({
                group: 'apax-atc',
                title: 'Có lỗi xảy ra!',
                type: 'danger',
                duration: 3000,
                text: `<br/>-----------------------------------------------<br/>${message}`
              })
            }
            return status
        },
    },
    }
</script>
