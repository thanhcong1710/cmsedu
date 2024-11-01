<template>
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-12">
        <div :class="ajax_loading ? 'loading' : 'standby'" class="ajax-loader">
          <img src="/static/img/images/loading/mnl.gif">
        </div>
        <b-card header="<i class='fa fa-align-justify'></i> Thông tin nhân viên ">
          <div id="page-content">
            <div class="row">
              <div class="col-md-4 avatar-frame">
                <div class="avatar">
                  <img :src="user.avatar" class="user-avatar" />
                </div>
                <div class="sub-info">
                  <div class="hrm-info">
                    <div class="col-12">
                      <div class="row">
                        <div class="col-md-6 hrm info-label">
                          Mã HRM:
                        </div>
                        <div class="col-md-6 hrm info-content">
                          {{user.hrm_id}}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="effect-info">
                    <div class="col-12">
                      <div class="row">
                        <div class="col-md-6 effect info-label">
                          Mã Effect:
                        </div>
                        <div class="col-md-6 effect info-content">
                          {{user.accounting_id}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-8 info-frame">
                <div class="col-12">
                  <span class="label-info">Họ và tên:</span><span class="content-info">{{user.full_name}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Email:</span><span class="content-info"><a :href="`mailto:${user.email}`">{{user.email}}</a></span>
                </div>
                <div class="col-12">
                  <span class="label-info">Tài khoản:</span><span class="content-info">{{user.username}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Trạng thái:</span><span class="content-info">{{prepareText(user.status)}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Chức danh:</span><span class="content-info">{{user.title}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Thủ trưởng:</span><span class="content-info">{{user.boss}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Trung tâm:</span><span class="content-info">{{user.branch}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Khu vực:</span><span class="content-info">{{user.region}}</span>
                </div>
                <div class="col-12">
                  <span class="label-info">Phân quyền:</span><span class="role-description content-info">{{user.title_description}}</span>
                </div>
                <apax_button
                  @click="goBack"
                >
                  Quay lại
                </apax_button>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import apax_button from '../../components/Button'

export default {
	data(){
		return {
      user: {
        ajax_loading: true,
        hrm_id: '',
        accounting_id: '',
        full_name: '',
        username: '',
        status: '',
        avatar: '',
        email: '',
        boss: '',
        title: '',
        branch: '',
        region: '',
        title_description: ''
      }
		}
  },
  components: {
    apax_button
  },
	created(){
    this.ajax_loading = true
		const link = '/api/user/' + this.$route.params.id;
		u.a().get(link)
			.then(response => {
        this.user = response.data.user
        this.ajax_loading = false
      }).catch(e => console.log(e))
  },
  methods: {
    prepareText: (stt) => u.userstatus(stt),
    goBack(e){
      this.$router.push("/users/list/1")
    }
  }
}

</script>

<style scoped lang="scss">
.div button{
	color: white;
}
.avatar{
  margin-bottom: 10px;
  width: 100%;
  text-align: center;
}
.apax-button {
  margin: 20px 0 10px 20px;
}
.info-frame .label-info {
  text-align: left;
  width:100px;
  font-weight: 700;
  padding: 5px;
  display: inline-block;
}
.info-frame .content-info {
  display: inline-block;
}
.info-label {
  text-align: right;
}
.user-avatar {
  height: 255px;
  -webkit-box-shadow: 0 1px 1px #333;
  box-shadow: 1px 3px 3px #333;
  border-radius: 50%;
}
.role-description {
  padding: 0 0 0 5px;
  font-size: 12px;
  font-weight: 400;
  font-style: italic;
}
</style>
