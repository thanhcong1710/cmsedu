<template>
  <div class="tab-student-payment apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử đóng phí</b>        
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th class="width-50">STT</th>
                <th class="width-150">Mã hợp đồng</th>
                <th class="width-150">Mã phiếu phu</th>
                <th class="width-150">Ngày đóng tiền</th>
                <th class="width-150">Người thu tiền</th>
                <th class="width-150">Tổng tiền cần thu</th>
                <th class="width-150">Tổng tiền đã thu</th>
                <th class="width-150">Công nợ còn lại</th>
                <th class="width-150">Phương thức thanh toán</th>
                <th class="width-150">Lần thu tiền</th>
                <th class="width-150">Hình thức đóng phí</th>
                <th class="width-150">Mã nhập học</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list" :key="index">
                <td>{{ index+1 }}</td>
                <td>{{ item.contract_accounting_id }}</td>
                <td>{{ item.accounting_id }}</td>
                <td>{{ item.charge_date }}</td>
                <td>{{ item.created_user }}</td>
                <td>{{ item.must_charge | formatMoney }} </td>
                <td>{{ item.total | formatMoney }}</td>
                <td>{{ item.debt | formatMoney }}</td>
                <td>{{ item.method | paymentType }}</td>
                <td>{{ item.count }}</td>
                <td>{{ item.payload | payloadType }}</td>
                <td>
                  <div v-if="item.is_recharged">
                    <router-link v-b-tooltip.hover class="link-me" :title="`Xem nội dung bản ghi nhập học mã '${item.contract_code}'`" :to="`/contracts/${item.contract_id}`">
										  {{item.contract_code}}
										</router-link>
                  </div>
                  <div v-else>
                    <router-link v-b-tooltip.hover class="link-me" :title="`Xem nội dung bản ghi tái tục mã '${item.contract_code}'`" :to="`/recharges/${item.contract_id}`">
										  {{item.contract_code}}
										</router-link>
                  </div>
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
    },
    watch: {
      student(data) {

      }
    },
    created() {},
    methods: {},
    components: {}
  }
</script>

<style scoped>
  
</style>
