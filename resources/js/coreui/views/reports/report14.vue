<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>BC14 - Báo cáo Pending</strong>
                            <div class="card-actions">
                                <a href="skype:thanhcong1710?chat" target="_blank">
                                    <small className="text-muted"><i class="fa fa-skype"></i></small>
                                </a>
                            </div>
                        </div>
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-8">
                                    <div class="row">
                                        <div class="col-3">
                                            <strong>Trung tâm: </strong>
                                        </div>
                                        <div class="col-9 form-group">
                                            <div class="row">
                                                <div class="col-10" v-if="role_branch == true">
                                                    <vue-select
                                                            label="id"
                                                            multiple
                                                            placeholder="Mặc định chọn tất cả..."
                                                            :options="selectedBranches"
                                                            v-model="selectedBranches"
                                                            :searchable="true"
                                                            language="zh-CN"
                                                    ></vue-select>
                                                </div>
                                                <div class="col-10" v-else>
                                                  <input type="text" class="form-control" v-model="selectedBranche_name" readonly>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-info btn-print" @click="showBC14Modal">...
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <strong>Sản phẩm: </strong>
                                        </div>
                                        <div class="col-9 form-group">
                                            <vue-select
                                                    label="name"
                                                    multiple
                                                    placeholder="Mặc định chọn tất cả..."
                                                    :options="products"
                                                    v-model="selectedProducts"
                                                    :searchable="true"
                                                    :onChange="programsOnProduct()"
                                                    language="zh-CN"
                                            ></vue-select>
                                        </div>
                                    </div>
                                    <div class="row date-block">
                                        <div class="col-7">
                                            <div class="row">
                                                <div class="col-5"><strong>Ngày:</strong></div>
                                                <div class="col-6">
                                                    <calendar
                                                            class="form-control calendar"
                                                            :value="from_date"
                                                            :transfer="true"
                                                            :format="html.calendar.options.formatSelectDate"
                                                            :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                            :clear-button="html.calendar.options.clearSelectedDate"
                                                            :placeholder="html.calendar.options.placeholderSelectDate"
                                                            :pane="1"
                                                            :disabled="html.calendar.disabled"
                                                            :onDrawDate="onDrawDate"
                                                            :lang="html.calendar.lang"
                                                            @input="selectDate"
                                                    ></calendar>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2"></div>
                            </div>
                            <div class="row">
                                <div class="print-btn-group">
                                    <button class="btn btn-info" @click="viewPrintInfo"><i class="fa fa-eye"> &nbsp;Xem
                                        trước</i></button> &nbsp;
                                    <button class="btn btn-default" @click="resetPrintInfo"><i class="fa fa-ban"> &nbsp;Bỏ
                                        lọc</i></button> &nbsp;
                                    <router-link class="btn btn-warning btn-back" :to="'/forms'">Quay lại</router-link>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>Danh sách</strong>
                            <button class="back-btn btn-success btn" @click="exportExcel"><i class="fa fa-print"> &nbsp;Xuất báo cáo</i></button>
                        </div>
                        <div class="content-detail">
                            <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                    <thead>
                                    <tr class="text-sm">
                                        <th>STT</th>
                                        <th>Mã LMS</th>
                                        <th>Mã EFFECT</th>
                                        <th>Tên học sinh</th>
                                        <th>Trung tâm</th>
                                        <th>Sản phẩm</th>
                                        <th>Chương trình</th>
                                        <th>Gói học phí</th>
                                        <th>Gía niêm yết</th>
                                        <th>Số tiền thực thu</th>
                                        <th>Số tiền đã đóng</th>
                                        <th>Công nợ</th>
                                        <th>Ngày nhập học</th>
                                        <th>Ngày bắt đầu pending</th>
                                        <th>Ngày kết thúc pending</th>
                                        <th>Số ngày pending</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in results" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ item.lms_id }}</td>
                                        <td>{{ item.accounting_id }}</td>
                                        <td>{{ item.student_name }}</td>
                                        <td>{{ item.branch_name }}</td>
                                        <td>{{ item.product_name }}</td>
                                        <td>{{ item.program_name }}</td>
                                        <td>{{ item.tuition_fee_name }}</td>
                                        <td>{{ item.tuition_fee_price | formatMoney}}</td>
                                        <td>{{ item.must_charge | formatMoney}}</td>
                                        <td>{{ item.total_charged | formatMoney}}</td>
                                        <td>{{ item.debt_amount | formatMoney}}</td>
                                        <td>{{ item.start_date }}</td>
                                        <td>{{ item.pending_date }}</td>
                                        <td>{{ item.pending_end_date }}</td>
                                        <td>{{ item.sessions }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
            <!-- the modal below -->
            <b-modal size="lg" id="showBC14" hide-header class="add-branchs" v-model="showBC14">
                <span class="close" @click="closeModal">x</span>
                <h5 class="title-modal-fix text-center">Chọn trung tâm áp dụng</h5>
                <hr>
                <b-container fluid>
                    <b-row class="mb-1">
                        <b-col cols="12">
                            <b-row>
                                <b-col cols="3" class="title-search">Khu vực</b-col>
                                <br>
                                <b-col cols="6">
                                    <div class="form-group">
                                        <vue-select label="name"
                                                    multiple
                                                    :options="zones"
                                                    :onChange="selectZone(this.zone)"
                                                    :disabled="disabledZone"
                                                    v-model="zone"
                                                    placeholder="Chọn tất cả"
                                                    :searchable="true"
                                        ></vue-select>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-row>
                                <b-col cols="3" class="title-search">Vùng</b-col>
                                <br>
                                <b-col cols="6">
                                    <div class="form-group">
                                        <vue-select
                                                label="name"
                                                multiple
                                                :options="regions"
                                                v-model="region"
                                                placeholder="Chọn tất cả"
                                                :searchable="true"
                                                :disabled="disabledRegion"
                                                :onChange="selectRegion(this.region)"
                                                language="zh-CN"
                                        ></vue-select>
                                    </div>
                                </b-col>
                            </b-row>
                            <div class="row">
                                <p class="text-center selected-button">
                                    <button class="btn btn-info" @click="findBranches()">Tìm trung tâm</button>
                                    <button class="btn btn-success" @click="selectItem()">Chọn</button>
                                    <button class="btn btn-warning" @click="resetBranches()">Reset</button>
                                </p>
                            </div>
                            <b-row>
                                <b-col cols="12" class="mt-30">
                                    <vue-good-table
                                            @on-select-all="selectAll"
                                            @on-row-click="toggleSelectRow"
                                            :columns="columns"
                                            :rows="branches"
                                            :pagination-options="{ enabled: true, perPage: 100 }"
                                            :select-options="{
                                                enabled: true,
                                                selectionInfoClass: 'info-custom'
                                               }"
                                            :search-options="{ enabled: true }">
                                    </vue-good-table>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                </b-container>
                <div slot="modal-footer" class="w-100">
                    <b-btn size="sm"
                           class="float-right"
                           variant="primary"
                           @click="cancelBC14()">
                        Hủy
                    </b-btn>
                    <b-btn size="sm" class="float-right" variant="warning" @click="selectItem()">
                        Chọn
                    </b-btn>
                </div>
            </b-modal>
        </div>
    </div>
