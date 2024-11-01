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
                                Chương trình
                              </label>
                              <br>
                              <select class="selection program form-control" v-model="data.temp.product_id" @change="getAllClass(data.temp.teacher_id)" >
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
                      <th v-for="(n,index) in data.max_input" >Điểm lần {{n}}</th>
                      <th>Điểm gần nhất</th>
                      <th>Xếp loại</th>
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
                        <td v-for="(n,index) in data.max_input">
                          <div v-for="(data_score, index1) in student.list_data_score" v-if="index1==n-1">
                            <div class="box_report_st">
                              <select v-model="data_score.type_score" class="report_st_select" :disabled="data_score.is_lock==1"  @change="updateReportStudent(student,n)">
                                <option value="1">Nhập điểm</option>
                                <option value="2">K</option>
                              </select>
                              <input v-model="data_score.score" v-show="data_score.type_score==1" :disabled="data_score.is_lock==1" class="report_st_input" type="number" max="100" @change="updateReportStudent(student,n,0,data_score.score)" />
                            </div>
                            <hr style="margin:5px">
                            <div>
                              <textarea class="report_st_textarea" v-model="data_score.nhan_xet" :disabled="data_score.is_lock==1" placeholder="Nhận xét của GV" @change="updateReportStudent(student,n)"></textarea>
                              <textarea class="report_st_textarea" v-model="data_score.de_xuat" :disabled="data_score.is_lock==1" placeholder="Đề xuất của GV" @change="updateReportStudent(student,n)"></textarea>
                            </div> 
                          </div>
                        </td>
                        <td>{{ student.last_score }}</td>
                        <td>{{ student.xep_loai }}</td>
                        <td>
                          <button class="apax-btn full detail" @click="updateReportStudent(student,0,1)" v-if="student.is_lock!=1">Chốt</button></td>
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
          max_input:0,
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
          u.a().get(`/api/report_student/student_info_by_class/${class_id}`)
            .then(response => {
              this.html.loader.processing = false
              this.data.students = response.data.data.students
              this.data.max_input = response.data.data.max_input
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
        }
      },
      updateReportStudent(student,n, is_lock=0,score=0){
        if(score>100 ||score <0){
          alert('Nhập điểm không hợp lệ');
          this.getStudents(0)
        }else{
          let data = {
              student_info: student,
              stt: n,
              is_lock:is_lock
          }
          u.p(`/api/report_student/update_data_score`,data).then(response => {
                this.getStudents(0)
              }).catch(e => {})
        }
      },
      lockStudent(){
        if(this.data.temp.class_id){
          let text = "Bạn chắc chắn muốn chốt tất cả học sinh trong lớp.";
          if (confirm(text) == true) {
            u.g(`/api/report_student/lock_class_new/${this.data.temp.class_id}`).then(response => {
              this.getStudents(0)
            })
            .catch(e => {
              this.html.loader.processing = false
              u.showError(this.html.modal, "Có lỗi xảy ra trong quá trình lấy thông tin lớp học. Vui lòng thử lại sau!")
            })
          }
        }
      },
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
<style scoped>
.report_st_select{
  width: 80px;
  float: left;
  height: 25px;
}
.box_report_st{
  width: 160px;
  text-align: center;
  overflow: hidden;
}
.report_st_input{
    width: 80px;
    float: left;
    height: 25px;
}
.report_st_textarea{
  width: 100%;
}
</style>

