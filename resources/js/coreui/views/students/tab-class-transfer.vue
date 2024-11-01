<template>
  <div class="tab-student-class-transfer apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử chuyển lớp</b>        
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
                <th class="width-150">Lớp chuyển đi</th>
                <th class="width-150">Trung tâm đến</th>
                <th class="width-150">Lớp chuyển đến</th>
                <th class="width-150">Ngày chuyển</th>
                <th class="width-150">Lý do</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, ind) in list" :key="ind">
                <td>{{ ind++ }}</td>
                <td>{{ item.created_at }}</td>
                <td>{{ item.creator }}</td>
                <td>{{ item.from_approver }}</td>
                <td>{{ item.from_branch }}</td>
                <td>{{ item.class_name }}</td>
                <td>{{ item.to_branch }}</td>
                <td>{{ item.to_class_name }}</td>
                <td>{{ item.transfer_date }}</td>
                <td>{{ item.note }}</td>
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
      return {}
    },
    created() {},
    methods: {},
    components: {}
  }
</script>

<style scoped>
  
</style>
