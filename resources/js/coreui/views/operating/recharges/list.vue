<template>
  <div class="animated fadeIn apax-form" @keyup="binding" id="contracts-management">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
          </div>
          <div id="filter_content">
            <div class="row">
              <div class="col-lg-12" :class="html.dom.filter.branch.display">
                <searchBranch
                    :onSelect="selectBranch"
                    :options="html.dom.filter.branch.options"
                    :disabled="html.dom.filter.branch.disabled"
                    :placeholder="html.dom.filter.branch.placeholder">
                </searchBranch><br/>
              </div>
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-sm-3">
                        <label class="filter-label control-label">Tìm Kiếm</label><br/>
                        <search
                          id="search_by_keyword"
                          name="search[keyword]"
                          :label="html.dom.filter.search.label"
                          :disabled="html.dom.filter.search.disabled"
                          :placeholder="html.dom.filter.search.placeholder"
                          :onSearch="searching"
                        >
                        </search>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="filter-label control-label">Loại Khách Hàng</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo loại khách hàng" class="fa fa-user-circle"></i>
                          </p>
                          <select
                            v-model="html.data.filter.customer_type"
                            id="select_customer_type"
                            name="search[customer_type]"
                            class="filter-selection customer-type form-control"
                            @change="html.dom.filter.customer_type.action"
                            :disabled="html.dom.filter.customer_type.disabled"
                            :placeholder="html.dom.filter.customer_type.placeholder">
                              <option v-for="(type, idx) in html.dom.filter.customer_type.data"
                                :value="type.id"
                                :key="idx">
                                {{ type.name }}
                              </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="filter-label control-label">Chương Trình Học</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo chương trình" class="fa fa-product-hunt"></i>
                          </p>
                          <select
                            v-model="html.data.filter.program"
                            id="select_program"
                            name="search[program]"
                            class="filter-selection program form-control"
                            @change="html.dom.filter.program.action"
                            :disabled="html.dom.filter.program.disabled"
                            :placeholder="html.dom.filter.program.placeholder">
                              <option v-for="(program, idx) in html.dom.filter.program.data"
                                :value="program.id"
                                :key="idx">
                                {{ program.name }}
                              </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label class="filter-label control-label">Gói Học Phí</label><br/>
                          <p class="input-group-addon filter-lbl">
                            <i v-b-tooltip.hover title="Lọc theo gói phí" class="fa fa-shopping-bag"></i>
                          </p>
                          <select
                            v-model="html.data.filter.tuition_fee"
                            id="select_tuition_fee"
                            name="search[tuition_fee]"
                            class="filter-selection tuition-fee form-control"
                            @change="html.dom.filter.tuition_fee.action"
                            :disabled="html.dom.filter.tuition_fee.disabled"
                            :placeholder="html.dom.filter.tuition_fee.placeholder">
                              <option v-for="(tuition_fee, idx) in html.dom.filter.tuition_fee.data"
                                :value="tuition_fee.id"
                                :key="idx">
                                {{ tuition_fee.full_name }}
                              </option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <router-link to="/recharges/add" v-show="onlyView()">
              <button type="button" class="apax-btn full reset">
                <i class="fa fa-plus"></i> Thêm mới
              </button>
            </router-link>
            <button @click="listContract(0)" class="apax-btn full edit"><i class="fa fa-list"></i> {{html.dom.filter.buttons.default}}</button>
            <button @click="listContract(1)" class="apax-btn full detail"><i class="fa fa-slack"></i> {{html.dom.filter.buttons.waitcharged}}</button>
            <button @click="listContract(2)" class="apax-btn full print"><i class="fa fa-angellist"></i> {{html.dom.filter.buttons.charged}}</button>
            <button @click="listContract(3)" class="apax-btn full remove"><i class="fa fa-send-o"></i> {{html.dom.filter.buttons.waiting}}</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div :class="html.loading.class ? 'loading' : 'standby'" class="ajax-loader">
          <img src="/static/img/images/loading/mnl.gif">
        </div>
        <b-card header>
          <div slot="header">
            <i class="fa fa-list"></i> <b class="uppercase">Danh sách đăng ký tái phí</b>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
            </div>
          </div>
          <div id="list_content" class="panel-heading">
            <div class="panel-body">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                  <tr class="text-sm">
                    <th >STT</th>
                    <th>Mã CMS</th>
                    <th>Tên học sinh</th>                    
                    <th>Mã HĐ</th>                    
                    <th>Loại HĐ</th>
                    <th>Trung tâm</th>
                    <th>EC</th>
                    <th>CS</th>
                    <th>Chương trình</th>
                    <th>Gói phí</th>
                    <th>Tổng số buổi</th>
                    <th>Học bổng</th>
                    <th>Giá gốc</th>
                    <th>Công nợ</th>
                    <th>Phải đóng</th>
                    <th>Ngày học</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in list" :key="index">
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{index+1 + (html.pagination.cpage-1)*html.pagination.limit}}</router-link></td>                      
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.crm_id}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.student_name}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.accounting_id}}</router-link></td>                      
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.contract_type | contractType}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.branch_name}} - {{item.region_name}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.contract_ec_name}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.contract_cm_name}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.program_name}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.tuition_fee_name}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.summary_sessions}} </router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.debt_amount ==0 ? item.bonus_sessions : ''}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.tuition_fee_price | formatMoney}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.debt_amount | formatMoney}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.contract_must_charge | formatMoney}}</router-link></td>
                      <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">{{item.start_date | formatDate}}</router-link></td>
                      <td>
                        <!-- <span class="apax-btn edit" v-if="item.contract_status == 1">
                          <router-link v-b-tooltip.hover class="link-me" title="Nhấp vào để sửa thông tin" :to="`${html.page.url.link}${item.id}/edit`"><i class="fa fa-pencil"></i></router-link>
                        </span>
                        <span class="apax-btn detail" v-else>
                          <router-link v-b-tooltip.hover class="link-me" title="Nhấp vào để xem chi tiết" :to="`${html.page.url.link}${item.id}`"><i class="fa fa-eye"></i></router-link>
                        </span> -->
                        <span class="apax-btn remove" @click="remove(item)" v-if="(!item.total_charged || session.user.role_id == '999999999') && session.user.role_id!=1200">
                          <i v-b-tooltip.hover title="Nhấp vào để xóa bỏ" class="fa fa-remove"></i>
                        </span>
                        <span class="apax-btn print" v-else-if="item.contract_type != 3">
                          <i v-b-tooltip.hover title="Nhấp vào để in bản ghi" @click="print(item)" class="fa fa-print"></i>
                        </span>
                      </td>
                  </tr>
                  </tbody>
                </table>
                <div class="text-center">
                  <nav aria-label="Page navigation">
                    <paging
                      :rootLink="html.pagination.url"
                      :id="html.pagination.id"
                      :listStyle="html.pagination.style"
                      :customClass="html.pagination.class"
                      :firstPage="html.pagination.spage"
                      :previousPage="html.pagination.ppage"
                      :nextPage="html.pagination.npage"
                      :lastPage="html.pagination.lpage"
                      :currentPage="html.pagination.cpage"
                      :pagesItems="html.pagination.total"
                      :pagesLimit="html.pagination.limit"
                      :pageList="html.pagination.pages"
                      :routing="redirect">
                    </paging>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
      <div class="printing-area hidden">
        <recyclePrint :infor="html.data.print.contract"></recyclePrint>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss"></style>

