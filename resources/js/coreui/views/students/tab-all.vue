<template>
    <div class="tab-student-class-transfer apax-content">
        <b-card header-tag="header">
            <div slot="header">
                <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Đang chờ duyệt</b>
            </div>
            <div class="panel">
                <div class="table-responsive scrollable">
                    <table class="table table-bordered table-striped apax-table">
                        <thead>
                        <tr class="text-sm">
                            <th class="width-50">STT</th>
                            <th class="width-150">Hành động</th>
                            <th class="width-150">Tên học viên</th>
                            <th class="width-150">Trạng thái</th>
                            <th class="width-150">Trung tâm</th>
                            <th class="width-150">Nội dung chi tiết</th>
                            <th class="width-150">Ngày tạo</th>
                            <th class="width-150">Người tạo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, ind) in pending" :key="ind">
                            <td>{{ ind + 1 }}</td>
                            <td>{{ item.action_name }}</td>
                            <td>{{ item.student_name }}</td>
                            <td>{{ item.status_name }}</td>
                            <td>{{ item.branch_name }}</td>
                            <td>{{ item.content }}</td>
                            <td>{{ item.created_at }}</td>
                            <td>{{ item.creator_name }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </b-card>
    </div>
</template>

<script>
  import u from '../../utilities/utility'
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
      return {
        pending: [],
      }
    },
    created() {
      let id = this.$route.params.id
      u.g(`/api/student/pending-approval/${id}`).then((res) => {
        if (res)
            this.pending = res
      })

    },
    methods: {},
    components: {}
  }
</script>

<style scoped>

</style>
