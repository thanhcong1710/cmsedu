<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thêm Ký Hiệu Thu Phí Mới</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Ký hiệu</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Tên ký hiệu</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Ký hiệu EFFECT</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select v-model="status" class="form-control" id="">
                        <option selected :value="0">Hoạt động</option>
                        <option :value="1">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="btn btn-success" type="submit" @click="updateTypeCharge">Lưu</button>
                    <button class="btn btn-default" type="reset" @click="resetAll">Hủy</button>
                    <router-link class="btn btn-sm btn-danger" :to="{name: 'Danh Sách Các Gói Phí'}">
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
    name: 'Add-Type-Charge',
    data() {
      return {
        //Set default checked
        product: '',
        products: [],
        status: '0',
        area: '0',
        regions: [],
        region: '',
        typecharges: [],
        typecharge: {
          lms_proc_id: '',
          name: '',
          status: '',
          price: '',
          receivable: ''
        }
      }
    },
    created() {
      let uri = '/api/tuitions/' + this.$route.params.id + '/edit';
      axios.get(uri).then((response) => {
        this.tuition = response.data;
      })
    },
    methods: {
      updateTypeCharge() {
        let uri = `/api/tuitions/` + this.$route.params.id;
        axios.put(uri, this.tuition).then((response) => {
          this.$router.push('/tuitions')
        })
      },
    }
  }
</script>


