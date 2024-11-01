<template>
  <div class="ada-table-filter apax-branches-filter" :id="id" :class="custom_class">
    <abt
      :markup="'primary'"
      :icon="'fa fa-plus'"
      label="Chọn Trung Tâm Áp Dụng"
      title="Chọn các trung tâm được áp dụng cấu hình"
      :disabled="disableAdd"
      :onClick="addBranchs"
      >
    </abt>
    <ul v-show="check_list.length" class="checked-branches-list col-md-12">
      <li v-for="(item, index) in check_list" :key="index" class="row">
        <span class="item-branch-id col-md-2" @click="remove(item.id)">{{ item.id }}</span>
        <span class="item-branch-name col-md-9" @click="remove(item.id)"><i class="fa fa-remove"></i> {{ item.name }}</span>
      </li>
    </ul>
    <b-modal size="lg" 
      :ok-variant="'success'"
      :title="'Chọn trung tâm áp dụng'" 
      :class="`modal-filter modal-primary ${custom_class}`" 
      v-model="show" 
      @ok="show=false" 
      @close="show=false">
      <div class="ada-table table-responsive header-filter" id="apax_branches_list">
        <table class="ada-table table-bordered">
          <thead>
            <tr>
              <th align="center">#</th>
              <th align="center">ID</th>
              <th align="center">LMS</th>
              <th align="center">Tên Trung Tâm </th>
              <th align="center">Khu Vực</th>
              <th align="center">Vùng</th>                      
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td class="checkbox-item checkbox-item filter-row"><input id="check-all" @click="checkAll()" name="Check-all" type="checkbox" :disabled="disabled" /></td>
              <td class="id-filter filter-row"><input type="text" placeholder="ID" @input="search('id', $event.target.value)" :v-model="branch_id" :disabled="disabled" /></td>
              <td class="lms-filter filter-row"><input type="text" placeholder="LMS" @input="search('lms', $event.target.value)" :v-model="branch_lms" :disabled="disabled" /></td>
              <td class="name-filter filter-row"><input type="text" placeholder="Tên trung tâm" @input="search('name', $event.target.value)" :v-model="branch_name" :disabled="disabled" /></td>
              <td class="zone-filter filter-row">
                <select @change="find('zone')" v-model="zone_id" :disabled="disabled">
                  <option value="0">Khu vực</option>
                  <option v-for="(zone, index) in zones" :key="index" :value="zone.id">{{ zone.name }}</option>
                </select>
              </td>
              <td class="region-filter filter-row">
                <select @change="find('region')" v-model="region_id" :disabled="disabled">
                  <option value="0">Vùng</option>
                  <option v-for="(region, index) in regions" :key="index" :value="region.id">{{ region.name }}</option>
                </select>
              </td>                      
            </tr>
          </tbody>
        </table>
      </div>
      <div class="ada-table content-wrapper table-responsive scrollable">
        <div class="ada-table content-frame table-content">
          <table class="ada-table table-bordered table-striped content-detail">
            <tbody>
              <tr v-for="(branch, index) in branches" :key="index">
                <td align="center" class="checkbox-item" @click="checking(branch)"><input v-model="check_list" :value="branch" :id="`branch_id${branch.id}`" type="checkbox" readonly /></td>
                <td align="right" class="id-filter" @click="checking(branch)">{{ branch.id }}</td>
                <td align="right" class="lms-filter" @click="checking(branch)">{{ branch.lms }}</td>
                <td align="left" class="name-filter" @click="checking(branch)">{{ branch.name }}</td>
                <td align="center" class="zone-filter" @click="checking(branch)">{{ branch.zone }}</td>
                <td align="center" class="region-filter" @click="checking(branch)">{{ branch.region }}</td>                      
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>

import u from '../../utilities/utility'
import abt from './Button'

