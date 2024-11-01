<template>
  <div class="animated fadeIn apax-form">
    <loader :active="processing" :spin="spin" :text="text" :duration="duration"/>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter"></i>
            <b class="uppercase">Bộ lọc</b>
          </div>
          <div class="content-detail">
            <div class="row">
              <div class="col-md-3">
                <multiselect
                  placeholder="Chọn trung tâm"
                  select-label="Chọn một trung tâm"
                  v-model="searchData.listBranchs"
                  :options="resource.branchs"
                  label="name"
                  :close-on-select="false"
                  :hide-selected="true"
                  :multiple="true"
                  :searchable="true"
                  track-by="id"
                  @select="onSelectBranch"
                >
                  <span slot="noResult">Không tìm thấy trung tâm phù hợp</span>
                </multiselect>
              </div>
              <div class="col-md-3">
                <input
                  class="search-field form-control filter-input"
                  v-model="searchData.keyword"
                  placeholder="Tìm học sinh theo: Tên, Mã LMS"
                  @input="validate_keyword()"
                >
                <i class="mx-input-min-icon fa fa-search"></i>
              </div>
              <div class="col-md-3">
                <multiselect
                  placeholder="Chọn CM (Tất Cả)"
                  select-label="Chọn một CM"
                  v-model="searchData.listCms"
                  :options="resource.cms"
                  label="full_name"
                  :close-on-select="false"
                  :hide-selected="true"
                  :multiple="true"
                  :searchable="true"
                  track-by="id"
                >
                  <span slot="noResult">Không tìm thấy CM phù hợp</span>
                </multiselect>
              </div>
              <div class="col-md-3">
                <date-picker
                  style="width:100%;"
                  v-model="searchData.dateRange"
                  @change="search()"
                  :lang="datepickerOptions.lang"
                  format="YYYY-MM"
                  type="month"
                  placeholder="Chọn tháng lọc"
                ></date-picker>
              </div>
              <div class="col-md-3">
                <multiselect
                  placeholder="Chọn gói sản phẩm (Tất Cả)"
                  select-label="Chọn một gói sản phẩm"
                  v-model="searchData.listProducts"
                  :options="resource.products"
                  label="name"
                  :close-on-select="false"
                  :hide-selected="true"
                  :multiple="true"
                  :searchable="true"
                  track-by="id"
                >
                  <span slot="noResult">Không tìm thấy sản phẩm phù hợp</span>
                </multiselect>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <button class="apax-btn full detail" @click="search()">
              <i class="fa fa-search"></i> Tìm Kiếm
            </button>
            <button class="apax-btn full reset" @click="clearSearch()">
              <i class="fa fa-refresh"></i> Lọc Lại
            </button>
            <button class="apax-btn full warning" @click="backList()">
              <i class="fa fa-sign-out"></i> Thoát
            </button>
            <button
              class="apax-btn full print"
              @click="exportExcel()"
            >
              <i class="fa fa-file-excel-o"></i> Xuất Báo Cáo
            </button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-file-text"></i>
            <strong>Báo cáo chi tiết học sinh Pending - Tổng số: {{pagination.total}}</strong>
          </div>

          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
                <tr class="text-sm">
                  <th>STT</th>
                  <th>Trung tâm</th>
                  <th>Mã CMS</th>
                  <th>Mã kế toán</th>
                  <th>Họ tên học sinh</th>
                  <th>Sản phẩm</th>
                  <th>Mã NV CM</th>
                  <th>Họ tên CM</th>
                  <th>Tên gói phí</th>
                  <th>Số buổi</th>
                  <th>Ngày dự kiến bắt đầu</th>
                  <th>Ngày dự kiến kết thúc</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataReport" :key="index">
                  <td>{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.crm_id }}</td>
                  <td>{{ item.accounting_id }}</td>
                  <td>{{ item.student_name }}</td>
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.hrm_id }}</td>
                  <td>{{ item.cm_name }}</td>
                  <td>{{item.tuition_fee_name}}</td>
                  <td>{{item.summary_sessions}}</td>
                  <td>{{item.start_date}}</td>
                  <td>{{item.end_date}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="text-center paging">
            <nav aria-label="Page navigation">
              <paging
                :rootLink="pagination.url"
                :id="pagination.id"
                :listStyle="pagination.style"
                :customClass="pagination.class"
                :firstPage="pagination.spage"
                :previousPage="pagination.ppage"
                :nextPage="pagination.npage"
                :lastPage="pagination.lpage"
                :currentPage="pagination.cpage"
                :pagesItems="pagination.total"
                :pagesLimit="pagination.limit"
                :pageList="pagination.pages"
                :routing="changePage"
              ></paging>
            </nav>
            <select
              class="form-control limit-selection"
              v-model="pagination.limit"
              @change="search()"
            >
              <option
                v-for="(item, index) in pagination.limitSource"
                :value="item"
                :key="index"
              >{{ item }}</option>
            </select>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import paging from "../../components/Pagination";
import u from "../../utilities/utility";
import axios from "axios";
import saveAs from "file-saver";
import Multiselect from "vue-multiselect";
import loader from "../../components/Loading";

export default {
  name: "Report02a",
  components: {
    DatePicker,
    paging,
    axios,
    saveAs,
    Multiselect,
    loader
  },
  data() {
    const model = {
      session: u.session(),
      searchData: {
        name: "",
        dateRange: "",
        listBranchs: "",
        keyword: "",
        listCms: "",
        listProducts: "",
      },
      resource: {
        branchs: [],
        cms: [],
        products: [],
      },
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: new Date(),
        lang: {
          days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          months: [
            "Tháng 1",
            "Tháng 2",
            "Tháng 3",
            "Tháng 4",
            "Tháng 5",
            "Tháng 6",
            "Tháng 7",
            "Tháng 8",
            "Tháng 9",
            "Tháng 10",
            "Tháng 11",
            "Tháng 12"
          ],
          pickers: ["", "", "7 ngày trước", "30 ngày trước"]
        }
      },
      dataReport: [],
      pagination: {
        url: "/api/reports/form-02a",
        id: "",
        style: "line",
        class: "",
        spage: 1,
        ppage: 1,
        npage: 0,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        limitSource: [10, 20, 30, 40, 50],
        pages: []
      },
      processing: false,
      spin: "mini",
      duration: 500,
      text: "Đang tải dữ liệu..."
    };
    return model;
  },
  created() {
    const session = u.session().user;
    let selectionList = session.branches;
    if (session.regions && session.regions.length) {
      selectionList = session.regions.concat(selectionList);
    }
    if (session.zones && session.zones.length) {
      selectionList = session.zones.concat(selectionList);
    }
    this.resource.branchs = selectionList;
    this.searchData.listBranchs = session.branches[0];
    this.searchData.dateRange = new Date();
    this.resource.products = u.session().info.products;
    u.g(`/api/cm/branch/${session.branches[0].id}?status=1`)
      .then(response => {
        this.resource.cms = response;
      });
    this.search();
  },
  methods: {
    search(a) {
      this.processing = true
      const data = this.getParamsSearch()
      const link = "/api/reports/form-02a"
      u.p(link, data, 1)
        .then(response => {
          this.dataReport = response.list;
          this.pagination.spage = response.paging.spage;
          this.pagination.ppage = response.paging.ppage;
          this.pagination.npage = response.paging.npage;
          this.pagination.lpage = response.paging.lpage;
          this.pagination.cpage = response.paging.cpage;
          this.pagination.total = response.paging.total;
          this.pagination.limit = response.paging.limit;
          this.processing = false;
        })
        .catch(e => {
          u.log("Exeption", e)
          this.processing = false
        })
    },
    exportExcel() {
      this.processing = true
      var params = this.getParamsSearch()
      var urlApi = "/api/export/report02a"
      var tokenKey = u.token()
      u.g(urlApi, params, 1, 1)
        .then(response => {
          saveAs(response, "Báo cáo chi tiết học sinh Pending.xlsx");
          this.processing = false;
        })
        .catch(e => {
          this.processing = false;
        });
    },
    getParamsSearch() {
      const ids = [];
      const cmids = [];
      const pids = [];
      this.searchData.listBranchs = u.is.obj(this.searchData.listBranchs)
        ? [this.searchData.listBranchs]
        : this.searchData.listBranchs;
      if (this.searchData.listBranchs.length) {
        this.searchData.listBranchs.map(item => {
          ids.push(item.id);
        });
      }
      if (this.searchData.listCms.length) {
        this.searchData.listCms.map(item => {
          cmids.push(item.id);
        });
      }
      if (this.searchData.listProducts.length) {
        this.searchData.listProducts.map(item => {
          pids.push(item.id);
        });
      }
      const data = {
        scope: ids,
        cms: cmids,
        limit: this.pagination.limit,
        page: this.pagination.cpage,
        date: this.getDate(this.searchData.dateRange),
        keyword: this.searchData.keyword.trim(),
        products: pids,
      };
      return data;
    },
    clearSearch() {
      location.reload();
    },
    changePage(link) {
      const info = link
        .toString()
        .substr(this.pagination.url.length)
        .split("/");
      const page = info.length > 1 ? info[1] : 1;
      this.pagination.cpage = parseInt(page);
      this.search();
    },
    getDate(date) {
      let day =
        date instanceof Date && !isNaN(date.valueOf()) ? date : new Date();
      if (day instanceof Date && !isNaN(day.valueOf())) {
        var year = day.getFullYear();
        var month = (day.getMonth() + 1).toString();
        var formatedMonth = month.length === 1 ? "0" + month : month;
        return `${year}-${formatedMonth}`;
      }
      return "";
    },
    backList() {
      this.$router.push("/forms");
    },
    validate_keyword() {
      this.searchData.keyword = this.searchData.keyword.replace(/[~`!#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi, '');
    },
    onSelectBranch(data) {
      u.g(`/api/cm/branch/${data.id}?status=1`)
        .then(response => {
          this.resource.cms = response;
        });
    },
  }
};
</script>
<style scoped>
  .form-control.limit-selection {
    width: 60px !important;
  }
</style>