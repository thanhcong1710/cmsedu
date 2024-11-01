<template>
    <div class="extralarge-modal animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>                    
                    <div slot="header">
                        <i class="fa fa-filter"></i> <b class="uppercase">Bộ Lọc</b>
                    </div>
                    <loader :active="processing" :spin="spin" :text="text" :duration="duration" />
                    <div id="filter_content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-4" v-if="filter_branch">
                                                <label class="filter-label control-label">Trung tâm</label><br/>
                                                <div class="form-group">
                                                    <searchBranch
                                                        :onSelect="selectBranch"
                                                        :disabled="html.dom.filter.branch.disabled"
                                                        :placeholder="html.dom.filter.branch.placeholder">
                                                    </searchBranch>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="filter-label control-label">Tìm kiếm</label><br/>
                                                    <input type="text" class="form-control" v-model="html.data.filter.keyword"  placeholder="Nhập tên học sinh hoặc mã CRM" />
                                                </div>
                                            </div>                                       
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="filter-label control-label">Trạng Thái</label><br/>
                                                    <select
                                                        v-model="html.data.filter.tuition_fee"
                                                        id="select_tuition_transfer"
                                                        name="search[tuition_transfer]"
                                                        class="tuition-transfer form-control"
                                                        :disabled="html.dom.filter.tuition_transfer.disabled"
                                                        :placeholder="html.dom.filter.tuition_transfer.placeholder">
                                                        <option v-for="(tuition_transfer, idx) in html.dom.filter.tuition_transfer.types"
                                                            :value="tuition_transfer.id"
                                                            :key="idx">
                                                            {{ tuition_transfer.label }}
                                                        </option>
                                                    </select>
                                                </div>                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="footer" class="text-center">
                        <router-link to="/tuition-transfers/add-tuition-transfer" v-show="html.dom.detail.add">
                            <button type="button" class="apn acl blue"><i class="fa fa-plus"></i> Thêm mới</button>
                        </router-link>
                        <button class="apn acl green" @click="filterData()"><i class="fa fa-search"></i> Tìm kiếm</button>
                        <button class="apn acl orange" @click="removeFilter()"><i class="fa fa-ban"></i> Bỏ lọc</button>
                    </div>
                </b-card>
            </div>
        </div>
        <div class="row drag-me-up">
            <div class="col-12">
                <b-card header>
                    <loader :active="processing" :text="text" :duration="duration" />
                    <div slot="header">
                        <i class="fa fa-list"></i> <b class="uppercase">{{html.dom.title}}</b>
                    </div>
                    <div id="list_content" class="panel-heading">
                        <div class="panel-body">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                        <tr class="text-sm">
                                            <th>STT</th>
                                            <th>HS Chuyển</th>
                                            <th>Mã CMS</th>
                                            <th>Mã kế toán</th>
                                            <th>HS Nhận</th>
                                            <th>Mã CMS</th>
                                            <th>Mã kế toán</th>
                                            <th>Số Tiền Chuyển</th>
                                            <th>Số Tiền Nhận</th>
                                            <th>Ngày Chuyển Phí</th>
                                            <th>Người Tạo</th>
                                            <th>Trạng Thái</th>                                            
                                            <th>Thao tác</th>
                                            <th>In</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(tuition_transfer, index) in list" :key="index" :class="`ttitm${tuition_transfer.status}`">
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{ index + 1 + ((html.pagination.cpage - 1) * html.pagination.limit) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.from_student_name}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.from_student_crm}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.from_student_act}}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.to_student_name}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.to_student_crm}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.to_student_act}}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.transferred_amount | formatMoney}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.received_amount | formatMoney}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.transfer_date}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.creator_name}}
                                                </div>
                                            </td>
                                            <td>
                                                <div :class="`hoverable label-transfer status${tuition_transfer.status}`" @click="viewDetail(tuition_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{tuition_transfer.status | tuitionTransferStatus}}
                                                </div>
                                            </td>
                                            <td>
                                                <span   class="apax-btn edit" @click="confirm(tuition_transfer)" v-if="(role == 686868 || role == 676767 || role == 7777777) && tuition_transfer.status  == 1 && session.user.branch_id.split(',').map(Number).indexOf(tuition_transfer.from_branch_id) !=-1">
                                                    <i v-b-tooltip.hover title="Nhấp vào để phê duyệt" class="fa fa-check"></i>
                                                </span>
                                                <span   class="apax-btn edit" @click="confirm(tuition_transfer)" v-if="(role == 84) && tuition_transfer.status  == 4">
                                                    <i v-b-tooltip.hover title="Nhấp vào để phê duyệt" class="fa fa-check"></i>
                                                </span>
                                                <span   class="apax-btn edit" @click="confirm(tuition_transfer)" v-if="role == 999999999 && (tuition_transfer.status == 1 || tuition_transfer.status == 4)">
                                                    <i v-b-tooltip.hover title="Nhấp vào để phê duyệt" class="fa fa-check"></i>
                                                </span>                                            
                                                <span :class="display" class="apax-btn remove" @click="confirm(tuition_transfer, false)" v-if="(role == 686868 || role == 676767 || role == 7777777) && tuition_transfer.status  == 1 && session.user.branch_id.split(',').map(Number).indexOf(tuition_transfer.from_branch_id) !=-1" >
                                                    <i v-b-tooltip.hover title="Nhấp vào để từ chối" class="fa fa-close"></i>
                                                </span>
                                                <span :class="display" class="apax-btn remove" @click="confirm(tuition_transfer, false)" v-if="(role == 84) && tuition_transfer.status  == 4 ">
                                                    <i v-b-tooltip.hover title="Nhấp vào để từ chối" class="fa fa-close"></i>
                                                </span>
                                                <span :class="display" class="apax-btn remove" @click="confirm(tuition_transfer, false)" v-if="role == 999999999 && (tuition_transfer.status == 1 || tuition_transfer.status == 4)">
                                                    <i v-b-tooltip.hover title="Nhấp vào để từ chối" class="fa fa-close"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="apax-btn print">
                                                  <i v-b-tooltip.hover title="Nhấp vào để in bản ghi" @click="callPrintForm(tuition_transfer)" class="fa fa-print"></i>
                                                </span>                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    <nav aria-label="Page navigation">
                                        <paging
                                        :rootLink="html.pagination.url"
                                        :id="html.pagination.id"
                                        :listStyle="html.pagination.style"
                                        :customClass="html.pagination.class"
                                        :firstPage="html.pagination.spage"
                                        :previousPage="html.pagination.ppage"
                                        :nextPage="html.pagination.npage"
                                        :lastPage="html.pagination.lpage"
                                        :currentPage="html.pagination.cpage"
                                        :pagesItems="html.pagination.total"
                                        :pagesLimit="html.pagination.limit"
                                        :pageList="html.pagination.pages"
                                        :routing="redirect">
                                        </paging>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
            </div>
        </div>
        <b-modal
            size="xl" 
            id="extra-frame" 
            :title="html.dom.detail.title"
            class="modal-primary modal-detail"
            v-model="html.dom.modal"
            >
            <div class="tuition-transfer-frame modal-success ada-modal" >
                <footer class="modal-footer">

                        <textarea v-if="((role == 84) && va_status == 4 ) || ((role == 686868 || role == 676767) && va_status == 1 && session.user.branch_id.split(',').map(Number).indexOf(va_from_branch_id) !=-1) || (role == 999999999 && (va_status == 1 || va_status == 4))" class="float-left transfer-note" v-model="html.dom.detail.note" cols="35" rows="2" />

                        <span style="width: 190px; height: 32px;" v-if="(role == 84) && va_status == 4">
                                <b-button v-if="html.dom.detail.action" @click="deny" variant="primary" size="sm" class="float-right">
                                    <i class="fa fa-close" /> Từ Chối
                                </b-button>

                                <b-button v-if="html.dom.detail.action" @click="accept" size="sm" class="float-right" variant="success">
                                    <i class="fa fa-check" /> Phê Duyệt
                                </b-button>

                                <b-button v-else @click="close" size="sm" class="float-right" variant="primary">
                                    <i class="fa fa-ban" /> Đóng
                                </b-button>
                        </span>

                        <span style="width: 190px; height: 32px;" v-if="(role == 686868 || role == 676767 || role == 7777777) && va_status == 1 && session.user.branch_id.split(',').map(Number).indexOf(va_from_branch_id) !=-1">
                            <b-button v-if="html.dom.detail.action" @click="deny" variant="primary" size="sm" class="float-right">
                                <i class="fa fa-close" /> Từ Chối
                            </b-button>
                            <b-button v-if="html.dom.detail.action" @click="accept" size="sm" class="float-right" variant="success">
                                <i class="fa fa-check" /> Phê Duyệt
                            </b-button>
                            <b-button v-else @click="close" size="sm" class="float-right" variant="primary">
                                <i class="fa fa-ban" /> Đóng
                            </b-button>
                        </span>
                        <span style="width: 190px; height: 32px;" v-if="role == 999999999 && (va_status == 1 || va_status == 4)">
                            <b-button @click="deny" variant="primary" size="sm" class="float-right">
                                <i class="fa fa-close" /> Từ Chối
                            </b-button>
                            <b-button @click="accept" size="sm" class="float-right" variant="success">
                                <i class="fa fa-check" /> Phê Duyệt
                            </b-button>                                                    
                        </span>
                    </footer> 
                <div class="modal-body">
                    <div v-html="html.dom.detail.content"></div>    
                </div>
            </div>
        </b-modal>

        <modal name="detail" width="900px" height="700px" style="top: 40px;left: 60px; overflow-y: scroll;">
            
        </modal>
    </div>
