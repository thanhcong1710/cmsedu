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
                <label class="control-label">Tên school grade</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên school grade" class="fa fa-search"></i>
                </p>
                <input type="text" class="filter-selection customer-type form-control" v-model="search.name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                  <option value="">Chọn trạng thái</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/grades/add-grade'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="filterGrade"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div> 
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh Sách Các School Grade</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Tên school grade</th>
                <th class="width-150">Trạng thái</th>
                <th>Lựa chọn</th>
              </tr>
            </thead>
            <tbody>
              <!-- grade data -->
              <tr v-for="(grade, index) in grades" :key="index">
                <td>{{indexOf(index)}}</td>
                <td>{{grade.name}}</td>
                <td :class="{statusColor}">{{grade.status | getStatusName}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin School Grade', params: {id: grade.id}}">
                    <span class="fa fa-eye"></span>
                  </router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin School Grade' , params: {id: grade.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteGrade(grade.id, index)" class="apax-btn remove"><span class="fa fa-times"></span></button>
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
  name: 'List-School-Grade',
  data() {
    return {
      grades: [],
      grade: {
        name: '',
        lms_proc_id: '',
        status: ''
      },
      router_url: '/schoolGrades/list',
      pagination_id: 'grades_paging',
      pagination_class: 'grades paging list',
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
        status: ''
      },
      key: '',
      value: '',
      statusColor: ''
    }
  },
  methods: {
    reset(){
      this.search.name = ''
      this.search.status = ''
      this.getGrades()
    },
    filterGrade(){
      var url = '/api/schoolGrades/list/1/';
      this.key ='';
      this.value = ''
      var name = this.search.name ? this.search.name:""
      if (name){
        this.key += "name,"
        this.value += this.search.name+","
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
    indexOf(value){
      return (value+1)+(this.pagination.cpage-1)*this.pagination.limit
    },
  get(link){
    this.ajax_loading = true
    u.a().get(link).then(response => {
      this.grades = response.data.grades
      this.pagination = response.data.pagination
      this.ajax_loading = false
    }).catch(e => console.log(e));
  },
  getGrades(page_url) {
    const key = this.keyword ? this.keyword : '_'
    const fil = this.fil ? this.fil : '_'
    page_url = page_url ? '/api'+page_url : '/api/schoolGrades/list/1'
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
  goTo(link) {
    this.getGrades(link)
  },
    deleteGrade(id, index) {
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa hồ sơ của school grade này không?");
      if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          u.a().delete('/api/schoolGrades/' + id)
          .then(response => {
            this.grades.splice(index, 1);
          })
          .catch(error => {
          });
        }
      }
    },
    computed: {
    },
    created() {
      this.getGrades()
    },
    filters: {
      getStatusName(value) {
        return value == 1 ? "Hoạt động" : "Ngừng hoạt động";
      },
      statusColor(cl) {
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
