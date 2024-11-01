<template>
    <div class="extralarge-modal animated fadeIn apax-form">
        <div class="row">
            <div class="col-12">
                <b-card header>                    
                    <div slot="header">
                        <i class="fa fa-filter"></i> <b class="uppercase">Bộ Lọc.</b>
                    </div>
                    <loader :active="processing" :spin="spin" :text="text" :duration="duration" />
                    <div id="filter_content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-4" v-if="filter_branch" >
                                                <div class="form-group">
                                                    <label class="filter-label control-label">Trung tâm</label><br/>
                                                    <searchBranch
                                                        :onSelect="selectBranch"
                                                        :disabled="false"
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
                                                    <select type="text" class="form-control" v-model="html.data.filter.status">
                                                        <option value="">Chọn trạng thái</option>
                                                        <option value="1">Chờ duyệt đi</option>
                                                        <option value="2">Đã từ chối duyệt đi</option>
                                                        <option value="3">Đã từ chối duyệt đến</option>
                                                        <option value="4">Chờ duyệt đến</option>
                                                        <option value="5">Chờ kế toán HO phê duyệt</option>
                                                        <option value="6">Đã được phê duyệt</option>
                                                        <option value="7">Kế toán HO từ chối phê duyệt</option>
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
                        <router-link to="/branch-transfers/add-branch-transfer" v-if="html.dom.detail.add">
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
                                            <th width="2%">STT</th>
                                            <th width="4%">Mã CRM</th>
                                            <th width="4%">Mã CMS</th>
                                            <th width="4%">Mã Cyber</th>
                                            <th width="8%">Tên Học Sinh</th>                                            
                                            <th width="12%">Trung Tâm Chuyển</th>                                            
                                            <th width="4%">Sản Phẩm</th>
                                            <th width="12%">Gói Phí</th>
                                            <th width="12%">Trung Tâm Nhận</th>
                                            <th width="4%">Sản Phẩm</th>                                            
                                            <th width="4%">Ngày Chuyển</th>
                                            <th width="6%">Trạng Thái</th>
                                            <th width="26%">Lý do</th> 
                                            <th width="4%">In</th>                                        
                                            <th v-if="html.dom.detail.action">Từ Chối</th>
                                            <th v-if="html.dom.detail.action">Phê Duyệt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(branch_transfer, index) in list" :key="index" :class="`ttitm${branch_transfer.status}`">
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{(html.pagination.ppage * html.pagination.limit)+index+1}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.student_crm}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.cms_id}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.accounting_id}}
                                                </div>
                                            </td>                                            
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.student_name}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.from_branch}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.from_product}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.from_tuition_fee}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.to_branch}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.to_product}}
                                                </div>
                                            </td>                                            
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.transfer_date}}
                                                </div>
                                            </td>
                                            <td>
                                                <div :class="`hoverable label-transfer status${branch_transfer.status}`" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.status | branchTransferStatus}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="hoverable" @click="viewDetail(branch_transfer)" v-b-tooltip.hover title="Nhấp vào để xem chi tiết">
                                                    {{branch_transfer.note}}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="apax-btn print">
                                                  <i v-b-tooltip.hover title="Nhấp vào để in bản ghi" @click="callPrintForm(branch_transfer)" class="fa fa-print"></i>
                                                </span>
                                            </td>  
                                            <td v-if="html.dom.detail.action">
                                                <span class="apax-btn remove" @click="checkConfirm(branch_transfer)" v-if="checkViewAction(branch_transfer)">
                                                    <i v-b-tooltip.hover title="Nhấp vào để từ chối" class="fa fa-close"></i>
                                                </span>
                                            </td>
                                            <td v-if="html.dom.detail.action">
                                                <span class="apax-btn edit" @click="checkConfirm(branch_transfer, true)" v-if="checkViewAction(branch_transfer)">
                                                    <i v-b-tooltip.hover title="Nhấp vào để phê duyệt" class="fa fa-check"></i>
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
            <b-modal
                size="xl" 
                id="extra-frame" 
                :title="html.dom.detail.title"
                class="modal-primary modal-detail"
                v-model="html.dom.modal"
                hide-footer="true"
                >
                <div class="branch-transfer-frame modal-success ada-modal">
                    <div class="modal-body" style="overflow: auto;max-height: 556px;">
                        <div v-html="html.dom.detail.content" />  
                        <div v-if="attached_file">
                            <div class="form-group">
                                <a target="_blank" type="button" class="btn btn-primary btn-upload" :href="attached_file | genDownloadUrl" download><i class="fa fa-download"></i>&nbsp; Tải về file đính kèm</a>
                            </div>
                        </div>                                              
                    </div>
                    <footer class="modal-footer">
                        <textarea v-if="checkViewAction(branch_transfer)" class="float-left transfer-note" v-model="html.dom.detail.note" cols="39" rows="2" />
                        <b-button v-if="checkViewAction(branch_transfer)" @click="deny" variant="primary" size="sm" class="float-right">
                            <i class="fa fa-close" /> Từ Chối
                        </b-button>
                        <b-button v-if="checkViewAction(branch_transfer)" @click="accept" size="sm" class="float-right" variant="success">
                            <i class="fa fa-check" /> Phê Duyệt
                        </b-button>
                        <b-button v-else @click="close" size="sm" class="float-right" variant="primary">
                            <i class="fa fa-ban" /> Đóng
                        </b-button>
                    </footer>
                </div>
            </b-modal>
        </div>
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
        name: 'branch-transfers-list',
        data() {
            const model = u.m('branch-transfers').list
            model.spin = 'mini'
            model.duration = 500
            model.text = 'Đang tải dữ liệu...'
            model.processing = false
            model.html.dom = {
                modal: false,
                title: 'Danh sách chuyển trung tâm',               
                filter: {
                    branch: {
                        options: [],
                        display: 'hidden',
                        disabled: true,
                        placeholder: 'Vui lòng chọn 1 trung tâm để giới hạn phạm vi tìm kiếm'
                    },
                    search: {
                        label: 'Tìm kiếm theo mã CRM hoặc tên học sinh',
                        disabled: true,
                        placeholder: 'Từ khóa tìm kiếm'
                    },
                    branch_transfer: {
                        data: null,
                        types: [{ id: '', label: 'Lọc theo trạng thái' },
                            { id: '1', label: 'Đang chờ duyệt' },
                            { id: '2', label: 'Chờ GĐTT phê duyệt' },
                            { id: '3', label: 'Giám đốc đã duyệt' },
                            { id: '4', label: 'Kế toán đã từ chối' },
                            { id: '5', label: 'Giám đốc đã từ chối' },
                            { id: '6', label: 'Đã được phê duyệt' },
                            { id: '7', label: 'Đã bị từ chối' }],
                        disabled: true,
                        action: () => this.selectBranchTransfer(),
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
                    status: '',
                },
                cached: null,
                branch_transfers: [],
            }
            model.html.order.by = 't.id'
            model.cache.filter = model.html.data.filter
            model.branch_transfer={}
            model.attached_file=''
            model.session= u.session()
            model.filter_branch=true
            return model
        },
        created() {
            this.start()
            if(u.session().user.branches.length==1){
                this.filter_branch=false
            }
        },
        methods: {
            callPrintForm(callPrintForm) {
                window.open(`/print/branch-transfer/${callPrintForm.id}`, '_blank');
            },
            checkViewAction(branch_transfer){
                if(((branch_transfer.status==1 && JSON.parse("[" + u.session().user.branch_id + "]").indexOf(branch_transfer.from_branch_id)>-1 && [55,56,58,676767,686868].indexOf(this.session.user.role_id)!=-1 )
                || (branch_transfer.status==4 && JSON.parse("[" + u.session().user.branch_id + "]").indexOf(branch_transfer.to_branch_id)>-1 && [55,56,58,676767,686868].indexOf(this.session.user.role_id)!=-1 )
                || ((branch_transfer.status==4 || branch_transfer.status==1 ||branch_transfer.status==5) && u.session().user.role_id==999999999)
                || (branch_transfer.status==5 && u.session().user.role_id==84))
                && [56,58,84,686868,676767,'999999999'].indexOf(u.session().user.role_id)> -1){
                    return true
                }else {
                    return false
                }
            },
            removeFilter(){
                location.reload()
            },
            canAdd() {
                return [55,56,'999999999'].indexOf(u.session().user.role_id)!=-1
            },
            checkAction() {  
                return [56,58,84,686868,676767,'999999999'].indexOf(u.session().user.role_id)!=-1
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
            filterData(){
                this.get(this.link(), this.load)
            },       
            selectBranchTransfer() {
                this.cache.filter.customer_type = this.html.data.filter.customer_type
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
                if (u.authorized()) {
                    this.html.dom.filter.branch.display = 'display'
                    this.html.dom.filter.branch.disabled = false
                } else {
                    this.html.data.filter.branch = this.session.user.branch_id
                    this.cache.filter.branch = this.session.user.branch_id
                    u.g(this.link())
                    .then(data => {
                        this.load(data)
                        this.html.dom.filter.search.disabled = false
                        this.html.dom.filter.branch_transfer.disabled = false
                    }).catch(e => console.log(e))
                }
                this.searching()
            },
            load(data) {
                this.cache.data = data
                this.list = data.list
                this.html.pagination = data.pagination
                this.processing = false
            },            
            confirm(info, deny = false) {
                this.html.dom.modal = false
                this.processing = true
                let rule = false
                const note = this.html.dom.detail.note                
                if ( parseInt(info.status, 10) === 1 && (u.session().user.role_id==999999999 || 
                ( [55,56,58,676767,686868].indexOf(this.session.user.role_id)!=-1 && this.session.user.branch_id.split(',').indexOf(info.from_branch_id.toString()) > -1))) {
                    rule = true
                    info.original_approver_note = note
                    info.destination_approver_note = '';
                    info.approve_by = 'from_approver'
                }else if (parseInt(info.status, 10) === 4 && (u.session().user.role_id==999999999 ||
                ( [55,56,58,676767,686868].indexOf(this.session.user.role_id)!=-1 && this.session.user.branch_id.split(',').indexOf(info.to_branch_id.toString()) > -1))) {
                    rule = true
                    info.original_approver_note = note
                    info.destination_approver_note = '';
                    info.approve_by = 'to_approver'
                } else if (parseInt(info.status, 10) === 5 && (u.session().user.role_id==999999999 || [84].indexOf(this.session.user.role_id)!=-1 )) {
                    rule = true
                    info.original_approver_note = note
                    info.destination_approver_note = '';
                    info.approve_by = 'accounting_approver'
                }
                if (rule) {
                    info.approve_status = deny
                    const that = this
                    u.p('api/branch-transfers/approve/store', info)
                    .then(response => {
                        this.processing = false
                        u.apax.$emit('apaxPopup', {
                            on: true,
                            content: response,
                            title: 'Thông Báo',
                            class: 'modal-success',
                            size: 'md',
                            confirm: {
                                success: {
                                    button: 'OK',
                                    action() {
                                        
                                    }
                                }
                            },
                            variant: 'success'
                        })
                        that.start()
                    }).catch(e => console.log(e))
                } else {
                    this.processing = false
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Thông Báo!',
                        type: 'warning',
                        duration: 5000,
                        text: 'Tài khoản của bạn không phải là OM hay Giám Đốc trung tâm nên không có quyền phê duyệt chuyển trung tâm.'
                    })
                }
            },
            checkConfirm(info, deny = false){
                if(deny){
                    if(info.check_approve==0){
                        alert(info.check_approve_mess)
                    }else{
                        this.confirm(info, true)
                    } 
                }else{
                    var r = confirm("Bạn chắc chắn muốn từ chối phê duyệt chuyển trung tâm này");
                    if (r == true) {
                        this.confirm(info)
                    }
                }
            },
            accept() {
                if(this.data.cached.check_approve==0){
                    alert(this.data.cached.check_approve_mess)
                }else{
                    this.confirm(this.data.cached, true)
                }
            },
            deny() {
                var r = confirm("Bạn chắc chắn muốn từ chối phê duyệt chuyển trung tâm này");
                if (r == true) {
                     this.confirm(this.data.cached)
                } 
            },            
            viewDetail(branch_transfer) {
                this.attached_file = branch_transfer.attached_file
                this.branch_transfer = branch_transfer
                this.data.cached = branch_transfer
                const status = this.tranStatus(branch_transfer.status)
                const from = branch_transfer.orgin_contracts
                const receive = branch_transfer.transfer_contracts
                let transfer_contracts_data = ''
                let receives_contracts_data = ''
                this.html.dom.detail.note=''
                const other_detail_data = `
                <div class="row" style="margin: 0px;">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Trạng thái:</b>
                                    </div>
                                    <div class="col-md-7">
                                        ${status}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Ngày chuyển trung tâm:</b>
                                    </div>
                                    <div class="col-md-7">
                                        ${branch_transfer.transfer_date}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Người tạo:</b>
                                    </div>
                                    <div class="col-md-7">
                                        ${branch_transfer.creator_name}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Ngày tạo:</b>
                                    </div>
                                    <div class="col-md-7">
                                        ${branch_transfer.created_at}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Lý do chuyển trung tâm:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${branch_transfer.note}</i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Ghi chú của trung tâm chuyển:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${branch_transfer.from_approve_comment || ''}</i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>Ghi chú của trung tâm nhận:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${branch_transfer.to_approve_comment || ''}</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>${branch_transfer.status==2?`Người từ chối duyệt đi`:`Người duyệt đi`}:</b>
                                    </div>
                                    <div class="col-md-7">
                                        ${u.changeNull(branch_transfer.from_om_name)}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>${branch_transfer.status==2?`Ngày từ chối duyệt đi`:`Ngày duyệt đi`}:</b>
                                    </div>
                                    <div class="col-md-7">
                                        ${u.changeNull(branch_transfer.from_approved_at)}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>${branch_transfer.status==3?`Người từ chối duyệt đến`:`Người duyệt đến`}:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${u.changeNull(branch_transfer.to_om_name)}</i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>${branch_transfer.status==3?`Ngày từ chối duyệt đến`:`Ngày duyệt đến`}:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${u.changeNull(branch_transfer.to_approved_at)}</i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>${branch_transfer.status==3?`Kế toán HO từ chối duyệt`:`Kế toán HO duyệt`}:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${u.changeNull(branch_transfer.accounting_name)}</i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <b>${branch_transfer.status==3?`Ngày kế toán HO từ chối duyệt`:`Ngày kế toán HO duyệt`}:</b>
                                    </div>
                                    <div class="col-md-7">
                                        <i>${u.changeNull(branch_transfer.accounting_approved_at)}</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`

                if (from) {
                    from.forEach(data => {
                        const class_info = data.class_name ? `<div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4" >Lớp học</div>
                                                <div class="info line col-md-8">${data.class_name}</div>
                                            </div>
                                        </div>` : ''
                        transfer_contracts_data += `<div class="transfering contract item col-md-12">
                            <div class="row">
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
                                                    ${u.f(data.total_charged-data.left_amount)} (${data.done_sessions} buổi)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4" style="color:red;font-weight:bold">
                                                    Còn lại:
                                                </div>
                                                <div class="info line col-md-8" style="color:red;font-weight:bold">
                                                    ${u.f(data.left_amount)} (${data.left_sessions} buổi)
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
                                                <div class="info line col-md-4" style="color:red;font-weight:bold">
                                                    Sản phẩm:
                                                </div>
                                                <div class="info line col-md-8" style="color:red;font-weight:bold">
                                                    ${u.changeNull(data.product_name)}
                                                </div>
                                            </div>
                                        </div>
                                        ${class_info}
                                    </div>
                                </div>
                            </div>
                        </div>`
                    })
                }
                
                if (receive) {
                    receive.forEach(data => {
                        receives_contracts_data += `<div class="transfering contract item col-md-12">
                            <div class="row">
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
                                                <div class="info line col-md-4" style="color:red;font-weight:bold">
                                                    Còn lại:
                                                </div>
                                                <div class="info line col-md-8" style="color:red;font-weight:bold">
                                                    ${u.f(data.left_amount)} (${data.left_sessions} buổi)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4">
                                                    Buổi học bổng:
                                                </div>
                                                <div class="info line col-md-8">
                                                    ${data.bonus_sessions} buổi
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info line col-md-12">
                                            <div class="row">
                                                <div class="info line col-md-4" style="color:red;font-weight:bold">
                                                    Sản phẩm:
                                                </div>
                                                <div class="info line col-md-8" style="color:red;font-weight:bold">
                                                    ${u.changeNull(data.product_name)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    })
                }

                const branch_transfer_detail = `<div class="tuition-transfer-information">
                    <div class="col-lg-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12 transfer-information" style="height: 230px;">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h5>
                                                    Thông Tin Trung Tâm Chuyển Đi
                                                </h5>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Trung Tâm:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.from_branch}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Tên Học Sinh:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.student_name}
                                                        </div>
                                                    </div>
                                                </div>  
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã CMR:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.changeNull(branch_transfer.student_crm)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã kế toán:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.changeNull(branch_transfer.student_act)}
                                                        </div>
                                                    </div>
                                                </div>                                                
                                                <div class="col-sm-12 transfer-amount">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số tiền chuyển:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.f(branch_transfer.transferred_amount)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số buổi chuyển:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.transferred_sessions}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Ngày chuyển: 
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.transfer_date}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <h5>
                                                    Thông Tin Trung Tâm Chuyển Đến
                                                </h5>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Trung Tâm:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.to_branch}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Tên Học Sinh:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.student_name}
                                                        </div>
                                                    </div>
                                                </div>  
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã CRM:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.changeNull(branch_transfer.student_crm)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-student">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Mã kế toán:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.changeNull(branch_transfer.student_act)}
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                                <div class="col-sm-12 transfer-amount">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số tiền nhận:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${u.f(branch_transfer.exchanged_amount)}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Số buổi nhận:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.exchanged_sessions}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 transfer-session">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            Kỳ học:
                                                        </div>
                                                        <div class="col-sm-8">
                                                            ${branch_transfer.information.semester_name ? branch_transfer.information.semester_name:''}
                                                        </div>
                                                    </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 title-content">
                                        <div class="row">
                                            <div class="col-sm-6">                                                
                                                <h6>Các Gói Phí Chuyển Đi</h6>
                                            </div> 
                                            <div class="col-sm-6">
                                                <h6>Các Gói Phí Chuyển Đến</h6>
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
                                        <div class="col-sm-12 other-detail">
                                            ${other_detail_data}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
                this.html.dom.detail.title = `Chi tiết bản ghi chuyển trung tâm ngày ${branch_transfer.transfer_date} được tạo bởi: ${branch_transfer.creator_name}`
                this.html.dom.detail.content = branch_transfer_detail
                this.html.dom.modal = true
                document.getElementsByTagName("main")[0].setAttribute("style", "z-index:1020");
            },
            searching(word) {
                const key = u.live(word) && word != '' ? word.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi, '') : '';
                this.cache.filter.keyword = key.trim();
                this.get(this.link(), this.load);
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
                    this.cache.filter.branch = data.id
                    this.html.data.filter.branch = data.id
                    this.get(this.link(), this.load)
                    u.g(`${this.html.page.url.load}${data.id}`)
                    .then(response => {
                        this.html.dom.filter.program.data = response.programs
                        this.html.dom.filter.tuition_fee.data = response.tuition_fees
                        this.html.data.filter.program = ''
                        this.html.data.filter.tuition_fee = ''
                        this.html.dom.filter.search.disabled = false
                        this.html.dom.filter.branch_transfer.disabled = false
                    }).catch(e => console.log(e))
                }
            },
            rollback(data){
                const delStdConf = confirm("Bạn có chắc rằng muốn rollback không?");
                if (delStdConf === true) {
                    u.g(`api/branch-transfers/rollback/${data.id}`).then(response => {
                       alert(response);
                       this.searching();
                    }).catch(e => console.log(e))
                }
            },
            tranStatus(status) {
                let resp = ''
                switch (status) {
                    case 1:
                    resp = 'Chờ duyệt đi'
                    break
                    case 2:
                    resp = 'Trung tâm chuyển đã từ chối'
                    break
                    case 3:
                    resp = 'Trung tâm nhận đã từ chối'
                    break
                    case 4:
                    resp = 'Chờ duyệt đến'
                    break
                    case 5:
                    resp = 'Chờ kế toán HO phê duyệt'
                    break
                    case 6:
                    resp = 'Đã được phê duyệt'
                    break
                    case 7:
                    resp = 'Kế toán HO từ chối phê duyệt'
                    break
                    default:
                    resp = 'Đã xóa'
                    break
                }
                return resp
            }
        }
    }
</script>

<style scoped language="scss">
    .hoverable {
        cursor: pointer;
    }
    .v--modal-overlay{
        position: absolute;
        width: 150%;
        height: 100%;
    }
    .v--modal-overlay .v--modal-box{
        overflow: auto;
    }
    .label-transfer.status7, .label-transfer.status9{
        background: #f1d24b;
        border: 1px solid #551717;
    }
    .label-transfer.status8, .label-transfer.status10{
        background: #ff0505;
        border: 1px solid #551717;
    }
</style>
