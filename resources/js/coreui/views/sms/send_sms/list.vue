<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <loader
            :active="html.loader.processing"
            text="Đang xử lý..."
            :duration="html.loader.duration"
          />
          <div slot="header">
            <i class="fa fa-id-card"></i>
            <b class="uppercase">Gửi tin nhắn Sms </b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-4 form-group">
                        <label class="control-label">Loại đối tượng</label>
                        <select class="form-control" v-model="data.from.type">
                          <option value="">Chọn loại đối tượng</option>
                          <option value="1">Full Fee Active</option>
                          <option value="2">Checkin</option>
                        </select>
                      </div>
                      <div class="col-4 form-group">
                        <label class="control-label">Trung tâm</label>
                        <branch
                          label="name"
                          :filterable="true"
                          :options="data.from.branches"
                          v-model="data.from.branch_info"
                          @input="data.from.action_from_branch"
                          placeholder="Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước"
                        />
                      </div>
                      <div class="col-md-4" v-if="data.from.type==1">
                        <div class="form-group">
                          <label class="filter-label control-label">
                            Kỳ học
                          </label>
                          <br />
                          <select
                            class="selection product form-control"
                            :disabled="html.disable.from.semester"
                            v-model="data.from.semester_id"
                            @change="loadFromProgram"
                          >
                            <option value="" disabled>Chọn kỳ học</option>
                            <option
                              :value="semester.id"
                              v-for="(semester, idx) in data.from.semesters"
                              :key="idx"
                            >
                              {{ semester.name }}
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4"  v-if="data.from.type==1">
                        <div class="form-group">
                          <label class="filter-label control-label">
                            Chương trình học
                          </label>
                          <br />
                          <select
                            class="selection program form-control"
                            :disabled="html.disable.from.program"
                            v-model="data.from.program_id"
                            @change="loadFromClass"
                          >
                            <option value="" disabled>
                              Chọn chương trình học
                            </option>
                            <option
                              :value="program.id"
                              v-for="(program, idx) in data.from.programs"
                              :key="idx"
                            >
                              {{ program.name }}
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4"  v-if="data.from.type==1">
                        <div class="form-group">
                          <label class="filter-label control-label">
                            Lớp
                          </label>
                          <br />
                          <select
                            class="selection program form-control"
                            :disabled="html.disable.from.class"
                            v-model="data.from.class_id"
                          >
                            <option value="" disabled>Chọn lớp</option>
                            <option
                              :value="cls.id"
                              v-for="(cls, idx) in data.from.classes"
                              :key="idx"
                            >
                              {{ cls.cls_name }}
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-4" v-if="data.from.type==2">
                        <div class="form-group">
                        <label class="control-label">Thời gian checkin</label>
                        <br />
                        <datePicker
                          style="width:100%;"
                          v-model="data.from.dateRangeCheckin"
                          :clearable="true"
                          range
                          format="YYYY-MM-DD"
                          id="apax-date-range"
                          placeholder="Chọn thời gian tìm kiếm từ ngày đến ngày"
                          ></datePicker>
                        </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="col-sm-12 text-center">
                        <button class="apax-btn full detail" @click="loadStudent">
                          <i class="fa fa-search"></i> Tìm kiếm
                        </button>
						<button class="apax-btn full edit" @click="showModal()">
							<i class="fa fa-paper-plane"></i> Gửi SMS
						</button> 
                      </div>
                    </div>
                    <div class="table-responsive scrollable">
						<p>Tìm thấy <b>{{total_data}}</b> học sinh</p>
                      <table class="table table-bordered apax-table">
                        <thead>
                          <tr>
                            <th width="40">STT</th>
                            <th width="40">
                              <input
                                type="checkbox"
                                :disabled="!data.students.length"
                                v-model="data.temp.check_all"
                              />
                            </th>
                            <th width="70">Mã CMS</th>
                            <th width="90">Mã kế toán</th>
                            <th width="92">Tên học sinh</th>
                            <th width="98">Tên phụ huynh</th>
                            <th width="98">Số điện thoại PH</th>
                            <th width="190">Email PH</th>
                          </tr>
                        </thead>
                        <tbody>
                          <template v-if="data.students.length">
                            <tr
                              v-for="(student, index) in data.students"
                            >
                              <td>{{ index + 1 }}</td>
                              <td>
                                <input
                                  type="checkbox"
                                  v-model="student.checked"
                                />
                              </td>
                              <td>{{ student.crm_id }}</td>
                              <td>{{ student.accounting_id }}</td>
                              <td>{{ student.name }}</td>
                              <td>{{ student.gud_name1 }}</td>
                              <td>{{ student.gud_mobile1 }}</td>
                              <td>{{ student.gud_email1 }}</td>
                            </tr>
                          </template>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
	<b-modal size="lg" v-model="modal.display" :title="modal.title" ok-variant="success" class="modal-success">
      <b-container fluid>
        <b-row class="mb-1">
          <b-col cols="12">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label class="control-label">Loại </label>
                  <br />
                  <select  class="form-control" v-model="modal.sms_type">
                    <option value="">Chọn loại</option>
                    <option value="1">Tin nhắn quảng cáo</option>
                    <option value="2">Tin nhắn chăm sóc khách hàng</option>
                    <option value="3">Email</option>
                  </select>
                </div>
              </div>
              <div class="col-6" v-if="modal.sms_type==1">
                <div class="form-group">
                  <label class="control-label">Nội dung tin nhắn quảng cáo</label>
                  <br />
                  <textarea  class="form-control" v-model="modal.sms_content"></textarea>
                </div>
              </div>
              <div class="col-6" v-if="modal.sms_type==2">
                <div class="form-group">
                  <label class="control-label">Mẫu tin nhắn CSKH</label>
                  <br />
                  <select class="form-control" v-model="modal.sms_template_id">
                    <option value="">Chọn mẫu tin nhắm</option>
                    <option
                              :value="temp.id"
                              v-for="(temp, idx) in data.sms_templates"
                              :key="idx"
                            >
                              {{ temp.title }}
                            </option>
                  </select>
                </div>
              </div>
            </div>
          </b-col>
        </b-row>
      </b-container>
      <div slot="modal-footer" class="w-100">
        <b-btn size="sm" class="float-right" variant="success" @click="sendSms">Thực hiện</b-btn>
        <b-btn size="sm" class="float-right" variant="primary" @click="closeModal">Đóng</b-btn>
      </div>
    </b-modal>
  </div>
