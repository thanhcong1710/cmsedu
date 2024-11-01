<template>
  <div class="animated fadeIn apax-form" id="class register">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i> <strong>Danh Sách Đăng Ký Lớp Học</strong>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">
                <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
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
                          <!-- <select
                            @change="selectBranch"
                            v-model="filter.branch"
                            :disabled="html.disable.filter.branch"
                            class="filter-selection branch form-control"
                          >
                            <option value="">Vui lòng chọn một trung tâm</option>
                            <option :value="branch.id" v-for="(branch, ind) in cache.branches" :key="ind">{{branch.name}}</option>
                          </select> -->
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
                            <option :value="semester.id" v-for="(semester, ind) in cache.semesters" :key="ind">{{semester.name}}</option>
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
                <div class="col-md-9" id="register-detail">
                  <div v-show="action.loading" class="ajax-load content-loading">
                    <div class="load-wrapper">
                      <div class="loader"></div>
                      <div v-show="action.loading" class="loading-text cssload-loader">{{ html.content.loading }}</div>
                    </div>
                  </div>
                  <div class="class-info" :class="html.class.display.class_info">
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Tên Lớp Học:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.class_name }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Thời Gian:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.class_time }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Giáo Viên:
                        </div>
                        <div class="col-md-9 field-detail" >
                          <span v-html="cache.class_info.teachers_name"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Tổng Số Học Sinh:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.total_students, cache.class_info.class_max_students | totalPerMax }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Thời Gian và Địa Điểm Học:
                        </div>
                        <div class="col-md-9 field-detail" >
                          <span v-html="cache.class_info.time_and_place"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          CM - Giáo Viên Chủ Nhiệm:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.cm_name }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 contracts-list" :class="html.class.display.contracts_list">
                    <div class="row">
                      <div class="col-md-12" >
                        <div :class="html.class.loading ? 'loading' : 'standby'" class="ajax-loader">
                          <img src="/static/img/images/loading/mnl.gif">
                        </div>
                        <div class="controller-bar content-heading">
                          <apaxButton
                            :markup="html.class.button.add_contract"
                            :onClick="callEnrolBoard"
                            :disabled="html.disable.load_contracts"
                            ><i class="fa fa-plus"></i> Thêm học sinh
                          </apaxButton>
                          <apaxButton
                            :markup="html.class.button.up_semester"
                            :onClick="loadUpSemester"
                            :disabled="html.disable.up_semester"
                            ><i class="fa fa-cloud-upload"></i> Chuyển kỳ học
                          </apaxButton>
                          <!-- <apaxButton
                            :markup="html.class.button.class_schedule"
                            :onClick="loadClassSchedule"
                            :disabled="html.disable.class_schedule"
                            ><i class="fa fa-calendar"></i> Xem chỗ trống trong lớp
                          </apaxButton> -->
                        </div>
                        <div class="table-responsive scrollable">
                          <table class="table table-striped table-bordered apax-table">
                            <thead>
                              <tr>
                                <th>STT</th>
                                <th>Tên Học Sinh</th>
                                <th>Mã LMS</th>
                                <th>Nickname</th>
                                <th>Gói Phí</th>
                                <th>Ngày Bắt Đầu Học</th>
                                <th>Ngày Kết Thúc Lớp</th>
                                <th>Số Buổi</th>
                                <th>Loại KH</th>
                                <th>Số Tiền Phải Đóng</th>
                                <th>Số Tiền Đã Đóng</th>
                                <th>Số Buổi Được Học</th>
                                <th>Ngày Học Cuối HS</th>
                                <th>Ngày Dự Tính Cuối HS</th>
                                <th>Trạng Thái Học Sinh</th>
                                <th>EC</th>
                                <th>CM</th>
                                <th>Thao Tác</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(student, ind) in cache.students" v-bind:key="ind">
                                <td align="right">{{ind + 1}}</td>
                                <td>{{ student.student_name }}</td>
                                <td>{{ student.lms_id }}</td>
                                <td>{{ student.student_nick }}</td>
                                <td>{{ student.tuition_fee_name, student.tuition_fee_price | tuitionFeeLabel }}</td>
                                <td>{{ student.start_date | formatDate }}</td>
                                <td>{{ student.end_date | formatDate }}</td>
                                <td>{{ student.total_sessions }}</td>
                                <td>{{ student.customer_type | contractType }}</td>
                                <td>{{ student.tuition_fee_price | formatMoney }}</td>
                                <td>{{ student.charged_total | formatMoney }}</td>
                                <td>{{ student.available_sessions }}</td>
                                <td>{{ student.last_date | formatDate}}</td>
                                <td>{{ student.final_last_date | formatDate}}</td>
                                <td><div v-html="studentStatus(student.enrolment_status)"></div></td>
                                <td>{{ student.ec_name }}</td>
                                <td>{{ student.cm_name }}</td>
                                <td>
                                  <span class="apax-btn print" v-if="student.customer_type != 0" @click="printForm(student)">
                                    <i v-b-tooltip.hover title="Nhấp vào để in bản ghi" class="fa fa-print"></i>
                                  </span>
                                  <span v-if="student.withdraw === 1" class="apax-btn remove" @click="remove(student)">
                                    <i v-b-tooltip.hover title="Nhấp vào để withdraw học sinh này" class="fa fa-remove"></i>
                                  </span>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="hidden">
                        <registerEnrolment :info="html.content.print.enrolment" />
                      </div>
                      <b-modal
                        title="ĐĂNG KÝ LỚP HỌC"
                        class="modal-primary"
                        v-model="html.class.display.modal.register"
                        @cancel="completeAddRegister"
                        :closed="resetEnrolBoard"
                        ok-variant="primary"
                        id="add-contract"
                      >
                        <div class="col-md-12">
                          <div class="search-contracts buttons-bar">
                            <label class="filter-label control-label">Tìm Kiếm</label><br/>
                            <search
                              :label="html.content.label.search"
                              :onSearch="searching"
                              :placeholder="html.content.placeholder.search"
                            >
                            </search>
                            <apaxButton
                              :markup="html.class.button.save_contracts"
                              @click="addContracts"
                              :disabled="html.disable.save_contracts"
                              ><i class="fa fa-save"></i> Thêm học sinh
                            </apaxButton>
                            <div class="empty-slots">Số chỗ còn trống trong lớp hiện tại là: {{ this.cache.available }}</div>
                          </div>
                          <div class="table-responsive scrollable">
                            <div v-show="action.loading_contracts" class="ajax-load content-loading">
                              <div class="load-wrapper">
                                <div class="loader"></div>
                                <div v-show="action.loading_contracts" class="loading-text cssload-loader">{{ html.content.loading }}</div>
                              </div>
                            </div>
                            <table class="table table-striped table-bordered apax-table contracts-list">
                              <thead>
                                <tr>
                                  <th align="center">
                                    <b-form-checkbox class="check-item" id="select-all" v-model="selectAll" ></b-form-checkbox>
                                  </th>
                                  <th>STT</th>
                                  <th>Tên Học Sinh</th>
                                  <th>Mã LMS</th>
                                  <th>Mã Effect</th>
                                  <th>Tên tiếng Anh</th>
                                  <th>Trường học</th>
                                  <th>School Grade</th>
                                  <th>Số điện thoại</th>
                                  <th>Ngày dự kiến học</th>
                                  <th>Ngày bắt đầu học</th>
                                  <th>Loại khách hàng</th>
                                  <th>Gói phí</th>
                                  <th>Số buổi</th>
                                  <th>Số tiền đã đóng</th>
                                  <th>EC</th>
                                  <th>CM</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="(contract, ind) in cache.contracts" :key="ind">
                                  <td align="center" :class="readyNick(contract)">
                                    <b-form-checkbox :disabled="isEmptyNick(contract)" class="check-item" v-model="cache.checked_list" :value="contract.contract_id"  @change.native="checkItem(contract)" number></b-form-checkbox>
                                  </td>
                                  <td align="right" :class="readyNick(contract)">{{ ind + 1 }}</td>
                                  <td :class="readyNick(contract)">{{ contract.student_name }}</td>
                                  <td :class="readyNick(contract)">{{ contract.student_lms_id }}</td>
                                  <td :class="readyNick(contract)">{{ contract.student_accounting_id }}</td>
                                  <td :class="readyNick(contract)">
                                    <input type="textbox" placeholder="Chưa có Nickname" :value="contract.student_nick" class="contract-nick" :class="emptyNick(contract.student_nick)" @change="checkNick(contract, $event.target.value)" :disabled="disableEmptyNick(contract.student_nick)" />
                                  </td>
                                  <td :class="readyNick(contract)">{{ contract.student_school }}</td>
                                  <td :class="readyNick(contract)">{{ contract.student_school_grade }}</td>
                                  <td :class="readyNick(contract)">{{ contract.student_phone }}</td>
                                  <td :class="readyNick(contract)">{{ contract.contract_start_date }}</td>
                                  <td :class="readyNick(contract)">
                                    <select :blur="onBlurCheckBox(contract)"
                                      :disabled="isEmptyNick(contract)"
                                      v-model="contract.class_date"
                                      class="selection class-date form-control"
                                    >
                                      <option value="">Chọn ngày bắt đầu học</option>
                                      <option :value="`${class_date.cjrn_id}~${class_date.cjrn_classdate}`" v-for="(class_date, ind) in filterStartDate(contract, cache.class_dates)" :key="ind">{{class_date.cjrn_classdate}}</option>
                                    </select>
                                  </td>
                                  <td :class="readyNick(contract)">{{ contract.customer_type | contractType }}</td>
                                  <td :class="readyNick(contract)">{{ contract.tuition_fee_name, contract.tuition_fee_price | tuitionFeeLabel }}</td>
                                  <td :class="readyNick(contract)">{{ contract.available_sessions }}</td>
                                  <td :class="readyNick(contract)">{{ contract.charged_total | formatMoney }}</td>
                                  <td :class="readyNick(contract)">{{ contract.ec_name }}</td>
                                  <td :class="readyNick(contract)">{{ contract.cm_name }}</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <div class="text-center">
                            <nav aria-label="Page navigation">
                              <paging
                                :rootLink="pagination.url"
                                :id="pagination.id"
                                :listStyle="pagination.style"
                                :customClass="pagination.class"
                                :firstPage="pagination.spage"
                                :previousPage="pagination.ppage"
                                :nextPage="pagination.npage"
                                :lastPage="pagination.lpage"
                                :currentPage="pagination.cpage"
                                :pagesItems="pagination.total"
                                :pagesLimit="pagination.limit"
                                :pageList="pagination.pages"
                                :routing="redirect">
                              </paging>
                            </nav>
                          </div>
                        </div>
                      </b-modal>
                      <b-modal
                        title="CHUYỂN KỲ HỌC"
                        class="modal-success"
                        v-model="html.class.display.modal.semester"
                        @ok="completeUpSemester"
                        ok-variant="success"
                        id="up-semester"
                      >
                        <div class="col-lg-12">
                          <div class="row">
                            <div v-show="html.loading.up_action" class="ajax-load content-loading">
                              <div class="load-wrapper">
                                <div class="loader"></div>
                                <div v-show="html.loading.up_action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
                              </div>
                            </div>
                            <div class="col-md-3" id="semester-filter">
                              <div class="col-md-12" :class="html.class.display.filter.semester">
                                <div class="row">
                                  <div class="col-md-4 filter-label">
                                    <label class="filter-label control-label">Kỳ Học: </label>
                                  </div>
                                  <div class="col-md-8 filter-selection">
                                    <div class="row form-group">
                                      <select
                                        @change="selectUpSemester"
                                        v-model="filter.up_semester"
                                        class="filter-selection semester form-control"
                                      >
                                        <option value="">Vui lòng chọn kỳ học</option>
                                        <option :value="up_semester.id" v-for="(up_semester, ind) in cache.up_semesters" :key="ind">{{up_semester.name}}</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12 filter-classes">
                                <div class="row">
                                  <label class="filter-label control-label">Lớp Học: </label>
                                  <div class="row tree-view">
                                    <tree
                                      :data="cache.up_classes"
                                      text-field-name="text"
                                      allow-batch
                                      @item-click="selectUpClass"
                                    >
                                    </tree>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-9" id="semester-detail">
                              <div v-show="html.loading.up_class_action" class="ajax-load content-loading">
                                <div class="load-wrapper">
                                  <div class="loader"></div>
                                  <div v-show="html.loading.up_class_action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
                                </div>
                              </div>
                              <div class="class-info">
                                <div class="col-md-12" >
                                  <div class="row">
                                    <div class="col-md-3 text-right field-label" >
                                      Tên Lớp Học:
                                    </div>
                                    <div class="col-md-9 field-detail" >
                                      {{ cache.up_class_info.class_name }}
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" >
                                  <div class="row">
                                    <div class="col-md-3 text-right field-label" >
                                      Thời Gian:
                                    </div>
                                    <div class="col-md-9 field-detail" >
                                      {{ cache.up_class_info.class_time }}
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" >
                                  <div class="row">
                                    <div class="col-md-3 text-right field-label" >
                                      Giáo Viên:
                                    </div>
                                    <div class="col-md-9 field-detail" >
                                      <span v-html="cache.up_class_info.teachers_name"></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" >
                                  <div class="row">
                                    <div class="col-md-3 text-right field-label" >
                                      Tổng Số Học Sinh:
                                    </div>
                                    <div class="col-md-9 field-detail" >
                                      {{ cache.up_class_info.total_students, cache.up_class_info.class_max_students | totalPerMax }}
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" >
                                  <div class="row">
                                    <div class="col-md-3 text-right field-label" >
                                      Thời Gian và Địa Điểm Học:
                                    </div>
                                    <div class="col-md-9 field-detail" >
                                      <span v-html="cache.up_class_info.time_and_place"></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" >
                                  <div class="row">
                                    <div class="col-md-3 text-right field-label" >
                                      CM - Giáo Viên Chủ Nhiệm:
                                    </div>
                                    <div class="col-md-9 field-detail" >
                                      {{ cache.up_class_info.cm_name }}
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="controller-bar content-heading">
                                <apaxButton
                                  :markup="html.class.button.up_semester_students"
                                  :onClick="upSemesterStudents"
                                  :disabled="html.disable.up_semester_students"
                                  ><i class="fa fa-plus"></i> Chuyển kỳ cho học sinh
                                </apaxButton>
                              </div>
                              <div class="table-responsive scrollable">
                                <table class="table table-striped table-bordered apax-table">
                                  <thead>
                                    <tr>
                                      <th align="center">
                                        <b-form-checkbox class="check-item" id="check-up-all" v-model="checkUpAll" ></b-form-checkbox>
                                      </th>
                                      <th>Tên Học Sinh</th>
                                      <th>Nickname</th>
                                      <th>Gói Phí</th>
                                      <th>Ngày Bắt Đầu Học</th>
                                      <th>Ngày Kết Thúc Lớp</th>
                                      <th>Số Buổi</th>
                                      <th>Loại KH</th>
                                      <th>Số Tiền Phải Đóng</th>
                                      <th>Số Tiền Đã Đóng</th>
                                      <th>Số Buổi Được Học</th>
                                      <th>Ngày Học Cuối HS</th>
                                      <th>Ngày Dự Tính Cuối HS</th>
                                      <th>Trạng Thái Học Sinh</th>
                                      <th>EC</th>
                                      <th>CM</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr v-for="(up_student, ind) in cache.up_students" v-bind:key="ind">
                                      <td align="center">
                                        <b-form-checkbox class="check-item" v-model="cache.checked_up_list" :value="up_student.student_id"  @change.native="checkUpItem(up_student)" number></b-form-checkbox>
                                      </td>
                                      <td>{{ up_student.student_name }}</td>
                                      <td>{{ up_student.student_nick }}</td>
                                      <td>{{ up_student.tuition_fee_name, up_student.tuition_fee_price | tuitionFeeLabel }}</td>
                                      <td>{{ up_student.start_date | formatDate }}</td>
                                      <td>{{ up_student.end_date | formatDate }}</td>
                                      <td>{{ up_student.total_sessions }}</td>
                                      <td>{{ up_student.customer_type | contractType }}</td>
                                      <td>{{ up_student.tuition_fee_price | formatMoney }}</td>
                                      <td>{{ up_student.charged_total | formatMoney }}</td>
                                      <td>{{ up_student.available_sessions }}</td>
                                      <td>{{ up_student.last_date | formatDate}}</td>
                                      <td>{{ up_student.final_last_date | formatDate}}</td>
                                      <td><div v-html="studentStatus(up_student.enrolment_status)"></div></td>
                                      <td>{{ up_student.ec_name }}</td>
                                      <td>{{ up_student.cm_name }}</td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </b-modal>
                      <b-modal
                        title="Bảng Các Chỗ Trống Trong Lớp"
                        class="modal-success"
                        v-model="html.class.display.modal.schedule"
                        @ok="html.class.display.modal.schedule = false"
                        ok-variant="success"
                        id="class-schedule"
                      >
                        <div class="col-lg-12">
                          <table class="table table-striped table-bordered apax-table">
                            <thead>
                              <tr>
                                <th>Ngày / Chỗ</th>
                                <th class="schedule-block-shell">1</th>
                                <th class="schedule-block-shell">2</th>
                                <th class="schedule-block-shell">3</th>
                                <th class="schedule-block-shell">4</th>
                                <th class="schedule-block-shell">5</th>
                                <th class="schedule-block-shell">6</th>
                                <th class="schedule-block-shell">7</th>
                                <th class="schedule-block-shell">8</th>
                                <th class="schedule-block-shell">9</th>
                                <th class="schedule-block-shell">10</th>
                                <th class="schedule-block-shell">11</th>
                                <th class="schedule-block-shell">12</th>
                                <th class="schedule-block-shell">13</th>
                                <th class="schedule-block-shell">14</th>
                                <th class="schedule-block-shell">15</th>
                                <th class="schedule-block-shell">16</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(date, ind) in cache.schedule" v-bind:key="ind">
                                <td>{{ date.date }}</td>
                                <td :class="classBlock(date.blocks, 1)" v-html="showBlock(date.blocks, 1)"></td>
                                <td :class="classBlock(date.blocks, 2)" v-html="showBlock(date.blocks, 2)"></td>
                                <td :class="classBlock(date.blocks, 3)" v-html="showBlock(date.blocks, 3)"></td>
                                <td :class="classBlock(date.blocks, 4)" v-html="showBlock(date.blocks, 4)"></td>
                                <td :class="classBlock(date.blocks, 5)" v-html="showBlock(date.blocks, 5)"></td>
                                <td :class="classBlock(date.blocks, 6)" v-html="showBlock(date.blocks, 6)"></td>
                                <td :class="classBlock(date.blocks, 7)" v-html="showBlock(date.blocks, 7)"></td>
                                <td :class="classBlock(date.blocks, 8)" v-html="showBlock(date.blocks, 8)"></td>
                                <td :class="classBlock(date.blocks, 9)" v-html="showBlock(date.blocks, 9)"></td>
                                <td :class="classBlock(date.blocks, 10)" v-html="showBlock(date.blocks, 10)"></td>
                                <td :class="classBlock(date.blocks, 11)" v-html="showBlock(date.blocks, 11)"></td>
                                <td :class="classBlock(date.blocks, 12)" v-html="showBlock(date.blocks, 12)"></td>
                                <td :class="classBlock(date.blocks, 13)" v-html="showBlock(date.blocks, 13)"></td>
                                <td :class="classBlock(date.blocks, 14)" v-html="showBlock(date.blocks, 14)"></td>
                                <td :class="classBlock(date.blocks, 15)" v-html="showBlock(date.blocks, 15)"></td>
                                <td :class="classBlock(date.blocks, 16)" v-html="showBlock(date.blocks, 16)"></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </b-modal>
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

