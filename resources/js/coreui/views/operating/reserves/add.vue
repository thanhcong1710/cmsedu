<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div v-show="flags.form_loading" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu... </div>
            </div>
          </div>
          <div v-show="flags.requesting" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ
                trong giây lát...
              </div>
            </div>
          </div>

          <div slot="header">
            <i class="fa fa-id-card"></i> <b class="uppercase">Bảo Lưu. </b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6 pad-no">
                        <div class="col-md-12">
                          <address>
                            <h6 class="text-main">Thông tin học sinh</h6>
                          </address>
                        </div>
                        <div class="col-12" :class="html.search_branch.display">
                          <label class="control-label">Trung tâm</label>
                          <branch                                                        
                              label="name"
                              :filterable=true
                              :options="branch_list"
                              v-model="branch_info" 
                              @input="selectBranch"
                              placeholder="Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước" 
                          />
                          <br/>
                        </div>
                        <div class="col-12" :class="html.search_contract.display">
                          <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc Tên</label>
                          <ContractSearch
                            :onSearchContract="searchContract"
                            :selectedContract="data.enroll"
                            :onSelectContract="selectEnrolment">
                          </ContractSearch>
                          <br/>
                        </div>
                        <div class="col-md-12 pad-no">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Mã Cyber</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.accounting_id">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Mã CMS</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.lms_id">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Họ Tên</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.student_name">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Tên Tiếng Anh</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.nick">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Sản phẩm</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.product_name">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Chương trình</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.program_name">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Lớp</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.class_name">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số phí đã đóng theo gói phí</label>
                                <input class="form-control" type="text" readonly
                                       :value="data.enroll.total_fee | formatMoney">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Tổng số buổi học theo gói phí</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.real_session">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi học bổng theo gói phí</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.bonus_sessions">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi đã học </label>
                                <input class="form-control" type="text" readonly :value="data.enroll.done_sessions">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số phí bảo lưu theo gói phí</label>
                                <input class="form-control" type="text" readonly
                                       :value="data.temp.amount_reserved | formatMoney">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi chưa học xin bảo lưu</label>
                                <input class="form-control" type="text" readonly
                                       :value="data.temp.number_of_session_reserved">
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                      <div class="col-md-6 pad-no">
                        <div class="col-md-12">
                          <address>
                            <h6 class="text-main">Thông tin bảo lưu</h6>
                          </address>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi bảo lưu <span class="text-danger"
                                                                                   :class="html.form_fields.number_of_sessions.suggest.display">(Tối đa {{data.temp.max_session}} buổi)</span>
                                  <strong class="text-danger h6">*</strong></label>
                                <input class="form-control" v-model="data.reserve.session"
                                       :readonly="html.form_fields.number_of_sessions.readonly"
                                       @change="validateReserveSession">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Lý do bảo lưu<strong class="text-danger h6">*</strong></label>
                                <textarea class="form-control" rows="5" v-model="data.reserve.note"
                                          :readonly="html.form_fields.note.readonly"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày bắt đầu bảo lưu <strong
                                  class="text-danger h6">*</strong></label>
                                <calendar
                                  class="form-control calendar"
                                  :value="data.temp.reserve_date"
                                  :transfer="true"
                                  :format="html.calendar.options.formatSelectDate"
                                  :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                  :clear-button="html.calendar.options.clearSelectedDate"
                                  :placeholder="html.calendar.options.placeholderSelectDate"
                                  :pane="1"
                                  :disabled="html.calendar.is_disabled"
                                  :onDrawDate="onDrawDate"
                                  :lang="html.calendar.lang"
                                  :not-before="data.temp.min_date"
                                  :not-after="data.temp.max_date"
                                  @input="selectTransferDate"
                                ></calendar>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày kết thúc bảo lưu</label>
                                <input class="form-control" type="text" readonly :value="data.temp.reserve_end_date">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày bắt đầu</label>
                                <input class="form-control" type="text" readonly :value="data.enroll.start_date">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày kết thúc</label>
                                <input class="form-control" type="text" readonly :value="data.temp.new_enrol_end_date">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <!-- <div class="col-md-6">
                              <div class="form-group">
                                <input v-model="data.reserve.is_reserved" type="checkbox"
                                       :disabled="html.form_fields.is_reserved.disabled"> Giữ chỗ trong lớp
                              </div>
                            </div> -->
                            <!-- <div class="col-md-6">
                              <div class="form-group">
                                <file
                                  :label="'File đính kèm'"
                                  :name="'upload_transfer_file'"
                                  :field="'attached_file'"
                                  :type="'transfer_file'"
                                  :full="false"
                                  :onChange="uploadFile"
                                  :title="'Tải lên 1 file đính kèm với định dạng tài liệu: jpg, png, pdf, doc, docx.'"
                                  :customClass="'no-current-file'"
                                >
                                </file>
                              </div>
                            </div> -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-footer">
                    <div class="col-sm-12 col-sm-offset-3">
                      <ApaxButton
                        :markup="html.buttons.save.style"
                        :onClick="confirm"
                      >Lưu
                      </ApaxButton>

                      <ApaxButton
                        :onClick="exitAddReserve"
                      >Thoát
                      </ApaxButton>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="callback"
                 ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true">
          <div v-html="html.modal.message">
          </div>
        </b-modal>
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.confirm.show" @ok="addReserve"
                 ok-variant="primary">
          <div v-html="html.confirm.message">
          </div>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import calendar from 'vue2-datepicker'
  import u from '../../../utilities/utility'
  import ContractSearch from '../../../components/ContractSearch'
  import SearchBranch from '../../../components/SearchBranchForTransfer'
  import ApaxButton from '../../../components/Button'
  import file from '../../../components/File'
  import branch from 'vue-select'

  export default {
    name: 'Add-Reserve',
    components: {
      calendar,
      ContractSearch,
      SearchBranch,
      ApaxButton,
      file,
      branch,
    },
    data() {
      return {
        html: {
          calendar: {
            is_disabled: true,
            options: {
              formatSelectDate: 'YYYY-MM-DD',
              disabledDaysOfWeek: [],
              clearSelectedDate: true,
              placeholderSelectDate: 'Chọn ngày bắt đầu',
            },
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
                ],
                pickers: ["", "", "7 ngày trước", "30 ngày trước"]
            }
          },
          search_branch: {
            display: 'show',
            placeholder: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
            id: 'search_suggest_branch'
          },
          search_contract: {
            display: 'hide',
            placeholder: ''
          },
          modal: {
            show: false,
            message: ''
          },
          confirm: {
            show: false,
            message: ''
          },
          form_fields: {
            is_reserved: {
              disabled: true,
            },
            reserve_type: {
              disabled: true,
              options: {
                normal_type: {
                  disabled: false
                }
              }
            },
            number_of_sessions: {
              readonly: true,
              suggest: {
                display: 'hide',
              }
            },
            note: {
              readonly: true,
            },
            start_reserve_date: {
              readonly: true,
            },
            attached_file: {
              name: 'File đính kèm',
              icon: 'fa-upload'
            }
          },
          buttons: {
            save: {
              style: 'success'
            }
          }
        },
        data: {
          reserve: {
            student_id: 0,
            contract_id: 0,
            type: '-1',
            session: 0,
            start_date: '',
            branch_id: 0,
            product_id: 0,
            program_id: 0,
            class_id: 0,
            new_enrol_end_date: '',
            note: '',
            end_date: '',
            is_reserved: 0,
            attached_file: ''
          },
          enroll: {
            accounting_id: '',
            lms_id: '',
            student_name: '',
            nick: '',
            program_name: '',
            product_name: '',
            class_name: '',
            start_date: '',
            last_date: '',
            real_session: '',
            bonus_sessions: 0,
            summary_sessions: 0,
            done_session:0,
            bonus_sessions:0,
            total_fee: '',
            class_days: {},
            holidays: []
          },
          branch: {
            id: 0
          },
          temp: {
            number_of_session_left: 0,
            amount_left: 0,
            reserve_date: '',
            reserve_end_date: '',
            new_enrol_end_date: '',
            max_session: 0,
            min_date: u.convertDateToString(new Date()),
            max_date: u.convertDateToString(new Date()),
            number_of_session_reserved: 0,
            number_of_real_sessions_reserved: 0,
            number_of_bonus_sessions_reserved: 0,
            amount_reserved: 0,
            new_start_date: '',
            sessions_from_start_to_reserve_date: 0,
            amount_from_start_to_reserve_date: 0,
            old_enrol_end_date: ''
          },
          cache: {
            holidays: {}
          },
        },
        flags: {
          created_success: false,
          searching: false,
          calculating: false,
          form_loading: false,
          requesting: false,
        },
        branch_list: [],
        branch_info:'',
      }
    },
    mounted() {
      this.flags.form_loading = true;
      // this.setDefaultBranch();
      this.getHolidays();
    },
    created() {
      this.branch_info = u.session().user.branches.filter(item => item.id == u.session().user.branch_id)[0]
      this.branch_list = u.session().user.branches
    },
    methods: {
      onDrawDate(e) {
        let date = e.date;
        date = u.convertDateToString(date);
        if (this.isGreaterThan(this.data.temp.min_date, date) || this.isGreaterThan(date, this.data.temp.max_date)) {
          e.allowSelect = false;
        }
      },
      prepareSearch(check) {
        if (!check) {
          this.html.search_branch.display = 'show';
        } else {
          this.html.search_branch.display = 'hide';
        }
      },
      selectBranch(data) {
        this.data.branch.id = parseInt(data.id);
        this.flags.form_loading = true;
        this.getHolidays();
        this.html.search_contract.display = false;
      },
      selectTransferDate(selected_date) {
        let date = u.utcToLocal(selected_date)

        if (!this.flags.calculating) {
          if (this.isGreaterThan(this.data.enroll.start_date, date)) {
            this.showMessage(
              "<span class='text-danger'>Ngày bắt đầu bảo lưu không được nhỏ hơn ngày bắt đầu học " +
                this.data.enroll.start_date +
                "</span>"
            )
            this.data.reserve.type = "-1"
          } else {
            this.flags.calculating = true
            this.data.reserve.start_date = date

            this.data.temp.reserve_date = date

            let reserve_end_date = u.calEndDate(
              this.data.reserve.session,
              this.data.enroll.class_days,
              this.data.enroll.holidays,
              this.data.temp.reserve_date
            )

            if (
              !this.isReserved(
                date,
                reserve_end_date.end_date,
                this.data.enroll.reserved_dates
              )
            ) {
              var done_sessions = 0

              this.data.temp.reserve_end_date = reserve_end_date.end_date
              this.data.reserve.end_date = reserve_end_date.end_date
              let new_enrol_end_date = u.calEndDate(
                this.data.reserve.session + 1,
                this.data.enroll.class_days,
                this.data.enroll.holidays,
                this.data.enroll.end_date
              )

              this.data.temp.new_enrol_end_date = new_enrol_end_date.end_date

              this.data.reserve.new_enrol_end_date = new_enrol_end_date.end_date

              this.data.temp.new_start_date = moment(reserve_end_date.end_date, "YYYY-MM-DD").add(1, 'days').format('YYYY-MM-DD')

              if (moment(date) > moment(this.data.enroll.start_date)) {
                let new_reserve_range = {
                  start_date: this.data.temp.reserve_date,
                  end_date: this.data.temp.reserve_end_date
                }

                let holidays = this.data.enroll.holidays.concat(
                  new_reserve_range
                )

                done_sessions = u.calSessions(
                  this.data.enroll.start_date,
                  u.pre(date),
                  holidays,
                  this.data.enroll.class_days
                ).total
                this.data.enroll.done_sessions = done_sessions
                if (done_sessions == 0) {
                  this.data.temp.old_enrol_end_date = this.data.enroll.start_date
                } else {
                  this.data.temp.old_enrol_end_date = u.calEndDate(
                    done_sessions,
                    this.data.enroll.class_days,
                    holidays,
                    this.data.enroll.start_date
                  ).end_date
                }
                
                this.data.temp.sessions_from_start_to_reserve_date = done_sessions
                this.data.temp.amount_from_start_to_reserve_date = Math.ceil(
                  (done_sessions * this.data.enroll.total_fee) /
                    this.data.enroll.real_sessions
                )

                this.data.temp.number_of_session_reserved =
                  this.data.enroll.summary_sessions - done_sessions
                this.data.temp.number_of_real_sessions_reserved =
                  this.data.enroll.real_sessions > done_sessions
                    ? this.data.enroll.real_sessions - done_sessions
                    : 0
                this.data.temp.number_of_bonus_sessions_reserved =
                  this.data.temp.number_of_session_reserved -
                  this.data.temp.number_of_real_sessions_reserved

                this.data.temp.amount_reserved = this.data.enroll.real_sessions
                  ? Math.ceil(
                      (this.data.temp.number_of_real_sessions_reserved *
                        this.data.enroll.total_fee) /
                        this.data.enroll.real_sessions
                    )
                  : 0
              } else {
                this.data.temp.old_enrol_end_date = this.data.enroll.start_date
                this.data.temp.sessions_from_start_to_reserve_date = done_sessions
                this.data.temp.amount_from_start_to_reserve_date = 0
                this.data.temp.number_of_real_sessions_reserved = this.data.enroll.real_sessions

                this.data.temp.number_of_session_reserved = this.data.enroll.summary_sessions
                this.data.temp.amount_reserved = this.data.enroll.total_fee
              }
            }
            this.flags.calculating = false
          }
        }
      },
      callback() {
        if (this.flags.created_success) {
          this.$router.push('/reserves');
        } else {
          this.html.modal.show = false;
        }
      },
      setDefaultBranch() {
        let rendered = false;
        while (!rendered) {
          if ($("#search_suggest_branch").length) {
            rendered = true;
            setTimeout(function () {
            }, 500);
          }
        }
        const branches = u.session().user.branches;
        const user_branch_id = u.session().user.branch_id;
        if (branches.length) {
          for (let i in branches) {
            if (branches[i].id == user_branch_id) {
              this.data.branch.id = user_branch_id;
              $("#search_suggest_branch").val(branches[i].name).prop('readonly', true);
              this.html.search_contract.display = 'show';
              break;
            }
          }
        }
      },
      searchContract(student_name) {
        if (student_name.length > 2) {
          let url = '/api/reserves/suggest/' + (student_name ? student_name : '_') + '/' + this.data.branch.id;
          return new Promise((resolve, reject) => {
            u.a().get(url)
              .then((response) => {
                let resp = response.data.data;
                resp = resp.length ? resp : [{contract_name: 'Không tìm thấy', label: 'Không có kết quả nào phù hợp'}]
                resolve(resp)
              })
          })
        }
      },
      selectEnrolment(enrolment) {
        if(typeof enrolment.contract_id !== 'undefined'){
          if(enrolment.waiting_status==0){
            this.data.reserve.student_id = enrolment.student_id;
            this.data.reserve.contract_id = enrolment.contract_id;
            this.data.reserve.branch_id = enrolment.branch_id;
            this.data.reserve.product_id = enrolment.product_id;
            this.data.reserve.program_id = enrolment.program_id;
            this.data.reserve.class_id = enrolment.class_id;


            this.data.enroll = enrolment;
            this.data.enroll.total_fee = enrolment.total_charged;
            this.data.enroll.reserved_session = parseInt(enrolment.reserved_session);
            this.data.enroll.class_days = enrolment.class_days;
            this.data.enroll.real_session = enrolment.real_sessions;

            this.data.temp.new_enrol_end_date = enrolment.end_date;

            this.data.enroll.holidays = this.data.cache.holidays[parseInt(enrolment.product_id)].concat(enrolment.reserved_dates);

            let now = u.convertDateToString(new Date());

            // this.data.temp.min_date = this.isGreaterThan(now, enrolment.start_date) ? now : enrolment.start_date;
            this.data.temp.min_date = enrolment.start_date
            if (enrolment.reserved_dates.length) {
              for (var i in enrolment.reserved_dates) {
                if (this.isGreaterThan(enrolment.reserved_dates[i].end_date, this.data.temp.min_date)) {
                  this.data.temp.min_date = moment(enrolment.reserved_dates[i].end_date).add(1, 'days').format('YYYY-MM-DD');
                }
              }
            }
            // if(u.session().user.role_id!='999999999' && this.isGreaterThan( now,this.data.temp.min_date)){
            //   this.data.temp.min_date = now
            // }
            // if(u.session().user.role_id!='999999999'){
            //   this.data.temp.max_date = moment(enrolment.end_date_schedule).subtract(1, 'd').format('YYYY-MM-DD');
            // }else{
              this.data.temp.max_date = moment(enrolment.end_date).subtract(1, 'd').format('YYYY-MM-DD');
            // }

            let passed_session = 0;

            if (this.isGreaterThan(now, enrolment.start_date)) {
              passed_session = u.calSessions(this.data.enroll.start_date, now, this.data.enroll.holidays, this.data.enroll.class_days).total;
            }

            this.data.temp.number_of_session_left = this.data.enroll.real_session - passed_session;
            this.data.temp.amount_left = Math.ceil(this.data.temp.number_of_session_left * this.data.enroll.total_fee / this.data.enroll.real_session);
            // if(this.data.enroll.done_sessions > this.data.enroll.real_session/2){
            //   this.data.enroll.reservable_sessions = Math.floor(this.data.enroll.reservable_sessions/2)
            // }
            this.data.temp.max_session = this.data.enroll.reservable_sessions - this.data.enroll.reserved_sessions >0 ?this.data.enroll.reservable_sessions - this.data.enroll.reserved_sessions:0;

            if (this.data.temp.max_session < 1) {
              this.html.form_fields.reserve_type.options.normal_type.disabled = true;
            }
            this.html.form_fields.reserve_type.disabled = false;
            this.html.form_fields.number_of_sessions.readonly = false;
            this.html.form_fields.note.readonly = false;
            this.html.form_fields.start_reserve_date.readonly = false;
            this.html.calendar.is_disabled = false;
            this.html.form_fields.is_reserved.disabled = false;

            this.resetReserveInfo();
          }else{
            var message="";
            switch(enrolment.waiting_status) {
              case 1:
                message="Học sinh đang chờ duyệt chuyển phí";
                break;
              case 2:
                message="Học sinh đang chờ duyệt nhận phí";
                break;
              case 3:
                message="Học sinh đang chờ duyệt chuyển trung tâm";
                break;
              case 4:
                message="Học sinh đang chờ duyệt bảo lưu";
                break;
              case 5:
                message="Học sinh đang chờ duyệt chuyển lớp";
                break;
              case 41:
                message="Học sinh chỉ được thực hiện bảo lưu 1 lần nên không thể thêm bảo lưu.";
                break;
              default:
                // code block
            }
            this.showMessage(message);
          }
        }
      },
      getHolidays() {
        let url = '/api/info/' + this.data.branch.id + '/holidays';
        u.a().get(url)
          .then((response) => {
            this.flags.form_loading = false;

            if (response.data.code == 200) {
              this.data.cache.holidays = response.data.data;
            } else {
              this.data.cache.holidays = []
            }
          });
      },
      isGreaterThan(_from, _to) {
        let _from_time = new Date(_from); // Y-m-d
        let _to_time = new Date(_to); // Y-m-d
        return (_from_time.getTime() > _to_time.getTime()) ? true : false;
      },
      addReserve() {
        let isValid = this.validate();
        if (isValid.valid) {
          if (this.flags.requesting === false) {
            this.flags.requesting = true
            let data = {
              student_id: this.data.reserve.student_id,
              contract_id: this.data.reserve.contract_id,
              note: this.data.reserve.note,
              start_date: this.data.reserve.start_date,
              end_date: this.data.reserve.end_date,
              session: this.data.reserve.session,
              branch_id: this.data.reserve.branch_id,
              product_id: this.data.reserve.product_id,
              program_id: this.data.reserve.program_id,
              class_id: this.data.reserve.class_id,
              is_reserved: this.data.reserve.is_reserved ? 1 : 0,
              new_end_date: this.data.reserve.new_enrol_end_date,
              reserve_type: this.getReserveType(),
              attached_file: this.data.reserve.attached_file,
              meta_data: {
                total_session: this.data.enroll.real_session,
                total_fee: this.data.enroll.total_fee,
                session_left: this.data.temp.number_of_session_left,
                amount_left: this.data.temp.amount_left,
                start_date: this.data.enroll.start_date,
                end_date: this.data.reserve.new_enrol_end_date,
                before_reserve_end_date: this.data.enroll.end_date,
                number_of_real_sessions_reserved: this.data.temp.number_of_real_sessions_reserved,
                number_of_session_reserved: this.data.temp.number_of_session_reserved,
                amount_reserved: this.data.temp.amount_reserved,
                sessions_from_start_to_reserve_date: this.data.temp.sessions_from_start_to_reserve_date,
                amount_from_start_to_reserve_date: this.data.temp.amount_from_start_to_reserve_date,
                new_start_date: this.data.temp.new_start_date,
                old_enrol_end_date: this.data.temp.old_enrol_end_date,
                special_reserved_sessions: ((this.data.reserve.session - this.data.temp.max_session) > 0)?(this.data.reserve.session - this.data.temp.max_session):0
              }
            };
            u.p('/api/reserves', data, null, true)
              .then(response => {
                this.flags.requesting = false;

                if (response.code == 200) {
                  this.flags.created_success = true;
                  let message = "<span class='text-success'>Đăng ký thành công</span>";
                  this.showMessage(message);
                } else {
                  let message = "<span class='text-danger'>" + response.message + "</span>";
                  this.showMessage(message);
                }
              }).catch(e => {
              this.flags.requesting = false;

              let message = "<span class='text-danger'>Có lỗi xảy ra. Vui lòng thử lại sau!</span>";
              this.showMessage(message);

            });
          }
        } else {
          this.showMessage(isValid.message);
        }
      },
      exitAddReserve() {
        this.$router.push('/reserves');
      },
      showMessage(message) {
        this.html.modal.message = message;
        this.html.modal.show = true;
      },
      confirm() {
        let message = ''
        if(this.data.reserve.is_reserved){
          message = "Học sinh này đang được bảo lưu giữ chỗ, bạn có chắc thông tin này là chính xác không?";
        }else{
          message = "Học sinh này đang được bảo lưu không giữ chỗ, bạn có chắc thông tin này là chính xác không?";
        }
        u.showModal(this.html.confirm, message)
      },
      validateReserveSession() {
        let session = this.data.reserve.session ? this.data.reserve.session : 0 ;
        if (!isNaN(session)) {
          this.data.reserve.session = parseInt(session);
          if (this.data.temp.reserve_date && this.isValidDate(this.data.temp.reserve_date)) {
            this.selectTransferDate(this.data.reserve.start_date);
          }
        } else {
          this.data.reserve.session = 0;
        }
      },
      isReserved(start, end, reserve_dates) {
        let reserved = false;

        if (reserve_dates.length) {
          for (var i in reserve_dates) {
            if (!((moment(start) > moment(reserve_dates[i].end_date)) || moment(end) < moment(reserve_dates[i].start_date))) {
              reserved = true;
              this.showMessage("<span class='text-danger'>Trùng ngày bảo lưu</br>Học sinh đã bảo lưu từ ngày " + reserve_dates[i].start_date + " đến ngày " + reserve_dates[i].end_date + "</span>");
              this.data.reserve.type = '-1';
              this.resetReserveInfo();
              break;
            }
          }
        }

        return reserved;
      },
      resetReserveInfo() {
        this.data.reserve.session = 0;
        this.data.temp.reserve_date = '';
        this.data.temp.reserve_end_date = '';
        this.data.temp.new_enrol_end_date = this.data.enroll.end_date;
      },
      uploadFile(file, param = null) {
        if (param) {
          this.data.reserve.attached_file = file
        }
      },
      validate() {
        let resp = {
          valid: true,
          message: ""
        };

        if (parseInt(this.data.reserve.session) <= 0) {
          resp.valid = false;
          resp.message += "<span class='text-danger'><i class='fa fa-exclamation-circle'></i> Số buổi bảo lưu không hợp lệ</span></br>";
        }
        if (parseInt(this.data.temp.amount_reserved) < 0) {
          resp.valid = false
          resp.message += "<span class='text-danger'><i class='fa fa-exclamation-circle'></i> Số tiền bảo lưu không hợp lệ</span></br>"
        }

        if (!this.data.reserve.start_date || !this.isValidDate(this.data.reserve.start_date)) {
          resp.valid = false;
          resp.message += "<span class='text-danger'><i class='fa fa-exclamation-circle'></i> Ngày bắt đầu bảo lưu không hợp lệ</span></br>";
        }
        if (!this.data.reserve.note) {
          resp.valid = false;
          resp.message += "<span class='text-danger'><i class='fa fa-exclamation-circle'></i> Lý do bảo lưu không để trống</span></br>";
        }
        return resp;
      },
      isValidDate(date) {
        let aDate = moment(date, 'YYYY-MM-DD', true);
        return aDate.isValid();
      },
      getReserveType(){
        if(this.data.enroll.start_date >= '2022-09-28'){
          // if(this.data.enroll.reserved_sessions>0 || this.data.reserve.session>=24){
          if(this.data.reserve.session>=24){
            return 1
          }else{
            return 0
          }
        }else{
          if(this.data.temp.max_session === 0){
            return 1
          }else{
            if((this.data.temp.max_session > 0) && (this.data.reserve.session <= this.data.temp.max_session)){
              return 0
            }else{
              return 2
            }
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
</style>
