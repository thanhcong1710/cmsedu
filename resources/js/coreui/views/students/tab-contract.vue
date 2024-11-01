<template>
  <div class="tab-student-contract apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử nhập học</b>
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th class="width-50">STT 11</th>
                <th class="width-100">Ngày thực hiện</th>
                <th class="width-100">Người thực hiện</th>
                <th class="width-100">Trạng thái</th>
                <th class="width-150">Trung tâm</th>
                <th class="width-150">Chương trình</th>
                <th class="width-150">Học lớp</th>
                <th class="width-150">Loại hợp đồng</th>
                <th class="width-100">Gói phí</th>
                <th class="width-100">Số buổi theo gói phí</th>
                <th class="width-100">Số buổi học bổng</th>
                <th class="width-100">Tổng số buổi được học</th>
                <th class="width-150">Giá trị gói</th>
                <th class="width-150">Số tiền phải đóng</th>
                <th class="width-100">Ngày dự kiến học</th>
                <th class="width-100">Ngày dự kiến kết thúc</th>
                <th class="width-100">Số buổi đã bảo lưu bình thường </th>
                <th class="width-100">Số buổi được bảo lưu bình thường</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(contract, index) in list" :key="index">
                <td>{{index+1}}</td>
                <td>{{contract.created_at}}</td>
                <td>{{contract.creator}}</td>
                <td>{{contract.status|contractStatus}}</td>
                <td>{{contract.branch_name}}</td>
                <td>{{contract.program_name}}</td>
                <td>{{contract.class_name}}</td>
                <td>{{contract.type|contractType}}</td>
                <td>{{contract.tuition_fee_name}}</td>
                <td>{{contract.real_sessions}}</td>
                <td>{{contract.bonus_sessions}}</td>
                <td>{{contract.summary_sessions}}</td>
                <td>{{contract.tuition_fee_price|formatMoney}}</td>
                <td>{{contract.must_charge|formatMoney}}</td>
                <td>{{contract.start_date}}</td>
                <td>{{contract.end_date}}</td>
                <td>{{contract.reserved_sessions | formatNumber}}</td>
                <td>{{contract.reservable_sessions | formatNumber}}</td>
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
  import contractType from '../../utilities/filters.js'

  export default {
    props: {
      list: {
        type: Array,
        default: () => []
      },
      data: {
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
    methods: {
       formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    }
    },
    components: {}
  }
</script>

<style scoped>

</style>
