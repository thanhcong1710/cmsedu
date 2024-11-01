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
                <label class="control-label">Tên giáo viên</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên giáo viên" class="fa fa-user-circle"></i>
                </p>
                <input v-model="search.ins_name" type="text" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trung tâm</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trung tâm" class="fa fa-bank"></i>
                </p>
                <select v-model="search.branch_id" class="filter-selection customer-type form-control" id="">
                  <option value="" disabled>Lựa chọn trung tâm</option>
                  <option :value="branch.id" v-for="(branch, index) in branches">{{branch.name}}</option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                  <option value="" selected>Tất cả</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Loại giáo viên</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo loại giáo viên" class="fa fa-search"></i>
                </p>
                <select v-model="search.is_head_teacher" class="filter-selection customer-type form-control" id="">
                  <option value="" selected>Tất cả</option>
                  <option value="1">Head Teacher</option>
                  <option value="0">Teacher</option>
                </select>
              </div>
            </div>

          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/teachers/add-teacher'" v-if="show_add">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="filterTeachers"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full default" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
            <button class="apax-btn full edit" type="button" @click="exportExcel">
              <i class="fa fa-file-word-o"></i> Export
            </button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách giáo viên</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Tên giáo viên</th>
                <th class="width-150">Trung tâm làm việc</th>
                <th class="width-150">Trạng thái</th>
                <th>Loại giáo viên</th>
                <th>Email</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <!-- teacher data -->
              <tr v-for="(teacher, index) in teachers" :key="index">
                <td>{{indexOf(index)}}</td>
                <td style="text-align: left;padding-left: 10px !important;">{{teacher.ins_name}}</td>
                <td>{{teacher.branch_name}}</td>
                <td>{{teacher.status | statusTeacher}}</td>
                <td>{{teacher.is_head_teacher | headTeacher}}</td>
                <td style="text-transform: none !important;">{{teacher.email}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Giáo Viên', params: {id: teacher.id}}"><i class="fa fa-pencil"></i></router-link>
                  <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Giáo Viên', params: {id: teacher.id}}">
                    <span class="fa fa-eye"></span>
                  </router-link>
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
      <b-modal title="NOTIFICATION" class="modal-success" size="sm" v-model="modal" @ok="closeModal" ok-variant="success">
            <div v-html="message">
            </div>
      </b-modal>

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
 name: 'List-Teacher',
 data() {
  return {
        show_add: true,
        //set selected defaut
        fees: '0',
        area: '0',
        product: '0',
        products: [],
        status: '0',
        modal: false,
        regions: [],
        region: '',
        message: '',
        teachers: [],
        teacher: '',
        router_url: '/api/teachers/lists',
        pagination_id: 'teachers_paging',
        pagination_class: 'teachers paging list',
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
          ins_name: '',
          branch_id: '',
          status: '',
          is_head_teacher: '',
        },
        branches: [],
        statusColor: '',
        key: '',
        value: '',
        action: {
          content: 'Đang tải dữ liệu...',
          loading: false
        }
      }
    },
    created(){
      u.a().get('/api/reports/branches').then(response =>{
        this.branches = response.data
        this.getTeachers()
      })
      if(u.session().user.role_id == 37){
        this.show_add =false
      } 
    },
    methods: {
      indexOf(value){
        return (value+1)+(this.pagination.cpage-1)*this.pagination.limit
      },
      reset(){
        this.branches_list = ''
        this.search.ins_name = ''
        this.search.branch_id = ''
        this.search.status = ''
        this.key = ''
        this.search.is_head_teacher = ''
        this.value = ''
        this.getTeachers()
      },
      filterTeachers(){
          this.action.loading = true
          var url = "/api/teachers/lists/1"

          var branches_list = '';
          if( this.search.branch_id == '' ) {
            this.branches.forEach(function(item,index) {
              branches_list += item.id + ',';
            });
            branches_list = branches_list.substring(0, branches_list.length - 1);
          }else {
            branches_list = this.search.branch_id;
          }

          const paramsUrl = '?branches=' + branches_list + 
                        '&ins_name=' + this.search.ins_name + 
                        '&status=' + this.search.status +
                        '&is_head_teacher=' + this.search.is_head_teacher;
          this.get(url + paramsUrl);
      },
      exportExcel() {
        var url = "/api/teachers/export/exportExcel"

          var branches_list = '';
          if( this.search.branch_id == '' ) {
            this.branches.forEach(function(item,index) {
              branches_list += item.id + ',';
            });
            branches_list = branches_list.substring(0, branches_list.length - 1);
          }else {
            branches_list = this.search.branch_id;
          }

          const paramsUrl = '?branches=' + branches_list + 
                        '&ins_name=' + this.search.ins_name +
                        '&status=' + this.search.status +
                        '&is_head_teacher=' + this.search.is_head_teacher;
          window.open(url + paramsUrl);
      },
     goTo(link){
      this.getTeachers(link)
    },
    get(link){
      // this.ajax_loading = true
       u.a().get(link)
          .then(response => {
            this.teachers = response.data.teachers
            this.pagination = response.data.pagination
            // this.ajax_loading = false
          }).catch(e => console.log(e));
    },
    closeModal(){

    },
    getTeachers(page_url) {
      let tmpUrl = '/api/teachers/lists/1';
      let Url = (page_url) ? page_url : tmpUrl;
      var branches_list = '';
      if( this.search.branch_id == '' ) {
        this.branches.forEach(function(item,index) {
          branches_list += item.id + ',';
        });
        branches_list = branches_list.substring(0, branches_list.length - 1);
      }else {
        branches_list = this.search.branch_id;
      }
      

      const paramsUrl = '?branches=' + branches_list + 
                        '&ins_name=' + this.search.ins_name +
                        '&is_head_teacher=' + this.search.is_head_teacher+
                        '&status=' + this.search.status;
      const xUrl = Url + paramsUrl;
      
      this.get(xUrl)
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
    deleteShift(id, index){
      const delStdConf = confirm("Do you ready want to delete this shift?");
      if (delStdConf === true) {
            // console.log(`xId = ${xId}, Index = ${idx}`)
            axios.delete(`/api/teachers/${id}`)
            .then(response => {
              this.shifts.splice(index, 1);
            })
            .catch(error => {
            });
          }
        }
  },
  computed:{
    filterByTeacher(){
      return this.filterTeachers();
    }
  },

  filters: {
    getStatusName(value){
      return value == 1 ? "Hoạt động" : "Ngừng hoạt động";
    },
    statusColor(cl){
      return cl == 1 ? "btn-primary" : "btn-danger";
    },
    headTeacher(val){
      return val == 1 ? "Head teacher" : "Teacher";
    },
    statusTeacher(val){
      return val == 1 ? "Hoạt động" : "Không hoạt động";
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
.apax-form label{
  margin: 5px 0px;
}
</style>
