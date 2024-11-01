<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card
            header-tag="header"
            footer-tag="footer"
          >
            <div slot="header">
              <i class="fa fa-clipboard" /> <strong>Chi Tiết Thông Tin Sản Phẩm</strong>
              <div class="card-actions">
                <a
                  href="skype:thanhcong1710?chat"
                  target="_blank"
                >
                  <small className="text-muted"><i class="fa fa-skype" /></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Mã Cyber<span class="text-danger"> (*)</span></label>
                      <input
                        class="form-control"
                        type="text"
                        readonly
                        v-model="product.accounting_id"
                      >
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Tên loại sản phẩm<span class="text-danger"> (*)</span></label>
                      <input
                        class="form-control"
                        type="text"
                        readonly
                        v-model="product.name"
                      >
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label
                        class="control-label"
                        title="Số lượng học sinh"
                      >Số ngày tối thiểu học trong một tuần</label>
                      <input
                        type="number"
                        max="7"
                        min="1"
                        readonly
                        class="form-control"
                        v-model="product.min_number_of_days_in_week"
                      >
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label
                        class="control-label"
                        title="Số lượng học sinh"
                      >Số ngày tối đa học trong một tuần</label>
                      <input
                        type="number"
                        max="7"
                        min="1"
                        readonly
                        class="form-control"
                        v-model="product.max_number_of_days_in_week"
                      >
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái<span class="text-danger"> (*)</span></label>
                      <select
                        name=""
                        id=""
                        readonly
                        class="form-control"
                        v-model="product.status"
                      >
                        <option
                          value=""
                          disabled
                        >
                          Chọn trạng thái
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
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Mô tả</label>
                      <textarea
                        class="description form-control"
                        rows="5"
                        readonly
                        placeholder="Nhập mô tả"
                        v-model="product.description"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <router-link
                      class="apax-btn full warning"
                      :to="'/products'"
                    >
                      <i class="fa fa-sign-out" /> Quay lại
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
  name: 'ProductDetail',
  data () {
    return {
      product: {
        name  : '',
        status: '',
      },
    }
  },
  created () {
    const uri = `/api/products/${this.$route.params.id}`
    axios.get(uri).then((response) => {
      this.product = response.data
      console.log(`this.product ${JSON.stringify(this.product)}`)
    })
  },
  methods: {
    filterStatus (value) {
      return value == 1 ? 'Hoạt động' : 'Không hoạt động'
    },
  },
}
</script>

<style scoped>
.apax-form .card-body{
  padding: 15px;
}
textarea.description {
  height: 120px;
}
</style>
