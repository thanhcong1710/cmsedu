<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong> <i class="fa fa-filter"></i> Bộ lọc</strong>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="filter-label control-label">Mã đơn vị cơ sở</label><br>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo mã đơn vị cơ sở" class="fa fa-search"></i>
                </p>
                <input type="text" v-model="search.hrm_id" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="filter-label control-label">Tên trung tâm</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên trung tâm" class="fa fa-bank"></i>
                </p>
                <input type="text" v-model="search.name" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã Vùng</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo mã vùng" class="fa fa-globe"></i>
                </p>
                <select v-model="search.region_id" class="filter-selection customer-type form-control" id="">
                  <option value="" disabled>Lựa chọn mã vùng</option>
                  <option :value="region.id" v-for="(region, index) in regions">{{region.name}}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã Cyber</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo mã Cyber" class="fa fa-usd"></i>
                </p>
                <input type="text" v-model="search.accounting_id" class="filter-selection customer-type form-control">
              </div>
            </div>
            <!--<div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Mã LMS</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo mã effect" class="fa fa-search"></i>
                </p>
                <input type="text" v-model="search.brch_id" class="filter-selection customer-type form-control">
              </div>
            </div>-->

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                  <option value="" disabled>Lựa chọn trạng thái</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>


          </div>
          <div slot="footer" class="text-center">
            <router-link :to="'/branches/add-branch'" class="apax-btn full reset"><i class="fa fa-plus"></i> Thêm mới</router-link>
            <button class="apax-btn full detail" @click.prevent="searchBranch"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>  
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách cơ sở</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Mã đơn vị cơ sở</th>
                <!--<th class="width-150">Mã LMS</th>-->
                <th class="width-150">Mã Cyber</th>
                <th class="width-150">Tên trung tâm / Chi tiết</th>
                <th class="width-150">Ngày khai trương</th>
                <th class="width-150">Vùng</th>
                <th class="width-150">Khu vực</th>
                <th class="width-150">Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <!-- branch data -->
              <tr v-for="(branch, index) in branches" :key="index">
                <td>{{indexOf(index+1)}}</td>
                <td class="text-center cl-btn">
                  {{ branch.hrm_id }}

                </td>
                <!--<td>{{ branch.brch_id }}</td>-->
                <td>{{ branch.accounting_id }}</td>
                <td>{{ branch.name }}</td>
                <td>{{ branch.opened_date }}</td>
                <td>{{ branch.region_name }}</td>
                <td>{{ branch.zone_name }}</td>
                <td>{{ branch.status | getStatusName }}</td>
                <td>
                  <router-link class="apax-btn detail " :to="{name: 'Chi Tiết Thông Tin Đơn Vị Cơ Sở', params: {id: branch.id}}">
                        <span class="fa fa-eye"></span>
                  </router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Trung Tâm', params: {id: branch.id}}">
                        <span class="fa fa-pencil"></span>
                  </router-link>
                  <button class="apax-btn remove" @click="removeBranch(branch.id, index)" v-if="branch.status==0"><i class="fa fa-times"></i></button>
                  <button @click="disabledDeleteBranch()" class="apax-btn remove" v-else>
                      <span class="fa fa-times"></span>
                    </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="text-center">
            <nav aria-label="Page navigation">
              <appPagination
              :rootLink="router_url"
              :id="pagination_id"
              :listStyle="list_style"
              :customClass="pagination_class"
              :firstPage="pagination.spage"
              :previousPage="pagination.ppage"
              :nextPage="pagination.npage"
              :lastPage="pagination.lpage"
              :currentPage="pagination.cpage"
              :pagesItems="pagination.total"
              :pageList="pagination.pages"
              :pagesLimit="pagination.limit"
              :routing="goTo"
              >
            </appPagination>
          </nav>
        </div>
      </b-card>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios'
import Pagination from '../../../components/Pagination'
import u from '../../../utilities/utility'

