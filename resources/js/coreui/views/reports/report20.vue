<template>
    <div class="wrapper">
      <div class="animated fadeIn">
        <b-row>
            <b-col cols="12">
              <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <strong>BC20 - Báo cáo hiệu số Vận hành - Tổng hợp</strong>
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
                               <div class="col-2"><button :disabled="disabledSelectBranch" class="btn btn-info btn-print" @click="showBC20Modal">...</button></div>
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
                      </div>
                      <div class="col-2"></div>
                    </div>
                    <div class="row">
                      <div class="print-btn-group">
                        <button class="btn btn-success" @click="printReportBC20"><i class="fa fa-print"> &nbsp;Xuất báo cáo</i></button> &nbsp;
                        <button class="btn btn-default" @click="resetPrintInfo"><i class="fa fa-ban"> &nbsp;Bỏ lọc</i></button> &nbsp;
                        <router-link class="btn btn-warning btn-back" :to="'/forms'" >Quay lại</router-link><br>
                      </div>
                    </div>
                  </div>
                </b-card>
            </b-col>
        </b-row>
        <b-modal size="lg" id="showBC20" hide-header class="add-branchs" v-model="showBC20">
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
                            :onChange="selectRegion"
                            language="zh-CN"
                      ></vue-select>
                    </div>
                  </b-col>
                </b-row>
                <div class="row">
                   <p class="text-center selected-button">
                    <button class="btn btn-info"  @click="findBranches()">Tìm trung tâm</button>
                    <button class="btn btn-success"  @click="selectItem()">Chọn</button>
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
                      :search-options="{ enabled: true }"
                    ></vue-good-table> 
                  </b-col>  
                </b-row>
              </b-col>
            </b-row>
          </b-container>
          <div slot="modal-footer" class="w-100">
            <b-btn size="sm" 
                    class="float-right" 
                    variant="primary" 
                    @click="cancelBC20()"
            > Hủy </b-btn>
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
  name: 'Report20',
  components: {
    Datepicker,
    "vue-select": select
  },
  data () {
    return {
      showBC20: false,
      disabledZone: false,
      disabledRegion: false,
      disabledSelectBranch: false,
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
          sortable: true,
        },
        {
          label: 'Khu vực',
          field: 'age',
          sortable: true,
        }
      ],
      zones: [],
      zone: '',
      region: '',
      regions: [],
      branches: [],
      selectedBranches: [],
      lang:'en',
      from_date : '',
      to_date: '',
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
          const branch_id = this.branches[0].id;
          if(branch_id){
            this.selectedBranche_name = this.branches[0].name;
            this.selectedBranches.push(branch_id)
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
      }
      u.a().post(`/api/reports/form-20`, data).then(response => {
        this.results = response.data
      })
    },
    resetPrintInfo(){
      this.selectedBranches = ''
      this.from_date = ''
      this.to_date = ''
    },
    getBranchesByRegion(ids = []){
      alert('Hello wold')
    },
    selectItem(){
      // console.log(this.selectedBranches);
      this.showBC20 = false
    },
    showBC20Modal(){
      this.showBC20 = true
    },
    printReportBC20(){
      this.showBC20 = false;
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

      // console.log(`${br}, ${pd}, ${pro}`);
      let fd = moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
      let td = moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

      br = br? br : '_'
      fd = fd? fd : '_'
      td = td? td : '_'


      var p =`/api/exel/print-report-bc20/${br}/${fd}/${td}`
      window.open(p, '_blank');
      if(extraWindow){
          extraWindow.location.reload();
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
    cancelBC20(){
      this.resetBranches()
      this.showBC20 = false
    },
    closeModal(){
      this.cancelBC20()
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
  width: 75px;
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
  margin-left: 440px;
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
</style>