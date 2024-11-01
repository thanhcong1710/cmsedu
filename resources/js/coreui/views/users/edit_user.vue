<template>
    <div class="animated fadeIn apax-form" id="users-management">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-address-book"></i> <strong>Sửa thông tin nhân viên</strong>
                    </div>
                    <div v-show="loading" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
                        </div>
                    </div>
                    <div class="panel page-content">
                        <div class="row">
                            <div class="col-2">
                                <div class="user-avatar">
                                    <file
                                            :label="'Ảnh đại diện'"
                                            :name="'upload_avatar'"
                                            :field="'avatar'"
                                            :type="'img'"
                                            :link="validFile(user.avatar)"
                                            :onChange="uploadAvatar"
                                            :title="'Tải lên 1 file ảnh đại diện với định dạng ảnh là: jpg, jpeg, png, gif.'"
                                            :class="'avatar-wrap'"
                                    >
                                    </file>
                                </div>
                            </div>
                            <div class="col-5 profile-form">
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Tên Nhân Viên:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" class="form-control"
                                                                   v-model="user.full_name"></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Mã Nhân Viên:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" class="form-control"
                                                                   v-model="user.hrm_id"></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Số Điện Thoại:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" class="form-control"
                                                                   v-model="user.phone"></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Tài Khoản:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" class="form-control"
                                                                   v-model="user.username"></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Email:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" class="form-control"
                                                                   v-model="user.email"></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Đổi Mật Khẩu:</strong></div>
                                    <div class="col-8 mb-2">
                                        <input type="password" class="form-control" v-model="user.username"
                                               @click="change_password=true">
                                        <b-modal size="md"
                                                 ok-variant="primary"
                                                 title="Cập Nhật Thông Tin Tài Khoản"
                                                 class="modal-primary change-password"
                                                 v-model="change_password"
                                                 hide-footer
                                                 @ok="modal = false"
                                                 @close="modal = false">
                                            <profile :user="`${user.id}`" :email="user.email"
                                                     :profile="user.full_name"></profile>
                                        </b-modal>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Trạng Thái:</strong></div>
                                    <div class="col-8 mb-2">
                                        <select v-model="user.status" class="form-control">
                                            <option value="" disabled>Chọn trạng thái</option>
                                            <option value="1">Đang làm việc</option>
                                            <option value="0">Đã nghỉ việc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5 history-form">
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Chức Danh:</strong></div>
                                    <div class="col-8 mb-2">
                                        <select name="" class="form-control" v-model="current_role_id"
                                                @change="selectUserRole" :disabled="html.history_form.position.disabled">
                                            <option value="" disabled>Chọn chức danh</option>
                                            <option :value="role.id" v-for="(role, i) in roles" :key="i">{{role.name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" v-show="checkSuperiorShow">
                                    <div class="col-4 mt-2 align-right"><strong>Mã Thủ Trưởng:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" class="form-control"
                                                                   v-model="role.superior_id" :readonly="html.history_form.superior.disabled"></div>
                                </div>
                                <div class="row" v-show="html.history_form.branch.show">
                                    <div class="col-4 mt-2 align-right"><strong>Nơi Công Tác:</strong></div>
                                    <div class="col-8 mb-2">
                                        <select class="form-control" v-model="role.branch_id" :disabled="html.history_form.branch.disabled">
                                            <option value="">Chọn trung tâm nơi công tác</option>
                                            <option :value="branch.id" v-for="(branch, index) in branches" :key="index">
                                                {{ branch.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" v-show="checkShowBranchesCeo">
                                    <div class="col-4 mt-2 align-right"><strong>Là Giám Đốc Trung Tâm:</strong></div>
                                    <div class="col-8 mb-2">
                                        <vue-select
                                                label="name"
                                                multiple
                                                :options="branches"
                                                v-model="selectedBranches"
                                                placeholder="Chọn trung tâm"
                                                :searchable="true"
                                                :disabled="disabledBranch"
                                                :onChange="selectBranch"
                                                language="en-US">
                                        </vue-select>
                                    </div>
                                </div>
                                <div class="row" v-show="checkShowBranchesCeo">
                                    <div class="col-4 align-right"><strong>Là Giám Đốc Vùng:</strong></div>
                                    <div class="col-8 mb-2">
                                        <input type="checkbox" class="form-control region-check" v-model="checked"
                                               @click="checkboxToggle">
                                    </div>
                                </div>
                                <div class="row"
                                     v-show="showRoleselected || getRole(current_history.role_id) === 'region_ceo'">
                                    <div class="col-4 mt-2 align-right"><strong>Giám Đốc Của Vùng:</strong></div>
                                    <div class="col-8 mb-2">
                                        <vue-select
                                                label="name"
                                                multiple
                                                :options="regions"
                                                v-model="selectedRegions"
                                                placeholder="Chọn vùng"
                                                :searchable="true"
                                                :disabled="disabledRegion"
                                                :onChange="selectRegion"
                                                language="en-US"
                                                :class="html.history_form.region.class"
                                        >
                                        </vue-select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Ngày Bắt Đầu:</strong></div>
                                    <div class="col-8 mb-2">
                                        <calendar
                                                class="form-control calendar"
                                                v-model="current_start_date"
                                                :transfer="true"
                                                :format="'YYYY-MM-DD'"
                                                :disabled-days-of-week="[]"
                                                :clear-button=true
                                                :placeholder="'Ngày bắt đầu làm việc'"
                                                :pane="1"
                                                :disabled="disabledStartDate"
                                                :onDrawDate="onDrawStartDate"
                                                :lang="html.calendar.lang"
                                                @input="checkStartDate"
                                        ></calendar>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Ngày Kết Thúc:</strong></div>
                                    <div class="col-8 mb-2">
                                        <calendar
                                                class="form-control calendar"
                                                v-model="current_end_date"
                                                :transfer="true"
                                                :format="'YYYY-MM-DD'"
                                                :disabled-days-of-week="[]"
                                                :clear-button=true
                                                :placeholder="'Hiện đang làm việc'"
                                                :pane="1"
                                                :disabled="disabledEndDate"
                                                :onDrawDate="onDrawEndDate"
                                                :lang="html.calendar.lang"
                                                @input="checkEndDate"
                                        ></calendar>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Lịch Sử:</strong></div>
                                    <div class="col-8 mb-2"><input type="text" :class="worked"
                                                                   class="form-control no-border" v-model="comment"
                                                                   readonly></div>
                                </div>
                                <!--<div class="row">
                                    <div class="col-4 mt-2 align-right"><strong>Mã Effect:</strong></div>
                                    <div class="col-8 mb-2">
                                        <div class="position-relative">
                                            <input type="text" class="form-control no-border" :value="user.accounting_id" readonly>
                                            <button class="form-control no-border update-effect" v-show="needEffect" @click="createEffectID"><i class="fa fa-plus"></i> Cập nhật mã Effect</button>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-12 mb-2"><br/>
                                <abt
                                        :markup="'success'"
                                        :icon="'fa-save'"
                                        :label="'Lưu Hồ Sơ'"
                                        :title="'Lưu lại thông tin hồ sơ tài khoản'"
                                        :disabled="disabledButtonSaveProfile"
                                        :onClick="saveProfile"
                                        @mouseover="hoverSaveProfile"
                                        @mouseleave="mouseoutSaveProfile"
                                >
                                </abt>
                                <abt
                                        :markup="'error'"
                                        :icon="'fa-plus'"
                                        :label="'Thêm Lịch Sử'"
                                        :title="'Thêm lịch sử làm việc mới'"
                                        :disabled="disabledButtonAdd"
                                        :onClick="addProfile"
                                        @mouseover="hoverAddHistory"
                                        @mouseleave="mouseoutAddHistory"
                                >
                                </abt>
                                <abt
                                        :markup="'primary'"
                                        :icon="'fa-history'"
                                        :label="'Lưu Lịch Sử'"
                                        :title="'Lưu lại thông tin lịch sử làm việc'"
                                        :disabled="disabledButtonSaveHistory"
                                        :onClick="saveHistory"
                                        @mouseover="hoverSaveHistory"
                                        @mouseleave="mouseoutSaveHistory"
                                >
                                </abt>
                                <abt
                                        :markup="'warning'"
                                        :icon="'fa-recycle'"
                                        :label="'Nhập lại'"
                                        :title="'Thao tác lại với thông tin lịch sử làm việc'"
                                        :disabled="disabledButtonReset"
                                        :onClick="resetProfile"
                                >
                                </abt>
                                <router-link class="exit-button" :to="'/user-management'"><i data-v-1eca3bc8="" class="fa fa-sign-out"></i> Thoát</router-link>
                            </div>
                        </div>
                        <b-modal :size="modal_size"
                                 ok-variant="primary"
                                 :title="modal_title"
                                 :class="modal_class"
                                 class="notification"
                                 v-model="modal_show"
                                 @ok="modal_show = false"
                                 @close="modal_show = false">
                            <div v-html="modal_content"></div>
                        </b-modal>
                    </div>
                </b-card>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-history"></i> <b class="uppercase">Quá trình công tác và làm việc :</b>
                    </div>
                    <div class="page-content working-history total-frame">
                        <div class="table-responsive scrollable">
                            <table class="table table-striped table-bordered apax-table">
                                <thead>
                                <tr class="text-sm">
                                    <th width="90px;">STT</th>
                                    <th width="280px;">Trung tâm</th>
                                    <th width="200px;">Role</th>
                                    <th width="100px;">Ngày bắt đầu</th>
                                    <th width="100px;">Ngày kết thúc</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, i) in history" :key="i" :class="i===0 ? 'current active' : ''"
                                    class="history-line" :id="`item-row-${item.id}`" v-b-tooltip.hover
                                    title="Sửa thông tin của bản ghi lịch sử làm việc này" @click="updateHistory(item)">
                                    <td>{{ history.length - i }}</td>
                                    <td>{{ item.branch_name }}</td>
                                    <td>{{ item.title }}</td>
                                    <td>{{ item.start_date|formatDate }}</td>
                                    <td>{{ item.end_date, 'Hiện đang làm việc' |workingDate }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </b-card>
            </div>
        </div>
    </div>

</template>

<script>
    import u from '../../utilities/utility'
    import paging from '../../components/Pagination'
    import search from '../../components/Search'
    import file from '../../components/File'
    import abt from '../../components/Button'
    import profile from '../../components/Profile'
    import select from 'vue-select'
    import Datepicker from 'vue2-datepicker'
    import calendar from 'vue2-datepicker'
    import moment from 'moment'

    export default {
        name: 'edit-user',
        data() {
            return {
                modal_size: 'md',
                modal_class: 'modal-primary',
                modal_title: 'Thông Báo',
                modal_show: false,
                modal_content: '',
                message: '',
                loading: false,
                history: [],
                checkSuperiorShow: false,
                current_history_id: 0,
                current_start_date: this.moment().format('YYYY-MM-DD'),
                current_end_date: this.moment().format('YYYY-MM-DD'),
                current_role_id: 0,
                change_password: false,
                history_changed: false,
                showDeleteHistoryButton: true,
                disabledDeleteHistory: false,
                history_length: '',
                readonly: false,
                showUpdateButton: true,
                modal: false,
                addNewUserModal: false,
                disabledEditButton: true,
                disabledButtonSaveHistory: true,
                disabledButtonSaveProfile: true,
                showRoleselected: false,
                disabledButtonAdd: true,
                disabledButtonReset: true,
                disabledButtonExit: true,
                disabledStartDate: true,
                disabledEndDate: true,
                checked_list: [],
                temp: [],
                checked: '',
                search: '',
                users: [],
                user: '',
                hrm_id: '',
                email: '',
                status: '',
                name: '',
                phone: '',
                superior_id: '',
                start_date: '',
                end_date: '',
                branch1: '',
                role1: '',
                start_date1: '',
                end_date1: '',
                branch2: '',
                role2: '',
                start_date2: '',
                end_date2: '',
                branch3: '',
                role3: '',
                start_date3: '',
                end_date3: '',
                branch4: '',
                role4: '',
                start_date4: '',
                end_date4: '',
                branch5: '',
                role5: '',
                start_date5: '',
                end_date5: '',
                roles: [],
                current_history: {},
                editUserModal: false,
                disabledZone: false,
                disabledRegion: false,
                disabledBranch: false,
                selectedBranches: [],
                selectedRegions: [],
                selectedZones: [],
                checkShowBranchesCeo: false,
                zone: '',
                zones: [],
                regions: [],
                region: '',
                comment: 'Đã từng công tác tại vị trí này!',
                role: {
                    id: 0,
                    start_date: '',
                    end_date: '',
                    branch_id: 0,
                    superior_id: 0
                },
                columns: [
                    {
                        label: 'Trung tâm',
                        field: 'name',
                        sortable: true,
                    },
                    {
                        label: 'Vùng',
                        field: 'age',
                        type: 'number',
                    },
                    {
                        label: 'Khu vực',
                        field: 'age',
                        type: 'number',
                    }
                ],
                html: {
                    calendar: {
                        disabled: false,
                        options: {
                            formatSelectDate: 'YYYY-MM-DD',
                            disabledDaysOfWeek: [],
                            clearSelectedDate: true,
                            placeholderSelectDate: 'Chọn ngày bắt đầu',
                        }
                    },
                    history_form: {
                        position: {
                            disabled: false
                        },
                        superior: {
                            disabled: false,
                            show: true
                        },
                        branch: {
                            disabled: false,
                            show: true
                        },
                        region: {
                            class: ''
                        }
                    }
                },
                worked: '',
                branches: [],
                branch: '',
                lang: 'en',
                update_user_id: '',
                update_history_id: '',
                needEffect: false
            }
        },
        components: {
            abt,
            file,
            paging,
            search,
            profile,
            'vue-select': select,
            calendar,
            Datepicker
        },
        watch: {},
        computed: {
            filteredBranches() {
                return this.branches.filter((branch) => {
                    return branch.name.toLowerCase().match(this.search);
                })
            },
            selectAll: {
                get: function () {
                    return parseInt(this.checked_list.length) === parseInt(this.branches.length)
                },
                set: function (value) {
                    const selected_list = []
                    if (value) {
                        this.branches.forEach((branch) => {
                            selected_list.push(branch.id)
                        })
                    }
                    this.checked_list = selected_list
                    this.temp = selected_list
                }
            }
        },
        created() {
            this.start()
        },
        methods: {
            start() {
                this.loading = true
                u.g(`/api/users/profile/information/${this.$route.params.id}`).then(response => {
                    this.user = response.user
                    this.roles = response.roles
                    this.zones = response.zones
                    this.regions = response.regions
                    this.history = response.history
                    this.branches = response.branches
                    this.checked = u.role(response.user.role_id) === 'region_ceo'
                    this.selectedRegions = u.role(response.user.role_id) === 'region_ceo' ? response.own_regions : []
                    this.selectedBranches = u.role(response.user.role_id) === 'branch_ceo' || u.role(response.user.role_id) === 'region_ceo' ? response.own_branches : []
                    this.disabledBranch = false
                    this.disabledRegion = false
                    let current = {}
                    if (response.history.length) {
                        //current = this._.head(response.history)
                    } else {
                        current = {
                            id: 0,
                            role_id: response.user.role_id,
                            branch_id: response.user.branch_id,
                            superior_id: response.user.superior_id,
                            start_date: response.user.start_date,
                            end_date: response.user.end_date
                        }
                    }
                    this.current_history = current
                    this.role = {
                        id: current.role_id,
                        branch_id: current.branch_id,
                        superior_id: current.superior_id,
                        start_date: current.start_date,
                        end_date: current.end_date
                    }
                    if (this.getRole(this.current_history.role_id) === 'region_ceo' || this.getRole(this.current_history.role_id) === 'branch_ceo') {
                        this.checkShowBranchesCeo = true
                    }
                    this.current_history_id = current.id
                    this.current_start_date = current.start_date
                    this.current_end_date = current.end_date === null || current.end_date === '0000-00-00' ? '' : current.end_date
                    this.disabledButtonAdd = false
                    this.disabledButtonSaveHistory = false
                    this.disabledButtonSaveProfile = false
                    this.disabledButtonReset = false
                    this.disabledButtonExit = false
                    this.disabledStartDate = false
                    this.current_role_id = this.user.role_id
                    this.checkRoleStatus()
                    this.html.history_form.position.disabled = true;
                    this.html.history_form.branch.disabled = true;
                    //this.checkEffectID()
                    setTimeout(() => {
                        this.loading = false
                    }, 1000)
                }).catch(e => console.log(e))
            },
            uploadAvatar(file, param = null) {
                if (param) {
                    this.user[param] = file
                }
            },
            validFile(file) {
                let resp = (typeof file === 'string') ? file : ''
                if (typeof file === 'object') {
                    resp = `${file.type},${file.data}`
                }
                return resp
            },
            imageChanged(e) {
                var fileReader = new FileReader();
                fileReader.readAsDataURL(e.target.files[0])
                fileReader.onload = (e) => {
                    this.user.avatar = e.target.result
                }
            },
            hoverAddHistory() {
                $('.page-content.working-history').addClass('active')
            },
            mouseoutAddHistory() {
                $('.page-content.working-history').removeClass('active')
            },
            hoverSaveHistory() {
                $('.history-form').addClass('active')
            },
            hoverSaveProfile() {
                $('.profile-form').addClass('active')
            },
            mouseoutSaveHistory() {
                $('.history-form').removeClass('active')
            },
            mouseoutSaveProfile() {
                $('.profile-form').removeClass('active')
            },
            checkRoleStatus() {
                if (this.role.id === this.current_history.role_id && this.current_history.branch_id === this.current_history.branch_id && (this.current_history.end_date === '' || this.current_history.end_date === null || this.current_history.end_date === '0000-00-00')) {
                    this.comment = 'Hiện đang công tác tại vị trí này!'
                    this.worked = 'now'
                    this.disabledStartDate = false
                    this.disabledEndDate = true
                    this.html.history_form.position.disabled = false
                    this.html.history_form.superior.disabled = false
                    this.html.history_form.branch.disabled = false
                } else {
                    this.worked = ''
                    this.disabledStartDate = true
                    this.disabledEndDate = true
                    this.html.history_form.position.disabled = true
                    this.html.history_form.superior.disabled = true
                    this.html.history_form.branch.disabled = true
                    this.comment = 'Đã từng công tác tại vị trí này!'
                }
                if (u.role(this.current_history.role_id) === 'region_ceo') {
                    this.checked = 'checked'
                } else {
                    this.checked = ''
                }
                if (this.getRole(this.current_history.role_id) === 'cm' || this.getRole(this.current_history.role_id) === 'ec') {
                    this.checkSuperiorShow = true
                } else {
                    this.checkSuperiorShow = false
                }
                if (this.getRole(this.current_history.role_id) === 'branch_ceo') {
                    this.checkShowBranchesCeo = true
                } else {
                    this.checkShowBranchesCeo = false
                }
            },
            addProfile() {
                this.checked = false
                this.current_role_id = ''
                this.worked = 'now'
                this.current_start_date = this.moment().add(1, 'days').format('YYYY-MM-DD')
                this.current_end_date = ''
                this.comment = 'Đây là vị trí sẽ đảm nhiệm ngay sau khi được cập nhật!'
                this.disabledStartDate = false
                this.disabledEndDate = true
                this.current_history_id = 0
                this.selectedBranches = []
                this.selectedRegions = []
                this.html.history_form.position.disabled = false
                this.html.history_form.branch.disabled = false
                this.checkEffectID()
                u.log(this.current_history_id)
            },
            resetProfile() {
                if (parseInt(this.current_history_id, 10) === 0) {
                    this.addProfile()
                } else {
                    this.updateHistory(this.current_history)
                }
            },
            exitProfile() {
                this.$router.push('/user-management')
            },
            saveProfile() {
                let valid = true
                let message = ''
                if (this.user.full_name === '') {
                    valid = false
                    message += '<i style="color:red">Tên nhân viên là trường bắt buộc không thể để trống.</i><br>'
                }
                if (this.user.hrm_id === '') {
                    valid = false
                    message += '<i style="color:red">Mã HRM nhân viên là trường bắt buộc không thể để trống.</i><br>'
                }
                if (this.user.username === '') {
                    valid = false
                    message += '<i style="color:red">Tài khoản đăng nhập là trường bắt buộc không thể để trống.</i><br>'
                }
                if (this.user.email === '') {
                    valid = false
                    message += '<i style="color:red">Email nhân viên là trường bắt buộc không thể để trống.</i><br>'
                } else if (u.vld.email(String(this.user.email).toLowerCase()) && String(this.user.email).toLowerCase().indexOf('apaxenglish.com') === -1) {
                    valid = false
                    message += '<i style="color:red">Email nhân viên không hợp lệ vui lòng kiểm tra đây phải là mail của ApaxEnglish.</i><br>'
                }
                if (valid) {
                    let confirmation = true
                    confirmation = confirm('Bạn có chắc là muốn cập nhật thông tin hồ sơ tài khoản này không?')
                    if (confirmation) {
                        this.user.role_id = this.current_role_id;
                        this.user.branch_id = this.role.branch_id;
                        const user = this.user;
                        u.a().post(`/api/users/${this.$route.params.id}/update-users-profile`, user).then(response => {
                            this.modal_class = 'modal-success'
                            this.modal_content = `Thông tin tài khoản người dùng <b>${user.full_name} (${user.username})</b> đã được cập nhật thành công!`
                            this.modal_show = true
                        })
                    }
                } else {
                    this.modal_class = 'modal-primary'
                    this.modal_content = `Thông tin dữ liệu hồ sơ tài khoản chưa hợp lệ:<br><br>${message}<br>Xin vui lòng kiểm tra lại và bổ sung đầy đủ.`
                    this.modal_show = true
                }
            },
            saveHistory() {
                let valid = true
                let message = ''
                const data = {
                    mode: 'update_log',
                    user_id: this.user.id,
                    term_id: this.user.term_id,
                    role_id: this.current_role_id,
                    end_date: this.current_end_date,
                    start_date: this.current_start_date,
                    history_id: this.current_history_id,
                    branch_id: this.role.branch_id,
                    branch_ids: this.selectedBranches,
                    region_ids: this.selectedRegions,
                    superior_id: this.role.superior_id,
                    meta_data: {}
                }
                if (parseInt(this.current_history.role_id, 10) === this.user.role_id && parseInt(this.current_history_id, 10) > 0) {
                    data.mode = 'update_all'
                } else if (parseInt(this.current_history_id, 10) === 0) {
                    data.mode = 'insert_log_update_term'
                }
                if (data.mode === 'insert_log_update_term') {
                    if (u.role(this.current_role_id) === 'region_ceo') {
                        if (this.selectedRegions.length === 0) {
                            valid = false
                            message += '<i style="color:red">Thông tin vùng được quản lý bởi vị trí giám đốc vùng là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (u.role(this.current_role_id) === 'branch_ceo') {
                        if (this.selectedBranches.length === 0) {
                            valid = false
                            message += '<i style="color:red">Thông tin trung tâm được quản lý bởi vị trí giám đốc trung tâm là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (u.role(this.current_role_id) === 'ec' || u.role(this.current_role_id) === 'cm') {
                        if (this.role.superior_id === '') {
                            valid = false
                            message += '<i style="color:red">Thông tin mã thủ trưởng của chức danh này là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (this.role.branch_id === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về nơi công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                    if (this.current_start_date === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về ngày bắt đầu công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                } else if (data.mode === 'update_log') {
                    if (this.current_start_date === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về ngày bắt đầu công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                    if (u.role(this.current_role_id) === 'ec' || u.role(this.current_role_id) === 'cm') {
                        if (this.role.superior_id === '') {
                            valid = false
                            message += '<i style="color:red">Thông tin mã thủ trưởng của chức danh này là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (this.role.branch_id === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về nơi công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                    if (this.current_end_date === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về ngày kết thúc công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                } else if (data.mode === 'update_all') {
                    if (u.role(this.current_role_id) === 'region_ceo') {
                        if (this.selectedRegions.length === 0) {
                            valid = false
                            message += '<i style="color:red">Thông tin vùng được quản lý bởi vị trí giám đốc vùng là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (u.role(this.current_role_id) === 'ec' || u.role(this.current_role_id) === 'cm') {
                        if (this.role.superior_id === '') {
                            valid = false
                            message += '<i style="color:red">Thông tin mã thủ trưởng của chức danh này là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (u.role(this.current_role_id) === 'branch_ceo') {
                        if (this.selectedBranches.length === 0) {
                            valid = false
                            message += '<i style="color:red">Thông tin trung tâm được quản lý bởi vị trí giám đốc trung tâm là bắt buộc không thể bỏ trống.</i><br>'
                        }
                    }
                    if (this.role.branch_id === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về nơi công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                    if (this.current_start_date === '') {
                        valid = false
                        message += '<i style="color:red">Thông tin về ngày bắt đầu công tác là bắt buộc không thể bỏ trống.</i><br>'
                    }
                }
                if (valid) {
                    let confirmation = true
                    confirmation = confirm('Bạn có chắc là muốn cập nhật thông tin lịch sử làm việc này không?')
                    if (confirmation) {
                        const user = this.user
                        u.p(`/api/users/${this.$route.params.id}/update-users-history`, data).then(response => {
                            if(response.success){
                                this.history = response.list
                                const current = this._.head(response.list)
                                this.current_history = current
                                this.role = {
                                    id: current.role_id,
                                    branch_id: current.branch_id,
                                    superior_id: current.superior_id,
                                    start_date: current.start_date,
                                    end_date: current.end_date
                                }
                                this.current_role_id = current.role_id
                                this.current_history_id = current.id
                                this.current_start_date = current.start_date
                                this.current_end_date = current.end_date === null || current.end_date === '0000-00-00' ? '' : current.end_date
                                $.when($('.working-history .active').removeClass('active')).then($('.working-history .current').addClass('active'))
                                this.checkRoleStatus()
                                this.modal_class = 'modal-success'
                                this.modal_content = `Thông tin lịch sử làm việc của nhân viên <b>${user.full_name} (${user.username})</b> đã được cập nhật thành công!`
                                this.modal_show = true
                                this.html.history_form.branch.disabled = true
                                this.html.history_form.position.disabled = true
                            }else{
                                this.modal_class = 'modal-danger'
                                this.modal_content = `${response.message}`
                                this.modal_show = true
                            }

                        })
                    }
                } else {
                    this.modal_class = 'modal-primary'
                    this.modal_content = `Thông tin dữ liệu lịch sử làm việc chưa hợp lệ:<br><br>${message}<br>Xin vui lòng kiểm tra lại và bổ sung đầy đủ.`
                    this.modal_show = true
                }
            },
            onDrawStartDate(e) {
                if(this.current_history_id){
                    let date = e.date
                    if (this.current_start_date > date.getTime()) {
                        e.allowSelect = false
                    }
                }else{
                    let date = u.convertDateToString(e.date)
                    if (moment(date) <= moment()) {
                        e.allowSelect = false
                    }
                }
            },
            onDrawEndDate(e) {
                let date = e.date
                if (this.current_end_date > date.getTime()) {
                    e.allowSelect = false
                }
            },
            checkNestDate(date) {
                let resp = false
                if (this.history.length) {
                    this.history.map(item => {
                        if (this.current_history_id !== item.id) {
                            if (u.checkDateIn(date, item.start_date, item.end_date)) {
                                resp = true
                            }
                        }
                    })
                }
                return resp
            },
            checkStartDate(start_date) {
                if (this.checkNestDate(start_date)) {
                    alert("Chý ý: Ngày bắt đầu công tác mà bạn vừa chọn bị lồng trong khoảng thời gian của công tác khác, xin vui lòng chọn lại 1 ngày hợp lệ.")
                    this.current_start_date = this.current_history.start_date
                }
            },
            checkEndDate(end_date) {
                if(!parseInt(this.current_history_id)){
                    if (this.checkNestDate(end_date)) {
                        alert("Chý ý: Ngày kết thúc công tác mà bạn vừa chọn bị lồng trong khoảng thời gian của công tác khác, xin vui lòng chọn lại 1 ngày hợp lệ.")
                        this.current_end_date = this.current_history.end_date
                    }
                }
            },
            updateHistory(item) {
                this.current_history = item
                $('.working-history .active').removeClass('active')
                this.role = {
                    id: item.role_id,
                    branch_id: item.branch_id,
                    superior_id: item.superior_id,
                    start_date: item.start_date,
                    end_date: item.end_date
                }
                this.current_history_id = item.id
                this.current_end_date = item.end_date === null || item.end_date === '0000-00-00' ? '' : item.end_date
                this.current_start_date = item.start_date
                this.current_role_id = item.role_id
                this.checkRoleStatus()
                this.html.history_form.branch.disabled = true
                this.html.history_form.position.disabled = true
                this.html.history_form.region.class = 'no-event'
                this.checkEffectID()
                $(`.working-history #item-row-${item.id}`).addClass('active')
            },
            getRole(id) {
                return u.role(id)
            },
            getRegions() {
                u.a().get(`/api/get-regions-by-role`).then(response => {
                    this.regions = response.data
                    console.log('the resiogn are', this.regions);
                })
            },
            selectZone(e) {
                if (e != '') {
                    // this.disabledZone = true
                }
            },
            selectBranch(e) {
                if (e != '') {
                    // this.disabledBranch = true
                }
            },
            selectRegion(e) {
                if (e != '') {
                    // this.disabledRegion = true
                }
            },
            selectUserRole() {
                if (u.role(this.current_role_id) === 'region_ceo') {
                    this.checked = 'checked'
                    this.showRoleselected = true
                    this.checkShowBranchesCeo = false
                } else {
                    this.checked = ''
                    this.showRoleselected = false
                }
                if (u.role(this.current_role_id) === 'branch_ceo') {
                    this.checkShowBranchesCeo = true
                } else {
                    this.checkShowBranchesCeo = false
                }
                if (u.role(this.current_role_id) === 'ec' || u.role(this.current_role_id) === 'cm') {
                    this.checkSuperiorShow = true
                } else {
                    this.checkSuperiorShow = false
                }
                if((parseInt(this.current_role_id) === u.r.ec_leader) || (parseInt(this.current_role_id) === u.r.om)){
                    this.role.superior_id = this.user.hrm_id;
                    this.html.history_form.superior.disabled = true;

                }else if((parseInt(this.current_role_id) === u.r.ec) || (parseInt(this.current_role_id) === u.r.cm)){
                    this.role.superior_id = this.user.superior_id;
                    this.html.history_form.superior.disabled = false;

                }else{
                    this.role.superior_id = '';
                    this.html.history_form.superior.disabled = true;

                }
            },
            findBranches() {
                let data = {
                    zone: this.zone,
                    region: this.region
                }
                if (data.zone == '' && data.region == '') {
                    data = this.regions
                } else if (data.zone != '') {
                    data = data.zone
                } else {
                    data = data.region
                }
                var ids = []
                for (var i = 0; i < data.length; i++) {
                    ids.push(data[i].id);
                }

                u.a().get(`/api/regions/${ids}/branches`).then(response => {
                    this.branches = response.data
                })
            },
            checkboxToggle() {
                if (this.checked) {
                    this.showRoleselected = false
                } else {
                    this.showRoleselected = true
                }
            },
            resetBranches() {
                this.zone = ''
                this.region = ''
                this.disabledZone = false
                this.disabledRegion = false
            },
            showEditUserModal() {
                this.editUserModal = true
            },
            selectItem() {
                console.log('test this is');
                // this.editUserModal = false
            },
            closeModal() {
                this.editUserModal = false
            },
            showBC12Modal() {

            },
            resetInfo() {
                this.selectedBranches = ''
            },
            cancelEditUserModal() {
                console.log('test hihi');
            },
            toggleSelectRow(branch) {
                const branch_id = branch
                // console.log(student_id);
                // console.log('the check_list',this.checked_list);
                if (this.checked_list && this.temp.indexOf(branch_id) === -1) {
                    this.temp.push(branch_id)
                    console.log(this.temp);
                } else {
                    this.temp.pop(branch_id)

                }
                ;
                // let rs = this.temp
                // console.log(rs);
                // if(this.checked_list && this.temp.indexOf(student_id) !== -1){
                //   alert('hoc sinh da ton tai')
                // }
            },
            validateUserHistoryChange() {
                // const rs = confirm("Bạn có muốn thay đổi lịch sử làm việc ? ")
                if (this.branch == '' || this.role == '') {
                    alert("Trung tâm và role không để trống !")
                    return false
                } else if (this.start_date > this.end_date && this.end_date != '') {
                    alert('Ngày kết thúc phải sau ngày bắt đầu làm việc !')
                    return false
                } else {
                    // console.log('test sai');
                    this.saveUserHistoryChange()
                }
            },
            saveUserHistoryChange() {
                // let branch1 = this.user.branch1[0];
                // console.log(branch1);
                const data = {
                    user: this.user,
                    branch: this.branch,
                    role: this.role,
                    start_date: moment(this.start_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                    end_date: moment(this.end_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

                }
                // console.log(data);
                u.a().post(`/api/users/save-user-working-history`, data).then(response => {
                    let rs = response.data

                    this.history.push(rs)

                    // this.message = "Thêm mới thành công"

                    // this.addNewUserModal = false
                    this.$router.push('/user-management')

                })
            },
            checkDisabledEditButton() {

            },
            validateUpdateUserInfo() {
                const user = {
                    hrm_id: this.user.hrm_id,
                    email: this.user.email,
                    phone: this.user.phone,
                    full_name: this.user.full_name,
                    role_id: this.user.role_id,
                    superior_id: this.user.superior_id,
                    branches: this.selectedBranches,
                    regions: this.selectedRegions,
                    status: this.user.status
                }
                if (this.user.hrm_id == '' || this.user.full_name == '') {
                    alert("Mã nhân viên, Tên nhân viên không để trống!")
                    return false
                } else {
                    if (this.user.role_id == 68) {
                        if (!this.user.superior_id) {
                            alert("Mã người quản lý với EC không để trống!")
                            return false
                        } else {
                            this.updateUserInfo(user)
                        }
                    } else {
                        this.updateUserInfo(user)
                    }
                }
            },
            updateUserInfo(user) {
                const cf = confirm("Bạn có muốn thay đổi thông tin user không ?");
                if (cf) {
                    u.a().post(`/api/users/${this.$route.params.id}/update-user-info`, user).then(response => {
                        this.$router.push('/user-management')
                    })
                }
            },
            getWorkingHistoryList() {
                const user_id = this.$route.params.id
                u.a().get(`/api/users/${user_id}/get-working-history-list`).then(response => {
                    this.history = response.data
                    let wl = this.history
                    this.history_length = wl.length
                    console.log('test leng', wl);
                    if (wl.length > 0) {
                        this.history_changed = true
                    }
                })
            },
            showAddNewUserModal(user_id) {
                this.addNewUserModal = true
                this.getUserHistoryWorkingInfo(user_id)
                console.log('user id to add history', user_id);
            },
            getUserHistoryWorkingInfo(user_id) {
                u.a().get(`/api/users/${user_id}/get-user-working-history-detail`).then(response => {
                    let history = response.data.history
                    let user = response.data.user[0]
                    if (!history) {
                        const end_date = user.end_date
                        this.start_date = moment(end_date, "YYYY-MM-DD").add(1, 'days');
                        // console.log('truong hop chua thay doi lich su', end_date);

                    } else {
                        const end_date = history.end_date
                        // this.history_change = 1;
                        this.start_date = moment(end_date, "YYYY-MM-DD").add(1, 'days');
                        console.log('truong hop da thay doi lich su', this.start_date);
                    }
                    // console.log('the resiogn are', this.history, this.user);
                })
            },
            editWorkingHistory(user_id, item) {
                console.log('sua thong tin hoc sinh', user_id, item);
                this.update_history_id = item
                this.update_user_id = user_id

                u.a().get(`/api/users/${user_id}/${item}/edit-user-working-history`).then(response => {
                    const data = response.data
                    const history = data.history
                    this.start_date = history.start_date
                    this.end_date = history.end_date
                    this.role = history.role_id
                    this.branch = history.branch_id
                    console.log('test user', history);
                })

                this.editUserModal = true
                if (this.history) {
                    this.disabledEditButton = true
                }

            },
            removeWorkingHistory(item, index) {
                let cf = confirm("Bạn có chắc chắn xóa không ?")
                if (cf) {
                    u.a().delete(`/api/users/${item}/remove-user-working-history`).then(response => {
                        this.history.splice(index, 1);
                        this.$router.push(`/user-management`);
                    })
                }
            },
            validateUpdateUserHistory() {
                let user_id = this.update_user_id
                let history = this.update_history_id
                const data = {
                    branch: this.branch,
                    user_id: user_id,
                    history_id: history,
                    role: this.role,
                    start_date: moment(this.start_date).format("YYYY-MM-DD"),
                    end_date: moment(this.end_date).format("YYYY-MM-DD")
                }

                if (!data.branch || !data.role) {
                    alert("Trung tâm và role không để trống !")
                    return false

                } else if (!data.start_date || data.start_date == '') {
                    alert("Ngày bắt đầu làm việc không để trống !")
                    return false

                } else if (data.end_date && data.end_date != '') {
                    if (data.end_date <= data.start_date) {
                        alert('Ngày kết thúc phải lớn hơn ngày bắt đẩu !');
                        return false
                    } else {
                        this.updateUserWorkingHistory(data)
                    }
                } else {
                    this.updateUserWorkingHistory(data)
                }
            },
            updateUserWorkingHistory(x) {
                const data = x
                console.log('test', data);
                const cf = confirm("Bạn có chắc chắn thay đổi lịch sử của user không ?");
                if (cf) {
                    u.a().put(`/api/users/update-user-working-history`, data).then(response => {
                        // this.$router.push('/user-management')
                        const rs = response.data
                        this.editUserModal = false
                        console.log(dt);

                    })
                }
            },
            closeAddUserModal() {
                this.addNewUserModal = false
            },
            closeEditUserModal() {
                this.editUserModal = false
            },
            onDrawDate() {

            },
            selectDate(date) {
                this.from_date = date;
            },
            selectItem() {

            },
            checkEffectID(){
                if(this.current_history_id > 0 && !this.user.accounting_id && ((parseInt(this.current_role_id) === u.r.ec) || (parseInt(this.current_role_id) === u.r.ec_leader))){
                    this.needEffect = true
                }else{
                    this.needEffect = false
                }

                // u.log(parseInt(this.current_role_id));
                // if(parseInt(this.current_role_id) > u.r.branch_ceo){
                //     this.html.history_form.branch.show = false
                // }else{
                //     this.html.history_form.branch.show = true
                // }
            },
            createEffectID(){
                let url = `/api/users/${this.user.id}/sale`
                u.a().put(url)
                    .then(resp => {
                        if(resp.data.code == 200){
                            this.modal_class = 'modal-success'
                            this.modal_content = `Mã Effect đã được tạo`
                            this.modal_show = true
                            this.user.accounting_id = resp.data.data.accounting_id
                            this.needEffect = false
                        }else{
                            this.modal_class = 'modal-primary'
                            this.modal_content = resp.data.message
                            this.modal_show = true
                        }
                    }).catch(e => {
                        this.modal_class = 'modal-primary'
                        this.modal_content = 'Có lỗi xảy ra! Vui lòng thử lại sau.'
                        this.modal_show = true
                })
            }
        }
    }
</script>

<style scoped lang="scss">
    .user-avatar img {
        border-radius: 50%;
        width: 100%;
        height: auto;
        max-height: 280px;
    }

    .align-right {
        text-align: right;
    }

    .input-search {
        height: 20px;
    }

    .apax-form .form-control.now:disabled, .apax-form .form-control.now[readonly] {
        color: #096f23 !important;
        border: 1px solid #81de81 !important;
        background: #c7ffc7 !important;
        text-shadow: 0 1px 0 #fff !important;
    }

    .close {
        cursor: pointer;
        margin-top: -10px;
    }

    .selected-button {
        margin: auto;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .page-content.working-history, .profile-form, .history-form {
        border: 1px solid #FFF;
    }

    .profile-form.active {
        background: #ccffd9;
        border: 1px solid #7ad390;
    }

    .history-form.active {
        background: #e6f5ff;
        border: 1px solid #9cc7e4;
    }

    .page-content.working-history table {
        background: #fff;
    }

    .page-content.working-history.active {
        background: #ffe6e6;
        border: 1px solid #e49c9c;
    }

    .print-btn-group {
        margin-left: 440px;
        margin-top: 50px;
    }

    .working-history tr.active.current, .working-history tr.current {
        color: #035819 !important;
        background: #aee9ae !important;
        text-shadow: 0 1px 0 #fff !important;
    }

    .working-history tr.active {
        color: #cf0707 !important;
        background: #ffc7c7 !important;
        text-shadow: 0 1px 0 #fff !important;
    }

    .working-history tr:hover {
        color: #FFF;
        cursor: not-allowed;
        text-shadow: 0 1px 1px #111;
        background: #dc0505;
    }

    .working-history tr td {
        font-weight: 500;
        cursor: pointer;
    }

    .m-auto {
        margin: auto;
    }

    .start_date {
        width: 150px !important;
    }

    .end_date {
        width: 100px !important;
    }

    .branch_input .dropdown-toggle .form-control {
        width: 20px !important;
    }

    .region-check {
        width: 20px;
        height: 20px;
    }

    .table-title {
        margin: auto;
    }

    .exit-button{
        color: #e7f3fd;
        background: #576986;
        opacity: 1;
        text-shadow: 0 1px 1px #363636;
        border-radius: 1px;
        font-weight: bold;
        border: 1px solid #363636;
        cursor: pointer;
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        font-size: 12px;
        padding: 7px 15px;
        text-decoration: none;
        outline: 0;
        margin: 1px;
    }

    .update-effect{
        position: absolute;
        width: 100%;
        top: 0;
        font-weight: bold;
        color: #ffedec;
        background-color: #ff3b34;
    }
    .update-effect:hover{
        background: #bb0903;
    }

    .position-relative{
        position: relative;
    }

    .no-event{
        pointer-events: none;
    }

    .avatar-wrap .current-file > img{
        max-width: 100%;
        border-radius: 0;
        position: absolute;
        left: 0;
        margin-top: auto;
        margin-bottom: auto;
        padding: 0;
    }
</style>
