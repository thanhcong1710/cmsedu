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
                  Trung tâm
                </label>
                <br>
                 <SearchBranch
                    :options="data.branches"
                    :disabled="data.branches.length <= 1"
                    placeholder="Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước."
                    v-model="data.branch"
                  ></SearchBranch>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="filter-label control-label">
                    Giáo viên
                  </label>
                  <br>
                  <select class="selection program form-control"
                          :disabled="role_36"
                          v-model="data.temp.teacher_id"
                  >
                    <option value="-1" >Chọn giáo viên</option>
                    <option :value="teacher.id" v-for="(teacher, idx) in data.teachers" :key="idx">
                      {{ teacher.name }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label class="filter-label control-label">
                    Lớp
                  </label>
                  <br>
                  <select class="selection program form-control"
                          v-model="data.temp.class_id"
                  >
                    <option value="-1">Chọn lớp</option>
                    <option :value="cls.id" v-for="(cls, idx) in data.classes" :key="idx">
                      {{ cls.class_name }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <label class="filter-label control-label">
                  Từ khóa
                </label>
                <br>
                <input
                  class="search-field form-control filter-input"
                  v-model="searchData.keyword"
                  placeholder="Tìm học sinh theo: Tên, Mã CRM"
                  @input="validate_keyword()"
                >
                <i class="mx-input-min-icon fa fa-search"></i>
              </div>
              <div class="col-md-3">
                <label class="filter-label control-label">
                  Tháng
                </label>
                <br>
                <select class="selection program form-control" v-model="searchData.report_month">
                  <option value="">Chọn tháng nhận xét</option>
                  <option :value="report_month.id" v-for="(report_month, idx) in resource.report_months" :key="idx">
                    {{ report_month.title }}
                  </option>
                </select>
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
            <strong>BÁO CÁO TÌNH HÌNH HỌC SINH BRIGHT IG & BLACK HOLE - {{title_report}}</strong>
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
                  <th>Tên lớp</th>
                  <th>Giáo viên</th>
                  <th>Level</th>
                  <th>Mã LMS</th>
                  <th>Mã HS</th>
                  <th>Họ và tên</th>
                  <th>Giới tính</th>
                  <th>Thời gian đăng ký</th>
                  <th>Trạng thái</th>
                  <th>Ngày đăng ký</th>
                  <th>Ghi chú</th>
                  <th>Chương trình</th>
                  <th>Điểm đầu vào</th>
                  <th>Điểm của quý</th>
                  <th>Xếp loại</th>
                  <th>Nhận xét của GV</th>
                  <th>Đề xuất của GV</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataReport" :key="index">
                  <td>{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.class_name }}</td>
                  <td>{{ item.teacher_name }}</td>
                  <td>{{ item.level_name }}</td>
                  <td>{{ item.crm_id }}</td>
                  <td>{{ item.accounting_id }}</td>
                  <td>{{ item.student_name }}</td>
                  <td>{{ item.gender }}</td>
                  <td>{{ item.thoigian_dangky }}</td>
                  <td>{{ item.student_status }}</td>
                  <td>{{ item.ngay_dangky }}</td>
                  <td>{{ item.note }}</td>
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.score_demo }}</td>
                  <td>{{ item.score_week_1 }}</td>
                  <td>{{ item.report_type | filterStatus}}</td>
                  <td>{{ item.comment }}</td>
                  <td>{{ item.suggestion }}</td>
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
      },
      sources_list:[],
      resource: {
        branchs: [],
        report_months: []
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
        url: "/api/reports/r08",
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
    this.data.branches = u.session().user.branches
    if(this.data.branches.length==1){
      this.data.branch = u.session().user.branches[0]
    }
    if(u.session().user.role_id==36){
      this.data.temp.teacher_id=u.session().user.id
      this.role_36 = true
    }
    this.search();
     u.g(`/api/report_student/get_report_month?product_id=2`)
      .then(response => {
        this.resource.report_months = response
      })
  },
  methods: {
    search(a) {
      this.processing = true
      const data = this.getParamsSearch()
      const link = "/api/reports/r08"
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
      var urlApi = "/api/export/report_r08"
      var tokenKey = u.token()
      u.g(urlApi, params, 1, 1)
        .then(response => {
          saveAs(response, "BÁO CÁO TÌNH HÌNH HỌC SINH IG BH.xlsx");
          this.processing = false;
        })
        .catch(e => {
          this.processing = false;
        });
    },
    getParamsSearch() {
      const data = {
        branch_id: this.data.branch.id,
        teacher_id: this.data.temp.teacher_id,
        class_id: this.data.temp.class_id,
        limit: this.pagination.limit,
        page: this.pagination.cpage,
        date: this.getDate(this.searchData.dateRange),
        keyword: this.searchData.keyword.trim(),
        report_month: this.searchData.report_month,
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
    getAllInfo(branch_id) {
        if(branch_id){
          u.a().get(`/api/report_student/info/${branch_id}`)
            .then(response => {
              if (response.data.code == 200) {
                this.data.teachers = response.data.data
                if(u.session().user.role_id!=36){
                  this.data.temp.teacher_id = "-1"
                }
              }else{
                u.showError(this.html.modal, response.data.message)
              }
            })
            .catch(e => {
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
        }
      },
      getAllClass(teacher_id) {
        if(teacher_id){
          u.a().get(`/api/report_student/class_info/${teacher_id}?product_id=2`)
            .then(response => {
              if (response.data.code == 200) {
                this.data.classes = response.data.data
                this.data.temp.class_id = "-1"
              }else{
                u.showError(this.html.modal, response.data.message)
              }
            })
            .catch(e => {
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
        }
      },
  },
  filters:{
    filterStatus(report_type){
      let txt =''
      switch (report_type) {
        case 1:
          txt = "Giỏi";
          break;
        case 2:
          txt = "Khá";
          break;
        case 3:
          txt = "Trung bình";
          break;
        case 4:
          txt = "Yếu";
          break;
      }
      return txt;
    }
  },
  watch: {
    "data.branch.id": function (newValue) {
      if(newValue && !isNaN(newValue)){
        this.getAllInfo(newValue)
      }
    },
    "data.temp.teacher_id": function (newValue) {
      this.data.temp.class_id = "-1"
      if(newValue && !isNaN(newValue) && parseInt(newValue) > -1){
        this.getAllClass(newValue)
      }else{
        this.data.classes = []
      }
    },
  }
};
</script>
<style scoped>
  .form-control.limit-selection {
    width: 60px !important;
  }
</style>