<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>Danh sách Ngân hàng </strong>
          </div>
          <div class="col-4 table-header">
            <router-link class="btn btn-primary" :to="'/banks/add'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <!-- <router-link class="btn btn-success" :to="{ path: '/' }">
              <i class="fa fa-file-word-o"></i> Export
            </router-link> -->
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">Tên Ngân hàng</th>
                <th class="width-150">Tên viết tắt</th>
                <th class="width-150">Logo</th>
                <th class="width-150">Ghi chú</th>
                <th class="width-150">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <!-- books data -->
              <tr v-for="(bank,index) in banks" :key="index">
                <td>{{index+1}}</td>
                <td>{{bank.name}}</td>
                <td>{{bank.alias}}</td>
                <td>
                  <div v-if="bank.logo != null">
                    <img :src="'/static/' + bank.logo" style="width:80px;" />
                  </div>
                </td>
                <td>{{bank.note}}</td>
                <td>
                  <router-link class="btn btn-sm btn-info" :to="{name: 'Chi tiết ngân hàng', params: {id: bank.id}}"><i class="fa fa-list"></i></router-link>
                  <router-link class="btn btn-sm btn-warning" :to="{name: 'Sửa ngân hàng' , params: {id: bank.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteBank(bank.id, index)" class="btn btn-sm btn-danger">
                    <span class="fa fa-times"></span></button>
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
        value: '',
        banks: []
      }
    },
    created(){
      let uri = `/api/banks`;
      u.a().get(uri).then((response) => {
        this.banks = response.data.data;
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

      makePagination(meta, links){
        let pagination = {
         current_page: data.current_page,
         last_page: data.last_page,
         next_page_url: data.next_page_url,
         prev_page_url: data.prev_page_url
       }
       this.pagination = pagination;
     },
     deleteBank(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa bank này không?");
      if (delStdConf === true) {
        	// console.log(`xId = ${xId}, Index = ${idx}`)
          u.a().delete('/api/banks/'+id)
          .then(response => {
            this.banks.splice(index, 1);
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
