<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
          header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <strong> <i class="fa fa-filter"></i> Bộ lọc</strong>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div class="content-detail">
            <div id="page-content">
              <div class="row">
                <div class="col-lg-12">
                  <div class="panel">
                    <form class="panel-body" method="post">
                      <div class="row">
                       <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Thời hạn ngày pending</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo thời hạn ngày pending" class="fa fa-calendar"></i>
                          </p>
                          <input class="filter-selection customer-type form-control" v-model="search.days" type="number">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Loại quy định</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo loại quy định" class="fa fa-life-ring"></i>
                          </p>
                          <select v-model="search.type" class="filter-selection customer-type form-control">
                            <option value="" disabled>Chọn loại quy định</option>
                            <option value="1">Bảo lưu</option>
                            <option value="0">Pending</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label">Trạng thái</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-bank"></i>
                          </p>
                          <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                            <option value="" disabled>Chọn trạng thái</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div slot="footer" class="text-center">
          <router-link class="apax-btn full reset" :to="'/pending-rules/add-pending-rule'">
            <i class="fa fa-plus"></i> Thêm mới
          </router-link>
          <button class="apax-btn full detail" @click.prevent="searchPending"><i class="fa fa-search"></i> Tìm kiếm</button>
          <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
        </div>   
      </b-card>
    </b-col>
  </b-row>
  <b-row>
    <b-col cols="12">
      <b-card>
        <div slot="header">
          <strong><i class="fa fa-list"></i> Danh Sách Các Quy Định Bảo lưu, Pending</strong>
        </div>
        <div class="panel">
          <div class="panel-body">
            <div class="table-responsive scrollable">
              <table class="table table-striped table-bordered apax-table">
                <thead>
                  <tr class="text-sm">
                    <th class="text-center width-50">STT</th>
                    <th class="width-150">Thời gian pending( Theo ngày)</th>
                    <th class="width-150">Vị trí gia hạn pending</th>
                    <th class="width-150">Thời gian bắt đầu hiệu lực</th>
                    <th class="width-150">Thời gian hết hiệu lực</th>
                    <th class="width-150">Loại quy định</th>
                    <th class="width-150">Trạng thái</th>
                    <th class="width-50">Lựa chọn</th>
                    <!--<th>Người tạo</th>-->
                    <!--<th>Người sửa</th>-->
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(pending, index) in pending_rule" :key="index">
                    <td class="text-center">{{index+1}}</td>
                    <td>{{pending.min_days}} - {{pending.max_days}}</td>
                    <td>{{pending.role_name}}</td>
                    <td>{{pending.start_date}}</td>
                    <td>{{pending.expired_date}}</td>
                    <td>{{pending.type | pendingTypeToString}}</td>
                    <td>{{pending.status | filterStatus}}</td>
                    <td class="text-center">
                      <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Quy Định Pending', params: {id: pending.id}}">
                        <i class="fa fa-eye"></i>
                      </router-link>
                      <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Quy Định Pending', params: {id: pending.id}}">
                        <i class="fa fa-pencil"></i>
                      </router-link>
                      <button class="apax-btn remove" @click="deletePending(pending.id,index)" v-if="pending.status==0"><i class="fa fa-times"></i></button>
                      <button @click="disabledDeletePending()" class="apax-btn remove" v-else>
                        <span class="fa fa-times"></span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
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
  </b-col>
</b-row>
</div> 
</div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'
import Pagination from '../../../components/Pagination'

export default {
 components: {
   appPagination: Pagination
 },
 name: 'List-Pending-Rule',
 data () {
  return {
    router_url: '/pendingRegulations/list',
    pagination_id: 'pending-rules_paging',
    pagination_class: 'pending-rules paging list',
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
    pending_rule: [],
    search: {
      days: '',
      type: '',
      status: ''
    },
    key: '',
    value: '',
    fil: ''
  }
},
methods: {
  searchPending(){
        var url = '/api/pendingRegulations/list/1/';
        this.key ='';
        this.value = ''
        var days = this.search.days ? this.search.days:""
        if (days){
          this.key += "days,"
          this.value += this.search.days+","
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
  },
  reset(){
    this.search.days = ''
    this.search.type = ''
    this.search.status = ''
    this.key = ''
    this.value = ''
    this.getPendingRule()
  },
  get(link){
    this.ajax_loading = true
    u.a().get(link).then(response => {
      this.pending_rule = response.data.pending
      this.pagination = response.data.pagination
      this.ajax_loading = false
    }).catch(e => console.log(e));
  },
  getPendingRule(page_url) {
    const key =  '_'
    const fil =  '_'
    page_url = page_url ? '/api'+page_url : '/api/pendingRegulations/list/1'
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
    this.getPendingRule(link)
  },
  disabledDeletePending(){
      alert('Hệ thống chỉ cho phép xóa quy định trạng thái ngừng hoạt động')
  },
  deletePending(id, index){
    const delStdConf = confirm("Bạn có chắc rằng muốn xóa quy định này không?");
    if (delStdConf === true) {
        // console.log(`xId = ${xId}, Index = ${idx}`)
      u.a().delete('/api/pendingRegulations/'+id)
      .then(response => {
        this.pending_rule.splice(index, 1);
      })
      .catch(error => {
      });
    }
  }
},
created() {
  this.getPendingRule()
},
filters: {
  filterStatus(value){
    return value == 1? "Hoạt động":"Không hoạt động"
  },
  pendingTypeToString(v){
    return v == 1? "Bảo lưu":"Pending"
  }
}
}
</script>

<style scoped>

</style>