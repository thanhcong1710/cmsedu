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
                        <i class="fa fa-clipboard"></i> <b class="uppercase">Nội dung chuyển lớp</b>
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
                                                        <h6 class="text-main">Thông tin lớp chuyển đi</h6>
                                                    </address>
                                                </div>

                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Trung tâm</label>
                                                                <input class="form-control" :value="transfer.from_branch"
                                                                       type="text" readonly />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã Cyber</label>
                                                                <input class="form-control" :value="transfer.accounting_id"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã CMS</label>
                                                                <input class="form-control" :value="transfer.lms_id"
                                                                       type="text" readonly/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Họ Tên</label>
                                                                <input class="form-control" :value="transfer.student_name"
                                                                       type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tên Tiếng Anh</label>
                                                                <input class="form-control" :value="transfer.nick"
                                                                       type="text" readonly/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Sản phẩm</label>
                                                                <input class="form-control" :value="transfer.from_product"
                                                                       type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Chương trình</label>
                                                                <input class="form-control"
                                                                       :value="transfer.from_program" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Lớp</label>
                                                                <input class="form-control"
                                                                       :value="transfer.class_name" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số phí đã đóng</label>
                                                                <input class="form-control"
                                                                       :value="transfer.f_total_fee | formatMoney" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng số buổi học</label>
                                                                <input class="form-control" :value="transfer.f_total_session"
                                                                       type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền chuyển</label>
                                                                <input class="form-control"
                                                                       :value="transfer.amount_transferred | formatMoney" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi chuyển</label>
                                                                <input class="form-control"
                                                                       :value="transfer.session_transferred" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control"
                                                                       :value="transfer.f_start_date" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control"
                                                                       :value="transfer.f_end_date" type="text"
                                                                       readonly/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pad-no">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Thông tin lớp chuyển đến</h6>
                                                    </address>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Kỳ học</label><br/>
                                                        <input class="form-control" :value="transfer.semester" type="text" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Gói Sản Phẩm</label><br/>
                                                        <input class="form-control" :value="transfer.to_product" type="text" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Chương Trình Học</label><br/>
                                                        <input class="form-control" :value="transfer.to_program" type="text" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Lớp</label><br/>
                                                        <input class="form-control" :value="transfer.to_class_name" type="text" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày chuyển lớp</label>
                                                                <input class="form-control" :value="transfer.transfer_date" type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Lý do chuyển lớp</label>
                                                                <textarea class="form-control" :value="transfer.note" rows="5" readonly></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền quy đổi</label>
                                                                <input class="form-control" :value="transfer.amount_exchange | formatMoney" type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi quy đổi</label>
                                                                <input class="form-control" :value="transfer.session_exchange" type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control" :value="transfer.t_start_date" type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control" :value="transfer.t_end_date" type="text" readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div v-if="transfer.enable_editor > 0"><input type="checkbox" disabled checked> Sửa số tiền nhận, số buổi nhận</div>
                                                                <div v-if="transfer.enable_editor == 0"><input type="checkbox" disabled> Sửa số tiền nhận, số buổi nhận</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6" v-if="transfer.attached_file">
                                                            <div class="form-group">
                                                                <a target="_blank" type="button" class="btn btn-primary btn-upload" :href="transfer.attached_file | genDownloadUrl" download><i class="fa fa-download"></i>&nbsp; Tải về file đính kèm</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="text-danger form-group" v-if="transfer.shift_checked > -1">
                                                <div v-if="transfer.shift_checked == 0"><input type="checkbox" disabled> Đã kiểm tra ca học của học sinh với các trường hợp đặc biệt ca học (Block) 1, ca học (Block) 2, ca học (Block) 3, ca học (Block) 4 của các ngày Thứ bảy - Chủ nhật</div>
                                                <div v-if="transfer.shift_checked == 1"><input type="checkbox" checked disabled> Đã kiểm tra ca học của học sinh với các trường hợp đặc biệt ca học (Block) 1, ca học (Block) 2, ca học (Block) 3, ca học (Block) 4 của các ngày Thứ bảy - Chủ nhật</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
                <b-card header>
                    <div v-show="flags.form_loading" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
                        </div>
                    </div>

                    <div slot="header">
                        <i class="fa fa-list"></i> <b class="uppercase">Danh sách chuyển lớp</b>
                        <router-link class="back-btn" :to="`/class-transfers`"><i class="fa fa-reply"></i> Quay lại</router-link>
                    </div>
                    <div id="list_content" class="panel-heading">
                        <div class="panel-body">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr class="text-sm">
                                        <th>STT</th>
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Tên học sinh</th>
                                        <th>Mã CMS</th>
                                        <!--<th>Mã EFFECT</th>-->
                                        <th>Lớp đi</th>
                                        <th>Lớp đến</th>
                                        <th>Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(branch_transfer, index) in branch_transfers"
                                        :key="index">
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{index+1}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{branch_transfer.creator}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{branch_transfer.created_at}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{branch_transfer.student_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{branch_transfer.lms_id}}
                                            </span>
                                        </td>
                                        <!--<td>-->
                                            <!--<span v-b-tooltip.hover-->
                                                  <!--title="Nhấp vào để xem chi tiết"-->
                                                  <!--class="link-me" @click="showDetail(branch_transfer.id)">-->
                                                <!--{{branch_transfer.accounting_id}}-->
                                            <!--</span>-->
                                        <!--</td>-->
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{branch_transfer.class_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(branch_transfer.id)">
                                                {{branch_transfer.to_class_name}}
                                            </span>
                                        </td>
                                        <td class="text-left">
                                            <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me apax-btn detail text-center" @click="showDetail(branch_transfer.id)">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                            <!--<span v-b-tooltip.hover title="Nhấp vào để duyệt" class="apax-btn edit text-center" v-if="branch_transfer.status < 2" @click="confirmApprove(branch_transfer.id, 0)">-->
                                                <!--<i class="fa fa-paper-plane"></i>-->
                                            <!--</span>-->
                                            <span v-b-tooltip.hover title="Nhấp vào để duyệt nhanh" class="apax-btn remove text-center" v-if="branch_transfer.status < 2" @click="confirmApprove(branch_transfer.id, 1)">
                                                <i class="fa fa-paper-plane"></i>
                                            </span>
                                            <span v-b-tooltip.hover title="Nhấp vào để từ chối" class="apax-btn remove text-center" v-if="branch_transfer.status < 2" @click="confirmDeny(branch_transfer.id)">
                                                <i class="fa fa-ban"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </b-card>
                <b-modal title="Xác nhận" class="modal-primary" size="sm" v-model="approve_modal" @ok="approve" ok-variant="primary">
                    <div v-show="mode == 0">Bạn đã chắn chắn duyệt chuyển lớp này?</div>
                    <div v-show="mode == 1">
                        Bạn đã chắn chắn duyệt nhanh chuyển lớp này?
                        <br>
                        <span class="text-danger">Lưu ý: Học sinh sẽ bị withdraw khỏi lớp ngay sau khi bạn duyệt chuyển lớp</span>
                    </div>
                </b-modal>
                <b-modal title="Xác nhận" class="modal-primary" size="sm" v-model="deny_modal" hide-footer>
                    <div class="form-group">
                        <div class="form-group">
                            <label class="control-label">Ý kiến phản hồi</label>
                            <textarea class="form-control" v-model="deny_comment"></textarea>
                            <span class="text-danger" :class="validReason">Thông tin này không được để trống</span>
                        </div>
                    </div>
                    <b-btn class="mt-3" variant="primary" @click="deny">OK</b-btn>
                    <b-btn class="mt-3" variant="default" @click="closeDenyModal">Hủy</b-btn>
                </b-modal>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="modal" @ok="callback" ok-variant="primary" ok-only>
                    <div v-html="message">
                    </div>
                </b-modal>
            </div>
        </div>
    </div>

</template>

<script>
    import u from '../../../utilities/utility'

    export default {
        name: 'class-transfers-approve',
        components: {

        },
        data() {
            return {
                success: 'success',
                student: {},
                students: [],
                branches: [],
                branch: {},
                branch_transfers: [],
                transfer: {
                    from_branch: '',
                    accounting_id: 0,
                    lms_id: 0,
                    student_name: '',
                    nick: '',
                    from_product: '',
                    from_program: '',
                    class_name: '',
                    f_total_session: 0,
                    f_total_fee: 0,
                    amount_transferred: 0,
                    session_transferred: 0,
                    f_start_date: '',
                    f_end_date: '',
                    semester: '',
                    to_product: '',
                    to_program: '',
                    to_class_name: '',
                    transfer_date: '',
                    note: '',
                    amount_exchange: 0,
                    session_exchange: 0,
                    t_start_date: '',
                    t_end_date: '',
                    attached_file: ''
                },
                message:'',
                modal: false,
                approve_modal: false,
                deny_modal: false,
                deny_comment:'',
                transfer_id: 0,
                mode: 0,
                validReason: 'hidden',
                flags: {
                    form_loading: false,
                    requesting: false
                },
            }
        },
        created() {
            this.flags.form_loading = true;
            let uri = '/api/class-transfers/requests';
            u.a().get(uri).then((response) => {
                this.flags.form_loading = false;
                if(response.data.code == 200){
                    this.branch_transfers = response.data.data;
                    if(this.branch_transfers.length){
                        this.showDetail(this.branch_transfers[0].id);
                    }
                }
            });
        },
        methods: {
            showDetail: function(transfer_id) {
                if(this.flags.requesting === false){
                    this.flags.requesting = true;
                    let uri = '/api/class-transfers/detail/' + transfer_id;
                    u.a().get(uri).then((response) => {
                        this.flags.requesting = false;
                        if(response.data.code == 200){
                            this.transfer = response.data.data;
                            let meta_data = response.data.data.meta_data;

                            if(meta_data){
                                meta_data = JSON.parse(meta_data);
                                console.log(meta_data);
                                this.transfer.f_total_fee = meta_data['total_fee'];
                                this.transfer.f_total_session = meta_data['total_session'];
                                this.transfer.f_start_date = meta_data['from_start_date'];
                                this.transfer.f_end_date = meta_data['from_end_date'];

                                this.transfer.t_start_date = meta_data['to_start_date'];
                                this.transfer.t_end_date = meta_data['to_end_date'];
                                this.transfer.shift_checked = parseInt(meta_data['shift_checked']);
                                this.transfer.enable_editor = parseInt(meta_data['enable_editor']);
                            }
                        }
                    });
                }
            },
            approve() {
                if(this.flags.requesting === false){
                    this.flags.requesting = true;
                    let tuition_transfer_id = this.transfer_id;
                    let uri = '/api/class-transfers/'+tuition_transfer_id+'/approve/'+this.mode;
                    u.a().put(uri).then((response) => {
                        this.flags.requesting = false;
                        if(response.data.code == 200){
                            this.approve_modal = false;
                            this.deny_modal = false;

                            let message = "<span class='text-success'>Thành công</span>";
                            this.showMessage(message);

                            this.updateRequests();
                        }else{
                            this.approve_modal = false;
                            this.deny_modal = false;
                            let message = "<span class='text-danger'>"+response.data.message+"</span>";
                            this.showMessage(message);
                        }
                    });
                }
            },
            deny() {
                if(this.flags.requesting === false) {
                    this.flags.requesting = true;
                    let cm = this.deny_comment;
                    let tuition_transfer_id = this.transfer_id;
                    if(cm){
                        let uri = '/api/class-transfers/'+tuition_transfer_id+'/deny';
                        u.a().put(uri, {comment: cm}).then((response) => {
                            this.flags.requesting = false;
                            if(response.data.code == 200){
                                this.approve_modal = false;
                                this.deny_modal = false;

                                let message = "<span class='text-success'>Thành công</span>";
                                this.showMessage(message);

                                this.updateRequests();
                            }else{
                                this.approve_modal = false;
                                this.deny_modal = false;
                                let message = "<span class='text-danger'>"+response.data.message+"</span>";
                                this.showMessage(message);
                            }
                        });
                    }else{
                        this.flags.requesting = false;
                        this.deny_modal = true;
                        this.validReason = 'display';
                    }
                }


            },
            callback(){

            },
            confirmApprove(id, mode=0){
                this.mode = mode;
                this.transfer_id = id;
                this.modal =false;
                this.deny_modal =false;
                this.approve_modal =true;
            },
            confirmDeny(id){
                this.deny_comment = '';
                this.transfer_id = id;
                this.modal =false;
                this.deny_modal =true;
                this.approve_modal =false;
            },
            showMessage(message){
                this.message = message;
                this.modal = true;
            },
            closeDenyModal(){
                this.deny_modal = false;
                this.validReason = 'hidden';
            },
            updateRequests(){
                this.flags.form_loading = true;
                let uri = '/api/class-transfers/requests';
                u.a().get(uri).then((response) => {
                    this.flags.form_loading = false;
                    this.branch_transfers = response.data.data;
                    if(this.branch_transfers.length){
                        this.showDetail(this.branch_transfers[0].id);
                    }else{
                        this.$router.push('/class-transfers');
                    }
                });
            }
        }
    }
</script>

<style scoped lang="scss">
    .transparent {
        opacity: 0;
    }
    .link-me{
        cursor: pointer;
    }
    .apax-form .btn-upload{
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        text-align: left;
    }
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
</style>
