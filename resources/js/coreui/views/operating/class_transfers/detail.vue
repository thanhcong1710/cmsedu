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
                                                                       type="text" readonly>
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
                                                                <input class="form-control" :value="transfer.student_name"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tên Tiếng Anh</label>
                                                                <input class="form-control" :value="transfer.nick"
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
                                                                <input class="form-control" :value="transfer.from_product"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Chương trình</label>
                                                                <input class="form-control"
                                                                       :value="transfer.from_program" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Lớp</label>
                                                                <input class="form-control"
                                                                       :value="transfer.class_name" type="text"
                                                                       readonly>
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
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng số buổi học</label>
                                                                <input class="form-control" :value="transfer.f_total_session"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số tiền chuyển</label>
                                                                <input class="form-control"
                                                                       :value="transfer.amount_transferred | formatMoney" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi chuyển</label>
                                                                <input class="form-control"
                                                                       :value="transfer.session_transferred" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control"
                                                                       :value="transfer.f_start_date" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control"
                                                                       :value="transfer.f_end_date" type="text"
                                                                       readonly>
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
                                                        <input class="form-control" :value="transfer.semester" type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Gói Sản Phẩm</label><br/>
                                                        <input class="form-control" :value="transfer.to_product" type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Chương Trình Học</label><br/>
                                                        <input class="form-control" :value="transfer.to_program" type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Lớp</label><br/>
                                                        <input class="form-control" :value="transfer.to_class_name" type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày chuyển lớp</label>
                                                                <input class="form-control" :value="transfer.transfer_date" type="text" readonly>
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
                                                                <input class="form-control" :value="transfer.amount_exchange | formatMoney" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi quy đổi</label>
                                                                <input class="form-control" :value="transfer.session_exchange" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control" :value="transfer.t_start_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control" :value="transfer.t_end_date" type="text" readonly>
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
                    <div>
                        <button class="btn btn-success print-button" @click="callPrintForm">In đơn chuyển lớp</button>
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
                                        <th>Lớp đi</th>
                                        <th>Lớp đến</th>
                                        <th>Người duyệt</th>
                                        <th>Ngày duyệt</th>
                                        <th>Ghi chú duyệt</th>
                                        <!--<th>Trạng thái</th>-->
                                        <th>Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(class_transfer, index) in class_transfers"
                                        :key="index">
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{index+1}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.creator}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.created_at}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.class_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.to_class_name}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.from_approver}}
                                            </span>
                                        </td>

                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.from_approved_at}}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-b-tooltip.hover
                                                  title="Nhấp vào để xem chi tiết"
                                                  class="link-me" @click="showDetail(class_transfer.id)">
                                                {{class_transfer.from_approve_comment}}
                                            </span>
                                        </td>
                                        <!--<td>-->
                                            <!--<span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me">-->
                                                <!--<span v-if="class_transfer.status < 3" class="text-warning">Chờ duyệt</span>-->
                                                <!--<span v-else-if="class_transfer.status == 3" class="text-success">Đã duyệt</span>-->
                                                <!--<span v-else class="text-danger">Từ chối</span>-->
                                            <!--</span>-->
                                        <!--</td>-->

                                        <td class="text-left">
                                            <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me apax-btn detail disabled text-center" @click="showDetail(class_transfer)">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                            <!--<span v-b-tooltip.hover title="Nhấp vào để xóa bỏ" class="link-me apax-btn detail disabled remove text-center" v-if="class_transfer.status == 0">-->
                                              <!--<i class="fa fa-remove"></i>-->
                                            <!--</span>-->
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
                    </div>
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
    import ApaxButton from '../../../components/Button'
    import spell from 'written-number'

    spell.defaults.lang = 'vi'

    export default {
        name: 'Tuition-Transfer-Detail',
        components: {
            ApaxButton
        },
        data() {
            return {
                html: {
                    modal: {
                        show: false,
                        message: ''
                    }
                },
                flags: {
                    form_loading: false,
                    requesting: false
                },
                success: 'success',
                class_transfers: [],
                transfer: [],
                item: {}
            }
        },
        created() {
            this.flags.form_loading = true;
            let uri = '/api/class-transfers/' + this.$route.params.id;
            u.a().get(uri).then((response) => {
                this.flags.form_loading = false;
                if(response.data.code == 200){
                    this.class_transfers = response.data.data;
                    if(this.class_transfers.length){
                        this.showDetail(this.class_transfers[0]);
                    }
                }else{
                    this.html.modal.message = "<span class='text-danger'>"+response.data.message+"</span>";
                    this.html.modal.show = true;
                }
            });
        },
        methods: {
            callback(){
                this.html.modal.show = false;
            },
            showDetail(transfer){
                // u.log(transfer.class_name);
                this.transfer = transfer;
                // console.log(this.transfer.id);
                let meta_data = transfer.meta_data;

                if(meta_data){
                    meta_data = JSON.parse(transfer.meta_data);
                    this.transfer.f_total_fee = meta_data.total_fee;
                    this.transfer.f_total_session = meta_data.total_session;
                    this.transfer.f_start_date = meta_data.from_start_date;
                    this.transfer.f_end_date = meta_data.from_end_date;

                    this.transfer.t_start_date = meta_data.to_start_date;
                    this.transfer.t_end_date = meta_data.to_end_date;
                    this.transfer.shift_checked = parseInt(meta_data.shift_checked);
                    this.transfer.enable_editor = parseInt(meta_data.enable_editor);
                }
                const class_transfer_id = this.transfer.id
                // console.log('test '+class_transfer_id);
                this.callPrintFunc(class_transfer_id)
            },
            callPrintFunc(id){
                u.a().get(`/api/class-transfers/print/${id}`).then(response => {

                    this.item = response.data.data;
                    console.log(`class transfer ${response.data.data}`);
                })
            },
            callPrintForm() {
                window.open(`/print/class-transfer/${this.transfer.id}`, '_blank');
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
    .card-header .btn-upload{
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