export default {
  name: 'bracnh-filter',
  components: {
    abt
  },
  data () {
    return {
      disableAdd: true,
      show: false,
      zone_id: 0,
      region_id: 0,
      branch_id: 0,
      branch_lms: 0,
      check_list: [],
      branch_name: '',
      keyword: '',
      cache: [],
      zones: [],
      regions: [],
      branches: []
    }
  },
  props: {
    id: {
      type: String,
      default: null
    },
    custom_class: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    real: {
      type: Boolean,
      default: false
    },
    checked_list: {
      type: Array,
      default: () => []
    },
    result: {
      type: Function,
      default: () => []
    }
  },
  created () {
    this.start()
  },
  methods: {
    start () {
      u.g(`/api/settings/branches/config/default`).then(response => {        
        this.branches = response.branches
        this.regions = response.regions
        this.zones = response.zones
        this.cache = response
        this.check_list = this.checked_list
        this.disableAdd = false
        setTimeout(()=>{
          this.scan()
        },10)
      })
    },
    search(name='', value='') {
      if (this.real) {
        if (value.length > 3 && (value.length % 3) === 0) {
          const data = {            
            id: name === 'id' && value !== '' ? value : '',
            lms: name === 'lms' && value !== '' ? value : '',
            name: name === 'name' && value !== '' ? value : '',
            zone: parseInt(this.zone_id),
            region: parseInt(this.region_id)
          }
          u.g(`/api/settings/branches/${JSON.stringify(data)}`).then(response => {            
            this.branches = response
            setTimeout(()=>{
              this.scan()
            },10)
          })
        } else if (value.length === 0 || value === '') {
          this.branches = this.cache.branches
          setTimeout(()=>{
            this.scan()
          },10)
        }
      } else {
        this.branches = this.cache.branches.filter(item => 
          (name === 'name' && item.name.toLowerCase().indexOf(value.toLowerCase()) > -1) 
          || (name === 'id' && parseInt(item.id, 10) === parseInt(value, 10)) 
          || (name === 'lms' && parseInt(item.lms, 10) === parseInt(value, 10)))
        setTimeout(()=>{
          this.scan()
        },10)
      }      
    },
    addBranchs() {
      this.show = true;
      this.find()
    },
    find(name='') {
      if (this.real) {
        const data = {            
          id: name === 'id' && value !== '' ? value : '',
          lms: name === 'lms' && value !== '' ? value : '',
          name: name === 'name' && value !== '' ? value : '',
          zone: parseInt(this.zone_id, 10),
          region: parseInt(this.region_id, 10)
        }
        u.g(`/api/settings/branches/${JSON.stringify(data)}`).then(response => {
          this.branches = response;
          setTimeout(()=>{
            this.scan()
          },10)
        })
      } else {
        if (name === 'zone') {
          this.region = ''
          const zone = parseInt(this.zone_id, 10) === 1 ? 'Hà Nội' : 'Hồ Chí Minh'
          this.branches = this.cache.branches.filter(item => item.zone && item.zone.toString().toLowerCase().indexOf(zone.toLowerCase()) > -1) 
        } else if (name === 'region') {
          this.zone_id = ''
          const region  = `Vùng ${parseInt(this.region_id, 10)}`
          this.branches = this.cache.branches.filter(item => item.region && item.region.toString().toLowerCase().indexOf(region.toLowerCase()) > -1) 
        }
        setTimeout(()=>{
          this.scan()
        },10)
      }
    },
    checking (branch) {
      const checked_items = this.check_list.filter(item => item.id === branch.id)
      if (checked_items.length) {
        this.check_list = this.check_list.filter(item => item.id !== branch.id)
      } else {
        this.check_list.push(branch)
      }
      setTimeout(()=>{
        this.scan()
      },10)
    },
    remove(id) {
      this.check_list = this.check_list.filter(item => item.id !== id)
      this.result ? this.result(this.check_list) : []
      this.$emit('result', this.check_list)
    },
    checkAll () {
      if (this.branches) {
        if ($('#check-all').prop("checked")) {
          $.each(this.branches, (index, branch) => {
            $(`#branch_id${branch.id}`).prop('checked', true)
          })
          this.check_list = this.branches;
        } else {
          $.each(this.branches, (index, branch) => {
            $(`#branch_id${branch.id}`).prop('checked', false)
          })
          this.check_list = []
        }
        setTimeout(()=>{
          this.scan()
        },10)
      }
    },
    reset () {
      this.check_list = this.checked_list
    },
    scan () {
      $.each(this.branches, (index, branch) => {
        let checked = true
        if (this.check_list.length) {
          const search = this.check_list.filter(item => parseInt(item.id, 10) === parseInt(branch.id, 10))
          checked = search.length ? true : false          
          if (checked) {          
            $(`#branch_id${branch.id}`).prop('checked', true)
          } else {          
            $(`#branch_id${branch.id}`).prop('checked', false)
          }
        } else {
          $(`#branch_id${branch.id}`).prop('checked', false)
        }      
      })
      this.result ? this.result(this.check_list) : []
      this.$emit('result', this.check_list)
    }
  }
}
</script>

