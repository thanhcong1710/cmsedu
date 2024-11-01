<template>
  <div class="wrapper">
    <div class="animated fadeIn">
      <b-row>
        <b-col cols="12">
          <b-card
                  header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong> Danh sách</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="row">
                <!--<div class="col-2">---</div>-->
                <div class="col-12">
                  <div class="row">
                    <div class="col-2">
                      <strong>Trung tâm: </strong>
                    </div>
                    <div class="col-7 form-group">
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
                          <button :disabled="disabledSelectBranch" class="btn btn-info btn-print" @click="showBC14Modal">...
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row date-block">
                    <div class="col-4">
                      <div class="row">
                        <div class="col-6"><strong>Từ ngày</strong></div>
                        <div class="col-6">
                          <datepicker
                                  v-model="start"
                                  :readonly="false"
                                  :lang="lang"
                                  :bootstrapStyling="true"
                                  placeholder="Chọn ngày bắt đầu"
                                  input-class="form-control bg-white"
                                  class="time-picker"
                          ></datepicker>
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="row">
                        <div class="col-4"><strong>Đến ngày</strong></div>
                        <div class="col-6">
                          <datepicker
                                  v-model="date"
                                  :lang="lang"
                                  input-class="form-control bg-white"
                                  placeholder="Chọn ngày kết thúc"
                                  :bootstrapStyling="true"
                          ></datepicker>
                        </div>
                      </div>
                    </div>

                    <!-- <div class="col-4">
                      <div class="row">
                        <div class="col-4"><strong>Nguồn</strong></div>
                        <div class="col-8">
                          <select class="form-control" v-model="sources">
                            <option value="" selected>Tất cả</option>
                            <option value="Từ Quá Trình Xếp Lớp">Từ Quá Trình Xếp Lớp</option>
                            <option value="Từ Chuyển Đổi Người Quản Lý">Từ Chuyển Đổi Người Quản Lý</option>
                          </select>
                        </div>
                      </div>
                    </div> -->

                  </div>

                </div>

                <div class="col-2"></div>
              </div>
              <div class="row">
                <div class="text-center">
                  <button class="apax-btn full detail" @click="viewPrintInfo"><i class="fa fa-eye"> &nbsp;Xem
                    trước</i></button> &nbsp;

                  <router-link class="apax-btn full warning" :to="'/cm-transfer'"><i class="fa fa-sign-out"></i> Quay lại</router-link> &nbsp;

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
            <div class="content-detail">
            	<div class="text-right">
            		<button class="apax-btn full edit" @click="exportExcel"><i class="fa fa-print"> &nbsp;Xuất báo cáo</i></button>
            	</div>
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                  <tr class="text-sm">
                    <th>STT</th>
                    <th>Trung tâm</th>
                    <th>CMS</th>
                    <th>Cyber</th>
                    <th>Tên học sinh</th>

                    <th>From EC</th>
                    <th>To EC</th>

                    <!-- <th>From CM</th>
                    <th>To CM</th> -->

                    <th>Ngày thực hiện</th>
                    <th>Người chuyển đổi</th>
                    <th>Nguồn</th>


                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item, index) in results" :key="index">
                    <td>{{ index+1 }}</td>
                    <td>{{ item.branch_name }}</td>
                    <td>{{ item.crm_id }}</td>
                    <td>{{ item.accounting_id }}</td>
                    <td>{{ item.student_name }}</td>

                    <td>{{ item.from_ec }}</td>
                    <td>{{ item.to_ec }}</td>

                    <!-- <td>{{ item.from_cm }}</td>
                    <td>{{ item.to_cm }}</td> -->

                    <td>{{ item.date|dateFormat }}</td>
                    <td>{{ item.editor }}</td>
                    <td>{{ item.note }}</td>


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
    import u from '../../../utilities/utility'
    import select from 'vue-select'
    import Datepicker from 'vue2-datepicker'
    import moment from 'moment'

    export default {
        name: 'history',
        components: {
            Datepicker,
            "vue-select": select
        },
        data() {
            return {
                showBC14: false,
                disabledZone: false,
                disabledRegion: false,
                disabledProgram: true,
                disabledSelectBranch: false,
                selectedProducts: [],
                selectedPrograms: [],
                products: [],
                programs: [],
                program: '',
                rows: [],
                result3: '',
                printResults: [],
                lang: 'en',
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
                selectedBranche_name: '',
                results: [],
                role_branch: '',
                date: '',
                start:'',
                results: [],
                sources: ''

            }
        },
        created() {
            this.setDefaultDate();
            u.a().get(`/api/zones`).then(response => {
                this.zones = response.data
            })
            u.a().get(`/api/get-all-regions`).then(response => {
                this.regions = response.data
            })
            u.a().get(`/api/reports/branches`).then(response => {
                this.branches = response.data
                this.checkRole()
            })

            u.a().get(`/api/all/products`).then(response => {
                this.products = response.data
            })

        },
        methods: {
            setDefaultDate() {
              this.start = moment().format('YYYY-MM-DD');
              this.date = moment().format('YYYY-MM-DD');
            },
            checkRole(){
                u.a().get(`/api/reports/check-role`).then(response => {
                    const rs = response.data
                    // console.log('checko role', rs);
                    if(rs === 1){
                        this.role_branch = true
                        this.disabledSelectBranch = false
                    }else {
                        this.role_branch = false
                        this.disabledSelectBranch = true
                        this.selectedBranche_name = this.branches[0].name;
                        var select_brch = this.branches[0].id;
                        this.selectedBranches.push(select_brch);
                    }
                })
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
                // console.log(ids);
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

                const data = {
                    branches: this.selectedBranches,
                    to_date: moment(this.date).format('YYYY-MM-DD'),
                    from_date: moment(this.start).format('YYYY-MM-DD'),
                    sources: this.sources,
                }

                u.a().post(`/api/log_manager_transfer/lists`,data).then(response => {
                  this.results = response.data;
                })
            },
            resetPrintInfo() {
                this.selectedBranches = []
                this.selectedProducts = []
            },
            getBranchesByRegion(ids = []) {
                alert('Hello wold')
            },
            selectItem() {
                this.showBC14 = false
            },
            showBC14Modal() {
                this.showBC14 = true
            },
            exportExcel() {

                let branches = [];
                if (this.selectedBranches.length) {
                    for (var i in this.selectedBranches) {
                        branches += this.selectedBranches[i] + ',';
                    }
                    branches = branches.slice(0, -1)
                } else {
                  branches = '_';
                }

                let from_date = ( this.start != '' ) ? moment(this.start).format('YYYY-MM-DD') : '_';
                let to_date = ( this.date != '' ) ? moment(this.date).format('YYYY-MM-DD') : '_';
                let sources = ( this.sources != '' ) ? this.sources : '_';

                let url = `api/log_manager_transfer/export_excel/${branches}/${from_date}/${to_date}/${sources}`;
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

        },
        filters: {
          dateFormat(date) {
            return moment(date).format("Y-MM-DD");
          }
        }
    }
</script>

<style scoped>
  .date-block {
    padding-bottom: 20px;
  }
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
  .text-center{
  	width: 100%;
  }
</style>
