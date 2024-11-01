<template>
  <div
    class="animated fadeIn apax-form"
    @keyup="binding"
    id="students-management"
  >
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter" /> <b class="uppercase">Bộ lọc</b>
          </div>
          <div id="students-list">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-sm-3">
                        <label class="filter-label control-label">Tìm Kiếm</label><br>
                        <search
                          :on-search="keywordChange"
                          placeholder="'Từ khóa tìm kiếm"
                        />
                      </div>
                      <div class="col-sm-3">
                        <date-component
                          label="Ngày bắt đầu"
                          v-model="data.filter.start_date"
                          :on-change="searching"
                        />
                      </div>
                      <div class="col-sm-3">
                        <date-component
                          label="Ngày kết thúc"
                          v-model="data.filter.end_date"
                          :on-change="searching"
                        />
                      </div>
                      <div class="col-sm-3">
                        <label class="filter-label control-label">Trạng thái</label><br>
                        <p class="input-group-addon filter-lbl">
                          <i
                            v-b-tooltip.hover
                            title="Lọc theo nhân viên EC"
                            class="fa fa-optin-monster"
                          />
                        </p>
                        <select
                          id="select_status"
                          name="status"
                          class="filter-selection filter-ec form-control"
                          v-model="data.filter.status"
                          @change="searching"
                        >
                          <option value="">
                            Tất cả
                          </option>
                          <option value="1">
                            Hoạt động
                          </option>
                          <option value="0">
                            Không hoạt động
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book" /> <strong>Danh sách mã chiết khấu</strong>
          </div>
          <div class="controller-bar table-header">
            <router-link to="/discount-code/add">
              <button
                type="button"
                class="apax-btn full reset"
              >
                <i class="fa fa-plus" /> Thêm mới
              </button>
            </router-link>
          </div>
          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Mã chiết khấu</th>
                  <th>Tên chiết khấu</th>
                  <th>Tỷ lệ chiết khấu</th>
                  <th>Giá gốc gói phí</th>
                  <th>Tiền chiết khấu</th>
                  <th>Số buổi học bổng</th>
                  <th>Ngày bắt đầu</th>
                  <th>Ngày kết thúc</th>
                  <th>Trạng Thái</th>
                  <th width="50">
                    Hành động
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in data.items"
                  :key="index"
                >
                  <td
                    v-for="(field, column) in ['stt', 'code', 'name', 'percent', 'price',
                                               'discount','bonus_sessions','start_date', 'end_date', 'status']"
                    :key="column"
                  >
                    <!--                    <router-link-->
                    <!--                      v-b-tooltip.hover-->
                    <!--                      class="link-me"-->
                    <!--                      :title="`${html.page.title}`"-->
                    <!--                      :to="`${html.page.url.link}${item.id}`"-->
                    <!--                    >-->
                    {{ renderCell(item, field, index) }}
                    <!--                    </router-link>-->
                  </td>
                  <td class="text-center">
                    <router-link
                      v-b-tooltip.hover
                      class="link-me"
                      title="Sửa mã chiết khấu"
                      :to="`/discount-code/${item.id}/edit`"
                    >
                      <span class="apax-btn edit">
                        <i class="fa fa-pencil" />
                      </span>
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <Paging
            :on-change="searching"
            v-model="paging"
            :total="paging.total"
          />
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import search from '../../components/Search'
import u from '../../../utilities/utility'
import Paging from '../../reports/common/PagingReport'
import DateComponent from '../../../components/form/elements/Date'

export default {

  components: {
    search,
    Paging,
    DateComponent,
  },

  data () {
    return {
      data: {
        filter: {
          keyword   : '', start_date: '', end_date  : '', status    : '',
        },
        items: [],
      },
      paging: {},
    }
  },

  mounted () {
    this.start()
  },
  methods: {
    renderCell (item, field, index) {
      if (field === 'stt')
        return index + 1

      if (field === 'status')
        return item[field] === 1 ? 'Hoạt động' : 'Không hoạt động'

      return item[field]
    },
    start () {
      this.searching()
    },
    link () {
      const search = u.jss(this.data.filter)
      const paging = u.jss({
        limit: this.paging.limit,
        cpage: this.paging.cpage,
      })
      return `api/discount-codes/list/${search}/${paging}`
    },
    get (link, callback) {
      u.apax.$emit('apaxLoading', true)
      u.g(link)
        .then((response) => {
          const data = response
          callback(data)
          setTimeout(() => {
            u.apax.$emit('apaxLoading', false)
          }, data.duration)
        }).catch((e) => {
          u.apax.$emit('apaxLoading', false)
          u.log('Exeption', e)
        })
    },
    load (data) {
      this.data.items   = data.data
      this.paging.total = data.total
    },
    keywordChange (word = '') {
      this.data.filter.keyword = word
      this.searching()
    },
    searching () {
      this.get(this.link(), this.load )
    },
    binding (e) {
      if (e.key === 'Enter')
        this.searching()
    },
    redirect (link) {
      // const info                 = link.toString().split('/')
      // const page                 = info.length > 1 ? info[1] : 1
      // this.html.pagination.cpage = parseInt(page)
      // this.get(this.link(), this.load)
    },
  },
}

</script>

<style lang="scss">
  .content-loading{
    position: fixed;
  }
</style>
