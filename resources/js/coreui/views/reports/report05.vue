<template>
    <div class="wrapper">
      <div class="animated fadeIn">
        <b-row>
            <b-col cols="12">
              <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <strong>BC05 - Hiệu suất phòng học, hiệu suất học sinh trên tổng sức chứa</strong>
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
                            <div class="col-8 form-group">
                             <div class="row">
                               <div class="col-10">
                                 <vue-select 
                                    label="id" 
                                    multiple
                                    placeholder="Mặc định chọn tất cả..."
                                    :options="selectedBranches" 
                                    v-model="selectedBranches" 
                                    :searchable="true" 
                                    language="zh-CN">
                                      
                                </vue-select>
                               </div>
                               <div class="col-2"><button class="btn btn-info btn-print" @click="showBC05Modal">...</button></div>
                             </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-3">
                              <strong>Sản phẩm: </strong>
                            </div>
                            <div class="col-8 form-group">
                              <vue-select 
                                    label="name" 
                                    multiple  
                                    :options="products" 
                                    v-model="selectedProducts" 
                                    :searchable="true" 
                                    language="zh-CN">
                                      
                              </vue-select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-3">
                              <strong>Chương trình: </strong>
                            </div>
                            <div class="col-8 form-group">
                              <vue-select 
                                    label="name" 
                                    multiple  
                                    :options="programs" 
                                    v-model="selectedPrograms" 
                                    :searchable="true" 
                                    language="zh-CN">
                                      
                              </vue-select>
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
                <button class="btn btn-success button-print" @click="printReportBC05"><i class="fa fa-print"> &nbsp;Xuất báo cáo</i></button>
                <div class="table-responsive scrollable">
                  <table class="table table-striped table-bordered apax-table">
                      <thead>
                        <tr class="text-sm">
                          <th>STT</th>
                          <th>Tên học sinh</th>
                          <th>Mã LMS</th>
                          <th>Mã EFFECT</th>
                          <th>Trung tâm học sinh</th>
                          <th>Chương trình</th>
                          <th>Gói học phí</th>
                          <th>Giá niêm yết</th>
                          <th>Tiền thực thu</th>
                          <th>Tiền đã đóng</th>
                          <th>Công nợ</th>
                          <th>Ngày thu tiền</th>
                          <th>Số ngày chờ hoàn phí</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>

                        </tr>
                      </tbody>
                  </table>
              </div>
              </div>
            </b-card>
          </b-col>
        </b-row>
        <!-- the modal below -->
        <b-modal size="lg" id="showBC05" hide-header class="add-branchs" v-model="showBC05">
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
                              :searchable="true" >
                      </vue-select>
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
                            language="zh-CN">  
                      </vue-select>
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
                    @click="cancelBC05()">
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

export default {
  name: 'Report05',
  components: {
    "vue-select": select
  },
  data () {
    return {
      showBC05: false,
      disabledZone: false,
      disabledRegion: false,
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
      lang:'en',
      from_date : '',
      to_date: ''
      
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

  },
  methods: {
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
        products: this.selectedProducts,
        programs: this.selectedPrograms
      }
      console.log(data);
      u.a().post(`/api/reports/form-05`, data).then(response => {
        this.results = response.data
      })
    },
    resetPrintInfo(){
      this.selectedBranches = ''
      this.selectedProducts = ''
      this.selectedPrograms = ''
    },
    getBranchesByRegion(ids = []){
      alert('Hello wold')
    },
    selectItem(){
      // console.log(this.selectedBranches);
      this.showBC05 = false
    },
    showBC05Modal(){
      this.showBC05 = true
    },
    printReportBC05(){
      this.showBC05 = false;
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

      console.log(`${br}, ${pd}, ${pro}`);

      br = br? br : '_'
      pd = pd ? pd : '_'
      pro = pro? pro : '_'

      var p =`/api/exel/print-report-bc05/${br}/${pd}/${pro}`
      window.open(p, '_blank')
      if(extraWindow){
          extraWindow.location.reload();
      }
    },
    cancelBC05(){
      this.resetBranches()
      this.showBC05 = false
    },
    closeModal(){
      this.cancelBC05()
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
  width: 60px;
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
}
</style>