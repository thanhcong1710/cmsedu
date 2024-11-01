<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Báo cáo số lượng học sinh " />
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
                                            :options="reportMonthTypeOptions"
                                            label="label"
                                            :close-on-select="true"
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
                                        v-if="type && type.value === 1"
                                        >
                                    <datepicker
                                            style="width:100%;"
                                            :value="state.month"
                                            @input="actions.changeMonth"
                                            placeholder="Chọn tháng"
                                            type="month"
                                            format="YYYY-MM"
                                            :notafter="notafter"
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
                            <strong>Báo cáo số lượng học sinh  {{ type && type.value === 0 ? "hàng ngày": "hàng tháng" }}</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table v-if="state.students" class="table table-striped table-bordered apax-table">
                                    <tr>
                                        <th></th>
                                        <th colspan="5">UCREA</th>
                                        <th colspan="5">BRIGHT IG</th>
                                        <th colspan="5">BLACK HOLE</th>
                                        <th colspan="9">Total</th>
                                    </tr>
                                    <template v-for="(student, index) in state.students">
                                    <tr>
                                        <th><b>{{index.replace('w8', 'Total')}}</b></th>
                                        <th><b>Số lớp</b></th>
                                        <th><b>Tổng số học sinh</b></th>
                                        <th><b>Số hs đang học</b></th>
                                        <th><b>Số hs bảo lưu</b></th>
                                        <th><b>Số hs chờ lớp</b></th>
                                        <th><b>Số lớp</b></th>
                                        <th><b>Tổng số hs</b></th>
                                        <th><b>Số hs đang học</b></th>
                                        <th><b>Số hs bảo lưu</b></th>
                                        <th><b>Số hs chờ lớp</b></th>
                                        <th><b>Số lớp</b></th>
                                        <th><b>Tổng số hs</b></th>
                                        <th><b>Số hs đang học</b></th>
                                        <th><b>Số hs bảo lưu</b></th>
                                        <th><b>Số hs chờ lớp</b></th>
                                        <th><b>Số lớp</b></th>
                                        <th><b>Tổng số hs</b></th>
                                        <th><b>Số hs đang học</b></th>
                                        <th><b>Số hs bảo lưu</b></th>
                                        <th><b>Số hs chờ lớp</b></th>
                                        <th><b>Tỷ lệ active</b></th>
                                        <th><b>Tỷ lệ bảo lưu</b></th>
                                        <th><b>Tỷ lệ pending</b></th>
                                        <th><b>Số HSTB/ lớp</b></th>
                                    </tr>
                                        <template v-for="(sdtList, sl) in student.list">
                                            <tr>
                                                <td>{{sl}}</td>
                                                <td>{{sdtList.UCREA.total_class}}</td>
                                                <td>{{sdtList.UCREA.total_student}}</td>
                                                <td>{{sdtList.UCREA.total_active}}</td>
                                                <td>{{sdtList.UCREA.total_reserves}}</td>
                                                <td>{{sdtList.UCREA.total_pending}}</td>
                                                <td>{{sdtList.BRIGHT.total_class}}</td>
                                                <td>{{sdtList.BRIGHT.total_student}}</td>
                                                <td>{{sdtList.BRIGHT.total_active}}</td>
                                                <td>{{sdtList.BRIGHT.total_reserves}}</td>
                                                <td>{{sdtList.BRIGHT.total_pending}}</td>
                                                <td>{{sdtList.BLACKHOLE.total_class}}</td>
                                                <td>{{sdtList.BLACKHOLE.total_student}}</td>
                                                <td>{{sdtList.BLACKHOLE.total_active}}</td>
                                                <td>{{sdtList.BLACKHOLE.total_reserves}}</td>
                                                <td>{{sdtList.BLACKHOLE.total_pending}}</td>
                                                <td>{{sdtList.ALL.total_class}}</td>
                                                <td>{{sdtList.ALL.total_student}}</td>
                                                <td>{{sdtList.ALL.total_active}}</td>
                                                <td>{{sdtList.ALL.total_reserves}}</td>
                                                <td>{{sdtList.ALL.total_pending}}</td>
                                                <td>{{sdtList.ALL.active_percentage}}</td>
                                                <td>{{sdtList.ALL.reserves_percentage}}</td>
                                                <td>{{sdtList.ALL.pending_percentage}}</td>
                                                <td>{{sdtList.ALL.student_class}}</td>
                                            </tr>
                                        </template>
                                        <tr>
                                            <td><b style="color: red">{{student.sum[1]}}</b></td>
                                            <td><b>{{student.sum[2]}}</b></td>
                                            <td><b>{{student.sum[3]}}</b></td>
                                            <td><b>{{student.sum[4]}}</b></td>
                                            <td><b>{{student.sum[5]}}</b></td>
                                            <td><b>{{student.sum[6]}}</b></td>
                                            <td><b>{{student.sum[7]}}</b></td>
                                            <td><b>{{student.sum[8]}}</b></td>
                                            <td><b>{{student.sum[9]}}</b></td>
                                            <td><b>{{student.sum[10]}}</b></td>
                                            <td><b>{{student.sum[11]}}</b></td>
                                            <td><b>{{student.sum[12]}}</b></td>
                                            <td><b>{{student.sum[13]}}</b></td>
                                            <td><b>{{student.sum[14]}}</b></td>
                                            <td><b>{{student.sum[15]}}</b></td>
                                            <td><b>{{student.sum[16]}}</b></td>
                                            <td><b>{{student.sum[17]}}</b></td>
                                            <td><b>{{student.sum[18]}}</b></td>
                                            <td><b>{{student.sum[19]}}</b></td>
                                            <td><b>{{student.sum[20]}}</b></td>
                                            <td><b>{{student.sum[21]}}</b></td>
                                            <td><b>{{student.sum[22]}}</b></td>
                                            <td><b>{{student.sum[23]}}</b></td>
                                            <td><b>{{student.sum[24]}}</b></td>
                                            <td><b>{{student.sum[25]}}</b></td>
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
    import { reportMonthTypeOptions } from '../../../utilities/constants'

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
            reportMonthTypeOptions,
            pagination: {},
            eccSelect:[],
            programSelect:[],
            score: 100,
            notbeforex: '2019-08-1',
            notafter: new Date().toISOString().slice(0,10) ,
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
