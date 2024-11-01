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
            <b class="uppercase">Cập nhật chăm sóc khách hàng</b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6 pad-no">
                        <div class="col-12 pad-no">
                          <div class="form-group">
                            <label class="control-label">Học sinh</label>
                            <input
                                    class="form-control"
                                    :value="student.name"
                                    type="text"
                                    readonly
                                  >
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
                      <button class="apax-btn full edit" type="submit" @click="careUpdate">
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
        name:"",
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
     u.a()
      .get("/api/customerCares/"+this.$route.params.id)
      .then(response => {
        this.student.accounting_id=response.data.accounting_id
        this.student.name = response.data.student_name
        this.student.crm_id =  response.data.crm_id
        this.student.parent_name= response.data.parent_name
        this.student.parent_mobile= response.data.parent_mobile
        this.student.parent_email= response.data.parent_email
        this.customer_care.created_at = response.data.created_at
        this.customer_care.creator_id = response.data.creator_id
        this.customer_care.contact_method_id = response.data.contact_method_id
        this.customer_care.contact_quality_id= response.data.contact_quality_id
        this.customer_care.quality_score= response.data.quality_score
        this.customer_care.note= response.data.note
        u.a()
        .get(`/api/branches/${response.data.branch_id}/user-care`)
        .then(response => {
          this.creators = response.data;
        });
      });    
  },
  methods: {
    closeModal() {
      this.$router.push("/student-care");
    },
    careUpdate() {
      if (this.customer_care.created_at == "") {
        alert("Ngày chăm sóc không được để trống");
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
       u.a().put("/api/customerCares/"+this.$route.params.id, this.customer_care)
        .then(response => {
          alert("Cập nhật chăm sóc thành công");
          this.closeModal();
        });
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
  }
};
</script>