import md5 from 'js-md5'
import moment from 'moment'
import tree from 'vue-jstree'

import u from '../../../utilities/utility'
import search from '../../../components/Search'
import apaxButton from '../../../components/Button'
import paging from '../../../components/Pagination'
import searchBranch from '../../../components/Selection'
import registerEnrolment from '../../base/prints/register_enrolment'

export default {
  name: 'Register-Add',
  components: {
    tree,
    search,
    paging,
    apaxButton,
    searchBranch,
    registerEnrolment
  },
  data () {
    return {
      item: {},
      html: {
        class: {
          loading: false,
          button: {
            up_semester_students: 'success',
            class_schedule: 'error',
            save_contracts: 'error',
            add_contract: 'primary',
            up_semester: 'success',
            print: 'success'
          },
          display: {
            modal: {
              schedule: false,
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
            }
          }
        },
        loading: {
          content: 'Đang tải dữ liệu...',
          up_class_action: false,
          up_action: false,
          action: false
        },
        content: {
          loading: 'Đang tải dữ liệu...',
          label: {
            search: 'Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh'
          },
          placeholder: {
            search: 'Từ khóa tìm kiếm'
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
        loading_contracts: false,
      },
      url: {
        api: '/api/enrolments/',
        nick: '/api/enrolments/nick/',
        class: '/api/enrolments/class/',
        classes: '/api/enrolments/classes/',
        branches: '/api/enrolments/branches',
        withdraw: '/api/enrolments/withdraw/',
        semesters: '/api/enrolments/semesters',
        contracts: '/api/enrolments/contracts/',
        up_contracts: '/api/enrolments/semester/up/contracts/'
      },
      filter: {
        class: '',
        branch: '',
        keyword: '',
        semester: '',
        up_class: '',
        up_semester: ''
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
        schedule: [],
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
        up_students: [],
        class_dates: [],
        up_class_dates: [],
        checked_list: [],
        checked_up_list: [],
        check_all: '',
        check_up_all: '',
        available: 0,
        validated: 0,
        up_available: 0
      },
      class_date: {},
      up_class_date: {},
      pagination: {
        url: '',
        id: '',
        style: 'line',
        class: '',
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
        by: 's.id',
        to: 'DESC'
      },
      temp: [],
      temp_selected_list: [],
      temp_selected_up_list: [],
      session: u.session()
    }
  },
  filters:{

  },
  computed:{
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
        this.temp_selected_list = selected_list
      }
    },
    checkUpAll: {
      get: function () {
        return parseInt(this.cache.checked_up_list.length) === parseInt(this.cache.up_students.length)
      },
      set: function (value) {
        const selected_up_list = []
        if (value) {
          this.cache.up_students.forEach((up_student) => {
            selected_up_list.push(up_student.student_id)
          })
        }
        this.cache.checked_up_list = selected_up_list
        this.temp_selected_up_list = selected_up_list
      }
    }
  },
  created () {
    this.start()
  },
  filters: {
    filterTuitionFee: (name, price) => price && price > 1000 ? `${name} - ${this.filterFormatCurrency(price)}` : name,
    filterFormatCurrency: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ'
  },
  watch: {
    temp_selected_list() {
      if (this.temp_selected_list.length > 0) {
        if (this.cache.available > -1) {
          this.html.disable.save_contracts = false
        } else {
          alert('Lớp hiện đã đầy 16/16 nên không còn chỗ trống nào để xếp thêm học sinh nữa')
        }
      } else {
        this.html.disable.save_contracts = true
      }
    }
  },
  methods: {
    link(semester = false) {
      let resp = ''
      this.filter.branch = this.cache.branch
      if (semester) {
        const search = u.jss({
          class: this.cache.class,
          branch: this.cache.branch,
          semester: this.cache.semester
        })
        resp = `${this.url.up_contracts}${search}`
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
    showBlock(blocks, index) {
      let resp = ''
      if (blocks.length && blocks[index]) {
        const cnt = blocks[index]
        const dat = cnt.split('˜')
        const obj = {
          id: dat[0],
          type: dat[1],
          name: dat[2],
          nick: dat[3],
          sessions: dat[4],
          start_date: dat[5],
          final_date: dat[6],
          status: 'Ok'
        }
        resp = `<div id="${obj.id}" class="block ${obj.type ? `type_${obj.type}` : 'empty'}"
                v-b-tooltip.hover :title="${obj.nick} - ${obj.name} (${obj.status})"
                ></div>`
      }
      return resp
    },
    classBlock(blocks, index) {
      let resp = 'schedule block'
      if (blocks.length && blocks[index]) {
        const cnt = blocks[index]
        const dat = cnt.split('˜')
        const obj = {
          id: dat[0],
          type: dat[1],
          name: dat[2],
          nick: dat[3],
          sessions: dat[4],
          start_date: dat[5],
          final_date: dat[6],
          status: 'Ok'
        }
        resp = `schedule block ${obj.type ? `type_${obj.type}` : 'empty'}`
      }
      return resp
    },
    studentStatus(v) {
      return parseInt(v) === 1 ? '<div class="success-label apax-label">Active</div>' : parseInt(v) === 2 ? '<div class="primary-label apax-label">Transfering</div>' : '<div class="alert-label apax-label">Withdraw</div>'
    },
    filterStartDate(student, class_dates) {
      let start_dates = class_dates
      const resp = []
      if (start_dates.length) {
        start_dates.map(item => {
          if (this.moment(item.cjrn_classdate).isSameOrAfter(student.contract_start_date)) {
            resp.push(item)
          }
          return item
        })
      }
      return resp
    },
    load(data, semester = false){
      if (semester) {
        u.log('Response Data', data)
        this.cache.up_semesters = data
        this.html.class.display.modal.semester = true
      } else {
        this.cache.contracts = data.contracts
        this.cache.class_dates = data.class_dates
        this.pagination = data.pagination
        this.cache.available = data.available
        this.cache.validated = data.available 
        this.cache.checked_list = []
        this.html.class.display.modal.register = true
      }
      this.action.loading = false
      this.action.loading_contracts = false
    },
    remove(student) {
      if (!this.html.class.display.modal.semester) {
        this.action.loading = true
      }
      u.g(`${this.url.withdraw}${student.enrolment_id}`)
      .then(response => {
        this.selectClass(this.cache.selected_class)
      }).catch(e => u.log('Exeption', e))
    },
    callEnrolBoard() {
      this.resetEnrolBoard()
      this.loadContracts()
    },
    searching(word) {
      const key = word
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
      u.g(this.link())
      .then(response => {
        const data = response
        this.load(data)
        setTimeout(() => {
          this.action.loading = false
          this.action.loading_contracts = false
        }, data.duration)
      }).catch(e => u.log('Exeption', e))
    },
    start() {
      if (u.authorized()) {
        this.html.loading.action = true
        u.g(this.url.branches)
        .then(response => {
          const data = response
          this.cache.branches = data
          this.cache.branch = ''
          this.html.class.display.filter.branch = 'display'
          this.html.class.display.filter.semester = 'display'
          this.html.disable.filter.branch = false
          this.html.loading.action = false
        }).catch(e => u.log('Exeption', e))
      } else {
        this.cache.branch = this.session.user.branch_id
        this.loadSemesters()
      }
    },
    loading() {
    },
    extract() {
    },
    loadContracts() {
      this.html.class.display.modal.semester = false
      if (!this.html.class.display.modal.register) {
        this.action.loading = true
      } else {
        this.action.loading_contracts = true
      }
      u.g(this.link())
      .then(response => {
        const data = response
        this.load(data)
        setTimeout(() => {
          this.action.loading = false
          this.action.loading_contracts = false
        }, data.duration)
      }).catch(e => u.log('Exeption', e))
    },
    loadUpSemester() {
      this.html.loading.up_action = true
      this.html.class.display.modal.register = false
      if (!this.html.class.display.modal.semester) {
        this.action.loading = true
      }
      u.g(this.link(1))
      .then(response => {
        const data = response
        this.load(data, 1)
        setTimeout(() => {
          this.action.loading = false
          this.html.loading.up_action = false
        }, data.duration)
      }).catch(e => u.log('Exeption', e))
    },
    printForm(data) {
      localStorage.setItem(`e_${data.enrolment_id}`, JSON.stringify(data))
      window.open(`/print/register/${data.enrolment_id}`,'_blank')
    },
    loadSemesters() {
      this.html.loading.action = true
      u.g(this.url.semesters)
        .then(response => {
          const data = response
          this.cache.semesters = data
          this.filter.semester = ''
          this.html.class.display.filter.semester = 'display'
          this.html.disable.filter.semester = false
          this.html.loading.action = false
        }).catch(e => u.log('Exeption', e))
    },
    selectBranch(data) {
      this.cache.branch = data.id
      this.filter.branch = data.id
      this.loadSemesters()
    },
    selectSemester() {
      this.html.loading.action = true
      this.cache.semester = this.filter.semester
      u.log('Semester', this.filter.semester)
      u.g(`${this.url.classes}${this.cache.branch}/${this.cache.semester}`)
        .then(response => {
          const data = response
          this.cache.classes = data
          this.filter.class = ''
          this.html.class.display.filter.classes = 'display'
          this.html.loading.action = false
        }).catch(e => u.log('Exeption', e))
    },
    selectUpSemester(selected_up_class) {
      this.html.loading.up_action = true
      this.cache.up_semester = this.filter.up_semester
      // u.log('Semester', this.filter.up_semester)
      u.g(`${this.url.classes}${this.cache.branch}/${this.cache.up_semester}`)
        .then(response => {
          const data = response
          this.cache.up_classes = data
          this.filter.up_class = ''
          this.html.class.display.filter.up_classes = 'display'
          this.html.loading.up_action = false
        }).catch(e => u.log('Exeption', e))
    },
    selectClass(selected_class) {
        u.log('check role',this.checkRole())
      if (selected_class.model.item_type === 'class') {
        // u.log('Program', selected_class.model)
        this.cache.selected_class = selected_class
        this.action.loading = true
        this.cache.class = selected_class.model.item_id
        this.filter.class = this.cache.class
        u.g(`${this.url.class}${this.filter.class}`)
        .then(response => {
            const data = response
            this.cache.class_info = data.class
            this.cache.students = data.students
            data.students.map(std => {
              this.cache.nicks.push(std.student_nick)
              return std
            })

            if(this.checkRole()){
                this.html.disable.up_semester = false

                if (data.class.cm_id && parseInt(data.class.cm_status, 10) > 0)
                    this.html.disable.load_contracts = false
                else
                    this.html.disable.load_contracts = true
            }else{
                this.html.disable.up_semester = true
            }

            this.html.disable.save_contracts = true

            this.html.class.display.class_info = 'display'
            this.html.class.display.contracts_list = 'display'
            this.action.loading = false

        }).catch(e => u.log('Exeption', e))
        // this.loadSchedule(this.cache.class)
      } else {
        u.log('Parent', selected_class.model)
        this.cache.selected_class = {}
        this.action.loading = false
        this.cache.class = ''
        this.filter.class = ''
        this.cache.class_info = {}
        this.cache.students = []
        this.cache.nicks = []
        this.html.disable.load_contracts = true
        this.html.disable.save_contracts = true
        this.html.disable.up_semester = true
        this.html.class.display.class_info = 'display'
        this.html.class.display.contracts_list = 'display'
      }
    },
    selectUpClass(selected_up_class) {
      if (selected_up_class.model.item_type === 'class') {
        // u.log('Program', selected_up_class.model)
        this.html.loading.up_class_action = true
        this.cache.selected_up_class = selected_up_class
        this.cache.up_class = selected_up_class.model.item_id
        this.filter.up_class = this.cache.up_class
        u.g(`${this.url.class}${this.filter.class}`)
        .then(response => {
            const data = response
            this.cache.up_class_info = data.class
            this.cache.up_students = data.students
            if (data.students.length) {
              this.html.disable.up_semester_students = false
            }
            u.log('Class Data', this.cache.up_class_info)
            this.html.class.display.up_class_info = 'display'
            this.html.class.display.up_contracts_list = 'display'
            this.html.loading.up_class_action = false
          }).catch(e => u.log('Exeption', e))
      }
    },
    upSemesterStudents() {
      const selected_up_students = this.cache.up_students.filter(item => this.cache.checked_up_list.indexOf(item.student_id) > -1)
      const data = {
        class_id: this.cache.up_class,
        students: selected_up_students
      }
      u.log('Selected Students to up semester', selected_up_students)
    },
    loadSchedule(class_id) {
      u.g(`${this.url.class}load/schedule/${class_id}`)
      .then(response => {
        if (response.length) {
          // u.log('response', response)
          this.cache.schedule = response
          this.html.disable.class_schedule = false
        }
      })
    },
    loadClassSchedule() {
      this.html.class.display.modal.schedule = true
    },
    activeSearch(event) {
       if(event.key == "Enter"){
         if (this.cache.branch && this.cache.semester && this.cache.class && this.html.class.display.modal.register == true) {
          this.searching(this.keyword)
         }
       }
    },
    emptyNick(nick) {
      return !nick || nick.length === 0 ? 'input-nick' : ''
    },
    disableEmptyNick(nick) {
      return !nick || nick.length === 0 ? false : true
    },
    checkNick(contract, nick) {
      // u.log('contract', nick, contract)
      if (nick && nick.length > 1) {
        const stduent_nick = nick.toUpperCase()
        u.log('Nicks', this.cache.nicks)
        if (this.cache.nicks.indexOf(stduent_nick) > -1) {
          alert(`Nick name: '${stduent_nick}' đã tồn tại, xin vui lòng chọn một nick khác!`)
        } else {
          u.g(`${this.url.nick}${this.cache.class}/${contract.student_id}/${stduent_nick}`)
          .then(response => {
            const data = response
            if (!data.update) {
              contract.student_nick = ''
              alert(`Nick name: '${stduent_nick}' đã tồn tại, xin vui lòng chọn một nick khác!`)
            } else {
              contract.student_nick = stduent_nick
            }
          }).catch(e => u.log('Exeption', e))
        }
      }
    },
    checkDuplicateNick(contract) {
      if (contract.student_nick && contract.student_nick.length > 1) {
        const stduent_nick = contract.student_nick.toUpperCase()
        u.g(`${this.url.nick}${this.cache.class}/${contract.student_id}/${stduent_nick}`)
        .then(response => {
          const data = response
          if (!data.update) {
            alert(`Nick name: '${stduent_nick}' đã tồn tại trong lớp, xin vui lòng kiểm tra lại!`)
          } else {
            this.html.disable.save_contracts = false
          }
        }).catch(e => u.log('Exeption', e))
      }
    },
    checkItem(contract) {
      // u.log('Check contract', contract)
      if (contract.student_nick == null || contract.student_nick == '') {
        alert(`Vui lòng nhập nick cho học sinh này!`)
        contract.student_nick = ''
      } else {
        // this.checkDuplicateNick(contract)
        this.html.disable.save_contracts = false
      }
      setTimeout(() => {
        this.cache.available = this.cache.validated - this.cache.checked_list.length
        if (this.cache.available < 0) {
          alert('Lớp đã hết chỗ!')
        }
      }, 100)
    },
    checkUpItem(up_student) {
      // u.log('Check student', up_student)
      setTimeout(() => {
        
      }, 100)
    },
    isEmptyNick(contract) {
      let resp = false
      if (this.cache.checked_list.indexOf(contract.contract_id) === -1 && this.cache.available <= 0) {
        resp = true
      }
      if (!contract.student_nick || contract.student_nick.length === 0) {
        resp = true
      }
      return resp
    },
    readyNick(contract) {
      return !contract.student_nick || contract.student_nick.length === 0 ? 'nick-empty' : ''
    },
    addContracts() {
      if (this.cache.available < 0) {
        alert('Lớp hiện đã đủ 16/16 chỗ, không còn có thể xếp thêm học sinh được nữa.')
      } else {
        const selected_contracts = this.cache.contracts.filter(item => this.cache.checked_list.indexOf(item.contract_id) > -1)
        // u.log('PPPPP',parseInt(selected_contracts.length - parseInt(this.cache.available)),selected_contracts.length)
        if (parseInt(this.cache.available) > 0 && parseInt(selected_contracts.length - parseInt(this.cache.available)) > 0) {
          alert(`Không thể xếp lớp cho ${selected_contracts.length} học sinh đang chọn, bởi vì nó lớn hơn số chỗ hiện tại đang còn trống trong lớp là ${this.cache.available}.`)
        } else if (selected_contracts.length == 0) {
          alert(`Xin hãy tích chọn học sinh để xếp lớp trước!`)
        } else {
          let checked = true
          selected_contracts.every(selected_contract => {
            if (!selected_contract.class_date || selected_contract.class_date.toString() === '') {
              alert(`Học sinh đã chọn ${selected_contract.student_name} chưa được chỉ định ngày bắt đầu học, vui lòng chọn ngày bắt đầu học cho em ${selected_contract.student_name}`)
              checked = false
              return selected_contract
            }
          })
          if (checked) {
            this.saveContracts(selected_contracts)
          }
        }
      }      
    },
    saveContracts(contracts) {
      const data = {
        contracts,
        class: this.cache.class,
        branch: this.cache.branch,
        program: this.cache.program,
        semester: this.cache.semester,
        class_info: this.cache.class_info
      }
      this.html.disable.save_contracts = true
      u.p('/api/enrolments/add', data)
      .then(response => {
        const names = []
        contracts.forEach(contract => {
          names.push(contract.student_name)
        })
        alert(`Đã xếp lớp thành công cho ${parseInt(names.length)} học sinh: \n${names.join(', ')}. \n`)
        this.cache.contracts = []
        this.cache.class_dates = []
        this.cache.available = 0
        this.cache.validated = 0
        this.cache.keyword = ''
        this.html.class.display.modal.register = false
        u.log('Cache Class: ', this.cache.class_info)
        this.selectClass(this.cache.selected_class)
      }).catch(e => console.log(e));
    },
    completeAddRegister() {
      this.resetEnrolBoard()
    },
    resetEnrolBoard() {
      this.html.disable.save_contracts = true
      this.cache.contracts = []
      this.cache.class_dates = []
      this.cache.available = 0
      this.cache.validated = 0
      this.cache.keyword = ''
    },
    completeUpSemester() {
      this.html.disable.up_semester_students= true
    },
    onBlurCheckBox(contract) {
      // u.log('contract', contract)
    },
    onDrawDate (e) {
      let date = e.date
      if (this.latest_date > date.getTime()) {
        e.allowSelect = false
      }
    },
    checkRole(){
        const deniedRoles = [u.r.ec, u.r.ec_leader];
        return !(deniedRoles.indexOf(parseInt(this.session.user.role_id)) > -1);
    }
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
  padding: 6px 6px 0 6px!important;
}
.contracts-list select.class-date {
  padding: 0 5px;
  border: 0.5px dashed #add8ff;
  font-size: 10px;
  width: 140px;
  margin: 0!important;
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
  float:left;
}
.buttons-bar .apax-button {
  float: left;
  height: 35px;
  font-size: 12px;
  padding: 3px 15px 0;
  margin: 0 0 0 20px;
}
#add-contract___BV_modal_footer_ button:first-child {
  display: none!important;
}
.apax-table tr .schedule-block-shell {
  padding: none;
}
.empty-slots {
  padding: 10px;
  margin: 0 0 0 10px;
  float: left;
  font-weight: bold;
  font-style: italic;
}

</style>
