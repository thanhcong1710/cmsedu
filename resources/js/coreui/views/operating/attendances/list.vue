<template>
  <div class="animated fadeIn apax-form" id="class register">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i>
            <strong>Điểm Danh</strong>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">
                <small class="text-muted">
                  <i class="fa fa-skype"></i>
                </small>
              </a>
            </div>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">
                {{
                html.loading.content }}
              </div>
            </div>
          </div>
          <div class="content-detail classes_tree">
            <div class="col-lg-12" style="min-height:500px;">
              <div class="row">
                <div class="col-md-3" id="register-filter">
                  <div class="col-md-12" :class="html.class.display.filter.branch">
                    <div class="row">
                      <div class="col-md-4 filter-label">
                        <label class="filter-label control-label">Trung Tâm:</label>
                      </div>
                      <div class="col-md-8 filter-selection">
                        <div class="row form-group suggest-branch">
                          <searchBranch
                            v-model="filter.branch"
                            :onSelect="selectBranch"
                            :options="cache.branches"
                            :disabled="html.disable.filter.branch"
                            placeholder="Vui lòng chọn một trung tâm"
                          ></searchBranch>
                          <br>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12" :class="html.class.display.filter.semester">
                    <div class="row">
                      <div class="col-md-4 filter-label">
                        <label class="filter-label control-label">Kỳ Học:</label>
                      </div>
                      <div class="col-md-8 filter-selection">
                        <div class="row form-group">
                          <select
                            @change="selectSemester"
                            v-model="filter.semester"
                            :disabled="html.disable.filter.semester"
                            class="filter-selection semester form-control"
                          >
                            <option value>Vui lòng chọn một kỳ học</option>
                            <option
                              :value="semester.id"
                              v-for="(semester, ind) in cache.semesters"
                              :key="ind"
                            >{{semester.name}}</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 filter-classes" :class="html.class.display.filter.classes">
                    <div class="row">
                      <label class="filter-label control-label">Lớp Học:</label>
                      <div class="row tree-view">
                        <tree
                          :data="cache.classes"
                          text-field-name="text"
                          allow-batch
                          @item-click="selectClass"
                        ></tree>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-9" id="register-detail">
                  <div v-show="action.loading" class="ajax-load content-loading">
                    <div class="load-wrapper">
                      <div class="loader"></div>
                      <div v-show="action.loading" class="loading-text cssload-loader">
                        {{
                        html.content.loading }}
                      </div>
                    </div>
                  </div>
                  <div class="class-info" :class="html.class.display.class_info">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">Tên Lớp Học:</div>
                        <div class="col-md-9 field-detail">{{ cache.class_info.class_name }}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">Thời Gian:</div>
                        <div class="col-md-9 field-detail">{{ cache.class_info.class_time }}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">Giáo Viên:</div>
                        <div class="col-md-9 field-detail">
                          <span v-html="cache.class_info.teachers_name"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">Tổng Số Học Sinh:</div>
                        <div class="col-md-9 field-detail">
                          {{ cache.class_info.total_students,
                          cache.class_info.class_max_students | totalPerMax }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">Thời Gian và Địa Điểm Học:</div>
                        <div class="col-md-9 field-detail">
                          <span v-html="cache.class_info.time_and_place"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">CS - Giáo Viên Chủ Nhiệm:</div>
                        <div class="col-md-9 field-detail">{{ cache.class_info.cm_name }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 contracts-list" :class="html.class.display.contracts_list">
                    <div class="row">
                      <div class="col-md-12">
                        <div
                          :class="html.class.loading ? 'loading' : 'standby'"
                          class="ajax-loader"
                        >
                          <img src="/static/img/images/loading/mnl.gif">
                        </div>
                        <div class="controller-bar content-heading">
                          <div class="row">
                            <div class="col-md-4">
                              <multiselect
                                v-model="inforSearch.dateAttendance"
                                track-by="cjrn_id"
                                label="cjrn_classdate"
                                placeholder="Chọn ngày cần điểm danh"
                                :options="cache.schedules"
                                :searchable="true"
                                @input="getStudents()"
                                :openDirection="'below'"
                                select-label
                                :allow-empty="false"
                                :disabled="false"
                              ></multiselect>
                            </div>
                            <div class="col-md-4">
                              <input
                                v-model="inforSearch.macrm"
                                type="text"
                                class="form-control"
                                placeholder="Tìm theo mã cms, mã cyber..."
                              >
                            </div>
                            <div class="col-md-4">
                              <input
                                v-model="inforSearch.name"
                                type="text"
                                class="form-control"
                                placeholder="Tìm theo tên học sinh..."
                              >
                            </div>
                          </div>
                        </div>
                        <div class="table-responsive scrollable">
                          <table class="table table-striped table-bordered apax-table">
                            <thead>
                              <tr>
                                <th>STT</th>
                                <th style="min-width: 100px;">Tên học sinh</th>
                                <th width="100">Thời gian</th>
                                <th width="50">Số buổi</th>
                                <th width="100">Số buổi được học</th>
                                <th width="100">Số buổi đã học</th>
                                <th width="150">Trạng thái</th>
                                <th>Phụ huynh</th>
<!--                                <th width="100">Số điện thoại phụ huynh</th>-->
<!--                                <th width="30">Bài tập</th>-->
<!--                                <th>Lý do</th>-->
                                <th style="min-width: 150px">Ghi chú</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(student, ind) in filterStudent" v-bind:key="ind">
                                <td align="right">{{ind + 1}}</td>
                                <td>
                                  <div>
                                    <strong>{{ student.student_name }}</strong>
                                  </div>
                                  <div>{{ student.crm_id }}</div>
                                  <div>({{student.student_accounting_id}})</div>
                                  <div>{{ student.student_nick }}</div>
                                  <div>{{ student.type | contractType }}</div>
                                </td>
                                <td>{{ student.enrolment_start_date | formatDate }} ~ {{ student.enrolment_last_date | formatDate }}</td>
                                <td>{{ student.total_sessions }}</td>
                                <td>{{ student.enrolment_real_sessions }}</td>
                                <td>{{ student.totalSessionLearned }}</td>
                                <td>
                                  <span v-if="student && student.attendance_status === 8">
                                    Vắng mặt do đang bảo lưu giữ chỗ
                                  </span>
                                  <multiselect
                                    v-if="student && student.attendance_status !== 8"
                                    v-model="student.attendance_status_drop"
                                    track-by="id"
                                    label="name"
                                    :placeholder="!enableAttendance ? 'Chưa đến ngày' :'Chọn trạng thái'"
                                    deselectLabel="Nhấn Enter để xóa"
                                    :options="resource.status"
                                    @input="save(student)"
                                    :searchable="true"
                                    :openDirection="'below'"
                                    select-label
                                    :allow-empty="true"
                                    :disabled="!enableAttendance"
                                  ></multiselect>
                                </td>
                                <td>
                                  <strong>{{ student.gud_name1 }}</strong><br/>
                                  <span>{{ student.gud_mobile1 }}</span>
                                </td>
<!--                                <td>-->
<!--                                  <strong>{{ student.gud_mobile1 }}</strong>-->
<!--                                </td>-->
<!--                                <td>-->
<!--                                  <b-form-checkbox-->
<!--                                    v-bind:id="'' + student.enrolment_id"-->
<!--                                    class="cursor-pointer checkbox-center"-->
<!--                                    v-model="student.homework"-->
<!--                                    value="1"-->
<!--                                    unchecked-value="0"-->
<!--                                    @change="changeCheckBox(student)"-->
<!--                                  ></b-form-checkbox>-->
<!--                                </td>-->
<!--                                <td>-->
<!--                                  <textarea-->
<!--                                    v-model="student.reason"-->
<!--                                    @change="save(student)"-->
<!--                                    class="form-control"-->
<!--                                    rows="2"-->
<!--                                  ></textarea>-->
<!--                                </td>-->
                                <td>
                                  <textarea
                                    v-model="student.note"
                                    @change="save(student)"
                                    class="form-control"
                                    rows="4"
                                    style="height: 100%"
                                  ></textarea>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import tree from "vue-jstree";
import u from "../../../utilities/utility";
import search from "../../../components/Search";
import apaxButton from "../../../components/Button";
import paging from "../../../components/Pagination";
import searchBranch from "../../../components/Selection";
import Multiselect from "vue-multiselect";
import { getDateCustom } from '../../base/common/utils'

export default {
  name: "Attendance-List",
  components: {
    tree,
    search,
    paging,
    apaxButton,
    searchBranch,
    Multiselect
  },
  data() {
    return {
      item: {},
      inforSearch: {
        dateAttendance: undefined,
        name: "",
        macrm: "",
        class: undefined
      },
      layout: {
        showDetail: false
      },
      resource: {
        status: [
          { id: 1, name: "Có đi học" },
          { id: 2, name: "Vắng mặt" },
          { id: 3, name: "Đi muộn" },
          { id: 4, name: "Về sớm" },
          { id: 5, name: "Vắng mặt có lý do" },
          { id: 6, name: "Nghỉ lễ" },
          { id: 7, name: "Nghỉ có phép" },
        ]
      },
      listStudent: [],
      html: {
        class: {
          loading: false,
          button: {
            up_semester_students: "success",
            class_schedule: "error",
            save_contracts: "error",
            add_contract: "primary",
            up_semester: "success",
            print: "success"
          },
          display: {
            modal: {
              schedule: false,
              register: false,
              semester: false
            },
            class_info: "display",
            contracts_list: "display",
            up_class_info: "display",
            up_contracts_list: "display",
            filter: {
              branch: "hidden",
              classes: "hidden",
              semester: "hidden",
              up_classes: "hidden",
              up_semester: "hidden"
            }
          }
        },
        loading: {
          content: "Đang tải dữ liệu...",
          up_class_action: false,
          up_action: false,
          action: false
        },
        content: {
          loading: "Đang tải dữ liệu...",
          label: {
            search: "Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh"
          },
          placeholder: {
            search: "Từ khóa tìm kiếm"
          },
          print: {
            enrolment: {}
          }
        },
        disable: {
          up_semester_students: true,
          class_schedule: true,
          save_contracts: true,
          load_contracts: true,
          add_contracts: true,
          up_semester: true,
          filter: {
            branch: true,
            semester: true,
            up_semester: true
          }
        }
      },
      action: {
        loading: false,
        loading_contracts: false
      },
      current_branch: 0,
      url: {
        api: "/api/registers/",
        nick: "/api/registers/nick/",
        class: "/api/attendances/class/",
        classes: "/api/attendances/classes/",
        branches: "/api/registers/branches",
        withdraw: "/api/registers/withdraw/",
        semesters: "/api/attendances/semesters",
        contracts: "/api/registers/contracts/",
        up_contracts: "/api/registers/semester/up/contracts/"
      },
      filter: {
        class: "",
        branch: "",
        keyword: "",
        semester: "",
        up_class: "",
        up_semester: ""
      },
      list: {
        nicks: [],
        students: [],
        contracts: [],
        up_students: [],
        up_contracts: []
      },
      cache: {
        nicks: [],
        schedules: [],
        class_info: {},
        up_class_info: {},
        selected_class: {},
        selected_up_class: {},
        class: "",
        up_class: "",
        branch: "",
        program: "",
        semester: "",
        up_semester: "",
        classes: [],
        up_classes: [],
        branches: [],
        semesters: [],
        up_semesters: [],
        contracts: [],
        up_students: [],
        class_dates: [],
        up_class_dates: [],
        checked_list: [],
        checked_up_list: [],
        check_all: "",
        check_up_all: "",
        available: 0,
        validated: 0,
        up_available: 0,
        selected_class_date: ""
      },
      class_date: {},
      up_class_date: {},
      pagination: {
        url: "",
        id: "",
        style: "line",
        class: "",
        spage: 1,
        ppage: 1,
        npage: 2,
        lpage: 2,
        cpage: 1,
        total: 2,
        limit: 20,
        pages: []
      },
      order: {
        by: "s.id",
        to: "DESC"
      },
      temp: [],
      temp_selected_list: [],
      temp_selected_up_list: [],
      session: u.session(),
      enable_attendance: true,
    };
  },
  created() {
    this.start();
  },
  computed: {
    filterStudent() {
      if (!this.inforSearch.name && !this.inforSearch.macrm) {
        return this.listStudent;
      } else {
        return this.listStudent.filter(item => {
                return item.student_name
                  .toLowerCase()
                  .includes(this.inforSearch.name.trim().toLowerCase()) && item.crm_id
                  .toLowerCase()
                  .includes(this.inforSearch.macrm.trim().toLowerCase()) || item.student_accounting_id
                  .toLowerCase()
                  .includes(this.inforSearch.macrm.trim().toLowerCase())
              });
      }
    },
    enableAttendance () {
      if (u.session().user.role_id == 1200){
        return  false
      }

      if (!this.enable_attendance)
        return true
      else
        return true
      // const currentDate     = Date.parse(new Date())
      // const semesterEndDate = Date.parse(_.get(this, 'cache.class_info.semester_end_date', ''))
      // const classEndDate    = Date.parse(_.get(this, 'cache.class_info.class_end_date', ''))
      // const semesterStatus  = parseInt(_.get(this, 'cache.class_info.semester_status'), 10)
      //
      // if (semesterEndDate < currentDate)
      //   return false
      //
      // return !(semesterStatus === 1 && classEndDate < currentDate)
    },
  },
  methods: {
    start() {
      if (u.authorized()) {
        this.html.loading.action = true;
        u.g(this.url.branches)
          .then(response => {
            const data = response;
            this.cache.branches = data;
            this.cache.branch = "";
            this.html.class.display.filter.branch = "display";
            this.html.class.display.filter.semester = "display";
            this.html.disable.filter.branch = false;
            this.html.loading.action = false;
          })
          .catch(e => u.log("Exeption", e));
      } else {
        this.cache.branch = this.session.user.branch_id;
        this.loadSemesters();
      }
    },
    loadSemesters() {
      this.html.loading.action = true;
      u.g(`${this.url.semesters}/${this.cache.branch}`)
        .then(response => {
          const data = response;
          this.cache.semesters = data;
          this.filter.semester = "";
          this.html.class.display.filter.semester = "display";
          this.html.disable.filter.semester = false;
          this.html.loading.action = false;
        })
        .catch(e => u.log("Exeption", e));
    },
    selectBranch(data) {
      this.cache.branch = data.id;
      this.filter.branch = data.id;
      this.loadSemesters();
    },
    selectSemester() {
      this.html.loading.action = true;
      this.cache.semester = this.filter.semester;
      u.log("Semester", this.filter.semester);
      u.g(`${this.url.classes}${this.cache.branch}/${this.cache.semester}`)
        .then(response => {
          const data = response;
          this.cache.classes = data;
          this.html.class.display.filter.classes = "display";
          this.html.loading.action = false;
        })
        .catch(e => u.log("Exeption", e));
    },
    selectClass(selected_class) {
      if (selected_class.model.item_type === "class") {
        this.layout.showDetail = true;
        this.inforSearch.class = selected_class.model.item_id;
        let url = `/api/attendances/class/${this.inforSearch.class}`;
        this.action.loading = true;
        u.g(url).then(response => {
          this.action.loading = false;
          const data = response;
          this.cache.class_info = data.class;
          this.cache.schedules = data.class.schedules;
          let today = getDateCustom(new Date())
          if (!u.cjrn_classdate(today, this.cache.schedules))
            this.enable_attendance = false
          else
            this.enable_attendance = true
          this.inforSearch.dateAttendance = u.cjrn_classdate(today,this.cache.schedules) ? {cjrn_id: u.cjrn_classdate(today,this.cache.schedules),cjrn_classdate: today}: this.cache.schedules[0];

          this.getStudents();
        });
      } else {
        this.layout.showDetail = false;
      }
    },
    changeCheckBox(student) {
      setTimeout(()=>{
        this.save(student);
      }, 100)
    },
    save(student) {
      this.html.content.loading = "Đang cập nhật...";
      student.attendance_date = this.inforSearch.dateAttendance.cjrn_classdate;
      student.attendance_status_id = 0;
      if (student.attendance_status_drop) {
        student.attendance_status_id = student.attendance_status_drop.id;
      }
      let data = { data: student };

      this.action.loading = true;
      u.a()
        .post("/api/attendances/save", data)
        .then(response => {
          this.action.loading = false;
          this.html.content.loading = "Đang tải dữ liệu...";
          // this.$toastr(
          //   "success",
          //   "Cập nhật thông tin điểm danh thành công",
          //   "Thông báo"
          // );
          this.getStudents();
        })
        .catch(e => {
          this.action.loading = false;
          this.html.content.loading = "Đang tải dữ liệu...";
          // this.$toastr(
          //   "error",
          //   "Có lỗi xảy ra khi cập nhật thông tin điểm danh",
          //   "Thông báo"
          // );
          this.getStudents();
        });
    },
    getStudents() {
      this.action.loading = true;
      let url = `/api/attendances/students/${this.inforSearch.class}/${
        this.inforSearch.dateAttendance.cjrn_classdate
      }`;
      u.a()
        .get(url)
        .then(resp => {
          this.action.loading = false;
          let arrayTemp = resp.data.data;
          arrayTemp.map(item => {
            item.attendance_status_drop = null;
            if (item.attendance_status) {
              let objectStatus = this.resource.status.filter(
                x => x.id == item.attendance_status
              );
              if (objectStatus.length) {
                item.attendance_status_drop = objectStatus[0];
              }
            }
            item.totalSessionLearned = this.countTotalSessionByStudent(item);
          });

          this.listStudent = arrayTemp;
        })
        .catch(e => {
          this.action.loading = false;
        });
    },
    countTotalSessionByStudent(student){
      if(!student) return 0

      const currentDate = new Date()
      currentDate.setHours(0, 0, 0, 0)
      const enrolmentLastDate = new Date(student.enrolment_last_date)
      if(currentDate.getTime() > enrolmentLastDate.getTime()){
        return student.enrolment_real_sessions
      }
      const end = u.convertDateToString(currentDate)
      const calSession = u.calSessions(student.enrolment_start_date, end, student.holydays, student.classdays)
      return _.get(calSession, 'total', 0)
    }
  }
};
</script>

<style scoped lang="scss">
.apax-form .filter-label {
  padding: 5px;
}

#register-detail {
  border-left: 0.5px solid #bbd5e8;
}

.filter-classes {
  border-top: 0.5px solid #bbd5e8;
  padding: 20px 0 0 20px;
  margin: 10px 0 0 0;
}

.tree-view {
  padding: 0;
  margin: 5px 0 20px 10px;
  width: 95%;
  overflow: -webkit-paged-x;
  overflow-x: scroll;
}

/* width */
::-webkit-scrollbar {
  height: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #b3cbe5;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #779cc4;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #3a6896;
}

#register-detail .class-info {
  padding: 0 0 20px 0;
  margin: 0 0 20px 0;
  border-bottom: 0.5px solid #bbd5e8;
}

.apax-form .field-label {
  font-weight: 600;
}

.apax-form .class-info .field-detail img {
  border-radius: 50%;
  width: 30px;
  height: 30px;
}

.class-info .col-md-12 {
  margin-bottom: 5px;
}

#register-detail table tr td {
  font-size: 0.6rem;
  padding: 0.6rem;
  text-align: center;
  font-weight: 400;
  text-transform: capitalize;
}

#register-detail table.contracts-list tr td {
  padding: 6px 6px 0 6px !important;
}

.cursor-pointer {
  cursor: pointer;
}

.checkbox-center {
  margin: 0 auto;
  padding-right: 0;
  padding-left: 16px;
}
</style>
