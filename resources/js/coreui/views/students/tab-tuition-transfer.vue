<template>
  <div class="tab-student-tuition-transfer apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử chuyển phí</b>
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th>STT</th>
                <th>HS Chuyển</th>
                <th>Mã CMS</th>
                <th>Mã kế toán</th>
                <th>HS Nhận</th>
                <th>Mã CMS</th>
                <th>Mã kế toán</th>
                <th>Số Tiền Chuyển</th>
                <th>Số Tiền Nhận</th>
                <th>Ngày Chuyển Phí</th>
                <th>Người Tạo</th>
                <th>Trạng Thái</th> 
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list" :key="index">
                <td>{{index+1}}</td>
                <td>{{ item.from_student_name }} </td>
                <td>{{ item.from_student_crm }}</td>
                <td>{{ item.from_student_act }}</td>
                <td>{{ item.to_student_name }}</td>
                <td>{{ item.to_student_crm }}</td>
                <td>{{ item.to_student_act }}</td>
                <td>{{ item.transferred_amount | formatMoney }}</td>
                <td>{{ item.received_amount | formatMoney }}</td>
                <td>{{ item.transfer_date }}</td>
                <td>{{ item.creator_name }}</td>
                <td>{{ item.status | tuitionTransferStatus }}</td>
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
      tuition: {
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
