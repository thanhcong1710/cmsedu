<template>
  <div class="animated fadeIn apax-form">
    <loader :active="processing" :spin="spin" :text="text" :duration="duration"/>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter"></i>
            <b class="uppercase">Bộ lọc/Filter</b>
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
                  placeholder="Tìm học sinh theo: Tên, Mã CMS"
                  @input="validate_keyword()"
                >
                <i class="mx-input-min-icon fa fa-search"></i>
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
            <strong>THỐNG KÊ SỐ PHÍ CÒN LẠI CỦA HỌC SINH - Tổng số: {{pagination.total}}</strong>
          </div>

          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
                <tr class="text-sm">
                  <th>STT</th>
                  <th>Tên trung tâm</th>
                  <th>Mã học sinh CRM</th>
                  <th>Tên học sinh</th>
                  <th>Loại hợp đồng</th>
                  <th>Phải đóng</th>
                  <th>Số tiền đã đóng</th>
                  <th>Công nợ</th>
                  <th>Tổng số buổi active</th>
                  <th>Buổi chính khóa</th>
                  <th>Buổi được tặng</th>
                  <th>Tên lớp</th>
                  <th>Số buổi đã học</th>
                  <th>Số buổi còn lại học </th>
                  <th>Học phí còn lại</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in dataReport" :key="index">
                  <td>{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.student_crm_id }}</td>
                  <td>{{ item.student_name }}</td>
                  <td>{{ item.contract_type != 0 ? 'Bình thường' : 'Chuyển phí' }}</td>
                  <td>{{ item.must_charge }}</td>
                  <td>{{ item.total_charged }}</td>
                  <td>{{ item.debt_amount }}</td>
                  <td>{{ item.summary_sessions }}</td>
                  <td>{{ item.real_sessions }}</td>
                  <td>{{ item.bonus_sessions }}</td>
                  <td>{{ item.class_name }}</td>
                  <td>{{ item.done_sessions }}</td>
                  <td>{{ Math.max(0, item.real_sessions - item.done_sessions) }}</td>
                  <td>{{ item.left_amount }}</td>
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
import paging from "../../components/Pagination";
import u from "../../utilities/utility";
import saveAs from "file-saver";
import Multiselect from "vue-multiselect";
import loader from "../../components/Loading";

export default {
  name: "ReportStudentFeeSummary",
  components: {
    paging,
    Multiselect,
    loader
  },
  data() {
    return {
      session: u.session(),
      searchData: {
        listBranchs: "",
        keyword: "",
      },
      resource: {
        branchs: [],
      },
      dataReport: [],
      pagination: {
        url: "/api/reports/student-fee-summary",
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
    search() {
      this.processing = true;
      const data = this.getParamsSearch();
      const link = "/api/reports/student-fee-summary";
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
          u.log("Exception", e);
          this.processing = false;
        });
    },
    exportExcel() {
      this.processing = true;
      var params = this.getParamsSearch();
      var urlApi = "/api/export/student-fee-summary";
      u.g(urlApi, params, 1, 1)
        .then(response => {
          saveAs(response, "Thong-ke-phi-con-lai.xlsx");
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
      return {
        branch_id: ids.length > 0 ? ids[0] : null,
        limit: this.pagination.limit,
        page: this.pagination.cpage,
        keyword: this.searchData.keyword.trim(),
      };
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
    backList() {
      this.$router.push("/forms");
    },
    validate_keyword() {
      this.searchData.keyword = this.searchData.keyword.replace(/[~`!#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi, '');
    },
    getContractStatusName(status) {
      const statuses = {
        0: 'Đã xóa',
        1: 'Mới tạo',
        2: 'Đã cọc',
        3: 'Đã thu full phí',
        4: 'Bảo lưu/Pending',
        5: 'Học bổng',
        6: 'Đang học',
        7: 'Ngừng học',
        8: 'Đã bỏ cọc'
      };
      return statuses[status] || status;
    }
  }
};
</script>
<style scoped>
  .form-control.limit-selection {
    width: 60px !important;
  }
</style>
