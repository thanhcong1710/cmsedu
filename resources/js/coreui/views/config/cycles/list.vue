<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
          header-tag="header"
          footer-tag="footer">
          <div slot="header">
            <strong><i class="fa fa-filter"></i> Bộ lọc</strong>
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

                    <form class="panel-body" method="post">
                      <div class="row">
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label class="control-label">Tên kỳ</label><br/>
                            <p class="input-group-addon filter-lbl">
                              <i v-b-tooltip.hover title="Lọc theo mã HRM" class="fa fa-search"></i>
                            </p>
                            <input type="text" v-model="search.name" class="filter-selection customer-type form-control">
                          </div>
                        </div>
                        <div class="col-sm-3">
                         <div class="form-group">
                          <label class="control-label">Năm</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo mã HRM" class="fa fa-search"></i>
                          </p>
                          <input v-model="search.year" class="filter-selection customer-type form-control">
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div> 
        <div slot="footer" class="text-center">
          <router-link class="apax-btn full reset" :to="'/cycles/add-cycles'"><i class="fa fa-plus"></i> Thêm mới</router-link>
          <button class="apax-btn full detail" @click.prevent="searchScore"><i class="fa fa-search"></i> Tìm kiếm</button>
          <button class="apax-btn full defalut" type="reset" @click.prevent="reset"><i class="fa fa-ban"></i> Bỏ lọc</button>
        </div> 
      </b-card>
    </b-col>
  </b-row>
  <b-row>
    <b-col cols="12">
      <b-card header>
        <div slot="header">
          <strong><i class="fa fa-list"></i> Danh Sách Các Kỳ Xếp Hạng</strong>
        </div>
        <div class="panel">
          
          <div class="panel-body">
            <div class="table-responsive scrollable">
              <table class="table table-striped table-bordered apax-table">
                <thead>
                  <tr class="text-sm">
                    <th class="text-center width-50">STT</th>
                    <th class="width-150">Tên kỳ</th>
                    <th class="width-150">Năm</th>
                    <th class="width-50">Trạng thái</th>
                    <th class="width-50"></th>
                    <!--<th>Người tạo</th>-->
                    <!--<th>Người sửa</th>-->
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(score, index) in scores" :key="index">
                    <td>{{indexOf(index)}}</td>
                    <td class="text-center">{{score.name}}</td>
                    <td>{{score.year}}</td>
                    <td>{{score.status | statusName}}</td>
                    <td class="text-center">

                      <router-link class="apax-btn detail" :to="{name: 'Chi Tiết Thông Tin Kỳ Xếp Hạng Nhân Viên', params: {id: score.id}}"><i class="fa fa-eye"></i></router-link>
                      <router-link class="apax-btn edit" :to="{name: 'Sửa Thông Tin Kỳ Xếp Hạng Nhân Viên', params: {id: score.id}}"><i class="fa fa-pencil"></i></router-link>

                      <button class="apax-btn remove" @click.prevent="deleteScore(score.id, index)"><i class="fa fa-times"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="text-center">
                <nav aria-label="Page navigation">
                  <paging
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
                  :pagesLimit="pagination.limit"
                  :routing="goTo"
                  >
                </paging>
              </nav>
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
import paging from '../../../components/Pagination'

export default {
  components: {
   paging
 },
  name: 'List-Cycle',
  data () {
    return {
      scores: [],
      router_url: '/scores/list',
      pagination_id: 'cycles_paging',
      pagination_class: 'cycles paging list',
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
        year: ''
      },
      key: '',
      value: ''
    }
  },
  methods: {
    reset(){
      this.key = ''
      this.value = ''
      this.search.name = ''
      this.search.year = ''
      this.getScores()
    },
    searchScore(){
        var url = '/api/scores/list/1/';
        this.key ='';
        this.value = ''
        var name = this.search.name ? this.search.name:""
        if (name){
          this.key += "name,"
          this.value += this.search.name+","
        }
        var year = this.search.year ? this.search.year :""
        if (year) {
          this.key += "year,"
          this.value += this.search.year+","
        }
        
        this.key = this.key? this.key.substring(0, this.key.length - 1):'_'
        this.value = this.value? this.value.substring(0, this.value.length - 1) : "_"
        url += this.key+"/"+this.value
        this.get(url);
    },
    indexOf(value){
      return (value+1)+((this.pagination.cpage-1)*this.pagination.limit)
    },
    goTo(link){
      this.getScores(link)
      },
      get(link){
        this.ajax_loading = true
         u.a().get(link)
            .then(response => {
              this.scores = response.data.scores
              this.pagination = response.data.pagination
              this.ajax_loading = false
            }).catch(e => console.log(e));
      },
      getScores(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/scores/list/1'
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
    deleteScore(id, index){
      const delStdConf = confirm("Bạn có chắc xóa ký xếp hạng này không ?");
      if (delStdConf === true) {
              // console.log(`xId = ${xId}, Index = ${idx}`)
              u.a().delete(`/api/scores/${id}`)
              .then(response => {
                this.scores.splice(index, 1);
              })
              .catch(error => {
              });
            }
          }
        },
        created(){
          this.getScores()
        },
        filters: {
          statusName(value){
            return value === 1 ? 'Hoạt động': 'Không hoạt động'
          }
        }
      }
      </script>

      <style scoped>

      </style>