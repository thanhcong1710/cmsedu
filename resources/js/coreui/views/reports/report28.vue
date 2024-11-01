<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
                    </div>
                    <div class="content-detail">
                        <div class="row">
                            <div class="col-md-6">
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
                                    :disabled="readOnly"
                                    >
                                    <span slot="noResult">Không tìm thấy trung tâm phù hợp</span>
                                </multiselect>
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
                            <div class="col-md-3">
                                <multiselect
                                    placeholder="Chọn CS (Tất Cả)"
                                    select-label="Chọn một CS"
                                    v-model="searchData.listCms"
                                    :options="resource.cms"
                                    label="full_name"
                                    :close-on-select="false"
                                    :hide-selected="true"
                                    :multiple="true"
                                    :searchable="true"
                                    track-by="id"
                                    >
                                    <span slot="noResult">Không tìm thấy CS phù hợp</span>
                                </multiselect>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <button class="apax-btn full detail" @click="search()"><i class="fa fa-search"></i> Tìm Kiếm</button>
                        <button class="apax-btn full print" @click="exportExcel()"><i class="fa fa-file-excel-o"></i> Xuất Báo Cáo</button>
                        <button class="apax-btn full reset" @click="clearSearch()"><i class="fa fa-refresh"></i> Bỏ Lọc</button>
                        <button class="apax-btn full remove" @click="backList()"><i class="fa fa-sign-out"></i> Thoát</button>
                    </div>
                </b-card>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-file-text"></i> <strong>BC28 - Báo cáo học sinh đến hạn chưa withdraw</strong>
                    </div>
                    <!--v-show="['CM','OM'].indexOf(session.user.title) > -1"-->
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
                        <select class="form-control paging-limit" v-model="pagination.limit" @change="search()" style=" height: 30px !important;border: 0.5px solid #dfe3e6 !important;">
                            <option v-for="(item, index) in pagination.limitSource" v-bind:value="item" v-bind:key="index">
                                {{ item }}
                            </option>
                        </select>
                    </div>
                    <div class="table-responsive scrollable">
                        <table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
                            <thead>
                                <tr class="text-sm">
                                    <th>STT</th>
                                    <th>Mã CMS</th>
                                    <th>Mã Cyber</th>
                                    <th>Tên học sinh</th>
                                    <th>Loại khách hàng</th>
                                    <th>Trung tâm</th>
                                    <th>Sản phẩm</th>
                                    <th>Chương Trình</th>
                                    <th>Lớp học</th>
                                    <th>Gói học phí</th>
                                    <th>Số buổi</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày học cuối</th>
                                    <th>EC</th>
                                    <th>CS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in dataReport" :key="index">
                                    <td class="text-right">{{ index + 1 + ((pagination.cpage - 1) * pagination.limit) }}</td>
                                    <td>{{ item.crm_id }}</td>
                                    <td>{{ item.cyber_code }}</td>
                                    <td>{{ item.student_name }}</td>
                                    <td>{{ item.customer_type | contractType }}</td>
                                    <td>{{ item.branch_name }}</td>
                                    <td>{{ item.product_name }}</td>
                                    <td>{{ item.program_name }}</td>
                                    <td>{{ item.class_name }}</td>
                                    <td>{{ item.tuition_fee_name }}</td>
                                    <td>{{ item.available_sessions }}</td>
                                    <td>{{ item.start_date }}</td>
                                    <td>{{ item.last_date }}</td>
                                    <td>{{ item.ec_name }}</td>
                                    <td>{{ item.cm_name }}</td>
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
                        <select class="form-control paging-limit" v-model="pagination.limit" @change="search()" style=" height: 30px !important;border: 0.5px solid #dfe3e6 !important;">
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
import saveAs from "file-saver"
import Multiselect from 'vue-multiselect'

