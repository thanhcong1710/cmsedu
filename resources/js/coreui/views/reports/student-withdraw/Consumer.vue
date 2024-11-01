<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Danh sách học sinh đã ngừng học tại CMS" />
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-8">
                                    <select-branch
                                            :value="state.branches"
                                            @input="actions.changeBranches"
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
                            <strong>Danh sách học sinh đã ngừng học tại CMS</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table class="table table-striped table-bordered apax-table">
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã CRM</th>
                                        <th>Mã Cyber</th>
                                        <th>Tên Học sinh</th>
                                        <th>Phụ huynh</th>
                                        <th>Số Điện Thoại</th>
                                        <th>Email</th>
                                        <th>Trung Tâm</th>
                                        <th>EC</th>
                                        <th>Gói Phí</th>
                                        <th>Loại hợp đồng</th>
                                        <th>Giá Gói phí</th>
                                        <th>Số Tiền Phải Đóng</th>
                                        <th>Số Tiền Đã Thu</th>
                                        <th>Số Buổi Được Học</th>
                                        <th>Ngày Bắt Đầu Học </th>
                                        <th>Ngày Kết Thúc</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                    <template v-for="(student, index) in state.students">
                                        <template v-for="n in student.c_size">
                                    <tr>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{getSTT(index)}}</td>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{student.crm_id}}</td>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{student.accounting_id}}</td>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{student.student_name}}</td>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{student.gud_name1}}</td>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{student.gud_mobile1}}</td>
                                        <td v-if="n == 1" :rowspan="student.c_size">{{student.email}}</td>
                                        <td>{{student.contracts[n-1].branch_name}}</td>
                                        <td>{{student.contracts[n-1].ec_name}}</td>
                                        <td>{{student.contracts[n-1].tuition_fee_name}}</td>
                                        <td>{{student.contracts[n-1].type}}</td>
                                        <td>{{student.contracts[n-1].tuition_fee_price}}</td>
                                        <td>{{student.contracts[n-1].must_charge}}</td>
                                        <td>{{student.contracts[n-1].total_charged}}</td>
                                        <td>{{student.contracts[n-1].total_sessions}}</td>
                                        <td>{{student.contracts[n-1].enrolment_start_date}}</td>
                                        <td>{{student.contracts[n-1].enrolment_last_date}}</td>
                                        <td>{{student.contracts[n-1].status}}</td>
                                    </tr>
                                        </template>
                                    </template>

                                </table>
                            </div>
                            <paging-report
                                    :on-change="search"
                                    v-model="pagination"
                                    :total="pagination.total"
                                    showall="no"
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
            type      : reportTypeOptions[1],
            reportTypeOptions,
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
          //return  parseInt(index) + 1
          return  (parseInt(this.pagination.cpage) - 1) * parseInt(this.pagination.limit) + parseInt(index) + 1
        },
        getStatus (code) {
            return contractStatus[code] || ''
        },
        getRevenue (key) {
            return "ok"
        },
        search () {
            this.pagination.limit = 20
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
