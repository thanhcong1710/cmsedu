<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Danh sách học sinh học thử trong tháng" />
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
                                            :notbefore="notbeforex"
                                            />
                                </div>
                            </div>
                            <action-report
                                    :on-search="search"
                                    :on-clean="reset"
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
                            <strong>DANH SÁCH HỌC SINH HỌC THỬ </strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên học viên</th>
                                        <th>Năm sinh</th>
                                        <th>Họ tên Phụ Huynh</th>
                                        <th>Số điện thoại</th>
                                        <th>Email</th>
                                        <th>Địa chỉ</th>
                                        <th>Tên khóa học</th>
                                        <th>Thời gian học</th>
                                        <th>Nguồn</th>
                                        <th>Tình trạng check in</th>
                                        <th>Tình trạng sau trải nghiệm</th>
                                        <th>NV sale phụ trách</th>
                                        <th>Ghi chú</th>
                                        <th>Trung Tâm</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(student, index) in state.students">
                                        <tr>
                                            <td class="tg-">{{getSTT(index)}}</td>
                                            <td class="tg-">{{student.name}}</td>
                                            <td class="tg-">{{student.date_of_birth}}</td>
                                            <td class="tg-">{{student.gud_name1}}</td>
                                            <td class="tg-">{{student.gud_mobile1}}</td>
                                            <td class="tg-">{{student.email}}</td>
                                            <td class="tg-">{{student.address}}</td>
                                            <td class="tg-">{{student.cls_name}}</td>
                                            <td class="tg-">{{student.enrolment_start_date}}</td>
                                            <td class="tg-">{{student.source}}</td>
                                            <td class="tg-"></td>
                                            <td class="tg-"></td>
                                            <td class="tg-">{{student.cs_name}}</td>
                                            <td class="tg-">{{student.note}}</td>
                                            <td class="tg-">{{student.branch_name}}</td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                            </div>
                            <paging-report
                                    :on-change="search"
                                    v-model="pagination"
                                    :total="pagination.total"
                                    showall= "no"
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
            pagination: {cpage:1, limit: 20},
            eccSelect:[],
            programSelect:[],
            score: 100,
            notbeforex: '2019-07-1'
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
          return  (parseInt(this.pagination.cpage) - 1) * parseInt(this.pagination.limit) + parseInt(index) + 1
          //return  parseInt(index) + 1
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
        reset () {
          this.pagination.cpage = 1
          this.actions.resetFilter()
        }
    },
    }
</script>

<style scoped>

</style>
