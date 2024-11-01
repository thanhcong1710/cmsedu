<template>
    <div>
        <b-card header>
            <div slot="header" class="text-center">
                <i></i> <b class="uppercase">DANH SÁCH HỌC SINH KHÔNG ĐIỂM DANH</b>
            </div>
            <div class="table-responsive scroll-tb">
                <div class="fixed-table">
                    <table class="table table-striped table-bordered apax-table">
                        <thead>
                        <tr>
							<th>#</th>
                            <th class="col-xs-2">Trung tâm</th>
                            <th class="col-xs-2">Lớp</th>
                            <th class="col-xs-2">Mã HS</th>
                            <th class="col-xs-2">Tên HS</th>
							<th class="col-xs-2">Ngày</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(stu, index) in students" :key="index">
                            <td>{{index+1}}</td>
                            <td>{{ stu.branch_name }}</td>
                            <td>{{ stu.cls_name }}</td>
                            <td>{{ stu.crm_id }}</td>
							<td>{{ stu.student_name }}</td>
							<td>{{ stu.date }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
    import u from '../../../../utilities/utility'

    export default {
        components: {},
        props: ['branches', 'month'],
        data() {
            return {
                students: [],
            }
        },
        created() {
            u.g(`/api/dashboards/students_attendances`).then(response => {
                this.students = response
            })
        },
        methods: {
        }
    }
</script>
