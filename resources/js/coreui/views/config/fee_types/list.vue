<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>Danh mục loại thu phí</strong>
          </div>
          <div class="col-4 table-header">
            <router-link class="btn btn-primary" :to="'/feetypes/add-fee-type'">
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
                <th>Tên loại thu phí</th>
                <th>Gói phí áp dụng</th>
                <th>Danh mục cha</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <!-- feetype data -->
              <tr v-for="(feetype, index) in filterByFeeType" v-model="feetype.status">
                <td>{{index+1}}</td>
                <td>{{feetype.name}}</td>
                <td>{{feetype.id}}</td>
                <td>{{feetype.lms_proc_id}}</td>
                <td :class="{statusColor}">{{feetype.status | getStatusName}}</td>
                <td class="text-center cl-btn">
                  <router-link class="btn btn-sm btn-info" :to="{name: 'Chi Tiết Thông Tin Sản Phẩm', params: {id: feetype.id}}">
                    <span class="fa fa-list"></span>
                  </router-link>
                  <router-link class="btn btn-sm btn-warning" :to="{name: 'Sửa Thông Tin Sản Phẩm' , params: {id: feetype.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteFeeTypes(feetype.id, index)" class="btn btn-sm btn-danger"><span class="fa fa-times"></span></button>
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
  name: 'List-Fee-Type',
  data() {
    return {
        //set selected defaut
        // fees: '0',
        // area: '0',
        // product: '0',
        // status: '0',
        //

        feetypes: [],
        feetype: {
          name: '',
          lms_proc_id: '',
          status: ''
        },
        router_url: '/fee-types/list',
        pagination_id: 'fee-types_paging',
        pagination_class: 'fee-types paging list',
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
    methods: {
      getFeeTypes(page_url) {
        let vm = this;
        page_url = page_url || '/api/feetypes'
        axios.get(page_url)
        .then(response => {
          this.feetypes = response.data.data
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
      deleteFeeTypes(id, index) {
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa hồ sơ của học sinh này không?");
        if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          axios.delete('/api/feetypes/' + id)
          .then(response => {
            this.feetypes.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
      filterFeeTypes() {
        return this.feetypes.filter((feetype) => {
          return feetype.name.match(this.search);
          // alert('not finish')
        });
      },
      goTo(link) {
      this.getFeeTypes(link)
      },
    },
    computed: {
      filterByFeeType() {
        return this.filterFeeTypes();
      }
    },
    created() {
      this.getFeeTypes()
      this.filterFeeTypes()
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
