<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thêm mới loại thu phí</strong>
              <div class="card-actions">
                <a href="skype:live:c7a5d68adf8682ff?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Tên</label>
                      <input class="form-control" type="text" v-model="feetype.name">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Sản phẩm</label>
                      <select name="" id="" class="form-control" v-model="product">
                        <option :value="0">April</option>
                        <option :value="1">Igarten</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Gói phí áp dụng</label>
                      <select name="" id="" class="form-control" v-model="packageFee">
                        <option :value="0">9 tháng</option>
                        <option :value="1">4.5 tháng</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Loại cha</label>
                      <select name="" id="" class="form-control" v-model="typeParent">
                        <option :value="0">9 tháng</option>
                        <option :value="1">4.5 tháng</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="status">
                        <option :value="0">Hoạt động</option>
                        <option :value="1">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="btn btn-success" type="submit" @click.prevent="updateFeeTypes">Lưu</button>
                    <button class="btn btn-default" type="reset">Hủy</button>
                    <router-link class="btn btn-sm btn-danger" :to="'/fee-types'">
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
    name: 'Edit-Feetype',
    data() {
      return {
        //set default checked
        product: '0',
        typeParent: '0',
        status: '0',
        packageFee: '0',
        //

        feetype: {
          lms_proc_id: '',
          name: '',
          status: '',
        }
      }
    },
    created() {

      let uri = '/api/feetypes/' + this.$route.params.id + '/edit';
      axios.get(uri).then((response) => {
        this.product = response.data;
      })
      // let uri = '/api/products/'+this.$route.params.id;
      // axios.get(uri).then((response) => {
      // 	this.product = response.data;
      // 	console.log(`this.product ${JSON.stringify(this.product)}`)
      // });
    },
    methods: {
      updateFeeTypes() {
        let uri = `/api/feetypes/` + this.$route.params.id;
        axios.put(uri, this.product).then((response) => {
          this.$router.push('/feetypes')
        })
      },
    }
  }
</script>


