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
                            <div class="col-md-12">
                                <multiselect
                                        placeholder="Chọn trung tâm"
                                        select-label="Chọn một trung tâm"
                                        v-model="searchData.branchIds"
                                        :options="resource.branchs"
                                        :preselect-first="resource.selectBranchFirst"
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
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col-md-4">
                                <input id="input_keyword"
                                       name="search[keyword]"
                                       class="filter-selection search-field form-control filter-input"
                                       v-model="searchData.keyword"
                                       placeholder="Tìm học sinh theo: Tên, EC, CM, Mã LMS/Effect"
                                >
                                <i class="mx-input-min-icon fa fa-search"></i>
                            </div>
                            <div class="col-md-4">
                                <date-picker style="width:100%;"
                                             v-model="searchData.dateRange"
                                             @change="filterReport()"
                                             :clearable="true"
                                             :not-before=datepickerOptions.minDate
                                             range
                                             :lang="datepickerOptions.lang"
                                             format="YYYY-MM-DD"
                                             placeholder="30 ngày trước">
                                </date-picker>
                            </div>
                            <div class="col-md-4">
                                <div class="select-control">
                                    <select
                                            v-model="searchData.status_contract"
                                            class="form-control"
                                    >
                                        <option value="" selected>Trạng thái</option>
                                        <option v-for="(option, index) in resource.statusContract" v-bind:value="option.id" :key="index">
                                            {{ option.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="select-control">
                                    <select v-model="searchData.type_student"
                                            class="form-control"
                                    >
                                        <option value="" selected>Loại khách hàng</option>
                                        <option v-for="(option, index) in resource.typeStudents" v-bind:value="option.id" :key="index">
                                            {{ option.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="select-control">
                                    <select v-model="searchData.type_contract"
                                            class="form-control"
                                    >
                                        <option value="" selected>Loại hợp đồng</option>
                                        <option v-for="(option, index) in resource.typeContract" v-bind:value="option.id" :key="index">
                                            {{ option.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <button class="apax-btn full detail" @click="filterReport()"><i class="fa fa-search"></i> Tìm Kiếm</button>
                        <button class="apax-btn full reset" @click="clearFilter()"><i class="fa fa-refresh"></i> Lọc Lại</button>
                        <button class="apax-btn full remove" @click="backList()"><i class="fa fa-sign-out"></i> Thoát</button>
                    </div>
                </b-card>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-file-text pdt-10"></i> <strong>Báo cáo thông tin học viên</strong>
                        <span class="right pull-right">
                            <button class="apax-btn full print float-right" @click="exportExcel()">
                                <i class="fa fa-file-excel-o"></i> Xuất Báo Cáo
                            </button>
                        </span>
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
                        <select class="form-control paging-limit" v-model="pagination.limit" @change="filterReport()" style="height: 30px !important;border: 0.5px solid #dfe3e6 !important;">
                            <option v-for="(item, index) in pagination.limitSource" v-bind:value="item" v-bind:key="index">
                                {{ item }}
                            </option>
                        </select>
                    </div>
                    <div class="table-responsive scrollable">
                        <table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
                            <thead>
                            <tr>
                                <th> STT </th>
                                <th> Trung tâm </th>
                                <th> Mã CMS </th>
                                <th> Mã Effect </th>
                                <th> Tên học sinh </th>
                                <th> Loại khách hàng </th>
                                <th> Tên phụ huynh 1 </th>
                                <th> SĐT phụ huynh 1 </th>
                                <th> Gói phí </th>
                                <th> CM </th>
                                <th> EC </th>
                                <th> EC Leader </th>
                                <th> Loại hợp đồng </th>
                                <th> Trạng thái </th>
                                <th> Lớp học </th>
                                <th> Kỳ học </th>
                                <th> Số buổi </th>
                                <th> Ngày bắt đầu học </th>
                                <th> Ngày kết thúc học </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in dataReport" v-bind:key="index">
                                <td> {{ ((pagination.cpage - 1)*pagination.limit + index + 1) }} </td>
                                <td> {{ item.branch_name }} </td>
                                <td> {{ item.crm_id }} </td>
                                <td> {{ item.accounting_id }} </td>
                                <td> {{ item.student_name }} </td>
                                <td> {{ item.type_name }} </td>
                                <td> {{ item.gud_name1 }} </td>
                                <td> {{ item.gud_mobile1 }} </td>
                                <td> {{ item.tuition_fee_name }} </td>
                                <td> {{ item.cm_name }} </td>
                                <td> {{ item.ec_name }} </td>
                                <td> {{ item.ec_leader_name }} </td>
                                <td> {{ item.contract_type_name }}</td>
                                <td> {{ item.contract_status_name }} </td>
                                <td> {{ item.class_name }} </td>
                                <td> {{ item.semester_name }} </td>
                                <td> {{ item.real_sessions }} </td>
                                <td> {{ item.start_date | formatDate }} </td>
                                <td> {{ item.last_date | formatDate }} </td>
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
                        <select class="form-control paging-limit" v-model="pagination.limit" @change="filterReport()" style=" height: 30px !important;border: 0.5px solid #dfe3e6 !important;">
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
    import DatePicker from "vue2-datepicker";
    import paging from "../../components/Pagination";
    import u from "../../utilities/utility";
    import axios from "axios";
    import saveAs from "file-saver";
    import Multiselect from 'vue-multiselect'

    export default {
        name: 'Report01b1',
        components: {
            DatePicker,
            paging,
            axios,
            saveAs,
            Multiselect
        },
        data() {
            const model = {
                searchData: {
                    name: "",
                    keyword: "",
                    dateRange: "",
                    ec_name: "",
                    cm_name: "",
                    branchIds: [],
                    productIds: [],
                    listBranchs: "",
                    listProducts: "",
                    status_contract: "",
                    type_student: "",
                    type_contract: ""
                },
                resource: {
                    branchs: [],
                    products: [],
                    resultsList: [{id: 1, name: 'Thành Công'}, {id: 2, name: 'Thất Bại'}],
                    selectBranchFirst: false,
                    statusContract: [
                        {id: 0, name : 'Đã xóa'},
                        {id: 1, name : 'Đã active nhưng chưa đóng phí'},
                        {id: 2, name : 'Đã active và đặt cọc nhưng chưa thu đủ phí'},
                        {id: 3, name : 'Nhận chuyển phí'},
                        {id: 4, name : 'Nhận chuyển trung tâm'},
                        {id: 5, name : 'Đã active và đã thu đủ phí full fee'},
                        {id: 6, name : 'VIP'},
                        {id: 7, name : 'Đã Withdraw'}
                    ],
                    typeStudents: [
                        {id:0, name:'Bình thường'},
                        {id:1, name:'Vip'}
                    ],
                    typeContract: [
                        {id: 0, name : 'Học thử'},
                        {id: 1, name : 'Chính thức'},
                        {id: 2, name : 'Tái phí bình thường'},
                        {id: 3, name : 'Tái phí do nhận chuyển phí'},
                        {id: 4, name : 'Chỉ nhận chuyển phí'},
                        {id: 5, name : 'Chuyển trung tâm'},
                        {id: 6, name : 'Chuyển lớp'},
                        {id: 7, name : 'Sinh do tai phi chua full phí'},
                        {id: 8, name : 'Được nhận học bổng'},
                    ]
                },
                datepickerOptions: {
                    closed: true,
                    value: "",
                    minDate: "",
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
            };
            return model;
        },
        created() {
            u.a().get(`/api/all/products`).then(response => {
                this.resource.products = response.data
            })
            const session = u.session().user
            this.resource.branchs = session.branches
            if (session.branches.length == 1) {
                this.resource.selectBranchFirst = true;
                this.searchData.listBranchs = session.branches[0]
            }
            this.filterReport()
        },
        methods: {
            filterReport: function () {
                var url = '/api/reports/students',
                    fd = this.getDate(typeof(this.searchData.dateRange[0]) != 'undefined' ? this.searchData.dateRange[0] : ''),
                    td = this.getDate(typeof(this.searchData.dateRange[1]) != 'undefined' ? this.searchData.dateRange[1] : ''),
                    params = {
                        page: this.pagination.cpage,
                        limit: this.pagination.limit,
                        fd: fd,
                        td: td,
                        keyword: this.searchData.keyword,
                        branches: this.searchData.branchIds,
                        products: this.searchData.productIds,
                        status_contract: this.searchData.status_contract,
                        type_student: this.searchData.type_student,
                        type_contract: this.searchData.type_contract
                    };

                u.apax.$emit("apaxLoading", true);

                u.p(url, params, true).then(response => {
                    u.apax.$emit("apaxLoading", false);
                    this.dataReport = response.list;
                    this.pagination.total = response.total_record;
                    this.pagination.lpage = Math.ceil(this.pagination.total / this.pagination.limit);
                    this.pagination.ppage = this.pagination.cpage > 0 ? this.pagination.cpage - 1 : 0;
                    this.pagination.npage = this.pagination.cpage + 1;
                }).catch(e => {
                    console.log(e);
                    u.apax.$emit("apaxLoading", false);
                });
            },
            exportExcel() {
                var url = `/api/exel/export-students`,
                    fd = this.getDate(typeof(this.searchData.dateRange[0]) != 'undefined' ? this.searchData.dateRange[0] : ''),
                    td = this.getDate(typeof(this.searchData.dateRange[1]) != 'undefined' ? this.searchData.dateRange[1] : ''),
                    params = {
                        page: this.pagination.cpage,
                        limit: this.pagination.limit,
                        fd: fd,
                        td: td,
                        keyword: this.searchData.keyword,
                        branches: this.searchData.branchIds,
                        products: this.searchData.productIds,
                        status_contract: this.searchData.status_contract,
                        type_student: this.searchData.type_student,
                        type_contract: this.searchData.type_contract,
                        headers: {
                            Authorization: u.token()
                        },
                        responseType: "blob"
                    };

                u.apax.$emit("apaxLoading", true);
                axios.post(url, params, {
                    headers: {
                        Authorization: u.token()
                    },
                    responseType: "blob"
                }).then(response => {
                    u.apax.$emit("apaxLoading", false);
                    saveAs(response.data, "Báo cáo thông tin học viên.xlsx");
                }).catch(e => {
                    console.log(e);
                    u.apax.$emit("apaxLoading", false);
                });
            },
            clearFilter: function () {
                this.searchData.branchIds = [];
                this.searchData.keyword = '';
                this.searchData.dateRange = [];
                this.searchData.status_contract = "";
                this.searchData.type_student = "";
                this.searchData.type_contract = "";
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
                this.filterReport();
            },
            backList() {
                this.$router.push('/forms')
            },
            changePage(link) {
                const info = link.toString().split("/");
                const page = info.length > 1 ? info[1] : 1;
                this.pagination.cpage = parseInt(page);
                this.filterReport();
            },
            getDate(date) {
                if (date instanceof Date && !isNaN(date.valueOf())) {
                    var year = date.getFullYear(),
                        month = (date.getMonth() + 1).toString(),
                        day = date.getDate().toString(),
                        strMonth = month < 10 ? "0" + month : month,
                        strYear = day < 10 ? "0" + day : day;

                    return `${year}-${strMonth}-${strYear}`;
                }
                return '';
            }
        }
    }
</script>

<style scoped>
    .card-body .text-center.paging, .card-block .text-center.paging {
        padding-left: 10px;
        margin-top: 0px;
    }
    .paging-limit{
        width: 40px;
    }
</style>
