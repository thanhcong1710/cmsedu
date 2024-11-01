<template>
  <div class="tab-student-reserve apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử bảo lưu</b>
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th>STT</th>
                <th>Tên học sinh</th>
                <th>Trung tâm</th>
                <th>Học Lớp</th>
                <th>Ngày đăng ký</th>
                <th>Người đăng ký</th>
                <th>Gói sản phẩm</th>
                <th>Chương trình</th>
                <th>Ngày bắt đầu bảo lưu</th>
                <th>Ngày kết thúc bảo lưu</th>
                <th>Giữ Chỗ</th>
                <th>Số buổi bảo lưu</th>
                <th>Hình thức</th>
                <th>Lý do</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(term, index) in list" :key="index">
                <td>{{(index + 1)}}</td>
                <td>{{term.student_name}}</td>
                <td>{{term.branch_name}}</td>
                <td>{{term.class_name}}</td>
                <td>{{term.created_at}}</td>
                <td>{{term.creator}}</td>
                <td>{{term.product_name}}</td>
                <td>{{term.program_name}}</td>
                <td>{{term.start_date}}</td>
                <td>{{term.end_date}}</td>
                <td>{{term.is_reserved | reserveToName}}</td>
                <td>{{term.session}}</td>
                <td>{{term.type | reserveType}}</td>
                <td>{{term.note}}</td>
                <td>
                  <span v-if="term.type > 0">
                      <span v-if="term.status == 0" class="text-warning">Chờ GĐTT/GĐV duyệt</span>
                      <span v-else-if="term.status == 1" class="text-warning">Chờ kê toán hội sở duyệt</span>
                      <span v-else-if="term.status == 2" class="text-success">Đã duyệt</span>
                      <span v-else class="text-danger">Từ chối</span>
                  </span>
                </td>
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
    created() {
    },
    methods: {

    },
    components: {}
  }
</script>

<style scoped>

</style>
