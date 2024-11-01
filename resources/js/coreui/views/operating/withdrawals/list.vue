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
                                            <label class="filter-label control-label">Mã CMS hoặc mã Cyber</label><br/>
                                            <p class="input-group-addon filter-lbl">
                                                <i class="fa fa-search"></i>
                                            </p>
                                            <input type="text" class="form-control filter-selection" v-model="filter.cms_id" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Tên học sinh</label><br/>
                                            <p class="input-group-addon filter-lbl">
                                                <i class="fa fa-search"></i>
                                            </p>
                                            <input type="text" class="form-control filter-selection" v-model="filter.student_name"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="filter-label control-label">Trạng thái</label><br/>
                                            <p class="input-group-addon filter-lbl">
                                                <i class="fa fa-search"></i>
                                            </p>
                                            <select type="text" class="form-control filter-selection" v-model="filter.status" >
                                                <option value="-1">Tất cả</option>
                                                <option value="0">Chưa duyệt</option>
                                                <option value="1">Đã duyệt</option>
                                                <option value="2">Từ chối</option>
                                                <option value="3">Đã hoàn phí</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <button class="apax-btn full edit" @click="filterData(1)"><i class="fa fa-search"></i> Tìm kiếm</button>
                        <button v-show="onlyView()" type="button" class="apax-btn full remove" @click="addWithdrawal()" v-if="session.user.role_id!=68 &&session.user.role_id!=69 &&session.user.role_id!=84"><i class="fa fa-plus"></i> Tạo rút phí</button>
                        <button type="button" class="apax-btn full print" @click="approveWithdrawal()" v-if="session.user.role_id>=686868"><i class="fa fa-paper-plane"></i> Phê duyệt</button>
                        <button type="button" class="apax-btn full detail" @click="withdrawling()" v-if="session.user.role_id==84 || session.user.role_id>=999999999"><i class="fa fa-usd"></i> Hoàn phí</button>
                        <button class="apax-btn full reset" @click="removeFilter()"><i class="fa fa-ban"></i> Bỏ lọc</button>
                    </div>
                </b-card>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div v-show="flags.form_loading" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
                        </div>
                    </div>

                    <div slot="header">
                        <i class="fa fa-list"></i> <b class="uppercase">Danh sách rút phí</b>
                    </div>
                    <div class="controller-bar table-header">                        
                    </div>
                    <div id="list_content" class="panel-heading">
                        <div class="panel-body">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr class="text-sm">
                                        <th>STT</th>
                                        <th>Mã CMS</th>
                                        <th>Mã Cyber</th>
                                        <th>Tên học sinh</th>
                                        <th>Trung tâm</th>
                                        <th>Sản phẩm</th>
                                        <th>Chương trình</th>
                                        <th>Lớp</th>
                                        <th>Số tiền rút</th>
                                        <th>Người duyệt</th>
                                        <th>Ngày duyệt</th>
                                        <th>Người hoàn phí</th>
                                        <th>Ngày hoàn phí</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(withdraw, index) in list" :key="index">
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{index+1}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.crm_id}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.accounting_id}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.student_name}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.branch_name}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.product_name}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.program_name}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.class_name}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                 {{withdraw.refun_amount | formatMoney}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.approver_name}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.approved_at}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.refuner_name}}
                                            </router-link>
                                        </td>
                                        <td class="text-left">
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                {{withdraw.completed_at}}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết"
                                                         class="link-me"
                                                         :to="`withdrawals/${withdraw.id}`">
                                                <span v-if="withdraw.status == 0" class="text-warning">Chờ duyệt</span>
                                                <span v-else-if="withdraw.status == 1" class="text-warning">Chờ Kế toán hội sở hoàn phí</span>
                                                <span v-else-if="withdraw.status == 2" class="text-danger">Từ chối</span>
                                                <span v-else-if="withdraw.status == 4" class="text-danger">Kế toán từ chối</span>
                                                <span v-else class="text-success">Đã hoàn phí</span>
                                            </router-link>
                                        </td>
                                        <td>
                                            <span class="apax-btn detail disabled">
                                              <router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me"
                                                           :to="`withdrawals/${withdraw.id}`"><i
                                                      class="fa fa-eye"></i></router-link>
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
                    </div>
                </b-card>
            </div>
        </div>
    </div>
</template>

<style scoped language="scss">
    p.filter-lbl {
        width: 40px;
        height: 35px;
        float: left;
    }

    .filter-selection {
        width: calc(100% - 40px);
        float: left;
        padding: 3px 5px;
        height: 35px !important;
    }

    .controller-bar {
        margin: 0 0 15px 0;
    }

    .hide{
        display: none;
    }

    .table-responsive{
        padding: 10px 0;
    }
</style>

<script>
    import u from '../../../utilities/utility'
    import SearchBranch from '../../../components/SearchBranchForTransfer'
    import paging from '../../../components/Pagination'

    export default {
        components: {
            SearchBranch,
            paging,
        },
        name: 'tuition-transfers-list',
        data() {
            return {
                filter: {
                    cms_id: '',
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
                session: u.session(),
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
            onlyView(){
                return u.onlyView(u.session().user.role_id)
            },
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
                    u.a().get(`/api/withdrawals?filter=${data}&pagination=${pagination}`)
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
            addWithdrawal() {
                u.go(this.$router, '/withdrawals/add')
            },
            approveWithdrawal() {
                u.go(this.$router, '/withdrawals/approve')
            },
            withdrawling() {
                u.go(this.$router, '/withdrawals/refun')
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
                    cms_id: '',
                    student_name: '',
                    branch_id: '0',
                    status: '-1',
                };
                $("#search_branch").val('');
                this.filterData(1);
            }
        }
    }
</script>


