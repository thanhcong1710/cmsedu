<template>
  <div class="animated fadeIn apax-form">
    <loader :spin="loader.spin" :text="loader.text" :active="loader.processing" :duration="loader.duration"/>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>
              <i class="fa fa-filter"></i>  Bộ lọc
            </strong>
          </div>
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label class="control-label">Tên mẫu tin nhắn </label>
                <br />
                <p class="input-group-addon filter-lbl">
                  <i v-b-tooltip.hover title="Lọc theo tên mẫu tin nhắn" class="fa fa-leanpub"></i>
                </p>
                <input type="text" v-model="search.keyword" class="filter-selection customer-type form-control" />
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <select class="form-control" v-model="search.status" >
                    <option value="-1">Tất Cả</option>
                    <option value="0">Không hoạt động</option>
                    <option value="1">Hoạt động</option>
                </select>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <router-link class="apax-btn full reset" :to="'/sms/template/add'">
              <i class="fa fa-plus"></i> Thêm mới
            </router-link>
            <button class="apax-btn full detail" @click.prevent="searchtemplate">
              <i class="fa fa-search"></i> Tìm kiếm
            </button>
            <button class="apax-btn full defalut" type="reset" @click.prevent="reset">
              <i class="fa fa-ban"></i> Bỏ lọc
            </button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <strong>
              <i class="fa fa-list"></i> Danh sách mẫu tin nhắn
            </strong>
          </div>
          <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th>STT</th>
                <th>Loại tin</th>
                <th>Tên mẫu tin nhắn</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(template, index) in templates" :key="index">
                <td>{{(pagination.ppage * pagination.limit) + index + 1}}</td>
                <td>{{template.type ==0 ?'SMS' :'Email'}}</td>
                <td>{{template.title}}</td>
                <td>{{template.content}}</td>
                <td>{{template.status | nameStatus}}</td>
                <td>
                  <router-link class="apax-btn edit" :to="`/sms/template/${template.id}/edit`" title="Chỉnh sửa ">
										<i class="fa fa-pencil"></i>
									</router-link>
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
                :firstPage="pagination.spage"
                :previousPage="pagination.ppage"
                :nextPage="pagination.npage"
                :lastPage="pagination.lpage"
                :currentPage="pagination.cpage"
                :pagesItems="pagination.total"
                :pageList="pagination.pages"
                :pagesLimit="20"
                :routing="goTo"
              ></appPagination>
            </nav>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Pagination from "../../../components/Pagination";
import u from "../../../utilities/utility";
import moment from "moment";
import loader from '../../../components/Loading'
import select from 'vue-select'
export default {
  components: {
    "vue-select": select, 
    appPagination: Pagination, loader
  },
  name: "List-Room",
  data() {
    var current = new Date();
    return {
      product: "0",
      products: [],
      status: "0",
      templates: [],
      router_url: "api/sms/template/lists",
      pagination_id: "templates_paging",
      list_style: "line",
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
        keyword: "",
        status: -1,
        zone_id:0,
      },
      loader: {
        spin: 'mini',
        text: "Đang tải dữ liệu...!",
        processing: false,
        duration: 300,
      },
      session: u.session()
    };
  },
  created() {
    this.getTemplate();
  },
  methods: {
    getTemplate() {
      this.loader.processing = true
      let url = "/api/sms/template/lists/1"
      this.get(url)
    },
    get(link) {
      let params = {
        keyword: this.search.keyword,
        status: this.search.status,
      }
      u.g(link, params).then(response => {
          this.loader.processing = false
          this.templates = response.lists
          this.pagination = response.pagination;
        })
        .catch(e => {
          this.loader.processing = false
        })
    },
    reset() {
      location.reload()
    },
    searchtemplate() {
      this.getTemplate()
    },
    goTo(link) {
      this.loader.processing = true
      this.get(link)
    },
  },
  filters: {
    nameStatus(value) {
      return value == 1 ? "Hoạt động" : "Không hoạt động";
    }
  }
};
</script>
<style>
.apax-form .table td{
   text-transform: none;
}
</style>