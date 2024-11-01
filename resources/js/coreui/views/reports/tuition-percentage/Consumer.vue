<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Báo cáo tỉ lệ theo gói phí" />
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-12">
                                    <select-branch
                                            :value="state.branches"
                                            @input="actions.changeBranches"
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div
                                        class="col-md-6"
                                        style="margin-top: 16px"
                                        >
                                    <multiselect
                                            :placeholder="'Chọn báo cáo theo thời gian'"
                                            v-model="type"
                                            :options="reportTypeOptions"
                                            label="label"
                                            :close-on-select="true"
                                            :hide-selected="true"
                                            :multiple="false"
                                            :searchable="false"
                                            track-by="value"
                                            />
                                </div>
                                <div
                                        class="col-md-4"
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
                                        class="col-md-4"
                                        style="margin-top: 16px"
                                        v-if="type && type.value === 1"
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
                                    :on-export="() => actions.exportEcc(type.value)"
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
                            <strong>Báo cáo tỉ lệ theo gói phí {{ type && type.value === 0 ? "hàng ngày": "hàng tháng" }}</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table v-if="state.students" class="table table-striped table-bordered apax-table">
                                    <tr>
                                        <th rowspan="2">Tên Trung Tâm</th>
                                        <template v-if="state.students.month"  v-for="m in state.students.month">
                                            <th colspan="11">{{state.students.dates[m]}}</th>
                                        </template>

                                    </tr>
                                    <tr>
                                        <template v-if="state.students.month"  v-for="m in state.students.month">
                                            <template v-for="(label, lb) in state.students.label">
                                                <th>{{label.title}}</th>
                                            </template>
                                        </template>
                                    </tr>
                                    <template v-if="state.students"  v-for="(branche, index) in state.students.branches">
                                    <tr>
                                        <td>{{branche}}</td>
                                        <template v-if="state.students.month"  v-for="m in state.students.month">
                                            <template v-for="(label, lb) in state.students.label">
                                                <td v-if="label.id <= 6">{{getTuition(state.students.tuition,m,lb,index,'list')}}</td>
                                                <td v-if="label.id >6">{{getTuition(state.students.tuition,m,lb,index,'percentage')}}</td>
                                            </template>

                                        </template>
                                    </tr>
                                    </template>
                                    <template v-if="state.students"  v-for="s in 1">
                                        <tr>
                                            <td>TỔNG</td>
                                            <template v-if="state.students.month"  v-for="m in state.students.month">
                                                <template v-for="(label, lb) in state.students.label">
                                                    <td v-if="label.id <= 6">{{getTuitionSum(state.students.sum,m,lb,s,'list')}}</td>
                                                    <td v-if="label.id >6">{{getTuitionSum(state.students.sum,m,lb,s,'percentage')}}</td>
                                                </template>

                                            </template>
                                        </tr>
                                    </template>
                                </table>
                            </div>
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
    import { reportTypeOptions } from '../../../utilities/constants'

    export default {
        components: {
            SelectBranch, HeaderReport, multiselect, ActionReport, Datepicker, PagingReport,
        },
        props: ['actions', 'state'],
        data () {
        return {
            type      : {
              label: 'Báo cáo tháng',
              value: 1,
            },
            reportTypeOptions,
            pagination: {},
            eccSelect:[],
            programSelect:[],
            score: 100,
            notbeforex: '2019-08-1'
        }
    },
    methods: {
        getTuitionSum (tuition,m,lb,id,list) {
            let w = 'w'+m
         return tuition[w][list][lb]
        },
        getTuition (tuition,m,lb,id,list) {
          let w = 'w'+m
          return tuition[w][list][id][lb]
        },
        getSTT (index) {
            return index +1
        },
        getStatus (code) {
            return contractStatus[code] || ''
        },
        getRevenue (key) {
            return "ok"
        },

        search () {
            const type = _.get(this, "type.value",-1)
            this.actions.search(type, parseInt(type) === 0 ? this.state.dateRange : this.state.month,
                    { page: this.pagination.cpage, limit: this.pagination.limit })
        },
        get(value, key, defaultValue){
            return _.get(value, key, '')
        },
    },
    }
</script>

<style scoped>

</style>
