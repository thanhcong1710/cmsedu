<template>
    <div class="animated fadeIn apax-form" @click="html.action.page">
        <div class="row">
            <div class="col-12 apax-show-detail">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-id-card"></i> <b class="uppercase">Tạo Bản Ghi Chuyển Trung Tâm</b>
                    </div>
                    <loader :active="html.processing.form" text="Đang xử lý..." :duration="html.duration.form" />
                    <div id="page-content tuition-transfer-form">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body tuition-transfer-form">
                                        <div class="row">
                                            <div class="col-md-6" :class="html.config.style">
                                                <loader :active="html.processing.from" text="Đang tải dữ liệu..." :duration="html.duration.from" />
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main first-upper">Trung Tâm Chuyển Đi</h6>
                                                    </address>
                                                </div>
                                                <div class="col-12 pad-no" :class="html.display.from_branch">
                                                    <div class="form-group">
                                                        <label class="control-label">Chọn Trung Tâm</label>
                                                        <branch                                                        
                                                            label="name"
                                                            :filterable=true
                                                            :options="data.from.branch_list"
                                                            v-model="branch_info" 
                                                            @input="html.action.from_branch"
                                                            :disabled="html.disabled.from_branch"
                                                            placeholder="Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước" 
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-12 pad-no" :class="html.display.from_search">
                                                    <div class="form-group">
                                                        <label class="control-label">Chọn Học Sinh</label>
                                                        <search
                                                            :displayIcon="html.loading.from"
                                                            :endpoint="html.action.from_link"
                                                            :disabled="html.disabled.from_search"
                                                            :suggestStudents="html.action.from_search"
                                                            :onSelectStudent="html.action.from_load"
                                                            placeholder="Tìm kiếm học sinh theo mã CRM/LMS hoặc Tên">
                                                        </search>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.from_detail">
                                                    <div class="form-group">
                                                        <label class="control-label">Thông Tin Hồ Sơ Học Sinh</label>
                                                        <div class="profile-detail" v-html="data.from.student_detail" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.from_summary">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng Số Tiền Chuyển Đi</label>
                                                                <input class="form-control" :value="data.from.transfer_amount | formatMoney" type="text" :disabled="html.disabled.transfer_amount">
                                                            </div>    
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng Số Buổi Chuyển Đi</label>
                                                                <input class="form-control" :value="data.from.transfer_sessions" type="text" :disabled="html.disabled.transfer_session">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.from_contracts">
                                                    <div class="row">
                                                        <div class="col-md-12 pad-no">
                                                            <div class="form-group">
                                                                <label class="control-label">Các gói phí chuyển đi</label>
                                                                <div class="col-md-12" v-for="(item, idx) of data.from.transfer_contracts" :key="idx">
                                                                    <div class="transfer-contract-list row" v-html="item" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" :class="html.config.style">
                                                <loader :active="html.processing.to" text="Đang tải dữ liệu..." :duration="html.duration.to" />
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main first-upper">Trung Tâm Chuyển Đến</h6>
                                                    </address>
                                                </div>
                                                <div class="col-12 pad-no" :class="html.display.to_branch">
                                                    <div class="form-group">
                                                        <label class="control-label">Chọn Trung Tâm</label>
                                                        <branch
                                                            label="name"
                                                            :filterable=true
                                                            :options="data.to.branch_list"
                                                            @input="html.action.to_branch"
                                                            :disabled="html.disabled.to_branch"
                                                            :placeholder="html.config.to_placeholder"
                                                            v-model="branch_info_to"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.to_setting">
                                                    <div class="row" :class="html.display.none_contract">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="filter-label control-label">Chọn Kỳ Học <strong class="text-danger h6">*</strong></label>
                                                                <select id="semester-list"
                                                                        class="selection semester form-control"
                                                                        v-model="data.information.semester_id"
                                                                        @change="html.action.to_semester"
                                                                        :disabled="html.disabled.select_semester">
                                                                    <option value="-1" disabled> Chọn kỳ học </option>
                                                                    <option :value="semester.id" v-for="(semester, idx) in data.to.semesters" :key="idx">
                                                                        {{ semester.name }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group select-date">
                                                                <label class="control-label">Ngày Chuyển Trung Tâm <strong class="text-danger h6">*</strong></label>
                                                                <datepicker
                                                                    id="transfer-date"
                                                                    class="form-control calendar"
                                                                    :value="data.from.transfer_date"
                                                                    v-model="data.from.transfer_date"
                                                                    placeholder="Chọn ngày chuyển trung tâm"
                                                                    :not-before="data.to.none_before"
                                                                    :not-after="data.to.none_after"
                                                                    @change="html.action.to_transfer_date"
                                                                    :disabled="html.disabled.from_transfer_date"
                                                                    lang="lang"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group select-date">
                                                                <label class="control-label"><strong style="color: red">Sản phẩm</strong></label>
                                                                <input class="form-control" :value="data.to.tmp_product_name" type="text" :disabled="true" style="color:red; font-weight:bold">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.to_detail">
                                                    <div class="form-group">
                                                        <label class="control-label">Thông Tin Hồ Sơ Học Sinh</label>
                                                        <div class="profile-detail" v-html="data.to.student_detail" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.to_transfer_info">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng Số Tiền Chuyển Đến</label>
                                                                <input class="form-control" :value="data.to.transfer_amount | formatMoney" type="text" :disabled="html.disabled.receiver_amount">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Tổng Số Buổi Chuyển Đến</label>
                                                                <input class="form-control" :value="data.to.transfer_sessions" type="text" :disabled="html.disabled.receiver_session">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no" :class="html.display.to_contract">
                                                    <div class="row">
                                                        <div class="col-md-12 pad-no">
                                                            <div class="form-group">
                                                                <label class="control-label">Các gói chuyển phí chuyển đến</label>
                                                                <div class="col-md-12" v-for="(item, idx) of data.to.receive_contract" :key="idx">
                                                                    <div class="receiving-contract row" v-html="item" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" :class="html.display.to_setting">
                                                    <div class="row">
                                                         <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Thời gian bắt đầu học <strong class="text-danger h6">*</strong></label>
                                                                <datepicker
                                                                    id="transfer-date"
                                                                    class="form-control calendar"
                                                                    :value="data.from.thoi_gian_hoc"
                                                                    v-model="data.from.thoi_gian_hoc"
                                                                    placeholder="Chọn ngày bắt đầu học"
                                                                    lang="lang"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ca học <strong class="text-danger h6">*</strong></label>
                                                                <select id="customer-types" class="form-control" v-model="data.from.shift_id">
                                                                    <option value disabled>Chọn ca học</option>
                                                                      <option :value="shift.id" v-for="(shift, idx) in list_shift" :key="idx">
                                                                        {{ shift.name }}
                                                                    </option>
                                                                </select>   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <label class="control-label">Lý Do Chuyển Trung Tâm <strong class="text-danger h6">*</strong></label>
                                                                <textarea class="form-control" v-model="data.from.transfer_note" :readonly="html.disabled.from_reason" rows="5"></textarea>                
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" :class="html.display.to_setting">
                                                            <div class="form-group">
                                                                <file
                                                                    :label="'File đính kèm'"
                                                                    name="'upload_transfer_file'"
                                                                    :field="'attached_file'"
                                                                    :type="'transfer_file'"
                                                                    :full="false"
                                                                    :onChange="html.action.upload_file"
                                                                    :title="'Tải lên 1 file đính kèm với định dạng tài liệu: jpg, png, pdf, doc, docx.'"
                                                                    :customClass="'no-current-file'"
                                                                >
                                                                </file>
                                                                <div class="remove-file">
                                                                    <span class="remove-file-button" @click="html.action.remove_file" v-show="html.action.remove_attached_file"><i class="fa fa-times-circle"></i> Hủy đính kèm file</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no" :class="html.display.buttons">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <apaxbtn
                                                                :onClick="html.action.save"
                                                                :disabled="html.disabled.save"
                                                                icon="fa-save"
                                                                label="Lưu"
                                                                title="Lưu bản ghi chuyển trung tâm mới"
                                                                markup="success">
                                                            </apaxbtn>
                                                            <apaxbtn
                                                                :onClick="html.action.cancel"
                                                                :disabled="html.disabled.cancel"
                                                                icon="fa-sign-out"
                                                                label="Thoát"
                                                                title="Thoát form tạo chuyển trung tâm"
                                                                markup="error">
                                                            </apaxbtn>
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
            </div>
        </div>
    </div>
</template>

<script>
    import datepicker from 'vue2-datepicker'
    import search from '../../../components/StudentSearch'    
    import loader from '../../../components/Loading'
    import apaxbtn from '../../../components/Button'
    import branch from 'vue-select'
    import file from '../../../components/File'
    import u from '../../../utilities/utility'

    export default {
        name: 'Add-Tuition-Transfer',
        components: {
            datepicker,
            apaxbtn,
            loader,
            branch,
            search,
            file
        },
        data() {
            const model = u.m('contracts').page
            model.html = {
                action: {
                    from_recal: () => this.recalculate(),
                    from_branch: branch => this.selectFromBranch(branch),
                    from_link: 0,
                    from_search: keyword => this.searchSuggestTransferStudent(keyword),
                    from_load: student => this.selectTransferStudent(student),
                    upload_file: (file, param) => this.uploadFile(file, param),
                    remove_file: '',
                    remove_attached_file: false,
                    to_transfer_date: date => this.selectTransferDate(date),
                    to_branch: branch => this.selectToBranch(branch),
                    to_semester: () => this.selectToSemester(),
                    to_link: 0,
                    page: () => this.handle(),
                    save: () => this.saveForm(),
                    reset: () => this.resetForm(),
                    cancel: () => this.cancelForm(),
                    update: () => this.updateInformation()
                },
                lable: {
                    update: 'Sửa'
                },
                loading: {
                    from: 'hidden',
                    to: 'hidden'
                },
                display: {
                    none_contract: 'hidden',
                    from_contracts: 'hidden',
                    from_addition: 'hidden',
                    from_summary: 'hidden',
                    from_branch: 'hidden',
                    from_search: 'hidden',
                    from_detail: 'hidden',
                    to_transfer_info: 'hidden',
                    to_new_contract: 'hidden',
                    to_contract: 'hidden',
                    to_setting: 'hidden',
                    to_summary: 'hidden',
                    to_branch: 'hidden',
                    to_search: 'hidden',
                    to_detail: 'hidden',
                    to_date: 'hidden',
                    buttons: 'hidden'
                },
                disabled: {
                    transfer_amount: true,
                    transfer_session: true,
                    from_branch: true,
                    from_reason: true,
                    from_search: true,
                    from_transfer_date: true,
                    select_semester: true,
                    to_search: true,
                    to_branch: true,
                    to_product: true,
                    to_start_date: true,
                    select_product: true,
                    receiver_amount: true,
                    receiver_session: true,
                    to_expected_class: true,
                    save: true,
                    edit: true,
                    reset: true,
                    cancel: false
                },
                processing: {
                    form: false,
                    from: false,
                    to: false
                },
                duration: {
                    form: 800,
                    from: 600,
                    to: 500
                },
                config: {
                    calling: false,
                    style: '',
                    page: '',
                    to_placeholder: 'Vui lòng chọn học sinh chuyển trung tâm trước'
                },
                branch_info:'',
                branch_info_to:'',
            }
            model.data = {
                from: {
                    branch: 0,
                    amount: 0,
                    session: 0,
                    student: null,
                    student_id: 0,
                    contracts: [],
                    branch_list: [],
                    student_name: '',
                    student_detail: '',
                    transfer_date: '',
                    none_after: '',
                    renewed: null,                    
                    transfer_note: '',
                    addition_amount: 0,                    
                    transfer_amount: 0,
                    addition_session: 0,
                    transfer_sessions: 0,
                    transfer_contracts: [],
                    calculated_contracts: [],
                    shift_id:'',
                    thoi_gian_hoc:'',
                },
                to: {
                    branch: 0,
                    amount: 0,
                    session: 0,
                    student: null,
                    student_id: 0,
                    contracts: [],
                    branch_list: [],
                    student_detail: '',
                    transfer_product: null,
                    contract: null,
                    products: [],
                    program_label: '',
                    start_date: '',
                    semesters: [],
                    semester_id: 0,
                    semester_name: '',
                    none_before: '',
                    end_date: '',
                    none_after: '',
                    transfer_amount: 0,
                    transfer_sessions: 0,
                    total_amount: 0,
                    total_session: 0,
                    receive_contract: [],
                    expected_class: '',
                    attached_file: null,
                    calculated_contracts: [],
                    tmp_product_name:''
                },
                temp:{
                    update: false,
                    transfer_amount: 0,
                    transfer_session: 0,
                    total_sessions: 0,
                    the_last_date: '',
                },
                information: {
                    date: '',
                    is_back: 0,
                    ex_data: null,
                    from_branch_id: 0,
                    to_branch_id: 0,
                    semester: null,
                    semester_id: 0,
                    semester_name: '',
                    expected_class: ''
                },
                none_contract:0,
            }
            model.list_shift= []
            return model
        },
        created(){
            u.i(this)
            this.start()
        },
        methods: {
            start() {
                this.branch_info = this.session.user.branches.filter(item => item.id == this.session.user.branch_id)[0]
                this.data.from.none_before = this.moment().format('YYYY-MM-DD')
                this.data.to.none_before = this.moment().format('YYYY-MM-DD')
                this.data.from.branch_list = this.session.user.branches
                this.data.to.branch_list = this.session.info.branches
                this.html.display.from_branch = 'display'
                this.html.display.to_branch = 'display'                
                this.html.disabled.from_branch = false
            },
            handle() {},
            loadContent(data, student = false ,type =0) {
                let result = ''
                if (data) {
                    if (student) {
                        result = `<div class="from-student information item col-md-12">
                            <div class="student info detail row">
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Họ tên: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.name)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Mã CRM: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.crm_id)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Mã kế toán: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.accounting_id)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Phụ huynh:
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.gud_name1)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Điện thoại: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.gud_mobile1)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Email : 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.gud_email1)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Địa chỉ: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.address)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            EC: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.ec_name)}
                                        </div>
                                    </div>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            CS: 
                                        </div>
                                        <div class="col-md-8">
                                            ${u.changeNull(data.cm_name)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    } else {
                        const class_info = data.class_name ? `<div class="info line col-md-12"><div class="row">
                                            <div class="info line col-md-4">Lớp học</div>
                                            <div class="info line col-md-8">${data.class_name}</div>
                                        </div></div>` : ''
                        result = `<div class="transfering contract item col-md-12 ${data.class || ''}">                                        
                            <div class="contract detail row">
                                <div class="info line col-md-12">
                                    <h5>${(data.order || 0)} . Hợp đồng số (${data.accounting_id}) - ${data.tuition_fee_name}</h5>
                                </div>
                                <div class="info line col-md-12">
                                    <div class="row detail-info">
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Thời gian: 
                                                </div>
                                                <div class="info line col-md-8">
                                                    Từ ${data.start_date} tới ${data.last_date || data.end_date}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Đã đóng:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${u.f(data.total_charged)} (${parseInt(data.real_sessions, 10)} buổi)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12 ${type != 2 ? 'display' : 'hidden'}" >
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Đã học:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${u.f(data.total_charged-data.left_amount)} (${parseInt(data.done_sessions, 10)} buổi)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Còn lại:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${u.f(data.left_amount)} (${parseInt(data.left_sessions, 10)} buổi)
                                                </div>
                                            </div>
                                        </div>
                                         <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Buổi học bổng:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${parseInt(data.bonus_sessions, 10)} buổi
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Sản phẩm:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${data.product_name}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Chương trình:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${u.changeNull(data.program_name)} 
                                                </div>
                                            </div>
                                        </div>
                                        ${class_info}
                                    </div>
                                </div>
                            </div>
                        </div><div style="width:100%;margin-top: 10px;" class="${type == 1 ? 'display' : 'hidden'}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="control-label"
                                        >Số Tiền Chuyển</label>
                                        <input
                                            class="form-control"
                                            value="${u.f(data.left_amount)}"
                                            type="text"
                                            disabled="true"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="control-label"
                                        >Số Buổi Chuyển</label>
                                        <input
                                            class="form-control"
                                            value="${data.left_sessions+data.bonus_sessions}"
                                            type="text"
                                            disabled="true"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="width:100%;margin-top: 10px;" class="${type == 2 ? 'display' : 'hidden'}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="control-label"
                                        >Số Tiền Nhận</label>
                                        <input
                                            class="form-control"
                                            value="${u.f(data.left_amount)}"
                                            type="text"
                                            disabled="true"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            class="control-label"
                                        >Số Buổi Nhận</label>
                                        <input
                                            class="form-control"
                                            value="${data.left_sessions+data.bonus_sessions}"
                                            type="text"
                                            disabled="true"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>`
                    }
                }
                return result
            },
            updateInformation() {
                if (this.data.temp.update === false) {
                    const confirmation = confirm('Bạn sẽ phải chịu trách nhiệm về những thông tin chỉnh sửa này, bạn có chắc là muốn tiếp tục không?')
                    if (confirmation) {
                        this.html.disabled.transfer_amount = false
                        this.html.disabled.transfer_session = false
                        this.html.lable.update = 'Hủy'
                        this.data.temp.update = true
                    }
                } else {
                    this.data.from.transfer_sessions = this.data.temp.transfer_sessions
                    this.data.from.transfer_amount = this.data.temp.transfer_amount
                    this.data.temp.update = false
                    this.html.disabled.transfer_amount = true
                    this.html.disabled.transfer_session = true
                    this.html.lable.update = 'Sửa'
                }
            },
            selectFromBranch(branch) {
                this.data.from.branch = branch.id
                this.html.display.from_search = 'display'
                this.data.information.from_branch_id = branch.id
                const new_list = []
                this.session.info.branches.map(item => {
                    if (parseInt(item.id, 10) !== parseInt(this.data.from.branch, 10)) {
                        new_list.push(item)                        
                    }
                    return item
                })
                this.branch_info_to = '';
                this.data.to.branch_list = new_list
                this.html.disabled.to_branch = true                          
                this.data.to.student_detail = ''
                this.data.from.student_detail = ''
                this.html.display.to_detail = 'hidden'
                this.html.display.to_setting = 'hidden'
                this.html.display.to_contract = 'hidden'
                this.html.display.from_detail = 'hidden'
                this.html.display.from_summary = 'hidden'                            
                this.html.display.from_contracts = 'hidden'
                this.html.display.to_new_contract = 'hidden'
                this.html.display.to_transfer_info = 'hidden'
            },            
            searchSuggestTransferStudent(keyword) {
                if (keyword && keyword.length > 3 && this.html.config.calling === false) {
                    keyword = keyword.trim()
                    keyword = keyword.replace(/[~`!#$%^&*[,\]./<>?;'\\:"|\t]/gi, '');
                    this.html.loading.from = 'display'
                    this.html.config.calling = true
                    return new Promise((resolve, reject) => {
                        u.g(`/api/branch-transfers/suggest-passenger/${keyword}/${this.data.from.branch}`)
                        .then(response => {
                            const resp = response.length ? response : [{
                                label: 'Không tìm thấy kết quả nào phù hợp',
                                student_name: 'Không có kết quả nào phù hợp'
                            }]
                            this.html.config.calling = false
                            this.html.loading.from = 'hidden'
                            resolve(resp)
                        }).catch(e => {
                            u.lg(e)
                            this.html.loading.from = 'hidden'
                        })
                    })
                }
            },
            selectTransferStudent(student) {
                if(this.html.config.calling === false && student.student_id !=undefined){
                    this.html.display.to_detail = 'hidden'
                    this.html.display.to_setting = 'hidden'
                    this.html.disabled.save = true
                    this.html.display.to_transfer_info = 'hidden'
                    this.branch_info_to = ''
                    this.html.display.to_contract ='hidden'
                    this.html.config.calling = true
                    this.data.from.student_id = student.student_id
                    this.html.processing.from = true
                    let url = `/api/branch-transfers/contracts/passenger/${student.student_id}`
                    u.g(url).then((response) => {                            
                        this.html.config.calling = false
                        let alert = false
                        this.none_contract = 0 
                        if (response.has_error == 0) {
                            this.data.to.none_after = response.student.next_learning_date 
                            this.data.from.student = response.student
                            this.data.from.student_detail = this.loadContent(response.student, 1,0)
                            this.data.from.contracts = response.contracts
                            this.data.from.transfer_contracts = []
                            this.html.display.from_detail = 'display'
                            if (response.contracts.length) {
                                for (let i = 0; i < response.contracts.length; i++) {
                                    const contract_data = response.contracts[i]
                                    contract_data.order = i + 1;
                                    this.data.from.transfer_contracts.push(this.loadContent(contract_data,0,0))
                                }                                
                                this.html.display.from_contracts = 'display'  
                                this.html.display.none_contract = 'display'                              
                                this.html.to_placeholder = 'Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước.'
                            } else {
                                this.html.display.from_contracts = 'hidden'
                                this.html.display.none_contract = 'hidden'
                                this.html.disabled.from_reason = false
                                this.none_contract = 1
                                alert = `Học sinh <b>${response.student.name}</b> hiện không có gói phí nào nên quá trình chuyển trung tâm sẽ chỉ chuyển thông tin hồ sơ của học sinh.`
                            }
                            this.html.disabled.to_branch = false
                        } else {
                            this.data.from.transfer_contracts = []                            
                            this.html.disabled.to_branch = true
                            // this.html.config.calling = true                            
                            this.data.to.student_detail = ''
                            this.data.from.student_detail = ''
                            this.html.display.to_detail = 'hidden'
                            this.html.display.to_setting = 'hidden'
                            this.html.display.to_contract = 'hidden'
                            this.html.display.from_detail = 'hidden'
                            this.html.display.from_summary = 'hidden'                            
                            this.html.display.from_contracts = 'hidden'
                            this.html.display.to_new_contract = 'hidden'
                            alert = response.message
                        }
                        if (alert) {
                            this.$notify({
                                group: 'apax-atc',
                                title: 'Thông Báo!',
                                type: 'warning',
                                duration: 5000,
                                text: alert
                            })
                        }
                        this.html.config.to_placeholder = 'Vui lòng chọn trung tâm chuyển đến'
                        this.html.display.buttons = 'display'
                        this.html.processing.from = false
                    })
                }
            },
            selectToBranch(branch) {
                if(branch!=''){
                    this.data.to.branch = branch.id
                    this.data.information.to_branch_id = branch.id
                    this.html.display.to_search = 'display'
                    const student_id = parseInt(this.data.from.student.id, 10)
                    u.g(`/api/branch-transfers/passenger/check/${student_id}/${branch.id}`).then((response) => {
                        this.data.information.ex_data = response.ex_data
                        this.data.information.is_back = response.come_back
                        this.data.to.student = response.student
                        this.data.to.student_detail = this.loadContent(response.student, 1,0)
                        this.html.display.to_detail = 'display'
                        this.html.display.to_setting = 'display'
                        this.html.disabled.select_semester = false
                        this.data.to.semesters = response.semesters
                        this.data.information.semester_id = -1
                        const msg = response.come_back ? 'Học sinh đang được chuyển trở lại trung tâm cũ, vui lòng chọn một kỳ học!' : 'Vui lòng chọn kỳ học tại trung tâm chuyển tới!'
                        if(this.none_contract==1){
                            this.html.disabled.save = false
                        }else{
                            this.html.disabled.save = true
                            this.$notify({
                                group: 'apax-atc',
                                title: 'Thông Báo!',
                                type: 'info',
                                duration: 5000,
                                text: msg
                            })
                        }
                    })
                    u.a().get(`/api/shifts/branch/${this.data.to.branch}?status=1`).then(response => {
                        this.list_shift = response.data;
                    })
                }
            },
            selectToSemester() {
                this.$notify({
                    group: 'apax-atc',
                    title: 'Thông Báo!',
                    type: 'info',
                    duration: 5000,
                    text: 'Vui lòng chọn ngày chuyển trung tâm!'
                })
                this.html.disabled.from_transfer_date = false
                this.html.disabled.save = true
                this.data.from.transfer_date=''
                let tmp_product_name = ''
                let tmp_semester_id = this.data.information.semester_id
                this.data.to.semesters.forEach(function(element) {
                    if(element.id == tmp_semester_id){
                        tmp_product_name = element.product_name
                    }
                });
                this.data.to.tmp_product_name = tmp_product_name
            },
            prepareTransfermation() { 
                u.lg([this.data.from.transfer_date, this.data.information.product, this.data.from.contracts, this.data.to.contract], 'INPUT DATA')
                this.data.from.transfer_contracts = []
                this.data.to.receive_contract = []                
                this.data.from.total_sessions = 0
                const holidays = this.session.info.holidays
                this.type_notify = 'info'
                this.text_notify = 'Vui lòng điền lý do chuyển trung tâm và nhấn nút Lưu!'
                this.html.disabled.from_search = true
                // this.html.disabled.from_transfer_date = true
                this.html.disabled.save = false
                this.html.disabled.reset = false
                this.html.disabled.cancel = false
                if (this.data.from.contracts.length) {
                    this.data.from.contracts.map(contract => {
                        const left_sessions = contract.enrolment_start_date ? parseInt(contract.real_sessions) - parseInt(u.calSessions(contract.enrolment_start_date, this.data.from.transfer_date, holidays, contract.class_days).total, 10) : parseInt(contract.real_sessions, 10)
                        contract.transferable_sessions = left_sessions
                        this.data.from.total_sessions += left_sessions
                        contract.transferable_amount = parseInt(( Math.ceil((contract.tuition_fee_receivable / contract.tuition_fee_sessions) * left_sessions / 1000) * 1000), 10)
                        this.data.from.transfer_amount += contract.transferable_amount
                        return contract
                    })
                    this.data.from.transfer_sessions = this.data.from.total_sessions
                    this.html.processing.to = true
                    this.html.processing.from = true
                    this.data.information.from_branch_id = parseInt(this.data.from.branch, 10)
                    this.data.information.to_branch_id = parseInt(this.data.to.branch, 10)
                    this.data.information.transfer_date = this.data.from.transfer_date 
                    const transferPrepareData = {
                        transfering_data: {
                            student: this.data.from.student,                            
                            information: this.data.information,
                            contracts: this.data.from.contracts                            
                        }
                    }
                    this.html.display.to_contract = 'hidden'
                    u.p('/api/branch-transfers/prepare-transfer-data', transferPrepareData)
                    .then(response => {
                        if(response.error==1){
                            this.html.disabled.save = true
                            this.html.processing.to = false
                            this.html.processing.from = false
                            this.$notify({
                                group: 'apax-atc',
                                title: 'Thông Báo!',
                                type: 'warning',
                                duration: 5000,
                                text: response.message_error
                            })
                        }else{
                            u.lg(response, 'Prepared data')
                            this.data.to.total_amount = response.total_transfered_amount
                            this.data.to.total_session = response.total_transfered_sessions
                            if (response.transfered_contracts.length) {                            
                                this.html.display.to_contract = 'display'
                                this.data.to.contracts = []
                                response.transfered_contracts.forEach((contract, idx) => {                                
                                    contract.start_date = u.older(this.data.from.transfer_date, contract.start_date) ? contract.start_date : this.data.from.transfer_date
                                    contract.end_date = this.data.from.transfer_date
                                    contract.last_date = this.data.from.transfer_date
                                    contract.class = 'transfered'
                                    this.data.from.transfer_amount = response.total_transfer_amount
                                    this.data.to.transfer_amount = response.total_transfered_amount
                                    this.data.from.transfer_sessions = response.total_transfer_session
                                    this.data.to.transfer_sessions = response.total_transfered_sessions
                                    this.html.display.from_summary = 'display'
                                    this.html.display.to_transfer_info = 'display'
                                    this.data.from.transfer_contracts.push(this.loadContent(contract,0,1))
                                    console.log(this.data.from.transfer_sessions)
                                })
                            }
                            if (response.received_contracts.length) {
                                response.received_contracts.forEach((contract, idx ) => {
                                    contract.class = 'received'
                                    this.data.to.contracts.push(contract)
                                    this.data.to.receive_contract.push(this.loadContent(contract,0,2))
                                })
                            }
                            this.html.processing.to = false
                            this.html.processing.from = false
                            this.$notify({
                                group: 'apax-atc',
                                title: 'Thông Báo!',
                                type: 'info',
                                duration: 5000,
                                text: 'Vui lòng điền lý do chuyển trung tâm và nhấn nút Lưu!'
                            })
                        }
                    })
                } else{
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Thông Báo!',
                        type: 'info',
                        duration: 5000,
                        text: 'Vui lòng điền lý do chuyển trung tâm và nhấn nút Lưu!'
                    })
                }
            },
            selectTransferDate(date) {
                if(date!=null){                
                    this.data.from.transfer_date = moment(date).format('YYYY-MM-DD')
                    this.prepareTransfermation()
                    this.html.disabled.from_reason = false
                }else{
                    this.html.disabled.save = true
                }
            },            
            selectStartDate(date) {
                this.data.information.date = moment(date).format('YYYY-MM-DD')                
                this.html.disabled.from_transfer_date = false
                this.$notify({
                    group: 'apax-atc',
                    title: 'Lưu ý!',
                    type: 'primary',
                    duration: 3000,
                    text: 'Xin vui lòng chọn ngày chuyển phí!'
                })
            },
            uploadFile(file, param = null) {
                if (param) {
                    this.data.to.attached_file = file
                }
            },
            cancelForm() {
                this.$router.push('/branch-transfers')
            },
            resetForm() {
                this.$router.push('/branch-transfers')
            },
            saveForm() {
                if(!this.data.from.thoi_gian_hoc){
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Thông Báo!',
                        type: 'info',
                        duration: 5000,
                        text: 'Vui lòng thời gian bắt đầu học!'
                    })
                    this.html.processing.to = false
                    this.html.processing.from = false
                    return false;
                }
                if(!this.data.from.shift_id){
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Thông Báo!',
                        type: 'info',
                        duration: 5000,
                        text: 'Vui lòng chọn ca học!'
                    })
                    this.html.processing.to = false
                    this.html.processing.from = false
                    return false;
                }
                if(!this.data.from.transfer_note){
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Thông Báo!',
                        type: 'info',
                        duration: 5000,
                        text: 'Vui lòng điền lý do chuyển trung tâm và nhấn nút Lưu!'
                    })
                    this.html.processing.to = false
                    this.html.processing.from = false
                    return false;
                }
                const that = this
                this.html.processing.to = true
                this.html.processing.from = true
                this.html.disabled.save = true
                const transferPrepareData = {
                    transfermation_data: {
                        original_student: this.data.from.student,
                        transferred_student: this.data.to.student,
                        information: this.data.information,
                        receiver_contracts: this.data.to.contracts,
                        transfer_date: this.data.from.transfer_date,
                        transfer_note: this.data.from.transfer_note,
                        transfer_contracts: this.data.from.contracts,
                        to_total_amount: this.data.to.total_amount,
                        to_total_sessions: this.data.to.total_session,
                        from_total_amount: this.data.from.transfer_amount,
                        from_total_sessions: this.data.from.transfer_sessions,
                        file: this.data.to.attached_file,
                        shift_id:this.data.from.shift_id,
                        thoi_gian_hoc: moment(this.data.from.thoi_gian_hoc).format('YYYY-MM-DD')
                    }
                }
                u.p('/api/branch-transfers/save', transferPrepareData)
                .then(response => {
                    u.lg(response, 'Stored data')
                    this.html.processing.to = false
                    this.html.processing.from = false
                    u.apax.$emit('apaxPopup', {
                        on: true,
                        content: `Bản ghi chuyển trung tâm mã "<b>${response}</b>" đã được khởi tạo thành công và đang chờ được phê duyệt!\n`,
                        title: 'Thông Báo',
                        class: 'modal-success',
                        size: 'md',
                        confirm: {
                            success: {
                                button: 'OK',
                                action() {
                                    that.$router.push('/branch-transfers')
                                }
                            }
                        },
                        variant: 'success'
                    })
                })
            }
        }
    }
</script>
