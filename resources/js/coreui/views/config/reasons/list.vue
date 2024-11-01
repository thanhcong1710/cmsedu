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
                <label class="control-label">Loại lý do</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo loại lý do" class="fa fa-search"></i>
                </p>
                <select data-placeholder="trạng thái" v-model="search.type" class="filter-selection tuition-fee form-control" id="">
                  <option value="" disabled>Chọn loại lý do</option>
                  <option value="0">Pending</option>
                  <option value="1">Withdraw</option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên lý do</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên lý do" class="fa fa-search"></i>
                </p>
                <input type="text" class="filter-selection tuition-fee form-control" v-model="search.description">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select data-placeholder="trạng thái" class="filter-selection tuition-fee form-control" v-model="search.status" id="">
                  <option value="" disabled>Chọn trạng thái</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/reasons/add-reason'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="filterReason"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh Sách Các Lý Do WithDraw, Pending</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th>Loại lý do</th>
                <th>Tên lý do</th>
                <th>Trạng thái</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <!-- reason data -->
              <tr v-for="(reason, index) in reasons" :key="index">
                <td>{{indexOf(index+1)}}</td>
                <td>{{reason.type | typePending}}</td>
                <td>{{reason.description}}</td>
                <td :class="{statusColor}">{{reason.status | getStatusName}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn detail" :to="{name: 'Nội Dung Lý Do Bảo Lưu, Pending', params: {id: reason.id}}">
                    <span class="fa fa-eye"></span>
                  </router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Lý Do Bảo Lưu, Pending' , params: {id: reason.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteReason(reason.id, index)" class="apax-btn remove" v-if="reason.status==0"><span class="fa fa-times"></span></button>
                  <button @click="disabledDeleteReason()" class="apax-btn remove" v-else>
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
              :pagesLimit="20"
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
 name: 'List-Reasons',
 data() {
  return {
    reasons: [],
    reason: {
      name: '',
      lms_proc_id: '',
      status: '0'
    },
    router_url: '/reasons/list',
    pagination_id: 'reasons_paging',
    pagination_class: 'reasons paging list',
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
      type: '',
      description: '',
      status: ''
    },
    key: '',
    value: '',
    statusColor: ''
  }
},
methods: {
  reset(){
    this.search.description = ''
    this.search.status = ''
    this.search.type = ''
    this.key = ''
    this.value = ''
    this.getReasons()
  },
  indexOf(value){
      return value+((this.pagination.cpage-1)*this.pagination.limit)
  },
  get(link){
    this.ajax_loading = true
    u.a().get(link).then(response => {
      this.reasons = response.data.reasons
      this.pagination = response.data.pagination
      this.ajax_loading = false
    }).catch(e => console.log(e));
  },
  getReasons(page_url) {
    const key = '_'
    const fil = '_'
    page_url = page_url ? '/api'+page_url : '/api/reasons/list/1'
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
    this.getReasons(link)
  },
  disabledDeleteReason(){
        alert('Hệ thống chỉ cho phép xóa lý do withdaw/pending trạng thái ngừng hoạt động')
    },
 deleteReason(id, index){
  const delStdConf = confirm("Bạn có chắc rằng muốn xóa lý do withdaw/pending này không?");
  if (delStdConf === true) {
		        	// console.log(`xId = ${xId}, Index = ${idx}`)
              axios.delete('/api/reasons/'+id)
              .then(response => {
                this.reasons.splice(index, 1);
              })
              .catch(error => {
              });
            }
          },
          filterReason(){
            var url = '/api/reasons/list/1/';
            this.key ='';
            this.value = ''
            // var description = description.trim();
            var desc = this.search.description.trim();
             // var desc = this.search.description ? this.search.description:""
             var description = desc ? desc : ""
            if (description){
              this.key += "description,"
              this.value += description+","
            }
            var type = this.search.type ? this.search.type :""
            if (type) {
              this.key += "type,"
              this.value += this.search.type+","
            }
            var status = this.search.status ? this.search.status :""
            if (status) {
              this.key += "status,"
              this.value += this.search.status+","
            }

            this.key = this.key? this.key.substring(0, this.key.length - 1):'_'
            this.value = this.value? this.value.substring(0, this.value.length - 1) : "_"
            url += this.key+"/"+this.value
            this.get(url);
          }
        },
        computed:{
          filterByReason(){
            return this.filterReason();
          }
        },
        created(){
          this.getReasons()
          this.filterReason()
        },
        filters: {
          typePending(value){
            return value == 1? "WithDraw" : "Pending"
          },
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
      p.filter-lbl {
        width: 40px;
        height: 35px;
        float: left;
      }
      .filter-selection {
        width: calc(100% - 40px);
        float: left;
        padding: 3px 5px;
        height: 35px!important;
      }
      </style>
