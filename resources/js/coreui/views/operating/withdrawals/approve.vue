<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
	            <div v-show="flags.form_loading" class="ajax-load content-loading">
	                <div class="load-wrapper">
	                    <div class="loader"></div>
	                    <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang xứ lý dữ liệu...</div>
	                </div>
	            </div>
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-clipboard"></i> <b class="uppercase">Nội dung rút phí</b>
                    </div>
                    <div id="page-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12 pad-no">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Tên học sinh</label>
                                                            <input class="form-control" :value="withdrawal.student_name"
                                                                type="text" readonly>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Mã CMS</label>
                                                            <input class="form-control" :value="withdrawal.cms_id"
                                                                type="text" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Mã Cyber </label>
                                                            <input class="form-control" :value="withdrawal.accounting_id"
                                                                type="text" readonly>
                                                        </div>
                                                    </div>
<!--                                                    <div class="col-md-4">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">Tên Tiếng Anh</label>-->
<!--                                                            <input class="form-control" :value="withdrawal.nick"-->
<!--                                                                type="text" readonly>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
                                                </div>
                                            </div>
                                            <div v-for="(contract, idc) in list_contract" :key="idc" class="col-md-12"  style="padding:0px">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Gói phí {{idc+1}}</h6>
                                                    </address>
                                                </div>
                                                    <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Trung tâm</label>
                                                                <input class="form-control" :value="contract.branch_name"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Sản phẩm</label>
                                                                <input class="form-control" :value="contract.product_name"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Lớp</label>
                                                                <input class="form-control"
                                                                    :value="contract.class_name" type="text"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>   
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số phí đã đóng</label>
                                                                <input class="form-control"
                                                                    :value="contract.total_charged | formatMoney" type="text"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng số buổi học</label>
                                                                <input class="form-control" :value="contract.real_sessions"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi đã học</label>
                                                                <input class="form-control" :value="list_contract[idc].done_sessions"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">        
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control"
                                                                    :value="contract.start_date" type="text"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control"
                                                                    :value="contract.end_date" type="text"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền còn lại</label>
                                                                <input class="form-control" :value="list_contract[idc].real_amount | formatMoney" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền bị khấu trừ</label>
                                                                <input class="form-control" :value="list_contract[idc].fee_amount | formatMoney" type="text" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <input :id="`enable_fee_` + idc" v-model="list_contract[idc].enable_editor_fee" type="checkbox" disabled> Sửa số tiền bị khấu trừ
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền thực nhận</label>
                                                                <input :id="`amount_` + idc" class="form-control" :value="list_contract[idc].refun_amount | formatMoney" type="text" readonly>
                                                            </div>
