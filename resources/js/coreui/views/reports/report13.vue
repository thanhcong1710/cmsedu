<template>
  <div class="wrapper">
    <div class="animated fadeIn">
      <b-row>
        <b-col cols="12">
          <b-card
                  header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>BC13 - Báo cáo công nợ</strong>
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
                  </div>
                </div>               
              </div>
              <br>
              <div slot="footer" class="text-center">
                <button class="apax-btn full detail" @click="viewPrintInfoSearch">
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
            <strong>BC03 - Danh sách</strong>
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
                    <th>Lớp</th>
                    <th>Gói học phí</th>
                    <th>Loại thu phí</th>
                    <th>Gía niêm yết</th>
                    <th>Tổng số tiền giảm trừ</th>
                    <th>Số tiền thực thu</th>
                    <th>Số tiền đã đóng</th>
                    <th>Công nợ</th>
                    <th>Ngày đóng tiền <br> (gần nhất)</th>
                    <th>Người thu tiền <br> (gần nhất)</th>
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
                    <td>{{ item.class_name }}</td>
                    <td>{{ item.tuition_fee_name }}</td>
                    <td>{{ item.payload | payloadType}}</td>
                    <td>{{ item.tuition_fee_price | formatMoney}}</td>
                    <td>{{ (item.tuition_fee_price - item.must_charge) | formatMoney}}</td>
                    <td>{{ item.must_charge | formatMoney}}</td>
                    <td>{{ item.total_charged | formatMoney}}</td>
                    <td>{{ item.debt_amount | formatMoney}}</td>
                    <td>{{ item.newest_payment_time }}</td>
                    <td>{{ item.creator }}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="text-center paging">
            <nav aria-label="Page navigation">
              <paging
                :rootLink="pagination.url"
                :id="pagination.id"
                :listStyle="pagination.style"
                :customClass="pagination.class"
                :firstPage="pagination.spage"
                :previousPage="pagination.ppage"
                :nextPage="pagination.npage"
                :lastPage="pagination.lpage"
                :currentPage="pagination.cpage"
                :pagesItems="pagination.total"
                :pagesLimit="pagination.limit"
                :pageList="pagination.pages"
                :routing="changePage"
              ></paging>
            </nav>
            <select
              class="form-control paging-limit"
              v-model="pagination.limit"
              @change="viewPrintInfo"
              style="width: auto;height: 30px !important;border: 0.5px solid #dfe3e6 !important;padding-top: 5px;"
            >
              <option
                v-for="(item, index) in pagination.limitSource"
                v-bind:value="item"
                v-bind:key="index"
              >{{ item }}</option>
            </select>
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
    import paging from "../../components/Pagination";
    import Multiselect from "vue-multiselect";

    export default {
        name: 'Report02',
        components: {
            Datepicker,paging,
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
                pagination: {
                  url: "",
                  id: "",
                  style: "line",
                  class: "",
                  spage: 1,
                  ppage: 1,
                  npage: 0,
                  lpage: 1,
                  cpage: 1,
                  total: 0,
                  limit: 20,
                  limitSource: [10, 20, 30, 40, 50],
                  pages: []
                }

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
            changePage(link) {
              const info = link.toString().split("/");              
              const page = info.length > 1 ? info[1] : 1;
              console.log(page)
              this.pagination.cpage = parseInt(page);
              this.viewPrintInfo();
            },
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
            viewPrintInfoSearch()
            {
              this.pagination.cpage = 1
              this.viewPrintInfo()

            },
            printReportBC03(){
              this.showBC03 = false;
              var br =''
              for(var t in this.selectedBranchID){
                br += this.selectedBranchID[t]+','; 
              }
              br = br.slice(0, -1)
              
              var pd = ''
              for (var c in this.selectedProducts){
                pd += this.selectedProducts[c].id+',';
              }
              pd = pd.slice(0, -1)

              var pro = ''
              for (var p in this.selectedPrograms){
                pro += this.selectedPrograms[p].id+','
              }
              pro = pro.slice(0,-1)

              let fd = moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
              let td = moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

              br = br? br : '_'
              pd = pd ? pd : '_'
              pro = pro? pro : '_'
              fd = fd? fd : '_'
              td = td? td : '_'
              let branches = '';
              if (this.selectedBranches.length) {
                  for (var i in this.selectedBranches) {
                      branches += "'"+(this.selectedBranches[i].id)+"',"
                  }
              }else
              {
                branches = '_';
              }               
              var p =`/api/exel/print-report-bc13/${branches}/${fd}/${td}`
              window.open(p, '_blank')
              if(extraWindow){
                  extraWindow.location.reload();
              }
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
                    limit: this.pagination.limit,
                    page: this.pagination.cpage,
                }
                u.a().post(`/api/reports/form-13`, data).then(response => {
                    this.results = response.data.data.list
                    this.pagination.total = response.data.data.total_record;
                    this.pagination.lpage = Math.ceil(
                      this.pagination.total / this.pagination.limit
                    );
                    this.pagination.ppage = this.pagination.cpage > 0 ? this.pagination.cpage - 1 : 0;
                    this.pagination.npage = this.pagination.cpage + 1;
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
                    tk: u.token()         
                }

                let data_string = JSON.stringify(data);

                let url = `/api/exel/print-report-bc13/${data_string}`;
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