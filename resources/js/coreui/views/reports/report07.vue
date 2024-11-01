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
              <strong>BC07 - Báo cáo phân loại học sinh: Tốt, yếu, cá biệt</strong>
              <div class="card-actions">
                <a
                  href="skype:thanhcong1710?chat"
                  target="_blank"
                >  <small className="text-muted"><i class="fa fa-skype" /></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="row">
                <div class="col-md-6">
                  <BranchSelect v-model="selectedBranches" />
                </div>
                <div class="col-md-6">
                  <ProductSelect v-model="selectedProducts" />
                </div>
              </div>
              <div class="row">
                <div
                  class="col-md-4"
                  style="margin-top: 10px"
                >
                  <ProgramSelect
                    v-model="selectedPrograms"
                    :branch-ids="branch_ids"
                    :product-ids="product_ids"
                  />
                </div>
                <div
                  class="col-md-4"
                  style="margin-top: 10px"
                >
                  <datepicker
                    v-model="from_date"
                    :readonly="false"
                    :lang="lang"
                    :bootstrap-styling="true"
                    placeholder="Chọn ngày bắt đầu"
                    input-class="form-control bg-white"
                    class="time-picker"
                  />
                </div>
                <div
                  class="col-md-4"
                  style="margin-top: 10px"
                >
                  <datepicker
                    v-model="to_date"
                    :lang="lang"
                    input-class="form-control bg-white"
                    placeholder="Chọn ngày kết thúc"
                    class="time-picker"
                    :bootstrap-styling="true"
                  />
                </div>
              </div>
              <div class="row">
                <ActionReport
                  :on-search="viewPrintInfo"
                  :on-export="printReportBC07"
                  :on-clean="resetPrintInfo"
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
              <div class="table-responsive scrollable">
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
                      <th>Lớp</th>
                      <th>Loại đánh giá</th>
                      <th>Giáo viên</th>
                      <th>Người đánh giá</th>
                      <th>Ngày đánh giá</th>
                      <th>Nội dung</th>
                      <th>EC</th>
                      <th>CM</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in results"
                      :key="index"
                    >
                      <td>{{ index+1 }}</td>
                      <td>{{ item.lms_code }}</td>
                      <td>{{ item.effect_code }}</td>
                      <td>{{ item.name_student }}</td>
                      <td>{{ item.branch_name }}</td>
                      <td>{{ item.product_name }}</td>
                      <td>{{ item.program_name }}</td>
                      <td>{{ item.class_name }}</td>
                      <td>{{ item.rank_name }}</td>
                      <td>{{ item.teacher_name }}</td>
                      <td>{{ item.creator_name }}</td>
                      <td>{{ item.rating_date }}</td>
                      <td>{{ item.comment }}</td>
                      <td>{{ item.ec_name }}</td>
                      <td>{{ item.cm_name }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <PagingReport />
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import Datepicker from 'vue2-datepicker'
import moment from 'moment'
import BranchSelect from './common/branch-select'
import ProductSelect from './common/product-select'
import ProgramSelect from './common/program-select'
import ActionReport from './common/action-report'
import PagingReport from './common/PagingReport'
import { getDate } from './common/utils'

export default {
  name      : 'Report07',
  components: {
    Datepicker,
    BranchSelect,
    ProductSelect,
    ProgramSelect,
    ActionReport,
    PagingReport,
  },
  data () {
    return {
      disabledZone        : false,
      disabledRegion      : false,
      disabledProgram     : true,
      disabledSelectBranch: false,
      selectedProducts    : [],
      selectedPrograms    : [],
      products            : [],
      programs            : [],
      program             : '',
      result3             : '',
      printResults        : [],
      selectedBranches    : [],
      lang                : 'en',
      from_date           : '',
      to_date             : '',
      results             : [],
      role_branch         : '',
      selectedBranch_name : '',
      access              : false,
    }
  },
  computed: {
    branch_ids () {
      return this.selectedBranches.map((obj) => obj.id)
    },
    product_ids () {
      return this.selectedProducts.map((obj) => obj.id)
    },
  },
  created () {
    u.a().get(`/api/reports/branches`).then((response) => {
      this.branches = response.data
    })
    this.checkRole()
    this.getDefaultDate()
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
          this.selectedBranch_name  = this.branches[0].name
          const selected_branch_id  = this.branches[0].id
          if (selected_branch_id)
            this.selectedBranches.push(selected_branch_id)
        }
        this.access = true
      })
    },
    getParamsSearch () {
      return {
        branch_ids : this.selectedBranches.map((obj) => obj.id),
        product_ids: this.selectedProducts.map((obj) => obj.id),
        program_ids: this.selectedPrograms.map((obj) => obj.id),
        limit      : this.pagination.limit,
        page       : this.pagination.cpage,
        from_date  : getDate(this.from_date),
        to_date    : getDate(this.to_date),
      }
    },
    viewPrintInfo () {
      u.a().post(`/api/reports/form-07`, this.getParamsSearch()).then((response) => {
        this.results = response.data
      })
    },
    resetPrintInfo () {
      this.selectedBranches = []
      this.selectedProducts = []
      this.selectedPrograms = []
    },
    printReportBC07 () {
      // this.showBC15 = false;
      // var br =''
      // for(var t in this.selectedBranches){
      //   br += this.selectedBranches[t]+',';
      // }
      // br = br.slice(0, -1)
      //
      // var pd = ''
      // for (var c in this.selectedProducts){
      //   pd += this.selectedProducts[c].id+',';
      // }
      // pd = pd.slice(0, -1)
      //
      // var pro = ''
      // for (var p in this.selectedPrograms){
      //   pro += this.selectedPrograms[p].id+','
      // }
      // pro = pro.slice(0,-1)
      // let fd = moment(this.from_date, "DD/MM/YYYY").format("YYYY-MM-DD");
      // let td = moment(this.to_date, "DD/MM/YYYY").format("YYYY-MM-DD");
      // console.log(`${br}, ${pd}, ${pro}`);
      //
      // br = br? br : '_'
      // pd = pd ? pd : '_'
      // pro = pro? pro : '_'
      // fd = fd ? fd : "_";
      // td = td ? td : "_";
      // var p =`/api/exel/print-report-bc07/${br}/${pd}/${pro}/${fd}/${td}`
      // var extraWindow = window.open(p)
      //  window.open(p, '_blank')
      // if(extraWindow){
      //     extraWindow.location.reload();
      // }
    },
    getDefaultDate () {
      const from_date =  new Date()
      // var lastDay = new Date(y, m + 1, 0);
      this.from_date = moment(from_date, 'DD/MM/YYYY').format('YYYY-MM-01')
      // const lastDay  = moment(lastDay).format('YYYY-MM-DD')
      // let lastDay = new Date(from_date.getFullYear(), from_date.getMonth() + 1, 0);
      const endOfMonth = moment().endOf('month').format('YYYY-MM-DD')
      this.to_date     = endOfMonth
    },
  },
}
</script>

<style scoped>

.mt-30{
  margin-top: 30px;
}
.selected-button{
  margin: auto;
  margin-top: 30px;
  margin-bottom: 30px;
}
.close{
  cursor: pointer;
  margin-top: -10px;
}

.time-picker{

}
.vdp-datepicker{
  margin-left: 6px;
}
</style>