<!--                                                            <div class="form-group">-->
<!--                                                                <input :id="`enable_` + idc" v-model="list_contract[idc].enable_editor" type="checkbox" disabled> Sửa số tiền thực nhận-->
<!--                                                            </div>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="padding:0px">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Ngày rút phí <strong class="text-danger h6">*</strong></label>
                                                        <input class="form-control"
                                                            :value="withdrawal.withdraw_date" type="text"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div> 
                                             <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Tổng </h6>
                                                    </address>
                                                </div>
                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền còn lại</label>
                                                                <input class="form-control" :value="withdrawal.real_amount | formatMoney"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền bị khấu trừ</label>
                                                                <input class="form-control" :value="withdrawal.fee_amount | formatMoney"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền thực nhận</label>
                                                                <input class="form-control" :value="withdrawal.refun_amount | formatMoney"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Lý do rút phí</label>
                                                                <input class="form-control" :value="withdrawal.comment_created"
                                                                    type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Tệp tin đính kèm</label>
                                                                <file
                                                                    readonly
                                                                    :label="'Tải xuống file đính kèm'"
                                                                    :name="'upload_file'"
                                                                    :field="'attached_file'"
                                                                    :link="withdrawal.attached_file"
                                                                    :title="'Tải xuống file đính kèm!'"
                                                                >
                                                                </file>
                                                            </div>
                                                        </div>
                                                    </div>
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
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-list"></i> <b class="uppercase">Danh sách rút phí</b>
                        <router-link class="back-btn" :to="`/withdrawals`"><i class="fa fa-reply"></i> Quay lại</router-link>
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
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(withdraw, index) in withdrawal_fees" :key="index">
                                        <td>
                                            <span v-b-tooltip.hover
                                               title="Nhấp vào để xem chi tiết"
                                               class="link-me" @click="showDetail(withdraw.id)">
                                                {{index+1}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover href="#"
                                               title="Nhấp vào để xem chi tiết"
                                               class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.crm_id}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover href="#"
                                               title="Nhấp vào để xem chi tiết"
                                               class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.accounting_id}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover href="#"
                                               title="Nhấp vào để xem chi tiết"
                                               class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.student_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover href="#"
                                               title="Nhấp vào để xem chi tiết"
                                               class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.branch_name}}
                                            </span>
                                        </td>

                                        <td>
                                            <span v-b-tooltip.hover
                                                 title="Nhấp vào để xem chi tiết"
                                                 class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.product_name}}
                                            </span>
                                        </td>

                                        <td>
                                            <span v-b-tooltip.hover
                                                 title="Nhấp vào để xem chi tiết"
                                                 class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.program_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                 title="Nhấp vào để xem chi tiết"
                                                 class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.class_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                 title="Nhấp vào để xem chi tiết"
                                                 class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.refun_amount | formatMoney}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                 title="Nhấp vào để xem chi tiết"
                                                 class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.creator_name}}
                                            </span>
                                        </td>

                                        <td>
                                            <span v-b-tooltip.hover
                                                 title="Nhấp vào để xem chi tiết"
                                                 class="link-me" @click="showDetail(withdraw.id)">
                                                {{withdraw.created_at}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me">
                                                <span v-if="withdraw.status == 0" class="text-warning">Chờ duyệt</span>
                                                <span v-else-if="withdraw.status == 1" class="text-success">Đã duyệt</span>
                                                <span v-else-if="withdraw.status == 3" class="text-success">Đã hoàn phí</span>
                                                <span v-else class="text-danger">Từ chối</span>
                                            </span>
                                        </td>

                                        <td>
                                            <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me apax-btn detail disabled text-center" @click="showDetail(withdraw.id)">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                            <span v-b-tooltip.hover title="Nhấp vào để duyệt" class="apax-btn disabled edit text-center" v-if="withdraw.status == 0" @click="confirmApprove(withdraw.id)">
                                                <i class="fa fa-paper-plane"></i>
                                            </span>
                                            <span v-b-tooltip.hover title="Nhấp vào để từ chối" class="apax-btn disabled remove text-center" v-if="withdraw.status == 0" @click="confirmDeny(withdraw.id)">
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
                    <div>Bạn đã chắc chắn duyệt rút phí này?</div>
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
    import ApaxButton from '../../../components/Button'
    import spell from 'written-number'
    import file from '../../../views/components/File'

spell.defaults.lang = 'vi'

    export default {
        name: 'Tuition-Transfer-Detail',
        components: {
            ApaxButton,
            file
        },
        data() {
            return {
            	flags: {
                    form_loading: false,
                    requesting: false
                },
                success: 'success',
                student: {},
                students: [],
                branches: [],
                branch: {},
                withdrawal_fees: [],
                withdrawal: {},
                message:'',
                modal: false,
                approve_modal: false,
                deny_modal: false,
                deny_comment:'',
                withdraw_id: 0,
                validReason: 'hidden',
                list_contact:[],
            }
        },
        created() {
            let uri = '/api/withdrawals/requests';
            u.a().get(uri).then((response) => {
                if(response.data.code == 200){
                    this.withdrawal_fees = response.data.data;
                    if(this.withdrawal_fees.length){
                        this.showDetail(this.withdrawal_fees[0].id);
                    }
                }
            });
        },
        methods: {
            showDetail: function(withdraw_id) {
                let uri = '/api/withdrawals/detail/' + withdraw_id;
                u.a().get(uri).then((response) => {
                    if(response.data.code == 200){
                        this.withdrawal = response.data.data;
                        let meta_data = response.data.data.meta_data;

                        if(meta_data){
                            this.list_contract = JSON.parse(meta_data);
                        }
                    }
                });
            },

            approve() {
            	this.flags.form_loading = true;
                let withdraw_id = this.withdraw_id;
                let uri = '/api/withdrawals/'+withdraw_id+'/approve';
                u.a().put(uri).then((response) => {
                	this.flags.form_loading = false
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
            },
            deny() {
                let cm = this.deny_comment;
                let withdraw_id = this.withdraw_id;
                if(cm){
                    let uri = '/api/withdrawals/'+withdraw_id+'/deny';
                    this.flags.form_loading = true
                    u.a().put(uri, {comment: cm}).then((response) => {
                    	this.flags.form_loading = false
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
                    this.deny_modal = true;
                    this.validReason = 'display';
                }

            },
            callback(){

            },
            confirmApprove(id){
                this.withdraw_id = id;
                this.modal =false;
                this.deny_modal =false;
                this.approve_modal =true;
            },
            confirmDeny(id){
                this.deny_comment = '';
                this.withdraw_id = id;
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
                let uri = '/api/withdrawals/requests';
                u.a().get(uri).then((response) => {
                    this.withdrawal_fees = response.data.data;
                    if(this.withdrawal_fees.length){
                        this.showDetail(this.withdrawal_fees[0].id);
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
