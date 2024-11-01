<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer"
                            >
                        <header-report title="Báo cáo thống kê số lượng gói học trong tháng" />
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
                                            :notbefore="notbeforex"
                                            :notafter="notafter"
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
                            <strong>Báo cáo - thống kê số lượng gói học {{ type && type.value === 0 ? "hàng ngày": "hàng tháng" }}</strong>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">

                                <table class="table table-striped table-bordered apax-table">
                                    <tr>
                                        <th colspan="2">GÓI HỌC ĐĂNG KÝ</th>
                                        <th rowspan="2">Tổng số học sinh đăng ký</th>
                                        <th colspan="8">UCREA</th>
                                        <th colspan="8">BRIGHT</th>
                                        <th colspan="8">BLACKHOLE</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">TRUNG TÂM</th>
                                        <th>UCREA01</th>
                                        <th>UCREA02</th>
                                        <th>UCREA03</th>
                                        <th>UCREA04</th>
                                        <th>UCREA05</th>
                                        <th>UCREA06</th>
                                        <th>UCREA12</th>
                                        <th>UCREA24</th>
                                        <th>BRIGHT01</th>
                                        <th>BRIGHT02</th>
                                        <th>BRIGHT03</th>
                                        <th>BRIGHT04</th>
                                        <th>BRIGHT05</th>
                                        <th>BRIGHT06</th>
                                        <th>BRIGHT12</th>
                                        <th>BRIGHT24</th>
                                        <th>BLACKHOLE01</th>
                                        <th>BLACKHOLE02</th>
                                        <th>BLACKHOLE03</th>
                                        <th>BLACKHOLE04</th>
                                        <th>BLACKHOLE05</th>
                                        <th>BLACKHOLE06</th>
                                        <th>BLACKHOLE12</th>
                                        <th>BLACKHOLE24</th>
                                    </tr>
                                    <template v-for="(student, index) in state.students">

                                            <tr>
                                                <td rowspan="2" >{{student.branch_name}}</td>
                                                <td>SỐ LƯỢNG</td>
                                                <td>{{student.total}}</td>
                                                <template v-for="(ucrea, uc) in student.detail.UCREA">
                                                    <td>{{ucrea.total}}</td>
                                                </template>
                                                <template v-for="(brightic, bh) in student.detail.BRIGHT">
                                                    <td>{{brightic.total}}</td>
                                                </template>
                                                <template v-for="(blackhole, bl) in student.detail.BLACKHOLE">
                                                    <td>{{blackhole.total}}</td>
                                                </template>
                                            </tr>
                                            <tr>
                                                <td>DOANH THU</td>
                                                <td>{{student.revenue}}</td>
                                                <template v-for="(ucrea, uc) in student.detail.UCREA">
                                                    <td>{{ucrea.revenue}}</td>
                                                </template>
                                                <template v-for="(brightic, bh) in student.detail.BRIGHT">
                                                    <td>{{brightic.revenue}}</td>
                                                </template>
                                                <template v-for="(blackhole, bl) in student.detail.BLACKHOLE">
                                                    <td>{{blackhole.revenue}}</td>
                                                </template>
                                            </tr>


                                    </template>
                                    <template v-for="s in 2" v-if="state.total.TOTAL">
                                    <tr>
                                        <td class="tg-baqh" rowspan="2" v-if="s == 1"><b>TỔNG</b></td>
                                        <td v-if="s == 1"><b>SỐ LƯỢNG</b></td>
                                        <td v-if="s == 2"><b>DOANH THU</b></td>
                                        <td v-if="s == 1"><b>{{state.total.TOTAL}}</b></td>
                                        <td v-if="s == 2"><b>{{state.total.REVENUE}}</b></td>
                                        <template v-for="(b1, i1) in state.total.UCREA01">
                                            <td v-if="i1 === 'total'  && s == 1"><b>{{b1}}</b></td>
                                            <td v-if="i1 === 'revenue' && s == 2"><b>{{b1}}</b></td>
                                        </template>
                                        <template v-for="(b2, i2) in state.total.UCREA02">
                                            <td v-if="i2 === 'total'  && s == 1"><b>{{b2}}</b></td>
                                            <td v-if="i2 === 'revenue' && s == 2"><b>{{b2}}</b></td>
                                        </template>
                                        <template v-for="(b3, i3) in state.total.UCREA03">
                                            <td v-if="i3 === 'total'  && s == 1"><b>{{b3}}</b></td>
                                            <td v-if="i3 === 'revenue' && s == 2"><b>{{b3}}</b></td>
                                        </template>
                                        <template v-for="(b4, i4) in state.total.UCREA04">
                                            <td v-if="i4 === 'total'  && s == 1"><b>{{b4}}</b></td>
                                            <td v-if="i4 === 'revenue' && s == 2"><b>{{b4}}</b></td>
                                        </template>
                                        <template v-for="(b5, i5) in state.total.UCREA02">
                                            <td v-if="i5 === 'total'  && s == 1"><b>{{b5}}</b></td>
                                            <td v-if="i5 === 'revenue' && s == 2"><b>{{b5}}</b></td>
                                        </template>
                                        <template v-for="(b6, i6) in state.total.UCREA06">
                                            <td v-if="i6 === 'total'  && s == 1"><b>{{b6}}</b></td>
                                            <td v-if="i6 === 'revenue' && s == 2"><b>{{b6}}</b></td>
                                        </template>
                                        <template v-for="(b12, i12) in state.total.UCREA12">
                                            <td v-if="i12 === 'total'  && s == 1"><b>{{b12}}</b></td>
                                            <td v-if="i12 === 'revenue' && s == 2"><b>{{b12}}</b></td>
                                        </template>
                                        <template v-for="(b24, i24) in state.total.UCREA24">
                                            <td v-if="i24 === 'total'  && s == 1"><b>{{b24}}</b></td>
                                            <td v-if="i24 === 'revenue' && s == 2"><b>{{b24}}</b></td>
                                        </template>
                                        <template v-for="(br1, ir1) in state.total.BRIGHT01">
                                            <td v-if="ir1 === 'total'  && s == 1"><b>{{br1}}</b></td>
                                            <td v-if="ir1 === 'revenue' && s == 2"><b>{{br1}}</b></td>
                                        </template>
                                        <template v-for="(br2, ir2) in state.total.BRIGHT02">
                                            <td v-if="ir2 === 'total'  && s == 1"><b>{{br2}}</b></td>
                                            <td v-if="ir2 === 'revenue' && s == 2"><b>{{br2}}</b></td>
                                        </template>
                                        <template v-for="(br3, ir3) in state.total.BRIGHT03">
                                            <td v-if="ir3 === 'total'  && s == 1"><b>{{br3}}</b></td>
                                            <td v-if="ir3 === 'revenue' && s == 2"><b>{{br3}}</b></td>
                                        </template>
                                        <template v-for="(br4, ir4) in state.total.BRIGHT04">
                                            <td v-if="ir4 === 'total'  && s == 1"><b>{{br4}}</b></td>
                                            <td v-if="ir4 === 'revenue' && s == 2"><b>{{br4}}</b></td>
                                        </template>
                                        <template v-for="(br5, ir5) in state.total.BRIGHT02">
                                            <td v-if="ir5 === 'total'  && s == 1"><b>{{br5}}</b></td>
                                            <td v-if="ir5 === 'revenue' && s == 2"><b>{{br5}}</b></td>
                                        </template>
                                        <template v-for="(br6, ir6) in state.total.BRIGHT06">
                                            <td v-if="ir6 === 'total'  && s == 1"><b>{{br6}}</b></td>
                                            <td v-if="ir6 === 'revenue' && s == 2"><b>{{br6}}</b></td>
                                        </template>
                                        <template v-for="(br12, ir12) in state.total.BRIGHT12">
                                            <td v-if="ir12 === 'total'  && s == 1"><b>{{br12}}</b></td>
                                            <td v-if="ir12 === 'revenue' && s == 2"><b>{{br12}}</b></td>
                                        </template>
                                        <template v-for="(b24, i24) in state.total.BRIGHT24">
                                            <td v-if="i24 === 'total'  && s == 1"><b>{{b24}}</b></td>
                                            <td v-if="i24 === 'revenue' && s == 2"><b>{{b24}}</b></td>
                                        </template>
                                        <template v-for="(bl1, il1) in state.total.BLACKHOLE01">
                                            <td v-if="il1 === 'total'  && s == 1"><b>{{bl1}}</b></td>
                                            <td v-if="il1 === 'revenue' && s == 2"><b>{{bl1}}</b></td>
                                        </template>
                                        <template v-for="(bl2, il2) in state.total.BLACKHOLE02">
                                            <td v-if="il2 === 'total'  && s == 1"><b>{{bl2}}</b></td>
                                            <td v-if="il2 === 'revenue' && s == 2"><b>{{bl2}}</b></td>
                                        </template>
                                        <template v-for="(bl3, il3) in state.total.BLACKHOLE03">
                                            <td v-if="il3 === 'total'  && s == 1"><b>{{bl3}}</b></td>
                                            <td v-if="il3 === 'revenue' && s == 2"><b>{{bl3}}</b></td>
                                        </template>
                                        <template v-for="(bl4, il4) in state.total.BLACKHOLE04">
                                            <td v-if="il4 === 'total'  && s == 1"><b>{{bl4}}</b></td>
                                            <td v-if="il4 === 'revenue' && s == 2"><b>{{bl4}}</b></td>
                                        </template>
                                        <template v-for="(bl5, il5) in state.total.BLACKHOLE02">
                                            <td v-if="il5 === 'total'  && s == 1"><b>{{bl5}}</b></td>
                                            <td v-if="il5 === 'revenue' && s == 2"><b>{{bl5}}</b></td>
                                        </template>
                                        <template v-for="(bl6, il6) in state.total.BLACKHOLE06">
                                            <td v-if="il6 === 'total'  && s == 1"><b>{{bl6}}</b></td>
                                            <td v-if="il6 === 'revenue' && s == 2"><b>{{bl6}}</b></td>
                                        </template>
                                        <template v-for="(bl12, il12) in state.total.BLACKHOLE12">
                                            <td v-if="il12 === 'total'  && s == 1"><b>{{bl12}}</b></td>
                                            <td v-if="il12 === 'revenue' && s == 2"><b>{{bl12}}</b></td>
                                        </template>
                                        <template v-for="(bl24, il24) in state.total.BLACKHOLE24">
                                            <td v-if="il24 === 'total'  && s == 1"><b>{{bl24}}</b></td>
                                            <td v-if="il24 === 'revenue' && s == 2"><b>{{bl24}}</b></td>
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
            type      : reportTypeOptions[1],
            reportTypeOptions,
            pagination: {},
            eccSelect:[],
            programSelect:[],
            score: 100,
            notbeforex: '2019-08-1',
            notafter: new Date(),
        }
    },
    methods: {
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
