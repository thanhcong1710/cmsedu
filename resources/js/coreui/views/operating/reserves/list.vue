<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div v-show="flags.requesting" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="flags.requesting" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
                        </div>
                    </div>

                    <div slot="header">
                        <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
                    </div>
                    <div class="content-detail">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3"  v-if="filter_branch">
                                        <div class="form-group">
                                            <label class="control-label">Trung tâm</label>
                                            <SearchBranch
                                                    :searchId="html.filter.branch.id"
                                                    :onSearchBranchReady="prepareSearch"
                                                    :onSelectBranch="selectBranch"
                                                    :placeholderBranch="html.filter.branch.placeholderSearchBranch"
                                                    :limited="true"
                                            >
                                            </SearchBranch>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Mã CMS hoặc mã Cyber</label><br/>
                                            <input type="text" class="form-control" v-model="filter.lms_accounting_id" :disabled="html.filter.lms_effect.disabled"/>
                                        </div>
                                    </div> -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Tên học sinh, mã CMS hoặc mã Cyber</label><br/>
                                            <input type="text" class="form-control" v-model="filter.student_name" :disabled="html.filter.student.disabled"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Trạng thái</label><br/>
                                            <select type="text" class="form-control" v-model="filter.status" :disabled="html.filter.status.disabled">
                                                <option value="-1">Tất cả</option>
                                                <option value="0">Chờ duyệt</option>
                                                <option value="2">Đã duyệt</option>
                                                <option value="3">Từ chối</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <b-collapse id="search_more">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Kỳ học</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.semester_id" :disabled="html.filter.semester.disabled">
                                                    <option value="0">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Sản phẩm</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.product_id" :disabled="html.filter.product.disabled">
                                                    <option value="0">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Chương trình</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.program_id" :disabled="html.filter.program.disabled">
                                                    <option value="0">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Lớp</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.class_id" :disabled="html.filter.cls.disabled">
                                                    <option value="0">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Gói học phí</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.tuition_fee_id" :disabled="html.filter.tuition_fee.disabled">
                                                    <option value="-1">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Giữ chỗ trong lớp</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.is_reserved" :disabled="html.filter.is_reserved.disabled">
                                                    <option value="-1">Tất cả</option>
                                                    <option value="0">Không</option>
                                                    <option value="1">Có</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Ngày bắt đầu bảo lưu</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <calendar
                                                        class="form-control calendar filter-selection"
                                                        :value="filter.start_date"
                                                        :transfer="true"
                                                        :format="html.calendar.options.formatSelectDate"
                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                        :pane="1"
                                                        :disabled="html.calendar.is_disabled"
                                                        :lang="html.calendar.lang"
                                                        @input="selectStartDate"
                                                ></calendar>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Ngày kết thúc bảo lưu</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <calendar
                                                        class="form-control calendar filter-selection"
                                                        :value="filter.end_date"
                                                        :transfer="true"
                                                        :format="html.calendar.options.formatSelectDate"
                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                        :pane="1"
                                                        :disabled="html.calendar.is_disabled"
                                                        :lang="html.calendar.lang"
                                                        @input="selectEndDate"
                                                ></calendar>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Số buổi bảo lưu nhỏ nhất</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <input type="text" class="form-control filter-selection" v-model="filter.min_sessions" :disabled="html.filter.sessions.disabled"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Số buổi bảo lưu lớn nhất</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <input type="text" class="form-control filter-selection" v-model="filter.max_sessions" :disabled="html.filter.sessions.disabled"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Ngày đăng ký bảo lưu (từ ngày)</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <calendar
                                                        class="form-control calendar filter-selection"
                                                        :value="filter.min_created_date"
                                                        :transfer="true"
                                                        :format="html.calendar.options.formatSelectDate"
                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                        :pane="1"
                                                        :disabled="html.calendar.is_disabled"
                                                        @input="selectMinCreatedDate"
                                                        :lang="html.calendar.lang"
                                                ></calendar>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Ngày đăng ký bảo lưu (đến ngày)</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <calendar
                                                        class="form-control calendar filter-selection"
                                                        :value="filter.max_created_date"
                                                        :transfer="true"
                                                        :format="html.calendar.options.formatSelectDate"
                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                        :pane="1"
                                                        :disabled="html.calendar.is_disabled"
                                                        @input="selectMaxCreatedDate"
                                                        :lang="html.calendar.lang"
                                                ></calendar>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Người đăng ký</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.creator_id" :disabled="html.filter.creator.disabled">
                                                    <option value="0">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Người duyệt</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <select type="text" class="form-control filter-selection" v-model="filter.approver_id" :disabled="html.filter.approver.disabled">
                                                    <option value="0">Tất cả</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Ngày duyệt bảo lưu (từ ngày)</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <calendar
                                                        class="form-control calendar filter-selection"
                                                        :value="filter.min_approved_date"
                                                        :transfer="true"
                                                        :format="html.calendar.options.formatSelectDate"
                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                        :pane="1"
                                                        :disabled="html.calendar.is_disabled"
                                                        @input="selectMinApprovedDate"
                                                        :lang="html.calendar.lang"
                                                ></calendar>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="filter-label control-label">Ngày duyệt bảo lưu (đến ngày)</label><br/>
                                                <p class="input-group-addon filter-lbl">
                                                    <i class="fa fa-search"></i>
                                                </p>
                                                <calendar
                                                        class="form-control calendar filter-selection"
                                                        :value="filter.max_approved_date"
                                                        :transfer="true"
                                                        :format="html.calendar.options.formatSelectDate"
                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                        :pane="1"
                                                        :disabled="html.calendar.is_disabled"
                                                        @input="selectMaxApprovedDate"
                                                        :lang="html.calendar.lang"
                                                ></calendar>
                                            </div>
                                        </div>
                                    </div>
                                </b-collapse>
                                <!--<div class="text-center search-more">-->
                                    <!--<span v-b-toggle.search_more class="search-more-button"><i class="fa fa-gear"></i> Tìm kiếm nâng cao</span>-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <router-link to="/reserves/add-reserve" v-if="can_add"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm mới</button></router-link>
                        <router-link to="/reserves/add-multiple" v-if="can_add_supplement"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Bảo lưu cho lớp</button></router-link>
                        <router-link to="/reserves/approve" :class="can_approve"><button type="button" class="btn btn-warning"><i class="fa fa-paper-plane"></i> Danh sách chờ duyệt</button></router-link>
                        <button class="btn btn-success" @click="filterData(1)"><i class="fa fa-search"></i> Tìm kiếm</button>
                        <button class="btn btn-danger" @click="removeFilter()"><i class="fa fa-ban"></i> Bỏ lọc</button>
                    </div>
                </b-card>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <b-card header-tag="header" footer-tag="footer">
                    <div v-show="flags.form_loading" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
                        </div>
                    </div>
                    <div slot="header">
                        <strong>Danh sách bảo lưu</strong>
                    </div>
                    <div class="content-detail">
                        <div class="table-responsive scrollable">
                            <table class="table table-striped table-bordered apax-table">
                                <thead>
                                <tr class="text-sm">
                                    <th>STT</th>
                                    <th>Thời gian đăng ký</th>
                                    <th>Mã CMS</th>
                                    <!--<th>Mã EFFECT</th>-->
                                    <th>Tên học sinh</th>
                                    <th>Loại bảo lưu</th>
                                    <th>Trung tâm</th>
                                    <th>Lớp</th>
                                    <th>Số buổi bảo lưu</th>
                                    <th>Ngày bắt đầu bảo lưu</th>
                                    <th>Ngày kết thúc bảo lưu</th>
                                    <th>Giữ chỗ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(reserve, index) in list" :key="index">
                                    <td>{{ index+1 }}</td>
                                    <td>{{ reserve.created_at }}</td>
                                    <td>{{ reserve.crm_id }}</td>
                                    <!--<td>{{ reserve.accounting_id }}</td>-->

                                    <td>
                                        <router-link v-b-tooltip.hover :title="`Nhấp vào để xem chi tiết`" class="link-me" :to="`/reserves/${reserve.student_id}`">
                                            {{ reserve.student_name }}
                                        </router-link>
                                    </td>
                                    <td>{{ reserve.type | reserveType(reserve.is_supplement) }}</td>
                                    <td>{{ reserve.branch_name }}</td>
                                    <td>{{ reserve.class_name }}</td>
                                    <td>{{ reserve.session }}</td>
                                    <td>{{ reserve.start_date }}</td>
                                    <td>{{ reserve.end_date }}</td>
                                    <td>
                                        <span class="text-success" v-if="reserve.is_reserved==1"><i class="fa fa-check"></i></span>
                                        <span class="text-danger" v-else><i class="fa fa-times"></i></span>
                                    </td>
                                    <td>
                                        <span v-if="reserve.status == 0" class="text-warning">Chờ duyệt</span>
                                        <span v-else-if="reserve.status == 2" class="text-success">Đã duyệt</span>
                                        <span v-else-if="reserve.status == 3" class="text-danger">Từ chối</span>
                                        <span v-else-if="reserve.status == 4" class="text-danger">Đã hủy</span>
                                    </td>
                                    <td>
                                        <span class="apax-btn detail disabled">
                                          <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/reserves/${reserve.student_id}`"><i class="fa fa-eye"></i></router-link>
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="text-center">
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
                                            :routing="redirect">
                                    </paging>
                                </nav>
                            </div>
                        </div>
                    </div>
                </b-card>

            </div>
        </div>

    </div>

</template>

<script>
    import u from '../../../utilities/utility'
    import SearchBranch from '../../../components/SearchBranchForTransfer'
    import paging from '../../../components/Pagination'
    import calendar from 'vue2-datepicker'

    export default {
        name: 'reserve-list',
        components: {
            SearchBranch,
            paging,
            calendar
        },
        data() {
            return {
                filter: {
                    lms_accounting_id: '',
                    student_name: '',
                    reserve_type: '-1',
                    branch_id: '0',
                    semester_id: '0',
                    product_id: '0',
                    program_id: '0',
                    tuition_fee_id: '0',
                    class_id: '0',
                    min_sessions: 0,
                    max_sessions: 0,
                    creator_id: 0,
                    min_created_date: '',
                    max_created_date: '',
                    start_date: '',
                    end_date: '',
                    is_reserved: '-1',
                    min_approved_date: '',
                    max_approved_date: '',
                    approver_id: 0,
                    status: '-1',
                },
                html: {
                    filter: {
                        branch: {
                            id: 'search_branch',
                            placeholder: 'Tìm kiếm trung tâm',
                        },
                        student: {
                            disabled: false,
                        },
                        lms_effect: {
                            disabled: false,
                        },
                        product: {
                            disabled: false,
                        },
                        program: {
                            disabled: false,
                        },
                        type: {
                            disabled: false,
                        },
                        semester: {
                            disabled: false,
                        },
                        tuition_fee: {
                            disabled: false,
                        },
                        cls: {
                            disabled: false,
                        },
                        sessions: {
                            disabled: false,
                        },
                        creator: {
                            disabled: false,
                        },
                        created_at: {
                            disabled: false,
                        },
                        start_date: {
                           disabled: false,
                        },
                        end_date: {
                            disabled: false,
                        },
                        is_reserved: {
                            disabled: false,
                        },
                        approver: {
                            disabled: false,
                        },
                        approved_at: {
                            disabled: false,
                        },
                        status: {
                            disabled: false,
                        }
                    },
                    calendar: {
                        is_disabled: false,
                        options: {
                            formatSelectDate: 'YYYY-MM-DD',
                            disabledDaysOfWeek: [],
                            clearSelectedDate: true,
                            placeholderSelectDate: 'Chọn ngày',
                        },
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
                    }
                },
                flags: {
                    form_loading: false,
                    requesting: false
                },
                pagination: {
                    url: '',
                    id: '',
                    style: 'line',
                    class: '',
                    spage: 1,
                    ppage: 1,
                    npage: 1,
                    lpage: 1,
                    cpage: 1,
                    total: 0,
                    limit: 20,
                    pages: []
                },
                order: {
                    by: 's.id',
                    to: 'DESC'
                },
                list: [],
                can_approve: 'hide',
                can_add_supplement: false,
                can_add: false,
                filter_branch:true,
            }
        },
        created() {
            let role = parseInt(u.session().user.role_id);
            let approvable_roles = [ 85858585,u.r.region_ceo, u.r.founder, u.r.admin, u.r.super_administrator];
            let can_create_roles = [u.r.cm_cashier,  u.r.om_cashier, u.r.cm, u.r.om, u.r.branch_ceo, u.r.region_ceo, u.r.founder, u.r.admin, u.r.super_administrator];
             let can_add_supplement = [u.r.admin, u.r.super_administrator];

            if (can_add_supplement.indexOf(role) != -1) {
                this.can_add_supplement = true
            }
            if (approvable_roles.indexOf(role) != -1) {
                this.can_approve = ''
            }
            if (can_create_roles.indexOf(role) != -1) {
                this.can_add = true
            }
            if(u.session().user.branches.length==1){
                this.filter_branch=false
            }
            this.filterData();
        },
        methods: {
            prepareSearch(){

            },
            selectBranch(data){
                this.filter.branch_id = parseInt(data.id);
            },
            selectCustomerType(){

            },
            selectStartDate(date){
                this.filter.start_date = date;
            },
            selectEndDate(date){
                this.filter.end_date = date;
            },
            selectMinCreatedDate(date){
                this.filter.min_created_date = date;
            },
            selectMaxCreatedDate(date){
                this.filter.max_created_date = date;
            },
            selectMinApprovedDate(date){
                this.filter.min_approved_date = date;
            },
            selectMaxApprovedDate(date){
                this.filter.max_approved_date = date;
            },
            filterData(type = 0){
                if(this.flags.form_loading === false){
                    this.flags.form_loading = true;
                    if(type){
                        this.resetPagination();
                    }
                    let data = JSON.stringify(this.filter);
                    let pagination = JSON.stringify(this.pagination);
                    u.a().get(`/api/reserves?filter=${data}&pagination=${pagination}`)
                        .then(response => {
                            this.flags.form_loading = false;
                            this.list = response.data.data.items;
                            this.pagination = response.data.data.pagination;
                        });
                }
            },
            redirect(link) {
                const info = link.toString().split('/');
                const page = info.length > 1 ? info[1] : 1;
                this.pagination.cpage = parseInt(page);
                this.filterData();
            },
            resetPagination(){
                this.pagination = {
                    url: '',
                    id: '',
                    style: 'line',
                    class: '',
                    spage: 1,
                    ppage: 1,
                    npage: 1,
                    lpage: 1,
                    cpage: 1,
                    total: 0,
                    limit: 20,
                    pages: []
                }
            },
            removeFilter(){
                this.filter = {
                    lms_accounting_id: '',
                    student_name: '',
                    reserve_type: '-1',
                    branch_id: '0',
                    semester_id: '0',
                    product_id: '0',
                    program_id: '0',
                    tuition_fee_id: '0',
                    class_id: '0',
                    min_sessions: 0,
                    max_sessions: 0,
                    creator_id: 0,
                    min_created_date: '',
                    max_created_date: '',
                    start_date: '',
                    end_date: '',
                    is_reserved: '-1',
                    min_approved_date: '',
                    max_approved_date: '',
                    approver_id: 0,
                    status: '-1',
                };
                $("#search_branch").val('');
                this.filterData(1);
            }
        }
    }
</script>
<style scoped lang="scss">
    .search-more{
        margin-top: 10px;
    }
    .search-more-button{
        color: #e30000;
        cursor: pointer;
    }
    .hide{
        display: none;
    }
    .show{

    }
</style>