export default {
  components: {
   appPagination: Pagination
 },
 name: 'List-branch',
 data() {
  return {
        //set selected defaut
        fees: '0',
        area: '0',
        product: '0',
        products: [],
        status: '0',
        //
        regions: [],
        region: '',

        branches: [],
        branch: '',
        router_url: '/branches/list',
        pagination_id: 'branches_paging',
        pagination_class: 'branches paging list',
        list_style: 'line',
        pagination: {
          limit: 20,
          spage: 0,
          ppage: 0,
          npage: 0,
          lpage: 0,
          cpage: 0,
          total: 0,
          pages: []
        },
        search: {
          brch_id: '',
          hrm_id: '',
          accounting_id: '',
          status: '',
          name: '',
          region_id: ''
        },
        key: '',
        value: '',
        statusColor: ''
      }
    },
    created(){
      this.getBranches()
      this.filterBranches()
      let uri = `/api/products`;
      axios.get(uri).then((response) => {
        this.products = response.data.data;
      });

      axios.get(`/api/get-all/regions/get-all-regions-list`).then((response) => {
        this.regions = response.data;
      });
    },
    methods: {
      indexOf(value){
        return value +((this.pagination.cpage-1)*this.pagination.limit)
      },
      reset(){
        this.key = ''
        this.value = ''
        this.search.name = ''
        this.search.brch_id = ''
        this.search.region_id = ''
        this.search.hrm_id = ''
        this.search.accounting_id = ''
        this.search.status = ''
        this.getBranches()
      },
      searchBranch(){
        var url = '/api/branches/list/1/';
        this.key ='';
        this.value = ''
        var brch_id = this.search.brch_id ? this.search.brch_id : ''
        if (brch_id){
          this.key += "brch_id,"
          this.value += brch_id+","
        }
        var hrm_id = this.search.hrm_id ? this.search.hrm_id : ''
        if (hrm_id){
          this.key += "hrm_id,"
          this.value += hrm_id+","
        }
        var accounting_id = this.search.accounting_id ? this.search.accounting_id : ''
        if (accounting_id){
          this.key += "accounting_id,"
          this.value += accounting_id+","
        }
        var status = this.search.status ? this.search.status : ''
        if (status){
          this.key += "status,"
          this.value += status+","
        }
        var name = this.search.name ? this.search.name : ''
        if (name){
          this.key += "name,"
          this.value += this.search.name+","
        }
        var region_id = this.search.region_id ? this.search.region_id : ''
        if (region_id){
          this.key += "region_id,"
          this.value += this.search.region_id+","
        }
        this.key = this.key ? this.key.substring(0, this.key.length - 1) : '_'
        this.value = this.value ? this.value.substring(0, this.value.length - 1) : "_"

        url += this.key+"/"+this.value
        this.get(url);
      },
      filterBranches(){

      },
      addNewBranch(){

      },
      get(link){
        this.ajax_loading = true
        u.a().get(link).then(response => {
          this.branches = response.data.branches
          this.pagination = response.data.pagination
          this.ajax_loading = false
        }).catch(e => console.log(e));
      },
      getBranches(page_url) {
        const key = this.serach ? this.search : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/branches/list/1'
        if (this.key) page_url += '/'+this.key +'/'+this.value
          else page_url+= '/' + key + '/' + fil
        this.get(page_url)
      },
      makePagination(meta, links){
          let pagination = {
             current_page: data.current_page,
             last_page: data.last_page,
             next_page_url: data.next_page_url,
             prev_page_url: data.prev_page_url
          }
         this.pagination = pagination;
      },
      goTo(link) {
        this.getBranches(link)
      },
      removeBranch(id,index){
        this.deleteBranches(id, index)
      },
      disabledDeleteBranch(){
          alert('Hệ thống chỉ cho phép xóa đơn vị trạng thái ngừng hoạt động')
      },
      deleteBranches(id, index){
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa đơn vị này không?");
        if (delStdConf) {
              axios.delete('/api/branches/'+id)
              .then(response => {
                this.branches.splice(index, 1);
              })
              .catch(error => {
              });
            }
          }
        },
    computed:{
    },

    filters: {
      getStatusName(value){
        return value == 1 ? "Hoạt động" : "Ngừng hoạt động";
      },
      statusColor(cl){
        return cl == 1 ? "btn-primary" : "btn-danger";
      }
    }
  }
      </script>

      <style scoped lang="scss">
      .table-header {
        margin-bottom: 5px;
        margin-left: -15px;
      }

      .img img {
        width: 100px;
      }

      a {
        /* color: blue; */
      }

      .cl-btn a {
        color: #fff;
      }
      .apax-btn:hover{
        color: #fff;
        text-decoration: none;
      }
      .apax-form .control-label{
        margin: 5px;
      }
      </style>
