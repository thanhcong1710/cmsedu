<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div v-show="flags.form_loading" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div
                v-show="flags.form_loading"
                class="loading-text cssload-loader"
              >Đang tải dữ liệu...</div>
            </div>
          </div>
          <div v-show="flags.requesting" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div
                v-show="flags.requesting"
                class="loading-text cssload-loader"
              >Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
            </div>
          </div>
          <div slot="header">
            <i class="fa fa-id-card"></i>
            <b class="uppercase">Quy đổi</b>
          </div>
          <div id="page-content" class="exchange-form">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6 pad-no">
                        <div class="col-12 pad-no" :class="filter.branch.display">
                          <label class="control-label">Thông tin trung tâm</label>
                          <branch
                            :onSelect="filter.branch.action"
                            :options="filter.branch.options"
                            :disabled="filter.branch.disabled"
                            :placeholder="filter.branch.placeholder"
                          ></branch>
                          <br>
                        </div>
                        <div class="col-12 pad-no" :class="filter.search.display">
                          <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc Tên</label>
                          <search
                            :endpoint="filter.search.link"
                            :suggestStudents="filter.search.find"
                            :onSelectStudent="filter.search.action"
                          ></search>
                          <br>
                        </div>

                        <div class="col-12 pad-no">
                          <label class="control-label">Thông tin hồ sơ học sinh</label>
                          <div class="profile-detail">
                            <div class="from-student information item col-md-12">
                              <div class="student info detail row">
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="col-md-4">Mã kế toán:</div>
                                    <div class="col-md-8">{{data.student.accounting_id}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="col-md-4">Mã CMS:</div>
                                    <div class="col-md-8">{{data.student.crm_id}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="col-md-4">Họ Tên:</div>
                                    <div class="col-md-8">{{data.student.name}}</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 pad-no">
                        <div class="col-12 pad-no">
                          <label class="control-label">Sản phẩm quy đổi</label>
                          <select v-model="product_transfer" class="form-control" @change="exchange">
                            <option disabled value>Chọn sản phẩm quy đổi</option>
                            <option 
                              v-for="(product,index) in products"
                              :key="index"
                              :value="product.id" v-if="product.id!=tmp_product_id"
                            >{{product.name}}</option>
                          </select>
                          <br>
                        </div>
                        <div class="col-12 pad-no">
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <label class="control-label">
                                  Ngày bắt đầu quy đổi
                                  <strong class="text-danger h6">*</strong>
                                </label>
                                <date-picker
                                  style="width:100%;"
                                  v-model="start_date"
                                  :editable="false"
                                  :clearable="true"
                                  :not-before="datepickerOptions.minDate"
                                  :shortcuts="datepickerOptions.shortcuts"
                                  :lang="datepickerOptions.lang"
                                  format="YYYY-MM-DD"
                                  placeholder="Chọn thời gian quy đổi"
                                  @change="exchange"
                                ></date-picker>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <br>
                    <div v-for="(contract, idc) in data.contracts" :key="idc" class="row">
                      <div class="col-md-6 pad-no">
                        <div class="exchanging contract item col-12">
                          <div class="contract detail">
                            <div class="info line col-md-12">
                              <h5 class="text-main">Gói phí {{idc+1}}</h5>
                            </div>
                            <div class="info line col-md-12">
                              <div class="row detail-info">
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Trung tâm:</div>
                                    <div class="info line col-md-8">{{contract.branch_name}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Sản phẩm:</div>
                                    <div class="info line col-md-8">{{contract.product_name}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Lớp:</div>
                                    <div class="info line col-md-8">{{contract.class_name}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Tên gói phí:</div>
                                    <div class="info line col-md-8">{{contract.tuition_fee_name}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Số phí đã đóng:</div>
                                    <div
                                      class="info line col-md-8"
                                    >{{contract.total_charged | formatMoney}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Số buổi còn lại:</div>
                                    <div class="info line col-md-8">{{contract.left_sessions}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Số buổi học bổng:</div>
                                    <div class="info line col-md-8">{{contract.bonus_sessions}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Ngày bắt đầu:</div>
                                    <div class="info line col-md-8">{{contract.start_date}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Ngày kết thúc:</div>
                                    <div class="info line col-md-8">{{contract.end_date}}</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-6 pad-no" :class="display_result">
                        <div class="exchanging contract item col-12">
                          <div class="contract detail">
                            <div class="info line col-md-12">
                              <h5 class="text-main">Quy đổi gói phí {{idc+1}}</h5>
                            </div>
                            <div class="info line col-md-12">
                              <div class="row detail-info">
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Gói phí quy đổi:</div>
                                    <div
                                      class="info line col-md-8"
                                    >{{contract.exchange_tuition_fee}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Số buổi còn lại quy đổi:</div>
                                    <div
                                      class="info line col-md-8"
                                    >{{contract.exchange_left_sessions}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Số buổi học bổng quy đổi:</div>
                                    <div class="info line col-md-8">{{contract.bonus_sessions}}</div>
                                  </div>
                                </div>
                                <div class="info line col-md-12">
                                  <div class="row">
                                    <div class="info line col-md-4">Tổng số buổi quy đổi:</div>
                                    <div
                                      class="info line col-md-8"
                                    >{{contract.exchange_left_sessions+contract.bonus_sessions}}</div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <br>
                    <div class="col-md-12 pad-no" :class="display_result">
                      <div class="row">
                        <div class="col-md-12">
                          <h6 class="text-main">Kết quả quy đổi</h6>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label class="control-label">Tổng số buổi còn lại sau quy đổi</label>
                            <input
                              class="form-control"
                              :value="data.total.exchange_left_sessions"
                              type="text"
                              readonly
                            >
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label class="control-label">Tổng số buổi học bổng sau quy đổi</label>
                            <input
                              class="form-control"
                              :value="data.total.exchange_bonus"
                              type="text"
                              readonly
                            >
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label class="control-label">Tổng số buổi sau quy đổi</label>
                            <input
                              class="form-control"
                              :value="data.total.exchange_all"
                              type="text"
                              readonly
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <p style="font-size: 16px;color: red;font-weight: 600;font-style: italic;">*** LƯU Ý: Bạn vui lòng kiểm tra kĩ số buổi được quy đổi trước khi ấn Lưu. Khi học sinh đã quy đổi, CNVH sẽ từ chối hỗ trợ chỉnh sửa dữ liệu nên bạn cần cân nhắc kỹ trước khi thao tác.</p>
                  </div>
                  <div class="panel-footer">
                    <div class="col-sm-12 text-center">
                      <button
                        class="apax-btn full edit"
                        type="button"
                        :class="data.access_exchange"
                        @click="saveExchange"
                      >
                        <i class="fa fa-save"></i> Lưu
                      </button>
                      <button class="apax-btn full default" type="button" @click="reset">
                        <i class="fa fa-ban"></i> Hủy
                      </button>
                      <router-link to="/exchange">
                        <button type="button" class="apax-btn full remove">
                          <i class="fa fa-sign-out"></i> Quay lại
                        </button>
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <b-modal
          :title="modal.title"
          :class="modal.class"
          size="sm"
          v-model="modal.display"
          @ok="exitModal"
          ok-variant="primary"
        >
          <div v-html="modal.message"></div>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import datePicker from "vue2-datepicker";
import u from "../../../utilities/utility";
import branch from "../../../components/Selection";
import search from "../../../components/StudentSearch";
export default {
  name: "Add-Branch-Transfer",
  components: {
    datePicker,
    branch,
    search
  },
  data() {
    return {
      data: {
        student: {
          id: 0,
          accounting_id: "",
          stu_id: "",
          name: "",
          nick: ""
        },
        contracts: [],
        total: {},
        access_exchange: "hidden"
      },
      display_result: "hidden",
      start_date: "",
      tmp_product_id: 0,
      products: [],
      product_transfer: "",
      filter: {
        branch: {
          display: "hidden",
          options: [],
          disabled: false,
          placeholder:
            "Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước",
          action: branch => this.selectBranch(branch)
        },
        search: {
          link: 0,
          display: "hidden",
          find: keyword => this.searchSuggestStudent(keyword),
          action: student => this.selectStudent(student)
        }
      },
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: new Date(),
        lang: {
          days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          months: [
            "Tháng 1",
            "Tháng 2",
            "Tháng 3",
            "Tháng 4",
            "Tháng 5",
            "Tháng 6",
            "Tháng 7",
            "Tháng 8",
            "Tháng 9",
            "Tháng 10",
            "Tháng 11",
            "Tháng 12"
          ]
        }
      },
      flags: {
        success: false,
        form_loading: false,
        requesting: false,
        has_error: false
      },
      calling: false,
      modal: {
        title: "Thông Báo",
        class: "modal-success",
        message: "",
        display: false
      }
    };
  },
  mounted() {},
  created() {
    let role = parseInt(u.session().user.role_id);
    let multi_branch_roles = [
      u.r.region_ceo,
      u.r.founder,
      u.r.admin,
      u.r.super_administrator
    ];
    if (u.session().user.branches.length>1) {
      this.filter.branch.display = "show";
    } else {
      this.filter.branch.display = "hidden";
      this.data.branch_id = u.session().user.branch_id;
      this.filter.search.display = "show";
    }
    u.a().get(`/api/all/products`)
      .then(response => {
        this.products = response.data
        console.log(response.data)
      });
  },
  methods: {
    searchSuggestStudent(keyword) {
      this.product_transfer=""
      this.display_result= "hidden"
      this.data.access_exchange = "hidden";
      if (keyword && keyword.length >= 3 && this.calling === false) {
        keyword = keyword.replace(/[~`!#$%^&*[,\]./<>?;'\\:"|\t]/gi, "");

        this.calling = true;
        return new Promise((resolve, reject) => {
          u.g(`/api/exchange/search/students/${this.data.branch_id}/${keyword}`)
            .then(response => {
              const resp = response.length
                ? response
                : [
                    {
                      label: "Không tìm thấy",
                      branch_name: "Không có kết quả nào phù hợp"
                    }
                  ];
              this.calling = false;
              resolve(resp);
            })
            .catch(e => console.log(e));
        });
      }
    },
    selectBranch(branch) {
      this.data.branch_id = branch.id;
      this.filter.search.display = "show";
    },
    selectStudent(student) {
      this.data.student_id = student.student_id;
      this.tmp_product_id = 0
      u.a()
        .get(`/api/exchange/all-contract/${this.data.student_id}`)
        .then(response => {
          if (!response.data.data.has_error) {
            this.data.student = response.data.data.student_info;
            this.data.contracts = response.data.data.contracts;
            this.tmp_product_id = this.data.contracts[0].product_id
            if (response.data.data.access_exchange) {
              this.data.access_exchange = "display";
            } else {
              this.data.access_exchange = "hidden";
            }
          } else {
            this.$notify({
              group: 'apax-atc',
              title: 'Lưu ý!',
              type: 'warning',
              duration: 3000,
              text: response.data.data.message
            })
          }
        });
    },
    exchange() {
      let data = {
        student_id: this.data.student.id,
        product_transfer: this.product_transfer,
        start_date: this.getDate(this.start_date)
      };
      if (data.student_id == 0) {
        alert("Vui lòng chọn học sinh");
        return false;
      } else if (data.product_transfer == "") {
        alert("Vui lòng chọn sản phẩm");
        return false;
      } else if (data.start_date == "") {
        alert("Vui lòng chọn ngày bắt đầu quy đổi");
        return false;
      }
      this.flags.form_loading = true;
      u.a()
        .post(`/api/exchange/result`, data)
        .then(response => {
          if (response.data.data.message != '') {
            this.data.access_exchange = "hidden";
            alert(response.data.data.message);
          }
          this.data.contracts = response.data.data.contracts;
          this.data.total = response.data.data.total;
          this.display_result = "show";
          this.flags.form_loading = false;
        });
    },
    exitModal() {},
    getDate(date) {
      if (date instanceof Date && !isNaN(date.valueOf())) {
        var year = date.getFullYear(),
          month = (date.getMonth() + 1).toString(),
          day = date.getDate().toString(),
          strMonth = month < 10 ? "0" + month : month,
          strYear = day < 10 ? "0" + day : day;

        return `${year}-${strMonth}-${strYear}`;
      }
      return "";
    },
    reset() {
      location.reload();
    },
    saveExchange() {
      let data = {
        student_id: this.data.student.id,
        product_transfer: this.product_transfer,
        start_date: this.getDate(this.start_date)
      };
      if (data.student_id == 0) {
        alert("Vui lòng chọn học sinh");
        return false;
      } else if (data.product_transfer == "") {
        alert("Vui lòng chọn sản phẩm");
        return false;
      } else if (data.start_date == "") {
        alert("Vui lòng chọn ngày bắt đầu quy đổi");
        return false;
      }
      this.flags.form_loading = true;
      u.a()
        .post(`/api/exchange/save`, data)
        .then(response => {
          alert("Quy đổi gói học phí thành công");
          this.$router.push('/exchange')
          this.flags.form_loading = false;
        });
    }
  }
};
</script>

<style scoped lang="scss">
.exchange-form .profile-detail {
  background: #eeffed;
  border-radius: 1px;
  border: 0.5px solid #3ea53e;
}
.exchange-form .profile-detail .information {
  padding: 10px 23px 12px 10px;
}
.exchange-form .profile-detail .col-md-4 {
  height: 25px;
  color: #073316;
  font-weight: bold;
  text-shadow: 0 1px 1px #bbb;
  text-transform: capitalize;
}
.exchange-form .profile-detail .col-md-8 {
  height: 25px;
  color: #073316;
  border: 0.5px solid #499850;
  margin: 0 0 -1px 0;
  background: #fff;
  -webkit-box-shadow: 0 2px 2px -1px #999;
  box-shadow: 0 2px 2px -1px #999;
  line-height: 150%;
  border-radius: 1px;
  letter-spacing: 0.5px;
  font-weight: 500;
  text-shadow: 0 1px 1px #ccc;
  padding: 3px 9px;
}
.exchanging .contract .detail-info {
  padding: 0 8px 0 0;
}
.exchanging .contract .detail-info .col-md-4 {
  color: #071f33;
  font-weight: bold;
  text-shadow: 0 1px 1px #bbb;
  text-transform: capitalize;
}
.exchanging .contract .detail-info .col-md-8 {
  color: #071f33;
  border: 0.5px solid #496998;
  margin: 0 0 -1px 0;
  background: #fff;
  -webkit-box-shadow: 0 2px 2px -1px #999;
  box-shadow: 0 2px 2px -1px #999;
  line-height: 150%;
  border-radius: 1px;
  letter-spacing: 0.5px;
  font-weight: 500;
  text-shadow: 0 1px 1px #ccc;
  padding: 3px 9px;
}
.exchange-form .contract.detail {
  background: #f0f8ff;
  padding: 10px 0;
  font-weight: 500;
  border: 0.5px solid #c1d2e9;
  text-shadow: 0 1px 0px #fff;
  color: #2d5e84;
}
.exchange-form .contract.detail h5 {
  font-size: 10px;
  text-transform: uppercase;
  margin: -10px -15px 10px;
  padding: 8px 12px;
  background: #246faf;
  font-weight: 500;
  text-shadow: 0 1px 1px #111;
  letter-spacing: 0.5px;
  color: #fff;
}
</style>