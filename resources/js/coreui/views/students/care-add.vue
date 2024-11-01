<template>
  <div class="animated fadeIn apax-form">
    <div v-show="flags.form_loading" class="ajax-load content-loading">
      <div class="load-wrapper">
        <div class="loader"></div>
        <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang xứ lý dữ liệu...</div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-hand"></i>
            <b class="uppercase">Chăm sóc khách hàng</b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6 pad-no">
                        <div class="col-12 pad-no" :class="displaySearchBranch">
                           <div class="form-group">
                            <label class="control-label">Chọn Trung Tâm</label>
                            <branch
                              :onSelect="filter.branch.action"
                              :options="filter.branch.options"
                              :disabled="filter.branch.disabled"
                              :placeholder="filter.branch.placeholder"
                            ></branch>
                          </div>
                        </div>
                        <div class="col-12 pad-no" :class="displaySearchStudent">
                           <div class="form-group">
                            <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc Tên</label>
                            <search
                              :endpoint="filter.search.link"
                              :suggestStudents="filter.search.find"
                              :onSelectStudent="filter.search.action"
                            ></search>
                           </div> 
                        </div>
                        <div class="col-md-12 pad-no">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Mã Cyber</label>
                                <input
                                  class="form-control"
                                  :value="student.accounting_id"
                                  type="text"
                                  readonly
                                >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Mã CMS</label>
                                <input
                                  class="form-control"
                                  :value="student.crm_id"
                                  type="text"
                                  readonly
                                >
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="control-label">Phụ huynh</label>
                            <input
                              class="form-control"
                              :value="student.parent_name"
                              type="text"
                              readonly
                            >
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số điện thoại</label>
                                <input
                                  class="form-control"
                                  :value="student.parent_mobile"
                                  type="text"
                                  readonly
                                >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Email</label>
                                <input
                                  class="form-control"
                                  :value="student.parent_email"
                                  type="text"
                                  readonly
                                >
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 pad-no">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="control-label">Ngày</label>
                            <date-picker
                              style="width:100%;"
                              v-model="customer_care.created_at"
                              :clearable="true"
                              :not-before="datepickerOptions.minDate"
                              :shortcuts="datepickerOptions.shortcuts"
                              :lang="datepickerOptions.lang"
                              format="YYYY-MM-DD"
                              placeholder="Chọn thời gian chăm sóc"
                            ></date-picker>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6 pad-no">
                              <div class="form-group">
                                <label class="control-label">Người thực hiện</label>
                                <select v-model="customer_care.creator_id" class="form-control" readonly>
                                  <option value>Chọn người thực hiện</option>
                                  <option
                                    :value="creator.id"
                                    v-for="(creator, index) in creators"
                                    :key="index"
                                    >{{creator.username}}</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6 pad-no">
                              <div class="form-group">
                                <label class="control-label">Phương thức</label>
                                <select class="form-control" v-model="customer_care.contact_method_id">
                                  <option value>Chọn phương thức</option>
                                  <option
                                    :value="contact.id"
                                    v-for="(contact, index) in contacts"
                                    :key="index"
                                  >{{contact.name}}</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6 pad-no">
                              <div class="form-group">
                                <label class="control-label">Trạng thái khách hàng</label>
                                <select v-model="customer_care.contact_quality_id" @change="changeQuality()"  class="form-control">
                                  <option value>Chọn điểm contact</option>
                                  <option
                                    :value="quality.id"
                                    v-for="(quality, index) in qualities"
                                    :key="index"
                                  >{{quality.title}}</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6 pad-no">
                              <div class="form-group">
                                <label class="control-label">Điểm</label>
                                <input type="text" :value="customer_care.quality_score"  class="form-control"  readonly/>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="control-label">Nội dung</label>
                            <textarea v-model="customer_care.note" class="form-control" rows="4"></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="col-sm-12 col-sm-offset-3">
                      <button class="apax-btn full edit" type="submit" @click="careAdd">
                        <i class="fa fa-save"></i> Lưu
                      </button>
                      <router-link class="apax-btn full warning" :to="`/student-care`">
                        <i class="fa fa-sign-out"></i> Quay lại
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <b-card>
          <div slot="header">
            <b class="uppercase">Lịch sử chăm sóc</b>
          </div>
          <div>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Thời gian</th>
                  <th>Người thực hiện</th>
                  <th>Phương thức</th>
                  <th>Quality contact</th>
                  <th>Điểm</th>
                  <th>Nội dung</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(care, index) in cares" :key="index">
                  <td>{{care.created_at}}</td>
                  <td>{{care.creator}}</td>
                  <td>{{care.contact_name}}</td>
                  <td>{{care.quality_name}}</td>
                  <td>{{care.quality_score}}</td>
                  <td>{{care.note}}</td>
                  <td>
                    <button class="btn btn-danger" @click="deleteCares(care.id, index)">
                      <i class="fa fa-times"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="text-center">
              <nav aria-label="Page navigation">
                <appPagination
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
                ></appPagination>
              </nav>
            </div>
          </div>
        </b-card>
        <b-modal
          title="THÔNG BÁO"
          class="modal-success"
          size="sm"
          v-model="modal"
          @ok="closeModal"
          ok-variant="success"
        >
          <div v-html="message"></div>
        </b-modal>
      </div>
    </div>
  </div>
