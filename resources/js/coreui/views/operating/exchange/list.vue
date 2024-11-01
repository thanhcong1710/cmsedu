<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div v-show="flags.requesting" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div
                v-show="flags.requesting"
                class="loading-text cssload-loader"
              >Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
            </div>
          </div>

          <div slot="header">
            <i class="fa fa-filter"></i>
            <b class="uppercase">Bộ lọc</b>
          </div>
          <div class="content-detail">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <searchBranch
                        :id="html.filter.branch.id"
                        :onSelect="selectBranch"
                        :disabled="html.filter.branch.disabled"
                        :placeholder="html.filter.branch.placeholder"
                      ></searchBranch>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="filter-label control-label">Mã CMS</label>
                      <br>
                      <p class="input-group-addon filter-lbl">
                        <i class="fa fa-search"></i>
                      </p>
                      <input
                        type="text"
                        class="form-control filter-selection"
                        v-model="filter.lms_effect_id"
                        :disabled="html.filter.lms_effect.disabled"
                      >
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="filter-label control-label">Tên học sinh</label>
                      <br>
                      <p class="input-group-addon filter-lbl">
                        <i class="fa fa-search"></i>
                      </p>
                      <input
                        type="text"
                        class="form-control filter-selection"
                        v-model="filter.student_name"
                        :disabled="html.filter.student.disabled"
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <button class="apax-btn full edit" @click="filterData(1)">
              <i class="fa fa-search"></i> Tìm kiếm
            </button>
            <button class="apax-btn full" @click="removeFilter()">
              <i class="fa fa-ban"></i> Bỏ lọc
            </button>
            <router-link to="/exchange/add-exchange" v-show="canAction()">
              <button type="button" class="apax-btn full remove">
                <i class="fa fa-plus"></i> Thêm mới
              </button>
            </router-link>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div v-show="flags.form_loading" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div
                v-show="flags.form_loading"
                class="loading-text cssload-loader"
              >Đang tải dữ liệu...</div>
            </div>
          </div>
          <div v-show="flags.requesting" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div
                v-show="flags.requesting"
                class="loading-text cssload-loader"
              >Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
            </div>
          </div>

          <div slot="header">
            <i class="fa fa-list"></i>
            <b class="uppercase">Danh sách quy đổi</b>
          </div>
          <div id="list_content" class="panel-heading">
            <div class="panel-body">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="text-sm">
                      <th>STT</th>
                      <th>Mã CMS</th>
                      <th>Mã Effect</th>
                      <th>Tên học sinh</th>
                      <th>Trung tâm</th>
                      <th>Sản phẩm trước</th>
                      <th>Tên gói phí trước</th>
                      <th>Số buổi trước</th>
                      <th>Sản phẩm sau</th>
                      <th>Tên gói phí sau</th>
                      <th>Số buổi sau</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in list" :key="index">
                      <td>{{ ((pagination.cpage - 1)*pagination.limit + index + 1) }}</td>
                      <td>{{item.crm_id}}</td>
                      <td>{{item.accounting_id}}</td>
                      <td>{{item.name}}</td>
                      <td>{{item.branch_name}}</td>
                      <td>{{item.product_name_from}}</td>
                      <td>{{item.tuition_fee_name_from}}</td>
                      <td>{{item.summary_sessions_from}}</td>
                      <td>{{item.product_name}}</td>
                      <td>{{item.tuition_fee_name}}</td>
                      <td>{{item.summary_sessions}}</td>
                    </tr>
                  </tbody>
                </table>
                <div class="text-center">
                  <nav aria-label="Page navigation">
                    <paging
                      :rootLink="pagination.url"
                      :id="pagination.id"
                      :listStyle="pagination.style"
                      :customClass="pagination.class"
                      :firstPage="pagination.spage"
                      :previousPage="pagination.ppage"
                      :nextPage="pagination.npage"
                      :lastPage="pagination.lpage"
                      :currentPage="pagination.cpage"
                      :pagesItems="pagination.total"
                      :pagesLimit="pagination.limit"
                      :pageList="pagination.pages"
                      :routing="redirect"
                    ></paging>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import u from "../../../utilities/utility";
import paging from "../../../components/Pagination";
import search from "../../../components/Search";
import searchBranch from "../../../components/Selection";

export default {
  components: {
    searchBranch,
    paging,
    search
  },
  name: "class-transfers",
  data() {
    return {
      list: [],
      filter: {
        lms_effect_id: "",
        student_name: "",
        branch_id: ""
      },
      html: {
        filter: {
          branch: {
            id: "search_branch",
            placeholder: "Tìm kiếm trung tâm",
            disabled: false
          },
          student: {
            disabled: false
          },
          lms_effect: {
            disabled: false
          }
        },
      },
      flags: {
        form_loading: false,
        requesting: false
      },
      pagination: {
        url: "",
        id: "",
        style: "line",
        class: "",
        spage: 1,
        ppage: 1,
        npage: 1,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        pages: []
      },
      order: {
        by: "s.id",
        to: "DESC"
      }
    };
  },
  created() {
    let role = parseInt(u.session().user.role_id);
    let branches = u.session().user.branches;
    if (branches.length == 1) {
      this.html.filter.branch.disabled = true;
      this.html.filter.branch.placeholder = branches[0].name;
      this.filter.branch_id = branches[0].id;
    }
    this.filterData();
  },
  methods: {
    canAction() {
      let arr_roles = [55, 56, 676767,686868, 7777777, 999999999];
      return arr_roles.indexOf(parseInt(u.session().user.role_id)) == -1 ? false : true;
    },
    selectBranch(data) {
      this.filter.branch_id = parseInt(data.id);
      this.filterData(1);
    },
    filterData(type = 0) {
      if (this.flags.form_loading === false) {
        this.flags.form_loading = true;
        if (type) {
          this.resetPagination();
        }
        let data = JSON.stringify(this.filter);
        let pagination = JSON.stringify(this.pagination);
        u.a()
          .get(`/api/exchange?filter=${data}&pagination=${pagination}`)
          .then(response => {
            this.flags.form_loading = false;
            this.list = response.data.data.items;
            this.pagination = response.data.data.pagination;
          });
      }
    },
    redirect(link) {
      const info = link.toString().split("/");
      const page = info.length > 1 ? info[1] : 1;
      this.pagination.cpage = parseInt(page);
      this.filterData();
    },
    resetPagination() {
      this.pagination = {
        url: "",
        id: "",
        style: "line",
        class: "",
        spage: 1,
        ppage: 1,
        npage: 1,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        pages: []
      };
    },
    removeFilter() {
      location.reload();
    }
  }
};
</script>

<style scoped>
.search-more {
  margin-top: 10px;
}
.search-more-button {
  color: #e30000;
  cursor: pointer;
}
.hide {
  display: none;
}
.show {
}
</style>


