<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Báo Cáo tương tác mới Tư vấn viên với khách hàng" />
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-12">
                                    <select-branch
                                            :value="state.branches"
                                            @input="actions.changeBranches"
                                            track-by="id"
                                            />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 16px">
                                    <ecc-select
                                            :branchIds="state.branches"
                                            :value="state.ecc"
                                            @input="actions.changeEcc"
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
                            <strong>Báo cáo - Tư vấn viên với khách hàng {{ type && type.value === 0 ? "hàng ngày": "hàng tháng" }}</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr class="text-sm">
                                        <th>STT</th>
                                        <th>Tên tư vấn viên</th>
                                        <th>Mã NV</th>
                                        <th>Mã Kế toán</th>
                                        <th>Học sinh</th>
                                        <th>Mã CMS</th>
                                        <th>Trung tâm</th>
                                        <th>Ngày giờ</th>
                                        <th>Kết quả tương tác</th>
                                        <th>Điểm tương tác</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="state.students">
                                    <template v-for="(student, index) in state.students">
                                            <tr :key="student.id">
                                                <td>
                                                    {{ getSTT(index) }}
                                                </td>
                                                <td>
                                                    {{ student.creator }}
                                                </td>
                                                <td>{{ student.hrm_id }}</td>
                                                <td>{{ student.accounting_id }}</td>
                                                <td>{{ student.student_name }}</td>
                                                <td>{{ student.crm_id }}</td>
                                                <td>{{ student.br_name }}</td>
                                                <td>{{ student.created_at }}</td>
                                                <td>{{ student.quality_name }}</td>
                                                <td>{{ student.quality_score }}</td>
                                            </tr>
                                    </template>
                                    </tbody>
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
    import EccSelect from '../common/EcSelect'
    import { reportTypeOptions } from '../../../utilities/constants'

    export default {
        components: {
            SelectBranch, HeaderReport, multiselect, ActionReport, Datepicker, PagingReport, EccSelect,
        },
        props: ['actions', 'state'],
        data () {
        return {
            type      : reportTypeOptions[0],
            reportTypeOptions,
            pagination: {},
            eccSelect:[],
            programSelect:[],
            score: 100
        }
    },
    watch: {
        'state.total': function (newValue, oldValue) {
            if (newValue !== oldValue)
                this.pagination.total = newValue
        },
        'branches': function (newValue, oldValue) {
        },
    },
    methods: {
        getSTT (index) {
            return (parseInt(this.pagination.cpage) - 1) * parseInt(this.pagination.limit) + index + 1
        },
        getStatus (code) {
            return contractStatus[code] || ''
        },

        search () {
            const type = _.get(this, "type.value",-1)
            this.actions.search(type, parseInt(type) === 0 ? this.state.dateRange : this.state.month,
                    { page: this.pagination.cpage, limit: this.pagination.limit })
        },
    },
    }
</script>

<style scoped>

</style>
