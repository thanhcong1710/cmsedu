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
              <strong>BC09 - Báo cáo học sinh withdraw</strong>
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
                <div class="col-md-12">
                  <BranchSelect v-model="infoSearch.listBranchs"/>
                </div>
              </div>
              <div
                class="row"
              >
                <div
                  class="col-md-6"
                  style="margin-top:10px;"
                >
                  <ProductSelect v-model="infoSearch.listProducts" />
                </div>
                <div
                  class="col-md-6"
                  style="margin-top:10px;"
                >
                  <datepicker
                    style="width:100%;"
                    v-model="infoSearch.dateRange"
                    @change="viewPrintInfo"
                    :clearable="true"
                    range
                    :lang="datepickerOptions.lang"
                    format="YYYY-MM-DD"
                    placeholder="Chọn thời gian từ ngày - đến ngày"
                  />
                </div>
              </div>
              <div class="row">
                <ActionReport
                  :on-search="viewPrintInfo"
                  :on-clean="resetPrintInfo"
                  :on-export="printReportBC09"
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
                      <th>Ngày withdraw</th>
                      <th>Loại withdraw</th>
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
                      <td>{{ item.lms_id }}</td>
                      <td>{{ item.accounting_id }}</td>
                      <td>{{ item.name }}</td>
                      <td>{{ item.branch_name }}</td>
                      <td>{{ item.product_name }}</td>
                      <td>{{ item.program_name }}</td>
                      <td>{{ item.class_name }}</td>
                      <td>{{ item.withdraw_date|formatDate }}</td>
                      <td>{{ item.withdraw_reason }}</td>
                      <td>{{ item.ec_name }}</td>
                      <td>{{ item.cm_name }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="text-center paging">
              <nav aria-label="Page navigation">
                <paging
                  :root-link="pagination.url"
                  :id="pagination.id"
                  :list-style="pagination.style"
                  :custom-class="pagination.class"
                  :first-page="pagination.spage"
                  :previous-page="pagination.ppage"
                  :next-page="pagination.npage"
                  :last-page="pagination.lpage"
                  :current-page="pagination.cpage"
                  :pages-items="pagination.total"
                  :pages-limit="pagination.limit"
                  :page-list="pagination.pages"
                  :routing="changePage"
                />
              </nav>
              <select
                class="form-control paging-limit"
                v-model="pagination.limit"
                @change="viewPrintInfo"
                style="width: auto;height: 30px !important;border: 0.5px solid #dfe3e6 !important;padding-top: 5px;"
              >
                <option
                  v-for="(item, index) in pagination.limitSource"
                  :value="item"
                  :key="index"
                >
                  {{ item }}
                </option>
              </select>
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import datepicker from 'vue2-datepicker'
import paging from '../../components/Pagination'
import multiselect from 'vue-multiselect'
import ActionReport from './common/action-report'
import ProductSelect from './common/product-select'
import BranchSelect from './common/branch-select'

export default {
  name      : 'Report09',
  components: {
    ProductSelect,
    BranchSelect,
    datepicker,
    multiselect,
    paging,
    ActionReport,
  },
  data () {
    return {
      resource: {
        branchs : [],
        products: [],
      },
      infoSearch: {
        dateRange   : '',
        listBranchs : [],
        listProducts: [],
      },
      datepickerOptions: {
        closed : true,
        value  : '',
        minDate: new Date(),
        lang   : {
          days: [
            'CN',
            'T2',
            'T3',
            'T4',
            'T5',
            'T6',
            'T7',
          ],
          months: [
            'Tháng 1',
            'Tháng 2',
            'Tháng 3',
            'Tháng 4',
            'Tháng 5',
            'Tháng 6',
            'Tháng 7',
            'Tháng 8',
            'Tháng 9',
            'Tháng 10',
            'Tháng 11',
            'Tháng 12',
          ],
          pickers: [
            '',
            '',
            '7 ngày trước',
            '30 ngày trước',
          ],
        },
      },
      columns: [
        {
          label   : 'Trung tâm',
          field   : 'name',
          sortable: true,
        },
        {
          label: 'Vùng',
          field: 'age',
          type : 'number',
        },
        {
          label: 'Khu vực',
          field: 'age',
          type : 'number',
        },
      ],
      lang      : 'en',
      results   : [],
      pagination: {
        url        : '',
        id         : '',
        style      : 'line',
        class      : '',
        spage      : 1,
        ppage      : 1,
        npage      : 0,
        lpage      : 1,
        cpage      : 1,
        total      : 0,
        limit      : 20,
        limitSource: [
          10,
          20,
          30,
          40,
          50,
        ],
        pages: [],
      },
    }
  },
  created () {
    this.viewPrintInfo()
  },
  methods: {
    changePage (link) {
      const info            = link.toString().split('/')
      const page            = info.length > 1 ? info[1] : 1
      this.pagination.cpage = parseInt(page)
      this.viewPrintInfo()
    },
    viewPrintInfo () {
      $('.mx-datepicker-popup').hide()
      this.processing = true
      const data      = this.getParamsSearch()
      const urlApi    = `/api/reports/form-09`
      u.apax.$emit('apaxLoading', true)
      u.a()
        .post(urlApi, data)
        .then((response) => {
          this.results          = response.data.list
          this.pagination.total = response.data.total
          this.pagination.lpage = Math.ceil(
            this.pagination.total / this.pagination.limit
          )
          this.pagination.ppage = this.pagination.cpage > 0 ? this.pagination.cpage - 1 : 0
          this.pagination.npage = this.pagination.cpage + 1
          u.apax.$emit('apaxLoading', false)
        })
        .catch((e) => {
        })
    },
    resetPrintInfo () {
      this.infoSearch = {
        dateRange   : '',
        listBranchs : [],
        listProducts: [],
      }
    },
    printReportBC09 () {
      const params = this.getParamsSearch()
      const br     = _.get(params, 'branches', []).toString() || '_'
      const pd     = _.get(params, 'products', []).toString() || '_'
      const pro    = '_'
      const fd     = this.getDate(this.infoSearch.dateRange[0]) || '_'
      const td     = this.getDate(this.infoSearch.dateRange[1]) || '_'
      const p      = `/api/exel/print-report-bc09/${br}/${pd}/${pro}/${fd}/${td}`
      window.open(p, '_blank')
      if (extraWindow)
        extraWindow.location.reload()
    },
    getDate (date) {
      if (date instanceof Date && !isNaN(date.valueOf())) {
        const year          = date.getFullYear()
        const month         = (date.getMonth() + 1).toString()
        const formatedMonth = month.length === 1 ? `0${month}` : month
        const day           = date.getDate().toString()
        const formatedDay   = day.length === 1 ? `0${day}` : day
        return `${year}-${formatedMonth}-${formatedDay}`
      }
      return ''
    },
    getParamsSearch () {
      return {
        branches : this.infoSearch.listBranchs.map((obj) => obj.id),
        products : this.infoSearch.listProducts.map((obj) => obj.id),
        limit    : this.pagination.limit,
        page     : this.pagination.cpage,
        from_date: this.getDate(this.infoSearch.dateRange[0]),
        to_date  : this.getDate(this.infoSearch.dateRange[1]),
      }
    },
  },
}
</script>

<style>
  .content-loading{
    position: fixed;
  }
</style>