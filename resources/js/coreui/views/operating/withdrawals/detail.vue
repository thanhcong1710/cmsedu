<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
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
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Họ Tên</label>
                                                            <input class="form-control" :value="withdrawal.student_name"
                                                                type="text" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Tên Tiếng Anh</label>
                                                            <input class="form-control" :value="withdrawal.nick"
                                                                type="text" readonly>
                                                        </div>
                                                    </div>
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
                                                        <h6 class="text-main">Tổng</h6>
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
                                                                   readonly="true"
                                                                   :label="'Click để tải tệp tin'"
                                                                   :field="'attached_file'"
                                                                   :type="'transfer_file'"
                                                                   :full="false"
                                                                   :link="validFile(withdrawal.attached_file)"
                                                                   :title="'Tải file xuống'"
                                                                   :customClass="'no-current-file'"
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
                    <ApaxButton
                            :markup="success"
                            :onClick="callPrintForm"
                    >In Đơn Xin Rút Phí
                    </ApaxButton>
                    <router-link class="apax-btn full default" :to="`/withdrawals`"><i class="fa fa-reply"></i> Quay lại</router-link>
                </b-card>
            </div>
        </div>
    </div>

</template>

<script>
    import u from '../../../utilities/utility'
    import ApaxButton from '../../../components/Button'
    import spell from 'written-number'
    import file from '../../components/File'

spell.defaults.lang = 'vi'

    export default {
        name: 'Tuition-Transfer-Detail',
        components: {
            ApaxButton,
            file
        },
        data() {
            return {
                success: 'success',
                student: {},
                students: [],
                branches: [],
                branch: {},
                withdrawal_fees: [],
                withdrawal: {},
                list_contact:[],
                total_charged_all: 0
            }
        },
        created() {
            this.showDetail()
        },
        methods: {
            showDetail() {
                let uri = '/api/withdrawals/detail/' + this.$route.params.id
                u.a().get(uri).then((response) => {
                    if(response.data.code == 200){
                        this.withdrawal = response.data.data;
                        let meta_data = response.data.data.meta_data;
                        let total = 0;
                        if(meta_data){
                            this.list_contract = JSON.parse(meta_data);
                            for(let i in JSON.parse(meta_data)){
                                total += parseInt(JSON.parse(meta_data)[i].total_charged)
                            }
                        }
                        this.total_charged_all = total
                    }
                });
            },
            callPrintForm() {
                let printing_data = {
                    // withdrawal_fees : this.withdrawal_fees,
                    withdrawal : this.withdrawal,
                    total_charged_all : this.total_charged_all
                }
                
                localStorage.setItem(`tuitionwd_${this.$route.params.id}`, JSON.stringify(printing_data))
                window.open(`/print/tuition-withdraw/${this.$route.params.id}`,'_blank')
            },
            validFile (file) {
                let resp = file && (typeof file === 'string') ? file : ''
                if (file && typeof file === 'object')
                    resp = `${file.type},${file.data}`

                return resp
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
