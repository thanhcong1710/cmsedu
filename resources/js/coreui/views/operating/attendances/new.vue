<template>
  <div class="animated fadeIn apax-form" id="class register">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i>
            <strong>Điểm danh</strong>
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
            <div class="card-body">
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="control-label">Trung Tâm</label>
                    <vue-select
                      label="name"
                      placeholder="Vui lòng chọn một trung tâm"
                      :options="cache.branches"
                      v-model="filter.branch"
                      language="tv-VN"
                      :onChange="selectBranch"
                      :disabled="html.disable.filter.branch"
                    ></vue-select>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="control-label">Kỳ học</label>
                    <div class="dropdown-toggle clearfix">
                      <select
                        @change="getSelectProgram(cache.branch,filter.semester)"
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
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="control-label">Chương trình</label>
                    <v-select
                            placeholder="Chọn chương trình"
                            select-label="Enter để chọn chương trình này"
                            :options="optionsProgram"
                            :disabled="html.disable.filter.program"
                            @input="selectedProgram"
                            label="name">
                    </v-select>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="control-label">Lớp học</label>
                    <c-select
                      placeholder="Chọn lớp học"
                      select-label="Enter để lớp này"
                      :options="classOptions"
                      @input="selectedToClass"
                      :disabled="html.disable.filter.classes"
                      label="name">
                    </c-select>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="control-label">Chọn tháng</label>
                    <datePicker
                      class="form-control calendar"
                      :value="in_month"
                      v-model="in_month"
                      :format="'YYYY-MM'"
                      type="month"
                      :not-before="cache.class_info.class_start_date"
                      :not-after="cache.class_info.class_end_date"
                      placeholder="Tháng điểm danh"
                      :disabled="html.disable.filter.classes"
                      @change="changeMonth"
                      lang="en"
                    >
                    </datePicker>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="row">
                <div class="col-md-12" id="register-detail">
                  <div v-show="action.loading" class="ajax-load content-loading">
                    <div class="load-wrapper">
                      <div class="loader"></div>
                      <div v-show="action.loading" class="loading-text cssload-loader">
                        {{html.content.loading }}
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
                        <div class="table-responsive scrollable">
                          <table class="table table-striped table-bordered apax-table">
                            <thead>
                            <tr>
                              <th colspan="6">❑ Lớp :{{cache.class_info.class_name}}</th>
                              <th colspan="3">❑ Level : {{cache.class_info.level_name}}</th>
                              <th colspan="4">❑ Thứ : {{cache.class_info.weekdays}}</th>
                              <th colspan="12">❑ Thời gian : {{cache.class_info.shifts_name}} ( {{cache.class_info.class_time}} )
                              </th>
                            </tr>
                            </thead>
                            <tbody v-show="months.length >0">
                            <tr>
                              <th class="tg-o23m" colspan="4">Giáo viên</th>
                              <th colspan="21"></th>
                            </tr>
                            <tr>
                              <th colspan="4">{{cache.class_info.teachers_name}}</th>
                              <th v-for="(m, inm) in months" v-bind:colspan="m.size">Tháng {{m.d}}</th>
                              <th rowspan="2" v-bind:colspan="(21-size)">Tổng</th>
                            </tr>
                            <tr>
                              <th>STT</th>
                              <th>Tên</th>
                              <th>Mã HV</th>
                              <th>Lớp (Tuổi)</th>
                              <th v-for="d in days">{{ d }}</th>
                            </tr>
                            <tr v-for="(student, ind) in listStudent" v-bind:key="ind" class="scrollable">
                              <td colspan="1">{{ind + 1}}</td>
                              <td colspan="1">{{student.student_name.toUpperCase()}}</td>
                              <td colspan="1">{{student.student_accounting_id}}</td>
                              <td colspan="1">{{student.school_grade_name}}</td>
                              <td v-for="(attendance,i) in student.attendances" colspan="1">
                                <select style="padding: 2px 3px" :disabled="(parseInt(attendance.status) == 0 || attendance.status == 1) && !isActiceAll " v-if="attendance.status >= 0" v-model="attendance.status" @change="onChange($event,student.student_id+'_'+attendance.date)">
                                  <option v-for="option in options" v-bind:value="option.value">
                                    {{ option.text }}
                                  </option>
                                </select>
                                <select :disabled="parseInt(attendance.status_make_up) == 1 && !isActiceAll" style="margin-top: 6px;padding: 2px 3px" v-if="parseInt(attendance.status) == 0" v-model="attendance.status_make_up" @change="onChangeMakeUp($event,student.student_id+'_'+attendance.date)">
                                  <option v-for="option in optionMakeUp" v-bind:value="option.value">
                                    {{ option.text }}
                                  </option>
                                </select>
                                <span v-show="(parseInt(attendance.status) == 1 || parseInt(attendance.status) == 0) && !attendance.note" style="margin-top: -3px;" class="apax-btn edit" @click="showModalNote(attendance.note,student.student_id+'_'+attendance.date, ind,i)"><i class="fa fa-plus"></i></span>
                                <span v-show="attendance.note" style="margin-top: -3px;" class="apax-btn detail" @click="showModalNote(attendance.note,student.student_id+'_'+attendance.date, ind,i)"><i class="fa fa-eye"></i></span>
                              </td>
                              <td v-bind:colspan="(21-size)"><b>{{sums[ind]}}</b></td>
                            </tr>
                            <tr style="font-weight: bold;">
                              <td colspan="1"><b>TỔNG</b></td>
                              <td colspan="1"></td>
                              <td colspan="1"></td>
                              <td colspan="1"></td>
                              <td v-for="attendance in attendances" colspan="1"><b>{{attendance.sum}}</b></td>
                              <td v-bind:colspan="(21-size)"></td>
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
        <b-modal size="sm" title="GHI CHÚ" class="modal-primary"  v-model="modalNote" :ok-disabled="modalText ? false: true" ok-title="Lưu" @ok="updateNote()" cancel-title="Bỏ qua">
            <div class="form-group">
              <textarea v-model="modalText" class="description form-control" rows="4" style="min-height: 115px; border: 1px solid #e1edff;"></textarea>
            </div>
        </b-modal>
        <b-modal size="sm" title="GHI CHÚ" class="modal-primary" v-model="modalNote1" :hide-footer="true">
          <div class="form-group">
            <textarea v-model="modalText" class="description form-control" rows="4" style="min-height: 115px; border: 1px solid #e1edff;"></textarea>
          </div>
        </b-modal>
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
import vSelect from "vue-select"
import cSelect from "vue-select"
import select from 'vue-select'
import productSelect from '../../base/common/product-select-new'
// import datePicker from 'vue2-datepicker'
import datePicker from 'vue2-datepicker'
// import datePicker from '../../base/common/DatePicker'

