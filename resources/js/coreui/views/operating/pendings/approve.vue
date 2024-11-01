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
                                                                <label class="control-label">Ngày bắt đầu bảo lưu</label>
                                                                <input class="form-control" readonly :value="reserve.start_date"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc bảo lưu</label>
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
                                    <th>Mã LMS</th>
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
                                <tr v-for="(reserve, index) in reserves" :key="index">
                                    <td>{{ index+1 }}</td>

                                    <td>{{ reserve.created_at }}</td>
                                    <td>{{ reserve.creator }}</td>
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
                                        <span class="apax-btn detail disabled" @click="showReserve(reserve)">
                                          <i class="fa fa-eye"></i>
                                        </span>
                                        <span class="apax-btn disabled edit text-center" @click="confirmApprove(reserve)">
                                          <i class="fa fa-paper-plane"></i>
                                        </span>
                                        <span class="apax-btn disabled remove text-center" @click="confirmDeny(reserve)">
                                          <i class="fa fa-ban"></i>
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
                </b-card>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="closeModal" ok-variant="primary" ok-only>
                    <div v-html="html.modal.message">
                    </div>
                </b-modal>
                <b-modal title="Xác nhận" class="modal-primary" size="sm" v-model="html.approve_modal.show" @ok="approve" ok-variant="primary">
                    <div>Bạn đã chắc chắn duyệt đăng ký pending này?</div>
                </b-modal>
                <b-modal title="Xác nhận" class="modal-primary" size="sm" v-model="html.deny_modal.show" hide-footer>
                    <div class="form-group">
                        <div class="form-group">
                            <label class="control-label">Ý kiến phản hồi</label>
                            <textarea class="form-control" v-model="html.deny_modal.comment"></textarea>
                            <span class="text-danger" :class="html.deny_modal.validReason">Thông tin này không được để trống</span>
                        </div>
                    </div>
                    <b-btn class="mt-3" variant="primary" @click="deny">OK</b-btn>
                    <b-btn class="mt-3" variant="default" @click="closeDenyModal">Hủy</b-btn>
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

    export default {
        name: 'List-Reserve',
        components: {
            SearchBranch,
            paging,
            search
        },
        data() {
            return {
                branches: [],
                branch: '',
                pagination: {},
                reserves: [],
                reserve: {
                    meta_data: {}
                },
                search_id: 'contract_search',
                search_name: 'search-contract',
                search_label: 'Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh',
                search_placeholder: 'Từ khóa tìm kiếm',
                disableSearch: true,
                placeholderSearchBranch: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
                html: {
                    modal: {
                        show: false,
                        message: ''
                    },
                    approve_modal: {
                        show: false,
                        message: ''
                    },
                    deny_modal: {
                        show: false,
                        message:'',
                        comment: '',
                        validReason: ''
                    }
                }
            }
        },
        created() {
            u.a().get('/api/branches').then(response => {
                this.branches = response.data;
            });
            this.getReserves()
        },
        methods: {
            closeModal(){
                this.html.modal.show = false;
            },
            getReserves() {
                u.a().get('/api/pendings-info/requests')
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
            approve() {
                let reserve_id = this.reserve.id;
                let uri = '/api/pendings/'+reserve_id+'/approve';
                u.a().put(uri).then((response) => {
                    this.html.approve_modal.show = false;
                    this.html.deny_modal.show = false;
                    if(response.data.code == 200){
                        let message = "<span class='text-success'>Thành công</span>";
                        this.showMessage(message);

                        this.updateRequests();
                    }else{
                        let message = "<span class='text-danger'>"+response.data.message+"</span>";
                        this.showMessage(message);
                    }
                });
            },
            deny() {
                let cm = this.html.deny_modal.comment;
                let reserve_id = this.reserve.id;
                if(cm){
                    let uri = '/api/pendings/'+reserve_id+'/deny';
                    u.a().put(uri, {comment: cm}).then((response) => {
                        this.html.approve_modal.show = false;
                        this.html.deny_modal.show = false;
                        if(response.data.code == 200){
                            let message = "<span class='text-success'>Thành công</span>";
                            this.showMessage(message);
                            this.updateRequests();
                        }else{
                            let message = "<span class='text-danger'>"+response.data.message+"</span>";
                            this.showMessage(message);
                        }
                    });
                }else{
                    this.html.deny_modal.show = true;
                    this.html.deny_modal.validReason = 'display';
                }

            },
            closeDenyModal(){
                this.html.deny_modal.show = false;
            },
            showMessage(message){
                this.html.modal.message = message;
                this.html.modal.show = true;
            },
            confirmApprove(reserve){
                this.reserve = reserve;
                this.html.approve_modal.show = true;
            },
            confirmDeny(reserve){
                this.reserve = reserve;
                this.html.deny_modal.comment = '';
                this.html.deny_modal.show = true;
            },
            updateRequests(){
                let uri = '/api/pendings-info/requests';
                u.a().get(uri).then((response) => {
                    if(response.data.code == 200){
                        this.reserves = response.data.data;
                        if(this.reserves.length){
                            this.showReserve(this.reserves[0]);
                        }else{
                            this.$router.push("/pendings")
                        }
                    }else{
                        this.html.modal.message = "<span class='text-danger'>"+response.data.message+"</span>";
                        this.html.modal.show = true;
                        this.reserve = {};
                    }
                });
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
