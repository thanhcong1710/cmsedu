<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Báo cáo số lượng học sinh thực học tại trung tâm" />
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-12">
                                    <select-branch
                                            :value="state.branches"
                                            @input="actions.changeBranches"
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
                                    :on-export-list="() => actions.exportStudentList(type.value)"
                                    add-btn="Xuất ra danh sách học sinh"
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
                            <strong>Báo cáo - số lượng học sinh thực học tại trung tâm {{ type && type.value === 0 ? "hàng ngày": "hàng tháng" }}</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr class="text-sm">
                                        <th rowspan="2">STT</th>
                                        <th rowspan="2">Tên trung tâm</th>
                                        <th colspan="4">Ucrea</th>
                                        <th colspan="4">Bright IG</th>
                                        <th colspan="4">Black Hole</th>

                                        <th rowspan="2">Tổng học sinh thực học</th>
                                        <th rowspan="2">Tổng lớp</th>
                                        <th rowspan="2">Tổng ca học</th>
                                    </tr>
                                    <tr class="text-sm">
                                        <th>Tên lớp</th>
                                        <th>Số lượng học sinh</th>
                                        <th>Ngày học</th>
                                        <th>Ca học</th>
                                        <th>Tên lớp</th>
                                        <th>Số lượng học sinh</th>
                                        <th>Ngày học</th>
                                        <th>Ca học</th>
                                        <th>Tên lớp</th>
                                        <th>Số lượng học sinh</th>
                                        <th>Ngày học</th>
                                        <th>Ca học</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="state.students">
                                    <template v-for="(student, index) in state.students">
                                        <template v-for="s in student.size">
                                        <tr :key="student.id">
                                            <td :rowspan="student.size" v-if="s === 1">
                                                {{ getSTT(index) }}
                                            </td>
                                            <td :rowspan="student.size" v-if="s === 1">
                                                {{ student.branch_name }}
                                            </td>

                                                <td v-for="a in 4">
                                                    <span v-if="a === 1">{{get(student, `detail.A[${s-1}].cls_name`)}} </span>
                                                    <span v-if="a === 2">{{get(student, `detail.A[${s-1}].total`)}} </span>
                                                    <span v-if="a === 3">{{get(student, `detail.A[${s-1}].class_day`)}} </span>
                                                    <span v-if="a === 4">{{get(student, `detail.A[${s-1}].shift_name`)}} </span>
                                                </td>
                                                <td v-for="b in 4">
                                                    <span v-if="b === 1">{{get(student, `detail.B[${s-1}].cls_name`)}} </span>
                                                    <span v-if="b === 2">{{get(student, `detail.B[${s-1}].total`)}} </span>
                                                    <span v-if="b === 3">{{get(student, `detail.B[${s-1}].class_day`)}} </span>
                                                    <span v-if="b === 4">{{get(student, `detail.B[${s-1}].shift_name`)}} </span>
                                                </td>
                                                <td v-for="c in 4">
                                                    <span v-if="c === 1">{{get(student, `detail.C[${s-1}].cls_name`)}} </span>
                                                    <span v-if="c === 2">{{get(student, `detail.C[${s-1}].total`)}} </span>
                                                    <span v-if="c === 3">{{get(student, `detail.C[${s-1}].class_day`)}} </span>
                                                    <span v-if="c === 4">{{get(student, `detail.C[${s-1}].shift_name`)}} </span>
                                                </td>
                                            <td :rowspan="student.size" v-if="s === 1">
                                                {{student.total.A.student + student.total.B.student + student.total.C.student}}
                                            </td>
                                            <td :rowspan="student.size" v-if="s === 1">
                                                {{student.total.A.class + student.total.B.class + student.total.C.class}}
                                            </td>
                                            <td :rowspan="student.size" v-if="s === 1">
                                                {{student.total.A.class + student.total.B.class + student.total.C.class}}
                                            </td>
                                        </tr>
                                        </template>
                                    </template>
                                    <tr v-if="state.total">
                                        <td>Tổng </td>
                                        <td></td>
                                        <td>{{get(state.total.class,'A')}}</td>
                                        <td>{{get(state.total.student,'A')}}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{get(state.total.class,'B')}}</td>
                                        <td>{{get(state.total.student,'B')}}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{get(state.total.class,'C')}}</td>
                                        <td>{{get(state.total.student,'C')}}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{get(state.total.sum,'student')}}</td>
                                        <td>{{get(state.total.sum,'class')}}</td>
                                        <td>{{get(state.total.sum,'class')}}</td>
                                    </tr>
                                    </tbody>
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
            type      : reportTypeOptions[1],
            reportTypeOptions,
            pagination: {},
            eccSelect:[],
            programSelect:[],
            score: 100,
        }
    },
    watch: {
//        'state.total': function (newValue, oldValue) {
//            if (newValue !== oldValue)
//                this.pagination.total = newValue
//        },
//        'branches': function (newValue, oldValue) {
//        },
    },
    methods: {
        getSTT (index) {
            return index +1
            //(parseInt(this.pagination.cpage) - 1) * parseInt(this.pagination.limit) + index + 1
        },
        getStatus (code) {
            return contractStatus[code] || ''
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
