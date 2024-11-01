<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong><i class="fa fa-list"></i> Hướng Dẫn Về Điểm Số</strong>
          </div>
          <div class="col-4 table-header">
            <router-link class="apax-btn full reset" :to="'/scoring-guidelines/add'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th>Điểm</th>
                <th>Hướng dẫn</th>
                <th>Giải thích</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in scoring_guidelines" :key="item.id">
                <td>{{index + 1}}</td>
                <td>{{item.score}}</td>
                <td>{{item.guideline}}</td>
                <td>{{item.explanation}}</td>
                <td>{{item.status | getStatusName}}</td>
                <td>
                  <router-link class="apax-btn edit" :to="{name: 'Cập Nhật Hướng Dẫn Điểm Số' , params: {id: item.id}}">
                    <span class="fa fa-pencil"></span>
                  </router-link>
                  <button @click="deleteScoringGuidelines(item.id, index)" class="apax-btn remove" v-if="item.status==0">
                    <span class="fa fa-times"></span></button>
                  <button @click="disabledDeleteScoringGuidelines()" class="apax-btn remove" v-else>
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
              :pagesLimit="pagination.limit"
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
  name: 'List-Scoring-Guidelines',
  data() {
    return {
      scoring_guidelines: [],
      scoring_guideline: {
        score: '',
        guideline: '',
        explanation: '',
        status: ''
      },
      search: {
        score: '',
        guideline: '',
        explanation: '',
        status: ''
      },
      router_url: '/scoring_guidelines/list',
      pagination_id: 'scoring_guidelines_paging',
      pagination_class: 'scoring-guideliness paging list',
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
      this.getScoringGuidelines(link)
    },
    get(link){
      this.ajax_loading = true
       u.a().get(link)
          .then(response => {
            this.scoring_guidelines = response.data.scoring
            this.pagination = response.data.pagination
            this.ajax_loading = false
          }).catch(e => console.log(e));
    },
    getScoringGuidelines(page_url) {
      const key = this.keyword ? this.keyword : '_'
      page_url = page_url ? '/api'+page_url : '/api/scoring_guidelines/list/1'
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
    disabledDeleteScoringGuidelines(){
        alert('Hệ thống chỉ cho phép xóa hướng dẫn trạng thái ngừng hoạt động')
    },
    deleteScoringGuidelines(id, index) {
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa hướng dẫn này không?");
      if (delStdConf === true) {
          // console.log(`xId = ${xId}, Index = ${idx}`)
          axios.delete('/api/scoring_guidelines/' + id)
          .then(response => {
            this.scoring_guidelines.splice(index, 1);
          })
          .catch(error => {
          });
        }
      },
      filterScoringGuidelines() {
        return this.scoring_guidelines.filter((scoring_guidelines) => {
          return scoring_guidelines.name.match(this.search);
          // alert('not finish')
        });
      },
      goTo(link) {
          this.getScoringGuidelines(link)
        },
    },
    computed: {
      filterByScoringGuidelines() {
        return this.filterScoringGuidelines();
      }
    },
    created() {
      this.getScoringGuidelines()
      this.filterScoringGuidelines()
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
