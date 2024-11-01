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
                      <label class="filter-label control-label">Trung tâm</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo trung tâm" class="fa fa-search"></i>
                      </p>
                      <select class="filter-selection customer-type form-control" @change="selectBranch" v-model="search.branch_id">
                            <option value="">Chọn trung tâm</option>
                            <option :value="branch.id" v-for="(branch, index) in branches" :key="index">{{branch.name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Giáo viên</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo giáo viên" class="fa fa-search"></i>
                      </p>
                      <select class="filter-selection customer-type form-control" v-model="search.teacher_id" >
                          <option value="">Chọn giáo viên</option>
                          <option :value="teacher.id" v-for="(teacher, index) in teachers" :key="index">{{teacher.ins_name}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Ca học</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo ca học" class="fa fa-search"></i>
                      </p>
                      <input type="text" class="filter-selection customer-type form-control" v-model="search.shift_name">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Buổi học</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo buổi học" class="fa fa-search"></i>
                      </p>
                      <select class="filter-selection customer-type form-control" v-model="search.class_day">
                            <option value="" disabled>Chọn buổi học</option>
                            <option value="1">Thứ 2</option>
                            <option value="2">Thứ 3</option>
                            <option value="3">Thứ 4</option>
                            <option value="4">Thứ 5</option>
                            <option value="5">Thứ 6</option>
                            <option value="6">Thứ 7</option>
                            <option value="0">Chủ nhật</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Lớp học</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i v-b-tooltip.hover title="Lọc theo lớp học" class="fa fa-search"></i>
                      </p>
                      <input type="text" class="filter-selection customer-type form-control" v-model="search.class_name">
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
          <!--<router-link :to="'/sessions/add-session'" class="apax-btn full reset"><i class="fa fa-plus"></i> Thêm mới</router-link>-->
          <button type="button" class="apax-btn full detail" @click="searchSessions"><i class="fa fa-search"></i> Tìm kiếm</button>
          <button class="apax-btn full default" @click="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
        </div>  
      </b-card>
    </b-col>
  </b-row>
      <b-row>
        <b-col cols="12">
          <b-card header>
            <div slot="header">
              <strong><i class="fa fa-list"></i> Danh sách buổi học</strong>
            </div>
            <div class="panel">
              <div class="panel-body">
                <div class="table-responsive scrollable">
                  <table class="table table-striped table-bordered apax-table">
                    <thead>
                      <tr class="text-sm">
                        <th class="text-center width-50">STT</th>
                        <th class="width-150">Tên buổi học</th>
                        <th class="width-150">Phòng học</th>
                        <th class="width-150">Ca học</th>
                        <th class="width-150">Lớp học</th>
                        <th class="width-150">Giáo viên</th>
                        <th class="width-150">Ngày bắt đầu</th>
                        <th class="width-150">Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th class="width-50">Lựa chọn</th>
                        <!--<th>Người tạo</th>-->
                        <!--<th>Người sửa</th>-->
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(session, index) in sessions" :key="index">
                        <td class="text-center">{{index+1}}</td>
                        <td>{{session.class_day | classDay}}</td>
                        <td>{{session.room_name}}</td>
                        <td>{{session.shift_name}}</td>
                        <td>{{session.class_name}}</td>
                        <td>{{session.teacher_name}}</td>
                        <td>{{session.start_date}}</td>
                        <td>{{session.end_date}}</td>
                        <td>{{session.status|getStatusToName}}</td>
                        <td class="text-center" >
                          <router-link title="Xem thông tin buổi học" v-b-tooltip.hover class="apax-btn detail" :to="{name: 'Sửa Thông Tin Buổi Học', params: {id: session.id}}"> <span class="fa fa-eye"></span></router-link>
                          <!--<button @click="deleteSession(session.id, index)" title="Xóa thông tin buổi học" v-b-tooltip.hover class="apax-btn remove"><i class="fa fa-times"></i></button>-->
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
  name: 'List-Session',
  data () {
    return {
      sessions: [],
      router_url: '/sessions/list',
      pagination_id: 'sessions_paging',
      pagination_class: 'sessions paging list',
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
        branch_id : '',
        teacher_id : '',
        shift_name : '',
        class_day : '',
        status : '',
        class_name:'',
      },
      fil: '',
      branches: [],
      teachers: [],
      session_user: u.session(),
    }
  },
  methods: {
    selectBranch(){
      u.a().get(`/api/branches/${this.search.branch_id}/teachers`).then(response =>{
          this.teachers = response.data
        })
    },
      get(link){
        this.ajax_loading = true
         u.a().get(link)
            .then(response => {
              this.sessions = response.data.sessions
              this.pagination = response.data.pagination
              this.ajax_loading = false
            }).catch(e => console.log(e));
      },
      getSessions(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/sessions/list/1'
        page_url+= '/' + key + '/' + fil
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
     reset(){
      this.search.branch_id = ''
      this.search.teacher_id = ''
      this.search.shift_name = ''
      this.search.class_day = ''
      this.search.status = ''
      this.search.class_name = ''
      this.searchSessions();
    },
    searchSessions(){
        var url = '/api/sessions/list/1/';
        this.key ='';
        this.value = '';
        if (this.search.branch_id!=''){
          this.key += "branch_id,"
          this.value += this.search.branch_id+","
        }
        if (this.search.teacher_id!=''){
          this.key += "teacher_id,"
          this.value += this.search.teacher_id+","
        }
        if (this.search.shift_name!=''){
          this.key += "shift_name,"
          this.value += this.search.shift_name+","
        }
        if (this.search.class_day!=''){
          this.key += "class_day,"
          this.value += this.search.class_day+","
        }
        if (this.search.status!=''){
          this.key += "status,"
          this.value += this.search.status+","
        }
        if (this.search.class_name!=''){
          this.key += "class_name,"
          this.value += this.search.class_name+","
        }
        
        this.key = this.key ? this.key.substring(0, this.key.length - 1) : '_'
        this.value = this.value ? this.value.substring(0, this.value.length - 1) : "_"

        url += this.key+"/"+this.value
        this.get(url);
        
      },
    deleteSession(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa buổi học này không?");
      if (delStdConf === true) {
              // console.log(`xId = ${xId}, Index = ${idx}`)
              axios.delete('/api/sessions/'+id)
              .then(response => {
                this.sessions.splice(index, 1);
              })
              .catch(error => {
              });
            }
          },
          goTo(link) {
          this.getSessions(link)
        },
        },
        filters: {
          classDay(value){
            if (value === 1) return 'Thứ 2'
              else if(value === 2) return 'Thứ 3'
                else if(value === 3) return 'Thứ 4'
                  else if(value === 4) return 'Thứ 5'
                    else if(value === 5) return 'Thứ 6'
                      else if(value === 6) return 'Thứ 7'
                        else return 'Chủ nhật'
                      }
              },
              created(){
                this.getSessions()
                u.a().get(`/api/reports/branches`).then(response =>{
                  this.branches = response.data;
                })
              }
            }
            </script>

            <style scoped>
              .apax-form label{
                margin: 5px 0px;
              }
            </style>