<template>
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-12 user-search">
        <div :class="ajax_loading ? 'loading' : 'standby'" class="ajax-loader">
          <img src="/static/img/images/loading/mnl.gif">
        </div>
        <b-card header>
          <div slot="header">
              <i class="fa fa-address-book"></i> <strong>Danh sách nhân viên</strong>
          </div>

          <div id="list_content" class="panel-heading">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-6">
                  <search
                    :id="search_id"
                    :name="search_name"
                    :label="search_label"
                    :onSearch="searchUser"
                    :placeholder="search_placeholder"
                  >
                  </search>
                </div>

                <!--<div class="col-md-6">-->
                    <!--<div class="row">-->
                        <!--<div class="col-md-12">-->
                            <!--<div class="row">-->
                              <!--<div class="col-md-5">-->
                                <!--<div class="form-group">-->
                                  <!--<input type="file" class="input-file form-control" @change="fileChanged" data-multiple-caption="{count} files selected" multiple/>-->
                                  <!--<label class="control-label" for="file"><i class="fa fa-upload"></i> <span id="atch-file">File đính kèm</span></label>-->
                                <!--</div>-->
                              <!--</div>-->
                              <!--<div class="col-md-3">-->
                                <!--<div class="form-group">-->
                                    <!--<button @click="uploadFile" class="btn btn-success">Upload File</button>-->
                                <!--</div>-->
                              <!--</div>-->
                            <!--</div>-->
                        <!--</div>-->
                    <!--</div>-->
                <!--</div>-->
              </div>
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="text-sm">
                        <th>Mã HRM</th>
                        <th >Ảnh</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Tài Khoản</th>
                        <th>Chức Danh</th>
                        <th>Thủ Trưởng</th>
                        <th>Trung Tâm</th>
                        <th>Khu Vực</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(user, index) in users" :key="index">
                        <td><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{user.mixing_id}}</router-link></td>
                        <td><img class="user-avatar" :src="user.avatar" />
                        <td><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{user.full_name}}</router-link></td>
                        <td><a v-b-tooltip.hover :title="'Nhấp vào để gửi email cho '+user.full_name" class="link-me" :href="'mailto:'+user.email">{{user.email}}</a></td>
                        <td><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{user.username}}</router-link></td>
                        <td><router-link v-b-tooltip.hover :title="user.title_description" class="link-me" :to="`/users/${user.user_id}`">{{user.title}}</router-link></td>
                        <td><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{user.boss}}</router-link></td>
                        <td><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{user.branch}}</router-link></td>
                        <td><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{user.region}}</router-link></td>
                        <td class="text-center"><router-link v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" :to="`/users/${user.user_id}`">{{prepareText(user.status)}}</router-link></td>
                        <td><div class="btn btn-success" @click="updateProfile(user.user_id, `${user.full_name} - ${user.username}`, user.email)">Đổi mật khẩu</div></td>
                    </tr>
                  </tbody>
                </table>
              </div>
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
                    :routing="goTo"
                  >
                  </paging>
                </nav>
              </div>
            </div>
            <b-modal size="lg"
              ok-variant="primary"
              title="Cập Nhật Thông Tin Tài Khoản"
              class="modal-primary change-password"
              v-model="modal"
              hide-footer
              @ok="modal = false"
              @close="modal = false">
              <profile :user="`${user_id}`" :email="user_email" :profile="profile_name"></profile>
            </b-modal>
          </div>
        </b-card>
      </div>
    </div><!--/.col-->
  </div><!--/.row-->
</template>

<script>
import u from '../../utilities/utility'
import paging from '../../components/Pagination'
import search from '../../components/Search'
import profile from '../../components/Profile'

export default {
  name: 'users',
  data (){
    return {
      modal: false,
      user_id: 0,
      user_email: '',
      profile_name: '',
      ajax_loading: true,
      search_id: 'user_search',
      search_name: 'search-user',
      search_label: 'Tìm kiếm theo mã HRM, Họ tên, Tài khoản hoặc Email',
      search_placeholder: 'Từ khóa tìm kiếm',
      router_url: '/users/list',
      pagination_id: 'user_paging',
      pagination_class: 'users paging list',
      list_style: 'line',
      pagination: {
        limit: 5,
        spage: 0,
        ppage: 0,
        npage: 0,
        lpage: 0,
        cpage: 0,
        total: 0,
        pages: []
      },
      prepareText: (stt) => u.userstatus(stt),
      format: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ',
      users: [],
      user: {},
      keyword: '_',
      files: {
        attached_file: ''
      }
    }
  },
  components: {
    paging,
    search,
    profile
  },
  watch: {

  },
  created() {
    this.getUsers()
  },
  methods: {
    load(data){
      this.users = data.users
      this.information = data.information
      this.pagination = data.pagination
      if (this.first_page < this.last_page) {
        for (let i = this.first_page; i < this.last_page + 1; i+=1) {
          this.pagination.pages.push(i)
        }
        if (this.last_page > 9) {
          this.list_style = 'list'
        }
      }
    },
    get(link) {
      this.ajax_loading = true
      u.a().get(link)
        .then(response => {
          this.load(response.data)
          this.ajax_loading = false
        }).catch(e => console.log(e))
    },
    getUsers(page_url){
      const key = this.keyword ? this.keyword : '_'
      page_url = page_url ? '/api'+page_url : '/api/users/list/1'
      page_url+= '/' + key
      this.get(page_url)
    },
    goTo(link) {
      this.getUsers(link)
    },
    searchUser(word) {
      const key = word != '' ? word : '_'
      this.keyword = key
      this.get( '/api/users/list/1/' + key)
    },
    fileChanged(e) {
      const fileReader = new FileReader();
      const fileName = e.target.value.split( '\\' ).pop();
      $('#atch-file').html(fileName)
      fileReader.readAsDataURL(e.target.files[0])
      fileReader.onload = (e) => {
        this.files.attached_file = e.target.result
      }
    },
    updateProfile(id, name, email) {
      this.user_id = id
      this.profile_name = name
      this.user_email = email
      this.modal = true
    },
    uploadFile() {
      let Url = `/api/user/uploadExcel`
      u.a().post(Url, this.files).then(response => {
          this.getUsers()
          $('#atch-file').html('File đính kèm')
          alert('Upload file thành công')
        }).catch(e => console.log(e))
    }
  },
  events: {
  }
}
</script>

<style scoped>
  img.user-avatar {
    border-radius:50%;
    width:29px;
    height:29px;
  }
  .change-password b {
    color:#333!important;
  }
</style>
