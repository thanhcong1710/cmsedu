<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <loader :active="html.loader.processing" text="Đang xử lý..." :duration="html.loader.duration" />
          <div slot="header">
            <i class="fa fa-id-card"></i>
            <b class="uppercase">Chuyển Lớp Cho Cả Lớp </b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-12"><address><h6 class="text-main first-upper">Thông tin lớp chuyển đi</h6></address></div>
                          <div class="col-12 form-group">
                            <label class="control-label">Trung tâm</label>
                            <branch                                                        
                                label="name"
                                :filterable=true
                                :options="data.from.branches"
                                v-model="data.from.branch_info" 
                                @input="data.from.action_from_branch"
                                placeholder="Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước" 
                            />
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Kỳ học
                              </label>
                              <br>
                              <select class="selection product form-control"
                                      :disabled="html.disable.from.semester"
                                      v-model="data.from.semester_id"
                                       @change="loadFromProgram"
                              >
                                <option value="" disabled>Chọn kỳ học</option>
                                <option :value="semester.id" v-for="(semester, idx) in data.from.semesters" :key="idx">
                                  {{ semester.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Chương trình học
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="html.disable.from.program"
                                      v-model="data.from.program_id"
                                      @change="loadFromClass"
                              >
                                <option value="" disabled>Chọn chương trình học</option>
                                <option :value="program.id" v-for="(program, idx) in data.from.programs" :key="idx">
                                  {{ program.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Lớp
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="html.disable.from.class"
                                      v-model="data.from.class_id"
                                       @change="loadStudent"
                              >
                                <option value="" disabled>Chọn lớp</option>
                                <option :value="cls.id" v-for="(cls, idx) in data.from.classes" :key="idx">
                                  {{ cls.cls_name }}
                                </option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-12"><address><h6 class="text-main first-upper">Thông tin lớp chuyển đến</h6></address></div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Kỳ học
                              </label>
                              <br>
                              <select class="selection product form-control"
                                      :disabled="html.disable.to.semester"
                                      v-model="data.to.semester_id"
                                      @change="loadToProgram"
                              >
                                <option value="" disabled>Chọn kỳ học</option>
                                <option :value="semester.id" v-for="(semester, idx) in data.to.semesters" :key="idx">
                                  {{ semester.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Chương trình học
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="html.disable.to.program"
                                      v-model="data.to.program_id"
                                       @change="loadToClass"
                              >
                                <option value="" disabled>Chọn chương trình học</option>
                                <option :value="program.id" v-for="(program, idx) in data.to.programs" :key="idx">
                                  {{ program.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Lớp
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="html.disable.to.class"
                                      v-model="data.to.class_id"
                                      @change="selectToClass"
                              >
                                <option value="" disabled>Chọn lớp</option>
                                <option v-if="cls.id!=data.from.class_id" :value="cls.id" v-for="(cls, idx) in data.to.classes" :key="idx">
                                  {{ cls.cls_name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">
                                Ngày bắt đầu
                                <strong class="text-danger h6">*</strong>
                              </label>
                             <datePicker
                                  :disabled="slect_cjrn_date"
                                  id="transfer-date"
                                  class="form-control calendar"
                                  :value="data.from.transfer_date"
                                  v-model="data.from.transfer_date"
                                  placeholder="Chọn ngày chuyển trung tâm"
                                  :not-before="data.from.none_before"
                                  :not-after="data.from.none_after"
                                  @change="data.from.action_transfer_date"
                                  lang="lang"
                              />
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="control-label">
                                Lý do chuyển lớp
                              </label>
                              <textarea class="form-control" rows="5"  v-model="data.from.note"></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="col-sm-12 text-center">
                      <button @click="save" class="apax-btn full edit"><i class="fa fa-save"></i> Lưu</button>
                      <button @click="exitAddTransferClass" class="apax-btn full"><i class="fa fa-sign-out"></i> Thoát</button>
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
                      <th>
                        <input type="checkbox" :disabled="!data.students.length" v-model="data.temp.check_all">
                      </th>
                      <th width="70">Mã CMS</th>
                      <th width="90">Mã kế toán</th>
                      <th width="92">Tên học sinh</th>
                      <th width="98">Số buổi</th>
                      <th width="98">Số buổi sau chuyển</th>
                      <th width="190">Ngày bắt đầu</th>
                      <th width="200">Ngày kết thúc</th>
                      <th>Lý do không được chuyển lớp</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="data.students.length">
                      <tr v-for="(student, index) in data.students" :class="!student.is_valid ? `inactive` : ``">
                        <td>{{ index + 1 }}</td>
                        <td>
                          <input type="checkbox" :readonly="!student.is_valid" v-model="student.checked">
                        </td>
                        <td>{{ student.crm_id }}</td>
                        <td>{{ student.accounting_id }}</td>
                        <td>{{ student.name }}</td>
                        <td>{{ student.summary_sessions }}</td>
                        <td>{{ student.left_sessions }}</td>
                        <td>{{ student.enrolment_start_date }}</td>
                        <td>{{ student.enrolment_last_date }}</td>
                        <td>{{ student.reason }}</td>
                      </tr>
                    </template>
                    <template v-else>
                      <tr>
                        <td colspan="9">Không có học sinh</td>
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
  import branch from 'vue-select'
  import datePicker from "vue2-datepicker"
  import loader from '../../../components/Loading'

  export default {
    name: "Add-Reserve",
    components: {
      datePicker,
      branch,
      loader
    },
    data() {
      return {
        html:{
          loader:{
            processing: false,
            duration : 600,
          },
          disable:{
            from:{
              semester: true,
              program : true,
              class : true,
            },
            to:{
              semester: true,
              program : true,
              class : true,
            },
          }
        },
        data: {
          from:{
            branch_id:0,
            branches: [],
            branch_info:'',
            action_from_branch: branch => this.selectFromBranch(branch),
            semesters: [],
            programs: [],
            classes: [],
            semester_id: "",
            program_id: "",
            class_id: "",
            note:'',
            transfer_date:'',
            none_before: u.convertDateToString(new Date()),
            none_after:'',
            action_transfer_date: date => this.selectTransferDate(date),
          },
          to:{
            semesters: [],
            programs: [],
            classes: [],
            semester_id: "",
            program_id: "",
            class_id: "",
          },
          max_student_transfer:0,
          students: [],
          temp:{
            check_all: false
          }
        },
        slect_cjrn_date: true,
      }
    },
    created() {
      this.data.from.branches = u.session().user.branches
      u.g(`/api/enrolments/semesters`).then(response => {
          this.data.from.semesters = response
          this.data.to.semesters = response
      })
    },
    methods: {
      selectFromBranch(branch) {
        if(branch){
          this.data.from.branch_id = branch.id
          this.html.disable.from.semester =false
          this.html.disable.to.semester =false
        }else{
          this.html.disable.from.semester =true
          this.html.disable.to.semester =true
        }
      },  
      loadFromProgram(){
        u.g(`/api/enrolments/programes/${this.data.from.branch_id}/${this.data.from.semester_id}`).then(response => {
          this.data.from.programs = response
          this.html.disable.from.program = false
         })
      },
      loadToProgram(){
        u.g(`/api/enrolments/programes/${this.data.from.branch_id}/${this.data.to.semester_id}`).then(response => {
          this.data.to.programs = response
          this.html.disable.to.program = false
         })
      },
      loadFromClass(){
        u.g(`/api/classes/program/${this.data.from.program_id}/${this.data.from.branch_id}`).then(response => {
          this.data.from.classes = response
          this.html.disable.from.class = false
         })
      },
      loadToClass(){
        u.g(`/api/classes/program/${this.data.to.program_id}/${this.data.from.branch_id}`).then(response => {
          this.data.to.classes = response
          this.html.disable.to.class = false
         })
      },
      loadStudent(){
        this.html.loader.processing = true
        u.g(`/api/class-transfers/get-student/${this.data.from.class_id}`).then(resp => {
          this.html.loader.processing = false
          this.data.students = resp
        }).catch(e => {
          this.html.loader.processing = false
          alert("Có lỗi xảy ra. Vui lòng thử lại!")
        })
      },
      checkAllStudent(students){
        for(let i in students){
          if(students[i].is_valid && !students[i].checked){
            students[i].checked = true
          }
        }
      },
      uncheckAllStudent(students){
        for(let i in students){
          if(students[i].is_valid && students[i].checked){
            students[i].checked = false
          }
        }
      },
      exitAddTransferClass() {
        this.$router.push("/class-transfers")
      },
      save() {
        var count_student = 0;
        for(let i in this.data.students){
          if(this.data.students[i].is_valid && this.data.students[i].checked){
            count_student ++
          }
        }
        if(!this.data.to.class_id){
          alert("Vui lòng chọn lớp chuyển đến");
          return false;
        }
        if(!this.data.from.transfer_date){
          alert("Vui lòng chọn ngày bắt đầu");
          return false;
        }
        if(!count_student){
          alert("Vui lòng chọn học sinh chuyển lớp");
          return false;
        }
        if(count_student > this.max_student_transfer){
          alert(`Số học sinh chuyển lớn hơn số chỗ còn trống trong lớp chuyển tới (${this.max_student_transfer} chỗ trống)`);
          return false;
        }
        var r = confirm("Bạn chắc chắn muốn chuyển lớp cho các học sinh đã chọn!");
        this.html.loader.processing = true
        if (r == true) {
          u.p(`/api/class-transfers/add-multi-student`,this.data).then(resp => {
            this.html.loader.processing = false
            alert("Chuyển lớp thành công!")
            this.exitAddTransferClass()
          }).catch(e => {
            this.html.loader.processing = false
            alert("Có lỗi xảy ra. Vui lòng thử lại!")
          })
        } 
      },
      selectTransferDate(date) {
        this.data.from.transfer_date = moment(date).format('YYYY-MM-DD')
        this.html.loader.processing = true
        u.p(`/api/class-transfers/check-multi-student`,this.data).then(resp => {
            this.html.loader.processing = false
            this.data.students = resp
          }).catch(e => {
            this.html.loader.processing = false
            alert("Có lỗi xảy ra. Vui lòng thử lại!")
          })
      },
      selectToClass(){
        var class_id = this.data.to.class_id;
        var cjrn_class_date =''
        var curr_student = 0
        var max_students=16
        this.data.to.classes.map(item => {
            if(item.id == class_id){
              cjrn_class_date = item.last_cjrn_class_date
              max_students = item.max_students
              curr_student = item.curr_student
            }
        })
        this.max_student_transfer = max_students - curr_student > 0 ? max_students - curr_student:0
        this.data.from.none_after = cjrn_class_date
        this.slect_cjrn_date = false
      }
    },
    
    watch: {
      "data.temp.check_all": function (newValue) {
        if(newValue){
          this.checkAllStudent(this.data.students)
        }else{
          this.uncheckAllStudent(this.data.students)
        }
      },
    }
  }
</script>

<style scoped lang="scss">
  .hide {
    display: none;
  }
  .pass-trial {
    float: left;
    margin: 5px 5px 0 0;
  }

  .transparent {
    opacity: 0;
  }

  .apax-form textarea.form-control {
    height: unset;
    resize: none;
  }

  .apax-form .btn-upload {
    width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    text-align: left;
  }
  p {
    margin-bottom: 5px;
  }
</style>
