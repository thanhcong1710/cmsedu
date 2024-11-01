<template>
    <div class="tab-student-class-transfer apax-content">
        <b-card header-tag="header">
            <div slot="header">
                <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử chuyển trung tâm</b>
            </div>
            <div class="panel">
                <div class="table-responsive scrollable">
                    <table class="table table-bordered table-striped apax-table">
                        <thead>
                        <tr class="text-sm">
                            <th class="width-50">STT</th>
                            <th class="width-150">Ngày thực hiện</th>
                            <th class="width-150">Người thực hiện</th>
                            <th class="width-150">Người phê duyệt</th>
                            <th class="width-150">Trung tâm đi</th>
                            <th class="width-150">Trung tâm đến</th>
                            <th class="width-150">Ngày chuyển</th>
                            <th class="width-150">Trạng thái</th>
                            <th class="width-150">Lý do</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, ind) in list" :key="ind">
                            <td>{{ ind + 1 }}</td>
                            <td>{{ item.created_at }}</td>
                            <td>{{ item.creator }}</td>
                            <td>{{ item.from_approver }}</td>
                            <td>{{ item.from_branch }}</td>
                            <td>{{ item.to_branch }}</td>
                            <td>{{ item.transfer_date }}</td>
                            <td>
                                <span v-if="item.status == 1" class="text-warning">Chờ duyệt đi</span>
                                <span v-else-if="item.status == 2" class="text-danger">Trung tâm chuyển đã từ chối</span>
                                <span v-else-if="item.status == 3" class="text-danger">Trung tâm nhận đã từ chối</span>
                                <span v-else-if="item.status == 4" class="text-warning">Chờ duyệt đến</span>
                                <span v-else-if="item.status == 5" class="text-warning">Chờ kế toán HO phê duyệt</span>
                                <span v-else-if="item.status == 6" class="text-success">Đã được phê duyệt</span>
                                <span v-else-if="item.status == 7" class="text-danger">Kế toán HO từ chối phê duyệt</span>
                            </td>
                            <td>{{ item.to_approve_comment?item.to_approve_comment:item.from_approve_comment }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </b-card>
    </div>
</template>

<script>
    export default {
        props: {
            list: {
                type: Array,
                default: () => []
            }
        },
        watch: {
            student(data) {

            }
        },
        data() {
            return {}
        },
        created() {},
        methods: {},
        components: {}
    }
</script>

<style scoped>

</style>