</template>

<script>
    import u from '../../utilities/utility'
    import select from 'vue-select'
    import Datepicker from 'vue2-datepicker'
    import moment from 'moment'
    import calendar from 'vue2-datepicker'

    export default {
        name: 'Report14',
        components: {
            Datepicker,
            "vue-select": select,
            calendar
        },
        data() {
            return {
                showBC14: false,
                disabledZone: false,
                disabledRegion: false,
                disabledProgram: true,
                selectedProducts: [],
                selectedPrograms: [],
                products: [],
                programs: [],
                program: '',
                rows: [],
                result3: '',
                printResults: [],
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
                zones: [],
                zone: '',
                region: '',
                regions: [],
                branches: [],
                selectedBranches: [],
                lang: 'en',
                from_date: '',
                // to_date: '',
                results: [],
                role_branch: '',
                selectedBranche_name: '',
                html: {
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
                        }
                    },
                }

            }
        },
        created() {
            u.a().get(`/api/zones`).then(response => {
                this.zones = response.data
            })
            u.a().get(`/api/get-all-regions`).then(response => {
                this.regions = response.data
            })
            u.a().get(`/api/reports/branches`).then(response => {
                this.branches = response.data
            })

            u.a().get(`/api/all/products`).then(response => {
                this.products = response.data
            })
            this.getDefaultDate()
            this.checkRole()
        },
        methods: {
            checkRole(){
              u.a().get(`/api/reports/check-role`).then(response => {
                const rs = response.data
                console.log('checko role', rs);
                if(rs === 1){
                  this.role_branch = true
                  this.disabledSelectBranch = false
                }else {
                  this.role_branch = false
                  this.disabledSelectBranch = true
                  this.selectedBranche_name = this.branches[0].name
                  const selected_branch_id = this.branches[0].id
                  if(selected_branch_id){
                    this.selectedBranches.push(selected_branch_id);
                  }
                }
              })
            },
            getDefaultDate(){
                this.from_date = moment().format('YYYY-MM-DD')
            },
            programsOnProduct() {

            },


            selectZone(e) {
                if (e != '') {
                    this.disabledRegion = true
                }
            },
            selectRegion(e) {
                if (e != '') {
                    this.disabledZone = true

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
            resetBranches() {
                this.zone = ''
                this.region = ''
                this.disabledZone = false
                this.disabledRegion = false
            },
            viewPrintInfo() {
                let products = [];
                if (this.selectedProducts.length) {
                    for (var i in this.selectedProducts) {
                        products.push(this.selectedProducts[i].id)
                    }
                }

                let branches = [];
                if (this.selectedBranches.length) {
                    for (var i in this.selectedBranches) {
                        branches.push(this.selectedBranches[i])
                    }
                }

                const data = {
                    branches: branches,
                    products: products,
                    date: this.from_date ? this.from_date : '',
                }
                u.a().post(`/api/reports/form-14`, data).then(response => {
                    this.results = response.data.data
                })
            },
            resetPrintInfo() {
                this.selectedBranches = []
                this.selectedProducts = []
                this.from_date = ''
            },
            selectItem() {
                this.showBC14 = false
            },
            showBC14Modal() {
                this.showBC14 = true
            },
            exportExcel() {
                let products = [];
                if (this.selectedProducts.length) {
                    for (var i in this.selectedProducts) {
                        products.push(this.selectedProducts[i].id)
                    }
                }

                let branches = [];
                if (this.selectedBranches.length) {
                    for (var i in this.selectedBranches) {
                        branches.push(this.selectedBranches[i])
                    }
                }

                const data = {
                    branches: branches,
                    products: products,
                    date: this.from_date ? this.from_date : '',
                    tk: u.token()
                }

                let data_string = JSON.stringify(data);

                let url = `/api/exel/print-report-bc14/${data_string}`;
                window.open(url, '_blank');
            },
            cancelBC14() {
                this.resetBranches()
                this.showBC14 = false
            },
            closeModal() {
                this.cancelBC14()
            },
            selectAll(selected, selectedRows) {
                const data = {
                    selected: selected,
                    rows: selectedRows
                }
                let ids = []
                const selectedItems = data.selected.selectedRows
                for (var i = 0; i < selectedItems.length; i++) {
                    ids.push(selectedItems[i].id);
                }
                this.selectedBranches = ids;
            },
            toggleSelectRow(params) {
                const data = params.row
                let selected_id = data.id
                let selectedBranches = []
                selectedBranches = this.selectedBranches
                if (this.selectedBranches.indexOf(selected_id) === -1) {
                    this.selectedBranches.push(selected_id);
                }
            },
            selectDate(date){
                this.from_date = date;
            },
            onDrawDate(){

            }
        }
    }
</script>

<style scoped>
    .scrollable{
        overflow-x: auto;
    }
    .btn-print {
        width: 70px;
        margin-left: -16px;
    }

    .mt-30 {
        margin-top: 30px;
    }

    .selected-button {
        margin: auto;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .btn-back {
        /*margin-left: 740px;*/
        display: inline;

    }

    .button-print {
        margin-left: 800px;
        /*margin-top: 0px;*/
        /*padding: 5px;*/

    }

    .close {
        cursor: pointer;
        margin-top: -10px;
    }

    .btn-block {

    }

    .print-btn-group {
        margin-left: 480px;
        margin-top: 50px;
    }

    .input-group {
        /*margin-left: 50px;*/
    }

    .time-picker {

    }

    .vdp-datepicker {
        margin-left: 6px;
    }

    .card-header .back-btn{
        font-size: 14px;
        padding: 4px 10px;
        color: #fff;
        text-shadow: none;
        text-transform: none;
        text-decoration: none;
        float: right;
        position: absolute;
        right: 34px;
        top: 14px;
        line-height: 23px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
</style>