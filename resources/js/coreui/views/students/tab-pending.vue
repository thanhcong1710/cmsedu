<template>
  <div class="tab-student-pending apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử Pending</b>        
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th>STT</th>
                <th>Tên học sinh</th>
                <th>Trung tâm</th>
                <th>Ngày đăng ký</th>
                <th>Người đăng ký</th>
                <th>Gói sản phẩm</th>
                <th>Chương trình</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Lý do</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(term, index) in list" :key="index">
                <td>{{(index + 1)}}</td>
                <td>{{term.student_name}}</td>
                <td>{{term.branch_name}}</td>
                <td>{{term.created_at}}</td>
                <td>{{term.creator}}</td>
                <td>{{term.product_name}}</td>                
                <td>{{term.program_name}}</td>
                <td>{{term.start_date}}</td>
                <td>{{term.end_date}}</td>
                <td>{{term.description}}</td>
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
      },
      pending: {
        type: Object,
        default: null
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
