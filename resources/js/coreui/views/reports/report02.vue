<template>
  <div class="wrapper">
    <div class="animated fadeIn">
      <b-row>
        <b-col cols="12">
          <b-card
                  header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>BC02 - Báo cáo hiện trạng trung tâm</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="row">
                <div class="col-md-12">
                  <multiselect
                    placeholder="Chọn trung tâm"
                    select-label="Chọn một trung tâm"
                    v-model="selectedBranches"
                    :options="branches"                    
                    label="name"
                    :close-on-select="false"
                    :hide-selected="true"
                    :multiple="true"
                    :searchable="true"
                    track-by="id"
                  >
                    <span slot="noResult">Không tìm thấy trung tâm phù hợp</span>
                  </multiselect>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="row" style="margin-top:10px;">
                      <div class="col-6">
                        <vue-select
                                label="name"
                                multiple
                                placeholder="Chọn sản phẩm..."
                                :options="products"
                                v-model="selectedProducts"
                                :searchable="true"
                                :onChange="programsOnProduct()"
                                language="zh-CN"
                        ></vue-select>
                      </div>
                      <div class="col-3">
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
                      <div class="col-3">
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
              </div>
              <br>
              <div slot="footer" class="text-center">
                <button class="apax-btn full detail" @click="viewPrintInfo">
                  <i class="fa fa-search"></i> Tìm Kiếm
                </button>
                <button class="apax-btn full reset" @click="resetPrintInfo">
                  <i class="fa fa-refresh"></i> Lọc Lại
                </button>
                <router-link class="btn btn-warning btn-back" :to="'/forms'">Quay lại</router-link>                
                <button class="apax-btn full print" @click="exportExcel">
                  <i class="fa fa-file-excel-o"></i> Xuất Báo Cáo
                </button>               
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
            <i class="fa fa-file-text pdt-10"></i>
            <strong>BC02 - Danh sách</strong>
          </div>
            <div class="content-detail">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                  <tr class="text-sm">
                    <th>STT</th>
                    <th>Trung tâm</th>
                    <th>Cọc bỏ</th>
                    <th>Tổng số học sinh full phí</th>
                    <th>Tổng số học sinh trial</th>
                    <th>Tổng trial chuyển sang thanh toán</th>
                    <th>Tổng số học sinh checkin</th>
                    <th>Tổng full phí / tổng trial</th>
                    <th>Tổng full phí / tổng trial chuyển sang thanh toán</th>
                    <th>Tổng full phí / tổng checkin</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item, index) in results" :key="index">
                    <td>{{ index+1 }}</td>
                    <td>{{ item.name }}</td>
                    <td></td>
                    <td>{{ item.student_types.type1 }}</td>
                    <td>{{ item.student_types.type2 }}</td>
                    <td>{{ item.student_types.type4 }}</td>
                    <td>{{ item.student_types.type3 }}</td>
                    <td>{{ item.student_types.type1 | filterRatio(item.student_types.type2) }}</td>
                    <td>{{ item.student_types.type1 | filterRatio(item.student_types.type4) }}</td>
                    <td>{{ item.student_types.type1 | filterRatio(item.student_types.type3) }}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
    import u from '../../utilities/utility'
    import select from 'vue-select'
    import Datepicker from 'vue2-datepicker'
    import Multiselect from "vue-multiselect";

    export default {
        name: 'Report02',
        components: {
            Datepicker,
            Multiselect,
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

            }
        },
        created() {
            /*u.a().get(`/api/zones`).then(response => {
                this.zones = response.data
            })*/
            /*u.a().get(`/api/get-all-regions`).then(response => {
                this.regions = response.data
            })*/
            u.a().get(`/api/reports/branches`).then(response => {
                this.branches = response.data
            })

            u.a().get(`/api/all/products`).then(response => {
                this.products = response.data
            })
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
                        this.selectedBranche_name = this.branches[0].name;
                        this.selectedBranches = this.branches[0].id
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
                    date: this.date,
                    start: this.start,
                }
                u.a().post(`/api/reports/form-02`, data).then(response => {
                    this.results = response.data.data
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
                    tk: u.token(),
                    date: this.date,
                    start: this.start
                }

                let data_string = JSON.stringify(data);

                let url = `/api/exel/print-report-bc02/${data_string}`;
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