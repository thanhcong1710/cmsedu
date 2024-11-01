<template>
    <div class="animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <div :class="html.loading.class ? 'loading' : 'standby'" class="ajax-loader">
                    <img :src="html.loading.source">
                </div>
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-id-card"></i> <b class="uppercase">Rút phí</b>
                        <div class="card-actions">
                            <a href="skype:thanhcong1710?chat" target="_blank">
                                <small className="text-muted"><i class="fa fa-skype"></i></small>
                            </a>
                        </div>
                    </div>
                    <div v-show="html.loading.action" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="html.loading.action" class="loading-text cssload-loader">{{
                                html.loading.content }}
                            </div>
                        </div>
                    </div>
                    <div id="page-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6" :class="html.dom.filter.branch.display">
                                                <div class="form-group">
                                                    <label class="control-label">Chọn Trung Tâm</label>
                                                    <branch
                                                            :onSelect="html.dom.filter.branch.action"
                                                            :options="html.dom.filter.branch.options"
                                                            :disabled="html.dom.filter.branch.disabled"
                                                            :placeholder="html.dom.filter.branch.placeholder">
                                                    </branch>
                                                    <br/>
                                                </div>
                                            </div>
                                            <div class="col-md-6" :class="html.dom.filter.search.display">
                                                <div class="form-group" >
                                                    <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc
                                                        Tên</label>
                                                    <search
                                                            :endpoint="html.dom.filter.search.link"
                                                            :suggestStudents="html.dom.filter.search.find"
                                                            :onSelectStudent="html.dom.filter.search.action">
                                                    </search>
                                                    <br/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="list_content" class="panel-heading">
                                        <div class="panel-body">
                                            <div class="table-responsive scrollable">
                                                <table class="table table-striped table-bordered apax-table withdrawal">
                                                    <thead>
                                                        <tr class="text-sm">
                                                            <th >STT</th>
                                                            <th>Tên học sinh</th>
                                                            <th>Mã CMS</th>
                                                            <th>Mã Cyber</th>
                                                            <th>Trung tâm</th>
                                                            <th>Loại HĐ</th>
                                                            <th>EC</th>
                                                            <th>CM</th>
                                                            <th>Sản phẩm</th>
                                                            <th>Gói phí</th>
                                                            <th>Số buổi</th>
                                                            <th>Giá niêm yết</th>
                                                            <th>Công nợ</th>
                                                            <th>Phải đóng</th>
                                                            <th>Ngày học</th>
                                                            <th>Trạng thái</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in data.list_contract" :key="index">
                                                            <td>
                                                                {{index+1}}
                                                            </td>
                                                            <td>
                                                                {{item.student_name}}
                                                            </td>
                                                            <td>
                                                                {{item.cms_id}}
                                                            </td>
                                                            <td>
                                                                {{item.accounting_id}}
                                                            </td>
                                                            <td>
                                                                {{item.branch_name}}
                                                            </td>
                                                            <td>
                                                                {{item.contract_type | contractType}}
                                                            </td>
                                                            <td>
                                                                {{item.ec_name}}
                                                            </td>
                                                            <td>
                                                                {{item.cm_name}}
                                                            </td>
                                                            <td>
                                                                {{item.product_name}}
                                                            </td>
                                                            <td>
                                                                {{item.tuition_fee_name}}
                                                            </td>
                                                            <td>
                                                                {{item.total_sessions}}
                                                            </td>
                                                            <td>
                                                                {{item.tuition_fee_price | formatMoney}}
                                                            </td>
                                                            <td>
                                                                {{item.debt_amount | formatMoney}}
                                                            </td>
                                                            <td>
                                                                {{item.must_charge | formatMoney}}
                                                            </td>
                                                            <td>
                                                                {{(item.enrolment_start_date || item.start_date) | formatDate}}
                                                            </td>
                                                             <td>
                                                                {{item.status | formatStatusContract}}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="form-input" :class="html.dom.display.form_input">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="panel">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-12 pad-no">
                                                                <div class="col-md-12">
                                                                    <address>
                                                                        <h6 class="text-main">Thông tin rút phí </h6>
                                                                    </address>
                                                                </div>
                                                                <div class="col-md-12 pad-no">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="control-label">Mã CMS</label>
                                                                                <input class="form-control" :value="withdrawal.contract_info.cms_id"
                                                                                    type="text" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="control-label">Họ Tên</label>
                                                                                <input class="form-control" :value="withdrawal.contract_info.student_name"
                                                                                    type="text" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="control-label">Ngày Sinh</label>
                                                                                <input class="form-control" :value="withdrawal.contract_info.date_of_birth"
                                                                                    type="text" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div v-for="(contract, idc) in data.list_contract" :key="idc">
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
                                                                                    <input class="form-control"
                                                                                           :value="data.list_contract[idc].number_done_sessions"
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
                                                                                        :value="contract.enrolment_start_date || contract.start_date" type="text"
                                                                                        readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">Ngày kết thúc</label>
                                                                                    <input class="form-control"
                                                                                        :value="contract.enrolment_last_date||contract.end_date" type="text"
                                                                                        readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">Số tiền còn lại</label>
                                                                                    <input class="form-control" :value="data.list_contract[idc].real_amount | formatMoney" type="text" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">Số tiền bị khấu trừ</label>
                                                                                    <input :id="`amount_fee_` + idc" class="form-control" :value="data.list_contract[idc].fee_amount" type="number" min="0" @change="changeAmountFee(idc)" readonly>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <input :id="`enable_fee_` + idc" v-model="data.list_contract[idc].enable_editor_fee" type="checkbox" @change="enableEditorFee(idc)"> Sửa số tiền bị khấu trừ
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">Số tiền thực nhận</label>
                                                                                    <input :id="`amount_` + idc" class="form-control" :value="data.list_contract[idc].refun_amount" type="text"  readonly>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
	                                                                <div class="row">
	                                                                    <div class="col-md-4">
	                                                                        <div class="form-group">
	                                                                            <label class="control-label">Ngày rút phí <strong class="text-danger h6">*</strong></label>
	                                                                            <calendar
	                                                                                    class="form-control calendar"
	                                                                                    :value="withdrawal.withdraw_date"
	                                                                                    :transfer="true"
	                                                                                    :format="html.dom.calendar.options.formatSelectDate"
	                                                                                    :disabled-days-of-week="html.dom.calendar.options.disabledDaysOfWeek"
	                                                                                    :clear-button="html.dom.calendar.options.clearSelectedDate"
	                                                                                    :placeholder="html.dom.calendar.options.placeholderSelectDate"
	                                                                                    :pane="1"
	                                                                                    :disabled="html.dom.calendar.disabled"
	                                                                                    :onDrawDate="onDrawDate"
                                                                                        :lang="html.dom.calendar.lang"
                                                                                        :not-before="html.dom.calendar.minDate"
                                                                                        :not-after="html.dom.calendar.maxDate"
	                                                                                    @input="selectWithdrawDate"
	                                                                            ></calendar>
	                                                                        </div>
	                                                                    </div>
				                                                        <div class="col-md-4">
				                                                            <div class="form-group">
				                                                                <label class="control-label">File đính kèm</label>
				                                                                <file
				                                                                        :label="'Click để chọn file'"
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
                                                                                    <input class="form-control" v-model="withdrawal.comment_created"
                                                                                        type="text">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-footer">
                                                        <div class="col-sm-12 col-sm-offset-3 text-right">
                                                            <button class="apax-btn full edit" type="submit" @click="addWithdraw"><i class="fa fa-save"></i> Lưu</button>
                                                            <router-link class="apax-btn full remove" :to="`/withdrawals`">
                                                                <i class="fa fa-sign-out"></i> Hủy
                                                            </router-link>
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
                <b-modal
                    :title="html.dom.modal.title"
                    :class="html.dom.modal.class" size="sm"
                    v-model="html.dom.modal.display"
                    @ok="exitModal"
                    ok-variant="primary"
                >
                <div v-html="html.dom.modal.message"></div>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import calendar from 'vue2-datepicker'
    import u from '../../../utilities/utility'
    import branch from '../../../components/Selection'
    import search from '../../../components/StudentSearch'
    import apaxbtn from '../../../components/Button'
    import file from '../../../components/File'

    export default {
        name: 'Add-Withdrawal',
        components: {
            calendar,
            branch,
            search,
            apaxbtn,
            file,
        },
        data() {
            const model = u.m('withdrawal').page
            model.html.dom = {
                calendar: {
                    disabled: false,
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
                    },
                    minDate: this.getDate(new Date()),
                },
                filter: {
                    branch: {
                        display: 'hidden',
                        options: [],
                        disabled: true,
                        placeholder: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước',
                        action: branch => this.selectBranch(branch)
                    },
                    search: {
                        link: 0,
                        display: 'hidden',
                        find: keyword => this.searchSuggestStudent(keyword),
                        action: student => this.selectStudent(student)
                    }
                },
                display: {
                    form_input: 'hidden',
                },
                disabled: {
                },
                list: {
                    product: [],
                    program: [],
                    tuition_fee: []
                },
                modal: {
                    title: 'Thông Báo',
                    class: 'modal-success',
                    message: '',
                    display:  false
                },
            }
            model.data = {
                branch_id: 0,
                student_id: 0,
                list_contract: [],
                holidays: {},
                student:{},
                nearest_school_day: null // Ngày học gần nhất tính trên tất cả các hợp đồng của học sinh
            }
            model.calling = false
            model.withdrawal = {
                contract_info : {},
                withdraw_date: '',
                enable_editor:0,
                contract_id: 0,
                student_id: 0,
                branch_id: 0,
                creator_id: 0,
                approver_id: 0,
                ec_id: 0,
                refuner_id: 0,
                refun_amount: 0,
                real_amount: 0,
                fee_amount: 0,
                real_sessions: 0,
                done_sessions: 0,
                left_sessions: 0,
                holidays:[],
                check_fee_amount:0,
                comment_created:'',
                is_fee_null: 0,
                attached_file: '',
            }
            return model
        },
        created() {
            this.start()
        },
        methods: {
            start() {
                if (u.authorized()) {
                    this.html.dom.filter.branch.display = 'display'
                    this.html.dom.filter.branch.disabled = false
                    this.html.loading.action = false
                } else {
                    this.data.branch_id = parseInt(this.session.user.branch_id)
                    this.ready()
                }
                this.html.loading.action = false
                this.html.dom.disabled.cancel = false
                this.getHolidays(this.data.branch_id);
            },
            getDoneSession(contract){
                const holidays = u.live(this.data.holidays[contract.branch_id][contract.product_id])?this.data.holidays[contract.branch_id][contract.product_id].concat(contract.reserved_dates):contract.reserved_dates;
                const currentDay = u.convertDateToString(new Date())
                const dateEnd = currentDay === this.data.nearest_school_day ?  moment(currentDay).subtract(1,'d').format('YYYY-MM-DD') : currentDay;
                return u.calSessions(contract.enrolment_start_date, dateEnd, holidays, contract.class_days).total;
            },
            okModal() {
                if (this.completed) {
                    this.exitForm()
                } else this.html.config.modal = false
            },
            ready() {
                this.html.dom.filter.search.display = 'display'
                this.html.dom.filter.search.disabled = false
                this.html.dom.filter.search.link = this.data.branch_id
            },
            searchSuggestStudent(keyword) {
                if (keyword && keyword.length >= 3 && this.calling === false) {
                    this.calling = true
                    return new Promise((resolve, reject) => {
                        u.g(`/api/withdrawals/search/students/${this.data.branch_id}/${keyword}`)
                            .then((response) => {
                                const resp = response.length ? response : [{
                                    label: 'Không tìm thấy',
                                    branch_name: 'Không có kết quả nào phù hợp'
                                }]
                                this.calling = false
                                resolve(resp)
                            }).catch(e => console.log(e))
                    })
                }
            },
            selectBranch(branch) {
                this.data.branch_id = branch.id
                this.getHolidays(this.data.branch_id);
                this.ready()
            },
            selectStudent(student) {
                this.data.student = student
                this.data.student_id = student.student_id
                u.a().get(`/api/withdrawals/all-contract/${this.data.student_id}`).then(response =>{
                    if(response.data.data.has_error ==0){
                        this.data.list_contract = response.data.data.contracts;
                        this.withdrawal.contract_info = this.data.list_contract[0];
                        this.getNearestSchoolDayOfListContracts(this.data.list_contract && this.data.list_contract.map((c) => c.id))
                    } else {
                        this.html.dom.modal.message = response.data.data.message;
                        this.html.dom.modal.display = true
                    }
                })
            },
            getNearestSchoolDayOfListContracts(contractIds){
                if (!contractIds) return

                u.g(`/api/classes/get-nearest-school-day-of-contracts/${contractIds.toString()}`).then((response) => {
                    const nearestSchoolDay = response && response.nearest_school_day
                    const currentDay = u.convertDateToString(new Date())
                    const preNearestSchoolDay = moment(nearestSchoolDay).subtract(1,'d').format('YYYY-MM-DD');
                    this.html.dom.calendar.maxDate = nearestSchoolDay === currentDay ? nearestSchoolDay : preNearestSchoolDay
                    this.html.dom.calendar.maxDate = nearestSchoolDay === currentDay ? nearestSchoolDay : preNearestSchoolDay
                    this.data.nearest_school_day = nearestSchoolDay
                    if(Array.isArray(this.data.list_contract)){
                        this.data.list_contract.forEach((contract) => {contract.number_done_sessions = this.getDoneSession(contract)})
                    }
                    this.html.dom.display.form_input = 'display'
                })
            },
            selectWithdrawDate(date){
                this.withdrawal.is_fee_null =0
                this.withdrawal.check_fee_amount = 0;
                this.withdrawal.real_amount = 0;
                this.withdrawal.fee_amount = 0;
                this.withdrawal.refun_amount = 0;
                this.withdrawal.done_sessions = 0;
                this.withdrawal.left_sessions = 0;

                for (var k in this.data.list_contract){
                    this.processWithdraw(this.data.list_contract[k],k,date);
                }
                if(this.withdrawal.check_fee_amount==0){
                    for (var k in this.data.list_contract){
                        this.data.list_contract[k].fee_amount = Math.ceil(this.data.list_contract[k].real_amount*30/100)
                        this.data.list_contract[k].fee_amount +=  k==0 ? 500000 : 0
                        this.data.list_contract[k].refun_amount = this.data.list_contract[k].real_amount - this.data.list_contract[k].fee_amount
                        this.withdrawal.fee_amount = this.withdrawal.fee_amount + this.data.list_contract[k].fee_amount;
                        this.withdrawal.refun_amount = this.withdrawal.refun_amount + this.data.list_contract[k].refun_amount;
                        this.data.list_contract[k].number_done_sessions = this.getDoneSession(this.data.list_contract[k]);
                    }
                }else{
                     for (var k in this.data.list_contract){
                        this.data.list_contract[k].fee_amount = Math.ceil(this.data.list_contract[k].real_amount*30/100)
                        this.data.list_contract[k].refun_amount = this.data.list_contract[k].real_amount - this.data.list_contract[k].fee_amount
                        this.withdrawal.fee_amount = this.withdrawal.fee_amount + this.data.list_contract[k].fee_amount;
                        this.withdrawal.refun_amount = this.withdrawal.refun_amount + this.data.list_contract[k].refun_amount;
                        this.data.list_contract[k].number_done_sessions = this.getDoneSession(this.data.list_contract[k]);
                    }
                }
            },
            processWithdraw(contract,idc,date){
                let real_sessions =0
                if(contract.passed_trial){
                    real_sessions = contract.real_sessions;
                }else{ // tích chọn bỏ qua học trải nghiệm
                    real_sessions = (contract.real_sessions > 3) ? (contract.real_sessions - 3) : 0;
                }
                this.getHolidays(contract.branch_id);
                let holidays = u.live(this.data.holidays[contract.branch_id][contract.product_id])?this.data.holidays[contract.branch_id][contract.product_id].concat(contract.reserved_dates):contract.reserved_dates;

                this.withdrawal.withdraw_date = date;
                let done_sessions = u.calSessions(contract.enrolment_start_date, u.pre(date), holidays, contract.class_days).total;
                let official_done_sessions = contract.passed_trial ? done_sessions : ((done_sessions > 3) ? (done_sessions - 3) : 0);
                //if(this.isGreaterThan(date,contract.start_date) && contract.status == 6){
                if(official_done_sessions > real_sessions){
                    official_done_sessions = real_sessions
                }
                if( contract.status == 6 || ((contract.status==3 ||contract.status == 4) && contract.relation_contract_id && contract.relation_contract_type != 8 && contract.relation_contract_type != 85 && contract.relation_contract_type != 86 && contract.relation_contract_class != null)){
                    // Không hiểu, đoạn này luôn sai mà?
                    if((contract.type == 3 && contract.type == 4 )&& contract.status==3){
                        official_done_sessions = 0;
                        this.data.list_contract[idc].real_amount = contract.total_charged;
                    }else{
                        this.data.list_contract[idc].real_amount = Math.ceil(((real_sessions - official_done_sessions) * (contract.total_charged / real_sessions)));
                        if(this.data.list_contract[idc].real_amount <0 ){
                            this.data.list_contract[idc].real_amount =0;
                        }
                        this.withdrawal.check_fee_amount = 1;
                    }
                }else{
                    official_done_sessions = 0;
                    this.data.list_contract[idc].real_amount = contract.total_charged;
                }
                this.data.list_contract[idc].done_sessions = official_done_sessions
                this.withdrawal.real_amount = this.withdrawal.real_amount + this.data.list_contract[idc].real_amount
                this.withdrawal.done_sessions = this.withdrawal.done_sessions + official_done_sessions
                this.withdrawal.left_sessions = this.withdrawal.left_sessions + (real_sessions - official_done_sessions)
                if(this.data.list_contract[idc].real_amount==0){
                    this.withdrawal.is_fee_null =1;
                }
            },
            isGreaterThan(_from, _to){
                let _from_time = new Date(_from); // Y-m-d
                let _to_time = new Date(_to); // Y-m-d
                return (_from_time.getTime() > _to_time.getTime())?true:false;
            },
            getHolidays(branch_id){
                if(!u.live(this.data.holidays[branch_id])){
                    let url = '/api/info/'+branch_id+'/holidays';
                    u.a().get(url)
                        .then((response) => {
                            if(response.data.code == 200){
                                this.data.holidays[branch_id] = response.data.data;
                            }else{
                                this.data.holidays[branch_id] = []
                            }
                        });
                }
            },
            addWithdraw(){
                let data = {
                    contract_id: this.withdrawal.contract_info.id,
                    student_id: this.withdrawal.contract_info.student_id,
                    branch_id: this.withdrawal.contract_info.branch_id,
                    ec_id: this.withdrawal.contract_info.ec_id,
                    refun_amount: this.withdrawal.refun_amount,
                    fee_amount: this.withdrawal.fee_amount,
                    real_amount: this.withdrawal.real_amount,
                    withdraw_date: this.withdrawal.withdraw_date,
                    done_sessions : this.withdrawal.done_sessions,
                    left_sessions : this.withdrawal.left_sessions,
                    meta_data: this.data.list_contract,
                    comment_created: this.withdrawal.comment_created,
                    attached_file: this.withdrawal.attached_file,
                };
                if(data.withdraw_date==''){
                    alert('Ngày rút phí là bắt buộc');
                    return false;
                }else if(this.withdrawal.is_fee_null==1){
                    alert('Số tiền thực nhận của từng gói phải lớn hơn 0');
                    return false;
                }else{
                	this.html.loading.action = true
                	u.a().post('/api/withdrawals', data).then(response =>{
                    	this.html.loading.action = false
                        this.html.dom.modal.message = "Thêm mới thành công rút phí!"
                        this.html.dom.modal.display = true
                    })
                }
            },
            exitModal(){
                this.$router.push('/withdrawals')
            },
            enableEditorFee(index){
                if(this.withdrawal.withdraw_date){
                    if(this.data.list_contract[index].enable_editor_fee==0){
                        this.withdrawal.withdraw_date=''
                        this.selectStudent(this.data.student);
                        for (var k in this.data.list_contract){
                        	$(`#amount_fee_${k}`).prop('readonly', true)
                        }
                    }else{
                        $(`#amount_fee_${index}`).prop('readonly', false)
                    }
                }else{
                    this.data.list_contract[index].enable_editor_fee=0;
                    $(`#enable_fee_${index}`).prop('checked',false)
                    alert('Vui lòng chọn ngày rút phí');
                }

            },
            changeAmountFee(index){
                const new_amount = parseInt($(`#amount_fee_${index}`).val())
                if(new_amount<0 || new_amount>this.data.list_contract[index].refun_amount){
                    $(`#amount_fee_${index}`).val(this.data.list_contract[index].fee_amount);
                    alert('Số tiền bị khấu trừ phải nhỏ hơn số tiền còn lại và lớn hơn 0');
                }else{
                    this.withdrawal.fee_amount += new_amount - this.data.list_contract[index].fee_amount
                    this.withdrawal.refun_amount = this.withdrawal.real_amount - this.withdrawal.fee_amount
                    this.data.list_contract[index].fee_amount = new_amount
                    this.data.list_contract[index].refun_amount = this.data.list_contract[index].real_amount - this.data.list_contract[index].fee_amount
                }
            },
             getDate(date) {
                if (date instanceof Date && !isNaN(date.valueOf())) {
                    var year = date.getFullYear(),
                        month = (date.getMonth() + 1).toString(),
                        day = date.getDate().toString(),
                        strMonth = month < 10 ? "0" + month : month,
                        strYear = day < 10 ? "0" + day : day;

                    return `${year}-${strMonth}-${strYear}`;
                }
                return '';
            },
            uploadFile(file, param = null) {
               if (param) {
                 this.withdrawal.attached_file = file
               }
             },
            onDrawDate (e) {
                let date = e.date;
                date = u.convertDateToString(date);
                if (this.isGreaterThan(this.html.dom.calendar.minDate,date)
                    || this.isGreaterThan(date, this.html.dom.calendar.maxDate)) {
                    e.allowSelect = false;
                }
            },
        },
         filters: {
            formatStatusContract(status){
                let tmp
                switch(status) {
                    case 0:
                        tmp ='Đã xóa'
                        break;
                    case 1:
                        tmp = 'Đã active nhưng chưa đóng phí'
                        break;
                    case 2:
                        tmp = 'Đã active và đặt cọc nhưng chưa thu đủ phí'
                        break;
                    case 3:
                        tmp = 'Đã active và thu đủ phí nhưng chưa xếp lớp'
                        break;
                    case 4:
                        tmp = 'Đang bảo lưu không giữ chỗ hoặc pending'
                        break;
                    case 5:
                        tmp = 'Được nhận học bổng hoặc VIP'
                        break;
                    case 6:
                        tmp = 'Đã được xếp lớp và đang đi học'
                        break;
                    case 7:
                        tmp = 'Đã bị withdraw'
                        break;
                    default:
                        tmp = 'Rút phí'
                    }
                return tmp
            }
        }
    }
</script>


