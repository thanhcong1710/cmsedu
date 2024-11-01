<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh sách Hình thức giảm trừ</strong>
          </div>
          <div class="col-4 table-header">
            <router-link class="apax-btn full reset" :to="'/discounts-forms/add-discounts_form'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th>Tên hình thức giảm trừ</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Lựa chọn</th>
              </tr>
            </thead>
            <tbody>
              <!-- discount data -->
              <tr v-for="(discount, index) in discounts" :key="index">
                <td>{{ indexOf(index+1) }}</td>
                <td>{{ discount.name }}</td>
                <td>{{ discount.description }}</td>
                <td>{{ discount.status|getStatusToName }}</td>
                <td class="text-center cl-btn">
                  <router-link class="apax-btn edit" :to="{name: 'Sửa Hình Thức Giảm Trừ' , params: {id: discount.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteDiscount(discount.id, index)" class="apax-btn remove" v-if="discount.status==0"><span class="fa fa-times"></span></button>
                  <button @click="disabledDeleteDiscount()" class="apax-btn remove" v-else>
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
  name: 'List-discount',
  data() {
    return {
      discounts: [],
      discount: {
        name: '',
        description: ''
      },
      router_url: '/discounts/list',
      pagination_id: 'discounts_forms_paging',
      pagination_class: 'discounts_forms paging list',
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
      search: '',
      statusColor: ''
    }
  },
  methods: {
    goTo(link){
      this.getDiscounts(link)
      },
      get(link){
        this.ajax_loading = true
         u.a().get(link)
            .then(response => {
              this.discounts = response.data.discounts
              this.pagination = response.data.pagination
              this.ajax_loading = false
            }).catch(e => console.log(e));
      },
      getDiscounts(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/discounts/list/1'
        page_url+= '/' + key + '/' + fil
        this.get(page_url)
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
    disabledDeleteDiscount(){
        alert('Hệ thống chỉ cho phép xóa hình thức giảm trừ trạng thái ngừng hoạt động')
    },
    deleteDiscount(id, index) {
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa hình thức giảm trừ này không?");
      if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          axios.delete('/api/discounts/' + id)
          .then(response => {
            this.discounts.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
    indexOf(value){
      return (value+1)+((this.pagination.cpage-1)*this.pagination.limit)
    },
      filterDiscount() {
        return this.discounts.filter((discount) => {
          return discount.name.match(this.search);
          // alert('not finish')
        });
      }
    },
    computed: {
      filterByDiscount() {
        return this.filterDiscount();
      }
    },
    created() {
      this.getDiscounts()
      this.filterDiscount()
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