</template>
<script>
import u from "../../utilities/utility";
import branch from "../../components/Selection";
import search from "../../components/StudentSearch";
import apaxbtn from "../../components/Button";
import Pagination from "../../components/Pagination";
import datePicker from "vue2-datepicker";
export default {
  name: "Add-Contract",
  components: {
    branch,
    search,
    appPagination: Pagination,
    datePicker
  },
  data() {
    return {
      displaySearchBranch: "hidden",
      displaySearchStudent: "hidden",
      student: {
        accounting_id: "",
        crm_id: "",
        parent_name: "",
        parent_mobile: "",
        parent_email: ""
      },
      customer_care: {
        crm_id: "",
        student_id: "",
        created_at: "",
        creator_id: "",
        contact_method_id: "",
        contact_quality_id:"",
        quality_score:0,
        note: ""
      },
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: "",
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
      creators: [],
      cares: [],
      contacts: [],
      qualities:[],
      list_style: "line",
      router_url: "/cares/list",
      pagination_id: "care_paginate",
      pagination_class: "care_class",
      modal: false,
      message: "",
      branch_id: 0,
      role_id: 0,
      flags: {
        form_loading: false,
        requesting: false
      }
    };
  },
  created() {
    this.branch_id = u.session().user.branch_id;
    this.role_id = u.session().user.role_id;
    this.customer_care.creator_id = u.session().user.id;
    this.checkUser();
    u.a()
      .get("/api/contact/allmethod/")
      .then(response => {
        this.contacts = response.data;
      });
     u.a()
      .get("/api/quality/allquality")
      .then(response => {
        this.qualities = response.data;
      });  
  },
  methods: {
    checkUser() {
      switch (parseInt(this.role_id)) {
        case 999999999:
          this.displaySearchBranch = "display";
          this.displaySearchStudent = "hidden";
          break;
        case 88888888:
          this.displaySearchBranch = "display";
          this.displaySearchStudent = "hidden";
          break;
        case 7777777:
          this.displaySearchBranch = "display";
          this.displaySearchStudent = "hidden";
          break;
        default:
          this.displaySearchBranch = "hidden";
          this.displaySearchStudent = "display";
          this.getEndpoint = parseInt(this.branch_id);
          u.a()
            .get(`/api/branches/${this.branch_id}/user-care`)
            .then(response => {
              this.creators = response.data;
            });
          break;
      }
    },
    get(link) {
      this.flags.form_loading = true;
      u.a()
        .get(link)
        .then(response => {
          this.cares = response.data.cares;
          this.pagination = response.data.pagination;
          this.flags.form_loading = false;
        })
        .catch(e => console.log(e));
    },
    getCares(page_url) {
      this.key = "";
      this.value = "";
      if (this.customer_care.student_id != "") {
        this.key += "student_id,";
        this.value += this.customer_care.student_id + ",";
      }
      this.key = this.key ? this.key.substring(0, this.key.length - 1) : "_";
      this.value = this.value
        ? this.value.substring(0, this.value.length - 1)
        : "_";
      page_url = page_url ? "/api" + page_url : "/api/cares/list/1";
      page_url += "/" + this.key + "/" + this.value;
      this.get(page_url);
    },
    makePagination(meta, links) {
      let pagination = {
        current_page: data.current_page,
        last_page: data.last_page,
        next_page_url: data.next_page_url,
        prev_page_url: data.prev_page_url
      };
      this.pagination = pagination;
    },
    goTo(link) {
      this.getCares(link);
    },
    deleteCares(id, index) {
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa ngày này không?");
      if (delStdConf === true) {
        u.a()
          .delete("/api/customerCares/" + id)
          .then(response => {
            this.cares.splice(index, 1);
          })
          .catch(error => {});
      }
    },
    closeModal() {
      this.$router.push("/student-care");
    },
    searchSuggestStudent(keyword) {
      if (keyword && keyword.length > 3) {
        return new Promise((resolve, reject) => {
          u.g(`/api/cares/search/students/${this.branch_id}/${keyword}`)
            .then(response => {
              const resp = response.length
                ? response
                : [
                    {
                      label: "Không tìm thấy",
                      branch_name: "Không có kết quả nào phù hợp"
                    }
                  ];
              resolve(resp);
            })
            .catch(e => console.log(e));
        });
      }
    },

    selectBranch(branch) {
      this.branch_id = branch.id;
      this.displaySearchStudent = "display";
      u.a()
        .get(`/api/branches/${branch.id}/user-care`)
        .then(response => {
          this.creators = response.data;
        });
    },
    selectStudent(student) {
      this.flags.form_loading = true;
      u.a()
        .get(`/api/students/detail-care/${student.student_id}`)
        .then(response => {
          this.student.accounting_id = response.data.accounting_id;
          this.student.crm_id = response.data.crm_id;
          this.student.parent_name = response.data.gud_name1;
          this.student.parent_mobile = response.data.gud_mobile1;
          this.student.parent_email = response.data.gud_email1;
          this.customer_care.student_id = response.data.id;
          this.customer_care.crm_id = response.data.crm_id;
          this.getCares();
          this.flags.form_loading = false;
        })
        .catch(e => console.log(e));
    },
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
    careAdd() {
      if (this.customer_care.student_id === "") {
        alert("Chọn học sinh chăm sóc");
        return false;
      }
      if (this.customer_care.created_at == "") {
        alert("Ngày chăm sóc không được để trống");
        return false;
      }
      if (this.customer_care.creator_id == "") {
        alert("Người thực hiện không được để trống");
        return false;
      }
      if (this.customer_care.contact_method_id == "") {
        alert("Phương thức không được để trống");
        return false;
      }
      if (this.customer_care.contact_quality_id == "") {
        alert("Đánh giá contact không được để trống");
        return false;
      }
      if (this.customer_care.note == "") {
        alert("Nội dung không được để trống");
        return false;
      }
      this.customer_care.created_at = this.getDate(
        this.customer_care.created_at
      );
      u.a()
        .post("/api/customerCares", this.customer_care)
        .then(response => {
          alert("Thêm mới chăm sóc thành công");
          this.getCares();
        });
    },
    deleteCares(id, index) {
      const delStdConf = confirm("Bạn có chắc rằng muốn xóa ngày này không?");
      if (delStdConf === true) {
        u.a()
          .delete("/api/customerCares/" + id)
          .then(response => {
            this.cares.splice(index, 1);
          })
          .catch(error => {});
      }
    },
    changeQuality(){
      this.customer_care.quality_score=0;
      let tmp_qualiry = this.customer_care.contact_quality_id
      let tmp_score = 0
      this.qualities.forEach(function(element) {
        if(element.id == tmp_qualiry){
          tmp_score = element.score
        }
      })
      this.customer_care.quality_score = tmp_score
    }
  }
};
</script>