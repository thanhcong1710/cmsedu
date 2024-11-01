<template>
    <div class="animated fadeIn apax-form" id="class register">
        <div class="row">
            <div class="col-12">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-address-book"></i> <strong>Chuyển ALL học sinh theo lớp</strong>
                        <div class="card-actions">
                            <a href="skype:thanhcong1710?chat" target="_blank">
                                <small className="text-muted"><i class="fa fa-skype"></i></small>
                            </a>
                        </div>
                    </div>
                    <div class="content-detail">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-3" id="register-filter">
                                    <div class="col-md-12" :class="html.class.display.filter.branch">
                                        <div class="row">
                                            <div class="col-md-4 filter-label">
                                                <label class="filter-label control-label">Trung Tâm: </label>
                                            </div>
                                            <div class="col-md-8 filter-selection">
                                                <div class="row form-group suggest-branch">
                                                    <searchBranch
                                                            v-model="filter.branch"
                                                            :onSelect="selectBranch"
                                                            :options="cache.branches"
                                                            :disabled="html.disable.filter.branch"
                                                            placeholder="Vui lòng chọn một trung tâm">
                                                    </searchBranch><br/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" :class="html.class.display.filter.semester">
                                        <div class="row">
                                            <div class="col-md-4 filter-label">
                                                <label class="filter-label control-label">Kỳ Học: </label>
                                            </div>
                                            <div class="col-md-8 filter-selection">
                                                <div class="row form-group">
                                                    <select
                                                            @change="selectSemester"
                                                            v-model="filter.semester"
                                                            :disabled="html.disable.filter.semester"
                                                            class="filter-selection semester form-control"
                                                    >
                                                        <option value="">Vui lòng chọn một kỳ học</option>
                                                        <option :value="semester.id"
                                                                v-for="(semester, ind) in cache.semesters" :key="ind">
                                                            {{semester.name}}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 filter-classes" :class="html.class.display.filter.classes">
                                        <div class="row">
                                            <label class="filter-label control-label">Lớp Học: </label>
                                            <div class="row tree-view">
                                                <tree
                                                        :data="cache.classes"
                                                        text-field-name="text"
                                                        allow-batch
                                                        @item-click="selectClass"
                                                >
                                                </tree>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9" id="register-detail" >
                                    <div v-show="action.loading" class="ajax-load content-loading">
                                        <div class="load-wrapper">
                                            <div class="loader"></div>
                                            <div v-show="action.loading" class="loading-text cssload-loader">{{
                                                html.content.loading }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-base">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li :class="tab.current === 1 ? 'forActive' : ''" class="nav-item" >
                                                <a class="nav-link" data-toggle="tab" :href="`#tab-1`" role="tab" :aria-controls="`tab-1`" aria-expanded="true" aria-selected="true" @click="tab.current = 1">Danh sách học sinh</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="page-control">
                                            <div :class="tab.current === 1 ? 'active' : ''" class="tab-pane tab-content-detail" id="tab-1" role="tabpanel">
                                                <div class="class-info" :class="html.class.display.class_info">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3 text-right field-label">
                                                                Tên Lớp Học:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                {{ cache.class_info.class_name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3 text-right field-label">
                                                                Trạng Thái Hoạt Động:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                {{ cache.class_info.class_is_cancelled | classStatus }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3 text-right field-label">
                                                                Thời Gian:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                {{ cache.class_info.class_time }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3 text-right field-label">
                                                                Giáo Viên:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                <span v-html="cache.class_info.teachers_name"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3 text-right field-label">
                                                                Tổng Số Học Sinh:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                {{ cache.class_info.total_students,
                                                                cache.class_info.class_max_students | totalPerMax }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3 text-right field-label">
                                                                Thời Gian và Địa Điểm Học:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                <span v-html="cache.class_info.time_and_place"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row" >
                                                            <div class="col-md-3 text-right field-label">
                                                                CS - Giáo Viên Chủ Nhiệm:
                                                            </div>
                                                            <div class="col-md-9 field-detail">
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <select v-model="cm" id="select_cm" :disabled="disabledCM" name="select[cm]"
                                                                                class="selection cm form-control"
                                                                                @change="onChangeCm"
                                                                        >
                                                                            <option value="-1" disabled>Chọn giáo viên chủ nhiệm</option>
                                                                            <option :value="cm.user_id" v-for="(cm, i) in list.list_cm"
                                                                                    :key="i">{{ cm.full_name }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4" v-show="false">
                                                                        <div class="row">
                                                                            <!--<button class="btn btn-success" @click="classFormModal">Lưu</button>-->
                                                                            <!-- <button class="btn btn-danger" @click="resetall">Hủy</button> -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 contracts-list" :class="html.class.display.contracts_list">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div :class="html.class.loading ? 'loading' : 'standby'"
                                                                 class="ajax-loader">
                                                                <img src="/static/img/images/loading/mnl.gif">
                                                            </div>
                                                            <div class="table-responsive scrollable">
                                                                <table class="table table-striped table-bordered apax-table">
                                                                    <thead>
                                                                    <tr>
                                                                        <th><input type="checkbox" @click="selectAllClass(cache.students,$event)" v-model="allSelected"></th>
                                                                        <th>STT</th>
                                                                        <th>Tên Học Sinh</th>
                                                                        <th>Mã CMS</th>
                                                                        <th>Gói Phí</th>
                                                                        <th>Ngày Bắt Đầu Học</th>
                                                                        <th>Ngày Kết Thúc Lớp</th>
                                                                        <th>Số Buổi</th>
                                                                        <th>Loại KH</th>
                                                                        <th>Số Tiền Phải Đóng</th>
                                                                        <th>Số Tiền Đã Đóng</th>
                                                                        <th>Số Buổi Được Học</th>
                                                                        <th>Ngày Học Cuối HS</th>
                                                                        <th>Trạng Thái Học Sinh</th>
                                                                        <th>EC</th>
                                                                        <th>CS</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr :class="checkAlert(student.withdraw, student.status)" v-for="(student, ind) in cache.students" v-bind:key="ind">
                                                                        <td><input type="checkbox" @click="selectedOneStudent(student.contract_id,$event)" v-model="userIds" :value="student.contract_id"></td>
                                                                        <td align="right">{{ind + 1}}</td>
                                                                        <td>{{ student.student_name }}</td>
                                                                        <td>{{ student.cms_id }}</td>
                                                                        <td>{{ student.tuition_fee_name, student.tuition_fee_price |
                                                                            tuitionFeeLabel }}
                                                                        </td>
                                                                        <td>{{ student.start_date | formatDate }}</td>
                                                                        <td>{{ student.end_date | formatDate }}</td>
                                                                        <td>{{ student.total_sessions }}</td>
                                                                        <td>{{ student.customer_type | contractType }}</td>
                                                                        <td>{{ student.tuition_fee_price | formatMoney }}</td>
                                                                        <td>{{ student.charged_total | formatMoney }}</td>
                                                                        <td>{{ student.available_sessions }}</td>
                                                                        <td>{{ student.last_date | formatDate}}</td>
                                                                        <td>
                                                                            <div v-html="studentStatus(student.status, student.withdraw)"></div>
                                                                        </td>
                                                                        <td>{{ student.ec_name }}</td>
                                                                        <td>{{ student.cm_name }}</td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="row">
                                                                <button class="btn btn-success"  :disabled="disableSaveCm" @click="classFormModal">Chuyển lớp</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="closeModal" ok-variant="primary" ok-only>
                    <div v-html="html.modal.message">
                    </div>
                </b-modal>
                <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.confirm.show" @ok="saveclasscm" ok-variant="primary">
                    <div v-html="html.confirm.message">
                    </div>
                </b-modal>
                <b-modal ref="my-modal" title="Thông tin lớp chuyển đến" size="lg" :ok-disabled="confirm_transfer" v-model="largeModal" @ok="yourOkFn"  ok-title="Lưu" @close="modalClassClosed">
                    <div class="animated fadeIn apax-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Sản phẩm</label>
                                            <product-select
                                                    :value="1"
                                                    :multiple="false"
                                                    :onSelectProduct="selectProduct"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Chương trình</label>
                                            <v-select
                                                    placeholder="Chọn chương trình"
                                                    select-label="Enter để chọn chương trình này"
                                                    :options="options"
                                                    @input="selectedProgram"
                                                    label="name">
                                            </v-select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Lớp học</label>
                                            <c-select
                                                    placeholder="Chọn lớp học"
                                                    select-label="Enter để lớp này"
                                                    :options="class_options"
                                                    @input="selectedToClass"
                                                    label="name">
                                            </c-select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Buổi học tiếp theo của lớp đi</label>
                                            <input type="text" :value="class_form.current_day" class="form-control" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Buổi học tiếp theo của lớp đến</label>
                                            <input type="text" :value="class_form.nearest_day" class="form-control" readonly/>
                                            <input type="hidden" :value="class_form.list_student" class="form-control" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Ngày chuyển lớp</label>
                                            <class-datepicker
                                                    v-model="class_form.start_date"
                                                    placeholder="Chọn thời gian bắt đầu"
                                                    format="YYYY-MM-DD"
                                                    :change="selectedDateTransfer"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>

  import md5 from 'js-md5'
  import moment from 'moment'
  import tree from 'vue-jstree'
  import vSelect from "vue-select"
  import cSelect from "vue-select"

  import datePicker from 'vue2-datepicker'
  import u from '../../../utilities/utility'
  import search from '../../../components/Search'
  import apaxButton from '../../../components/Button'
  import paging from '../../../components/Pagination'
  import searchBranch from '../../../components/Selection'
  import productSelect from '../../base/common/product-select-new'
  import programSelect from '../../base/common/program-select-new'
  import ClassDatepicker from '../../base/common/DatePicker'

  export default {
    name: 'Register-Add',
    components: {
      tree,
      search,
      paging,
      apaxButton,
      datePicker,
      searchBranch,
      productSelect,
      programSelect,
      vSelect,
      ClassDatepicker,
      cSelect,
    },
    data() {
      return {
        last_selected_id:0,
        userIds:[],
        allSelected: false,
        class_form:{
          class_day:'',
          from_product_id:'',
          from_program_id:'',
          branch_id:0,
          product_id:0,
          program_id:0,
          nearest_day:'',
          current_day:'',
          list_student:[],
          from_class_id:0,
          to_class_id:0,
          start_date:'',
        },
        largeModal:false,
        confirm_transfer:true,
        datepickerOptions: {
          closed: true,
          value: '',
          minDate: moment().add(-15, 'days'),//this.getDate(new Date()),
          lang: {
            days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
            months: [
              'Tháng 1',
              'Tháng 2',
              'Tháng 3',
              'Tháng 4',
              'Tháng 5',
              'Tháng 6',
              'Tháng 7',
              'Tháng 8',
              'Tháng 9',
              'Tháng 10',
              'Tháng 11',
              'Tháng 12'
            ],
            pickers: ['', '', '7 ngày trước', '30 ngày trước']
          }
        },
        disableSaveCm: true,
        hideSaveCm: 'hidden',
        placeholderSelectDate: 'Chọn ngày chủ nhiệm',
        formatSelectDate: 'yyyy/MM/dd',
        clearSelectedDate: true,
        disabledDaysOfWeek: [],
        cm_assign_date: '',
        cms: [],
        cm: '',
        item: {},
        disabledCM: false,
        rooms: [],
        shifts: [],
        teachers: [],

        obj: {
          class: {
            room_id: 0,
            shift_id: 0,
            teacher_id: 0,
            weekdays: [2],
            status: 0,
            timeline: '',
            max_students:15,
            cm_id:'',
            num_session:48,
            start_date:'',
            is_trial:0,
          }
        },
        html: {
          class: {
            loading: false,
            button: {
              save_contracts: 'error',
              add_contract: 'primary',
              up_semester: 'success',
              print: 'success'
            },
            display: {
              modal: {
                register: false,
                semester: false
              },
              class_info: 'display',
              contracts_list: 'display',
              up_class_info: 'display',
              up_contracts_list: 'display',
              filter: {
                branch: 'hidden',
                classes: 'hidden',
                semester: 'hidden',
                up_classes: 'hidden',
                up_semester: 'hidden'
              },
              update_cm_btn: 'hidden',
            }
          },
          content: {
            loading: 'Đang tải dữ liệu...',
            label: {
              search: 'Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh'
            },
            placeholder: {
              search: 'Từ khóa tìm kiếm'
            }
          },
          disable: {
            add_contracts: true,
            up_semester: true,
            filter: {
              branch: true,
              semester: true,
              up_semester: true
            }
          },
          modal: {
            show: false,
            message: ''
          },
          confirm: {
            show: false,
            message: ''
          }
        },
        action: {
          loading: false,
        },
        url: {
          api: '/api/enrolments/',
          class: '/api/enrolments/classInfo/',
          classes: '/api/enrolments/classes/',
          branches: '/api/enrolments/branches',
          semesters: '/api/enrolments/classes/semesters',
          contracts: '/api/enrolments/contracts/',
          cms: '/api/settings/cms/',
          withdraw: '/api/enrolments/withdraw/',
          update_cm: '/api/classes/update/cm',
          list_cm: '/api/teachers/branch/'
        },
        filter: {
          class: '',
          branch: '',
          keyword: '',
          semester: '',
          up_class: '',
          up_semester: '',
        },
        list: {
          students: [],
          contracts: [],
          up_students: [],
          up_contracts: [],
          list_cm: []
        },
        cache: {
          class_info: {},
          up_class_info: {},
          selected_class: {},
          selected_up_class: {},
          class: '',
          up_class: '',
          branch: '',
          program: '',
          semester: '',
          up_semester: '',
          classes: [],
          up_classes: [],
          branches: [],
          semesters: [],
          up_semesters: [],
          contracts: [],
          up_contracts: [],
          class_dates: [],
          up_class_dates: [],
          checked_list: [],
          up_checked_list: [],
          check_all: '',
          up_check_all: '',
          available: 0,
          up_available: 0,
          cms: {},
          product: {}
        },
        class_date: {},
        up_class_date: {},
        showsave: false,
        pagination: {
          url: '',
          id: '',
          style: 'line',
          class: '',
          spage: 0,
          ppage: 0,
          npage: 0,
          lpage: 0,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: []
        },
        order: {
          by: 's.id',
          to: 'DESC'
        },
        tab: {
          list: [
            {
              name: 'tab-1',
              text: 'Cấu Hình'
            },
            {
              name: 'tab-2',
              text: 'Thông Tin'
            }
          ],
          current: 1
        },
        temp: {},
        session: u.session(),
        show: false,
        show_status: true,
        show_edit: false,
        program_id:0,
        show_semester_end: false,
        admin_edit: true,
        has_edit: true,
        options: [],
        class_options: [],
      }
    },
    computed: {
      selectAll: {
        get: function () {
          return parseInt(this.cache.checked_list.length) === parseInt(this.cache.contracts.length)
        },
        set: function (value) {
          const selected_list = []
          if (value) {
            this.cache.contracts.forEach((contract) => {
              if (contract.student_nick && contract.student_nick.length > 0) {
                selected_list.push(contract.contract_id)
              }
            })
          }
          this.cache.checked_list = selected_list
        }
      },
      hasEditPermission: function(){
        return [999999999, 56, 58, 686868].includes(parseInt(_.get(this, 'session.user.role_id', 0)))
      },
      hasOnlyEditChangeTeacherPermission: function () {
        return [55, 57].includes(_.get(this, 'session.user.role_id'),0)
      }
    },
    created() {
      this.start()
      if (u.session().user.role_id == 999999999){
        this.admin_edit = false
      }
    },
    filters: {
      filterTuitionFee: (name, price) => price && price > 1000 ? `${name} - ${this.filterFormatCurrency(price)}` : name,
      filterFormatCurrency: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ'
    },
    watch: {},
    methods: {
      modalClassClosed(){
        this.confirm_transfer = true
        this.class_form.product_id = 0
        this.class_form.program_id = 0
        this.class_form.nearest_day =''
        this.class_form.list_student =[]
        this.class_form.to_class_id = 0
        this.class_form.start_date = ''
      },
      selectedDateTransfer(){
        if (this.class_form.list_student.length >0 && this.class_form.from_product_id && this.class_form.from_program_id && this.class_form.product_id && this.class_form.program_id && this.class_form.nearest_day && this.class_form.current_day&& this.class_form.to_class_id&& this.class_form.start_date){
          this.confirm_transfer = false
        }
      },
      selectedOneStudent: function(value,event){
        if (event.target.checked){
          console.log("checked",value)
          this.userIds.push(value)
        }
        else{
          console.log("un checked",value)
          this.userIds.splice(this.userIds.indexOf(value), 1)
        }
        this.class_form.list_student = this.userIds
        console.log("this.userIds", this.userIds)
      },
      selectAllClass: function(students,event) {
        this.userIds = [];
          if (event.target.checked){
            let list=[];
            $.each(students, function(key, student) {
              list.push(student.contract_id);
            })
            this.userIds = list
          }
        console.log("selectAllClass", this.userIds)
        this.class_form.list_student = this.userIds
      },
      getSelectProgram(branch_id,product_id){
        u.a().get(`/api/all/programs/${branch_id}/${product_id}`).then((response) => {
          console.log("response.data ",response.data)
          //this.disabledProgram = false
          this.options         = response.data
        })
      },
      selectedToClass(classInfo){
        this.class_form.nearest_day = classInfo.nearest_day
        this.class_form.to_class_id = classInfo.id
      },
      selectedProgram(program){
        this.class_form.program_id = program.id
        console.log("branch.data=== ")
        u.a().get(`/api/support/transfer-all-class/${parseInt(this.filter.branch)}/${program.id}?not_in=${this.class_form.from_class_id}`).then((response) => {
          console.log("response.data ",response.data)
          //this.disabledProgram = false
          let data = response.data.data
          this.class_options         = data
        })
      },
      selectProduct(product){
        this.options = []
        this.class_form.product_id = product.id
        this.getSelectProgram(parseInt(this.filter.branch),product.id)
      },
      yourOkFn(){
        if(confirm("Bạn có chắc chắn không?")){
          const params = {
            ...this.class_form,
            transfer_start_date: moment(this.class_form.start_date).format('YYYY-MM-DD'),
          }
          this.action.loading = true
          u.a().post('/api/all-class-transfers', params)
            .then(response => {
                this.action.loading = false
                this.html.modal.show = true
                this.last_selected_id = response.data.data
                this.html.modal.message = response.data.message

            }).catch(e => {


          });
        }
        else{
          this.modalClassClosed()
        }
      },
      classFormModal(){
        this.largeModal =true
      },
      checkAlert: (alert, withdraw) => {
        let resp = alert ? 'alert' : 'active'
        resp = withdraw ? resp : `${resp} withdraw`
        if (parseInt(withdraw,10) === 7) {
          resp = 'active withdraw'
        }
        if (parseInt(withdraw,10) === 8) {
          resp = 'alert withdraw'
        }
        return resp
      },
      closeModal(){
        this.html.modal.show = false
        this.modalClassClosed()
        this.reloadSelectedClass(this.last_selected_id)
      },
      showMessage(message){
        this.html.modal.message = message;
        this.html.modal.show = true;
      },
      confirmSaveClassCm(){
        this.html.confirm.message = "Bạn có chắc chắn thực hiện thao tác này?"
        this.html.confirm.show = true
        this.cache.class_info.cm_id = this.cm
        this.showsave = false
      },
      resetall() {
        this.cm = '-1';
      },
      saveclasscm() {
        if(parseInt(this.cm) == -1){
          this.html.confirm.message = "<span class='text-danger'>Vui lòng chọn CM</span>"
        }else{
          this.html.confirm.show = false
          let data = {
            cm_id: this.cm,
            class_id: this.cache.class
          }
          u.a().post(`${this.url.update_cm}`, data)
            .then(response => {
              let message = '';
              if(response.data.code == 200){
                message = "<span class='text-success'>Cập nhật thành công</span>"
              }else{
                message = "<span class='text-success'>" + response.data.message + "</span>"
              }
              this.showMessage(message);
            }).catch(e => {
            u.log('Exeption', e);
            let message = "<span class='text-danger'>Có lỗi xảy ra. Vui lòng thử lại sau!</span>"
            this.showMessage(message)
          });
        }
      },
      link(semester = false) {
        let resp = ''
        this.filter.branch = this.cache.branch
        if (semester) {
          const search = u.jss({
            class: this.cache.up_class,
            branch: this.cache.branch,
            semester: this.cache.up_semester
          })
          resp = `${this.url.up_contracts}`
        } else {
          const search = u.jss({
            class: this.cache.class,
            branch: this.cache.branch,
            keyword: this.cache.keyword,
            semester: this.cache.semester
          })
          const pagination = u.jss({
            spage: this.pagination.spage,
            ppage: this.pagination.ppage,
            npage: this.pagination.npage,
            lpage: this.pagination.lpage,
            cpage: this.pagination.cpage,
            total: this.pagination.total,
            limit: this.pagination.limit
          })
          resp = `${this.url.contracts}${pagination}/${search}`
        }
        return resp
      },
      remove(student) {
        const confirmation = confirm("Bạn có chắc là muốn withdraw học sinh này không?")
        if (confirmation) {
          if (!this.html.class.display.modal.semester) {
            this.action.loading = true
          }
          u.g(`${this.url.withdraw}${student.contract_id}`)
            .then(response => {
              this.selectClass(this.cache.selected_class)
            }).catch(e => u.log('Exeption', e))
        }
      },
      load(data, semester = false) {
        u.log('Response Data', data)
        if (semester) {
          this.cache.up_contracts = data.contracts
          this.cache.up_class_dates = data.class_dates
          this.cache.up_available = data.available
          this.cache.up_checked_list = []
          html.class.up_display.modal.semester = true
        } else {
          this.cache.contracts = data.contracts
          this.cache.class_dates = data.class_dates
          this.pagination = data.pagination
          this.cache.available = data.available
          this.cache.checked_list = []
          this.html.class.display.modal.register = true
        }
        this.action.loading = false
      },
      searching(word) {
        const key = word != '' ? word : this.cache.keyword
        this.cache.keyword = key
        this.loadContracts()
      },
      redirect(link) {
        const info = link.toString().split('/')
        const page = info.length > 1 ? info[1] : 1
        this.pagination.cpage = parseInt(page)
        if (!this.html.class.display.modal.register) {
          this.action.loading = true
        }
        u.a().get(this.link())
          .then(response => {
            const data = response.data.data
            this.load(data)
            setTimeout(() => {
              this.action.loading = false
            }, data.duration)
          }).catch(e => u.log('Exeption', e))
      },
      start() {
        if (u.authorized()) {
          u.a().get(this.url.branches)
            .then(response => {
              const data = response.data.data
              this.cache.branches = data
              this.cache.branch = ''
              this.html.class.display.filter.branch = 'display'
              this.html.class.display.filter.semester = 'display'
              this.html.disable.filter.branch = false
            }).catch(e => u.log('Exeption', e))
        } else {
          this.cache.branch = this.session.user.branch_id
          this.cm = '-1';
          this.getCms()
          this.loadSemesters()
        }
      },
      loading() {
      },
      extract() {
      },
      enableWithdraw(v,s) {
        if (s == 7) return false
        if (this.session.user.role_id == 999999999 || this.session.user.role_id == 55 || this.session.user.role_id == 56)
          return true
        let lastDate = new Date(v)
        let currentDate = new Date()

        if (lastDate <= currentDate && (this.session.user.role_id == 55 || this.session.user.role_id == 56))
          return true
        else
          return false
      },
      studentStatus(v, w = 0) {
        let resp = ""
        if (w) {
          resp = parseInt(v) === 1 ? '<div class="alert-label apax-label">Withdraw</div>' : parseInt(v) === 2 ? '<div class="primary-label apax-label">Transfering</div>' : '<div class="disable-label apax-label">Withdraw</div>'
        } else {
          resp = parseInt(v) === 1 ? '<div class="success-label apax-label">Đang Học</div>' : parseInt(v) === 2 ? '<div class="primary-label apax-label">Transfering</div>' : '<div class="disable-label apax-label">Withdraw</div>'
        }
        if (parseInt(v) === 6) {
          resp = '<div class="success-label apax-label">Đang Học</div>'
        }
        if (parseInt(v) === 7) {
          resp = '<div class="disable-label apax-label">Đã Withdrawed</div>'
        }
        if (parseInt(v) === 8) {
          resp = '<div class="disable-label apax-label">Đã Rút phí</div>'
        }
        return resp
      },
      loadContracts() {
        this.html.class.display.modal.semester = false
        if (!this.html.class.display.modal.register) {
          this.action.loading = true
        }
        u.a().get(this.link())
          .then(response => {
            const data = response.data.data
            this.load(data)
            setTimeout(() => {
              this.action.loading = false
            }, data.duration)
          }).catch(e => u.log('Exeption', e))
      },
      loadSemesters() {
        u.a().get(this.url.semesters)
          .then(response => {
            const data = response.data.data
            this.cache.semesters = data
            this.filter.semester = ''
            this.html.class.display.filter.semester = 'display'
            this.html.disable.filter.semester = false
          }).catch(e => u.log('Exeption', e))
      },
      selectBranch(data) {
        this.cache.branch = data.id
        this.filter.branch = data.id
        this.cm = '-1';
        this.getCms()
        this.loadSemesters()
        this.selectProgram(0)
      },
      selectSemester() {
        this.cache.semester = this.filter.semester
        u.log('Semester', this.filter.semester)
        u.a().get(`${this.url.classes}all/${this.cache.branch}/${this.cache.semester}`)
          .then(response => {
            const data = response.data.data
            this.cache.classes = data
            this.filter.class = ''
            this.html.class.display.filter.classes = 'display'
            this.type=data[0].type
          }).catch(e => u.log('Exeption', e))
        this.selectProgram(0)
        u.a().get(`/api/semesters/${this.cache.semester}`).then(response =>{
          if(Date.parse(response.data.end_date) < Date.parse(new Date())){
            this.show_semester_end = true;
          }else{
            this.show_semester_end = false;
          }
        })
        this.getProductBySemesterId(this.cache.semester)
      },
      getProductBySemesterId(semesterId){
        u.a().get(`/api/products/semester-id/${semesterId}`).then(response =>{
          this.cache.product = response.data
        })
      },
      clear_checked(){
        this.class_form.list_student = []
        this.userIds = []
      },
      selectUpSemester(selected_up_class) {
        this.cache.up_semester = this.filter.up_semester
        u.log('Semester', this.filter.up_semester)
        u.a().get(`${this.url.classes}${this.cache.branch}/${this.cache.up_semester}`)
          .then(response => {
            const data = response.data.data
            this.cache.up_classes = data
            this.filter.up_class = ''
            this.html.class.display.filter.up_classes = 'display'
          }).catch(e => u.log('Exeption', e))
      },
      selectClass(selected_class) {
        if (selected_class.model.item_type === 'class') {
          this.cms = this.cache.cms[this.cache.branch]
          this.cm = '-1';
          u.log('Program', selected_class.model)
          this.cache.selected_class = selected_class
          this.action.loading = true
          this.cache.class = selected_class.model.item_id
          this.filter.class = this.cache.class
          this.clear_checked()
          u.a().get(`${this.url.class}${this.filter.class}?status=6`)
            .then(response => {
              const data = response.data.data
              this.cache.class_info = data.class
              this.cache.students = data.students
              this.html.disable.add_contracts = false
              this.html.disable.up_semester = false
              this.html.class.display.class_info = 'display'
              this.html.class.display.contracts_list = 'display'
              this.action.loading = false
              if(this.cache.class_info.can_update == 1){
                this.disabledCM = false;
              }else{
                this.disabledCM = true;
              }
              this.class_form.current_day = data.class.next_classdate
              this.class_form.branch_id = this.filter.branch
              this.class_form.from_class_id = data.class.class_id
              this.class_form.class_day = data.class.arr_weekdays[0]
              this.class_form.from_product_id = data.class.product_id
              this.class_form.from_program_id = data.class.program_id
              if (this.class_form.from_program_id){
                this.disableSaveCm = false
              }
              this.obj.class.name = data.class.class_name
              this.obj.class.teacher_id = data.class.teacher_id
              this.obj.class.weekdays=data.class.arr_weekdays
              this.obj.class.room_id=data.class.room_id
              this.obj.class.shift_id=data.class.shift_id
              this.obj.class.cm_id=data.class.cm_id
              this.obj.class.max_students=data.class.class_max_students
              this.obj.class.start_date=data.class.class_start_date
              this.obj.class.num_session=data.class.num_session
              this.obj.class.is_trial=data.class.is_trial
              this.cm = data.class.cm_id
              // console.log("is_trial== ",this.obj.class.is_trial);
              if(data.class.class_is_cancelled=='no'){
                this.obj.class.status=0
              }else{
                this.obj.class.status=1
              }
              if(this.show_semester_end == true){
                this.show = true;
              }else{
                this.show=false;
              }
              this.show_edit= true;
              if(data.class.total_students == 0){
                this.show_status= false;
                if (u.session().user.role_id == 999999999){
                  this.has_edit = false
                }
              }
              else{
                this.show_status = true;
                if (u.session().user.role_id == 999999999){
                  this.has_edit = true
                }
              }
              this.show_status = false;
            }).catch(e => u.log('Exeption', e))

          u.a().get(`/api/branches/${this.cache.branch}/teachers`).then(response =>{
            this.teachers = response.data
          })
          u.a().get(`/api/rooms/branch/${this.cache.branch}`).then(response =>{
            this.rooms = response.data
          })
          u.a().get(`/api/shifts/branch/${this.cache.branch}`).then(response =>{
            this.shifts = response.data;
          })
        } else {
          this.has_edit = false
          this.selectProgram(selected_class.model.item_id);
          this.show_edit= false;
          if(this.show_semester_end == true){
            this.show = true;
          }else{
            this.show=false;
          }

          u.a().get(`/api/branches/${this.cache.branch}/teachers?status=1`).then(response =>{
            this.teachers = response.data
          })
          u.a().get(`/api/rooms/branch/${this.cache.branch}?status=1`).then(response =>{
            this.rooms = response.data
          })
          u.a().get(`/api/shifts/branch/${this.cache.branch}?status=1`).then(response =>{
            this.shifts = response.data;
          })
          u.a().get(`/api/branches/${this.cache.branch}/cm?status=1`).then(response =>{
            this.list.list_cm = response.data;
          })
        }
      },
      selectProgram(program_id){
        this.program_id = program_id;
        if(this.program_id==0){
          this.show=true;
        }else{
          this.show=false;
        }
        this.obj.class.name='';
        this.obj.class.teacher_id=0;
        this.obj.class.weekdays=[2];
        this.obj.class.room_id=0;
        this.obj.class.shift_id=0;
        this.obj.class.status=0;
        this.obj.class.cm_id=0;
        this.obj.class.max_students=15;
        this.obj.class.start_date= '';
        this.obj.class.num_session=48;
        this.cache.class='';
      },

      onChangeCm() {
        if(this.cache.class_info.cm_id != this.cm){
          this.showsave = true;
        }else{
          this.showsave = false;
        }

      },

      selectUpClass(selected_up_class) {
        if (selected_up_class.model.item_type === 'class') {
          u.log('Program', selected_up_class.model)
          this.cache.selected_up_class = selected_up_class
          this.cache.up_class = selected_up_class.model.item_id
          this.filter.up_class = this.cache.up_class
          u.a().get(`${this.url.class}${this.filter.class}`)
            .then(response => {
              const data = response.data.data
              this.cache.up_class_info = data.class
              this.cache.up_students = data.students
              u.log('Class Data', this.cache.up_class_info)
              this.html.class.display.up_class_info = 'display'
              this.html.class.display.up_contracts_list = 'display'
            }).catch(e => u.log('Exeption', e))
        }
      },
      activeSearch(event) {
        if (event.key == "Enter") {
          if (this.cache.branch && this.cache.semester && this.cache.class && this.html.class.display.modal.register == true) {
            this.searching(this.keyword)
          }
        }
      },

      checkItem(contract) {
        u.log('Check contract', contract)
        if (contract.student_nick == '') {
          alert(`Vui lòng nhập nick cho học sinh này!`)
          contract.student_nick = ''
        }
      },


      completeAddRegister() {

      },
      completeUpSemester() {

      },
      getCms(){
        if(!u.live(this.cache.cms[this.cache.branch])){
          u.a().get(`/api/branches/${this.cache.branch}/cm`).then(response =>{
            this.list.list_cm = response.data;
          })
        }
      },
      addClass(){
        const session = {
          branch_id: this.cache.branch,
          semester_id: this.cache.semester,
          program_id: this.program_id,
          class_name: this.obj.class.name,
          teacher_id: this.obj.class.teacher_id,
          weekdays: this.obj.class.weekdays,
          room_id: this.obj.class.room_id,
          shift_id: this.obj.class.shift_id,
          status: this.obj.class.status,
          cm_id: this.obj.class.cm_id,
          is_trial: this.obj.class.is_trial,
          max_students: this.obj.class.max_students,
          start_date: this.getDate(typeof(this.obj.class.start_date) != 'undefined' ? this.obj.class.start_date : ''),
          num_session: this.obj.class.num_session,
        }
        const maxNumberOfDaysInWeek = parseInt(_.get(this, 'cache.product.max_number_of_days_in_week'))
        const minNumberOfDaysInWeek = parseInt(_.get(this, 'cache.product.min_number_of_days_in_week'))
        if(maxNumberOfDaysInWeek || minNumberOfDaysInWeek){
          const numberOfWeekdays = parseInt(_.get(session, 'weekdays.length'),10)
          if(maxNumberOfDaysInWeek && maxNumberOfDaysInWeek === minNumberOfDaysInWeek && maxNumberOfDaysInWeek !== numberOfWeekdays){
            alert(`Bắt buộc phải chọn ${maxNumberOfDaysInWeek} ngày học trong tuần`)
            return false
          }
          if((maxNumberOfDaysInWeek && numberOfWeekdays > maxNumberOfDaysInWeek)
            || (minNumberOfDaysInWeek && numberOfWeekdays < minNumberOfDaysInWeek)){
            alert(`Số ngày học trong tuần phải lớn hơn hoặc bằng ${minNumberOfDaysInWeek} và nhỏ hơn hoặc bằng ${maxNumberOfDaysInWeek}`)
            return false
          }
        }else if(session.weekdays.length != 1){
          alert("Bắt buộc phải chọn 1 ngày học trong tuần")
          return false
        }else
        if(session.room_id == ''){
          alert("Phòng học không để trống")
          return false
        }else if(session.start_date == ''){
          alert("Thời gian bắt đầu không để trống")
          return false
        }else if(session.cm_id == ''){
          alert("CS - Giáo Viên Chủ Nhiệm không để trống")
          return false
        }else if(session.shift_id == ''){
          alert("Ca học không để trống")
          return false
        }else if(session.teacher_id == ''){
          alert("Giáo viên không để trống")
          return false
        }
        else {
          const data = session
          u.a().post(`/api/sessions`, data).then(response => {
            if (response.data.code === 1){
              this.html.modal.message = "Thêm lớp học thất bại. Vui lòng kiểm tra lại!"
            }
            else{
              this.html.modal.message = "Thêm lớp học thành công!"
            }
            this.selectSemester();
            this.html.modal.show = true
          })
        }
      },
      updateClass(){
        const session = {
          class_id: this.cache.class,
          class_name: this.obj.class.name,
          teacher_id: this.obj.class.teacher_id,
          room_id: this.obj.class.room_id,
          shift_id: this.obj.class.shift_id,
          status: this.obj.class.status,
          cm_id: this.obj.class.cm_id,
          max_students: this.obj.class.max_students,
          weekdays: this.obj.class.weekdays,
          num_session: this.obj.class.num_session,
          start_date: this.getDate(this.obj.class.start_date),
          is_class:1,
          is_trial:this.obj.class.is_trial,
        }
        if(session.room_id == ''){
          alert("Phòng học không để trống")
          return false
        }
        // else if(session.start_date == ''){
        //     alert("Thời gian bắt đầu không để trống")
        //     return false
        // }
        else if(session.shift_id == ''){
          alert("Ca học không để trống")
          return false
        }else if(session.cm_id == ''){
          alert("CS - Giáo Viên Chủ Nhiệm không để trống")
          return false
        }else if(session.teacher_id == ''){
          alert("Giáo viên không để trống")
          return false
        }
        else {
          u.a().put(`/api/sessions/${this.cache.class}`, {...session}).then(response => {
            this.html.modal.message = response.data.msg
            this.html.modal.show = true
            this.selectSemester();
          })
        }
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
        return '';
      },
      reloadSelectedClass(class_id) {
          this.cms = this.cache.cms[this.cache.branch]
          this.cm = '-1';
          this.action.loading = true
          this.filter.class = this.cache.class
          this.clear_checked()
          u.a().get(`${this.url.class}${class_id}?status=6`)
            .then(response => {
              const data = response.data.data
              this.cache.class_info = data.class
              this.cache.students = data.students
              this.html.disable.add_contracts = false
              this.html.disable.up_semester = false
              this.html.class.display.class_info = 'display'
              this.html.class.display.contracts_list = 'display'
              this.action.loading = false
              if(this.cache.class_info.can_update == 1){
                this.disabledCM = false;
              }else{
                this.disabledCM = true;
              }
              this.class_form.current_day = data.class.next_classdate
              this.class_form.branch_id = this.filter.branch
              this.class_form.from_class_id = data.class.class_id
              this.class_form.class_day = data.class.arr_weekdays[0]
              this.class_form.from_product_id = data.class.product_id
              this.class_form.from_program_id = data.class.program_id
              if (this.class_form.from_program_id){
                this.disableSaveCm = false
              }
              this.obj.class.name = data.class.class_name
              this.obj.class.teacher_id = data.class.teacher_id
              this.obj.class.weekdays=data.class.arr_weekdays
              this.obj.class.room_id=data.class.room_id
              this.obj.class.shift_id=data.class.shift_id
              this.obj.class.cm_id=data.class.cm_id
              this.obj.class.max_students=data.class.class_max_students
              this.obj.class.start_date=data.class.class_start_date
              this.obj.class.num_session=data.class.num_session
              this.obj.class.is_trial=data.class.is_trial
              this.cm = data.class.cm_id
              // console.log("is_trial== ",this.obj.class.is_trial);
              if(data.class.class_is_cancelled=='no'){
                this.obj.class.status=0
              }else{
                this.obj.class.status=1
              }
              if(this.show_semester_end == true){
                this.show = true;
              }else{
                this.show=false;
              }
              this.show_edit= true;
              if(data.class.total_students == 0){
                this.show_status= false;
                if (u.session().user.role_id == 999999999){
                  this.has_edit = false
                }
              }
              else{
                this.show_status = true;
                if (u.session().user.role_id == 999999999){
                  this.has_edit = true
                }
              }
              this.show_status = false;
            }).catch(e => u.log('Exeption', e))

          u.a().get(`/api/branches/${this.cache.branch}/teachers`).then(response =>{
            this.teachers = response.data
          })
          u.a().get(`/api/rooms/branch/${this.cache.branch}`).then(response =>{
            this.rooms = response.data
          })
          u.a().get(`/api/shifts/branch/${this.cache.branch}`).then(response =>{
            this.shifts = response.data;
          })
      },
    }
  }
</script>

<style scoped language="scss">
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

    .contracts-list select.class-date {
        padding: 0 5px;
        border: 0.5px dashed #add8ff;
        font-size: 10px;
        width: 140px;
        margin: 0 !important;
        display: inline;
        height: 20px;
    }

    #register-detail table.contracts-list tr td.nick-empty {
        background: #fff5f5;
    }

    label.check-item .custom-control-indicator {
        width: 0.9rem;
        height: 0.9rem;
    }
    .withdraw-button {
        cursor: pointer;
    }
    .contracts-list .contract-nick {
        background: none;
        text-align: center;
        border: none;
    }

    .contracts-list .contract-nick.input-nick {
        border: 0.5px solid #d6e6fc;
        border-top: 0.5px solid #8cc8ff;
        color: #2a374e;
        text-shadow: 0 -1px 0 #fff;
        background: #f6fbff;
    }

    .contract-nick.input-nick.alert {
        color: #862323;
        border: 0.5px solid #fcd6d6;
        border-top: 0.5px solid #ff8c8c;
        background: #fff5f5;
    }

    .search-contracts {
        padding: 0 0 0 10px;
    }

    .buttons-bar .apax-search {
        float: left;
    }

    .buttons-bar .apax-button {
        float: left;
        height: 35px;
        font-size: 12px;
        padding: 3px 15px 0;
        margin: 0 0 0 20px;
    }
    table.apax-table tr.active td {
        background: #e4ffdd!important;
    }
    table.apax-table tr.alert td {
        background: #ffdddd!important;
    }
    table.apax-table tr.active.withdraw td {
        background: #c4c5c7!important;
    }
    #add-contract___BV_modal_footer_ button:first-child {
        display: none !important;
    }
    button[disabled].reset, button[disabled].reset:hover{
        background-color: #fd8c0a7a;
    }
    button[disabled].edit, button[disabled].edit:hover{
        background-color: #02bd2473;
    }
    .nav-tabs .nav-item a.nav-link, .nav-tabs .nav-item .navbar a.dropdown-toggle, .navbar .nav-tabs .nav-item a.dropdown-toggle {
        color: #b93232;
        font-weight: 500;
    }
    .nav-tabs .nav-item:last-child {
        border-right: 1px solid #b93232;
    }
    .nav-tabs {
        padding: 0;
        border-bottom: 1px solid #b93232 !important;
    }
    .nav-tabs .nav-item.forActive, .nav-tabs .nav-item.forActive:hover {
        outline: none;
        background: #FFF;
        text-shadow: 0 1px 1px #333;
        border-left: 1px solid #b93232;
        border-bottom: 1px solid #FFF;
    }
    .nav-tabs .nav-item {
        background: #f5d4d4;
        color: #b93232;
        text-shadow: 0 -1px 0 #fff;
        border-left: 1px solid #b93232;
        border-top: 1px solid #b93232;
        border-bottom: 1px solid #b93232;
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
        color: #3e515b;
        background-color: #fff;
        border-color: #fff;
    }
    .form-check-left {
        float: left;
        display: block;
        width: 75px;
    }
    .form-check-input ~ .form-check-label {
        padding-left: 15px;
    }
    .form-check-input{
        margin-left: 0px;
    }
</style>
