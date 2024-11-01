<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thêm Thiết Lập Cấu Hình Mới</strong>
              <div class="card-actions">
                <a href="skype:live:c7a5d68adf8682ff?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Tên cấu hình</label>
                      <input class="form-control" type="text" v-model="config.name">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Khóa</label>
                      <input class="form-control" type="text" v-model="config.key">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Giá trị khóa</label>
                      <input class="form-control" type="text" v-model="config.value">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Mô tả</label>
                      <input class="form-control" type="text" v-model="config.description">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select class="form-control" v-model="config.status">
                        <option value="0">Không hoạt dộng</option>
                        <option value="1">Hoạt dộng</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Nhóm</label>
                      <input class="form-control" v-model="config.group" type="number">
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="btn btn-success" type="submit" @click.prevent="addSetting">Lưu</button>
                    <button class="btn btn-default" type="reset">Hủy</button>
                    <router-link class="btn btn-sm btn-danger" :to="'/settings'">
                      Quay lại
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </b-card>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Add-Setting',
    data() {
      return {
        config: {
          name: '',
          key: '',
          value: '',
          description: '',
          group: '',
          status: ''
        }
      }
    },
    methods: {
      addSetting() {
        u.a().post(`/api/configs`, this.config).then(response =>{
          this.$router.push('/settings')
        })
      },
    }
  }
</script>


