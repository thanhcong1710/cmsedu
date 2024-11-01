<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Ký hiệu</label>
                <input type="text" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Tên ký hiệu</label>
                <select v-model="product" class="form-control" id="">
                  <option selected :value="0">Lựa chọn ký hiệu</option>
                  <option value="1">Ký hiệu 1</option>
                  <option value="2">Ký hiệu 2</option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Ký hiệu EFFECT</label>
                <select v-model="region" class="form-control" id="">
                  <option value="0">Chọn Ký hiệu EFFECT</option>
                  <option value="1">Ký hiệu EFFECT 1</option>
                  <option value="2">Ký hiệu EFFECT 2</option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select v-model="status" class="form-control" id="">
                  <option selected :value="0">Hoạt động</option>
                  <option :value="1">Không hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div class="">
            <button class="btn btn-success" @click.prevent="filtertypecharges">Tìm kiếm</button>
            <button class="btn btn-default" type="reset">Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>Danh Sách Các Ký Hiệu Thu Phí</strong>
          </div>
          <div class="col-4 table-header">
            <router-link class="btn btn-primary" :to="'/typechargess/add-typecharges'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <router-link class="btn btn-success" :to="{ path: '/' }">
              <i class="fa fa-file-word-o"></i> Export
            </router-link>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th>Ký hiệu</th>
                <th>Tên ký hiệu</th>
                <th>Ký hiệu EFFECT</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <!-- typecharges data -->
              <tr v-for="(typecharges, index) in filterBytypecharges" v-model="typecharges.status">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-center cl-btn">
                  <router-link class="btn btn-sm btn-info" :to="{name: 'Chi Tiết Thông Tin Sản Phẩm', params: {id: typecharges.id}}">
                    <span class="fa fa-list"></span>
                  </router-link>
                  <router-link class="btn btn-sm btn-warning" :to="{name: 'Sửa Thông Tin Sản Phẩm' , params: {id: typecharges.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteTypeCharges(typecharges.id, index)" class="btn btn-sm btn-danger">
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
  name: 'List-Type-Charge',
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

        typechargess: [],
        typecharges: '',
        router_url: '/type-charges/list',
        pagination_id: 'type-charges_paging',
        pagination_class: 'type-charges paging list',
        list_style: 'line',
        pagination: {
          limit: 5,
          spage: 0,
          ppage: 0,
          npage: 0,
          lpage: 0,
          cpage: 0,
          total: 0,
          pages: []
        },
        search: '',
        statusColor: ''
      }
    },
    created() {
      this.getTypeCharges()
      this.filterTypeCharges()
      let uri = `/api/products`;
      axios.get(uri).then((response) => {
        this.products = response.data.data;
      });

      axios.get(`/api/regions`).then((response) => {
        this.regions = response.data.data;
      });
    },
    methods: {
      getTypeCharges(page_url) {
        let vm = this;
        page_url = page_url || '/api/type-charges'
        axios.get(page_url)
        .then(response => {
          this.typechargess = response.data.data
          this.pagination = response.data
          vm.makePagination(response.meta, response.links);
        }).catch(e => console.log(e));
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
      deleteTypeCharges(id, index) {
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa hồ sơ của học sinh này không?");
        if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          axios.delete('/api/type-charges/' + id)
          .then(response => {
            this.typechargess.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
      filterTypeCharges() {
        return this.typechargess.filter((typecharges) => {
          return typecharges.name.match(this.search);
          // alert('not finish')
        });
      },
      goTo(link) {
          this.getTypeCharges(link)
        },
    },
    computed: {
      filterBytypecharges() {
        return this.filterTypeCharges();
      }
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
