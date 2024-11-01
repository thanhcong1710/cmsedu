<template>
    <div class="wrapper">
        <div class="animated fadeIn ada-form">
            <b-card header-tag="header">
                <div slot="header">
                    <i class="fa fa-gears"></i> <b class="uppercase">CÔNG CỤ HỖ TRỢ</b>
                </div>
                <div class="panel nav-support">
                    <div class="col-md-8 ada-form-block">
                        <ul class="nav-dropdown-items" style="list-style: none;">
                            <li class="nav-item">
                                <a v-bind:href="'/supports/branch/change_enrolment_last_date'">+ <i class="nav-icon fa fa-briefcase"></i> Cập nhật nhanh ngày học cuối</a>
                            </li>
                            <li class="nav-item">
                                <a v-bind:href="'/supports/convert-tuition-fee'">+ <i class="nav-icon fa fa-briefcase"></i> Qui đổi gói phí</a>
                            </li>
                            <li class="nav-item">
                                <a v-bind:href="'/supports/transfer-all-class'">+ <i class="nav-icon fa fa-briefcase"></i> Chuyển All học sinh theo lớp</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </b-card>
        </div>
    </div>
</template>
<script>
  import u from '../../utilities/utility'
  import abt from '../../components/Button'
  import branch from '../../components/Branches'

  export default {
    name: 'Settings',
    components: {
      abt,
      branch
    },
    data () {
      const model = u.m('settings').page
      model.obj = {
        title: '',
        configs: [{
          id: '',
          name: '',
          key: '',
          value: '',
          description: '',
        }],
        settings: [],
        branches: [],
        branches_ids: '',
        description: ''
      }
      model.html = {
        modal: {
          notification: {
            title: 'Thông báo',
            class: 'modal-success',
            variant: 'success',
            display: false,
            action: {
              ok: () => this.notificationComplete(),
              close: () => this.notificationClose()
            }
          }
        },
        button: {
          save: {
            disabled: true,
            action: () => this.save()
          }
        }
      }
      model.filter = {
        id: 0,
        lms: 0,
        name: '',
        branch: 0,
        area_id: 0,
        zone_id: 0,
        region_id: 0
      }
      model.branch = {
        id: 0,
        name: '',
        code: '',
        status: 0,
        brch_id: 0,
        area_id: 0,
        zone_id: 0,
        region_id: 0,
        zone_name: '',
        region_name: ''
      }
      model.config = {

      }
      return model
    },
    created () {
      this.start ()
    },
    methods: {
      start () {
      },
      add () {
        const n = `${this.moment().unix()}${Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)}`
        this.obj.configs.push({
          id: n,
          name: '',
          key: '',
          value: '',
          description: '',
        })
      },
      remove () {

      },
      test (list) {
        u.log('CHECKED LIST', list)
      },
      save () {

      },
      notificationComplete () {

      },
      notificationClose () {

      }
    }
  }
</script>

<style scoped>
    li.nav-item a{text-decoration: none;color: #151b1e}
</style>

