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
            dateRange: null, month    : null, students : [], total    : null, branches : null, ecc : null, branchName: null,
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
                branchName    : this.branchName,
            }

        },
        actions () {
            return { search: this.search, resetFilter: this.resetFilter,
                changeBranches: this.changeBranches, changeEcc: this.changeEcc,exportEcc: this.exportEcc,
                changeMonth: this.changeMonth, changeDateRange: this.changeDateRange
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
           // let url = `/api/export/student-care?${u.makeParamsUrl(params)}`;
            //window.open(url, '_blank');
            u.apax.$emit('apaxLoading', true)
            u.getFile('/api/export/student-history', params).then(() => {
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
            u.apax.$emit('apaxLoading', true)
            u.g(`/api/reports/student-history`, params).then((res) => {
                this.students = res && res.data
                this.total    = res && res.total
                this.branchName    = res && res.name
            }).finally(() => {
                u.apax.$emit('apaxLoading', false)
            })
        },
        getBranchIds () {
            if (!(this.state.branches))
                return null

            return this.state.branches.id
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
    },
    }
</script>