</template>

<script>
    import u from '../../../utilities/utility'
    import searchBranch from '../../../components/Selection'
    import paging from '../../../components/Pagination'
    import search from '../../../components/Search'
    import loader from '../../../components/Loading'

    export default {
        components: {
            searchBranch,
            paging,
            search,
            loader
        },
        name: 'tuition-transfers-list',
        data() {
            const model = u.m('tuition-transfers').list
            model.spin = 'mini'
            model.duration = 500
            model.text = 'Đang tải dữ liệu...'
            model.processing = false
            model.html.dom = {
                modal: false,
                title: 'Danh sách chuyển phí',               
                filter: {
                    branch: {
                        options: [],
                        display: 'display',
                        disabled: false,
                        placeholder: 'Vui lòng chọn 1 trung tâm để giới hạn phạm vi tìm kiếm'
                    },
                    search: {
                        label: 'Tìm kiếm theo mã CRM hoặc tên học sinh',
                        disabled: true,
                        placeholder: 'Từ khóa tìm kiếm'
                    },
                    tuition_transfer: {
                        data: null,
                        types: [
                            { id: '', label: 'Lọc theo trạng thái' },
                            { id: '0', label: 'Đã bị từ chối' },
                            { id: '1', label: 'Đang chờ duyệt' },
                            { id: '2', label: 'Kế toán đã từ chối' },
                            { id: '3', label: 'Giám đốc đã từ chối' },
                            { id: '4', label: 'Chờ GĐTT phê duyệt' },
                            { id: '5', label: 'Giám đốc duyệt' },
                            { id: '6', label: 'Đã được duyệt thành công' },
                        ],
                        disabled: true,
                        action: () => this.selectTuitionTransfer(),
                        placeholder: 'Chọn trạng thái duyệt'
                    }
                },
                detail: {
                    title: '',
                    content: '',
                    note: '',
                    action: this.checkAction(),
                    add: this.canAdd()
                },
                data: {

                }
            }
            model.html.data = {
                filter: {
                    branch: '',
                    keyword: '',
                    customer_type: '',
                    program: '',
                    tuition_fee: '',
                    type_contract:''
                },
                cached: null,
                tuition_transfers: [],
            }
            model.html.order.by = 't.id'
            model.cache.filter = model.html.data.filter
            model.role = u.session().user.role_id
            model.display = ''
            model.va_status = '',
            model.va_from_branch_id = '',
            model.html.pagination = {
                spage: 0,
                ppage: 0,
                npage: 0,
                lpage: 0,
                cpage: 1,
                total: 0,
                limit: 20,
                pages: []
            },
            model.session= u.session()
            model.filter_branch = true
            return model
        },
        created() {
            this.start();
            if(u.session().user.branches.length==1){
                this.filter_branch=false
            }
        },
        methods: {
            callPrintForm(callPrintForm) {
                window.open(`/print/tuition-transfer/${callPrintForm.id}`, '_blank');
            },
            canAdd() {
                return [55,56,686868,676767,'999999999'].indexOf(u.session().user.role_id)!=-1
            },
            checkAction() {
                return [56,686868,84,676767,'999999999'].indexOf(u.session().user.role_id)!=-1
            },
            close() {
                this.html.dom.modal = false
                document.getElementsByTagName("main")[0].setAttribute("style", "z-index:0");
            },
            showAddNoteContract (contract_opject) {            
                this.full_name = contract_opject.student_name
                this.diemdauvao = contract_opject.score
                this.note = contract_opject.note 
                this.all_data = contract_opject
                this.addNoteContract = true; 
            },
            saveNote (contract_opject) {
                const confirmation = confirm("Bạn có chắc là muốn cập nhật học sinh này không?")
                let data = {}
                if (confirmation) {
                    this.addNoteContract = false
                    this.processing = true
                    data = contract_opject
                    data.note = this.note
                    u.p('/api/contracts/update/note',data)
                    .then(response => {
                    //console.log(response)
                    this.processing = false          
                    u.go(this.$router, `/contracts`)
                    }).catch(e => u.log('Exeption', e))
                }
            },        
            checkPermission(){
                let role = parseInt(u.session().user.role_id);
                
            },            
            selectTuitionTransfer() {

                // gán lại khi tìm kiếm theo cái này
                this.html.pagination.spage = 0
                this.html.pagination.ppage = 0
                this.html.pagination.npage = 0
                this.html.pagination.lpage = 0
                this.html.pagination.cpage = 1
                this.html.pagination.total = 0
                this.html.pagination.limit = 20

                this.cache.filter.tuition_transfer = this.html.data.filter.tuition_fee
                this.get(this.link(), this.load)
            },
            link(link = '') {
                const url = link || this.html.page.url.list
                this.html.data.filter.branch = this.cache.filter.branch
                this.html.data.filter.keyword = this.html.data.filter.keyword.trim()
                this.html.data.filter.keyword = this.html.data.filter.keyword.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi, '')
                const sort = u.jss(this.html.order)
                const search = u.jss(this.html.data.filter)
                const pagination = u.jss({
                    spage: this.html.pagination.spage,
                    ppage: this.html.pagination.ppage,
                    npage: this.html.pagination.npage,
                    lpage: this.html.pagination.lpage,
                    cpage: this.html.pagination.cpage,
                    total: this.html.pagination.total,
                    limit: this.html.pagination.limit
                })
                return `${url}${pagination}/${search}/${sort}`
            },
            filterData(){
                this.get(this.link(), this.load)
            },
            get(link, callback) {
                this.processing = true
                u.g(link)
                .then(response => {
                    const data = response
                    callback(data)
                    setTimeout(() => {
                    this.processing = false
                    }, data.duration)
                }).catch(e => u.log('Exeption', e))
            },
            pdt(dt) {
                if (dt.length) {
                    dt.map(itm => {
                        itm.received_data = JSON.parse(itm.received_data)
                        itm.transferred_data = JSON.parse(itm.transferred_data)
                        return itm
                    })
                    return dt
                }
            },
            start() {
                this.processing = true
                this.checkPermission()  
                // if (u.authorized()) {
                //     this.html.dom.filter.branch.display = 'display'
                //     this.html.dom.filter.branch.disabled = false
                // } else {
                    this.html.data.filter.branch = this.session.user.branch_id
                    this.cache.filter.branch = this.session.user.branch_id
                    u.g(this.link())
                    .then(data => {
                        this.load(data)
                        this.html.dom.filter.search.disabled = false
                        this.html.dom.filter.tuition_transfer.disabled = false
                    }).catch(e => console.log(e))
                // }
                this.searching()
            },
            load(data) {
                this.cache.data = data
                this.list = data.list
                this.html.pagination = data.pagination
                this.processing = false
            },            
            confirm(info, approve = true) {
                var text_alert=""
                if(approve){
                    text_alert = "Bạn có chắc là muốn phê duyệt chuyển phí cho học sinh này?"
                }else{
                    text_alert = "Bạn có chắc là muốn từ chối phê duyệt chuyển phí cho học sinh này?"
                }
                const confirmation = confirm(text_alert)
                if (confirmation)
                {
                    this.html.dom.modal = false
                    this.processing = true
                    const note = this.html.dom.detail.note
                    if(info.status==4){
                        info.accounting_note = note
                    }else{
                        info.ceo_branch_note = note
                    }
                    info.approve_by = parseInt(this.session.user.role_id, 10) === 84 ? 'accounting' : 'ceo'
                    info.approve_status = approve
                    info.note = this.html.dom.detail.note
                    
                    const that = this
                    u.p('/api/tuition-transfers/approve/store', info)
                    .then(response => {
                        this.processing = false
                        alert(response)
                        this.start()  
                    }).catch(e => console.log(e))
                }
                
            },
            accept() {
                this.confirm(this.data.cached)
            },
            deny() {
                this.confirm(this.data.cached, false)
            },            
            viewDetail(tuition_transfer) {
                this.html.dom.detail.note =""
                this.data.cached = tuition_transfer
                this.va_status = this.data.cached.status
                this.va_from_branch_id = this.data.cached.from_branch_id
                const status = this.tranStatus(tuition_transfer.status)
                var array_code_send = []
                var array_transfer = []
                var array_receive = []                
                const from = tuition_transfer.transferred_data
                const receive = tuition_transfer.received_data
                let transfer_contracts_data = ''
                let receives_contracts_data = ''
                const other_detail_data = 
                `<div class="col-md-12">
                    <div class="row" >
                        <div class="col-md-6">
                            <b>Trạng thái: </b>${status}
                        </div>
                        <div class="col-md-6">
                            <b>Ghi chú chuyển phí: </b><i>${tuition_transfer.note}</i>
                        </div>
                    </div>

                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <b>Ngày chuyển phí: </b>${tuition_transfer.transfer_date}
                        </div>
                        <div class="col-md-6">
                            <b>Người tạo: ${tuition_transfer.creator_name}</b>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <b>${tuition_transfer.status==2?`GĐTT từ chối duyệt`:`GĐTT duyệt`}: </b><i>${u.changeNull(tuition_transfer.ceo_name)}</i>
                        </div>
                        <div class="col-md-6">
                            <b>${tuition_transfer.status==3?`Kế toán HO từ chối duyệt`:`Kế toán HO duyệt`}: </b> <i> ${u.changeNull(tuition_transfer.accounting_name)}</i>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <b>${tuition_transfer.status==3?`Ngày GĐTT từ chối duyệt`:`Ngày GĐTT duyệt`}: </b><i>${u.changeNull(tuition_transfer.ceo_approved_at)}</i>
                        </div>
                        <div class="col-md-6">
                            <b>${tuition_transfer.status==3?`Ngày kế toán HO từ chối duyệt`:`Ngày Kế toán HO duyệt`}: </b> <i> ${u.changeNull(tuition_transfer.accounting_approved_at)}</i>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <b>Ghi chú GĐTT duyệt: </b><i>${tuition_transfer.log_ceo.message}</i>
                        </div>
                        <div class="col-md-6">
                            <b>Ghi chú kế toán HO duyệt: </b> <i>${tuition_transfer.log_kt.message}</i>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <b>File đính kèm: ${tuition_transfer.attached_file ? `<a href="/${tuition_transfer.attached_file}">Tải xuống</a></a>` : '<i>Không có file đính kèm nào.</i>'}
                </div>`
                from.forEach((data, index) => {
                    const class_info = data.class_name ? `<div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">Lớp học</div>
                                            <div class="info line col-md-8">${data.class_name}</div>
                                        </div>
                                    </div>` : ''
                    transfer_contracts_data += 
                    `<div class="transfering contract item col-md-12">
                        <div class="row">
                            <div class="info line col-md-12">
                                <h5>${(data.order || index+1)} . Hợp đồng số (${data.accounting_id}) - ${data.tuition_fee_name}</h5>
                            </div>
                            <div class="info line col-md-12">
                                <div class="row detail-info">
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Thời gian: 
                                            </div>
                                            <div class="info line col-md-8">
                                                Từ ${data.start_date} tới ${data.end_date}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Đã đóng:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${u.f(data.total_charged)} (${data.real_sessions} buổi)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Đã học:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${u.f(data.done_amount)} (${data.done_sessions} buổi)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Học bổng:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${data.bonus_sessions} buổi
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Đã học học bổng:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${data.done_bonus_sessions} buổi
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12" >
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Còn lại:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${u.f(data.left_amount)} (${data.left_sessions} buổi)
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
                                    ${class_info}
                                </div>
                            </div>
                        </div>
                    </div>`
                })
                receive.forEach((data, index) => {
                    const class_info = data.class_name ? `<div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">Lớp học</div>
                                            <div class="info line col-md-8">

                                            </div>
                                        </div>
                                    </div>` : ''
                    receives_contracts_data += `<div class="transfering contract item col-md-12">
                        <div class="row">
                            <div class="info line col-md-12">
                                <h5>${(data.order || index+1)} . Hợp đồng số (${data.accounting_id?data.accounting_id:'-----------'}) - ${data.tuition_fee_name}</h5>
                            </div>
                            <div class="info line col-md-12">
                                <div class="row detail-info">
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Thời gian: 
                                            </div>
                                            <div class="info line col-md-8">
                                                Từ ${data.start_date} tới ${data.end_date}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12"  style="${tuition_transfer.sibling==1?'display:none':''}">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Phải đóng:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${u.f(data.must_charge)}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Đã đóng:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${u.f(data.total_charged)} <span style="${tuition_transfer.sibling==0?'display:none':''}">(${data.real_sessions} buổi)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                ${tuition_transfer.sibling==1?'Còn lại' :'Còn phải đóng'}:
                                            </div>
                                            <div class="info line col-md-8" style="${tuition_transfer.sibling==0?'display:none':''}">
                                                ${u.f(data.left_amount)} (${data.left_sessions} buổi)
                                            </div>
                                            <div class="info line col-md-8" style="${tuition_transfer.sibling==1?'display:none':''}">
                                                ${u.f(data.must_charge-data.total_charged)} 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info line col-md-12" style="${tuition_transfer.sibling==0?'display:none':''}">
                                        <div class="row">
                                            <div class="info line col-md-4">
                                                Học bổng:
                                            </div>
                                            <div class="info line col-md-8">
                                                ${data.bonus_sessions} buổi
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
                                    ${class_info}
                                </div>
                            </div>
                        </div>
                    </div>`
                })
                const tuition_transfer_detail = `<div class="tuition-transfer-information">
                    <div class="col-lg-12">
                        <div class="panel">
                            <div class="panel-body" >
                                <div class="row">
                                    <div class="col-sm-12 transfer-information">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h5>
                                                    Thông Tin Chuyển Phí
                                                </h5>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Trung tâm:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.from_branch_name}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Người chuyển:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.from_student_name}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã CRM:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.from_student_crm}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã Kế toán:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.from_student_act}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-amount">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số tiền chuyển:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.f(tuition_transfer.transferred_amount)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session" style="${tuition_transfer.sibling==0?'display:none':''}">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số buổi chuyển:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.transferred_sessions}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session" style="${tuition_transfer.sibling==1?'display:none':''}">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số tiền truy thu:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.f(tuition_transfer.amount_truythu)}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <h5>
                                                    Thông Tin Nhận Phí
                                                </h5>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Trung tâm:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.to_branch_name}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Người nhận:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.to_student_name}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã CRM:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.to_student_crm}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã kế toán:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.changeNull(tuition_transfer.to_student_act)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-amount">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số tiền nhận:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.f(tuition_transfer.received_amount)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session" style="${tuition_transfer.sibling==0?'display:none':''}">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số buổi nhận:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${tuition_transfer.received_sessions}
                                                        </div>
                                                    </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 title-content">
                                        <div class="row">
                                            <div class="col-sm-6">                                                
                                                <h6>Các Gói Phí Chuyển</h6>
                                            </div> 
                                            <div class="col-sm-6">
                                                <h6>Các Gói Phí Nhận</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 scrollable-content">
                                        <div class="row">
                                            <div class="col-sm-6">                                                
                                                ${transfer_contracts_data}
                                            </div> 
                                            <div class="col-sm-6">
                                                ${receives_contracts_data}
                                            </div>
                                        </div>
                                        <div class="col-sm-12 other-detail"  style = "height: 170px;"">
                                            ${other_detail_data}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
                this.html.dom.detail.title = `Chi tiết bản ghi chuyển phí ngày ${tuition_transfer.transfer_date} được tạo bởi: ${tuition_transfer.creator_name}`
                this.html.dom.detail.content = tuition_transfer_detail
                this.html.dom.modal = true
                document.getElementsByTagName("main")[0].setAttribute("style", "z-index:1020");
            },
            searching(word) {
                // gán lại khi tìm kiếm theo cái này
                this.html.pagination.spage = 0
                this.html.pagination.ppage = 0
                this.html.pagination.npage = 0
                this.html.pagination.lpage = 0
                this.html.pagination.cpage = 1
                this.html.pagination.total = 0
                this.html.pagination.limit = 20
                const key = u.live(word) && word != '' ? word.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi, '') : '';
                this.cache.filter.keyword = key               
                this.get(this.link(), this.load)
            },
            binding(e) {
                if (e.key == "Enter") {
                    this.searching()
                }
            },
            redirect(link) {
                const info = link.toString().split('/')
                const page = info.length > 1 ? info[1] : 1
                this.html.pagination.cpage = parseInt(page)
                this.get(this.link(), this.load)
            },            
            selectBranch(data) {
                if (data) {
                    this.html.pagination.spage = 0
                    this.html.pagination.ppage = 0
                    this.html.pagination.npage = 0
                    this.html.pagination.lpage = 0
                    this.html.pagination.cpage = 1
                    this.html.pagination.total = 0
                    this.html.pagination.limit = 20
                    this.cache.filter.branch = data.id
                    this.html.data.filter.branch = data.id
                    this.get(this.link(), this.load)
                    this.html.dom.filter.search.disabled = false
                    this.html.dom.filter.tuition_transfer.disabled = false
                }
            },
            tranStatus(status) {
                let resp = ''
                switch (status) {
                    case 1:
                    resp = 'Chờ giám đốc TT duyệt'
                    break
                    case 2:
                    resp = 'Giám đốc TT đã từ chối'
                    break
                    case 3:
                    resp = 'Kế toán HO đã từ chối'
                    break
                    case 4:
                    resp = 'Chờ Kế toán HO phê duyệt'
                    break
                    case 5:
                    resp = 'Kế toán HO đã duyệt'
                    break
                    case 6:
                    resp = 'Đã được duyệt thành công'
                    break
                    default:
                    resp = 'Đã bị hủy'
                    break
                }
                return resp
            },
            removeFilter() {
                location.reload();
            }
        }
    }
</script>

<style scoped language="scss">
    .hoverable {
        cursor: pointer;
    }
</style>
