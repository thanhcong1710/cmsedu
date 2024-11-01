<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
          header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh Sách Các Phân Vùng 12</strong>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div class="content-detail">
            <div id="page-content">
              <div class="row">
                <div class="col-lg-12">
                  <div class="panel">
                    <div class="panel-heading">
                      <div class="panel-control"></div>
                    </div>
                    <div class="panel-body">
                      <div class="controller-bar table-header">
                          <router-link class="apax-btn full reset" :to="'/regions/add-region'"><i class="fa fa-plus"></i> Thêm mới</router-link>
                      </div>
                      <div class="table-responsive scrollable">
                        <table class="table table-striped table-bordered apax-table">
                          <thead>
                            <tr class="text-sm">
                              <th class="width-50">STT</th>
                              <th class="width-150">Mã vùng</th>
                              <th class="width-150">Tên vùng</th>
                              <th >Giám đốc vùng</th>
                              <th class="width-150">Trạng thái</th>
                              <th class="width-50">Lựa chọn</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(region, index) in regions" :key="index">
                              <td>{{indexOf(index+1)}}</td>
                              <td>{{region.hrm_id}}</td>
                              <td>{{region.name}}</td>
                              <td>{{region.full_name}}</td>
                              <td>{{region.status|getTypeStatus}}</td>
                              <td class="text-center cl-btn">
                                <router-link title="Xem chi tiết" v-b-tooltip.hover class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Phân Vùng', params:{id: region.id}}">
                                  <span class="fa fa-eye"></span>
                                </router-link>

                                <router-link title="Sửa" v-b-tooltip.hover class="apax-btn edit" :to="{name: 'Sửa Thông Tin Phân Vùng', params:{id: region.id}}">
                                  <span class="fa fa-pencil"></span>
                                </router-link>

                                <button class="apax-btn remove" title="Xóa vùng" v-b-tooltip.hover @click="deleteRegion(region.id, index)" v-if="region.status==0"><i class="fa fa-times"></i></button>
                                <button @click="disabledDeleteRegion()" class="apax-btn remove" v-else>
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
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>  
      </b-card>
    </b-col>
  </b-row>
</div> 
</div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'
import Pagination from '../../../components/Pagination'

export default {
  components: {
   appPagination: Pagination
 },
  name: 'List-Region',
  data () {
    return {
      addNewRegionModal: false,
      regions: [],
      region: {},
      router_url: '/regions/list',
      pagination_id: 'regions_paging',
      pagination_class: 'regions paging list',
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
    }
  },
  created(){
    this.getRegions()
  },
  methods: {
    indexOf(value){
      return value+((this.pagination.cpage-1)*this.pagination.limit)
    },
    get(link){
        this.ajax_loading = true
        u.a().get(link).then(response => {
          this.regions = response.data.regions
          this.pagination = response.data.pagination
          this.ajax_loading = false
        }).catch(e => console.log(e));
      },
      addNewRegion(){
        this.addNewRegionModal = true
      },
      getRegions(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/regions/list/1'
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
        this.getRegions(link)
      },
      disabledDeleteRegion(){
          alert('Hệ thống chỉ cho phép xóa phân vùng trạng thái ngừng hoạt động')
      },
    deleteRegion(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa phân vùng này không?");
      if (delStdConf === true) {
              // console.log(`xId = ${xId}, Index = ${idx}`)
              u.a().delete('/api/regions/'+id)
              .then(response => {
                this.regions.splice(index, 1);
              })
              .catch(error => {
              });
            }
          }

          
        },

        computed:{
          getTypeStatus(){
            return this.getTypeStatus();
          }
        },

        filters: {
          getTypeStatus(value){
            return value == 1 ? "Hoạt động" : "Không Hoạt động"
          }
        }

      }
      </script>

      <style scoped>

      </style>