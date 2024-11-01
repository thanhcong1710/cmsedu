<template>
  <div class="animated fadeIn apax-form">
    <loader :active="processing" :spin="spin" :text="text" :duration="duration"/>
    <div class="row">
        <div class="col-12">
            <b-card header>
                <div slot="header">
                    <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
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
                            >
                            <span slot="noResult">Không tìm thấy trung tâm phù hợp</span>
                          </multiselect>
                        </div>
                        <div class="col-md-3">
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
                    <button class="apax-btn full print" @click="exportExcel()"><i class="fa fa-file-excel-o"></i> Xuất Báo Cáo</button>
                </div>
            </b-card>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <b-card header>
                <div slot="header">
                    <i class="fa fa-file-text"></i> <strong>TỔNG HỢP thành BC02A2 - BÁO CÁO HỌC SINH TÁI PHÍ - TỔNG HỢP</strong>
                </div>
                
                <div class="table-responsive scrollable">
                    <table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
                        <thead>
                        <tr class="text-sm">
                          <th>STT</th>
                          <th class="width-300">Trung tâm</th>
                          <th>Số học sinh đến hạn tái phí</th>
                          <th>Số học sinh đóng phí tái tục</th>
                          <th>Tỷ lệ tái tục</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in dataReport" :key="index">
                          <td class="text-center">{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                          <td class="text-center">{{ item.branch_name }}</td>
                          <td class="text-center">{{ item.total_item }}</td>
                          <td class="text-center">{{ item.success_item }}</td>
                          <td>{{  item.total_item?calcpercentage( item.success_item, item.total_item): 75 }}%</td>
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
                    <select class="form-control" v-model="pagination.limit" @change="search()" style="width: auto;height: 30px !important;border: 0.5px solid #dfe3e6 !important;padding-top: 5px;">
                        <option v-for="(item, index) in pagination.limitSource" v-bind:value="item" v-bind:key="index">
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
import moment from "moment"
import saveAs from "file-saver"
import Multiselect from 'vue-multiselect'
import loader from "../../components/Loading"

export default {
  name: 'Report01b2',
  components: {
    DatePicker,
    paging,
    axios,
    saveAs,
    Multiselect,
    loader
  },
  data () {
    const model = {
      session: u.session(),
      searchData: {
        name: "",
        dateRange: moment().format('YYYY-MM'),
        ec_name:"",
        cm_name: "",
        listBranchs:"",
        listProducts:""
      },
      resource: {
        branchs: []
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
      processing: false,
      spin: "mini",
      duration: 500,
      text: "Đang tải dữ liệu..."
    }
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
    // this.searchData.listBranchs = selectionList[0]
    this.searchData.listBranchs = session.branches[0]
    this.search()
  },
  methods: {
		search() {
      this.processing = true;
      const data  = this.getParamsSearch()
      const link  = '/api/reports/form-01b2'
      const param = {
        scope: data.scope,
        page: data.page,
        limit: data.limit,
        date: data.date
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
				this.processing = false;
			})
			.catch(e => {
				u.log("Exeption", e)
				this.processing = false;
			});
		},
    exportExcel() {
      this.processing = true;
      var params = this.getParamsSearch()
      var urlApi = '/api/export/report01b2'
      var tokenKey = u.token()
      u.g(urlApi, params, 1, 1)
      .then(response => {
          saveAs(response, "BC02A2 - BÁO CÁO HỌC SINH TÁI PHÍ - TỔNG HỢP.xlsx")
          this.processing = false;
      })
      .catch(e => {
          this.processing = false;
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
        date: this.getDate(this.searchData.dateRange)
      };
      return data;
    },
    clearSearch() {
      location.reload();
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
    calcpercentage(a, b){
      return parseInt(b) > 0 ? (a/b * 100).toFixed(2) : 0
    }
	}
}
</script>

<style scoped>
  .width-300 {
    width: 300px;
  }
</style>
