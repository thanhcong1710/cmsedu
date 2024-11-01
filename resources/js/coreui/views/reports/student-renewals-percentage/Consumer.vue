<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Danh sách học sinh cần renew trong tháng" />
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
                            <strong>Danh sách học sinh cần renew trong tháng</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table class="table table-striped table-bordered apax-table">
                                    <tr>
                                        <th rowspan="3">STT</th>
                                        <th rowspan="3">Trung tâm</th>
                                        <th rowspan="3">Mã HS</th>
                                        <th rowspan="3">Mã Cyber</th>
                                        <th rowspan="3">Tên học viên</th>
                                        <th rowspan="3">Ngày sinh</th>
                                        <th rowspan="3">Năm sinh</th>
                                        <th colspan="4">Thông Tin phụ huynh</th>
                                        <th rowspan="3">Địa chỉ</th>
                                        <th rowspan="3">Ngày nhập học</th>
                                        <th rowspan="3">Lớp học</th>
                                        <th rowspan="3">Chương trình</th>
                                        <th rowspan="3">Trị giá khóa học</th>
                                        <th rowspan="3">Ngày đến hạn đóng học phí mới</th>
                                        <th rowspan="3">Thông tin sales</th>
                                        <th rowspan="3">Lịch học đăng ký/Ngày</th>
                                        <th rowspan="3">Ca học</th>
                                        <th rowspan="3">Tình trạng Renew (Yes/No)</th>
                                        <th rowspan="3">Nguyên nhân chưa Renew</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Thông tin phụ huynh 1</th>
                                        <th colspan="2">Thông tin phụ huynh 2</th>
                                    </tr>
                                    <tr>
                                        <th class="tg-">Họ và tên</th>
                                        <th class="tg-">Sđt</th>
                                        <th class="tg-">Họ và tên</th>
                                        <th class="tg-">Sđt</th>
                                    </tr>
                                    <template v-for="(student, index) in state.students">
                                    <tr>
                                        <td class="tg-">{{getSTT(index)}}</td>
                                        <td class="tg-">{{student.branch_name}}</td>
                                        <td class="tg-">{{student.crm_id}}</td>
                                        <td class="tg-">{{student.cyber_code}}</td>
                                        <td class="tg-">{{student.sdt_name}}</td>
                                        <td class="tg-">{{student.date_of_birth}}</td>
                                        <td class="tg-">{{student.year_of_birth}}</td>
                                        <td class="tg-">{{student.gud_name1}}</td>
                                        <td class="tg-">{{student.gud_mobile1}}</td>
                                        <td class="tg-">{{student.gud_name2}}</td>
                                        <td class="tg-">{{student.gud_mobile2}}</td>
                                        <td class="tg-">{{student.address}}</td>
                                        <td class="tg-">{{student.enrolment_start_date}}</td>
                                        <td class="tg-">{{student.cls_name}}</td>
                                        <td class="tg-">{{student.program_name}}</td>
                                        <td class="tg-">{{student.tuition_fee}}</td>
                                        <td class="tg-">{{student.enrolment_last_date}}</td>
                                        <td class="tg-">{{student.ec_name}}</td>
                                        <td class="tg-">{{student.class_day}}</td>
                                        <td class="tg-">{{student.shift_name}}</td>
                                        <td class="tg-">{{student.re_type}}</td>
                                        <td class="tg-">{{student.re_info}}</td>
                                    </tr>
                                    </template>


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
