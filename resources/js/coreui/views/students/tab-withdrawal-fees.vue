<template>
  <div class="tab-student-tuition-transfer apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử rút phí</b>        
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th class="width-50">STT</th>
                <th class="width-150">Mã CMS</th>
                <th class="width-150">Tên học sinh</th>
                <th class="width-150">Trung tâm</th>
                <th class="width-150">Sản phẩm</th>
                <th class="width-150">Chương trình</th>
                <th class="width-150">Lớp</th>
                <th class="width-150">Số tiền rút</th>
                <th class="width-150">Số tiền bị khấu trừ</th>
                <th class="width-150">Người duyệt</th>
                <th class="width-150">Ngày duyệt</th>
                <th class="width-150">Người hoàn phí</th>
                <th class="width-150">Ngày hoàn phí</th>
                <th class="width-150">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list" :key="index">
                <td>{{index+1}}</td>
                <td>{{ item.crm_id }} </td>
                <td>{{ item.student_name }}</td>
                <td>{{ item.branch_name }}</td>
                <td>{{ item.product_name }}</td>
                <td>{{ item.program_name }}</td>
                <td>{{ item.class_name }}</td>
                <td>{{ item.refun_amount | formatMoney}}</td>
                <td>{{ item.fee_amount | formatMoney}}</td>
                <td>{{ item.approver_name }}</td>
                <td>{{ item.approved_at }}</td>
                <td>{{ item.refuner_name }}</td>
                <td>{{ item.completed_at }}</td>
                <td>
                    <span v-if="item.status == 0" class="text-warning">Chờ duyệt</span>
                    <span v-else-if="item.status == 1" class="text-success">Đã duyệt</span>
                    <span v-else-if="item.status == 2" class="text-danger">Từ chối</span>
                    <span v-else-if="item.status == 4" class="text-danger">Kế toán từ chối</span>
                    <span v-else>Đã hoàn phí</span>
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
