<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i>Danh sách khu vực</strong>
          </div>
           <div class="panel-body">
            <div class="controller-bar table-header">
              <router-link class="apax-btn full reset" :to="'/zones/add-zone'">
                <i class="fa fa-plus"></i> Thêm mới
              </router-link>
            </div>
            <table class="table table-bordered apax-table">
              <thead>
                <tr>
                  <th class="text-center">STT</th>
                  <th>Mã khu vực</th>
                  <th>Tên khu vực</th>
                  <th>Trạng thái</th>
                  <th><i class="fa fa-cog fa-2x"></i></th>
                </tr>
              </thead>
              <tbody>
                <!-- zone data -->
                <tr v-for="(zone, index) in zones" :key="index">
                  <td>{{index+1}}</td>
                  <td>{{zone.id}}</td>
                  <td>{{zone.name}}</td>
                  <td :class="{statusColor}">{{zone.status | getStatusName}}</td>
                  <td class="text-center cl-btn">
                    <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Khu Vực', params: {id: zone.id}}">
                      <span class="fa fa-eye"></span>
                    </router-link>
                    <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Khu Vực' , params: {id: zone.id}}">
                      <span class="fa fa-pencil"></span>
                    </router-link>
                    <button @click="deleteZone(zone.id, index)" class="apax-btn remove" v-if="zone.status==0"><span class="fa fa-times"></span></button>
                    <button @click="disabledDeleteZone()" class="apax-btn remove" v-else>
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
  name: 'List-Zone',
  data() {
    return {
      zones: [],
      zone: {
        name: '',
        lms_proc_id: '',
        status: ''
      },
      router_url: '/zones/list',
      pagination_id: 'zones_paging',
      pagination_class: 'zones paging list',
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
      this.getZones(link)
    },
    get(link){
      this.ajax_loading = true
       u.a().get(link)
          .then(response => {
            this.zones = response.data.zones
            this.pagination = response.data.pagination
            this.ajax_loading = false
          }).catch(e => console.log(e));
    },
    getZones(page_url) {
      const key = this.keyword ? this.keyword : '_'
      page_url = page_url ? '/api'+page_url : '/api/zones/list/1'
      page_url+= '/' + key + '/_'
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
    disabledDeleteZone(){
        alert('Hệ thống chỉ cho phép xóa khu vực trạng thái ngừng hoạt động')
    },
    deleteZone(id, index) {
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa khu vực này không?");
      if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          axios.delete('/api/zones/' + id)
          .then(response => {
            this.zones.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
      filterZone() {
        return this.zones.filter((zone) => {
          return zone.name.match(this.search);
          // alert('not finish')
        });
      },
      goTo(link) {
          this.getZones(link)
        },
    },
    computed: {
      filterByzone() {
        return this.filterZone();
      }
    },
    created() {
      this.getZones()
      this.filterZone()
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
    margin-left: 0px;
  }

  .img img {
    width: 100px;
  }

  a {
    /* color: blue; */
  }
  .apax-btn.reset:hover{
    color: #fff;
    text-decoration: none;
  }
  .cl-btn a {
    color: #fff;
  }

  </style>
