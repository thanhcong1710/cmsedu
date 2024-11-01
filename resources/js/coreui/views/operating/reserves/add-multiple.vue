<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <loader :active="html.loader.processing" :spin="html.loader.spin" :text="html.loader.text" :duration="html.loader.duration" />
          <div slot="header">
            <i class="fa fa-id-card"></i>
            <b class="uppercase">Bảo lưu cho lớp</b>
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
                              :options="data.cache.branches"
                              :disabled="data.cache.branches.length <= 1"
                              :placeholder="html.search_branch.placeholder"
                              v-model="data.branch"
                            ></SearchBranch>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Kỳ học
                                <strong class="text-danger h6">*</strong>
                              </label>
                              <br>
                              <select class="selection product form-control"
                                      :disabled="disableSemester"
                                      v-model="data.temp.semester_id"
                              >
                                <option value="-1" disabled>Chọn kỳ học</option>
                                <option :value="semester.id" v-for="(semester, idx) in data.semesters" :key="idx">
                                  {{ semester.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Chương trình học
                                <strong class="text-danger h6">*</strong>
                              </label>
                              <br>
                              <select class="selection program form-control"
                                      :disabled="disableProgram"
                                      v-model="data.temp.program_id"
                              >
                                <option value="-1" disabled>Chọn chương trình học</option>
                                <option :value="program.id" v-for="(program, idx) in data.programs" :key="idx">
                                  {{ program.name }}
                                </option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-8 pad-no">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="filter-label control-label">
                                Lớp
                                <strong class="text-danger h6">*</strong>
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
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">
                                Số buổi bảo lưu <strong class="text-danger h6">*</strong>
                              </label>
                              <input class="form-control" v-model="data.temp.sessions" :readonly="disableOthers">
                            </div>
                          </div>
<!--                          <div class="col-md-6">-->
<!--                            <div class="form-group">-->
<!--                              <label class="control-label">-->
<!--                                Ngày thực hiện bảo lưu-->
<!--                                <strong class="text-danger h6">*</strong>-->
<!--                              </label>-->
<!--                              <datePicker-->
<!--                                class="form-control calendar"-->
<!--                                v-model="data.create_reserve_date"-->
<!--                                :placeholder="html.calendar.options.placeholderSelectDate"-->
<!--                                :disabled="disableCreateReserveDate"-->
<!--                                :not-after="data.temp.max_date"-->
<!--                                lang="lang"-->
<!--                              ></datePicker>-->
<!--                            </div>-->
<!--                          </div>-->
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">
                                Ngày bắt đầu bảo lưu
                                <strong class="text-danger h6">*</strong>
                              </label>
                              <datePicker
                                id="start_reserve_date"
                                class="form-control calendar"
                                v-model="data.reserve_date"
                                :placeholder="html.calendar.options.placeholderSelectDate"
                                :disabled="disableOthers"
                                :not-before="data.temp.create_reserve_date"
                                :not-after="data.temp.max_date"
                                lang="lang"
                              ></datePicker>
                            </div>
                          </div>
                          <!-- <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">
                                Giữ chỗ trong lớp<strong class="text-danger h6">*</strong>
                              </label>
                              <br>
                              <input
                                v-model="data.temp.is_reserved"
                                type="checkbox"
                                :disabled="disableOthers"
                              >
                            </div>
                          </div> -->
                        </div>
                      </div>
                      <div class="col-md-4 pad-no">
                        <div class="form-group">
                          <label class="control-label">
                            Lý do bảo lưu
                            <strong class="text-danger h6">*</strong>
                          </label>
                          <textarea class="form-control" rows="5" :readonly="disableOthers" v-model="data.temp.note"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="col-sm-12 text-center">
                      <button @click="confirm" class="apax-btn full edit"><i class="fa fa-save"></i> Lưu</button>
                      <button @click="exitAddReserve" class="apax-btn full"><i class="fa fa-sign-out"></i> Thoát</button>
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
                      <th width="70">Mã LMS</th>
                      <th width="90">Mã EFFECT</th>
                      <th width="92">Tên học sinh</th>
                      <th width="98">Tên tiếng Anh</th>
                      <th width="190">Thông tin gói phí</th>
                      <th width="200">Thông tin bảo lưu</th>
                      <th>Lý do không được bảo lưu</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="data.students.length">
                      <tr v-for="(student, index) in data.students" :class="!student.is_valid ? `inactive` : ``">
                        <td>{{ index + 1 }}</td>
                        <td>
                          <input type="checkbox" :readonly="!student.is_valid" v-model="student.checked">
                        </td>
                        <td>{{ student.lms_id }}</td>
                        <td>{{ student.accounting_id }}</td>
                        <td>{{ student.student_name }}</td>
                        <td>{{ student.nick }}</td>
                        <td class="text-left">
                          <p><strong>Số phí đã đóng:</strong> {{ student.total_charged | formatMoney }}</p>
                          <p><strong>Tổng số buổi học:</strong> {{ student.summary_sessions }}</p>
                          <p><strong>Số phí bảo lưu:</strong> {{ student.amount_left | formatMoney }}</p>
                          <p><strong>Số buổi chưa học xin bảo lưu:</strong> {{ student.number_of_session_left }}</p>
                        </td>
                        <td class="text-left">
                          <p><strong>Ngày bắt đầu bảo lưu:</strong> {{ data.temp.reserve_date }}</p>
                          <p><strong>Ngày kết thúc bảo lưu:</strong> {{ student.reserve_end_date }}</p>
                          <p><strong>Ngày bắt đầu:</strong> {{ student.start_date }}</p>
                          <p><strong>Ngày kết thúc:</strong> {{ student.new_end_date ? student.new_end_date : student.end_date }}</p>
                        </td>
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
        <b-modal
          title="THÔNG BÁO"
          class="modal-primary"
          size="sm"
          v-model="html.modal.show"
          @ok="callback"
          ok-variant="primary"
          ok-only
          :no-close-on-backdrop="true"
          :no-close-on-esc="true"
          :hide-header-close="true"
        >
          <div v-html="html.modal.message"></div>
        </b-modal>
        <b-modal
          title="THÔNG BÁO"
          class="modal-primary"
          size="sm"
          v-model="html.confirm.show"
          @ok="addReserve"
          ok-variant="primary"
        >
          <div v-html="html.confirm.message"></div>
        </b-modal>
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
          calendar: {
            is_disabled: true,
            options: {
              formatSelectDate: "YYYY-MM-DD",
              disabledDaysOfWeek: [],
              clearSelectedDate: true,
              placeholderSelectDate: "Chọn ngày bắt đầu"
            }
          },
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
          form_fields: {
            start_reserve_date: {
              readonly: true
            },
            is_reserved: {
              disabled: true
            },
            number_of_sessions: {
              readonly: true,
              suggest: {
                display: "hide"
              }
            },
            note: {
              readonly: true
            },
            attached_file: {
              name: "File đính kèm",
              icon: "fa-upload"
            },
            semester: {
              disabled: true
            },
            program: {
              disabled: true
            },
            cls: {
              disabled: true
            },
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
          branch: {
            id: 0
          },
          semesters: [],
          programs: [],
          classes: [],
          students: [],
          cls: {
            id: 0,
            class_end_date: "",
            shift_id: 0,
            class_days: []
          },
          create_reserve_date: "",
          reserve_date: "",
          temp: {
            branch_id: 0,
            semester_id: "-1",
            product_id: "-1",
            program_id: "-1",
            class_id: "-1",
            create_reserve_date: "",
            reserve_date: "",
            sessions: 1,
            note: "",
            is_reserved: false,
            max_date: u.convertDateToString(new Date()),
            check_all: false
          },
          cache: {
            branches: []
          }
        },
        cache: {},
        flags: {
          success: false
        },
        request: {
          searchStudent: {
            cancelToken: null
          }
        }
      }
    },
    mounted() {

    },
    created() {
      this.data.cache.branches = u.session().user.branches
    },
    methods: {
      callback() {
        if(this.flags.success){
          this.data.reserve_date = ""
          this.data.temp.note = ""
          this.getStudents(this.data.temp.class_id)
        }
      },
      getHolidays() {

      },
      addReserve() {
        let validate = this.finalValidate()

        if(validate.valid){
          this.html.loader.processing = true
          this.flags.success = false
          const data = this.processData(this.data.students)
          u.a().post(`/api/reserves/multiple`,{reserves: data})
            .then(resp => {
              this.html.loader.processing = false
              if(resp.data.code == 200){
                this.flags.success = true
                u.showSuccess(this.html.modal, "Bảo lưu cho lớp thành công!")
                this.exitAddReserve()
              }else{
                this.flags.success = true
                u.showError(this.html.modal, resp.data.message)
              }
            }).catch(e => {
            this.html.loader.processing = false
            this.flags.success = true
            u.showError(this.html.modal, "Có lỗi xảy ra. Vui lòng thử lại sau!")
          })
        }else{
          u.showModal(this.html.modal, validate.message)
        }
      },
      exitAddReserve() {
        this.$router.push("/reserves")
      },
      showMessage(message) {
        this.html.modal.message = message
        this.html.modal.show = true
      },
      confirm() {
        u.showModal(this.html.confirm, "Bạn có chắc chắn thực hiện thao tác này?")
      },
      getAllInfo(branch_id) {
        if(branch_id){
          this.html.loader.processing = true
          // this.cache = {}
          this.cancelSearchStudent();
          u.a().get(`/api/reserves/info/${branch_id}`)
            .then(response => {
              this.html.loader.processing = false
              if (response.data.code == 200) {
                this.cache = response.data.data
                this.data.temp.semester_id = "-1"
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
      cancelSearchStudent(){
        if (this.request.searchStudent.cancelToken) {
          this.request.searchStudent.cancelToken.cancel();
        }
      },
      getStudents(class_id){
        this.html.loader.processing = true
        const url = `/api/reserves/students/${class_id}`

        this.cancelSearchStudent();
       
        u.a().get(url).then(resp => {
          this.html.loader.processing = false
          if(resp.data.code == 200){
            this.initStudents(resp.data.data.students)
            this.data.cls.class_end_date = resp.data.data.class.class_end_date
            this.data.cls.shift_id = resp.data.data.class.shift_id
          }else{
            u.showError(this.html.modal, resp.data.message)
          }
        }).catch(e => {
          this.html.loader.processing = false
          u.showError(this.html.modal, "Có lỗi xảy ra. Vui lòng thử lại!")
        })
      },
      initStudents(students){
        let resp = {}
        for(let i in students) {
          students[i].min_date = this.setMinDate(students[i])

          resp = this.firstValidate(students[i])
          if(resp.valid){
            resp = this.thirdValidate(students[i], this.data.temp.is_reserved)
          }
          students[i].is_valid = resp.valid
          students[i].reason = resp.message
          students[i].error = resp.error
          if(!resp.valid){
            students[i].checked = false
          }
        }
        this.data.students = students
      },
      setMinDate(student){
        const reserved_dates = student.reserved_dates
        let min_date = ""
        for(let i in reserved_dates){
          min_date = min_date ? (u.isGreaterThan(reserved_dates[i].end_date,min_date)
                                ? reserved_dates[i].end_date
                                : min_date)
                              : reserved_dates[i].end_date
        }

        return min_date
      },
      firstValidate(student){
        let is_valid = false
        let reason = ""
        let error = 0

        if(student.waiting_status){
          is_valid = false
          reason = this.getWaitingStatus(student.waiting_status)
          error = 1
        }else if(student.debt_amount){
          is_valid = false
          reason = "Học sinh chưa hoàn thành học phí"
          error = 2
        }else if(!student.summary_sessions) {
          is_valid = false
          reason = "Số buổi học bằng 0"
          error = 3
        }else{
          is_valid = true
        }

        return {
          valid: is_valid,
          message: reason,
          error: error
        }
      },
      secondValidate(student){
        let is_valid = true
        let reason = ""
        let error = 0

        if(u.isGreaterThan(this.data.temp.reserve_date, student.end_date)){
          is_valid = false
          reason = "Quá ngày học cuối"
          error = 4
        }else if (u.isGreaterThan(student.start_date, this.data.temp.reserve_date)) {
          is_valid = false
          reason = "Chưa đến ngày bắt đầu học"
          error = 4
        }else if(student.min_date && !u.isGreaterThan(this.data.temp.reserve_date, student.min_date)){
          is_valid = false
          reason = `Học sinh chỉ có thể bảo lưu sau ngày ${student.min_date}`
          error = 4
        }else{
          if(this.data.temp.reserve_date && student.reserve_end_date){
            for(let i in student.reserved_dates){
              if(
                !(
                  moment(this.data.temp.reserve_date) > moment(student.reserved_dates[i].end_date) ||
                  moment(student.reserve_end_date) < moment(student.reserved_dates[i].start_date)
                )
              ){
                is_valid = false
                reason = `Đã bảo lưu từ ngày ${student.reserved_dates[i].start_date} đến ngày ${student.reserved_dates[i].end_date}`
                error = 4
                break
              }
            }
          }
        }

        return {
          valid: is_valid,
          message: reason,
          error: error
        }
      },
      thirdValidate(student, is_reserved){
        let is_valid = true
        let reason = ""
        let error = 0

        if(!is_reserved) {
          if([8,85,86].indexOf(student.contract_type) > -1){
            is_valid = false
            reason = "Không thể bảo lưu không giữ chỗ cho học sinh VIP/học bổng"
            error = 5
          }else if(student.action){
            if(student.action.search("withdraw_fees") > -1){
              is_valid = false
              reason = "Đã rút phí"
              error = 5
            }else if(student.action.search("tuition_transfer") > -1){
              is_valid = false
              reason = "Đã chuyển phí"
              error = 5
            }
          }
        }

        return {
          valid: is_valid,
          message: reason,
          error: error
        }
      },
      finalValidate(){
        let resp = {
          valid: true,
          message: ""
        }

        if (parseInt(this.data.temp.sessions) <= 0 || !this.data.temp.sessions || isNaN(this.data.temp.sessions)) {
          resp.valid = false
          resp.message += u.genErrorHtml("Số buổi bảo lưu không hợp lệ")
        }

        if (
          !this.data.temp.reserve_date ||
          !u.isValidDate(this.data.temp.reserve_date)
        ) {
          resp.valid = false
          resp.message += u.genErrorHtml("Ngày bắt đầu bảo lưu không hợp lệ")
        }

        if(!this.data.temp.note.trim()) {
          resp.valid = false
          resp.message += u.genErrorHtml("Lý do bảo lưu không được để trống")
        }

        return resp
      },
      getWaitingStatus(status){
        switch (status) {
          case 1:
            return "Học sinh đang chờ duyệt chuyển phí"
          case 2:
            return "Học sinh đang chờ duyệt nhận phí"
          case 3:
            return "Học sinh đang chờ duyệt chuyển trung tâm"
          case 4:
            return "Học sinh đang chờ duyệt chuyển bảo lưu"
          case 5:
            return "Học sinh đang chờ duyệt pending"
          case 6:
            return "Học sinh đang chờ duyệt rút phí"
          case 7:
            return "Học sinh đang chờ duyệt chuyển lớp"
          default:
            return ""
        }
      },
      calculate(student){
        if(student.is_valid){
          const holidays = this.cache.holidays[this.data.temp.product_id].concat(student.reserved_dates)
          const class_days = u.processClassDays(student.enrolment_schedule)

          let passed_session = 0
          if (u.isGreaterThan(this.data.temp.reserve_date, student.start_date)) {
            passed_session = u.calSessions(
              student.start_date,
              u.pre(this.data.temp.reserve_date),
              holidays,
              class_days
            ).total
          }

          student.number_of_session_left = student.summary_sessions - passed_session
          student.number_of_real_session_left = (student.real_sessions > passed_session)
                                              ? (student.real_sessions - passed_session)
                                              : 0
          student.sessions_from_start_to_reserve_date = passed_session
          student.amount_left = student.number_of_real_session_left
                                ? Math.ceil((student.number_of_real_session_left * student.total_charged)/student.real_sessions)
                                : 0
          student.amount_from_start_to_reserve_date = student.total_charged - student.amount_left
          student.reserve_end_date = u.calEndDate(
                                      this.data.temp.sessions,
                                      class_days,
                                      holidays,
                                      this.data.temp.reserve_date
                                    ).end_date

          const new_holidays = holidays.concat({
            start_date: this.data.temp.reserve_date,
            end_date: student.reserve_end_date
          })

          if (passed_session == 0) {
            student.old_enrol_end_date = student.start_date
          } else {
            student.old_enrol_end_date = u.calEndDate(
              passed_session,
              class_days,
              new_holidays,
              student.start_date
            ).end_date
          }

          student.new_start_date = moment(student.reserve_end_date, "YYYY-MM-DD").add(1, 'days').format('YYYY-MM-DD')

          student.new_end_date = u.calEndDate(this.data.temp.sessions + 1, class_days, holidays, student.end_date).end_date
        }
      },
      getProductId(semester_id){
        for(let i in this.data.semesters){
          if(this.data.semesters[i].id == semester_id){
            return this.data.semesters[i].product_id
          }
        }
        return 0
      },
      processData(students){
        let data = []
        for(let i in students) {
          if (students[i].checked && students[i].is_valid) {
            data.push({
              student_id: students[i].student_id,
              contract_id: students[i].contract_id,
              note: this.data.temp.note,
              start_date: this.data.temp.reserve_date,
              end_date: students[i].reserve_end_date,
              sessions: this.data.temp.sessions,
              branch_id: this.data.branch.id,
              product_id: this.data.temp.product_id,
              program_id: this.data.temp.program_id,
              class_id: this.data.temp.class_id,
              is_reserved: this.data.temp.is_reserved ? 1 : 0,
              new_end_date: students[i].new_end_date,
              reserve_type: 3,
              attached_file: "",
              version: 2,
              is_extended: 0,
              meta_data: {
                total_session: students[i].summary_sessions,
                total_real_sessions: students[i].real_sessions,
                total_fee: students[i].total_charged,
                session_left: students[i].number_of_session_left,
                amount_left: students[i].amount_left,
                start_date: students[i].start_date,
                end_date: students[i].new_end_date,
                before_reserve_end_date: students[i].end_date,
                number_of_sessions_reserved: students[i].number_of_session_left,
                number_of_real_sessions_reserved: students[i].number_of_real_session_left,
                amount_reserved: students[i].amount_left,
                sessions_from_start_to_reserve_date: students[i].sessions_from_start_to_reserve_date,
                amount_from_start_to_reserve_date: students[i].amount_from_start_to_reserve_date,
                new_start_date: students[i].new_start_date,
                old_enrol_end_date: students[i].old_enrol_end_date,
                special_reserved_sessions: this.data.temp.sessions,
                tuition_fee_id: students[i].tuition_fee_id,
                schedules: students[i].enrolment_schedule,
                shift_id: students[i].shift_id
              }
            })
          }
        }
        return data
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
      }
    },
    computed: {
      disableSemester: function () {
        return !(this.data.branch.id > 0)
      },
      disableProgram: function () {
        return !(parseInt(this.data.temp.semester_id) > 0)
      },
      disableClass: function () {
        return !(parseInt(this.data.temp.program_id) > 0)
      },
      disableOthers: function () {
        return !(parseInt(this.data.temp.class_id) > 0)
      },
      disableStudent: function (student) {

      }
    },
    watch: {
      "data.branch.id": function (newValue) {
        if(newValue && !isNaN(newValue)){
          this.getAllInfo(newValue)
        }
      },
      "cache": function (newValue) {
        this.data.semesters = newValue.semesters
        this.data.temp.semester_id = "-1"
      },
      "data.temp.semester_id": function (newValue) {
        this.data.temp.program_id = "-1"
        if(newValue && !isNaN(newValue) && parseInt(newValue) > -1){
          this.data.temp.product_id = this.getProductId(newValue)
          this.data.programs = this.cache.programs[`semester${newValue}`]
        }else{
          this.data.programs = []
        }
      },
      "data.temp.program_id": function (newValue) {
        this.data.temp.class_id = "-1"
        if(newValue && !isNaN(newValue) && parseInt(newValue) > -1){
          this.data.classes = this.cache.classes[`program${newValue}`]
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
      "data.reserve_date": function (newValue) {
        this.data.temp.reserve_date = u.utcToLocal(newValue)
      },
      "data.temp.reserve_date": function (newValue) {
        let valid = {}
        for(let i in this.data.students){
          if(this.data.students[i].is_valid || this.data.students[i].error === 4){
            valid = this.secondValidate(this.data.students[i])
            this.data.students[i].is_valid = valid.valid
            this.data.students[i].reason = valid.message
            this.data.students[i].error = valid.error
            if(!valid.valid){
              this.data.students[i].checked = false
            }
            this.calculate(this.data.students[i])
          }
        }
      },
      "data.temp.sessions": function (newValue) {
        if(!isNaN(newValue)){
          if(u.isNumber(newValue)){
            if(u.isValidDate(this.data.temp.reserve_date)){
              for(let i in this.data.students){
                if(this.data.students[i].is_valid){
                  this.calculate(this.data.students[i])
                }
              }
            }
          }else{
            this.data.temp.sessions = parseInt(newValue)
          }
        }else{
          this.data.temp.sessions = 1
        }
      },
      "data.temp.check_all": function (newValue) {
        if(newValue){
          this.checkAllStudent(this.data.students)
        }else{
          this.uncheckAllStudent(this.data.students)
        }
      },
      "data.temp.is_reserved": function (newValue) {
        let valid = {}
        for(let i in this.data.students){
          if(this.data.students[i].is_valid || this.data.students[i].error == 5){
            valid = this.thirdValidate(this.data.students[i], newValue)
            this.data.students[i].is_valid = valid.valid
            this.data.students[i].error = valid.error
            this.data.students[i].reason = valid.message
          }
        }
      }
    }
  }
</script>

<style scoped lang="scss">
  .hide {
    display: none;
  }

  .show {
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
