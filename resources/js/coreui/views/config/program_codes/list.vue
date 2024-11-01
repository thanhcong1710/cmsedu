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
                <label class="control-label">Sản phẩm</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo sản phẩm" class="fa fa-product-hunt"></i>
                </p>
                <select name="" id="" v-model="search.product_id" class="filter-selection customer-type form-control">
                  <option value="">Chọn sản phẩm</option>
                  <option :value="product.id" v-for="(product, index) in products" :key="index">{{product.name}}</option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Mã</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo mã" class="fa fa-search"></i>
                </p>
                <input type="text" v-model="search.code" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Mô tả</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo mô tả" class="fa fa-search"></i>
                </p>
                <input type="text" v-model="search.description" class="filter-selection customer-type form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Trạng thái</label><br/>
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo trạng thái" class="fa fa-desktop"></i>
                </p>
                <select class="filter-selection customer-type form-control" v-model="search.status">
                  <option value="" disabled>Trạng thái</option>
                  <option value="1">Hoạt động</option>
                  <option value="0">Không hoạt động</option>
                </select>
              </div>
            </div>

          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/program-codes/add-program_code'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="searchProgram"><i class="fa fa-search"></i> Tìm kiếm</button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> DANH SÁCH CÁC MÃ QUY CHIẾU</strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th>Mã</th>
                <th>Mô tả</th>
                <th>Sản phẩm</th>
                <th>Trạng thái</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <!-- programCode data -->
              <tr v-for="(programCode, index) in programCodes" :key="index">
                <td>{{indexOf(index)}}</td>
                <td>{{programCode.code}}</td>
                <td>{{programCode.description}}</td>
                <td>{{programCode.product_name}}</td>
                <td :class="{statusColor}">{{programCode.status | getStatusName}}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Mã Quy Chiếu' , params: {id: programCode.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteProgramCode(programCode.id, index)" class="apax-btn remove" v-if="programCode.status==0"><span class="fa fa-times"></span></button>
                  <button @click="disabledDeleteProgramCode()" class="apax-btn remove" v-else>
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
  name: 'List-Program-Code',
  data() {
    return {
      ajax_loading: true,
      programCodes: [],
      fil:'',
      keyword: '',
      programCode: {
        code: '',
        description: '',
        status: ''
      },
      router_url: '/programCodes/list',
      pagination_id: 'contract_paging',
      pagination_class: 'program-codes paging list',
      list_style: 'line',
      prepareText: (txt) => txt && txt.length ? u.sub(txt, 20) : '',
      format: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ',
      customer_type: -1,
      program: -1,
      tuition_fee: -1,
      information: {},
      list_style: 'line',
      products: [],
      product: 0,
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
        product_id: '',
        code: '',
        description: '',
        status: ''
      },
      statusColor: '',
      key: '',
      value: ''
    }
  },
  methods: {
    indexOf(value){
      return value + 1 + (this.pagination.cpage-1)*this.pagination.limit
    },
    reset(){
      this.search.product_id = ''
      this.search.description = ''
      this.search.code = ''
      this.key = ''
      this.value = ''
      this.getProgramCodes()
    },
    searchProgram(){
      var url = '/api/programCodes/list/1/';
      this.key ='';
      this.value = ''
      var product_id = this.search.product_id ? this.search.product_id:""
      if (product_id){
        this.key += "product_id,"
        this.value += this.search.product_id+","
      }

      var code = this.search.code ? this.search.code:""
      if (code){
        this.key += "code,"
        this.value += this.search.code+","
      }
      var description = this.search.description ? this.search.description:""
      if (description){
        this.key += "description,"
        this.value += this.search.description+","
      }

      var status = this.search.status ? this.search.status:""
      if (status){
        this.key += "status,"
        this.value += this.search.status+","
      }

      this.key = this.key? this.key.substring(0, this.key.length - 1):'_'
      this.value = this.value? this.value.substring(0, this.value.length - 1) : "_"
      url += this.key+"/"+this.value

      this.get(url);
    },
    goTo(link){
      this.getZones(link)
    },
    get(link){
      this.ajax_loading = true
       u.a().get(link)
          .then(response => {
            this.programCodes = response.data.program_codes
            this.pagination = response.data.pagination
            this.ajax_loading = false
          }).catch(e => console.log(e));
    },
    getProgramCodes(page_url) {
      const key =  '_'
      const fil =  '_'
      page_url = page_url ? '/api'+page_url : '/api/programCodes/list/1'
      if (this.key){
        page_url += '/'+this.key+'/'+this.value
      }else page_url+= '/' + key + '/' + fil
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
      disabledDeleteProgramCode(){
          alert('Hệ thống chỉ cho phép xóa mã quy chiếu trạng thái ngừng hoạt động')
      },
      deleteProgramCode(id, index){
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa mã quy chiếu này không?");
        if (delStdConf === true) {
                // console.log(`xId = ${xId}, Index = ${idx}`)
        axios.delete('/api/programCodes/'+id)
          .then(response => {
            this.programCodes.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
      filterProgramCode(){
        return this.programCodes.filter((programCode) => {
          return programCode.name.match(this.search);
        // alert('not finish')
      });
      },
      goTo(link) {
        this.getProgramCodes(link)
      },
    },
    computed:{
      filterByProgramCode(){
        return this.filterProgramCode();
      }
    },
    created(){
      this.getProgramCodes()
      this.filterProgramCode()
      u.a().get(`/api/all/products`).then(response =>{
        this.products = response.data;
      })
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
