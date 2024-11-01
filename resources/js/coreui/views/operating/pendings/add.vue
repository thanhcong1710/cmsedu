<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-id-card"></i> <b class="uppercase">Pending </b>
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
                                                <div class="col-12" :class="html.search_branch.display">
                                                    <label class="control-label">Trung tâm</label>
                                                    <SearchBranch
                                                            :searchId="html.search_branch.id"
                                                            :onSearchBranchReady="prepareSearch"
                                                            :onSelectBranch="selectBranch"
                                                            :placeholderBranch="html.search_branch.placeholder"
                                                            :limited="true"
                                                    >
                                                    </SearchBranch>
                                                </div>
                                                <div class="col-12" :class="html.search_contract.display">
                                                    <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc Tên</label>
                                                    <ContractSearch
                                                            :onSearchContract="searchContract"
                                                            :selectedContract="contract"
                                                            :onSelectContract="selectContract">
                                                    </ContractSearch>
                                                    <br/>
                                                </div>
                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã EFFECT</label>
                                                                <input class="form-control" type="text" readonly :value="contract.accounting_id">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã CMS</label>
                                                                <input class="form-control" type="text" readonly :value="contract.lms_id">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Họ Tên</label>
                                                                <input class="form-control" type="text" readonly :value="contract.student_name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tên Tiếng Anh</label>
                                                                <input class="form-control" type="text" readonly :value="contract.nick">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Sản phẩm</label>
                                                                <input class="form-control" type="text" readonly :value="contract.product_name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Chương trình</label>
                                                                <input class="form-control" type="text" readonly :value="contract.program_name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng số buổi học</label>
                                                                <input class="form-control" type="text" readonly :value="contract.real_sessions">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số phí đã đóng</label>
                                                                <input class="form-control" type="text" readonly :value="contract.total_charged | formatMoney">
                                                            </div>
                                                        </div>
                                                        <!--<div class="col-md-6">-->
                                                            <!--<div class="form-group">-->
                                                                <!--<label class="control-label">Số buổi còn lại</label>-->
                                                                <!--<input id="f_session_left" class="form-control" type="text" readonly :value="temp.number_of_session_left">-->
                                                            <!--</div>-->
                                                        <!--</div>-->
                                                        <!--<div class="col-md-6">-->
                                                            <!--<div class="form-group">-->
                                                                <!--<label class="control-label">Số phí còn lại</label>-->
                                                                <!--<input class="form-control" type="text" readonly :value="temp.amount_left">-->
                                                            <!--</div>-->
                                                        <!--</div>-->
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
                                                                <label class="control-label">Số ngày pending <strong class="text-danger h6">*</strong></label>
                                                                <input class="form-control" v-model="pending.number_of_sessions" :readonly="html.form_fields.number_of_sessions.readonly">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="filter-label control-label">Lý do pending <strong class="text-danger h6">*</strong></label><br/>
                                                                <select :disabled="html.form_fields.reason.readonly" v-model="reason" @change="selectReason(reason)" class="selection product form-control">
                                                                    <option value="-1" disabled> Chọn lý do pending </option>
                                                                    <option :value="reason.id" v-for="(reason, idx) in cache.pending_reasons" :key="idx">{{ reason.description }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu pending <strong class="text-danger h6">*</strong></label>
                                                                <calendar
                                                                        class="form-control calendar"
                                                                        :value="pending.start_date"
                                                                        :transfer="true"
                                                                        :format="html.calendar.options.formatSelectDate"
                                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                                        :pane="1"
                                                                        :disabled="html.calendar.is_disabled"
                                                                        :onDrawDate="onDrawDate"
                                                                        :lang="html.calendar.lang"
                                                                        :not-before="temp.min_date"
                                                                        @input="selectTransferDate"
                                                                ></calendar>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc pending</label>
                                                                <input class="form-control" type="text" readonly :value="temp.reserve_end_date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control" type="text" readonly :value="contract.start_date">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control" type="text" readonly :value="temp.new_end_date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <file
                                                                :label="'File đính kèm'"
                                                                :name="'upload_transfer_file'"
                                                                :field="'attached_file'"
                                                                :type="'transfer_file'"
                                                                :full="false"
                                                                :onChange="uploadFile"
                                                                :title="'Tải lên 1 file đính kèm với định dạng tài liệu: jpg, png, pdf, doc, docx.'"
                                                                :customClass="'no-current-file'"
                                                        >
                                                        </file>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="col-sm-12 col-sm-offset-3">
                                            <ApaxButton
                                                    :markup="html.buttons.save.style"
                                                    :onClick="confirm"
                                            >Lưu
                                            </ApaxButton>

                                            <ApaxButton
                                                    :onClick="exitAddPending"
                                            >Thoát
                                            </ApaxButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="callback" ok-variant="primary" ok-only>
                    <div v-html="html.modal.message">
                    </div>
                </b-modal>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.confirm.show" @ok="addPending" ok-variant="primary">
                    <div v-html="html.confirm.message">
                    </div>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'
    import calendar from 'vue2-datepicker'
    import u from '../../../utilities/utility'
    import ContractSearch from '../../../components/ContractSearch'
    import SearchBranch from '../../../components/SearchBranchForTransfer'
    import ApaxButton from '../../../components/Button'
    import file from '../../../components/File'

    export default {
        name: 'Add-Pending',
        components: {
            calendar,
            ContractSearch,
            SearchBranch,
            ApaxButton,
            file
        },
        data() {
            return {
                html: {
                    calendar: {
                        is_disabled: true,
                        options: {
                            formatSelectDate: 'YYYY-MM-DD',
                            disabledDaysOfWeek: [],
                            clearSelectedDate: true,
                            placeholderSelectDate: 'Chọn ngày bắt đầu',
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
                    },
                    search_branch: {
                        display: 'show',
                        placeholder: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
                        id: 'search_suggest_branch'
                    },
                    search_contract: {
                        display: 'hide',
                        placeholder: ''
                    },
                    modal: {
                        show: false,
                        message: ''
                    },
                    confirm: {
                        show: false,
                        message: ''
                    },
                    form_fields: {
                        number_of_sessions: {
                            readonly: true,
                            suggest: {
                                display: 'hide',
                            }
                        },
                        reason: {
                            readonly: true,
                        },
                        start_reserve_date: {
                            readonly: true,
                        },
                        attached_file: {
                            name: 'File đính kèm'
                        }
                    },
                    buttons:{
                        save: {
                            style: 'success'
                        }
                    }
                },
                pending: {
                    start_date: '',
                    number_of_sessions: '',
                    end_date: '',
                    new_end_date: '',
                    is_reserved: 0,
                    reason: '',
                    max_session: 0,
                    attached_file: '',
                },
                contract: {},
                class_days :{},
                branch: {
                    id: 0
                },
                reason: {},
                temp: {
                    number_of_session_left: 0,
                    amount_left: 0,
                    reserve_end_date: '',
                    new_end_date: '',
                    min_date: u.convertDateToString(new Date()),
                },
                cache: {
                    holidays: {},
                    pending_reasons: {}
                },
                flags: {
                    created_success: false,
                    selecting_date: false,
                    searching: false
                }
            }
        },
        mounted() {
            this.setDefaultBranch();
            this.getHolidays();
            this.getPendingReasons();
        },
        created(){

        },
        methods: {
            onDrawDate (e) {
                let date = e.date;
                date = u.convertDateToString(date);
                if (this.isGreaterThan(this.temp.min_date,date)) {
                    e.allowSelect = false;
                }
            },
            prepareSearch(check){
                if (!check) {
                    this.html.search_branch.display = 'show';
                } else {
                    this.html.search_branch.display = 'hide';
                }
            },
            selectBranch(data){
                this.branch.id = parseInt(data.id);
                this.html.search_contract.display = 'show';
            },
            selectTransferDate(date){
                if(!this.flags.selecting_date && u.validDate(date)){
                    this.flags.selecting_date = true;
                    this.pending.start_date = date;

                    let start_date = new Date(date);
                    let days = parseInt(this.pending.number_of_sessions);
                    start_date.setDate(start_date.getDate() + days);

                    this.temp.reserve_end_date = u.convertDateToString(start_date);
                    this.contract.start_date = this.temp.reserve_end_date;

                    this.temp.new_end_date = u.getRealSessions(this.temp.number_of_session_left,[2,5],this.cache.holidays,this.contract.start_date)['end_date'];
                    this.flags.selecting_date = false;
                }
            },
            callback(){
                if(this.flags.created_success){
                    this.$router.push('/pendings');
                }else{
                    this.html.modal.show = false;
                }
            },
            setDefaultBranch(){
                let rendered = false;
                while(!rendered){
                    if($("#search_suggest_branch").length){
                        rendered = true;
                        setTimeout(function(){}, 500);
                    }
                }
                const branches = u.session().user.branches;
                const user_branch_id = u.session().user.branch_id;
                if(branches.length){
                    for(let i in branches){
                        if(branches[i].id == user_branch_id){
                            this.branch.id = user_branch_id;
                            $("#search_suggest_branch").val(branches[i].name).prop('readonly',true);
                            this.html.search_contract.display = 'show';
                            break;
                        }
                    }
                }
            },
            searchContract(student_name){
                // if(!this.flags.searching) {
                //     this.flags.searching = true;
                    let url = '/api/pendings/suggest/' + (student_name ? student_name : '_') + '/' + this.branch.id;
                    return new Promise((resolve, reject) => {
                        u.a().get(url)
                            .then((response) => {
                                let resp = response.data.data;
                                resp = resp.length ? resp : [{
                                    contract_name: 'Không tìm thấy',
                                    label: 'Không có kết quả nào phù hợp'
                                }];
                                this.flags.searching = false;
                                resolve(resp)
                            })
                    })
                // }
            },
            selectContract(contract){
                this.contract = contract;

                if(this.isGreaterThan(contract.start_date, u.convertDateToString(new Date()))){
                    this.temp.min_date = contract.start_date;
                }

                this.contract.total_session = Math.ceil(this.contract.real_sessions * this.contract.total_charged/this.contract.must_charge);

                this.temp.number_of_session_left = this.contract.real_sessions;
                this.temp.amount_left = Math.ceil(this.contract.total_charged * this.temp.number_of_session_left/this.contract.real_sessions);
                this.temp.new_end_date = contract.end_date;

                this.html.form_fields.number_of_sessions.readonly = false;
                this.html.form_fields.reason.readonly = false;
                this.html.form_fields.start_reserve_date.readonly = false;
                this.html.form_fields.is_reserved = false;
                this.html.calendar.is_disabled = false;
                this.reason = '-1';
            },
            getHolidays(){
                let url = '/api/info/'+this.branch.id+'/holidays';
                u.a().get(url)
                    .then((response) => {
                        if(response.data.code == 200){
                            this.holidays = response.data.data;
                        }else{
                            this.holidays = []
                        }
                    });
            },
            getPendingReasons(){
                let url = '/api/info/reasons/pendings';
                u.a().get(url)
                    .then((response) => {
                        if(response.data.code == 200){
                            this.cache.pending_reasons = response.data.data;
                        }else{
                            this.cache.pending_reasons = []
                        }
                    });
            },
            isGreaterThan(_from, _to){
                let _from_time = new Date(_from); // Y-m-d
                let _to_time = new Date(_to); // Y-m-d
                return (_from_time.getTime() > _to_time.getTime())?true:false;
            },
            addPending(){
                if(this.validate()){
                    let data = {
                        student_id: this.contract.student_id,
                        contract_id: this.contract.contract_id,
                        reason_id: this.pending.reason,
                        start_date: this.pending.start_date,
                        end_date: this.temp.reserve_end_date,
                        session: this.pending.number_of_sessions,
                        branch_id: this.contract.branch_id,
                        product_id: this.contract.product_id,
                        program_id: this.contract.program_id,
                        new_end_date: this.temp.new_end_date,
                        attached_file: this.pending.attached_file,
                        meta_data: {
                            total_session: this.contract.real_sessions,
                            total_fee: this.contract.total_charged,
                            session_left: this.temp.number_of_session_left,
                            amount_left: this.temp.amount_left,
                            start_date: this.contract.start_date,
                            end_date: this.temp.new_end_date
                        }
                    };
                    u.a().post('/api/pendings', data)
                        .then(response => {
                            if(response.data.code == 200){
                                this.flags.created_success = true;
                                let message = "<span class='text-success'>Đăng ký thành công</span>";
                                this.showMessage(message);
                            }else{
                                let message = "<span class='text-danger'>"+response.data.message+"</span>";
                                this.showMessage(message);
                            }
                        }).catch(e => {
                        let message = "<span class='text-danger'>Có lỗi xảy ra. Vui lòng thử lại sau!</span>";
                        this.showMessage(message);
                    });
                }
            },
            exitAddPending(){
                this.$router.push('/pendings');
            },
            showMessage(message){
                this.html.modal.message = message;
                this.html.modal.show = true;
            },
            confirm(){
                let message = "<span class='text-default'>Bạn có chắc chắn thực hiện thao tác này?</span>";
                this.html.confirm.message = message;
                this.html.confirm.show = true;
            },
            selectReason(item){
                this.pending.reason = item;
            },
            validate(){
                let message = '';
                let valid = true;
                if(isNaN(this.pending.number_of_sessions) || this.pending.number_of_sessions <= 0){
                    message += '<span class="text-danger">Số ngày pending không hợp lệ</span></br>';
                    valid = false;
                }
                if(!this.pending.reason || parseInt(this.pending.reason) == -1){
                    message += '<span class="text-danger">Bạn chưa chọn Lý do pending</span></br>';
                    valid = false;
                }

                if(!moment(this.pending.start_date, "YYYY-MM-DD", true).isValid()){
                    message += '<span class="text-danger">Ngày bắt đầu pending không hợp lệ</span>';
                    valid = false;
                }

                this.html.modal.message = message;
                this.html.modal.show = true;

                return valid;
            },
            uploadFile(file, param = null) {
                if (param) {
                    this.pending.attached_file = file
                }
            },
        }
    }
</script>

<style scoped lang="scss">
    .hide {
        display: none;
    }
    .show {}
    .pass-trial {
        float: left;
        margin: 5px 5px 0 0;
    }
    .transparent{
        opacity: 0;
    }
    .apax-form textarea.form-control{
        height: unset;
        resize: none;
    }
    .apax-form .btn-upload{
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        text-align: left;
    }
</style>
