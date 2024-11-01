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
                        <i class="fa fa-address-book" /> <strong>Danh sách cộng tác viên</strong>
                    </div>
                    <div class="controller-bar table-header">
                        <router-link to="/collaborator/add">
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
                                <th>Mã CTV</th>
                                <th>Tên cộng tác viên</th>
                                <th>Tên người đại diện</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Email</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
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
                                        v-for="(field, column) in ['stt', 'code', 'school_name','personal_name', 'address',
                                               'phone_number','email','status','created_at']"
                                        :key="column"
                                >
                                    {{ renderCell(item, field, index) }}
                                </td>
                                <td class="text-center">
                                    <router-link
                                            v-b-tooltip.hover
                                            class="link-me"
                                            title="Cập nhật CTV"
                                            :to="`/collaborator/${item.id}/edit`"
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
            keyword   : '',  status    : '',
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
          return item[field] === '1' ? 'Hoạt động' : 'Không hoạt động'

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
        return `api/collaborator-list/${search}/${paging}`
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
