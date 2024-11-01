<template>
    <div class="wrapper">
      <div class="animated fadeIn">
        <b-row>
            <b-col cols="12">
              <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <strong>BC17C - Báo cáo phân loại nhân viên</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
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
                               <div class="col-2"><button :disabled="disabledSelectBranch" class="btn btn-info btn-print" @click="showBC17cModal">...</button></div>
                             </div>
                            </div>
                          </div>
                          <div class="row date-block">
                            <div class="col-7">
                              <div class="row">
                                <div class="col-5"><strong>Từ ngày</strong></div>
                                <div class="col-6">
                                  <!-- <input type="date" v-model="from_date"> -->
                                  <datepicker   
                                      v-model="from_date"
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
                            <div class="col-5">
                              <div class="row">
                                <div class="col-4"><strong>Đến ngày</strong></div>
                                <div class="col-8">
                                  <datepicker   
                                        v-model="to_date"
                                        :lang="lang"
                                        input-class="form-control bg-white"
                                        placeholder="Chọn ngày kết thúc"
                                        :bootstrapStyling="true" 
                                  ></datepicker>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row input-number" v-show="false">
                            <div class="col-7">
                              <div class="row">
                                <div class="col-5"><strong>Best Leaders</strong></div>
                                <div class="col-6">
                                  <input type="number" v-model="best_leaders" class="form-control input-text-left" placeholder="Best leaders...">
                                </div>
                              </div>
                            </div>
                            <div class="col-5" v-show="false">
                              <div class="row">
                                <div class="col-5"><strong>Worst Leaders</strong></div>
                                <div class="col-7">
                                  <input type="number" v-model="worst_leaders" class="form-control input-text-right" placeholder="Worst leaders...">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row input-number">
                            <div class="col-7">
                              <div class="row">
                                <div class="col-5"><strong>Best Staffs</strong></div>
                                <div class="col-6">
                                  <input type="number" @input="disableGoodSale" v-model="best_staffs" :disabled="this.disableGoodEc" class="form-control input-text-left" placeholder="Best staffs...">
                                </div>
                              </div>
                            </div>
                            <div class="col-5">
                              <div class="row">
                                <div class="col-5"><strong>Worst Staffs</strong></div>
                                <div class="col-7">
                                  <input type="number" v-model="worst_staffs" :disabled="this.disableBadEc" @input="disableBadSale" class="form-control input-text-right" placeholder="Worst staffs...">
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
                      <div class="col-2"></div>
                    </div>
                    <div class="row">
                      <div class="print-btn-group">
                        <button class="btn btn-info" @click="viewPrintInfo"><i class="fa fa-eye"> &nbsp;Xem trước</i></button> &nbsp;
                        <button class="btn btn-default" @click="resetPrintInfo"><i class="fa fa-ban"> &nbsp;Bỏ lọc</i></button> &nbsp;
                        <router-link class="btn btn-warning btn-back" :to="'/forms'" >Quay lại</router-link><br>
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
                  
              </div>
              <div class="content-detail">
                <button class="btn btn-success button-print" @click="printReportBC17c"><i class="fa fa-print"> &nbsp;Xuất báo cáo</i></button>
                <div class="table-responsive scrollable">
                  <table class="table table-striped table-bordered apax-table">
                      <thead>
                        <tr class="text-sm">
                          <th>STT</th>
                          <th>Mã nhân viên</th>
                          <th>Tên nhân viên</th>
                          <th>Vị trí</th>
                          <th>Loại</th>
                          <th>Doanh số</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in results" :key="index">
                          <td>{{ index+1 }}</td>
                          <td>{{item.hrm_id}}</td>
                          <td>{{item.name}}</td>
                          <td>{{item.position}}</td>
                          <td>{{item.type}}</td>
                          <td>{{item.total_amount|formatCurrency2}}</td>
                        </tr>
                      </tbody>
                  </table>
              </div>
              </div>
            </b-card>
          </b-col>
        </b-row>
        <!-- the modal below -->
        <b-modal size="lg" id="showBC17c" hide-header class="add-branchs" v-model="showBC17c">
          <span class="close" @click="closeModal">x</span>
          <h5 class="title-modal-fix text-center">Chọn trung tâm áp dụng</h5><hr>
          <b-container fluid >
            <b-row class="mb-1">
              <b-col cols="12">
                <b-row>
                  <b-col cols="3" class="title-search">Khu vực</b-col><br>
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
                  <b-col cols="3" class="title-search">Vùng</b-col><br>
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
                    <button class="btn btn-info"  @click="findBranches()">Tìm trung tâm</button>
                    <button class="btn btn-success" @click="selectItem()">Chọn</button>
                    <button class="btn btn-warning"  @click="resetBranches()">Reset</button>
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
                    @click="cancelBC17c()">
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
import axios from 'axios'
import u from '../../utilities/utility'
import select from 'vue-select'
import Datepicker from 'vue2-datepicker'
import moment from 'moment'

