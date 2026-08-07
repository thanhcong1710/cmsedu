<template>
  <div class="animated fadeIn apax-form">
    <loader :active="processing" :spin="spin" :text="text" :duration="duration"/>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter"></i>
            <b class="uppercase">Bộ lọc/Fillter</b>
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
                >
                  <span slot="noResult">Không tìm thấy trung tâm phù hợp</span>
                </multiselect>
              </div>
              <div class="col-md-3">
                <input
                  class="search-field form-control filter-input"
                  v-model="searchData.keyword"
                  placeholder="Tìm học sinh theo: Tên, Mã học sinh"
                  @input="validate_keyword()"
                >
                <i class="mx-input-min-icon fa fa-search"></i>
              </div>
              <div class="col-md-3">
                <date-picker
                  style="width:100%;"
                  v-model="searchData.dateRange"
                  :lang="datepickerOptions.lang"
                  format="YYYY-MM-DD"
                  range
                  placeholder="Chọn thời gian bắt đầu"
                ></date-picker>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <button class="apax-btn full detail" @click="search()">
              <i class="fa fa-search"></i> Tìm kiếm
            </button>
            <button class="apax-btn full reset" @click="clearSearch()">
              <i class="fa fa-refresh"></i> Lọc lại
            </button>
            <button class="apax-btn full warning" @click="backList()">
              <i class="fa fa-sign-out"></i> Thoát
            </button>
            <button
              class="apax-btn full print"
              @click="exportExcel()"
            >
              <i class="fa fa-file-excel-o"></i> Xuất báo cáo
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
            <strong>BÁO CÁO PHÂN BỔ DOANH THU - Tổng số: {{pagination.total}}</strong>
          </div>

          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
                <tr class="text-sm">
                  <th>STT</th>
                  <th>Mã học sinh</th>
                  <th>Tên học sinh</th>
                  <th>Tên trung tâm</th>
                  <th>Tên khóa học</th>
                  <th>Số tiền thực tế full phí</th>
                  <th>Số tháng học thực tế</th>
                  <th>Ngày bắt đầu</th>
                  <th>Ngày kết thúc</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataReport" :key="index">
                  <td>{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                  <td>{{ item.crm_id }}</td>
                  <td>{{ item.student_name }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.real_amount | formatMoney }}</td>
                  <td>{{ (item.real_months % 1 === 0) ? parseInt(item.real_months) : parseFloat(item.real_months).toFixed(1) }}</td>
                  <td>{{ item.start_date }}</td>
                  <td>{{ item.end_date }}</td>
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
  name: "ReportLgl05",
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
        listBranchs: "",
        keyword: "",
        dateRange: "",
      },
      resource: {
        branchs: [],
      },
      disabledBefore:'',
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
        url: "/api/reports/lgl05",
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
    this.search();
  },
  methods: {
    search(a) {
      this.processing = true
      const data = this.getParamsSearch()
      const link = "/api/reports/lgl05"
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
      var urlApi = "/api/export/lgl05"
      var tokenKey = u.token()
      u.g(urlApi, params, 1, 1)
        .then(response => {
          saveAs(response, "Báo cáo phân bổ doanh thu.xlsx");
          this.processing = false;
        })
        .catch(e => {
          this.processing = false;
        });
    },
    getParamsSearch() {
      const ids = [];
      this.searchData.listBranchs = u.is.obj(this.searchData.listBranchs)
        ? [this.searchData.listBranchs]
        : this.searchData.listBranchs;
      if (this.searchData.listBranchs && this.searchData.listBranchs.length) {
        this.searchData.listBranchs.map(item => {
          ids.push(item.id);
        });
      }
      const from_date = this.searchData.dateRange!='' && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
      const to_date = this.searchData.dateRange!='' && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
      const data = {
        scope: ids,
        limit: this.pagination.limit,
        page: this.pagination.cpage,
        keyword: this.searchData.keyword.trim(),
        from_date: from_date,
        to_date: to_date,
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
    progressMaxClassNumber(bx) {
      return parseInt(bx) === 0 ? 0 : (bx * 2 * 4 + bx * 6 * 2) / 2;
    },
    progressAcsFullfee(a, b) {
      return b == 0 ? 0 : (a / b).toFixed(2);
    },
    progressRP(cl, bx) {
      const resultbx = this.progressMaxClassNumber(bx);
      return resultbx === 0 ? 0 : ((cl / resultbx) * 100).toFixed(2);
    },
    validate_keyword() {
      this.searchData.keyword = this.searchData.keyword.replace(/[~`!#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi, '');
    },
  }
};
</script>
<style scoped>
  .form-control.limit-selection {
    width: 60px !important;
  }
</style>