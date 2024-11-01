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
              <div class="col-md-9">
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
                  id="input_keyword"
                  name="search[keyword]"
                  class="filter-selection search-field form-control filter-input"
                  v-model="searchData.keyword"
                  placeholder="Tìm học sinh theo: Tên, Mã LMS/Effect"
                >
                <i class="mx-input-min-icon fa fa-search"></i>
              </div>
            </div>
            <div class="row" style="margin-top:10px;">
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
              <div class="col-md-3">
                <multiselect
                  placeholder="Lọc theo kết quả tái tục (Tất Cả)"
                  select-label="Chọn loại kết quả"
                  v-model="searchData.typeResult"
                  :options="resource.resultsList"
                  label="name"
                  :close-on-select="false"
                  :hide-selected="true"
                  :multiple="false"
                  :searchable="true"
                  track-by="id"
                >
                  <span slot="noResult">Không tìm thấy kết quả phù hợp</span>
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
                  placeholder="Chọn EC (Tất Cả)"
                  select-label="Chọn một EC"
                  v-model="searchData.listEcs"
                  :options="resource.ecs"
                  label="full_name"
                  :close-on-select="false"
                  :hide-selected="true"
                  :multiple="true"
                  :searchable="true"
                  track-by="id"
                >
                  <span slot="noResult">Không tìm thấy EC phù hợp</span>
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
            <button class="apax-btn full remove" @click="backList()">
              <i class="fa fa-sign-out"></i> Thoát
            </button>
            <button class="apax-btn full print" @click="exportExcel()">
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
            <strong>BC02A1 BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ - <span> Thành công/Tổng số: {{success_total}}/{{pagination.total}}</span></strong> 
          </div>
          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
                <tr class="text-sm">
                  <th>STT</th>
                  <th>Mã CMS</th>
                  <th>Mã kế toán</th>
                  <th>Tên học sinh</th>
                  <th>Trung tâm</th>
                  <th>Sản phẩm</th>
                  <th>Lớp Học</th>
                  <th>Ngày đến hạn tái tục</th>
                  <th>Kết quả</th>
                  <th>Gói tái phí</th>
                  <th>Số tiền tái phí</th>
                  <th>Tên EC</th>
                  <th>Mã EC</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataReport" :key="index">
                  <td
                    class="text-center"
                  >{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                  <td>{{ item.crm_id }}</td>
                  <td>{{ item.accounting_id }}</td>
                  <td>{{ item.student_name }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.class_name }}</td>
                  <td>{{ item.last_date }}</td>
                  <td>{{ item.status_title }}</td>
                  <td><span v-if="item.status==1">{{ item.tuition_fee_name }}</span></td>
                  <td><span v-if="item.status==1">{{ item.renew_amount | formatCurrency2 }}</span></td>
                  <td>{{ item.ec_name }}</td>
                  <td>{{ item.ec_hrm_id }}</td>
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
              class="form-control paging-limit"
              v-model="pagination.limit"
              @change="search()"
              style=" height: 30px !important;border: 0.5px solid #dfe3e6 !important;"
            >
              <option
                v-for="(item, index) in pagination.limitSource"
                v-bind:value="item"
                v-bind:key="index"
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
import moment from "moment";
import saveAs from "file-saver";
import Multiselect from "vue-multiselect";
import loader from "../../components/Loading";

export default {
  name: "Report01b1",
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
      processing: false,
      spin: "mini",
      duration: 500,
      text: "Đang tải dữ liệu...",
      session: u.session(),
      searchData: {
        name: "",
        keyword: "",
        dateRange: moment().format("YYYY-MM"),
        ec_name: "",
        cm_name: "",
        branchIds: [],
        productIds: [],
        listBranchs: "",
        listProducts: "",
        listCms: "",
        listEcs: ""
      },
      resource: {
        branchs: [],
        products: [],
        resultsList: [
          { id: 1, name: "Thành Công" },
          { id: 2, name: "Thất Bại" }
        ],
        ecs: []
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
        url: "",
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
      success_total: 0
    };
    return model;
  },
  created() {
    const sessions = u.session();
    const session = sessions.user;
    this.resource.products = sessions.info.products;
    let selectionList = session.branches;
    if (session.regions && session.regions.length) {
      selectionList = session.regions.concat(selectionList);
    }
    if (session.zones && session.zones.length) {
      selectionList = session.zones.concat(selectionList);
    }
    this.resource.branchs = selectionList;
    this.searchData.listBranchs = session.branches[0];

    u.g(`/api/ec/branch/${session.branches[0].id}/?status=1`)
      .then(response => {
        this.resource.ecs = response;
      });

    this.search();
  },
  methods: {
    search() {
      this.processing = true
      const data = this.getParamsSearch();
      const link = "/api/reports/form-01b1";
      const params = {
        scope: data.scope,
        page: data.page,
        keyword: data.keyword,
        products: data.products,
        ecs: data.ecs,
        limit: data.limit,
        type: data.type,
        date: data.date
      };
      u.p(link, params, 1)
        .then(response => {
          this.dataReport = response.list;
          this.success_total = response.success_total;
          this.pagination.spage = response.paging.spage;
          this.pagination.ppage = response.paging.ppage;
          this.pagination.npage = response.paging.npage;
          this.pagination.lpage = response.paging.lpage;
          this.pagination.cpage = response.paging.cpage;
          this.pagination.total = response.paging.total;
          this.pagination.limit = response.paging.limit;
          this.processing = false
        })
        .catch(e => {
          u.log("Exeption", e);
          this.processing = false
        });
    },
    exportExcel() {
      this.processing = true
      var params = this.getParamsSearch();
      var urlApi = "/api/export/report01b1";
      var tokenKey = u.token();
      if ([686868, 7777777].indexOf(u.session().user.role_id) > -1) {
        params.phone = 1
      }
      u.g(urlApi, params, 1, 1)
        .then(response => {
          saveAs(response, "BC02A1 BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ.xlsx");
          this.processing = false
        })
        .catch(e => {
          this.processing = false
        });
    },
    getParamsSearch() {
      const ids = [];
      const pids = [];
      const ecids = [];
      this.searchData.listBranchs = u.is.obj(this.searchData.listBranchs)
        ? [this.searchData.listBranchs]
        : this.searchData.listBranchs;
      if (this.searchData.listBranchs.length) {
        this.searchData.listBranchs.map(item => {
          ids.push(item.id);
        });
      }
      if (this.searchData.listProducts.length) {
        this.searchData.listProducts.map(item => {
          pids.push(item.id);
        });
      }
      if (this.searchData.listEcs.length) {
        this.searchData.listEcs.map(item => {
          ecids.push(item.id);
        });
      }
      const data = {
        scope: ids,
        page: this.pagination.cpage,
        limit: this.pagination.limit,
        keyword: this.searchData.keyword,
        products: pids,
        ecs: ecids,
        type: this.searchData.typeResult ? this.searchData.typeResult.id : 0,
        phone: 0,
        date: this.getDate(this.searchData.dateRange)
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
    onSelectBranch(data) {
      this.searchData.listBranchs = "";
      u.g(`/api/ec/branch/${data.id}?status=1`)
        .then(response => {
          this.resource.ecs = response;
        });
    }
  }
};
</script>
<style scoped>
#input_keyword {
  width: 100%;
}
</style>
