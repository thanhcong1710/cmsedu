<template>
  <div class="animated fadeIn apax-form" @keyup="binding" id="charges-function">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
          </div>
          <div class="filter content">
            <div class="row">
              <div class="col-lg-12 filter-box" :class="html.class.display.filter.fields">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-sm-3" :class="html.class.display.filter.branch">
                        <label class="filter-label control-label">Trung Tâm</label><br/>
                        <suggestion
                          class="select-branch"
                          :onSelect="dom.branch.action"
                          :disabled="false"
                          :placeholder="dom.branch.placeholder"
                        ></suggestion>
                      </div>
                      <div class="col-sm-3">
                        <label class="filter-label control-label">Tìm Kiếm</label><br/>
                        <search
                          :onSearch="searching"
                          :placeholder="html.placeholder.filter.search"
                        >
                        </search>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="filter-label control-label">Trạng thái</label><br/>
                          <select
                            v-model="cache.status"
                            @change="searching()"
                            data-placeholder="Chọn hình thức đóng phí"
                            id="select_payload"
                            class="filter-selection payload form-control"
                          >
                            <option value="-1">Tất cả</option>
                            <option value="0">Chưa duyệt</option>
                            <option value="1">Đã duyệt</option>
                            <option value="2">Từ chối</option>
                          </select>
                        </div>
                      </div>
                      
                      <!-- <div class="col-sm-3">
                        <div class="form-group">
                          <label class="filter-label control-label">Lọc Theo Sản Phẩm</label><br/>
                          <p class="input-group-addon filter-lbl"><i v-b-tooltip.hover title="Lọc theo sản phẩm" class="fa fa-fort-awesome"></i></p>
                          <select
                            :disabled="html.disable.filter.product"
                            v-model="filter.product"
                            @change="selectFilter"
                            data-placeholder="Chọn gói sản phẩm"
                            id="select-product-filter"
                            name="search[product_filter]"
                            class="filter-selection product-filter form-control"
                          >
                            <option value="">Tất cả</option>
                            <option :value="product.id" v-for="(product, ind) in list.products" :key="ind">{{ product.name }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="filter-label control-label">Gói Học Phí</label><br/>
                          <p class="input-group-addon filter-lbl"><i v-b-tooltip.hover title="Lọc theo gói học phí" class="fa fa-shopping-bag"></i></p>
                          <select
                            :disabled="html.disable.filter.tuition_fee"
                            v-model="filter.tuition_fee"
                            @change="selectFilter"
                            data-placeholder="Chọn gói học phí"
                            id="select_tuition_fee"
                            name="search[tuition_fee_filter]"
                            class="filter-selection tuition-fee form-control"
                          >
                            <option value="">Tất cả</option>
                            <option :value="tuition_fee.id" v-for="(tuition_fee, ind) in list.tuition_fees" :key="ind">{{ tuition_fee.name }}</option>
                          </select>
                        </div>
                      </div> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row drag-me-up">
      <div class="col-12">
        <div :class="html.class.loading ? 'loading' : 'standby'" class="ajax-loader">
          <img src="/static/img/images/loading/mnl.gif">
        </div>
        <b-card header>
          <div slot="header">
            <i class="fa fa-list"></i> <b class="uppercase">Danh sách chờ duyệt đóng phí.</b>
          </div>
          <div v-show="action.loading" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="action.loading" class="loading-text cssload-loader">{{ html.content.loading }}</div>
            </div>
          </div>
          <div class="list content panel-heading">
            <div class="panel-body">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                  <tr class="text-sm">
                    <th >STT</th>
                    <th>Tên học sinh</th>
                    <th>Mã CMS</th>
                    <th>Trung tâm</th>
                    <th>Sản phẩm</th>
                    <th>Gói phí</th>
                    <th>Số tiền đã đóng</th>
                    <th>Người tạo</th>
                    <th>Trạng thái</th>
                    <th>Người duyệt</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, ind) in items" :key="ind">
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{ind + 1}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.student_name}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.student_crm_id}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.branch_name}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.product_name}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.tuition_fee_name}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{formatAmount(item.charge_amount)}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.creator_name}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.approve_status==1 ? 'Đã duyệt' : ( item.approve_status==2 ? 'Từ chối duyệt' : 'Chưa duyệt')}}</router-link></td>
                    <td><router-link v-b-tooltip.hover :title="`Nhấp vào để thực hiện duyệt thu phí`" class="link-me" :to="`${url.page}${item.tmp_id}`">{{item.approver_name}}</router-link></td>
                    <td> 
                     <span class="apax-btn print" @click="print(item)">
                      <i v-b-tooltip.hover title="Nhấp vào để in bản ghi" class="fa fa-print"></i>
                    </span>
                    </td>
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
                      :routing="redirect">
                    </paging>
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

import moment from 'moment'

import paging from '../../../components/Pagination'
import search from '../../../components/Search'
import u from '../../../utilities/utility'
import suggestion from '../../../components/Selection'

