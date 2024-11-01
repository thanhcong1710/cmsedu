<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
        <div class="col-12">
            <b-card header>
                <div slot="header">
                    <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc @_@</b>
                </div>
                <div class="content-detail">
                    <div class="row">
                        <div class="col-md-8">
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
                        <div class="col-md-4">
                            <date-picker style="width:100%;"
                                v-model="searchData.dateRange"
                                @change="search()"
                                :lang="datepickerOptions.lang"
                                format="YYYY-MM"
                                type="month" 
                                placeholder="Chọn tháng lọc">
                            </date-picker>
                        </div>
                    </div>
                </div>
                <div slot="footer" class="text-center">
                    <button class="apax-btn full detail" @click="search()"><i class="fa fa-search"></i> Tìm Kiếm</button>
                    <button class="apax-btn full reset" @click="clearSearch()"><i class="fa fa-refresh"></i> Lọc Lại</button>
                    <button class="apax-btn full remove" @click="backList()"><i class="fa fa-sign-out"></i> Thoát</button>
                </div>
            </b-card>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <b-card header>
                <div slot="header">
                    <i class="fa fa-file-text"></i> <strong>Báo cáo Average Classes Size</strong>
                </div>
                <div class="controller-bar table-header" v-show="['CM','OM'].indexOf(session.user.title) > -1">
                  <button class="apax-btn full print float-right" @click="exportExcel()"><i class="fa fa-file-excel-o"></i> Xuất Báo Cáo</button>
                </div>
                <div class="text-center paging">
                    <nav aria-label="Page navigation">
                        <paging :rootLink="pagination.url"
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
                                :routing="changePage">
                        </paging>
                    </nav>
                    <select class="form-control limit-selection" v-model="pagination.limit" @change="search()">
                        <option v-for="(item, index) in pagination.limitSource" :value="item" :key="index">
                            {{ item }}
                        </option>
                    </select>
                </div>
                
                <div class="table-responsive scrollable" style="padding-left: 0px;padding-right: 0px;padding-top: 30px;">
                    <table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
                        <thead>
                        <tr class="text-sm">
                          <th>No.</th>
                          <th>Branch</th>
                          <th>Class Room</th>
                          <th>Max Class Number</th>
                          <th>Total Actualy Class</th>
                          <th>Total Full Fee Student</th>
                          <th>ACS Full Fee</th>
                          <th>Room Performance</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in dataReport" :key="index">
                          <td class="text-right">{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                          <td>{{ item.branch_name }}</td>
                          <td>{{ item.total_rooms }}</td>
                          <td>{{ progressMaxClassNumber(item.total_rooms) }}</td>
                          <td>{{ item.total_classes }}</td>
                          <td>{{ item.total_full_fees }}</td>
                          <td>{{ progressAcsFullfee(item.total_full_fees,item.total_classes) }}</td>
                          <td>{{ progressRP(item.total_classes,item.total_rooms) }}%</td>
                        </tr>
                      </tbody>
                    </table>
                </div>

                <div class="text-center paging">
                    <nav aria-label="Page navigation">
                        <paging :rootLink="pagination.url"
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
                                :routing="changePage">
                        </paging>
                    </nav>
                    <select class="form-control limit-selection" v-model="pagination.limit" @change="search()">
                        <option v-for="(item, index) in pagination.limitSource" :value="item" :key="index">
                            {{ item }}
                        </option>
                    </select>
                </div>
            </b-card>
        </div>
    </div>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker"
import paging from "../../components/Pagination"
import u from "../../utilities/utility"
import axios from "axios"
import saveAs from "file-saver"
import Multiselect from 'vue-multiselect'

export default {
  name: 'Report01a',
  components: {
    DatePicker,
    paging,
    axios,
    saveAs,
    Multiselect
  },
  data () {
    const model = {
      session: u.session(),
      searchData: {
        name: "",
        dateRange: "",
        listBranchs:""
      },
      resource: {
        branchs: []
      },
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: new Date('2018-10-01'),
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
        url: "/api/reports/form-01a",
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
      }
    };
    return model;
  },
  created() {
    const session = u.session().user
    let selectionList = session.branches
    if (session.regions && session.regions.length) {
      selectionList = session.regions.concat(selectionList)
    }
    if (session.zones && session.zones.length) {
      selectionList = session.zones.concat(selectionList)
    }
    this.resource.branchs = selectionList
    this.searchData.listBranchs = selectionList[0]
    this.searchData.dateRange = new Date()
    this.search()
  },
  methods: {
    search() {
      u.apax.$emit("apaxLoading", true)
      const data  = this.getParamsSearch()
      const link  = '/api/reports/form-01a'
      const param = {
        scope: data.scope,
        page: data.page,
        limit: data.limit,
        date: data.date,
      }
      u.p(link, param, 1)
        .then(response => {
          this.dataReport = response.list
          this.pagination.spage = response.paging.spage
          this.pagination.ppage = response.paging.ppage
          this.pagination.npage = response.paging.npage
          this.pagination.lpage = response.paging.lpage
          this.pagination.cpage = response.paging.cpage
          this.pagination.total = response.paging.total
          this.pagination.limit = response.paging.limit
          u.apax.$emit("apaxLoading", false)
        })
        .catch(e => {
          u.log("Exeption", e)
          u.apax.$emit("apaxLoading", false)
        });
    },
    exportExcel() {
      u.apax.$emit("apaxLoading", true)
      var params = this.getParamsSearch()
      var urlApi = '/api/export/report01a'
      var tokenKey = u.token()
      u.g(urlApi, params, 1, 1)
        .then(response => {
          u.apax.$emit("apaxLoading", false)
          saveAs(response, "Báo cáo Average Classes Size.xlsx")
        })
        .catch(e => {
          u.apax.$emit("apaxLoading", false)
        })
    },
    getParamsSearch() {
      const ids = []
      this.searchData.listBranchs = u.is.obj(this.searchData.listBranchs) ? [this.searchData.listBranchs] : this.searchData.listBranchs
      if (this.searchData.listBranchs.length) {
        this.searchData.listBranchs.map(item => {
          ids.push(item.id)
        })
      }
      const data = {
        scope: ids,
        limit: this.pagination.limit,
        page: this.pagination.cpage,
        date: this.getDate(this.searchData.dateRange),
      };
      return data;
    },
    clearSearch() {
      this.searchData = {
        name: "",
        dateRange: "",
        ec_name:"",
        cm_name: ""
      };
      this.pagination = {
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
      };
      this.search();
    },
    changePage(link) {
      const info = link.toString().substr(this.pagination.url.length).split("/")
      const page = info.length > 1 ? info[1] : 1
      this.pagination.cpage = parseInt(page)
      this.search()
    },
    getDate(date) {
      let day = date instanceof Date && !isNaN(date.valueOf()) ? date : new Date()
      if (day instanceof Date && !isNaN(day.valueOf())) {
        var year = day.getFullYear()
        var month = (day.getMonth() + 1).toString()
        var formatedMonth = month.length === 1 ? "0" + month : month
        return `${year}-${formatedMonth}`
      }
      return "";
    },
    backList() {
      this.$router.push('/forms')
    },
    progressMaxClassNumber(bx) {
      return parseInt(bx) === 0 ? 0 : ((bx * 2 * 4) + (bx * 6 * 2)) / 2 
    },
    progressAcsFullfee(a,b) {
      return b == 0 ? 0 : (a / b).toFixed(2)
    },
    progressRP(cl,bx) {
      const resultbx = this.progressMaxClassNumber(bx)
      return resultbx === 0 ? 0 : ((cl / resultbx) * 100).toFixed(2)
    }
  }
}
</script>

<style scoped>
select.form-control.limit-selection {
    float: left;
    width: auto;
    height: 30px !important;
    border: 0.5px solid #dfe3e6;
    padding: 5px 0 3px 5px;
}
</style>
