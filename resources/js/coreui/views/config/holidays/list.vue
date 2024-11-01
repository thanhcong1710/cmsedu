<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12 user-search">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
                    </div>
                    <div id="holidays-list" class="panel-heading">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Tìm kiếm</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i v-b-tooltip.hover title="Lọc theo tiêu đề" class="fa fa-search"></i>
                                                </p>
                                                <input type="text" v-model="search.keyword" class="filter-selection customer-type form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label">Sản phẩm</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i v-b-tooltip.hover title="Lọc theo sản phẩm" class="fa fa-product-hunt"></i>
                                                </p>
                                                <select v-model="search.product_id" class="filter-selection customer-type form-control" id="">
                                                    <option value="">Lọc theo sản phẩm</option>
                                                    <option :value="product.id" v-for="(product, index) in products"
                                                            :key="index">{{product.name}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3" v-show="checkRole">
                                            <div class="form-group">
                                                <label class="control-label">Khu vực áp dụng</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i v-b-tooltip.hover title="Lọc theo khu vực áp dụng" class="fa fa fa-map"></i>
                                                </p>
                                                <select v-model="search.zone_id" class="filter-selection customer-type form-control" id="">
                                                    <option value="">Lọc theo khu vực</option>
                                                    <option :value="zone.id" v-for="(zone, index) in zones"
                                                            :key="index">{{zone.name}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--<div class="col-sm-3" v-show="checkRole">
                                            <div class="form-group">
                                                <label class="control-label">Trung tâm áp dụng</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i v-b-tooltip.hover title="Lọc theo trung tâm áp dụng" class="fa fa-bank"></i>
                                                </p>
                                                <select v-model="search.branch_id" class="filter-selection customer-type form-control" id="">
                                                    <option value="">Lọc theo trung tâm</option>
                                                    <option :value="branch.id" v-for="(branch, index) in branches"
                                                            :key="index">{{branch.name}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>-->
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label">Từ ngày</label><br/>
                                                <datePicker
                                                        id="start-date"
                                                        class="form-control calendar"
                                                        :value="search.start_date"
                                                        v-model="search.start_date"
                                                        placeholder="Chọn ngày bắt đầu"
                                                        lang="'en'"
                                                >
                                                </datePicker>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label">Đến ngày</label><br/>
                                                <datePicker
                                                        id="end-date"
                                                        class="form-control calendar"
                                                        :value="search.end_date"
                                                        v-model="search.end_date"
                                                        placeholder="Chọn ngày kết thúc"
                                                        lang="'en'"
                                                >
                                                </datePicker>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label">Trạng thái</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i v-b-tooltip.hover title="Lọc theo trung tâm áp dụng" class="fa fa-desktop"></i>
                                                </p>
                                                <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                                                    <option value="">Lọc theo trạng thái</option>
                                                    <option value="0">Không có hiệu lực</option>
                                                    <option value="1">Đang được kích hoạt</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <span v-if="hasPermission == true">
                            <router-link class="apax-btn full reset" :to="'/holidays/add-holiday'">
                                <i class="fa fa-plus"></i> Thêm mới
                            </router-link>
                        </span>
                        <button class="apax-btn full detail" @click.prevent="searchFilter"><i class="fa fa-search"></i> Tìm kiếm</button>
                        <button class="apax-btn full defalut" type="reset" @click.prevent="resetFilter"><i class="fa fa-ban"></i> Bỏ lọc</button>
                    </div> 
                </b-card>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-align-justify"></i> <b class="uppercase">Danh sách các ngày nghỉ lễ</b>
                    </div>
                    <table class="table table-bordered apax-table">
                        <thead>
                        <tr>
                            <th class="text-center">STT</th>
                            <th class="width-150">Tiêu Đề</th>
                            <th class="width-150">Từ ngày</th>
                            <th class="width-150">Đến ngày</th>
                            <th class="width-150">Khu vực áp dụng</th>
                            <th class="width-150">Sản phẩm</th>
                            <th>Trạng thái</th>
                            <th v-if="hasPermission == true">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- holiday data -->
                        <tr v-for="(holiday, index) in holidays" :key="index">
                            <td>
                                {{ indexOf(index+1) }}
                            </td>
                            <td>
                                {{holiday.name}}
                            </td>
                            <td>
                                {{holiday.start_date}}
                            </td>
                            <td>
                                {{holiday.end_date}}
                            </td>
                            <td>
                                {{ holiday.zone_name }}
                            </td>
                            <td>
                                {{holiday.products}}
                            </td>
                            <td>
                                {{holiday.status |getStatusToName }}
                            </td>
                            <td class="text-center cl-btn" v-if="hasPermission == true">
                                <router-link class="apax-btn detail " :to="{name: 'Chi Tiết Thông Tin Ngày Nghỉ Lễ', params: {id: holiday.id}}">
                                        <span class="fa fa-eye"></span>
                                </router-link>
                                <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Ngày Nghỉ Lễ', params: {id: holiday.id}}">
                                        <span class="fa fa-pencil"></span>
                                </router-link>
                                  <span class="apax-btn remove delete" v-b-tooltip.hover
                                        title=""
                                        @click="deleteHolidays(holiday.id,index)" v-if="holiday.status==0">
                                    <i class="fa fa-remove"></i>
                                  </span>
                                  <span class="apax-btn remove delete" v-b-tooltip.hover
                                        title=""
                                        @click="disabledDeleteHolidays(holiday.id)" v-else>
                                    <i class="fa fa-remove"></i>
                                  </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <nav aria-label="Page navigation">
                            <appPagination
                                    :rootLink="router_url"
                                    :id="pagination_id"
                                    :listStyle="list_style"
                                    :customClass="pagination_class"
                                    :firstPage="pagination.spage"
                                    :previousPage="pagination.ppage"
                                    :nextPage="pagination.npage"
                                    :lastPage="pagination.lpage"
                                    :currentPage="pagination.cpage"
                                    :pagesItems="pagination.total"
                                    :pageList="pagination.pages"
                                    :pagesLimit="pagination.limit"
                                    :routing="goTo"
                            >
                            </appPagination>
                        </nav>
                    </div>
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
    import datePicker from 'vue2-datepicker'
    import Pagination from '../../../components/Pagination'
    import u from '../../../utilities/utility'
    import abt from '../../../components/Button'
    import file from '../../../components/File'
    import search from '../../../components/Search'

    export default {
        components: {
            abt,
            file,
            search,
            datePicker,
            appPagination: Pagination
        },
        name: 'List-Holiday',
        data() {
            return {
                fees: '0',
                area: '0',
                product: '0',
                branches: [],
                status: '0',
                products: [],
                regions: [],
                region: '',
                search: {
                    keyword: '',
                    product_id: '',
                    start_date: '',
                    end_date: '',
                    zone_id: '',
                    branch_id: ''
                },
                key: '',
                value: '',
                holidays: [],
                holiday: '',
                router_url: '/publicHolidays/list',
                pagination_id: 'holiday_paging',
                pagination_class: 'holiday paging list',
                list_style: 'line',
                pagination: {
                    limit: 20,
                    spage: 0,
                    ppage: 0,
                    npage: 0,
                    lpage: 0,
                    cpage: 0,
                    total: 0,
                    pages: []
                },
                statusColor: '',
                zones: [],
                hasPermission: false
            }
        },
        created() {
            this.checkPermission()
            u.g('api/holidays/start').then(response => {
                this.products = response.products
                this.holidays = response.holidays
                this.pagination = response.pagination
                if (u.authorized()) {
                    this.branches = response.branches
                    this.zones = response.zones
                } else {
                    this.search.branch_id = response.branches
                    this.search.zone_id = response.zones
                }
            })
        },
        methods: {
            resetFilter() {
                this.search.keyword = '';
                this.search.start_date = ''
                this.search.end_date = ''
                this.search.zone_id = ''
                this.search.product_id=''
                this.search.branch_id=''
                this.search.status=''
                this.searchFilter();
            },
            checkRole() {
                return u.authorized()
            },
            showTitle(name) {
                return this.checkRole() ? `Sửa thôn tin ngày nghỉ lễ '${name}'` : `Xem thôn tin ngày nghỉ lễ '${name}'`
            },
            showLink(id) {
                return this.checkRole() ? `/edit-holiday/${id}` : `/holidays/${id}`
            },
            searchFilter() {
                var url = '/api/publicHolidays/list/1/';
                this.key = '';
                this.value = ''
                var keyword = this.search.keyword?this.search.keyword.trim():'';
                if (keyword) {
                    this.key += "keyword,"
                    this.value += this.search.keyword + ","
                }
                var start_date = this.search.start_date ? this.search.start_date : ""
                if (start_date) {
                    this.key += "start_date,"
                    this.value += this.getDate(this.search.start_date) + ","
                }
                var end_date = this.search.end_date ? this.search.end_date : ""
                if (end_date) {
                    this.key += "end_date,"
                    this.value += this.getDate(this.search.end_date) + ","
                }

                var zone_id = this.search.zone_id ? this.search.zone_id : ""
                if (zone_id) {
                    this.key += "zone_id,"
                    this.value += this.search.zone_id + ","
                }
                 var product_id = this.search.product_id ? this.search.product_id : ""
                if (product_id) {
                    this.key += "product_id,"
                    this.value += this.search.product_id + ","
                }
                var status = this.search.status ? this.search.status : ""
                if (status!=="") {
                    this.key += "status,"
                    this.value += this.search.status + ","
                }

                this.key = this.key ? this.key.substring(0, this.key.length - 1) : '_'
                this.value = this.value ? this.value.substring(0, this.value.length - 1) : "_"
                url += this.key + "/" + this.value
                this.get(url);
            },
            get(link) {
                this.ajax_loading = true
                u.a().get(link).then(response => {
                    this.holidays = response.data.holidays
                    this.pagination = response.data.pagination
                    this.ajax_loading = false
                }).catch(e => console.log(e));
            },
            getHolidays(page_url) {
                const key = '_'
                const fil = '_'
                page_url = page_url ? '/api' + page_url : '/api/publicHolidays/list/1'
                if (this.key) page_url += '/' + this.key + '/' + this.value
                else page_url += '/' + key + '/' + fil
                this.get(page_url)
            },
            makePagination(meta, links) {
                let pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    next_page_url: data.next_page_url,
                    prev_page_url: data.prev_page_url
                }
                this.pagination = pagination;
            },
            indexOf(value) {
                return value + ((this.pagination.cpage - 1) * this.pagination.limit)
            },
            disabledDeleteHolidays(){
                alert('Hệ thống chỉ cho phép xóa kỳ nghỉ lễ trạng thái ngừng hoạt động')
            },
            deleteHolidays(id, index) {
                const delStdConf = confirm("Bạn có chắc muốn xóa kỳ nghỉ lễ này không?");
                if (delStdConf === true) {
                    // console.log(`xId = ${xId}, Index = ${idx}`)
                    u.a().get('/api/holyday/' + id)
                        .then(response => {
                            this.holidays.splice(index, 1);
                        })
                        .catch(error => {
                        })
                }
            },
            filterHolidays() {
                return this.holidays.filter((holiday) => {
                    return holiday.name.match(this.search);
                    // alert('not finish')
                });
            },
            goTo(link) {
                this.getHolidays(link)
            },
            checkPermission(){
                const roles = [u.r.admin, u.r.super_administrator];
                if(roles.indexOf(parseInt(u.session().user.role_id)) > -1){
                    this.hasPermission = true;
                }else{
                    this.hasPermission = false;
                }
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
            },
        },
        computed: {
            filterByHoliday() {
                return this.filterHolidays();
            }
        },

        filters: {
            getStatusName(value) {
                return value == 1 ? "Hoạt động" : "Ngừng hoạt động";
            },
            statusColor(cl) {
                return cl == 1 ? "btn-primary" : "btn-danger";
            }
        },
        
    }
</script>

<style scoped lang="scss">
    .table-header {
        margin-bottom: 5px;
        margin-left: -15px;
    }

    .img img {
        width: 100px;
    }

    a {
        /* color: blue; */
    }
    .apax-btn:hover{
        color: #fff;
        text-decoration: none;
      }
    .cl-btn a {
        color: #fff;
    }
</style>