export default {
  name: "Attendance-List",
  components: {
    tree,
    search,
    paging,
    apaxButton,
    searchBranch,
    Multiselect,
    productSelect,
    vSelect,
    cSelect,
    datePicker,
    "vue-select": select,
  },
  data() {
    return {
      isActiceAll: false,
      in_month:moment(new Date()).format('YYYY-MM'),
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
      selected1: [],
      selected: [],
      options: [
        { text: 'Chọn', value: '' },
        { text: 'Đi học', value: '1' },
        { text: 'Nghỉ học', value: '0' }
      ],
      optionMakeUp: [
        { text: 'Chọn', value: '' },
        { text: 'Học bù', value: '1' },
      ],
      modalAttendance: false,
      modalNote: false,
      modalNote1: false,
      tmp_index0: '',
      tmp_index: '',
      modalText: '',
      modalData: [],
      months: [],
      days: [],
      dates: [],
      listStudent: [],
      attendances: [],
      sums: [],
      studentsId: [],
      size: [],
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
            classes: true,
            program: true
          }
        }
      },
      action: {
        loading: false,
        loading_contracts: false
      },
      current_branch: 0,
      optionsProgram: [],
      classOptions: [],
      url: {
        api: "/api/registers/",
        nick: "/api/registers/nick/",
        class: "/api/attendances/class/",
        classes: "/api/attendances/classes/",
        branches: "/api/settings/branches",
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
        up_semester: "",
        select_month: "",
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
      filter: {
        class: '',
        branch: '',
        keyword: '',
        semester: '',
        up_class: '',
        up_semester: '',
      },
    };
  },
  created() {
    const session = u.session().user;
    if(session.role_id == 999999999){
      this.isActiceAll = true;
    }
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
    },
  },
  methods: {
    start() {
      // this.filter.select_month = moment(new Date()).format('YYYY-MM')
      if (u.authorized() || u.session().user.role_id == 56) {
        this.html.loading.action = true;
        u.g(this.url.branches)
          .then(response => {
            const data = response;
            this.cache.branches = data;
            this.cache.branch = "";
            if(data.length>1){
              this.cache.branch = ''
              this.html.class.display.filter.branch = 'display'
              this.html.disable.filter.branch = false;
            }else{
              this.cache.branch = this.session.user.branch_id
              this.loadSemesters()
            }
            this.html.class.display.filter.semester = "display";
            this.html.loading.action = false;
          })
          .catch(e => u.log("Exeption", e));
      } else {
        this.filter.branch = this.session.user.branches[0]
        this.cache.branch = this.session.user.branch_id
        this.cache.branches = this.session.user.branches
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
    selectBranch(data = null) {
      if (data)
        this.cache.branch = data.id;
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
        this.layout.showDetail = true;
        //this.inforSearch.class = selected_class.model.item_id;
        let url = `/api/attendances/class/${this.inforSearch.class}`;
        this.action.loading = true;
        u.g(url).then(response => {
          this.action.loading = false;
          const data = response;
          this.cache.class_info = data.class;
          this.cache.schedules = data.class.schedules;
          this.getStudents();
        });
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
          this.getStudents();
        });
    },
    getStudents() {
      this.action.loading = true;
      let url = `/api/attendances-new/students/${this.filter.class}/${getDateCustom(new Date())}?m=${this.in_month}`;
      u.a()
        .get(url)
        .then(resp => {
          this.action.loading = false;
          let arrayTemp = resp.data.data[0];
          this.listStudent = arrayTemp;
          this.months = resp.data.data[1];
          this.days = resp.data.data[2];
          this.dates = resp.data.data[3];
          this.attendances = resp.data.data[4];
          this.size = resp.data.data[5];
          this.sums = resp.data.data[6];
          this.studentsId = resp.data.data[7];
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
    },
    greet(id) {
      return this.selected[id]
    },
    changeMonth(date) {
      this.in_month = moment(date).format('YYYY-MM')
      this.getStudents()
    },
    showModalNote(note, id, index0, index){
      this.tmp_index0 = index0
      this.tmp_index = index
      this.resetModal()
      if (note){
        this.modalNote1 = true
        this.modalText = note
      }
      else{
        this.modalData = { id:id ,dates:this.dates, class_id:this.filter.class,type:'note'}
        this.modalNote = true
      }
    },
    resetModal(){
      this.modalText = null
      this.modalData = null
    },
    updateNote(){
      let data = this.modalData
      data.note = this.modalText
      this.postProcess(data,3)
    },
    postProcess(data, i){
      data.s = this.studentsId
      this.action.loading = true
      u.a()
        .post("/api/attendances-new/save", data)
        .then(response => {
          if (i == 3)
            this.listStudent[this.tmp_index0].attendances[this.tmp_index].note = data.note
          if (response.data.data.sum.length > 0){
            this.attendances = response.data.data.sum
            this.sums = response.data.data.sumStudent
          }
          this.action.loading = false
        })
        .catch(e => {
        })
    },
    onChangeMakeUp(event, id) {
      if (event.target.value != ''){
        let data = { data: event.target.value, id:id ,dates:this.dates, class_id:this.filter.class,type:'makeup'}
        this.postProcess(data,2)
      }
    },
    onChange(event, id) {
      if (event.target.value != ''){
        let data = { data: event.target.value, id:id ,dates:this.dates, class_id:this.filter.class}
        this.postProcess(data,1)
      }
    },
    selectProduct(product){
      this.getSelectProgram(parseInt(this.filter.branch),product.id)
    },
    getSelectProgram(branch_id,product_id){
      u.a().get(`/api/all/programs/${branch_id}/${product_id}`).then((response) => {
        this.optionsProgram         = response.data
        this.html.disable.filter.program = false
      })
    },
    selectedProgram(program){
      this.filter.select_month = moment(new Date()).format('YYYY-MM')
      u.a().get(`/api/support/transfer-all-class/${parseInt(this.cache.branch)}/${program.id}`).then((response) => {
        let data = response.data.data
        this.classOptions  = data
        this.html.disable.filter.classes = false
      })
    },
    selectedToClass(classif){
      let url = `/api/attendances/class/${classif.id}`
      this.action.loading = true
      this.filter.class = classif.id
      u.g(url).then(response => {
        const data = response
        this.cache.class_info = data.class
        this.getStudents()
      });
    }
  }
};
</script>

<style scoped lang="scss">
.apax-form .filter-label {
  padding: 5px;
}
.apax-table{font-size: 12px!important;}

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
