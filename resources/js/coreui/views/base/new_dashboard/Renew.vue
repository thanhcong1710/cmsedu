<template>
    <div>
        <b-card header>
            <div slot="header" class="text-center">
                <i></i> <b class="uppercase">DANH SÁCH HỌC SINH TÁI TỤC</b>
            </div>
            <div class="tab-header">
                <ul class="nav nav-tabs">
                    <li :class="{forActive: currentTab === index}" :key="index" v-for="(tab, index) in tabs"
                        class="nav-item">
                        <a class="nav-link" data-toggle="tab" aria-expanded="true"
                           @click="changeTab(index, tab.month, tab.year)">{{tab.text}}</a>
                    </li>
                </ul>
            </div>
            <div class="table-responsive scroll-tb">
                <div class="fixed-table">
                    <table class="table table-striped table-bordered apax-table">
                        <thead>
                        <tr>
                            <th class="col-xs-2">Trung tâm</th>
                            <th class="col-xs-2">Số học sinh đến hạn tái phí</th>
                            <th class="col-xs-2">Số học sinh đóng phí tái tục</th>
                            <th class="col-xs-2">Tỷ lệ tái tục</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(branch, index) in brs" :key="index">
                            <td style="cursor: pointer" @click="clickModal(branch ,index)">{{branch.branch_name}}</td>
                            <td>{{ branch.resign_total }}</td>
                            <td>{{ branch.recharged_total }}</td>
                            <td>{{ branch.recharged_total, branch.resign_total | filterPercentage }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <b-modal :title="modalTitle" class="reNew-Modal" v-model="isOpen" @ok="isOpen = false">
                <table class="table table-striped table-bordered apax-table charge-expired">
                    <thead>
                    <tr class="text-sm">
                        <th class="width-20">STT</th>
                        <th class="width-50">Mã LMS</th>
                        <th class="width-50">Mã Effect</th>
                        <th class="width-50">Tên học sinh</th>
                        <th class="width-50">Sản phẩm</th>
                        <th class="width-50">Chương trình</th>
                        <th class="width-50">Lớp</th>
                        <th class="width-50">Loại khách hàng</th>
                        <th class="width-50">Ngày đến hạn tái tục</th>
                        <th class="width-50">Kết quả</th>
                        <th class="width-50">Gói tái phí</th>
                        <th class="width-50">Số tiền tái phí</th>
                        <th class="width-50">EC</th>
                        <th class="width-50">CM</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(stu, index) in students" :key="index" :class="loadClass(stu)">
                        <td>{{index+1}}</td>
                        <td>{{stu.stu_id}}</td>
                        <td>{{stu.accounting_id}}</td>
                        <td>{{stu.student_name}}</td>
                        <td>{{stu.product_name}}</td>
                        <td>{{stu.program_name}}</td>
                        <td>{{stu.class_name}}</td>
                        <td>{{stu.student_type | customerType}}</td>
                        <td>{{stu.end_date}}</td>
                        <td>{{stu.success}}</td>
                        <td>{{stu.tuition_fee_name}}</td>
                        <td>{{stu.tuition_fee_price | formatMoney}}</td>
                        <td>{{stu.ec_name}}</td>
                        <td>{{stu.cm_name}}</td>
                    </tr>
                    </tbody>
                </table>
            </b-modal>
        </b-card>
    </div>
</template>
<style>
    .forActive {
        background: white;
        border: 1px solid gray;
        border-bottom: none;
    }

    .fixed-table {
        max-height: 300px;
        overflow-y: auto;
    }

    .modal-info .modal-content {
        width: 1000px !important
    }

    .tab-header {
        margin-left: 12px;
    }

    table.charge-expired tr.soon td {
        color: #FFF;
        text-shadow: 0 1px 0 #111;
        background: rgb(255, 4, 4);
    }
</style>
<script>
    import u from '../../../utilities/utility'

    export default {
        components: {},
        props: ['branches', 'month'],
        data() {
            return {
                currentTab: 0,
                forActive: '',
                tabs: this.getTabInfo(),
                modalTitle: '',
                isOpen: false,
                itemReNew: {},
                students: [],
                brs: [],
                currentMonth: parseInt(this.moment().month()) + 1,
                selectedYear: parseInt(this.moment().year())
            }
        },
        created() {
            u.a().get(`/api/dashboards/renew/${this.currentMonth}/${this.selectedYear}`).then(response => {
                this.brs = response.data
            })
        },
        methods: {
            getTabInfo() {
                const resp = [
                    {
                        name: 'tab-1',
                        text: `Tháng ${parseInt(this.moment().month()) + 1} / ${parseInt(this.moment().year())}`,
                        month: parseInt(this.moment().month()) + 1,
                        year: parseInt(this.moment().year())
                    },
                    {
                        name: 'tab-2',
                        text: `Tháng ${parseInt(this.moment().add(1, 'M').month()) + 1} / ${parseInt(this.moment().add(1, 'M').year())}`,
                        month: parseInt(this.moment().add(1, 'M').month()) + 1,
                        year: parseInt(this.moment().add(1, 'M').year())
                    },
                    {
                        name: 'tab-3',
                        text: `Tháng ${parseInt(this.moment().add(2, 'M').month()) + 1} / ${parseInt(this.moment().add(2, 'M').year())}`,
                        month: parseInt(this.moment().add(2, 'M').month()) + 1,
                        year: parseInt(this.moment().add(2, 'M').year())
                    }
                ]
                return resp
            },
            changeTab(val, month, year) {
                this.currentMonth = month
                this.selectedYear = year
                this.currentTab = val
                u.a().get(`/api/dashboards/renew/${month}/${year}`).then(response => {
                    this.brs = response.data
                })
            },
            clickModal(item, index) {
                this.isOpen = true;
                this.modalTitle = item.branch_name;
                u.a().get(`/api/dashboards/renew/${item.branch_id}/${this.currentMonth}/${this.selectedYear}`).then(response => {
                    this.students = response.data
                })
            },
            loadClass(std) {
                let resp = ''
                if (parseInt(std.soon) < 6) {
                    resp = parseInt(std.soon) <= 0 ? 'expired' : `soon`
                }
                return resp
            },
        }
    }
</script>
