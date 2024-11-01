<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-clipboard"></i> <b class="uppercase">Nội dung pending</b>
                    </div>
                    <div id="page-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 pad-no">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Thông tin học sinh</h6>
                                                    </address>
                                                </div>

                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Trung tâm</label>
                                                                <input class="form-control" :value="reserve.branch_name"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã EFFECT</label>
                                                                <input class="form-control" :value="reserve.accounting_id"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã CMS</label>
                                                                <input class="form-control" :value="reserve.lms_id"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Họ Tên</label>
                                                                <input class="form-control" :value="reserve.student_name"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tên Tiếng Anh</label>
                                                                <input class="form-control" :value="reserve.nick"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Sản phẩm</label>
                                                                <input class="form-control" :value="reserve.product_name"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Chương trình</label>
                                                                <input class="form-control"
                                                                       :value="reserve.program_name" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng số buổi học</label>
                                                                <input class="form-control" :value="reserve.meta_data.total_session"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số phí đã đóng</label>
                                                                <input class="form-control"
                                                                       :value="reserve.meta_data.total_fee | formatMoney" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pad-no">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Thông tin pending</h6>
                                                    </address>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số ngày pending</label>
                                                                <input class="form-control" readonly :value="reserve.session">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Lý do pending</label>
                                                                <textarea class="form-control" readonly rows="5" v-model="reserve.description"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu pending</label>
                                                                <input class="form-control" readonly :value="reserve.start_date"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc pending</label>
                                                                <input class="form-control" :value="reserve.end_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control" :value="reserve.meta_data.start_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control" :value="reserve.meta_data.end_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="reserve.attached_file">
                                                    <div class="form-group">
                                                        <a target="_blank" type="button" class="btn btn-primary btn-upload" :href="reserve.attached_file | genDownloadUrl"><i class="fa fa-download"></i>&nbsp; Tải về file đính kèm</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <b-card header-tag="header" footer-tag="footer">
                    <div slot="header">
                        <strong>Danh sách pending</strong>
                        <router-link class="back-btn" :to="`/pendings`"><i class="fa fa-reply"></i> Quay lại</router-link>
                    </div>
                    <div class="content-detail">
                        <div class="table-responsive scrollable">
                            <table class="table table-striped table-bordered apax-table">
                                <thead>
                                <tr class="text-sm">
                                    <th>STT</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Người đăng ký</th>
                                    <th>Trung tâm</th>
                                    <th>Lý do pending</th>
                                    <th>Ngày bắt đầu pending</th>
                                    <th>Số ngày pending</th>
                                    <th>Ngày kết thúc pending</th>
                                    <th>Ngày duyệt</th>
                                    <th>Người duyệt</th>
                                    <th>Ghi chú người duyệt</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(reserve, index) in reserves" :key="index">
                                    <td>{{ index+1 }}</td>

                                    <td>{{ reserve.created_at }}</td>
                                    <td>{{ reserve.creator }}</td>
                                    <td>{{ reserve.branch_name }}</td>

                                    <td>{{ reserve.description }}</td>
                                    <td>{{ reserve.start_date }}</td>
                                    <td>{{ reserve.session }}</td>
                                    <td>{{ reserve.end_date }}</td>
                                    <td>{{ reserve.approved_at }}</td>
                                    <td>{{ reserve.approver }}</td>
                                    <td>{{ reserve.comment }}</td>

                                    <td>
                                        <span v-if="reserve.status == 0" class="text-warning">Chờ duyệt</span>
                                        <span v-else-if="reserve.status == 1" class="text-success">Đã duyệt</span>
                                        <span v-else class="text-danger">Từ chối</span>
                                    </td>
                                    <td>
                                        <span class="apax-btn detail disabled" @click="showReserve(reserve)">
                                          <i class="fa fa-eye"></i>
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="text-center">
                                <nav aria-label="Page navigation">

                                </nav>
                            </div>
                        </div>
                    </div>
                    <reserve :item="reserve" ref="form" v-show="false"/>
                </b-card>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="callback" ok-variant="primary" ok-only>
                    <div v-html="html.modal.message">
                    </div>
                </b-modal>
            </div>
        </div>

    </div>

</template>

<script>
    import u from '../../../utilities/utility'
    import SearchBranch from '../../../components/SearchBranchForTransfer'
    import paging from '../../../components/Pagination'
    import search from '../../../components/Search'
    import reserve from '../../base/prints/reserve'
    import ApaxButton from '../../../components/Button'

    export default {
        name: 'List-Reserve',
        components: {
            SearchBranch,
            ApaxButton,
            paging,
            search,
            reserve
        },
        data() {
            return {
                html: {
                    modal: {
                        show: false,
                        message: ''
                    }
                },
                success: 'success',
                branches: [],
                branch: '',
                pagination: {},
                reserves: [],
                reserve: {
                    meta_data: {}
                },
                search_id: 'contract_search',
                search_name: 'search-contract',
                search_label: 'Tìm kiếm theo mã CMS, Tên học sinh',
                search_placeholder: 'Từ khóa tìm kiếm',
                disableSearch: true,
                placeholderSearchBranch: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
            }
        },
        created() {
            u.a().get('/api/branches').then(response => {
                this.branches = response.data;
            });
            this.getReserves()
        },
        methods: {
            callback(){
                this.html.modal.show = false;
            },
            getReserves(page_url) {
                page_url = page_url || '/api/pendings-info/' + this.$route.params.id;
                u.a().get(page_url)
                    .then(response => {
                        if(response.data.code == 200){
                            this.reserves = response.data.data;
                            if(this.reserves.length){
                                this.showReserve(this.reserves[0]);
                            }
                        }else{
                            this.html.modal.message = "<span class='text-danger'>"+response.data.message+"</span>";
                            this.html.modal.show = true;
                        }
                    }).catch(e => console.log(e));
            },
            showReserve(reserve){
                this.reserve = reserve;
                if((typeof reserve.meta_data) == 'string'){
                    this.reserve.meta_data = JSON.parse(reserve.meta_data);
                }
            },
            callPrintForm() {
                this.$refs.form.callPrintForm()
            }
        }
    }
</script>

<style scoped>
    .card-header .back-btn{
        font-size: 12px;
        padding: 2px 10px;
        background: #a4b7c1;
        color: #151b1e;
        text-shadow: none;
        text-transform: none;
        text-decoration: none;
        float: right;
        position: absolute;
        right: 34px;
        top: 9px;
        line-height: 20px;
    }
    .apax-form .btn-upload{
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        text-align: left;
    }
</style>
