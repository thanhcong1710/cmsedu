<template>
    <div>
        <b-card header>
            <div slot="header" class="text-center">
                <i></i> <b class="uppercase">{{tableName}}</b>
            </div>
            <div class="fixed-table">
                <table class="table table-striped table-bordered apax-table charge-expired">
                    <thead>
                    <tr class="text-sm">
                        <th>STT</th>
                        <th>Trung tâm</th>
                        <th>Số học sinh quá hạn cọc</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(branch, index) in branches" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td @click="clickModal(branch)" class="clicky">{{ branch.branch_name }}</td>
                        <td>{{ branch.total_students }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <b-modal :title="modalTitle" class="reNew-Modal" v-model="isOpen" @ok="isOpen = false">
                <table class="table table-striped table-bordered apax-table charge-expired">
                    <thead>
                    <tr class="text-sm">
                        <th>STT</th>
                        <th>Tên học sinh</th>
                        <th>Mã LMS</th>
                        <th>Mã EFFECT</th>
                        <th>Gói sản phẩm</th>
                        <th>Chương trình</th>
                        <th>Gói học phí</th>
                        <th>Giá niêm yết</th>
                        <th>Giá thực thu</th>
                        <th>Số đã đóng</th>
                        <th>Số công nợ</th>
                        <th>Ngày đóng</th>
                        <th>Số ngày chờ hoàn phí</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :class="loadClass(student)" v-for="(student, index) in students" :key="index">
                        <td>{{ index + 1}}</td>
                        <td>{{ student.name }}</td>
                        <td>{{ student.stu_id }}</td>
                        <td>{{ student.accounting_id }}</td>
                        <td>{{ student.product_name }}</td>
                        <td>{{ student.program_name }}</td>
                        <td>{{ student.tuition_fee_name }}</td>
                        <td>{{ student.tuition_fee_price | formatMoney }}</td>
                        <td>{{ student.must_charge | formatMoney }}</td>
                        <td>{{ student.total_amount_charged | formatMoney }}</td>
                        <td>{{ student.debt_amount | formatMoney }}</td>
                        <td>{{ student.payment_date | formatDate }}</td>
                        <td>{{ student.left_dates }}</td>
                    </tr>
                    </tbody>
                </table>
            </b-modal>
        </b-card>
    </div>
</template>
<style scoped lang="scss">
    .bgColor {
        background: #f86c6b;
    }

    .nobgColor {
        background: #63c2de;
    }

    table.charge-expired tr td {
        font-size: 11 spx;
    }

    table.charge-expired tr.expired td {
        color: #FFF;
        text-shadow: 0 1px 0 #111;
        background: rgb(148, 0, 0);
    }

    table.charge-expired tr.soon_1 td {
        color: #FFF;
        text-shadow: 0 1px 0 #111;
        background: rgb(255, 4, 4);
    }

    table.charge-expired tr.soon_2 td {
        color: #111;
        background: rgb(255, 101, 101);
    }

    table.charge-expired tr.soon_3 td {
        color: #111;
        background: rgb(255, 171, 171);
    }

    table.charge-expired tr.soon_4 td {
        color: #111;
        background: #ffcccc;
    }

    table.charge-expired tr.soon_5 td {
        // color:#111;
        // background: rgb(255, 199, 146);
    }

    .clicky {
        cursor: pointer;
    }
</style>
<script>

    import axios from 'axios';
    import u from '../../../../utilities/utility'

    export default {
        components: {},
        props: ["tableName"],
        data() {
            return {
                students: [],
                student: '',
                modalTitle: '',
                isOpen: false,
                branches: []
            }
        },
        created() {
            u.bus.$emit('CALL_ME', {test_param: 'test emit ok'})
            this.o.d('CALLING', {test_calling: 'Calling golbal function ok'})
            u.a().get('api/dashboards/get-students-status-monthly-overview').then(response => {
                this.branches = response.data
            })
        },
        methods: {
            check(val) {
                if (this.bgColor == "bad") return true
                else return false
            },
            loadClass(std) {
                let resp = ''
                if (parseInt(std.left_dates) < 6) {
                    resp = parseInt(std.left_dates) <= 0 ? 'expired' : `soon_${std.left_dates}`
                }
                return resp
            },
            clickModal(item) {
                this.isOpen = true;
                this.modalTitle = item.branch_name;
                u.a().get('api/dashboards/get-students-status-monthly/' + item.branch_id).then(response => {
                    this.students = response.data
                })
            }
        }
    }
</script>