<style lang="scss" scoped>
  .modal-filter {
    text-align: center;
  }
  .ada-table-filter {
    color: #555;
    font-size: 11px;
    font-weight: 400;
  }
  .item-branch-id {
    text-align: right;
  }
  ul.checked-branches-list {
    width: 100%;
    float: left;
    overflow-y: auto;
    overflow-x: auto;
    max-height: 360px;
    border: 1px solid #ccc;
    margin: 15px 0;
    border-radius: 1px;
  }
  ul.checked-branches-list li:first-child {
    padding: 10px 0 0 0;
  }
  ul.checked-branches-list li span {
    padding: 0 0 7px 0;
    margin: 0 0 7px 0;
    font-weight: 500;
    font-size: 10px;
    color: #666;
    cursor: pointer;
    border-bottom: 1px solid #d6e1ec;
  }
  ul.checked-branches-list li:hover span {
    color: #d20000;
    text-shadow: 0 1px 1px #999;
  }
  ul.checked-branches-list li span.item-branch-name {
    margin: 0 0 7px 2%;
  }
  .ada-table-filter table tr th {
    text-align: center;
    text-transform: uppercase;
    padding: 7px 0px;
    text-shadow: 0 1px 1px #111;
    font-size: 11px;
    font-weight: 500;
    background: #174365;
    color: #FFF;
  }
  .ada-table-filter table tr td {
    
  }
  .apax-branches-filter {

  }
  .ada-table.header-filter {
    overflow: hidden;
    padding: 10px 0 0 0;
    height: 77px;
    z-index: 999;
    width: 612px;
    margin: 0 0 -11px 80px;
    position: relative;
  }
  .ada-table .header-filter table {
    
  }
  .ada-table .header-filter table tr {
    
  }
  .ada-table .header-filter table tr th {
    
  }
  .ada-table .header-filter table tr td {
    
  }  
  .ada-table .header-filter table tr td input {
    
  }
  .ada-table .header-filter table tr td select {
    
  }
  .ada-table.content-wrapper {
    overflow: hidden;
    z-index: 9;
    width: 612px;
    padding: 0;
    margin: 0 0 0 80px;
    position: relative;
  }
  .ada-table.content-frame {
    overflow-y: auto;
    max-height: 318px;
  }
  ul.checked-branches-list::-webkit-scrollbar, .ada-table.content-frame::-webkit-scrollbar {
    width: 5px;
  }
  ul.checked-branches-list::-webkit-scrollbar-track, .ada-table.content-frame::-webkit-scrollbar-track {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
      -webkit-border-radius: 10px;
      border-radius: 10px;
      width:5px;      
  }
  ul.checked-branches-list::-webkit-scrollbar-thumb, .ada-table.content-frame::-webkit-scrollbar-thumb {
      background-color: rgb(255, 86, 86);	
	    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .2) 25%,
											  transparent 25%,
											  transparent 50%,
											  rgba(255, 255, 255, .2) 50%,
											  rgba(255, 255, 255, .2) 75%,
											  transparent 75%,
											  transparent);
      width:5px;
  }
  ul.checked-branches-list::-webkit-scrollbar-thumb:window-inactive, .ada-table.content-frame::-webkit-scrollbar-thumb:window-inactive {
    background: rgba(155, 155, 155, 0.4); 
    width:5px;
  }
  table.ada-table.content-detail tr td {
    padding: 5px;
    border-bottom: 1px solid #ccc;
  }
  .ada-table-filter table .checkbox-item {
    width: 25px;
    border:none;
    padding: 6px 0 0 0!important;
    text-align: center;
  }
  .ada-table-filter table .id-filter {
    width: 45px;
  }
  .ada-table-filter table .id-filter input {
    width: 43px;
    border:none;
    text-align: center;
  }
  .ada-table-filter table .lms-filter {
    width: 59px;
  }
  .ada-table-filter table .lms-filter input {
    height: 23px;
    width: 57px;
    border:none;
    text-align: center;
  }
  .ada-table-filter table .name-filter {
    width: 315px;
  }
  .ada-table-filter table .name-filter input {
    height: 23px;
    width: 313px;
    border:none;
    text-align: center;
  }
  .ada-table-filter table .zone-filter {
    width: 88px;
  }
  .ada-table-filter table .zone-filter select {
    height: 23px;
    width: 86px;
    border:none;
    text-align: center;
  }
  .ada-table-filter table .region-filter {
    width: 65px;
  }
  .ada-table-filter table .region-filter select {
    height: 23px;
    width: 63px;
    border:none;
    text-align: center;
  }
  .ada-table.content-detail tr td {
    cursor: pointer;
    font-weight: 500;
    color: #666;
  }
  .ada-table.content-detail tr:hover td {
    color: #ffffff;
    text-shadow: 0 1px 1px #111;
    background: #6aaae6;
  }
  .ada-table.content-detail tr td.id-filter {
    width: 47px;
  }
  .ada-table.content-detail tr td.lms-filter {
    width: 60px;
  }
  .ada-table.content-detail tr td.name-filter {
    width: 315px;
    padding: 5px 0 5px 10px;
  }
  .ada-table.content-detail tr td.region-filter {
    width: 67px;
  }
</style>
