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
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Tiêu chí</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tiêu chí" class="fa fa-leanpub"></i>
                </p>
                <input type="text" v-model="search.title" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-3">
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
            <router-link class="apax-btn full reset" :to="'/qualities/add'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="searchquality"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách chất lượng contact </strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Tiêu chí</th>
                <th class="width-150">Điểm</th>
                <th class="width-150">Trạng thái</th>
                <th class="width-150">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <!-- qualities data -->
              <tr v-for="(quality,index) in qualities" :key="index">
                <td>{{index+1}}</td>
                <td>{{quality.title}}</td>
                <td>{{quality.score}}</td>
                <td>{{quality.status | nameStatus}}</td>
                <td>
                  <!--<router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Đánh Giá Contact', params: {id: quality.id}}"><i class="fa fa-eye"></i></router-link>-->
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Đánh Giá Contact' , params: {id: quality.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteQualities(quality.id, index)" class="apax-btn remove" v-if="quality.status==0">
                    <span class="fa fa-times"></span></button>
                   <button @click="disabledDeleteQualities()" class="apax-btn remove" v-else>
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
import moment from 'moment'

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
        product: '0',
        products: [],
        status: '0',
        //
        regions: [],
        region: '',
        qualities: [],
        router_url: '/qualities/list',
        pagination_id: 'qualities_paging',
        pagination_class: 'qualities paging list',
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
          title: '',
          status: ''
        },
        statusColor: '',
        fil: '',
        key: '',
        value: ''
      }
    },
    created(){
      this.getQualities()
    },
    methods: {
      reset(){
        this.key ='';
        this.value =''
        this.search.title = ''
        this.search.status = ''
        this.getQualities();
      },
      searchquality(){
        var url = '/api/qualities/list/1/';
        this.key ='';
        this.value = ''
        var title = this.search.title ? this.search.title:""
        if (title){
          this.key += "title,"
          this.value += this.search.title+","
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
      goTo(link){
      this.getQualities(link)
      },
      get(link){
        this.ajax_loading = true
         u.a().get(link)
            .then(response => {
              this.qualities = response.data.qualities
              this.pagination = response.data.pagination
              this.ajax_loading = false
            }).catch(e => console.log(e));
      },
      getQualities(page_url) {
        const key = '_'
        const fil = '_'
        page_url = page_url ? '/api'+page_url : '/api/qualities/list/1'
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
     disabledDeleteQualities(){
        alert('Hệ thống chỉ cho phép xóa đầu sách trạng thái ngừng hoạt động')
    },
     deleteQualities(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa đầu sách này không?");
      if (delStdConf === true) {
        	// console.log(`xId = ${xId}, Index = ${idx}`)
          u.a().delete('/api/qualities/'+id)
          .then(response => {
            this.qualities.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
          goTo(link) {
          this.getQualities(link)
        }
        },
        filters: {
          nameStatus(value){
            return value == 1? "Hoạt động" : "Không hoạt động"
          },
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