export default {
  name: 'Report17c',
  components: {
    Datepicker,
    "vue-select": select
  },
  data () {
    return {
      showBC17c: false,
      disableGoodEc: false,
      disableBadEc: false,
      disabledZone: false,
      disabledRegion: false,
      disabledSelectBranch: false,
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
      from_date: '',
      to_date: '',
      best_leaders: null,
      worst_leaders: null,
      best_staffs: null,
      worst_staffs: null,
      lang:'en',
      results: [],
      role_branch: '',
      selectedBranche_name: ''
      
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

    u.a().get(`/api/all/products`).then(response =>{
      this.products = response.data
    })

    u.a().get(`/api/all/programs/`).then(response =>{
      this.programs = response.data
    })
    this.checkRole()
    this.getDefaultDate()

  },
  methods: {
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
          this.selectedBranche_name = this.branches[0].name
          const selected_branch_id = this.branches[0].id
          if(selected_branch_id){
            this.selectedBranches.push(selected_branch_id);
          }
        }
      })
    },
    selectZone(e){
      if(e != ''){
        this.disabledRegion = true
      }
    },
    selectRegion(e){
      if(e != ''){
      this.disabledZone = true
        
      }
    },
    findBranches(){
      let data = {
        zone: this.zone,
        region: this.region
      }
      if(data.zone == '' && data.region == ''){
        data = this.regions
      } else if(data.zone != ''){
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
    resetBranches(){
      this.zone = ''
      this.region = ''
      this.disabledZone = false
      this.disabledRegion = false
    },
    viewPrintInfo(){
      const data = {
        branches: this.selectedBranches,
        fromDate: moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        toDate: moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        best_leaders: this.best_leaders,
        worst_leaders: this.worst_leaders,
        best_staffs: this.best_staffs,
        worst_staffs: this.worst_staffs
      }
      u.a().post(`/api/reports/form-17c`, data).then(response => {
        this.results = response.data
      })
    },
    resetPrintInfo(){
      this.selectedBranches = ''
      this.selectedProducts = ''
      this.selectedPrograms = ''
      this.from_date = ''
      this.to_date = ''
      this.best_leaders = ''
      this.worst_leaders = ''
      this.best_staffs = ''
      this.worst_staffs = ''
      this.disableGoodEc = false
      this.disableBadEc = false
    },
    getBranchesByRegion(ids = []){
      alert('Hello wold')
    },
    selectItem(){
      // console.log(this.selectedBranches);
      this.showBC17c = false
    },
    showBC17cModal(){
      this.showBC17c = true
    },
    printReportBC17c(){
      this.showBC17c = false;
      var br =''
      for(var t in this.selectedBranches){
        br += this.selectedBranches[t]+','; 
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

      var fd = moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

      var td = moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
      var bestLead = this.best_leaders
      var worstLead = this.worst_leaders
      var bestStaff = this.best_staffs
      var worstStaff = this.worst_staffs

      // console.log(`${br}, ${pd}, ${pro}`);

      br = br? br : '_'
      pd = pd ? pd : '_'
      pro = pro? pro : '_'
      fd = fd? fd : '_'
      td = td? td : '_'
      bestLead = bestLead? bestLead : '_'
      worstLead = worstLead? worstLead : '_'
      bestStaff = bestStaff? bestStaff : '_'
      worstStaff = worstStaff? worstStaff : '_'

      var p =`/api/exel/print-report-bc17c/${br}/${fd}/${td}/${bestLead}/${worstLead}/${bestStaff}/${worstStaff}`
      window.open(p, '_blank');
      if(extraWindow){
          extraWindow.location.reload();
      }
    },
    cancelBC17c(){
      this.resetBranches()
      this.showBC17c = false
    },
    closeModal(){
      this.cancelBC17c()
    },
    selectAll(selected, selectedRows) {
      // selected indicates whether select all 
      // was selected or unselected
      // const data = selectedRows
      // console.log(selected, selectedRows);
      const data = {
        selected: selected,
        rows: selectedRows
      }
      // console.log(data.selected.selectedRows);
      // this.selectedBranches = data.selected.selectedRows
      let ids = []
      const selectedItems = data.selected.selectedRows
      for(var i = 0; i< selectedItems.length; i++){
        ids.push(selectedItems[i].id);
      }
      this.selectedBranches = ids;
      // selectedRows contains all selected rows
    },
    disableGoodSale(){
      if(this.best_staffs != ''){
        this.disableBadEc = true
      }
    },
    disableBadSale(){
      if(this.worst_staffs != ''){
        this.disableGoodEc = true
      }
    },
    getDefaultDate(){
      let from_date =  new Date();
      // var lastDay = new Date(y, m + 1, 0);
      this.from_date = moment(from_date, 'DD/MM/YYYY').format('YYYY-MM-01')
      let lastDay = moment(lastDay).format('YYYY-MM-DD');
      // let lastDay = new Date(from_date.getFullYear(), from_date.getMonth() + 1, 0);
      const endOfMonth   = moment().endOf('month').format('YYYY-MM-DD');
      this.to_date = endOfMonth
      // console.log(endOfMonth);
    },
    toggleSelectRow(params) {
      // row that was clicked
      // console.log(params.row);
      // index of page
      const data = params.row

      // console.log(data.id);
      let selected_id = data.id

      let selectedBranches = []

      selectedBranches = this.selectedBranches

      if(this.selectedBranches.indexOf(selected_id) === -1){
        this.selectedBranches.push(selected_id);
        // var a =selectedBranches
        // this.selectedBranches = selectedBranches
      } 

      console.log(selectedBranches);

    },
    
  }
}
</script>

<style scoped>
.btn-print{
  width: 70px;
  margin-left: -16px;
}
.mt-30{
  margin-top: 30px;
}
.selected-button{
  margin: auto;
  margin-top: 30px;
  margin-bottom: 30px;
}
.btn-back{
  /*margin-left: 740px;*/
  display: inline;

}
.button-print{
  margin-left: 800px;
  /*margin-top: 0px;*/
  /*padding: 5px;*/

}
.close{
  cursor: pointer;
  margin-top: -10px;
}
.btn-block{

}
.print-btn-group{
  margin-left: 480px;
  margin-top: 50px;
}
.input-group{
  /*margin-left: 50px;*/
}
.time-picker{

}
.vdp-datepicker{
  margin-left: 6px;
}
.input-number{
  margin-top: 25px;
}
strong {
  font-size: 12px;
  text-align: left;
}
.input-text-right{
  width: 160px;
  margin-left: -19px; 
}
.input-text-left{
  width: 170px;
  margin-left: 5px; 
}
</style>