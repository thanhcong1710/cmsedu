<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
            header-tag="header"
            footer-tag="footer"
          >
            <div slot="header">
              <strong>BC15 - Báo cáo quá hạn cọc</strong>
              <div class="card-actions">
                <a
                  href="skype:thanhcong1710?chat"
                  target="_blank"
                >
                  <small className="text-muted"><i class="fa fa-skype" /></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="row">
                <div class="col-md-4">
                  <BranchSelect v-model="selectedBranches" />
                </div>
                <div class="col-md-4">
                  <ProductSelect v-model="selectedProducts" />
                </div>
                <div class="col-md-4">
                  <ProgramSelect
                    v-model="selectedPrograms"
                    :product-ids="product_ids"
                    :branch-ids="branch_ids"
                  />
                </div>
              </div>
              <div class="row">
                <ActionReport
                  :on-search="viewPrintInfo"
                  :on-clean="resetPrintInfo"
                  :on-export="printReportBC15"
                />
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-card
            header-tag="header"
            footer-tag="footer"
          >
            <div slot="header">
              <strong>Danh sách</strong>
            </div>
            <div class="content-detail">
              <div class="table-responsive scrollable bg">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="text-sm">
                      <th>STT</th>
                      <th>Mã LMS</th>
                      <th>Mã EFFECT</th>
                      <th>Tên học sinh</th>
                      <th>Trung tâm</th>
                      <th>Sản phẩm</th>
                      <th>Chương trình</th>
                      <th>Gói học phí</th>
                      <th>Giá niêm yết</th>
                      <th>Tiền phải đóng</th>
                      <th>Tiền đã đóng</th>
                      <th>Công nợ</th>
                      <th>Ngày thu tiền</th>
                      <th>Chờ hoàn phí</th>
                      <th>EC</th>
                      <th>CM</th>
                    </tr>
                  </thead>
                  <tbody
                    v-for="(item, index) in results"
                    :key="index"
                  >
                    <tr
                      v-if="item.left_dates < 0"
                      class="bg_red"
                    >
                      <td>{{ index+1 }}</td>
                      <td>{{ item.stu_id }}</td>
                      <td>{{ item.accounting_id }}</td>
                      <td>{{ item.student_name }}</td>
                      <td>{{ item.branch_name }}</td>
                      <td>{{ item.product_name }}</td>
                      <td>{{ item.programs_name }}</td>
                      <td>{{ item.tuition_fee_name }}</td>
                      <td>{{ item.tuition_fee_price|formatCurrency2 }}</td>
                      <td>{{ item.must_charge|formatCurrency2 }}</td>
                      <td>{{ item.total_charged|formatCurrency2 }}</td>
                      <td>{{ item.debt_amount|formatCurrency2 }}</td>
                      <td>{{ item.payment_date }}</td>
                      <td>{{ item.left_dates }}</td>
                      <td>{{ item.ec_name }}</td>
                      <td>{{ item.cm_name }}</td>
                    </tr>
                    <tr v-else>
                      <td>{{ index+1 }}</td>
                      <td>{{ item.stu_id }}</td>
                      <td>{{ item.accounting_id }}</td>
                      <td>{{ item.student_name }}</td>
                      <td>{{ item.branch_name }}</td>
                      <td>{{ item.product_name }}</td>
                      <td>{{ item.programs_name }}</td>
                      <td>{{ item.tuition_fee_name }}</td>
                      <td>{{ item.tuition_fee_price|formatCurrency2 }}</td>
                      <td>{{ item.must_charge|formatCurrency2 }}</td>
                      <td>{{ item.total_charged|formatCurrency2 }}</td>
                      <td>{{ item.debt_amount|formatCurrency2 }}</td>
                      <td>{{ item.payment_date }}</td>
                      <td>{{ item.left_dates }}</td>
                      <td>{{ item.ec_name }}</td>
                      <td>{{ item.cm_name }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <PagingReport
                :on-change="viewPrintInfo"
                v-model="pagination"
                :total="pagination.total"
              />
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import BranchSelect from './common/branch-select'
import ActionReport from './common/action-report'
import ProductSelect from './common/product-select'
import ProgramSelect from './common/program-select'
import PagingReport from './common/PagingReport'

export default {
  name      : 'Report15',
  components: {
    BranchSelect,
    ActionReport,
    ProductSelect,
    ProgramSelect,
    PagingReport
  },
  data () {
    return {
      selectedProducts: [],
      selectedPrograms: [],
      pagination      : {},
      branches        : [],
      selectedBranches: [],
      results         : [],

    }
  },
  computed: {
    branch_ids () {
      return this.selectedBranches.map((obj) => obj.id)
    },
    product_ids () {
      return this.selectedProducts.map((obj) => obj.id)
    },
    program_ids () {
      return this.selectedPrograms.map((obj) => obj.id)
    },
  },
  created () {
    u.a().get(`/api/reports/branches`).then((response) => {
      this.branches = response.data
    })

    this.checkRole()
  },
  methods: {
    checkRole () {
      u.a().get(`/api/reports/check-role`).then((response) => {
        const rs = response.data
        if (rs === 1) {
          this.role_branch          = true
          this.disabledSelectBranch = false
        } else {
          this.role_branch          = false
          this.disabledSelectBranch = true
          this.select_branch_name   = this.branches[0].name
          const selected_branch_id  = this.branches[0].id
          if (selected_branch_id)
            this.selectedBranches.push(selected_branch_id)
        }
        this.viewPrintInfo()
      })
    },
    viewPrintInfo () {
      const params  = this.getParamsSearch()
      u.apax.$emit('apaxLoading', true)
      u.a().post(`/api/reports/form-15`, params).then((response) => {
        this.results          = response.data.data
        this.pagination.total = response.data.total
        u.apax.$emit('apaxLoading', false)
      })
    },
    resetPrintInfo () {
      this.selectedBranches = []
      this.selectedProducts = []
      this.selectedPrograms = []
    },
    getParamsSearch () {
      return {
        branch_ids : this.branch_ids,
        product_ids: this.product_ids,
        program_ids: this.program_ids,
        limit      : this.pagination.limit,
        page       : this.pagination.cpage,
      }
    },
    printReportBC15 () {
      const params = this.getParamsSearch()
      const p      = `/api/exel/print-report-bc15/${params.branch_ids.toString()}/${params.product_ids.toString()}/${params.program_ids.toString()}`
      window.open(p, '_blank')
    },
  },
}
</script>

<style scoped>
</style>