export default {
	name: 'Report28',
	components: {
		DatePicker,
		paging,
		axios,
		saveAs,
		Multiselect
	},
    props     : {
        readOnly: { type: Boolean, default: false },
    },
	data() {
		const model = {
            session: u.session(),
			searchData: {
				name: "",
				keyword: "",
				dateRange: "",
				ec_name:"",
				cm_name: "",
				branchIds: [],
                productIds: [],
                cms: [],
				listBranchs:"",
                listProducts:"",
                listCms:"",
			},
			resource: {
				branchs: [],
                products: [],
                cms: [],
				resultsList: [{ id: 1, name: 'Thành Công' }, { id: 2, name: 'Thất Bại' }]
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
			}
		}
		return model;
	},
	created() {
        if (u.session().user.role_id === 686868 || u.session().user.role_id === 55){
            this.readOnly = true
        }
		u.a().get(`/api/all/products`).then(response => {
            this.resource.products = response.data
        })
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
        this.searchData.dateRange = [
            new Date(new Date().getFullYear(),new Date().getMonth(), new Date().getDate() - 7),
            new Date()
        ]
        if(this.searchData.listBranchs.id){
            u.a().get(`/api/cm/branch/${this.searchData.listBranchs.id}`).then(response =>{
                this.resource.cms = response.data;
            })
        }
		this.search()
	},
	methods: {
		search() {
            u.apax.$emit("apaxLoading", true)
            const data  = this.getParamsSearch()
            const link  = '/api/reports/form-28'
            const params = {
                scope: data.scope,
                page: data.page,
                keyword: data.keyword,
                products: data.products,
                cms: data.cms,
                limit: data.limit,
                type: data.type
            }
            u.p(link, params, 1)
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
            u.getFile('/api/export/report28', params).then(() => {
                u.apax.$emit('apaxLoading', false)
            })
            //var urlApi = '/api/export/report28'
            // var tokenKey = u.token()
            // u.g(urlApi, params, 1, 1)
            // .then(response => {
            //     saveAs(response, "Báo Cáo Học Sinh Đến Hạn Tái Tục.xlsx")
            //     u.apax.$emit("apaxLoading", false)
            // })
            // .catch(e => {
            //     u.apax.$emit("apaxLoading", false)
            // })
        },
        getParamsSearch() {
            const ids = []
            const pids = []
            const cmids = []
            this.searchData.listBranchs = u.is.obj(this.searchData.listBranchs) ? [this.searchData.listBranchs] : this.searchData.listBranchs
            if (this.searchData.listBranchs.length) {
                this.searchData.listBranchs.map(item => {
                ids.push(item.id)
                })
            }
            if (this.searchData.listProducts.length) {
                this.searchData.listProducts.map(item => {
                pids.push(item.id)
                })
            }
            if (this.searchData.listCms.length) {
                this.searchData.listCms.map(item => {
                cmids.push(item.id)
                })
            }
            const data = {
                scope: ids,
                page: this.pagination.cpage,
                limit: this.pagination.limit,
                products: pids,
                cms: cmids,
            };
            return data;
        },
        clearSearch() {

          this.searchData= {
              name: "",
              keyword: "",
              dateRange: "",
              ec_name:"",
              cm_name: "",
              branchIds: [],
              productIds: [],
              cms: [],
              listBranchs:"",
              listProducts:"",
              listCms:"",
          }

            // this.pagination = {
            //     url: "",
            //     id: "",
            //     style: "line",
            //     class: "",
            //     spage: 1,
            //     ppage: 1,
            //     npage: 0,
            //     lpage: 1,
            //     cpage: 1,
            //     total: 0,
            //     limit: 20,
            //     limitSource: [10, 20, 30, 40, 50],
            //     pages: []
            // };
            this.search();
        },
        changePage(link) {
            const info = link.toString().substr(this.pagination.url.length).split("/")
            const page = info.length > 1 ? info[1] : 1
            this.pagination.cpage = parseInt(page)
            this.search()
        },
        getDate(date) {
            if (date instanceof Date && !isNaN(date.valueOf())) {
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString();
                var formatedMonth = month.length === 1 ? "0" + month : month;
                var day = date.getDate().toString();
                var formatedDay = day.length === 1 ? "0" + day : day;
                return `${year}-${formatedMonth}`;
            }
            return "";
        },
        backList() {
            this.$router.push('/forms')
        },
        onSelectBranch(data){
            this.searchData.listBranchs="";
            u.a().get(`/api/cm/branch/${data.id}`).then(response =>{
                this.resource.cms = response.data;
            })
        }
	}
}
</script>

<style scoped>
    .paging{
        position: relative;
        z-index: 1;
    }
    .controller-bar{
        position: relative;
        z-index: 9;
    }
     .paging-limit{
        width: 40px;
    }
</style>