</template>

<script>
import moment from "moment";
import u from "../../../utilities/utility";
import branch from "vue-select";
import datePicker from '../../../components/DatePicker'
import loader from "../../../components/Loading";

export default {
  name: "Add-Reserve",
  components: {
    datePicker,
    branch,
    loader,
  },
  data() {
    return {
      html: {
        loader: {
          processing: false,
          duration: 600,
        },
        disable: {
          from: {
            semester: true,
            program: true,
            class: true,
          },
          to: {
            semester: true,
            program: true,
            class: true,
          },
        },
      },
      data: {
        from: {
          branch_id: 0,
          branches: [],
          branch_info: "",
          action_from_branch: (branch) => this.selectFromBranch(branch),
          semesters: [],
          programs: [],
          classes: [],
          semester_id: "",
          program_id: "",
          class_id: "",
          note: "",
          transfer_date: "",
          none_before: u.convertDateToString(new Date()),
          none_after: "",
          action_transfer_date: (date) => this.selectTransferDate(date),
          type :"",
          dateRangeCheckin:"",
        },

        students: [],
        temp: {
          check_all: false,
        },
      sms_templates:[],
      },
		total_data: 0,
	  slect_cjrn_date: true,
	  modal: {
        count:0,
        is_all:0,
        display: false,
        title: 'Thông Báo',
        class: 'modal-success',
        message: '',
        sms_content:'',
        sms_type:'',
        sms_template_id:'',
	  },
	  temp:[],
    };
  },
  created() {
    this.data.from.branches = u.session().user.branches;
    u.g(`/api/enrolments/semesters`).then((response) => {
      this.data.from.semesters = response;
    });
    u.g(`/api/sms/get_all_sms_template`).then((response) => {
      this.data.sms_templates = response;
    });
  },
  methods: {
    selectFromBranch(branch) {
      if (branch) {
        this.data.from.branch_id = branch.id;
        this.html.disable.from.semester = false;
        this.html.disable.to.semester = false;
      } else {
        this.html.disable.from.semester = true;
        this.html.disable.to.semester = true;
      }
    },
    loadFromProgram() {
      u.g(
        `/api/enrolments/programes/${this.data.from.branch_id}/${this.data.from.semester_id}`
      ).then((response) => {
        this.data.from.programs = response;
        this.html.disable.from.program = false;
      });
    },
    loadFromClass() {
      u.g(
        `/api/classes/program/${this.data.from.program_id}/${this.data.from.branch_id}`
      ).then((response) => {
        this.data.from.classes = response;
        this.html.disable.from.class = false;
      });
    },
    loadStudent() {
      const startDateCheckin = this.data.from.dateRangeCheckin!='' && this.data.from.dateRangeCheckin[0] ?`${u.dateToString(this.data.from.dateRangeCheckin[0])}`:''
      const endDateCheckin = this.data.from.dateRangeCheckin!='' && this.data.from.dateRangeCheckin[1] ?`${u.dateToString(this.data.from.dateRangeCheckin[1])}`:''
      let params = {
        type: this.data.from.type,
        branch_id: this.data.from.branch_id,
        semester_id: this.data.from.semester_id,
        program_id: this.data.from.program_id,
        class_id: this.data.from.class_id,
        startDateCheckin: startDateCheckin,
        endDateCheckin: endDateCheckin,
      }
      this.html.loader.processing = true;
      u.p(`/api/sms/get-student`,params)
        .then((resp) => {
          this.html.loader.processing = false;
		  this.total_data = resp.total;
		  this.data.students = resp.list;
        })
        .catch((e) => {
          this.html.loader.processing = false;
          alert("Có lỗi xảy ra. Vui lòng thử lại!");
        });
    },
    checkAllStudent(students) {
      for (let i in students) {
        if (!students[i].checked) {
          students[i].checked = true;
        }
      }
    },
    uncheckAllStudent(students) {
      for (let i in students) {
        if ( students[i].checked) {
          students[i].checked = false;
        }
      }
    },
    showModal() {
    this.modal.sms_content=''
    this.modal.sms_type=''
    this.modal.sms_template_id=''
		const selected_list = [];
		this.data.students.forEach(st => {
			if(st.checked == true){
				selected_list.push(st);
			}
		});
		this.temp = selected_list;
      if(this.temp.length){
        if(this.temp.length == this.data.students.length && this.data.temp.check_all){
          this.modal.is_all = 1
          this.modal.title = "Gửi sms cho "+this.total_data+" học sinh";
          this.modal.count = this.total_data
        }else{
          this.modal.is_all = 0
          this.modal.title = "Gửi sms cho "+this.temp.length+" học sinh";
          this.modal.count = this.temp.length
        }
        this.modal.display = true
      }else{
        alert('Chọn học sinh để gửi sms');
      }
    },
    closeModal(){
      this.modal.display = false
	},
	sendSms(){
    if(!this.modal.sms_type){
      alert('Vui lòng chọn loại');
      return false;
    }
    if(this.modal.sms_type==1 && !this.modal.sms_content){
      alert('Vui lòng nhập  nội dung  tin nhắn quảng cáo');
      return false;
    }
    if(this.modal.sms_type==2 && !this.modal.sms_template_id){
      alert('Vui lòng chọn mẫu tin nhắn CSKH');
      return false;
    }
    if(this.modal.sms_type==3){
      return false;
    }
    this.closeModal();
    this.html.loader.processing = true;
    const startDateCheckin = this.data.from.dateRangeCheckin!='' && this.data.from.dateRangeCheckin[0] ?`${u.dateToString(this.data.from.dateRangeCheckin[0])}`:''
    const endDateCheckin = this.data.from.dateRangeCheckin!='' && this.data.from.dateRangeCheckin[1] ?`${u.dateToString(this.data.from.dateRangeCheckin[1])}`:''
      
    let params_search = {
			type: this.data.from.type,
			branch_id: this.data.from.branch_id,
			semester_id: this.data.from.semester_id,
			program_id: this.data.from.program_id,
      class_id: this.data.from.class_id,
      startDateCheckin: startDateCheckin,
      endDateCheckin: endDateCheckin,
		}
    let params = {
			modal: this.modal,
			params_search: params_search,
			students: this.data.students
		}
     u.p(`/api/sms/send`,params)
        .then((resp) => {
          this.html.loader.processing = false;
          alert("Gửi tin nhắn thành công!");
        })
        .catch((e) => {
          this.html.loader.processing = false;
          alert("Có lỗi xảy ra. Vui lòng thử lại!");
        });
	}
  },

  watch: {
    "data.temp.check_all": function (newValue) {
      if (newValue) {
        this.checkAllStudent(this.data.students);
      } else {
        this.uncheckAllStudent(this.data.students);
      }
    },
  },
};
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
