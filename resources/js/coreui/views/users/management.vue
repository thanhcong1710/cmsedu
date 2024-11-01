<template>
    <div class="animated fadeIn apax-form" id="users-management">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-address-book"></i> <strong>Danh sách quản lý</strong>
                    </div>
                    <div v-show="loading" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
                        </div>
                    </div>
                    <div class="controller-bar table-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" @change="searchUsers(1)" v-model="status">
                                        <option value="">Lọc trạng thái</option>
                                        <option value="1">Đang làm việc</option>
                                        <option value="-1">Đã nghỉ việc</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group user-upload-frame">
                                    <input type="file" class="input-file form-control hidden"
                                           id="fileUploadExcel"
                                           apcerpt=".xsl,.xsls"
                                           @change="fileChanged"
                                           data-multiple-caption="{count} files selected"/>
                                    <input class="form-control mask" :value="uploadFileName" @click="fireChangeUploadFile" readonly/>
                                    <button @click="uploadFile" class="btn btn-success user-upload-file"><i
                                            class="fa fa-upload" aria-hidden="true"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button class="btn btn-warning" @click="exportExcel"><i
                                            class="fa fa-file-excel-o"></i> Export
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-right" href="#" @click="downloadSampleExcel">
                                        <i class="fa fa-file-excel-o"></i> Download biểu mẫu import
                                    </a> /
                                    <a class="text-right" href="#" @click="downloadSampleExcelBlock">
                                        <i class="fa fa-file-excel-o"></i> Download biểu mẫu nghỉ việc
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button class="btn btn-success" @click="goToAdd"><i
                                            class="nav-icon fa fa-user-plus"></i> Thêm mới nhân viên
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr class="text-sm">
                                        <th width="2%">STT</th>
                                        <th width="86">Mã Nhân Viên</th>
                                        <th width="86">Mã Leader</th>
                                        <th>Tên Nhân Viên</th>
                                        <th>Chức Danh</th>
                                        <th>Trung Tâm</th>
                                        <th>Email</th>
                                        <th width="80">Ngày bắt đầu làm việc</th>
                                        <th width="80">Ngày nghỉ việc</th>
                                        <th width="3%">Đổi MK</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td width="2%">#</td>
                                        <td width="86"><input type="text" class="form-control input-search"
                                                              @input="searchUsers(2)" v-model="hrm_id"/></td>
                                        <td width="86"><input type="text" class="form-control input-search"
                                                              @input="searchUsers(3)" v-model="superior_hrm_id"/></td>
                                        <td><input type="text" class="form-control input-search" @input="searchUsers(4)"
                                                   v-model="name"/></td>
                                        <td>
                                            <select v-model="role" class="form-control" @change="searchUsers">
                                                <option value="" selected>Tất cả</option>
                                                <option v-for="option in roles" :value="option.id">
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                            <!--<vue-select-->
                                                    <!--label="name"-->
                                                    <!--multiple-->
                                                    <!--placeholder="Lọc Chức Danh"-->
                                                    <!--:options="roles"-->
                                                    <!--v-model="role"-->
                                                    <!--:searchable="true"-->
                                                    <!--language="en-US"-->

                                            <!--&gt;</vue-select>-->
                                        </td>
                                        <td>
                                            <select v-model="branch" class="form-control" @change="searchUsersByBranch(branch)">
                                                <option value="" selected>Tất cả</option>
                                                <option v-for="option in branches" :value="option.id">
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                            <!--<vue-select-->
                                                    <!--label="name"-->
                                                    <!--multiple-->
                                                    <!--placeholder="Lọc Trung Tâm"-->
                                                    <!--:options="branches"-->
                                                    <!--v-model="branch"-->
                                                    <!--:searchable="true"-->
                                                    <!--language="en-US"-->
                                                    <!---->
                                                    <!--@selected="searchUsersByBranch(branch)"-->
                                            <!--&gt;</vue-select>-->
                                        </td>
                                        <!-- <td><input type="text" class="form-control input-search" @input="searchUsers" v-model="phone"/></td> -->
                                        <td><input type="text" class="form-control input-search" @input="searchUsers(6)"
                                                   v-model="email"/></td>
                                        <td width="80">
                                            <datepicker
                                                    v-model="start_date"
                                                    :readonly="false"
                                                    :lang="lang"
                                                    :bootstrapStyling="true"
                                                    placeholder="Ngày làm"
                                                    input-class="form-control bg-white"
                                                    class="time-picker"
                                                    change="searchUsers(7)"
                                            ></datepicker>
                                        </td>
                                        <td width="80">
                                            <datepicker
                                                    v-model="end_date"
                                                    :readonly="false"
                                                    :lang="lang"
                                                    :bootstrapStyling="true"
                                                    placeholder="Ngày nghỉ"
                                                    input-class="form-control bg-white"
                                                    class="time-picker"
                                                    change="searchUsers(8)"
                                            ></datepicker>
                                        </td>
                                        <td width="3%">
                                            <span class="apax-btn reset" v-b-tooltip.hover title="Xóa các tiêu chí lọc"
                                                  @click="removeFilter"><i title="" class="fa fa-recycle"></i></span>
                                        </td>
                                    </tr>
                                    <tr v-for="(user, index) in users" :key="index">
                                        <td width="2%">
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ index+1 }}
                                            </router-link>
                                        </td>
                                        <td width="86">
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.hrm_id }}
                                            </router-link>
                                        </td>
                                        <td width="86">
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.boss_hrm_id }}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.full_name }}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.title }}
                                            </router-link>
                                        </td>
                                        <td>
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.branch }}
                                            </router-link>
                                        </td>
                                        <!-- <td><router-link v-b-tooltip.hover class="line anchor-link" :title="`Sửa thông tin nhân viên ${user.full_name}`" :to="`/edit-user/${user.user_id}`">{{ user.phone }}</router-link></td> -->
                                        <td>
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`"><span class="email-field">{{ user.email }}</span>
                                            </router-link>
                                        </td>
                                        <!-- <td><router-link v-b-tooltip.hover class="line anchor-link" :title="`Sửa thông tin nhân viên ${user.full_name}`" :to="`/edit-user/${user.user_id}`">{{ user.status }}</router-link></td> -->
                                        <td width="80">
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.start_date }}
                                            </router-link>
                                        </td>
                                        <td width="80">
                                            <router-link v-b-tooltip.hover class="line anchor-link"
                                                         :title="`Sửa thông tin nhân viên ${user.full_name}`"
                                                         :to="`/users/${user.user_id}/edit`">{{ user.end_date }}
                                            </router-link>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="apax-btn remove" v-b-tooltip.hover
                                                                 :title="`Đổi mật khẩu tài khoản nhân viên ${user.full_name}`"
                                                                 @click="updateProfile(user.user_id, `${user.full_name} - ${user.username}`, user.email)"><i
                                                title="" class="fa fa-id-card"></i></span>&nbsp;
                                                <!--<router-link v-b-tooltip.hover class="line anchor-link"-->
                                                             <!--:title="`Phân quyền cho nhân viên ${user.full_name}`"-->
                                                             <!--:to="`/user-role/${user.user_id}`">-->
                                                    <!--<span class="apax-btn remove">-->
                                                        <!--<i title="" class="fa fa-bug"></i>-->
                                                    <!--</span>-->
                                                <!--</router-link>-->
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- pagination -->
                            <div class="text-center">
                                <nav aria-label="Page navigation">
                                    <paging
                                            :rootLink="router_url"
                                            :id="pagination_id"
                                            :listStyle="list_style"
                                            :customClass="pagination_class"
                                            :firstPage="pagination.spage"
                                            :previousPage="pagination.ppage"
                                            :nextPage="pagination.npage"
                                            :lastPage="pagination.lpage"
                                            :currentPage="pagination.cpage"
                                            :pagesItems="pagination.total"
                                            :pageList="pagination.pages"
                                            :pagesLimit="pagination.limit"
                                            :routing="redirect">
                                    </paging>
                                </nav>
                            </div>
                        </div>
                    </div>
                </b-card>
                <b-modal size="lg" id="importUserModal" hide-header class="add-branchs" v-model="importUserModal">
                    <span class="close" @click="closeImportUserModal">x</span>
                    <h5 class="title-modal-fix text-center">Kết quả Import Nhân Viên</h5>
                    <hr>
                    <b-container fluid>
                        <b-row class="mb-1">
                            <b-col cols="12">
                                <b-row>
                                    <b-col cols="12">
                                        <table class="table table-striped table-bordered apax-table">
                                            <thead>
                                            <tr class="text-sm">
                                                <th width="100">Mã nhân viên</th>
                                                <th>Tên nhân viên</th>
                                                <th>Email</th>
                                                <th>Trạng thái</th>
                                                <th>Note</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(item, index) in resultImport" :key="index">
                                                <td width="100">{{item[0]}}</td>
                                                <td>{{item[1]}}</td>
                                                <td>{{item[3]}}</td>
                                                <td>
                                                    <span v-if="item[10] == 1">Thành công</span>
                                                    <span v-if="item[10] == 0">Thất bại</span>
                                                </td>
                                                <td>{{item[11]}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                    </b-container>
                    <div slot="modal-footer" class="w-100">
                        <b-btn size="sm"
                               class="float-right"
                               variant="primary"
                               @click="closeImportUserModal"
                        >
                            Xác nhận
                        </b-btn>
                    </div>
                </b-modal>
                <b-modal size="lg" id="importUserModalQuit" hide-header class="add-branchs"
                         v-model="importUserModalQuit">
                    <span class="close" @click="closeImportUserModal">x</span>
                    <h5 class="title-modal-fix text-center">Kết quả Import Nhân Viên Nghỉ Việc</h5>
                    <hr>
                    <b-container fluid>
                        <b-row class="mb-1">
                            <b-col cols="12">
                                <b-row>
                                    <b-col cols="12">
                                        <table class="table table-striped table-bordered apax-table">
                                            <thead>
                                            <tr class="text-sm">
                                                <th width="100">Mã nhân viên</th>
                                                <th>Trạng thái</th>
                                                <th>Note</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(item, index) in resultQuit" :key="index">
                                                <td width="100">{{item[0]}}</td>
                                                <td>
                                                    <span v-if="item[2] == 1">Thành công</span>
                                                    <span v-if="item[2] == 0">Thất bại</span>
                                                </td>
                                                <td>{{item[3]}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </b-col>
                                </b-row>

                            </b-col>
                        </b-row>
                    </b-container>
                    <div slot="modal-footer" class="w-100">
                        <b-btn size="sm"
                               class="float-right"
                               variant="primary"
                               @click="closeImportUserModal"
                        >
                            Xác nhận
                        </b-btn>
                    </div>
                </b-modal>
                <b-modal size="mg"
                         ok-variant="primary"
                         title="Cập Nhật Thông Tin Tài Khoản"
                         class="modal-primary change-password"
                         v-model="modal"
                         hide-footer
                         @ok="modal = false"
                         @close="modal = false">
                    <profile :user="`${user_id}`" :email="user_email" :profile="profile_name"></profile>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import u from '../../utilities/utility'
    import paging from '../../components/Pagination'
    import search from '../../components/Search'
    import select from 'vue-select'
    import Datepicker from 'vue2-datepicker'
    import profile from '../../components/Profile'

    export default {
        name: 'user-management',
        data() {
            return {
                loading: false,
                modal: false,
                user_id: 0,
                user_email: '',
                profile_name: '',
                users: [],
                user: {},
                roles: [],
                role: "",
                hrm_id: '',
                superior_hrm_id: '',
                start_date: '',
                end_date: '',
                lang: 'en',
                status: '',
                branches: [],
                branch: '',
                name: '',
                email: '',
                phone: '',
                editUserModal: false,
                importUserModal: false,
                importUserModalQuit: false,
                keywords: [],
                user_list: [],
                selected_branch: '',
                files: {
                    attached_file: ''
                },

                list_style: 'line',
                search_id: 'user_search',
                search_name: 'search-user',
                ajax_loading: true,
                pagination_class: 'users paging list',
                pagination_id: 'user_paging',
                router_url: 'user-management',
                pagination: {
                    limit: 50,
                    spage: 0,
                    ppage: 0,
                    npage: 0,
                    lpage: 0,
                    cpage: 0,
                    total: 0,
                    pages: []
                },
                resultImport: [],
                resultQuit: [],
                uploadFileName: 'Click để chọn file'
            }
        },
        components: {
            paging,
            "vue-select": select,
            Datepicker,
            search,
            profile
        },
        watch: {},
        created() {
            this.start()
        },
        methods: {
            goToAdd(){
              this.$router.push('/add-user')
            },
            start() {
                u.g(`/api/users/get-users-management`).then(response => {
                    // this.users = response.users
                    this.roles = response.roles
                    this.branches = response.branches
                    // return this.searchUsers();
                }).catch(e => console.log(e))

                this.searchUsers()
            },
            showModalEditUser() {
                this.editUserModal = true
            },
            selectItem() {

            },
            removeFilter() {
                this.status = ''
                this.hrm_id = ''
                this.email = ''
                this.phone = ''
                this.name = ''
                this.role = []
                this.branch = []
                this.selected_branch = ''
                this.start_date = ''
                this.end_date = ''
                this.superior_hrm_id = ''
                this.start()
            },
            closeImportUserModal() {
                this.importUserModal = false;
                this.importUserModalQuit = false;
                this.resultImport = '';
                this.resultQuit = '';
            },
            searchUsers(pos = 0) {
                if (!this.loading) {
                    this.loading = true
                    const params = {
                        searchdatas: {
                            hrm_id: this.hrm_id,
                            superior_hrm_id: this.superior_hrm_id,
                            name: this.name,
                            branch: this.selected_branch,
                            role: this.role,
                            phone: this.phone,
                            email: this.email,
                            status: this.status,
                            start_date: this.start_date,
                            end_date: this.end_date
                        },
                        pagination: {
                            spage: this.pagination.spage,
                            ppage: this.pagination.ppage,
                            npage: this.pagination.npage,
                            lpage: this.pagination.lpage,
                            cpage: this.pagination.cpage,
                            total: this.pagination.total,
                            limit: this.pagination.limit
                        }
                    }
                    u.a().post(`/api/users/search-users-multi-keyword`, params).then(response => {
                        this.users = response.data.data.users
                        this.pagination = response.data.data.pagination
                        this.loading = false
                    })
                }
            },
            searchUsersByBranch(data) {
                u.log(data);
                this.selected_branch = data
                this.searchUsers(9)
            },
            closeModal() {
                this.editUserModal = false
            },
            fileChanged(e) {
                const fileReader = new FileReader();
                const fileName = e.target.value.split('\\').pop();
                $('#atch-file').html(fileName)
                this.uploadFileName = fileName
                fileReader.readAsDataURL(e.target.files[0])
                fileReader.onload = (e) => {
                    this.files.attached_file = e.target.result
                }
            },
            redirect(link) {
                const info = link.toString().split('/')
                const page = info.length > 1 ? info[1] : 1
                this.pagination.cpage = parseInt(page)
                u.log('WWWWW', link, this.pagination)
                this.searchUsers(10)
            },
            uploadFile() {
                let Url = `/api/user/uploadExcel`;
                u.a().post(Url, this.files).then(response => {
                    this.files.attached_file = null
                    $('#fileUploadExcel').val('');
                    $('#atch-file').html('File đính kèm')
                    if (response.data.code == 200) {
                        var rep = response.data;
                        if (rep.data.excel == 1) {
                            this.resultImport = rep.data.data;
                            this.importUserModal = true;
                        }
                        if (rep.data.excel == 0) {
                            this.resultQuit = rep.data.data;
                            this.importUserModalQuit = true;
                        }
                        this.searchUsers()
                    } else {
                        alert('Có lỗi! Vui lòng thử lại')
                    }
                }).catch(e => console.log(e))
            },
            fireChangeUploadFile(){
                u.log('hello')
                $("#fileUploadExcel").click()
            },
            updateProfile(id, name, email) {
                this.user_id = id
                this.profile_name = name
                this.user_email = email
                this.modal = true
            },
            exportExcel() {
                const keywords = {
                    hrm_id: this.hrm_id,
                    superior_hrm_id: this.superior_hrm_id,
                    name: this.name,
                    branch: this.selected_branch,
                    role: this.role,
                    phone: this.phone,
                    email: this.email,
                    status: this.status,
                    start_date: this.start_date,
                    end_date: this.end_date

                }
                var strParams = '?hrm_id=' + this.hrm_id +
                    '&superior_hrm_id=' + this.superior_hrm_id +
                    '&name=' + this.name +
                    '&branch=' + this.selected_branch +
                    '&role=' + this.role +
                    '&phone=' + this.phone +
                    '&email=' + this.email +
                    '&status=' + this.status +
                    '&start_date=' + this.start_date +
                    '&end_date=' + this.end_date;

                var p = `/api/user_excel/exportExcel` + strParams;
                window.open(p, '_blank');
                if (extraWindow) {
                    extraWindow.location.reload();
                }
            },
            downloadSampleExcel() {
                var p1 = `/api/user_excel/downloadExample`;
                window.open(p1, '_blank');
            },
            downloadSampleExcelBlock() {
                var p2 = `/api/user_excel/downloadExampleQuit`;
                window.open(p2, '_blank');
            }

        }
    }
</script>

<style scoped lang="scss">
    .apax-table .mx-datepicker.time-picker {
        width: 100% !important;
        min-width: 100px !important;
    }

    .user-upload-frame {
        position: relative;
    }

    .user-upload-file {
        position: absolute;
        top: 0;
        right: 0;
    }

    .email-field {
        text-transform: lowercase;
    }

    .apax-form input.mask[readonly]{
        pointer-events: unset;
        cursor: pointer;
    }
</style>
