<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
          header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <strong><i class="fa fa-list"></i> Danh Sách Các Hạng Học Sinh</strong>
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
                    <div class="panel-body">
                      <div class="newtoolbar overhidden">
                        <div id="demo-custom-toolbar2" class="table-toolbar-left ml-2">

                          <router-link class="apax-btn full reset" :to="'/scores/add-score'"><i class="fa fa-plus"></i> Thêm mới</router-link>
                        </div>
                      </div>
                      <div class="table-responsive scrollable">
                        <table class="table table-striped table-bordered apax-table">
                          <thead>
                            <tr class="text-sm">
                              <th class="text-center width-50">STT</th>
                              <th class="width-150">Tên xếp hạng</th>
                              <th>Mô tả</th>
                              <th>Trạng thái</th>
                              <th>Đối tượng</th>
                              <th class="width-50">Lựa chọn</th>
                              <!--<th>Người tạo</th>-->
                              <!--<th>Người sửa</th>-->
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(studentRank, index) in studentRanks" :key="index">
                              <td class="text-center">{{index+1}}</td>
                              <td>{{studentRank.name}}</td>
                              <td>{{studentRank.description}}</td>
                              <td>{{studentRank.status | statusRank}}</td>
                              <td>{{studentRank.type |rankTypeToName}}</td>
                              <td class="text-center">
                                <router-link title="Xem chi tiết" v-b-tooltip.hover class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Hạng Học Sinh', params: {id: studentRank.id}}"><i class="fa fa-eye"></i></router-link>
                                <router-link title="Sửa Thông Tin Hạng Học Sinh" v-b-tooltip.hover class="apax-btn edit" :to="{name: 'Sửa Thông Tin Hạng Học Sinh', params: {id: studentRank.id}}"><i class="fa fa-pencil"></i></router-link>
                                <button title="Xóa Thông Tin Hạng Học Sinh" v-b-tooltip.hover class="apax-btn remove" @click="deleteRank(studentRank.id,index)"><i class="fa fa-times"></i></button>
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
  name: 'List-Score',
  data () {
    return {
      studentRanks: [] ,
      router_url: '/ranks/list',
      pagination_id: 'scores_paging',
      pagination_class: 'scores paging list',
      list_style: 'line',
      scores: [],
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
  filters: {
    statusRank(value){
      return value === 1 ? 'Hoạt động' : 'Không hoạt động'
    }
  },
  created(){
    this.getScores()
  },
  methods: {
    get(link){
        this.ajax_loading = true
        u.a().get(link).then(response => {
          this.studentRanks = response.data.ranks
          this.pagination = response.data.pagination
          this.ajax_loading = false
        }).catch(e => console.log(e));
      },
      getScores(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/ranks/list/1'
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
        this.getScores(link)
      },
    deleteRank(id, index){
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa xếp loại học sinh này không?");
      if (delStdConf === true) {
              // console.log(`xId = ${xId}, Index = ${idx}`)
              u.a().delete(`/api/ranks/${id}`)
              .then(response => {
                this.studentRanks.splice(index, 1);
              })
              .catch(error => {
              });
            }
          }
        }
      }
      </script>

      <style scoped>

      </style>