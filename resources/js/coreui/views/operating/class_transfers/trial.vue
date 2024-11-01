<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div v-show="flags.form_loading" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
                        </div>
                    </div>
                    <div v-show="flags.requesting" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="flags.requesting" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
                        </div>
                    </div>

                    <div slot="header">
                        <i class="fa fa-id-card"></i> <b class="uppercase">Chuyển lớp </b>
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
                                                <div class="col-12 pad-no">
                                                    <div class="form-group">
                                                        <label class="control-label">Trung tâm</label>
                                                        <SearchBranch
                                                                :searchId="html.transferring.search_branch.id"
                                                                :onSearchBranchReady="prepareTransferringSearch"
                                                                :onSelectBranch="selectTransferringBranch"
                                                                :placeholderBranch="html.transferring.search_branch.placeholder"
                                                                :limited="true"
                                                        >
                                                        </SearchBranch>
                                                    </div>
                                                </div>
                                                <div class="col-12 pad-no" :class="html.transferring.search_contract.display">
                                                    <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc Tên</label>
                                                    <ContractSearch
                                                            :onSearchContract="searchContract"
                                                            :selectedContract="data.contract"
                                                            :onSelectContract="selectContract">
                                                    </ContractSearch>
                                                    <br/>
                                                </div>
                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã Cyber</label>
                                                                <input class="form-control" :value="data.contract.accounting_id" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã CMS</label>
                                                                <input class="form-control" :value="data.contract.lms_id" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Họ Tên</label>
                                                                <input class="form-control" :value="data.contract.student_name" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tên Tiếng Anh</label>
                                                                <input class="form-control" :value="data.contract.nick" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Sản phẩm</label>
                                                                <input class="form-control" :value="data.contract.product_name" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Chương trình</label>
                                                                <input class="form-control" :value="data.contract.program_name" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Lớp</label>
                                                                <input class="form-control" :value="data.contract.class_name" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">Tổng số buổi học</label>
                                                                        <input class="form-control" :value="data.contract.real_sessions" type="text" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi còn lại</label>
                                                                <input id="f_session_left" class="form-control" :value="data.temp.transferring.number_of_session_left" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Số buổi chuyển</label>
                                                                <input class="form-control" :value="data.temp.transferring.number_of_session_transferred" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control" :value="data.contract.start_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control" :value="data.temp.transferring.end_date" type="text" readonly>
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
                                                        <label class="filter-label control-label">Kỳ học <strong class="text-danger h6">*</strong></label><br/>
                                                        <select :disabled="html.receiving.form_fields.semester.disabled" v-model="data.temp.receiving.semester_id" @change="selectSemester(data.temp.receiving.semester_id)" class="selection product form-control">
                                                            <option value="-1" disabled> Chọn kỳ học </option>
                                                            <option :value="semester.id" v-for="(semester, idx) in data.semesters" :key="idx">{{ semester.name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Gói sản phẩm <strong class="text-danger h6">*</strong></label><br/>
                                                        <select :disabled="html.receiving.form_fields.product.disabled" v-model="data.temp.receiving.product_id" @change="selectProduct(data.temp.receiving.product_id)" class="selection product form-control">
                                                            <option value="-1" disabled> Chọn gói sản phẩm </option>
                                                            <option :value="product.id" v-for="(product, idx) in data.products" :key="idx">{{ product.name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Chương trình học <strong class="text-danger h6">*</strong></label><br/>
                                                        <select :disabled="html.receiving.form_fields.program.disabled" v-model="data.temp.receiving.program_id" @change="selectProgram(data.temp.receiving.program_id)" class="selection program form-control">
                                                            <option value="-1" disabled> Chọn chương trình học </option>
                                                            <option :value="program.id" v-for="(program, idx) in data.programs" :key="idx">{{ program.name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="filter-label control-label">Lớp <strong class="text-danger h6">*</strong></label><br/>
                                                        <select :disabled="html.receiving.form_fields.cls.disabled" v-model="data.temp.receiving.class_id" @change="selectClass(data.temp.receiving.class_id)" class="selection program form-control">
                                                            <option value="-1" disabled> Chọn lớp </option>
                                                            <option :value="cls.id" v-for="(cls, idx) in data.classes" :key="idx" v-if="cls.id != data.contract.class_id">{{ cls.class_name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày chuyển lớp <strong class="text-danger h6">*</strong></label>
                                                                <calendar
                                                                        class="form-control calendar"
                                                                        :value="data.temp.receiving.transfer_date"
                                                                        :transfer="true"
                                                                        :format="html.calendar.options.formatSelectDate"
                                                                        :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                                        :clear-button="html.calendar.options.clearSelectedDate"
                                                                        :placeholder="html.calendar.options.placeholderSelectDate"
                                                                        :pane="1"
                                                                        :disabled="html.calendar.disabled"
                                                                        :onDrawDate="onDrawDate"
                                                                        :lang="html.calendar.lang"
                                                                        :not-before="data.temp.receiving.min_date"
                                                                        :not-after="data.temp.receiving.max_date"
                                                                        @input="selectTransferDate"
                                                                ></calendar>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Lý do chuyển lớp</label>
                                                                <textarea class="form-control" v-model="data.temp.receiving.note" :readonly="html.receiving.form_fields.note.readonly" rows="5"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label">Số buổi quy đổi</label>
                                                                        <input class="form-control" v-model="data.temp.receiving.number_of_session_received" type="text" :readonly="html.receiving.form_fields.session_received.readonly" @keyup="editSessionsReceived">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày bắt đầu</label>
                                                                <input class="form-control" :value="data.temp.receiving.start_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày kết thúc</label>
                                                                <input class="form-control" :value="data.temp.receiving.end_date" type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group" v-show="html.receiving.form_fields.enable_editor.show">
                                                                <input v-model="data.temp.receiving.enable_editor" type="checkbox" :disabled="html.receiving.form_fields.enable_editor.disabled" @change="enableEditor()"> Sửa số tiền nhận, số buổi nhận
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
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="text-danger form-group" v-show="html.transferring.form_fields.shift_checked.show">
                                                <input type="checkbox" v-model="data.temp.transferring.shift_checked"> Đã kiểm tra ca học của học sinh với các trường hợp đặc biệt ca học (Block) 1, ca học (Block) 2, ca học (Block) 3, ca học (Block) 4 của các ngày Thứ bảy - Chủ nhật
                                            </div>
                                            <ApaxButton
                                                    :markup="html.buttons.save.style"
                                                    :disabled="html.buttons.save.disabled"
                                                    :onClick="addTransfer"
                                            >Lưu
                                            </ApaxButton>
                                            <ApaxButton
                                                    :onClick="exitAddContract"
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
        name: 'Add-Branch-Transfer',
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
                        disabled: true,
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
                    transferring: {
                        search_branch: {
                            exceptedBranch: 0,
                            display: 'show',
                            placeholder: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
                            id: 'search_suggest_branch'
                        },
                        search_contract: {
                            display: 'hide',
                            placeholder: ''
                        },
                        form_fields: {
                            shift_checked: {
                                show: false,
                            }
                        },
                    },
                    receiving: {
                        search_branch: {
                            display: 'show',
                            placeholder: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.'
                        },
                        form_fields: {
                            semester: {
                                disabled: true
                            },
                            product: {
                                disabled: true
                            },
                            program: {
                                disabled: true
                            },
                            cls: {
                                disabled: true
                            },
                            note: {
                                readonly: true,
                            },
                            attached_file: {
                                name: 'File đính kèm'
                            },
                            enable_editor: {
                                disabled: true,
                                show: false,
                            },
                            session_received: {
                                readonly: true,
                            },
                            amount_received: {
                                readonly: true,
                            }
                        }
                    },
                    buttons:{
                        save: {
                            style: 'success',
                            disabled:  true
                        }
                    },
                    modal: {
                        show: false,
                        message: ''
                    },
                    confirm: {
                        show: false,
                        message: ''
                    },
                },
                data: {
                    contract: {
                        id: 0,
                        accounting_id: '',
                        lms_id: '',
                        student_name: '',
                        student_id: 0,
                        nick: '',
                        product_name: '',
                        product_id: 0,
                        class_name: '',
                        class_id: 0,
                        start_date: '',
                        end_date: '',
                        real_sessions: 0,
                        bonus_sessions: 0,
                        summary_sessions: 0,
                        total_charged: 0,
                        semester_id: 0,
                        branch_id: 0,
                        done_sessions: 0,
                        real_done_sessions: 0,
                        bonus_done_sessions: 0,
                        class_days: [],
                        holidays: [],
                    },
                    semesters: {},
                    semester: {},
                    products: {},
                    product: {},
                    programs: {},
                    program: {},
                    classes: {},
                    cls: {},
                    temp: {
                        transferring: {
                            selected: false,
                            branch_id: 0,
                            end_date: '',
                            number_of_session_left: 0,
                            amount_left: 0,
                            number_of_session_transferred: 0,
                            amount_transferred: 0,
                            sessions_from_start_to_transfer_date: 0,
                            real_sessions_from_start_to_transfer_date: 0,
                            bonus_sessions_from_start_to_transfer_date: 0,
                            amount_from_start_to_transfer_date: 0,
                            old_enrol_end_date: '',
                            shift_checked: 0,
                        },
                        receiving: {
                            selected: false,
                            branch_id: 0,
                            min_date: u.convertDateToString(new Date()),
                            max_date: u.convertDateToString(new Date()),

                            semester_id: '-1',
                            product_id: '-1',
                            program_id: '-1',
                            class_id: '-1',
                            transfer_date: '',
                            number_of_session_received: 0,
                            number_of_real_sessions_received: 0,
                            number_of_bonus_sessions_received: 0,
                            amount_received: 0,
                            start_date: '',
                            end_date: '',
                            attached_file: '',
                            note: '',
                            class_days: [2,5],
                            new_enrol_start_date: '',
                            enable_editor: 0,
                        }
                    },
                    cache: {
                        holidays: {},
                        semesters: {},
                        classes: {}
                    }
                },
                flags: {
                    success: false,
                    form_loading: false,
                    requesting: false,
                },
            }
        },
        mounted() {
            this.flags.form_loading = true;
            this.setDefaultBranch();
            this.getHolidays(this.data.temp.transferring.branch_id);
        },
        created(){

        },
        methods: {
            prepareTransferringSearch(check) {

            },
            prepareReceivingSearch(check){

            },
            selectTransferringBranch(data) {
                this.flags.form_loading = true;
                this.data.temp.transferring.branch_id = parseInt(data.id);
                this.data.temp.receiving.branch_id = this.data.temp.transferring.branch_id;
                this.html.transferring.search_contract.display = 'show';
                this.getHolidays(this.data.temp.transferring.branch_id);
            },
            onDrawDate (e) {
                let date = e.date;
                date = u.convertDateToString(date);
                if (this.isGreaterThan(this.data.temp.receiving.min_date,date) || this.isGreaterThan(date, this.data.temp.receiving.max_date)) {
                    e.allowSelect = false;
                }
            },
            addTransfer(){
                let validate = this.validate();
                if(validate.valid){
                    this.flags.requesting = true;
                    u.a().get(`/api/class-transfers/extend/${this.data.temp.receiving.new_enrol_start_date}/${this.data.temp.receiving.end_date}/${this.data.temp.receiving.class_id}`)
                        .then(response => {
                            this.flags.requesting = false;
                            if(response.data.code == 200){
                                let cls = response.data.data;

                                if(cls.current_students >= cls.max_students){
                                    let message = u.genErrorHtml(`Lớp ${this.data.cls.class_name} đã full từ ngày ${this.data.temp.receiving.new_enrol_start_date} đến ngày ${this.data.temp.receiving.end_date}`);
                                    this.showMessage(message);
                                }else{
                                    if(this.flags.requesting === false){
                                        this.flags.requesting = true;
                                        let data = {
                                            student_id: this.data.contract.student_id,
                                            contract_id: this.data.contract.contract_id,
                                            note: this.data.temp.receiving.note,
                                            transfer_date: this.data.temp.receiving.transfer_date,
                                            amount_transferred: this.data.temp.transferring.amount_transferred,
                                            amount_exchange: this.data.temp.receiving.amount_received,
                                            session_transferred: this.data.temp.transferring.number_of_session_transferred,
                                            session_exchange: this.data.temp.receiving.number_of_session_received,
                                            from_branch_id: this.data.contract.branch_id,
                                            to_branch_id: this.data.contract.branch_id,
                                            from_product_id: this.data.contract.product_id,
                                            to_product_id: parseInt(this.data.temp.receiving.product_id),
                                            from_program_id: this.data.contract.program_id,
                                            to_program_id: parseInt(this.data.temp.receiving.program_id),
                                            from_class_id: this.data.contract.class_id,
                                            to_class_id: parseInt(this.data.temp.receiving.class_id),
                                            semester_id: parseInt(this.data.temp.receiving.semester_id),
                                            attached_file: this.data.temp.receiving.attached_file,
                                            meta_data: {
                                                total_session: this.data.contract.real_sessions,
                                                total_fee: this.data.contract.total_charged,
                                                from_start_date: this.data.contract.start_date,
                                                from_end_date: this.data.temp.transferring.end_date,
                                                to_start_date: this.data.temp.receiving.start_date,
                                                to_end_date: this.data.temp.receiving.end_date,
                                                sessions_from_start_to_transfer_date: this.data.temp.transferring.sessions_from_start_to_transfer_date,
                                                real_sessions_from_start_to_transfer_date: this.data.temp.transferring.real_sessions_from_start_to_transfer_date,
                                                bonus_sessions_from_start_to_transfer_date: this.data.temp.transferring.bonus_sessions_from_start_to_transfer_date,
                                                real_sessions_transferred: this.data.temp.transferring.number_of_real_session_transferred,
                                                bonus_sessions_transferred: this.data.temp.transferring.number_of_bonus_session_transferred,
                                                real_sessions_received: this.data.temp.receiving.number_of_real_sessions_received,
                                                bonus_sessions_received: this.data.temp.receiving.number_of_bonus_sessions_received,
                                                amount_from_start_to_transfer_date: this.data.temp.transferring.amount_from_start_to_transfer_date,
                                                old_enrol_end_date: this.data.temp.transferring.old_enrol_end_date,
                                                new_enrol_start_date: this.data.temp.receiving.new_enrol_start_date,
                                                shift_checked: (this.data.temp.transferring.shift_checked === -1)?(-1):((this.data.temp.transferring.shift_checked === true)?1:0),
                                                enable_editor: this.data.temp.receiving.enable_editor?1:0,
                                            }
                                        };
                                        u.a().post('/api/class-transfers', data)
                                            .then(response => {
                                                this.flags.requesting = false;
                                                if(response.data.code == 200){
                                                    this.is_success = true;
                                                    let message = "<span class='text-success'>Đăng ký thành công</span>";
                                                    this.showMessage(message);
                                                }else{
                                                    let message = "<span class='text-danger'>"+response.data.message+"</span>";
                                                    this.showMessage(message);
                                                }

                                            }).catch(e => {
                                            this.flags.requesting = false;
                                            let message = "<span class='text-danger'>Có lỗi xảy ra. Vui lòng thử lại sau!</span>";
                                            this.showMessage(message);


                                        });
                                    }
                                }
                            }else{
                                let message = u.genErrorHtml(response.data.message);
                                this.showMessage(message);
                            }
                        });

                }else{
                    this.showMessage(validate.message);
                }
            },
            resetForm(){

            },
            exitAddContract(){
                this.$router.push('/class-transfers');
            },
            selectTransferDate(selected_date) {
                let date = u.utcToLocal(selected_date)
                if (date !== "" && date !=="Invalid date") {
                    if (this.flags.requesting === false) {
                    if (this.data.temp.receiving.class_days.length) {
                        this.flags.requesting = true
                        this.data.temp.receiving.transfer_date = date

                        if (
                        this.data.contract.has_enrolment &&
                        this.isGreaterThan(date, this.data.contract.start_date)
                        ) {
                        if(this.data.contract.trial_sessions>0 && this.data.contract.debt_amount==0){
                            this.data.temp.transferring.end_date = date

                            var done_sessions = u.calSessions(
                            this.data.contract.start_date,
                            u.pre(date),
                            this.data.contract.holidays,
                            this.data.contract.class_days
                            ).total
                            this.data.temp.transferring.done_trial_sessions = this.data.contract.trial_sessions < done_sessions ? this.data.contract.trial_sessions:done_sessions;
                            done_sessions = done_sessions < this.data.contract.trial_sessions ? 0 :done_sessions - this.data.contract.trial_sessions
                    
                            this.data.temp.transferring.sessions_from_start_to_transfer_date = done_sessions
                            this.data.temp.transferring.real_sessions_from_start_to_transfer_date =
                            done_sessions > this.data.contract.real_sessions
                                ? this.data.contract.real_sessions
                                : done_sessions
                            this.data.temp.transferring.bonus_sessions_from_start_to_transfer_date =
                            this.data.temp.transferring
                                .sessions_from_start_to_transfer_date -
                            this.data.temp.transferring
                                .real_sessions_from_start_to_transfer_date +  this.data.temp.transferring.done_trial_sessions
                            this.data.temp.transferring.amount_from_start_to_transfer_date = this
                            .data.contract.real_sessions
                            ? Math.ceil(
                                (this.data.temp.transferring
                                    .real_sessions_from_start_to_transfer_date *
                                    this.data.contract.total_charged) /
                                    this.data.contract.real_sessions
                                )
                            : 0

                            this.data.temp.transferring.number_of_session_transferred =
                            this.data.contract.summary_sessions - done_sessions - this.data.temp.transferring.done_trial_sessions
                            this.data.temp.transferring.number_of_real_session_transferred =
                            this.data.contract.real_sessions -
                            this.data.temp.transferring
                                .real_sessions_from_start_to_transfer_date
                            this.data.temp.transferring.number_of_bonus_session_transferred =
                            this.data.contract.bonus_sessions -
                            this.data.temp.transferring
                                .bonus_sessions_from_start_to_transfer_date

                            this.data.temp.transferring.number_of_session_left =
                            done_sessions - this.data.contract.done_sessions
                            this.data.temp.transferring.number_of_real_session_left =
                            this.data.temp.transferring
                                .real_sessions_from_start_to_transfer_date -
                            this.data.contract.real_done_sessions
                            this.data.temp.transferring.number_of_bonus_session_left =
                            this.data.temp.transferring
                                .bonus_sessions_from_start_to_transfer_date -
                            this.data.contract.bonus_done_sessions
                        }else{
                            this.data.temp.transferring.end_date = date

                            let done_sessions = u.calSessions(
                            this.data.contract.start_date,
                            u.pre(date),
                            this.data.contract.holidays,
                            this.data.contract.class_days
                            ).total

                            this.data.temp.transferring.sessions_from_start_to_transfer_date = done_sessions
                            this.data.temp.transferring.real_sessions_from_start_to_transfer_date =
                            done_sessions > this.data.contract.real_sessions
                                ? this.data.contract.real_sessions
                                : done_sessions
                            this.data.temp.transferring.bonus_sessions_from_start_to_transfer_date =
                            this.data.temp.transferring
                                .sessions_from_start_to_transfer_date -
                            this.data.temp.transferring
                                .real_sessions_from_start_to_transfer_date
                            this.data.temp.transferring.amount_from_start_to_transfer_date = this
                            .data.contract.real_sessions
                            ? Math.ceil(
                                (this.data.temp.transferring
                                    .real_sessions_from_start_to_transfer_date *
                                    this.data.contract.total_charged) /
                                    this.data.contract.real_sessions
                                )
                            : 0

                            this.data.temp.transferring.number_of_session_transferred =
                            this.data.contract.summary_sessions - done_sessions
                            this.data.temp.transferring.number_of_real_session_transferred =
                            this.data.contract.real_sessions -
                            this.data.temp.transferring
                                .real_sessions_from_start_to_transfer_date
                            this.data.temp.transferring.number_of_bonus_session_transferred =
                            this.data.contract.bonus_sessions -
                            this.data.temp.transferring
                                .bonus_sessions_from_start_to_transfer_date

                            this.data.temp.transferring.number_of_session_left =
                            done_sessions - this.data.contract.done_sessions
                            this.data.temp.transferring.number_of_real_session_left =
                            this.data.temp.transferring
                                .real_sessions_from_start_to_transfer_date -
                            this.data.contract.real_done_sessions
                            this.data.temp.transferring.number_of_bonus_session_left =
                            this.data.temp.transferring
                                .bonus_sessions_from_start_to_transfer_date -
                            this.data.contract.bonus_done_sessions
                        }

                        if (done_sessions == 0) {
                            this.data.temp.transferring.old_enrol_end_date = this.data.contract.start_date
                        } else {
                            this.data.temp.transferring.old_enrol_end_date = u.calEndDate(
                            done_sessions,
                            this.data.contract.class_days,
                            this.data.contract.holidays,
                            this.data.contract.start_date
                            ).end_date
                        }
                        } else {
                        this.data.temp.transferring.end_date = date

                        this.data.temp.transferring.number_of_session_transferred = this.data.contract.summary_sessions
                        this.data.temp.transferring.number_of_real_session_transferred = this.data.contract.real_sessions
                        this.data.temp.transferring.number_of_bonus_session_transferred = this.data.contract.bonus_sessions

                        this.data.temp.transferring.number_of_session_left = 0
                        this.data.temp.transferring.number_of_real_session_left = 0
                        this.data.temp.transferring.number_of_bonus_session_left = 0

                        this.data.temp.transferring.sessions_from_start_to_transfer_date = 0
                        this.data.temp.transferring.amount_from_start_to_transfer_date = 0
                        this.data.temp.transferring.old_enrol_end_date = this.data.contract.start_date
                        }

                        this.data.temp.transferring.amount_left = this.data.contract
                        .real_sessions
                        ? Math.floor(
                            (this.data.temp.transferring.number_of_real_session_left *
                                this.data.contract.total_charged) /
                                this.data.contract.real_sessions
                            )
                        : 0
                        this.data.temp.transferring.amount_transferred = this.data.contract
                        .real_sessions
                        ? Math.ceil(
                            (this.data.temp.transferring
                                .number_of_real_session_transferred *
                                this.data.contract.total_charged) /
                                this.data.contract.real_sessions
                            )
                        : 0

                        this.data.temp.receiving.amount_received = this.data.temp.transferring.amount_transferred
                        this.data.temp.receiving.start_date = date

                        this.data.temp.receiving.new_enrol_start_date = u.calEndDate(
                        1,
                        this.data.temp.receiving.class_days,
                        this.data.contract.holidays,
                        date
                        ).end_date

                        const transferred_amount = Math.ceil((this.data.temp.transferring.number_of_real_session_transferred *this.data.contract.tuition_fee_receivable) /this.data.contract.tuition_fee_session)

                        if (
                        !this.data.contract.student_type &&
                        this.data.temp.transferring.number_of_real_session_transferred
                        ) {
                        u.a().get(
                            `/api/settings/exchange-v2/${
                                this.data.contract.tuition_fee_id
                            }/${transferred_amount}/${this.data.contract.branch_id}/${
                                this.data.semester.product_id
                            }/${
                                this.data.temp.transferring
                                .number_of_real_session_transferred
                            }`
                            )
                            .then(response => {
                            this.flags.requesting = false
                            if (response.data.code == 200) {
                                let info = response.data.data
                                this.data.temp.receiving.number_of_real_sessions_received = parseInt(info.sessions)
                                this.data.temp.receiving.tuition_fee_id = info.receive_tuition_fee.id
                                this.data.temp.receiving.number_of_bonus_sessions_received = Number(this.data.temp.transferring.number_of_bonus_session_transferred)
                                this.data.temp.receiving.number_of_session_received = this.data.temp.receiving.number_of_real_sessions_received + this.data.temp.receiving.number_of_bonus_sessions_received
                                this.data.temp.receiving.end_date = u.calEndDate(this.data.temp.receiving.number_of_session_received,this.data.temp.receiving.class_days,this.data.contract.holidays,date).end_date
                            } else {
                                let message =
                                "<span class='text-danger'>" +
                                response.data.message +
                                "</span>"
                                this.showMessage(message)
                            }
                            })
                        } else {
                        this.data.temp.receiving.number_of_session_received = this.data.temp.transferring.number_of_session_transferred
                        this.data.temp.receiving.number_of_real_sessions_received = this.data.temp.transferring.number_of_real_session_transferred
                        this.data.temp.receiving.number_of_bonus_sessions_received = this.data.temp.transferring.number_of_bonus_session_transferred

                        this.data.temp.receiving.end_date = u.calEndDate(
                            this.data.temp.receiving.number_of_session_received,
                            this.data.temp.receiving.class_days,
                            this.data.contract.holidays,
                            date
                        ).end_date
                        this.flags.requesting = false
                        }

                        this.html.buttons.save.disabled = false
                    } else {
                        let message = u.genErrorHtml(
                        "Vui lòng cập nhật thời khóa biểu cho lớp"
                        )
                        this.showMessage(message)
                    }
                    }
                }
            },
            selectContract(contract){
                if(contract.waiting_status==0){
                    this.reset();
                    this.resetLearningInfo();
                    this.data.contract = contract;
                    this.data.contract.class_days = contract.class_days;

                    this.data.temp.transferring.shift_checked = -1;
                    this.html.transferring.form_fields.shift_checked.show = false;

                    this.data.temp.receiving.enable_editor = 0;

                    this.data.contract.holidays = this.data.cache.holidays[parseInt(contract.branch_id)][parseInt(contract.product_id)].concat(contract.reserved_dates);

                    let now = u.convertDateToString(new Date());

                    if(moment(now) <= moment(contract.start_date)){
                        this.data.contract.done_sessions = 0;
                        this.data.temp.receiving.min_date = contract.start_date;
                        this.data.temp.transferring.number_of_session_left = contract.real_sessions;
                        this.data.temp.transferring.amount_left = 0;

                    }else{
                        let done_sessions = u.calSessions(contract.start_date, now, this.data.contract.holidays, this.data.contract.class_days).total;
                        this.data.contract.done_sessions = done_sessions;
                        this.data.temp.transferring.number_of_session_left = this.data.contract.real_sessions - done_sessions;
                        this.data.temp.transferring.amount_left = 0;
                    }

                    const currentDate = u.convertDateToString(new Date())
                    const lastDate = this.data.contract.end_date;

                    this.data.temp.receiving.max_date = currentDate === lastDate ? currentDate :
                        moment(lastDate).subtract(1, 'd').format('YYYY-MM-DD')
                    this.setMinDate();

                    this.data.temp.transferring.end_date = contract.end_date;

                    this.data.temp.transferring.selected = true;

                    this.getAllInfo(this.data.temp.transferring.branch_id);
                }else{
                    var message="";
                    switch(enrolment.waiting_status) {
                        case 1:
                        message="Học sinh đang chờ duyệt chuyển phí";
                        break;
                        case 2:
                        message="Học sinh đang chờ duyệt nhận phí";
                        break;
                        case 3:
                        message="Học sinh đang chờ duyệt chuyển trung tâm";
                        break;
                        case 4:
                        message="Học sinh đang chờ duyệt bảo lưu";
                        break;
                        case 5:
                        message="Học sinh đang chờ duyệt chuyển lớp";
                        break;
                        default:
                        // code block
                    }
                    this.showMessage(message);
                }
            },
            enableSelectTransferDate(){
                if(this.data.temp.transferring.selected && this.data.temp.receiving.selected){
                    this.html.calendar.disabled = false;
                    this.html.receiving.form_fields.note.readonly = false;
                    this.html.receiving.form_fields.enable_editor.disabled = false;
                }
            },
            isGreaterThan(_from, _to){
                let _from_time = new Date(_from); // Y-m-d
                let _to_time = new Date(_to); // Y-m-d
                return (_from_time.getTime() > _to_time.getTime())?true:false;
            },
            validToken(){
                if(!this.token){
                    u.go(this.$router, '/login');
                }
            },
            showMessage(message){
                this.html.modal.message = message;
                this.html.modal.show = true;
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
                            this.data.temp.transferring.branch_id = parseInt(user_branch_id);
                            this.data.temp.receiving.branch_id = this.data.temp.transferring.branch_id;
                            $("#search_suggest_branch").val(branches[i].name).prop('readonly',true);
                            this.html.transferring.search_contract.display = 'show';
                            break;
                        }
                    }
                }
            },
            selectSemester(item) {
                this.data.semester = this.data.semesters[`semester${item}`];
                this.data.products = this.data.semester['products'];
                this.data.temp.receiving.product_id = '-1';
                this.html.receiving.form_fields.product.disabled = false;
            },
            selectProduct(item) {
                this.data.product = this.data.products[`product${item}`];
                this.data.programs = this.data.product['programs'];
                this.data.temp.receiving.program_id = '-1';
                this.html.receiving.form_fields.program.disabled = false;
            },
            selectProgram(item) {
                this.data.program = this.data.programs[`program${item}`];
                this.html.receiving.form_fields.cls.disabled = false;
                this.data.classes = this.data.cache.classes[this.data.temp.receiving.branch_id][item];
                this.data.temp.receiving.class_id = '-1';
            },
            selectClass(item){
                this.data.cls = this.data.classes[item];
                this.data.temp.receiving.class_days = this.data.cls.class_days;
                this.data.temp.receiving.selected = true;
                this.enableSelectTransferDate();
            },
            getAllInfo(branch_id){
                if(this.data.cache.semesters[branch_id]){
                    this.data.semesters =this.data.cache.semesters[branch_id];
                    this.html.receiving.form_fields.semester.disabled = false;

                    this.data.semester = this.data.semesters[`semester${this.data.contract.semester_id}`];
                    this.data.temp.receiving.semester_id = '' + this.data.contract.semester_id;


                    this.data.products = this.data.semester['products'];
                    this.data.product = this.data.products[`product${this.data.contract.product_id}`];
                    this.data.temp.receiving.product_id = '' + this.data.contract.product_id;
                    this.html.receiving.form_fields.product.disabled = false;

                    this.data.programs = this.data.product['programs'];
                    this.data.program = this.data.programs[`program${this.data.contract.program_id}`];
                    this.data.temp.receiving.program_id = this.data.contract.program_id;
                    this.html.receiving.form_fields.program.disabled = false;

                    this.html.receiving.form_fields.cls.disabled = false;
                    this.data.classes = this.data.cache.classes[branch_id][this.data.contract.program_id];;
                    this.data.temp.receiving.class_id = '-1';

                    this.enableSelectTransferDate();
                }else{
                    u.a().get('/api/class-transfers/' + branch_id + '/info')
                        .then(response => {
                            if(response.data.code == 200){
                                this.data.semesters = response.data.data.semesters;

                                this.data.cache.semesters[branch_id] = response.data.data.semesters;
                                this.data.cache.classes[branch_id] = response.data.data.classes;

                                this.data.semester = this.data.semesters[`semester${this.data.contract.semester_id}`];
                                this.data.temp.receiving.semester_id = '' + this.data.contract.semester_id;
                                this.html.receiving.form_fields.semester.disabled = false;

                                this.data.products = this.data.semester['products'];
                                this.data.product = this.data.products[`product${this.data.contract.product_id}`];
                                this.data.temp.receiving.product_id = '' + this.data.contract.product_id;
                                this.html.receiving.form_fields.product.disabled = false;

                                this.data.programs = this.data.product['programs'];
                                this.data.program = this.data.programs[`program${this.data.contract.program_id}`];
                                this.data.temp.receiving.program_id = this.data.contract.program_id;
                                this.html.receiving.form_fields.program.disabled = false;

                                this.data.classes = this.data.cache.classes[branch_id][this.data.contract.program_id];
                                this.html.receiving.form_fields.cls.disabled = false;
                                this.data.temp.receiving.class_id = '-1';

                                this.enableSelectTransferDate();
                            }
                        }).catch(e => {

                    });
                }
            },
            getHolidays(branch_id){
                if(!u.live(this.data.cache.holidays[branch_id])){
                    this.flags.form_loading = true;
                    let url = '/api/info/'+branch_id+'/holidays';
                    u.a().get(url)
                        .then((response) => {
                            this.flags.form_loading = false;
                            if(response.data.code == 200){
                                this.data.cache.holidays[branch_id] = response.data.data;
                            }else{
                                this.data.cache.holidays[branch_id] = []
                            }
                        });
                }
            },
            uploadFile(file, param = null) {
                if (param) {
                    this.data.temp.receiving.attached_file = file
                }
            },
            reset(){
                this.data.temp.transferring.number_of_session_transferred = 0;
                this.data.temp.transferring.amount_transferred = 0;
                this.data.temp.receiving.transfer_date = '';
                this.data.temp.receiving.number_of_session_received = 0;
                this.data.temp.receiving.amount_received = 0;
                this.data.temp.receiving.start_date = '';
                this.data.temp.receiving.end_date = '';
            },
            resetLearningInfo(){
                this.data.temp.receiving.semester_id = '-1';
                this.data.products = {};
                this.data.product = {};
                this.data.temp.receiving.product_id = '-1';
                this.data.programs = {};
                this.data.program = {};
                this.data.temp.receiving.program_id = '-1';
                this.data.classes = {};
                this.data.cls = {};
                this.data.temp.receiving.class_id ='-1';
            },
            is2017Contract(){
                return moment(this.data.contract.start_date, 'YYYY-MM-DD') <= moment("2017-12-31", 'YYYY-MM-DD');
            },
            isValidDate(date){
                let aDate   = moment(date, 'YYYY-MM-DD', true);
                return aDate.isValid();
            },
            enableEditor(){
                if(this.data.temp.receiving.enable_editor){
                    this.html.receiving.form_fields.session_received.readonly = false;
                    this.html.receiving.form_fields.amount_received.readonly = false;
                }else{
                    this.html.receiving.form_fields.session_received.readonly = true;
                    this.html.receiving.form_fields.amount_received.readonly = true;
                    if(this.isValidDate(this.data.temp.receiving.transfer_date)){
                        this.selectTransferDate(this.data.temp.receiving.transfer_date);
                    }
                }
            },
            editSessionsReceived(){
                let sessions = parseInt(this.data.temp.receiving.number_of_session_received);

                this.data.temp.receiving.end_date = u.calEndDate(sessions, this.data.temp.receiving.class_days, this.data.contract.holidays, this.data.temp.receiving.transfer_date).end_date;
            },
            editAmountReceived(){

            },
            validate(){
                let resp = {
                    valid: true,
                    message: ""
                };
                if(!this.data.contract.student_id){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Thông tin học sinh không hợp lệ');
                }

                if(!parseInt(this.data.temp.receiving.branch_id)){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Thông tin trung tâm chuyển đến không hợp lệ');
                }

                if(parseInt(this.data.temp.receiving.semester_id) <= 0){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Thông tin kỳ học không hợp lệ');
                }

                if(parseInt(this.data.temp.receiving.product_id) <= 0){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Thông tin sản phẩm không hợp lệ');
                }

                if(parseInt(this.data.temp.receiving.program_id) <= 0){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Thông tin chương trình không hợp lệ');
                }

                if(parseInt(this.data.temp.receiving.class_id) <= 0){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Thông tin lớp học không hợp lệ');
                }

                if(!this.data.temp.receiving.transfer_date || !this.isValidDate(this.data.temp.receiving.transfer_date)){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Ngày bắt đầu chuyển trung tâm không hợp lệ');
                }

                if(!parseInt(this.data.temp.receiving.number_of_session_received)){
                    resp.valid = false;
                    resp.message += u.genErrorHtml('Số buổi quy đổi không hợp lệ');
                }

                return resp;
            },
            setMinDate(){
              this.data.temp.receiving.min_date = u.convertDateToString(new Date())
            }
        }
    }
</script>

<style scoped lang="scss">
    .hide {
        display: none;
    }
    .show{}
    .display {
        display: block;
    }
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
