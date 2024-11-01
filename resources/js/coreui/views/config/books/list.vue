<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong> <i class="fa fa-filter"></i> Bộ lọc sách ..</strong>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Tên sách</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên sách" class="fa fa-leanpub"></i>
                </p>
                <input type="text" v-model="search.name" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Sản phẩm</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo sản phẩm" class="fa fa-product-hunt"></i>
                </p>
                <select v-model="search.product_id" class="filter-selection customer-type form-control" >
                  <option value="">Tất cả</option>
                  <option :value="product.id" v-for="(product,p) in products" :key="p">{{product.name}}</option>
                </select>
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
            <router-link class="apax-btn full reset" :to="'/books/add-book'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="searchbook"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách các đầu sách </strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Tên sách</th>
                <th class="width-150">Mã sách</th>
                <th class="width-150">Năm sinh áp dụng</th>
                <th class="width-150">Sản phẩm</th>
                <th class="width-150">Ngày bắt đầu</th>
                <th class="width-150">Ngày kết thúc</th>
                <th class="width-150">Trạng thái</th>
                <th class="width-150">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <!-- books data -->
              <tr v-for="(book,index) in books" :key="index">
                <td>{{index+1}}</td>
                <td>{{book.name}}</td>
                <td>{{book.book_id}}</td>
                <td>{{book.year_apply}}</td>
                <td>{{book.product_name}}</td>
                <td>{{moment(String(book.start_date)).format('Y-MM-DD')}}</td>
                <td>{{moment(String(book.end_date)).format('Y-MM-DD')}}</td>
                <td>{{book.status | nameStatus}}</td>
                <td>
                  <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Đầu Sách', params: {id: book.id}}"><i class="fa fa-eye"></i></router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Đầu Sách' , params: {id: book.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteBooks(book.id, index)" class="apax-btn remove" v-if="book.status==0">
                    <span class="fa fa-times"></span></button>
                   <button @click="disabledDeleteBooks()" class="apax-btn remove" v-else>
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
        books: [],
        router_url: '/books/list',
        pagination_id: 'books_paging',
        pagination_class: 'books paging list',
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
          product_id: '',
          status: ''
        },
        statusColor: '',
        fil: '',
        key: '',
        value: ''
      }
    },
    created(){
      this.getBooks()
      let uri = `/api/products`;
      axios.get(uri).then((response) => {
        this.products = response.data.data;
      });
    },
    methods: {
      reset(){
        this.key ='';
        this.value =''
        this.search.product_id = ''
        this.search.name = ''
        this.search.status = ''
        this.getBooks();
      },
      searchbook(){
        var url = '/api/books/list/1/';
        this.key ='';
        this.value = ''
        var name = this.search.name ? this.search.name:""
        if (name){
          this.key += "name,"
          this.value += this.search.name+","
        }
        var product_id = this.search.product_id ? this.search.product_id :""
        if (product_id) {
          this.key += "product_id,"
          this.value += this.search.product_id+","
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
      this.getBooks(link)
      },
      get(link){
        this.ajax_loading = true
         u.a().get(link)
            .then(response => {
              this.books = response.data.books
              this.pagination = response.data.pagination
              this.ajax_loading = false
            }).catch(e => console.log(e));
      },
      getBooks(page_url) {
        const key = '_'
        const fil = '_'
        page_url = page_url ? '/api'+page_url : '/api/books/list/1'
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
     disabledDeleteBooks(){
        alert('Hệ thống chỉ cho phép xóa đầu sách trạng thái ngừng hoạt động')
    },
     deleteBooks(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa đầu sách này không?");
      if (delStdConf === true) {
        	// console.log(`xId = ${xId}, Index = ${idx}`)
          u.a().delete('/api/books/'+id)
          .then(response => {
            this.books.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
          goTo(link) {
          this.getBooks(link)
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
