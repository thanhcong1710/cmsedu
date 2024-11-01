<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter" /> <b class="uppercase">*Bộ lọc*</b>
          </div>
          <div class="row">
            <div
              class="col-sm-4"
              v-if="!hideSearchByBranch"
            >
              <div class="form-group">
                <label class="control-label">Trung tâm</label>
                <searchBranch
                  :on-select-branch="selectBranch"
                  placeholder-branch="Lọc theo trung tâm"
                />
              </div>
            </div>
            <div :class="hideSearchByBranch ? 'col-sm-6' : 'col-sm-4'">
              <div class="form-group">
                <label class="control-label">Tên học sinh mã học sinh</label>
                <input
                  class="form-control"
                  type="text"
                  v-model="search.student_name"
                >
              </div>
            </div>
            <div :class="hideSearchByBranch ? 'col-sm-6' : 'col-sm-4'">
              <div class="form-group">
                <label class="control-label">Người thực hiện</label>
                <input
                  class="form-control"
                  type="text"
                  v-model="search.hrm_id"
                  placeholder="Nhập mã HRM"
                >
              </div>
            </div>
          </div>
          <div class=" text-center">
            <button
              type="button"
              class="apax-btn full detail"
              @click="getCares()"
            >
              <i class="fa fa-search" /> Tìm kiếm
            </button>
            <div
              class="apax-btn full warning"
              @click="reload"
            >
              <i class="fa fa-refresh" /> Bỏ lọc
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row drag-me-up">
      <div class="col-12">
        <div
          :class="ajax_loading ? 'loading' : 'standby'"
          class="ajax-loader"
        >
          <img
            style="display:none"
            src="/static/img/images/loading/mnl.gif"
          >
        </div>
        <b-card header>
          <div slot="header">
            <i class="fa fa-list" /> <b class="uppercase">Danh sách học sinh</b>
          </div>
          <div class="controller-bar table-header">
            <router-link to="/students/add-care">
              <button
                type="button"
                class="btn btn-primary"
              >
                <i class="fa fa-plus" /> Thêm mới
              </button>
            </router-link>
            <button
              @click="extract"
              class="btn btn-success"
            >
              <i class="fa fa-file-word-o" /> Trích xuất
            </button>
          </div>
          <div
            id="list_content"
            class="panel-heading"
          >
            <div class="panel-body">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="text-sm">
                      <th>STT</th>
                      <th>Mã CMS</th>
                      <th>Mã Cyber</th>
                      <th>Tên học sinh</th>
                      <th>Phương thức</th>
                      <th>Trạng thái khách hàng</th>
                      <th>Điểm</th>
                      <th>Người thực hiện</th>
                      <th>Thời gian thực hiện</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(care, index) in cares"
                      :key="index"
                    >
                      <td>{{ index+1 }}</td>
                      <td>{{ care.crm_id }}</td>
                      <td>{{ care.accounting_id }}</td>
                      <td>{{ care.student_name }}</td>
                      <td>{{ care.contact_name }}</td>
                      <td>{{ care.quality_name }}</td>
                      <td>{{ care.quality_score }}</td>
                      <td>{{ care.creator }}</td>
                      <td>{{ care.created_at }}</td>
                      <td>
                        <router-link
                          class="apax-btn detail"
                          :to="`/students/detail-care/${care.id}`"
                        >
                          <span class="fa fa-eye" />
                        </router-link>
                        <router-link
                          class="apax-btn edit"
                          :to="`/students/${care.id}/edit-care`"
                        >
                          <i class="fa fa-edit" />
                        </router-link>
                        <button
                          @click="deleteCares(care.id, index)"
                          class="apax-btn remove"
                        >
                          <i class="fa fa-times" />
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div class="text-center">
                  <nav aria-label="Page navigation">
                    <appPagination
                      :root-link="router_url"
                      :id="pagination_id"
                      :list-style="list_style"
                      :custom-class="pagination_class"
                      :first-page="pagination.spage"
                      :previous-page="pagination.ppage"
                      :next-page="pagination.npage"
                      :last-page="pagination.lpage"
                      :current-page="pagination.cpage"
                      :pages-items="pagination.total"
                      :page-list="pagination.pages"
                      :routing="goTo"
                    />
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

<style scoped language="scss">
  .drag-me-up {
    margin: -25px -15px -15px;
  }
  .controller-bar {
    margin: 0 0 15px 0;
  }
</style>

<script>
import u from '../../utilities/utility'
import axios from 'axios'
import Pagination from '../../components/Pagination'
import searchBranch from '../../components/SearchBranchForTransfer'
export default {
  components: { appPagination: Pagination, searchBranch },
  name      : 'Cares',
  data () {
    return {
      session   : u.session(),
      branches  : [],
      cares     : [],
      pagination: {
        limit: 5,
        spage: 0,
        ppage: 0,
        npage: 0,
        lpage: 0,
        cpage: 0,
        total: 0,
        pages: [],
      },
      list_style      : 'line',
      router_url      : '/cares/list',
      pagination_id   : 'care_paginate',
      pagination_class: 'care_class',
      ajax_loading    : false,
      search          : {
        branch_id   : '',
        student_name: '',
        hrm_id      : '',
      },
    }
  },

  computed: {
    hideSearchByBranch () {
      return [
        69,
        68,
        55,
        56,
        686868,
      ].indexOf(parseInt(this.session.user.role_id, 10)) >= 0
    },
  },
  created () {
    this.getCares()
  },
  methods: {
    selectBranch (data) {
      this.search.branch_id = data.id
    },
    reload: function () {
      location.reload()
    },
    get (link) {
      this.ajax_loading = true
      u.a().get(link).then((response) => {
        this.cares        = response.data.cares
        this.pagination   = response.data.pagination
        this.ajax_loading = false
      }).catch((e) => console.log(e))
    },
    extract () {
      const data_string = JSON.stringify(this.search)
      const p           = `/api/exel/print-student-care/${data_string}`
      window.open(p, '_blank')
    },
    getCares (page_url) {
      this.key   = ''
      this.value = ''
      if (this.hideSearchByBranch)
        this.search.branch_id = this.session.user.branch_id

      if (this.search.branch_id != '') {
        this.key   += 'branch_id,'
        this.value += `${this.search.branch_id},`
      }
      if (this.search.student_name != '') {
        this.key   += 'student_name,'
        this.value += `${this.search.student_name},`
      }
      if (this.search.hrm_id != '') {
        this.key   += 'hrm_id,'
        this.value += `${this.search.hrm_id},`
      }
      this.key   = this.key ? this.key.substring(0, this.key.length - 1) : '_'
      this.value = this.value
        ? this.value.substring(0, this.value.length - 1)
        : '_'
      page_url   = page_url ? `/api${page_url}` : '/api/cares/list/1'
      page_url   += `/${this.key}/${this.value}`
      this.get(page_url)
    },
    makePagination (meta, links) {
      const pagination = {
        current_page : data.current_page,
        last_page    : data.last_page,
        next_page_url: data.next_page_url,
        prev_page_url: data.prev_page_url,
      }
      this.pagination  = pagination
    },
    goTo (link) {
      this.getCares(link)
    },
    deleteCares (id, index) {
      const delStdConf = confirm('Bạn có chắc rằng muốn xóa ngày này không?')
      if (delStdConf === true) {
        u.a().delete(`/api/customerCares/${id}`)
          .then((response) => {
            this.cares.splice(index, 1)
          })
          .catch((error) => {
          })
      }
    },
  },
}
</script>
