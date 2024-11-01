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
                <label class="filter-label control-label">
                  Trung tâm tạo
                </label>
                <br>
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
                <label class="filter-label control-label">
                  Thời gian đặt lịch checkin
                </label>
                <br>
                <date-picker
                  style="width:100%;"
                  v-model="searchData.dateRange"
                  :lang="datepickerOptions.lang"
                  format="YYYY-MM-DD"
                  :not-before="datepickerOptions.min_date"
                  range
                  placeholder="Chọn thời gian tìm kiếm"
                ></date-picker>
              </div>
              <div class="col-md-3">
                <label class="filter-label control-label">
                  Thời gian khởi tạo
                </label>
                <br>
                <date-picker
                  style="width:100%;"
                  v-model="searchData.dateRangeInit"
                  :lang="datepickerOptions.lang"
                  format="YYYY-MM-DD"
                  :not-before="datepickerOptions.min_date"
                  range
                  placeholder="Chọn thời gian tìm kiếm"
                ></date-picker>
              </div>
              <div class="col-md-3">
                <label class="filter-label control-label">
                  Từ khóa
                </label>
                <input
                  class="search-field form-control filter-input"
                  v-model="searchData.keyword"
                  placeholder="Tìm học sinh theo: Tên, Mã CRM, SĐT"
                >
                <i class="mx-input-min-icon fa fa-search"></i>
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
            <strong>BÁO CÁO CONFIRM HỌC SINH</strong>
          </div>

          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
                <tr class="text-sm">
                  <th>STT</th>
                  <th>Mã CRM</th>
                  <th>Tên học sinh</th>
                  <th>Trung tâm tạo</th>
                  <th>TVV tạo</th>
                  <th>Thời gian tạo</th>
                  <th>Thời gian checkin</th>
                  <th>Trung tâm checkin</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataReport" :key="index">
                  <td>{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                  <td>{{ item.crm_id }}</td>
                  <td>{{ item.name }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.checkin_by }}</td>
                  <td>{{ item.created_at }}</td>
                  <td>{{ item.checkin_at }}</td>
                  <td>{{ item.checkin_by_branch_name }}</td>
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
import SearchBranch from "../../components/BranchSelection"

export default {
  name: "Report02b",
  components: {
    DatePicker,
    paging,
    axios,
    saveAs,
    Multiselect,
    loader,
    SearchBranch,
  },
  data() {
    const model = {
      title_report:'',
      session: u.session(),
      searchData: {
        listBranchs: "",
        keyword: "",
        report_month:"",
        dateRange: "",
        dateRangeInit: "",
      },
      sources_list:[],
      resource: {
        branchs: [],
        report_months: []
      },
      datepickerOptions: {
        closed: true,
        value: "",
        min_date: '2022-12-01',
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
      resource: {
        branchs: [],
      },
      pagination: {
        url: "/api/reports/r12",
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
      summary:{},
      processing: false,
      spin: "mini",
      duration: 500,
      text: "Đang tải dữ liệu...",
      data: {
        branches:[],
        branch: {
          id: 0
        },
        teachers: [],
        classes: [],
        students: [],
        temp: {
          branch_id: 0,
          teacher_id: "-1",
          class_id: "-1",
          keyword:"",
        },
        report_week_id:0,
        report_weeks:[],
      },
      role_36:false,
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
    if(u.session().user.id == 323){
      this.resource.branchs = u.session().info.branches
    }
    this.search();
  },
  methods: {
    search(a) {
      this.processing = true
      const data = this.getParamsSearch()
      const link = "/api/reports/r12"
      u.p(link, data, 1)
        .then(response => {
          this.dataReport = response.list;
          this.title_report = response.title_report
          this.pagination.spage = response.paging.spage;
          this.pagination.ppage = response.paging.ppage;
          this.pagination.npage = response.paging.npage;
          this.pagination.lpage = response.paging.lpage;
          this.pagination.cpage = response.paging.cpage;
          this.pagination.total = response.paging.total;
          this.pagination.limit = response.paging.limit;
          this.processing = false;
          this.summary = response.summary
        })
        .catch(e => {
          u.log("Exeption", e)
          this.processing = false
        })
    },
    exportExcel() {
      this.processing = true
      var params = this.getParamsSearch()
      var urlApi = "/api/export/report_r12"
      var tokenKey = u.token()
      u.g(urlApi, params, 1, 1)
        .then(response => {
          saveAs(response, "BÁO CÁO CONFIRM HỌC SINH.xlsx");
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
      if (this.searchData.listBranchs.length) {
        this.searchData.listBranchs.map(item => {
          ids.push(item.id);
        });
      }
      const from_date = this.searchData.dateRange!='' && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
      const to_date = this.searchData.dateRange!='' && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
      const from_date_init = this.searchData.dateRangeInit!='' && this.searchData.dateRangeInit[0] ?`${u.dateToString(this.searchData.dateRangeInit[0])}`:''
      const to_date_init = this.searchData.dateRangeInit!='' && this.searchData.dateRangeInit[1] ?`${u.dateToString(this.searchData.dateRangeInit[1])}`:''
      const data = {
        scope: ids,
        from_date: from_date,
        to_date: to_date,
        from_date_init: from_date_init,
        to_date_init: to_date_init,
        page: this.pagination.cpage,
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
    
  },
};
</script>
<style scoped>
  .form-control.limit-selection {
    width: 60px !important;
  }
</style>