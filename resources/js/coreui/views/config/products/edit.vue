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
              <i class="fa fa-clipboard" /> <strong>Cập Nhật Thông Tin Sản Phẩm</strong>
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
                    <button
                      class="apax-btn full edit"
                      type="submit"
                      @click="updateProduct"
                    >
                      <i class="fa fa-save" /> Lưu
                    </button>
                    <button
                      class="apax-btn full default"
                      type="reset"
                      @click="reset"
                    >
                      <i class="fa fa-ban" /> Hủy
                    </button>
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

      <b-modal
        :title="html.modal.title"
        :class="html.modal.class"
        size="sm"
        v-model="html.modal.display"
        @ok="action.modal"
        ok-variant="primary"
      >
        <div v-html="html.modal.message" />
      </b-modal>
    </div>
  </div>
</template>

<script>
import u from '../../../utilities/utility'

export default {
  name: 'EditProduct',
  data () {
    return {
      action: { modal: () => this.exitModal() },
      html  : {
        modal: {
          title  : 'Thông báo',
          class  : 'modal-success',
          display: false,
          message: '',
        },
      },
      product: {
        name  : '',
        status: '',
      },
      products: [],
    }
  },
  created () {
    const uri = `/api/products/${this.$route.params.id}/edit`
    u.a().get(uri).then((response) => {
      this.product = response.data
      console.log('test', this.product)
    })
  },
  methods: {
    validate () {
      if (!this.product.accounting_id)
        return 'Mã Cyber không được để trống'

      if (!this.product.name)
        return 'Tên Sản phẩm không được để trống'

      if (!this.product.status)
        return 'Trạng thái không được để trống'

      if (parseInt(this.product.min_number_of_days_in_week) > parseInt(this.product.max_number_of_days_in_week))
        return 'Số ngày học tối thiểu của một tuần phải nhỏ hơn hoặc bằng số ngày học tối đa của một tuần'

      return null
    },
    updateProduct () {
      const val = this.validate()
      if (val) {
        alert(val)
        return false
      }
      const uri     = `/api/products/${this.$route.params.id}`
      u.a().put(uri, this.product).then((response) => {
        const rs = response.data
        if (rs) {
          this.html.modal.message = 'Sửa thành công sản phẩm !'
          this.html.modal.display = true
        }
      })
    },
    exitModal () {
      this.$router.push('/products')
    },
    reset () {
      this.product.name      = ''
      this.product.prod_code = ''
      this.product.status    = ''
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
