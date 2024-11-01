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
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Mã CMS/Effect</label><br/>
                                            <p class="input-group-addon filter-lbl">
                                                <i class="fa fa-search"></i>
                                            </p>
                                            <input type="text" class="form-control filter-selection" v-model="filter.lms_accounting_id" :disabled="html.filter.lms_effect.disabled"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Tên học sinh</label><br/>
                                            <p class="input-group-addon filter-lbl">
                                                <i class="fa fa-search"></i>
                                            </p>
                                            <input type="text" class="form-control filter-selection" v-model="filter.student_name" :disabled="html.filter.student.disabled"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Trạng thái</label><br/>
                                            <p class="input-group-addon filter-lbl">
                                                <i class="fa fa-search"></i>
                                            </p>
                                            <select type="text" class="form-control filter-selection" v-model="filter.status" :disabled="html.filter.status.disabled">
                                                <option value="-1">Tất cả</option>
                                                <option value="0">Chưa duyệt</option>
                                                <option value="1">Đã duyệt</option>
                                                <option value="2">Từ chối</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <button class="btn btn-success" @click="filterData(1)"><i class="fa fa-search"></i> Tìm kiếm</button>
                        <button class="btn btn-danger" @click="removeFilter()"><i class="fa fa-ban"></i> Bỏ lọc</button>
                    </div>
                </b-card>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <b-card header-tag="header" footer-tag="footer">
                    <div slot="header">
                        <strong>Danh sách pending</strong>
                    </div>
                    <div class="controller-bar table-header">
                        <router-link to="/pendings/add-pending"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm mới</button></router-link>
                        <router-link to="/pendings/approve" :class="can_approve"><button type="button" class="btn btn-warning"><i class="fa fa-paper-plane"></i> Duyệt</button></router-link>
                    </div>
                    <div class="content-detail">
                        <div class="table-responsive scrollable">
                            <table class="table table-striped table-bordered apax-table">
                                <thead>
                                <tr class="text-sm">
                                    <th>STT</th>
                                    <th>Mã CMS</th>
                                    <th>Mã EFFECT</th>
                                    <th>Tên học sinh</th>
                                    <th>Trung tâm</th>
                                    <th>Lý do pending</th>
                                    <th>Ngày bắt đầu pending</th>
                                    <th>Số ngày pending</th>
                                    <th>Ngày kết thúc pending</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(reserve, index) in list" :key="index">
                                    <td>{{ index+1 }}</td>

                                    <td>{{ reserve.lms_id }}</td>
                                    <td>{{ reserve.accounting_id }}</td>
                                    <td>{{ reserve.student_name }}</td>

                                    <td>{{ reserve.branch_name }}</td>
                                    <td>{{ reserve.description }}</td>
                                    <td>{{ reserve.start_date }}</td>
                                    <td>{{ reserve.session }}</td>
                                    <td>{{ reserve.end_date }}</td>

                                    <td>
                                        <span v-if="reserve.status == 0" class="text-warning">Chờ duyệt</span>
                                        <span v-else-if="reserve.status == 1" class="text-success">Đã duyệt</span>
                                        <span v-else class="text-danger">Từ chối</span>
                                    </td>
                                    <td>
                                        <span class="apax-btn detail disabled">
                                          <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/pendings/${reserve.student_id}`"><i class="fa fa-eye"></i></router-link>
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
    import search from '../../../components/Search'

    export default {
        name: 'List-Reserve',
        components: {
            SearchBranch,
            paging,
            search
        },
        data() {
            return {
                filter: {
                    lms_accounting_id: '',
                    student_name: '',
                    branch_id: '0',
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
                        status: {
                            disabled: false,
                        }
                    },
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
                can_approve: 'hide'
            }
        },
        created() {
            let role = parseInt(u.session().user.role_id);
            let approvable_roles = [u.r.om, u.r.branch_ceo, u.r.region_ceo, u.r.founder, u.r.admin, u.r.super_administrator];
            if (approvable_roles.indexOf(role) != -1) {
                this.can_approve = '';
            }
            this.filterData();
        },
        methods: {
            prepareSearch(){

            },
            selectBranch(data){
                this.filter.branch_id = parseInt(data.id);
            },
            filterData(type = 0){
                if(this.flags.form_loading === false){
                    this.flags.form_loading = true;
                    if(type){
                        this.resetPagination();
                    }
                    let data = JSON.stringify(this.filter);
                    let pagination = JSON.stringify(this.pagination);
                    u.a().get(`/api/pendings?filter=${data}&pagination=${pagination}`)
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

<style scoped>
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
