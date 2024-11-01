<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
          header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div class="content-detail">
           <div id="page-content">
            <div class="panel">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Tên ca học</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo tên ca học" class="fa fa-search"></i>
                      </p>
                      <input type="text" class="filter-selection customer-type form-control" v-model="search.name">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Giờ bắt đầu</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo giờ bắt đầu" class="fa fa-calendar"></i>
                      </p>
                      <input type="text" class="filter-selection customer-type form-control" v-model="search.start_time">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Giờ kết thúc</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo giờ kết thúc" class="fa fa-calendar"></i>
                      </p>
                      <input type="text" class="filter-selection customer-type form-control" v-model="search.end_time">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Trạng thái</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo giờ kết thúc" class="fa fa-desktop"></i>
                      </p>
                      <select name="" class="filter-selection customer-type form-control" id="" v-model="search.status">
                        <option value="" disabled>Chọn trạng thái</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
            </div>

          </div>
        </div>
        <div slot="footer" class="text-center">
          <router-link class="apax-btn full reset" :to="'/shifts/add-shift'" v-if="session.user.role_id!=55 && session.user.role_id!=686868 && session.user.role_id!=7777777&& session.user.role_id!=56">
            <i class="fa fa-plus"></i> Thêm mới
          </router-link>
          <button type="button" class="apax-btn full detail" @click="searchShifts"><i class="fa fa-search"></i> Tìm kiếm</button>
          <button class="apax-btn full default" @click="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
        </div>  
      </b-card>
    </b-col>
  </b-row>
  <b-row>
    <b-col cols="12">
      <b-card>
        <div slot="header">
          <strong><i class="fa fa-list"></i> Danh sách ca học</strong>
        </div>
        <div class="panel">
          
          <div class="panel-body">
            <div class="table-responsive scrollable">
              <table class="table table-striped table-bordered apax-table">
                <thead>
                  <tr class="text-sm">
                    <th class="text-center width-50">STT</th>
                    <th class="width-150">Tên ca học</th>
                    <th class="width-150">Giờ bắt đầu</th>
                    <th class="width-150">Giờ kết thúc</th>
                    <th class="width-150">Trạng thái</th>
                    <th class="width-150">Khu Vực</th>
                    <th class="width-50">Lựa chọn</th>
                    <!--<th>Người tạo</th>-->
                    <!--<th>Người sửa</th>-->
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(shift,index) in shifts" :key="index">
                    <td class="text-center">{{index+1}}</td>
                    <td>Ca {{shift.name}}</td>
                    <td>{{shift.start_time }}</td>
                    <td>{{shift.end_time }}</td>
                    <td>{{shift.status | shiftStatus}}</td>
                    <td>{{shift.zone_name}}</td>
                    <td class="text-center" v-if="session.user.role_id!=55 && session.user.role_id!=686868 && session.user.role_id!=7777777 && session.user.role_id!=56">
                      <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Ca Học', params: {id: shift.id}}"><i class="fa fa-pencil"></i></router-link>
                      <button class="apax-btn remove" @click="deleteShift(shift.id, index)" v-if="shift.status==0"><i class="fa fa-times"></i></button> 
                      <button  @click="disabledDeleteShift()" class="apax-btn remove" v-else>
                        <span class="fa fa-times"></span>
                      </button>
                    </td>
                    <td v-else></td>
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
          </div>
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
import u from '../../../utilities/utility'
import Pagination from '../../../components/Pagination'

export default {
  components: {
    appPagination: Pagination
  },
  name: 'List-Shift',
  data () {
    return {
      shifts: [],
      router_url: '/shifts/list',
      pagination_id: 'shifts_paging',
      pagination_class: 'shifts paging list',
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
        name: '',
        start_time: '',
        end_time: '',
        status: '',
      },
      key: '',
      value: '',
      session: u.session(),
    }
  },
  created(){
    this.getShifts()
  },
  filters: {
    shiftStatus(value){
      return value == 1? "Hoạt động" : "Không hoạt động"
    }
  },
  methods: {
    goTo(link){
      this.getShifts(link)
    },
    get(link){
      this.ajax_loading = true
       u.a().get(link)
          .then(response => {
            this.shifts = response.data.shifts
            this.pagination = response.data.pagination
            this.ajax_loading = false
          }).catch(e => console.log(e));
    },
    reset(){
      this.search.name = ''
      this.search.start_time = ''
      this.search.end_time = ''
      this.search.status = ''
      this.searchShifts()
    },
    searchShifts(){
        var url = '/api/shifts/list/1/';
        this.key ='';
        this.value = ''
        var name = this.search.name ? this.search.name : ""
        if (name){
          this.key += "name,"
          this.value += this.search.name+","
        }
        var start_time = this.search.start_time ? this.search.start_time :""
        if (start_time) {
          this.key += "start_time,"
          this.value += this.search.start_time+","
        }
        var end_time = this.search.end_time ? this.search.end_time :""
        if (end_time) {
          this.key += "end_time,"
          this.value += this.search.end_time+","
        }
        var status = this.search.status ? this.search.status :""
        if (status) {
          this.key += "status,"
          this.value += this.search.status+","
        }
        // console.log('test', this.key, this.value);
        this.key = this.key ? this.key.substring(0, this.key.length - 1) : '_'
        this.value = this.value ? this.value.substring(0, this.value.length - 1) : "_"
        url += this.key+"/"+this.value
        this.get(url);
    },
    getShifts(page_url) {
      const key = this.keyword ? this.keyword : '_'
      page_url = page_url ? '/api'+page_url : '/api/shifts/list/1'
      page_url+= '/' + key + '/_'
      this.get(page_url)
    },
    makePagination(meta, links) {
      let pagination = {
        current_page: data.current_page,
        last_page: data.last_page,
        next_page_url: data.next_page_url,
        prev_page_url: data.prev_page_url
      }
      this.pagination = pagination;
    },
    disabledDeleteShift(){
          alert('Hệ thống chỉ cho phép xóa ca học trạng thái ngừng hoạt động')
      },
    deleteShift(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa ca học này không?");
      if (delStdConf === true) {
            // console.log(`xId = ${xId}, Index = ${idx}`)
            axios.delete(`/api/shifts/${id}`)
            .then(response => {
              this.shifts.splice(index, 1);
            })
            .catch(error => {
            });
          }
        },
        goTo(link) {
          this.getShifts(link)
        },
      }
    }
    </script>

    <style scoped>

    </style>