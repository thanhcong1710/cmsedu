<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div  slot="header">
            <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
          </div>
          <div class="content-detail">
            <div class="row">
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="filter-label control-label">Tên phòng học</label><br/>
                  <p class="input-group-addon filter-lbl">
                    <i v-b-tooltip.hover title="Lọc theo tên phòng học" class="fa fa-search"></i>
                  </p>
                  <input type="text" v-model="search.room_name" class="filter-selection customer-type form-control">
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="filter-label control-label">Trung tâm</label><br/>
                  <p class="input-group-addon filter-lbl">
                    <i v-b-tooltip.hover title="Lọc theo trung tâm" class="fa fa-bank"></i>
                  </p>
                  <select v-model="search.branch_id" class="filter-selection customer-type form-control" id="">
                    <option value="">Tất cả</option>
                    <option :value="branch.id" v-for="(branch,index) in branches">{{branch.name}}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="filter-label control-label">Trạng thái</label><br/>
                  <p class="input-group-addon filter-lbl">
                    <i v-b-tooltip.hover title="Lọc theo trung tâm" class="fa fa-desktop"></i>
                  </p>
                  <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                    <option value="" disabled>Chọn trạng thái</option>
                    <option value="1">Hoạt động</option>
                    <option value="0">Không hoạt động</option>
                  </select>
                </div>
              </div>
             </div> 
          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/rooms/add-room'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="filterRooms"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full default" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
           </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách phòng học</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Tên phòng học</th>
                <th class="width-150">Trung tâm</th>
                <th class="width-150">Trạng thái</th>
                <th class="width-150">Loại phòng học</th>
                <th class="width-150"></th>
              </tr>
            </thead>
            <tbody>
              <!-- room data -->
              <tr v-for="(room, index) in rooms" :key="index">
                <td>{{indexOf(index)}}</td>
                <td>{{room.room_name}}</td>
                <td>{{room.branch_name}}</td>
                <td>{{room.status | getStatusName}}</td>
                <td>{{room.type | getTypeRoom}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Phòng Học', params: {id: room.id}}">
                    <span class="fa fa-eye"></span>
                  </router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Phòng Học' , params: {id: room.id}}">
                    <i class="fa fa-pencil"></i>
                  </router-link>
                  <button @click="deleterooms(room.id, index)" class="apax-btn remove" v-if="room.status ==0 && session.user.role_id>=999999999" >
                    <span class="fa fa-times"></span>
                  </button>
                  <button @click="disabledDeleteRoom()" class="apax-btn remove" v-else-if="session.user.role_id>=999999999">
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
 name: 'List-Room',
 data() {
  return {
        //set selected defaut
        fees: '0',
        area: '0',
        status: '0',

        rooms: [],
        room: '',
        router_url: '/api/rooms/lists',
        pagination_id: 'rooms_paging',
        pagination_class: 'rooms paging list',
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
          room_name: '',
          branch_id: '',
          status: '',
        },
        value: '',
        key: '',
        statusColor: '',
        fil: '',
        branches: [],
        session: u.session(),
      }
    },
    created(){
      u.a().get('/api/reports/branches').then(response=>{
        this.branches = response.data
        if(this.branches.length == 1) {
          this.search.branch_id = this.branches[0].id;
        }
        this.filterRooms()
      })
    },
    methods: {
      reset(){
        this.search.room_name = ''
        this.search.branch_id = ''
        this.search.status = ''
        this.key = ''
        this.value = ''
        this.getRooms()
      },
      filterRooms(){
        var url = '/api/rooms/lists/1/';
        this.key ='';
        this.value = ''

        var branch_list = ''
        if(this.search.branch_id == '') {
          this.branches.forEach(function(item, index) {
             branch_list += item.id + ',';
          });
          branch_list = branch_list.substring(0, branch_list.length - 1);
        } else {
            branch_list = this.search.branch_id;
        }
        
        var paramsUrl = '?branches=' + branch_list + 
                        '&room_name=' + this.search.room_name + 
                        '&status=' + this.search.status;

        
        this.get(url + paramsUrl);
      },
      indexOf(value){
        return (value+1)+(this.pagination.cpage-1)*this.pagination.limit
      },
      goTo(link){
        this.getRooms(link)
      },
      get(link){
        this.ajax_loading = true
         u.a().get(link)
            .then(response => {
              this.rooms = response.data.rooms
              this.pagination = response.data.pagination
              this.ajax_loading = false
            }).catch(e => console.log(e));
      },
      getRooms(page_url) {
        var TmpUrl = '/api/rooms/lists/1/';
        var branch_list = ''
        if(this.search.branch_id == '') {
          this.branches.forEach(function(item, index) {
            branch_list += item.id + ',';
          });
          branch_list = branch_list.substring(0, branch_list.length - 1);
        } else {
            branch_list = this.search.branch_id;
        }
        
        var paramsUrl = '?branches=' + branch_list + 
                        '&room_name=' + this.search.room_name + 
                        '&status=' + this.search.status;
        var xUrl = (page_url) ? page_url : TmpUrl;

        this.get(xUrl + paramsUrl)
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
     disabledDeleteRoom(){
          alert('Hệ thống chỉ cho phép xóa phòng học trạng thái ngừng hoạt động')
      },
     deleterooms(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa phòng học này không?");
      if (delStdConf === true) {
        	// console.log(`xId = ${xId}, Index = ${idx}`)
          u.a().delete('/api/rooms/'+id)
          .then(response => {
            this.rooms.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
          goTo(link) {
          this.getRooms(link)
        },
        },
        computed:{
          filterByRoom(){
            return this.filterRooms();
          }
        },

        filters: {
          getTypeRoom(value){
            return value == 1 ? "Lấy từ hệ thống" : "Nhập thủ công"
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
      </style>