<script>
import u from '../../../utilities/utility'
import searchBranch from '../../../components/Selection'
import paging from '../../../components/Pagination'
import search from '../../../components/Search'
import recyclePrint from '../../base/prints/recycle'

export default {
  name: 'Recharges',
  components: {
    searchBranch,
    paging,
    search,
    recyclePrint
  },
  data() {
    const model = u.m('recharges').list
    model.html.dom = {
      filter: {
        buttons: {
          default: 'Đã thực hiện tái phí',
          waitcharged: 'Danh sách Booking',
          charged: 'Đã đóng phí',
          waiting: 'Chờ xếp lớp'
        },
        branch: {
          options: [],
          display: 'hidden',
          disabled: true,
          placeholder: 'Vui lòng chọn 1 trung tâm để giới hạn phạm vi tìm kiếm'
        },
        search: {
          label: 'Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh',
          disabled: true,
          placeholder: 'Từ khóa tìm kiếm'
        },
        customer_type: {
          data: [{ id: '', name: 'Lọc theo loại khách hàng' },
          { id: '1', name: 'Chính thức' },
          { id: '4', name: 'Chỉ nhận chuyển phí' },
          { id: '5', name: 'Chuyển trung tâm' },
          { id: '6', name: 'Chuyển lớp' }],
          disabled: true,
          action: () => this.selectCustomerType(),
          placeholder: 'Chọn loại khách hàng'
        },
        program: {
          data: [],
          disabled: true,
          action: () => this.selectProgram(),
          placeholder: 'Chọn chương trình học'
        },
        tuition_fee: {
          data: [],
          disabled: true,
          action: () => this.selectTuitionFee(),
          placeholder: 'Chọn gói học phí'
        }
      }
    }
    model.html.data = {
      filter: {
        branch: '',
        keyword: '',
        customer_type: '',
        program: '',
        tuition_fee: ''
      },
      print: {
        contract: {},
      }
    }
    model.html.data.url=''
    model.html.order.by = 'c.id'
    model.cache.filter = model.html.data.filter
    return model
  },
  created() {
    this.html.data.url = this.html.page.url.list
    this.start()
  },
  methods: {
    onlyView(){
      return u.onlyView(u.session().user.role_id)
    },
    selectCustomerType() {
      this.cache.filter.customer_type = this.html.data.filter.customer_type
      this.get(this.link(), this.load)
    },
    selectProgram(){
      this.cache.filter.program = this.html.data.filter.program
      this.get(this.link(), this.load)
    },
    selectTuitionFee(){
      this.cache.filter.tuition_fee = this.html.data.filter.tuition_fee
      this.get(this.link(), this.load)
    },
    link(link = '') {
      const url = link || this.html.data.url
      this.html.data.filter.branch = this.cache.filter.branch
      const sort = u.jss(this.html.order)
      this.html.data.filter.keyword =this.html.data.filter.keyword.trim()
      const search = u.jss(this.html.data.filter)
      const pagination = u.jss({
        spage: this.html.pagination.spage,
        ppage: this.html.pagination.ppage,
        npage: this.html.pagination.npage,
        lpage: this.html.pagination.lpage,
        cpage: this.html.pagination.cpage,
        total: this.html.pagination.total,
        limit: this.html.pagination.limit
      })
      return `${url}${pagination}/${search}/${sort}`
    },
    checkRemove(item) {
      return item.delete_able
    },
    get(link, callback) {
      this.html.loading.action = true
      u.g(link)
      .then(response => {
        const data = response
        callback(data)
        setTimeout(() => {
          this.html.loading.action = false
        }, data.duration)
      }).catch(e => u.log('Exeption', e))
    },
    start() {
      if (u.authorized() || u.session().user.role_id === 84) {
        this.html.dom.filter.branch.display = 'display'
        this.html.dom.filter.branch.disabled = false
      } else {
        this.html.data.filter.branch = this.session.user.branch_id
        this.cache.filter.branch = this.session.user.branch_id
        u.g(`${this.html.page.url.load}${this.session.user.branch_id}`)
        .then(response => {
          this.html.dom.filter.program.data = response.programs
          this.html.dom.filter.tuition_fee.data = response.tuition_fees
          this.html.data.filter.program = ''
          this.html.data.filter.tuition_fee = ''
          this.html.dom.filter.search.disabled = false
          this.html.dom.filter.program.disabled = false
          this.html.dom.filter.tuition_fee.disabled = false
          this.html.dom.filter.customer_type.disabled = false
        }).catch(e => console.log(e))
      }
      if(u.session().user.role_id == '999999999'){
        this.html.dom.filter.search.disabled = false
      }
      this.searching()
    },
    load(data) {
      this.cache.data = data
      this.list = data.list
      this.html.pagination = data.pagination
    },
    searching(word) {
      const key = u.live(word) ? word : this.html.data.filter.keyword
      this.cache.filter.keyword = key ? key : ''
      this.get(this.link(), this.load)
    },
    binding(e) {
      if (e.key == "Enter") {
        this.searching()
      }
    },
    redirect(link) {
      const info = link.toString().split('/')
      const page = info.length > 1 ? info[1] : 1
      this.html.pagination.cpage = parseInt(page)
      this.get(this.link(), this.load)
    },
    loading() {
    },
    listContract(type) {
      let url = this.html.page.url.list
      switch(type) {
        case 1: {
          url += 'list_waitcharged/'
        } break;
        case 2: {
          url += 'list_charged/'
        } break;
        case 3: {
          url += 'list_waiting/'
        } break;
        default: {
          url += ''
        } break;
      }
      this.html.data.url = url
      u.g(this.link(url)).then(response => {
        this.load(response)
      }).catch(e => console.log(e))
    },
    selectBranch(data) {
      if (data) {
        this.cache.filter.branch = data.id
        this.html.data.filter.branch = data.id
        this.get(this.link(), this.load)
        u.g(`${this.html.page.url.load}${data.id}`)
        .then(response => {
          this.html.dom.filter.program.data = response.programs
          this.html.dom.filter.tuition_fee.data = response.tuition_fees
          this.html.data.filter.program = ''
          this.html.data.filter.tuition_fee = ''
          this.html.dom.filter.search.disabled = false
          this.html.dom.filter.program.disabled = false
          this.html.dom.filter.tuition_fee.disabled = false
          this.html.dom.filter.customer_type.disabled = false
        }).catch(e => console.log(e))
      }
    },
    print(contract) {
      localStorage.setItem(`contract_${contract.id}`, JSON.stringify(contract))
      window.open(`/print/recycle/${contract.id}`,'_blank')
    },
    removed(data) {
      this.$notify({
          group: 'apax-atc',
          title: 'Thông Báo!',
          type: 'success',
          duration: 3000,
          text: 'Bản ghi nhập học đã được xóa thành công!'
      })
      this.load(data)
    },
    remove(contract) {
      if(contract.waiting_status !=0){
        var message="";
            switch(contract.waiting_status) {
              case 1:
                message="Học sinh đang chờ duyệt chuyển phí";
                break;
              case 2:
                message="Học sinh đang chờ duyệt nhận phí";
                break;
              case 3:
                message="Học sinh đang chờ duyệt chuyển trung tâm";
                break;
              case 4:
                message="Học sinh đang chờ duyệt bảo lưu";
                break;
              case 5:
                message="Học sinh đang chờ duyệt chuyển lớp";
                break;
              default:
                // code block
            }
            alert(message);
            return false;
      }else{
        const delStdConf = confirm("Bạn có chắc rằng muốn xóa bản ghi nhập học này không?");
        if (delStdConf === true) {
          u.g(`/api/remove-contract/${contract.id}`)
          .then(response => {
            alert('Xóa bản ghi nhập học thành công')
            this.searching()
          }).catch(e => console.log(e))
        }
      }
    }
  }
}
</script>