export default {

 	name: 'Charges-List',

  components: {
    suggestion,
    paging,
    search
  },

	data(){
    const session_data = u.session()
		return {
      page: 'Bản Ghi Nhập Học',
      items: [],
      list: {
        payloads: [
          {
            id: 0,
            name: 'Đóng phí 1 lần'
          },
          {
            id: 1,
            name: 'Đóng phí nhiều lần'
          }
        ],
        products: session_data.info.products,
        tuition_fees: session_data.info.tuitions
      },
      action: {
        loading: true
      },
      dom: {
        branch: {
          options: [],
          disabled: true,
          display: 'hidden',
          action: branch => this.selectBranch(branch),
          placeholder: 'Lọc theo trung tâm'
        }
      },
      filter: {
        branch: '',
        keyword: '',
        payload: '',
        product: '',
        tuition_fee: '',
        status: ''
      },
      url: {
        page: '/waitapprove/',
        api: '/api/waitapprove/',
        load: '/api/waitapprove/list/'
      },
      cache: {
        branch: 0,
        keyword: '',
        status:-1
      },
      html: {
        id: {
        },
        class: {
          table: '',
          loading: false,
          display: {
            filter: {
              branch: 'hidden',
              fields: ''
            }
          }
        },
        disable: {
          filter: {
            search: true,
            payload: true,
            product: true,
            tuition_fee: true
          }
        },
        placeholder: {
          filter: {
            search: 'Từ khóa tìm kiếm',
            branch: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước'
          }
        },
        content: {
          loading: 'Đang tải dữ liệu...'
        }
      },
			pagination: {
        url: '',
        id: '',
        style: 'line',
        class: '',
        spage: 1,
        ppage: 1,
        npage: 2,
        lpage: 2,
        cpage: 1,
        total: 2,
        limit: 20,
        pages: []
      },
      order: {
        by: 'c.id',
        to: 'DESC'
      },
      temp: {},
      session: u.session()
		}
	},

	created(){
    this.start()
  },

  computed: {
  },

	methods: {
    formatAmount: (num) => num && num >= 1000 ? u.currency(num, 'đ') : '0đ',
    formatTime: (inputtime) => inputtime ? moment(inputtime).format('YYYY-MM-DD - HH:mm:ss') : '',
    link() {
      this.filter.branch = this.cache.branch
      this.filter.keyword = this.cache.keyword
      this.filter.status = this.cache.status
      const sort = u.jss(this.order)
      const search = u.jss(this.filter)
      const pagination = u.jss({
        spage: this.pagination.spage,
        ppage: this.pagination.ppage,
        npage: this.pagination.npage,
        lpage: this.pagination.lpage,
        cpage: this.pagination.cpage,
        total: this.pagination.total,
        limit: this.pagination.limit
      })
      return `${this.url.load}${pagination}/${search}/${sort}`
    },
    get(link, callback) {
      this.action.loading = true
      u.g(link)
      .then(response => {
        const data = response
        callback(data)
        setTimeout(() => {
          this.action.loading = false
        }, data.duration)
      }).catch(e => u.log('Exeption', e))
    },
    start() {
      if (u.authorized()) {
        this.html.class.display.filter.branch = 'display'
      } else {
        this.html.disable.filter.search = false
        this.html.disable.filter.payload = false
        this.html.disable.filter.product = false
        this.html.disable.filter.tuition_fee = false
        this.cache.branch = this.session.user.branch_id
      }
      this.searching()
    },
    load(data){
      this.items = data.list
      this.pagination = data.pagination
    },
    searching(word) {
      const key = u.live(word) && word != '' ? word : this.filter.keyword
      this.cache.keyword = key
      this.get(this.link(), this.load)
    },
    binding(e) {
      if(e.key == "Enter"){
        this.searching()
      }
    },
    redirect(link) {
      const info = link.toString().split('/')
      const page = info.length > 1 ? info[1] : 1
      this.pagination.cpage = parseInt(page)
      this.get(this.link(), this.load)
    },
    loading() {
    },
    extract() {
    },
    selectBranch(data) {
      if (data) {
        this.html.disable.filter.search = false
        this.html.disable.filter.payload = false
        this.html.disable.filter.product = false
        this.html.disable.filter.tuition_fee = false
        this.cache.branch = data.id
        this.get(this.link(), this.load)
      }
    },
    selectFilterByPayload() {
    },
    selectFilterByProduct() {
    },
    selectFilterByTuitionFee() {
    },
    selectFilter() {
      const payload_id = this.filter.payload ? parseInt(this.filter.payload, 10) : -1
      const product_id = this.filter.product ? parseInt(this.filter.product, 10) : -1
      const tuition_id = this.filter.tuition_fee ? parseInt(this.filter.tuition_fee, 10) : -1
    },
    print(item) {
        window.open(`/print/chargefee/${item.tmp_id}`,'_blank')
    }
 	}

}
</script>

<style scoped language="scss">

</style>
