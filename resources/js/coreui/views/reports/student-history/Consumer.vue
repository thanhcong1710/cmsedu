<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Báo cáo thống kê lịch sử học viên" />
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-6">
                                    <select-branch
                                            :value="state.branches"
                                            @input="actions.changeBranches"
                                            :multiple="false"
                                            :all-branch= "true"
                                            />
                                </div>
                            </div>
                            <div class="row"></div>
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
                            <strong>Báo cáo - thống kê lịch sử học viên <span v-if="state.total">{{state.branchName}}</span></strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table class="table table-striped table-bordered apax-table">
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã CMS</th>
                                        <th>Mã Cyber </th>
                                        <th>Tên học sinh</th>
                                        <th>Tên phụ huynh</th>
                                        <th>Số điện thoại</th>
                                        <th>Trung tâm </th>
                                        <th>EC</th>
                                        <th>CS</th>
                                        <th>Giáo viên </th>
                                        <th>Sản phẩm</th>
                                        <th>Gói phí</th>
                                        <th>Giá gói phí</th>
                                        <th>Số tiền phải đóng</th>
                                        <th>Số buổi theo gói phí</th>
                                        <th>Số tiền đã đóng </th>
                                        <th>Số buổi thực tế được học </th>
                                        <th>Trạng thái </th>
                                        <th>Loại hợp đồng  </th>
                                        <th>Chương trình</th>
                                        <th>Lớp học </th>
                                        <th>Ngày bắt đầu học </th>
                                        <th>Ngày kết thúc</th>
                                    </tr>
                                    <template v-for="(student, index) in state.students">
                                        <template v-for="(detail, det) in student.detail">
                                        <tr>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{getSTT(index)}}</td>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{student.cms_code}}</td>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{student.cyber_code}}</td>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{student.student_name}}</td>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{student.gud_name1}}</td>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{student.gud_mobile1}}</td>
                                            <td v-if="det == 0" :rowspan="student.detail.length">{{student.branch_name}}</td>
                                            <td>{{detail.ec_id}}</td>
                                            <td>{{detail.cm_id}}</td>
                                            <td>{{detail.teacher_name}}</td>
                                            <td>{{detail.product_name}}</td>
                                            <td>{{detail.tuition_name}}</td>
                                            <td>{{detail.tuition_fee_price}}</td>
                                            <td>{{detail.must_charge}}</td>
                                            <td>{{detail.total_sessions}}</td>
                                            <td>{{detail.total_charged}}</td>
                                            <td>{{detail.real_sessions}}</td>
                                            <td>{{detail.c_type}}</td>
                                            <td>{{detail.s_type}}</td>
                                            <td>{{detail.program_name}}</td>
                                            <td>{{detail.class_name}}</td>
                                            <td>{{detail.enrolment_start_date}}</td>
                                            <td>{{detail.enrolment_last_date}}</td>
                                        </tr>
                                        </template>
                                    </template>
                                </table>
                            </div>
                            <paging-report
                                :on-change="search"
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
                type      : reportTypeOptions[0],
                reportTypeOptions,
                pagination: {},
                eccSelect:[],
                programSelect:[],
                score: 100,
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
              //return (parseInt(this.pagination.cpage) - 1) * parseInt(this.pagination.limit) + index + 1
            },
            getStatus (code) {
                return contractStatus[code] || ''
            },
            getRevenue (key) {
                return "ok"
            },

            search () {
                //this.pagination.limit = 100
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
