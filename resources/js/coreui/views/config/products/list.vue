<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
              <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên sản phẩm</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên sản phẩm" class="fa fa-product-hunt"></i>
                </p>
                <input type="text" class="filter-selection customer-type form-control" v-model="product.name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select v-model="product.status" class="filter-selection customer-type form-control" id="">
                  <option value="" disabled>Chọn trạng thái</option>
                  <option :value="1">Hoạt động</option>
                  <option :value="0">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/products/add-product'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="filterProduct"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="resetKeyword"><i class="fa fa-ban"></i> Bỏ lọc</button>
            <button class="apax-btn full edit" @click="exportProductList"><i class="fa fa-file-word-o"></i> Export</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>Danh sách các sản phẩm</strong>
          </div>
          
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th>Tên sản phẩm</th>
                <th>Trạng thái</th>
                <th>Lựa chọn</th>
              </tr>
            </thead>
            <tbody>
              <!-- product data -->
              <tr v-for="(product, index) in products" :key="index">
                <td>{{indexOf(index)}}</td>
                <td>{{product.name}}</td>
                <td :class="{statusColor}">{{product.status | getStatusName}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Sản Phẩm', params: {id: product.id}}">
                    <span class="fa fa-eye"></span>
                  </router-link>
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Sản Phẩm' , params: {id: product.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteProduct(product.id, index)" class="apax-btn remove" v-if="product.status==0"><span class="fa fa-times"></span></button>
                  <button @click="disabledDeleteProduct()" class="apax-btn remove" v-else>
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
 name: 'List-Product',
 data() {
  return {
    products: [],
    product: {
      name: '',
      status: ''
    },
    router_url: '/products/list',
    pagination_id: 'products_paging',
    pagination_class: 'products paging list',
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
    statusColor: ''
  }
},
methods: {
  exportProductList(){
    // const url = `/api/export/config/product`
    var p = `/api/export/config/product`;
    window.open(p, '_blank')
  },
  indexOf(value){
    return (value+1)+((this.pagination.cpage-1)*this.pagination.limit)
  },
  get(link){
        this.ajax_loading = true
        u.a().get(link).then(response => {
          this.products = response.data.products
          this.pagination = response.data.pagination
          this.ajax_loading = false
        }).catch(e => console.log(e));
  },
  filterProduct(){
    const keywords = {
      name: this.product.name,
      status: this.product.status,
    }
    u.a().post(`/api/products/search-products`, keywords).then(response => {
      this.products = response.data
    })    
  },
  getProducts(page_url) {
    const params = '?name=' + this.search.name +
                   '&status=' + this.search.status;

    page_url = page_url ? '/api'+page_url : '/api/products/list/1'
    this.get(page_url + params)
  },
  resetKeyword(){
    this.product.name = ''
    this.product.lms_id = ''
    this.product.status = ''
    this.getProducts()
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
    this.getProducts(link)
  },
  disabledDeleteProduct(){
      alert('Hệ thống chỉ cho phép xóa sản phẩm trạng thái ngừng hoạt động')
  },
  deleteProduct(id, index){
    const delStdConf = confirm("Bạn có chắc rằng muốn xóa sản phẩm này không?");
    if (delStdConf === true) {
    axios.delete('/api/products/'+id)
    .then(response => {
      this.products.splice(index, 1);
    })
    .catch(error => {
    });
    }
  }
  },
  computed:{
  },
  created(){
    this.getProducts()
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
