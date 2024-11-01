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
                <label class="control-label">Semester</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo semester" class="fa fa-search"></i>
                </p>
                <input type="text" v-model="search.name" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select v-model="search.status" class="filter-selection customer-type form-control" id="">
                  <option value="" disabled>Chọn trạng thái</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/semesters/add-semester'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="filterSemesters"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full default" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách semester</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Semester</th>
                <th class="width-150">Thời gian bắt đầu</th>
                <th class="width-150">Thời gian kết thúc</th>
                <th class="width-150">Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <!-- semester data -->
              <tr v-for="(semester, index) in semesters" :key="index">
                <td>{{index+1}}</td>
                <td>{{semester.name}}</td>
                <td>{{semester.start_date}}</td>
                <td>{{semester.end_date}}</td>
                <td>{{semester.status| getStatusName}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Kỳ Học', params: {id: semester.id}}">
                    <span class="fa fa-eye"></span>
                  </router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Kỳ Học' , params: {id: semester.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteSemesters(semester.id, index)" class="apax-btn remove" v-if="semester.status==0">
                    <span class="fa fa-times"></span>
                  </button>
                    <button @click="disabledDeleteSemesters()" class="apax-btn remove" v-else>
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
  name: 'List-Semester',
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

        semesters: [],
        semester: '',
        router_url: '/semesters/list',
        pagination_id: 'semesters_paging',
        pagination_class: 'semesters paging list',
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
          start_date: '',
          end_date: '',
          status: ''
        },
        key: '',
        value: '',
        statusColor: ''
      }
    },
    created(){
      this.getSemesters()
      this.filterSemesters()
      let uri = `/api/products`;
      axios.get(uri).then((response) => {
        this.products = response.data.data;
      });

      axios.get(`/api/regions`).then((response) => {
        this.regions = response.data.data;
      });
    },
    methods: {
      reset(){
        this.search.name =''
        this.search.status = ''
        this.search.start_date = ''
        this.search.end_date = ''
        this.getSemesters()
      },
      get(link){
        this.ajax_loading = true
        u.a().get(link).then(response => {
          this.semesters = response.data.semesters
          this.pagination = response.data.pagination
          this.ajax_loading = false
        }).catch(e => console.log(e));
      },
      getSemesters(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/semesters/list/1'
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
        this.getSemesters(link)
      },
      disabledDeleteSemesters(){
          alert('Hệ thống chỉ cho phép xóa kỳ học trạng thái ngừng hoạt động')
      },
      deleteSemesters(id, index){
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa kỳ học này không?");
        if (delStdConf === true) {
              // console.log(`xId = ${xId}, Index = ${idx}`)
              axios.delete('/api/semesters/'+id)
              .then(response => {
                this.semesters.splice(index, 1);
              })
              .catch(error => {
              });
            }
          },
          filterSemesters(){
            var url = "/api/semesters/list/1/"

            this.key ='';
            this.value = ''
            var name = this.search.name ? this.search.name:""
            if (name){
              this.key += "name,"
              this.value += this.search.name+","
            }
            var start_date = this.search.start_date ? this.search.start_date :""
            if (start_date) {
              this.key += "start_date,"
              this.value += this.search.start_date+","
            }
            var end_date = this.search.end_date ? this.search.end_date :""
            if (end_date) {
              this.key += "end_date,"
              this.value += this.search.end_date+","
            }

            var status = this.search.status ? this.search.status : ""
            if (status){
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
          filterBySemester(){
            return this.filterSemesters();
          }
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
      </style>
