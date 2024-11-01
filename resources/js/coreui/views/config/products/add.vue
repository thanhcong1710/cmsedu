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
              <i class="fa fa-clipboard" /> <strong>Thêm Sản Phẩm Mới</strong>
              <div class="card-actions">
                <a
                  href="skype:live:c7a5d68adf8682ff?chat"
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
                      @click="addProduct"
                    >
                      <i class="fa fa-save" /> Lưu
                    </button>
                    <button
                      class="apax-btn full default"
                      @click="resetInputValue"
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
        @ok="html.modal.done"
        ok-variant="primary"
      >
        <div v-html="html.modal.message" />
      </b-modal>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AddProduct',
  data () {
    return {
      html: {
        modal: {
          title   : 'Thông báo',
          class   : 'modal-success',
          display : false,
          done    : false,
          message : '',
          disabled: { save: true },
        },
      },
      product: {
        name                      : '',
        status                    : '',
        min_number_of_days_in_week: 1,
        max_number_of_days_in_week: 2,
      },
    }
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
    addProduct () {
      const val = this.validate()
      if (val) {
        alert(val)
        return false
      }
      axios.post(`/api/products`, this.product).then((response) => {
        if (response.data == 1) {
          this.html.modal.message = 'Mã LMS sản phẩm đã tồn tại'
          this.html.modal.display = true
          this.html.modal.done    = true
          return false
        } else {
          this.html.modal.message = 'Thêm mới sản phẩm thành công'
          this.html.modal.display = true
          this.html.modal.done    = true
          this.$router.push('/products')
        }
        this.product.push(response.data.product)
      })
    },
    resetInputValue () {
      this.product.status = ''
      this.product.name   = ''
    },
  },
}
</script>

<style scoped lang="scss">
.apax-form .card-body{
  padding: 15px;
}
textarea.description {
  height: 120px;
}
</style>
