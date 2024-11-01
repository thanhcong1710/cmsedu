<template>
    <div class="wrapper">
      <div class="animated fadeIn">
        <b-row>
            <b-col cols="12">
              <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <strong>BC17A - Báo cáo phân loại vùng</strong>
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
                              <strong>Vùng: </strong>
                            </div>
                            <div class="col-9 form-group">
                             <div class="row">
                               <div class="col-12" v-if="showRegion">
                                  <vue-select 
                                        label="name" 
                                        multiple  
                                        :options="regions"
                                        placeholder="Mặc định chọn tất cả..."
                                        v-model="selectedRegions" 
                                        :searchable="true" 
                                        language="zh-CN"
                                  ></vue-select>
                               </div>
                               <div class="col-12" v-else>
                                 <input type="text" class="form-control" v-model="selectedRegion_name" readonly>
                               </div>
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
                                  <!-- <input type="date" v-model="to_date"> -->
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
                  <strong>Danh sách vùng tốt nhất</strong>
                  
              </div>
              <div class="content-detail">
                <button class="btn btn-success button-print" @click="printReportBC17a"><i class="fa fa-print"> &nbsp;Xuất báo cáo</i></button>
                <div class="table-responsive scrollable">
                  <table class="table table-striped table-bordered apax-table">
                      <thead>
                        <tr class="text-sm">
                          <th>STT</th>
                          <th>Vùng</th>
                          <th>Loại</th>
                          <th>Bình quân doanh số vùng</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in results " :key="index">
                          <td>{{ index+1 }}</td>
                          <td>{{item.name}}</td>
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
  name: 'Report17a',
  components: {
    Datepicker,
    "vue-select": select
  },
  data () {
    return {
      disabledZone: false,
      disabledRegion: false,
      showRegion: true,
      selectedRegions: [],
      result3: '',
      printResults: [],
      highlighted: {
        // days: [6, 0] // Highlight Saturday's and Sunday's
      },
      from_date: '',
      to_date: '',  
      region: '',
      regions: [],
      best_branches: '',
      worst_branches: '',
      lang:'en',
      results: [],
      user_id: '',
      selectedRegion_name: ''

    }
  },
  created() {
    var x = u.session().user.code.split("-")
    this.user_id = x[0];
    this.role_id = u.session().user.role_id
    const role_id = this.role_id
    // console.log('this = role id', this.role_id);
    u.a().get(`/api/roles/${role_id}/get-all-regions`).then(response => {
      this.regions = response.data
      console.log('region information', this.regions);
      const l  = this.regions.length
      if(l <=1){
        this.showRegion = false 
        this.selectedRegion_name = this.regions[0].name
        // console.log('test region name', this.selectedRegion_name);
        this.selectedRegions.push(this.regions)
        // console.log('test', this.selectedRegions);
      }
    })
    this.getDefaultDate()
    this.checkUserRoleForRegion(role_id)
  },
  methods: {
    customFormatter(date) {
      return moment(date).format('YYYY-MM-DD');
    },
    selectRegion(e){
      if(e != ''){
      this.disabledZone = true
        
      }
    },
    checkUserRoleForRegion(role_id){
      // u.a().get(`/api/roles/${role_id}get-all-regions`).then(response => {
      //   this.regions = response.data
      //   console.log(this.regions);
      // })
    },
    viewPrintInfo(){
      const data = {
        regions: this.selectedRegions,
        fromDate: moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        toDate: moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
      }
      console.log(data);
      u.a().post(`/api/reports/form-17a`, data).then(response => {
        this.results = response.data
      })
    },
    resetPrintInfo(){
      this.selectedRegions = ''
      this.from_date = ''
      this.to_date = ''
    },
    getBranchesByRegion(ids = []){
      alert('Hello wold')
    },
    selectItem(){
      // console.log(this.selectedBranches);
      this.showBC15 = false
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
    printReportBC17a(){
      var r =''
      for(var t in this.selectedRegions){
        r += this.selectedRegions[t].id+','; 
      }
      r = r.slice(0, -1)

      var fd = moment(this.from_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

      var td = moment(this.to_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

      r = r? r : '_'
      fd = fd ? fd : '_'
      td = td ? td : '_'

      var p =`/api/exel/print-report-bc17a/${r}/${fd}/${td}`
      window.open(p, '_blank');
      if(extraWindow){
          extraWindow.location.reload();
      }
    }
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