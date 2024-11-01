<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <loader :active="html.loader.processing" :spin="html.loader.spin" :text="html.loader.text" :duration="html.loader.duration" />
          <div slot="header">
            <i class="fa fa-id-card"></i>
            <b class="uppercase">Nhận xét học sinh</b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-12 pad-no">
                        <div class="row">
                          <div class="col-4 form-group">
                            <label class="control-label">Trung tâm</label>
                            <SearchBranch
                              :options="data.branches"
                              :disabled="data.branches.length <= 1"
                              :placeholder="html.search_branch.placeholder"
                              v-model="data.branch"
                            ></SearchBranch>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Loại đánh giá
                              </label>
                              <br>
                              <select class="selection program form-control" v-model="data.temp.product_id" @change="loadReportWeek">
                                <option value="1">UCREA</option>
                                <option value="2">BRIGHT IG - BLACK HOLE</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Giáo viên
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="! ( !disableTeacher || role_36)"
                                      v-model="data.temp.teacher_id"
                              >
                                <option value="-1" disabled>Chọn giáo viên</option>
                                <option :value="teacher.id" v-for="(teacher, idx) in data.teachers" :key="idx">
                                  {{ teacher.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Lớp
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="disableClass"
                                      v-model="data.temp.class_id"
                              >
                                <option value="-1" disabled>Chọn lớp</option>
                                <option :value="cls.id" v-for="(cls, idx) in data.classes" :key="idx">
                                  {{ cls.class_name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Thời gian nhận xét
                              </label>
                              <br>
                              <select class="selection program form-control" v-model="data.report_week_id" @change="getStudents(0)">
                                <option :value="report_week.id" v-for="(report_week, idx) in data.report_weeks" :key="idx">
                                  {{ report_week.title }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <!-- <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Tìm kiếm
                              </label>
                              <br>
                              <input
                                class="search-field form-control filter-input"
                                v-model="data.temp.keyword"
                                placeholder="Tìm học sinh theo: Tên, Mã CRM"
                              >
                            </div>
                          </div> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="col-sm-12 text-center">
                      <button class="apax-btn full detail" @click="getStudents(0)"><i class="fa fa-search"></i> Tìm kiếm</button>
                      <button class="apax-btn full edit" @click="lockStudent()"><i class="fa fa-save"></i> Chốt tất cả</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive scrollable">
                  <table class="table table-bordered apax-table">
                    <thead>
                    <tr>
                      <th width="40">STT</th>
                      <th>Tên trung tâm</th>
                      <th>Tên lớp</th>
                      <th>Giáo viên</th>
                      <th>Level</th>
                      <th>Mã LMS</th>
                      <th>Mã HS</th>
                      <th>Họ và tên</th>
                      <th>Ghi chú</th>
                      <th>Chương trình</th>
                      <th>Điểm đầu vào</th>
                      <th>Điểm gần nhất</th>
                      <th>Điểm tuần</th>
                      <th>Xếp loại</th>
                      <th>Nhận xét của GV</th>
                      <th>Đề xuất của GV</th>
                      <th>Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="data.students.length">
                      <tr v-for="(student, index) in data.students" >
                        <td>{{ index + 1 }}</td>
                        <td>{{ student.branch_name }}</td>
                        <td>{{ student.class_name }}</td>
                        <td>{{ student.teacher_name }}</td>
                        <td>{{ student.level_name }}</td>
                        <td>{{ student.crm_id }}</td>
                        <td>{{ student.accounting_id }}</td>
                        <td>{{ student.name }}</td>
                        <td>{{ student.note }}</td>
                        <td>{{ student.product_name }}</td>
                        <td><input class="form-control"
                                v-model="student.score_demo" @change="updateReportStudent(student,1)"/></td>
                        <td>{{ student.last_score }}</td>
                        <td>
                          <input class="form-control" v-model="student.score" v-if="student.is_lock!=1 || role_id== 999999999" @change="updateReportStudent(student)"/>
                          <span v-else>{{student.score}}</span>
                        </td>
                        <td>
                          <!-- <select class="form-control" v-model="student.report_type" v-if="student.is_lock!=1|| role_id== 999999999" @change="updateReportStudent(student)">
                                <option value="">Chọn loại xếp hạng</option>
                                <option value="1">Giỏi</option>
                                <option value="2">Khá</option>
                                <option value="3">Trung bình</option>
                                <option value="4">Yếu</option>
                              </select> -->
                          <span>{{student.report_type | filterStatus }}</span>
                        </td>
                        <td>
                            <textarea v-model="student.comment" v-if="student.is_lock!=1 || role_id== 999999999" @change="updateReportStudent(student)"></textarea>
                            <span v-else>{{student.comment}}</span>
                        </td>
                        <td>
                          <textarea v-model="student.suggestion" v-if="student.is_lock!=1 || role_id== 999999999" @change="updateReportStudent(student)"></textarea>
                          <span v-else>{{student.suggestion}}</span>
                        </td>
                        <td>
                          <!-- <button class="apax-btn full edit" @click="updateReportStudent(student)" v-if="student.is_lock!=1">Lưu</button>  -->
                          <button class="apax-btn full detail" @click="updateReportStudent(student,0,1)" v-if="student.is_lock!=1">Chốt</button></td>
                      </tr>
                    </template>
                    <template v-else>
                      <tr>
                        <td colspan="17">Không có học sinh</td>
                      </tr>
                    </template>
                    </tbody>
                  </table>
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
  import moment from "moment"
  import u from "../../../utilities/utility"
  import ContractSearch from "../../../components/ContractSearch"
  import SearchBranch from "../../../components/BranchSelection"
  import file from "../../../components/File"
  import datePicker from "vue2-datepicker"
  import loader from '../../../components/Loading'

  export default {
    name: "Add-Reserve",
    components: {
      datePicker,
      ContractSearch,
      SearchBranch,
      file,
      loader
    },
    data() {
      return {
        html: {
          search_branch: {
            display: "show",
            placeholder:
              "Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.",
            id: "search_suggest_branch"
          },
          search_contract: {
            display: "hide",
            placeholder: ""
          },
          modal: {
            show: false,
            message: ""
          },
          confirm: {
            show: false,
            message: ""
          },
          buttons: {
            save: {
              style: "success"
            }
          },
          loader: {
            spin: 'mini',
            duration: 500,
            processing: false,
            text: 'Đang xử lý dữ liệu...'
          }
        },
        data: {
          branches:[],
          branch: {
            id: 0
          },
          teachers: [],
          classes: [],
          students: [],
          temp: {
            branch_id: 0,
            teacher_id: "-1",
            class_id: "-1",
            keyword:"",
            product_id:1,
          },
          report_week_id:0,
          report_weeks:[],
        },
        role_36:false,
        role_id:''
      }
    },
    created() {
      this.data.branches = u.session().user.branches
      if(this.data.branches.length==1){
        this.data.branch = u.session().user.branches[0]
      }
      this.role_id=u.session().user.role_id
      if(u.session().user.role_id==36){
        this.data.temp.teacher_id=u.session().user.id
        this.role_36 = true
      }
      this.loadReportWeek();
    },
    methods: {
      getAllInfo(branch_id) {
        if(branch_id){
          this.html.loader.processing = true
          u.a().get(`/api/report_student/info/${branch_id}`)
            .then(response => {
              this.html.loader.processing = false
              if (response.data.code == 200) {
                this.data.teachers = response.data.data
                if(u.session().user.role_id!=36){
                  this.data.temp.teacher_id = "-1"
                }
              }else{
                u.showError(this.html.modal, response.data.message)
              }
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
        }
      },
      getAllClass(teacher_id) {
        if(teacher_id){
          this.html.loader.processing = true
          u.a().get(`/api/report_student/class_info/${teacher_id}?product_id=${this.data.temp.product_id}`)
            .then(response => {
              this.html.loader.processing = false
              if (response.data.code == 200) {
                this.data.classes = response.data.data
                this.data.temp.class_id = "-1"
              }else{
                u.showError(this.html.modal, response.data.message)
              }
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
        }
      },
      getStudents(class_id) {
        if(!class_id) class_id = this.data.temp.class_id
        if(class_id){
          this.html.loader.processing = true
          u.a().get(`/api/report_student/student_info/${this.data.report_week_id}/${class_id}?keyword=${this.data.temp.keyword}`)
            .then(response => {
              this.html.loader.processing = false
              this.data.students = response.data.data
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
        }
      },
      updateReportStudent(student,demo=0,is_lock=0){
        u.p(`/api/report_student/update_data?demo=${demo}&action_is_lock=${is_lock}`,student).then(response => {
              if(is_lock){
                alert("Cập nhật thành công")
                this.getStudents(0)
              }
            }).catch(e => {})
      },
      lockStudent(){
        if(this.data.temp.class_id){
          let text = "Bạn chắc chắn muốn chốt tất cả học sinh trong lớp.";
          if (confirm(text) == true) {
            u.g(`/api/report_student/lock_class/${this.data.report_week_id}/${this.data.temp.class_id}`).then(response => {
              this.getStudents(0)
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
          }
        }
      },
      loadReportWeek(){
        this.data.temp.teacher_id = -1
        u.a().get(`/api/report_student/get_report_weeks?product_id=${this.data.temp.product_id}`)
            .then(response => {
              this.data.report_weeks = response.data.data
              this.data.report_week_id = this.data.report_weeks[0].id
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
      }
    },
    computed: {
      disableTeacher: function () {
        return !(this.data.branch.id > 0)
      },
      disableClass: function () {
        return !(parseInt(this.data.temp.teacher_id) > 0)
      },
      disableOthers: function () {
        return !(parseInt(this.data.temp.class_id) > 0)
      },
    },
    watch: {
      "data.branch.id": function (newValue) {
        if(newValue && !isNaN(newValue)){
          this.getAllInfo(newValue)
        }
      },
      "data.temp.teacher_id": function (newValue) {
        this.data.temp.class_id = "-1"
        if(newValue && !isNaN(newValue) && parseInt(newValue) > -1){
          this.getAllClass(newValue)
        }else{
          this.data.classes = []
        }
      },
      "data.temp.class_id": function (newValue) {
        this.data.temp.reserve_date = ""
        this.data.reserve_date = ""
        if(newValue && !isNaN(newValue) && parseInt(newValue) > -1){
          this.getStudents(newValue)
        }else{
          this.data.students = []
        }
      },
    },
    filters:{
      filterStatus(report_type){
        let txt =''
        switch (parseInt(report_type)) {
          case 1:
            txt = "Giỏi";
            break;
          case 2:
            txt = "Khá";
            break;
          case 3:
            txt = "Trung bình";
            break;
          case 4:
            txt = "Yếu";
          case 5:
            txt = "Xuất sắc";
          case 6:
            txt = "Trung bình khá";
            break;
        }
        return txt;
      }
    }
  }
</script>

