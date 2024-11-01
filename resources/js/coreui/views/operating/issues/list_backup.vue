<template>
  <div
    class="animated fadeIn apax-form"
    id="class register"
    :key="$route.params.type"
  >
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book" /> <strong>Đánh giá học sinh</strong>
            <div class="card-actions">
              <a
                href="skype:thanhcong1710?chat"
                target="_blank"
              >
                <small className="text-muted"><i class="fa fa-skype" /></small>
              </a>
            </div>
          </div>
          <div class="content-detail">
            <div class="col-lg-12">
              <div class="row">
                <div
                  class="col-md-3"
                  id="register-filter"
                >
                  <div
                    class="col-md-12"
                    :class="html.class.display.filter.branch"
                  >
                    <div class="row">
                      <div class="col-md-4 filter-label">
                        <label class="filter-label control-label">Branch: </label>
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
                            :on-select="selectBranch"
                            :options="cache.branches"
                            :disabled="html.disable.filter.branch"
                            placeholder="Vui lòng chọn một trung tâm"
                          />
                          <br>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="col-md-12"
                    :class="html.class.display.filter.semester"
                  >
                    <div class="row">
                      <div class="col-md-4 filter-label">
                        <label class="filter-label control-label">Học kỳ: </label>
                      </div>
                      <div class="col-md-8 filter-selection">
                        <div class="row form-group">
                          <select
                            @change="selectSemester"
                            v-model="filter.semester"
                            :disabled="html.disable.filter.semester"
                            class="filter-selection semester form-control"
                          >
                            <option value="">
                              Please choose one semester
                            </option>
                            <option
                              :value="semester.id"
                              v-for="(semester, ind) in cache.semesters"
                              :key="ind"
                            >
                              {{ semester.name }}
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="col-md-12 filter-classes"
                    :class="html.class.display.filter.classes"
                  >
                    <div class="row">
                      <label class="filter-label control-label">Lớp học: </label>
                      <div class="row tree-view">
                        <tree
                          :data="cache.classes"
                          text-field-name="text"
                          allow-batch
                          @item-click="selectClass"
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="col-md-9"
                  id="register-detail"
                >
                  <div
                    v-show="action.loading"
                    class="ajax-load content-loading"
                  >
                    <div class="load-wrapper">
                      <div class="loader" />
                      <div
                        v-show="action.loading"
                        class="loading-text cssload-loader"
                      >
                        {{
                          html.content.loading }}
                      </div>
                    </div>
                  </div>
                  <div
                    class="class-info"
                    :class="html.class.display.class_info"
                  >
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">
                          Lớp học:
                        </div>
                        <div class="col-md-9 field-detail">
                          {{ cache.class_info.class_name }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">
                          Giáo viên:
                        </div>
                        <div class="col-md-9 field-detail">
                          <span v-html="cache.class_info.teacher_name" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">
                          Số lượng học sinh:
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
                          Giờ học:
                        </div>
                        <div class="col-md-9 field-detail">
                          <span v-html="cache.class_info.shifts_name" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3 text-right field-label">
                          CM:
                        </div>
                        <div class="col-md-9 field-detail">
                          <div class="row">
                            <div class="col-md-5">
                              <input
                                type="text"
                                class="form-control"
                                :value="cache.class_info.cm_name"
                                readonly
                              >
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="col-md-12 contracts-list"
                    :class="html.class.display.contracts_list"
                  >
                    <div class="row">
                      <div
                        class="col-md-4"
                        v-if="$route.params.type === 'date'"
                      >
                        <class-select-date
                          :class-id="cache.class"
                          v-model="selectedDate"
                          @input="handleSelectedDate"
                        />
                      </div>
                      <div
                        class="col-md-12"
                        style="margin-left: -10px"
                      >
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
                                <th>STT</th>
                                <th>Tên học sinh</th>
                                <th>Mã CMS</th>
                                <th>Phụ huynh</th>
                                <th>Số điện thoại phụ huynh</th>
                                <th>Nhận xét chung</th>
                                <th style="min-width: 67px;">
                                  Hành động
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="(student, ind) in cache.students"
                                :key="ind"
                              >
                                <td align="right">
                                  {{ ind + 1 }}
                                </td>
                                <td>
                                  {{ student.student_name }} <br>{{ student.student_nick
                                  }}
                                </td>
                                <td>{{ student.crm_id }}</td>
                                <td>{{ student.gud_name1 }}</td>
                                <td>{{ student.gud_mobile1 }}</td>
                                <td>{{ student.feedback_comment }}</td>
                                <td>
                                  <!--                                  <button-->
                                  <!--                                    title="Click to add new"-->
                                  <!--                                    class="btn btn-sm btn-success"-->
                                  <!--                                    @click="addIssue(student.student_id)"-->
                                  <!--                                  >-->
                                  <!--                                    <i class="fa fa-plus" />-->
                                  <!--                                  </button>-->
                                  <button
                                    class="btn btn-sm btn-success"
                                    @click="handleCreateFeedback(student)"
                                  >
                                    <i class="fa fa-plus" />
                                  </button>
                                  <button
                                    class="btn btn-sm btn-info"
                                    @click="handleCreateFeedback(student, true)"
                                  >
                                    <i class="fa fa-eye" />
                                  </button>
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
          <add-feedback
            :type="dialogFeedback.type"
            :display-modal="dialogFeedback.display"
            :student="dialogFeedback.student"
            :program="dialogFeedback.program"
            :clazz="dialogFeedback.clazz"
            :date="dialogFeedback.date"
            @close="handleHideAddFeedback"
            :read-only="dialogFeedback.readOnly"
            :feedback-id="dialogFeedback.feedbackId"
          />
          <b-modal
            size="lg"
            title="Issue Detail"
            class="modal-primary"
            v-model="issueDetailModal"
            id="issueDetailModal"
            ok-title="Close"
          >
            <div class="animated fadeIn apax-form">
              <div class="row">
                <div class="col-12">
                  <b-card header>
                    <div slot="header">
                      <div class="col-12">
                        <i class="fa fa-clipboard" /> <b class="uppercase"> <span
                          class="name-title"
                        >{{ issue.student_nick }} -- {{ issue.student_name }} / ({{ cache.class_info.class_name }})</span></b>
                      </div>
                    </div>
                    <div id="page-content">
                      <div class="row">
                        <div class="col-6">
                          <div class="row">
                            <div class="col-4">
                              <b class="book-title text-small">Book :</b>
                            </div>
                            <div class="col-8">
                              <div
                                class="form-group"
                                id="book-input"
                              >
                                <input
                                  type="text"
                                  v-model="book_name"
                                  class="form-control"
                                  readonly
                                >
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-4">
                                  <b class="book-title text-small">Term Start - End
                                    Date:</b>
                                </div>
                                <div class="col-8">
                                  {{ issue.book_from }} ~ {{ issue.book_to }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    CLASS TERM SUMMARY <br> <i>Tổng kết
                                      khóa</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr class="text-xs-left">
                                  <td
                                    width="50%"
                                    class="td-text"
                                  >
                                    <div class="row">
                                      <div class="col-sm-12 text-left">
                                        <textarea
                                          v-model="issue.trail_report"
                                          :disabled="disabledBook"
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                          readonly
                                        />
                                        <textarea
                                          v-show="session.user.role_id === 55 || session.user.role_id === 56"
                                          v-model="issue.cm_trail_report"
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                          placeholder="Nội dung nhập của CM"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-sm-12 text-left">
                                        <textarea
                                          v-model="issue.summary"
                                          :disabled="disabledBook"
                                          id=""
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                          readonly
                                        />
                                        <textarea
                                          v-show="session.user.role_id === 55 || session.user.role_id === 56"
                                          v-model="issue.cm_summary"
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                          placeholder="Nội dung nhập của CM"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    HOW TO SUPPORT YOUR CHILD AT HOME
                                    <br><i>Cách hỗ trợ học sinh học tại nhà</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-left text-table">
                                    1. Đảm bảo con phải
                                    hoàn thành học E-learning ở nhà hoặc tại trung tâm
                                  </td>
                                  <td class="text-left text-table">
                                    4. Giúp con làm bài tập về nhà phần từ vựng và viết
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-left text-table">
                                    2. Để cho con đọc to
                                    truyện phần Gallery
                                  </td>
                                  <td class="text-left text-table">
                                    5. Nghe các bài hát ABC
                                    để giúp nhận biết chữ cái và các âm
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-left text-table">
                                    3. Luyện tập với sách
                                    Palette (các câu mệnh lệnh)
                                  </td>
                                  <td class="text-left text-table">
                                    6. Ôn lại từ vựng và
                                    câu trả lời trong sách Atelier
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="3">
                                    SCORING GUIDELINES
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="10%">
                                    Score
                                  </td>
                                  <td width="15%">
                                    Guideline
                                  </td>
                                  <td width="75%">
                                    Explanation
                                  </td>
                                </tr>
                                <tr
                                  v-for="(score, i) in scores"
                                  :key="i"
                                >
                                  <td class="text-center text-table">
                                    {{ score.score }}
                                  </td>
                                  <td class="text-left text-table">
                                    {{ score.guideline }}
                                  </td>
                                  <td class="text-left text-table">
                                    {{ score.explanation }}
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th width="17%">
                                    Category((<i>Kĩ năng</i>)
                                  </th>
                                  <th width="15%">
                                    Sub-Category(<i>Thành phần</i>)
                                  </th>
                                  <th width="50%">
                                    Description(<i>Miêu tả</i>)
                                  </th>
                                  <th width="18%">
                                    <span
                                      class="text-danger"
                                    >Score / 5 (*)</span>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td
                                    rowspan="3"
                                    class="text-heading"
                                  >
                                    Classroom Duty
                                  </td>
                                  <td class="text-table">
                                    Attendance <br>
                                    Điểm danh
                                  </td>
                                  <td class="text-left text-table">
                                    Student attends class
                                    regularly. <br>
                                    <i>Học sinh đi học đều</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control input-small text-small"
                                      v-model="issue.scoring_classroom_duty_attendance"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Punctuality <br>
                                    Đúng giờ
                                  </td>
                                  <td class="text-left text-table">
                                    Student arrives to class on time.. <br>
                                    <i>Học sinh đến lớp đúng giờ</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_classroom_duty_punctuality"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Homework <br>
                                    Bài tập về nhà
                                  </td>
                                  <td class="text-left text-table">
                                    Student completes homework assignments (E-Learning
                                    and Homework) on time. <br>
                                    <i>Học sinh làm bài đầy đủ (E-learning và
                                      Notebook)</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_classroom_duty_homework"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr class="table-primary">
                                  <td>Category((<i>Kĩ năng</i>)</td>
                                  <td>Sub-Category(<i>Thành phần</i>)</td>
                                  <td>Description(<i>Miêu tả</i>)</td>
                                  <td><span class="text-danger">Score / 5 (*)</span></td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="2"
                                    class="text-heading"
                                  >
                                    ABC
                                  </td>
                                  <td class="text-table">
                                    Letter Knowledge <br>
                                    Nhận biết chữ cái
                                  </td>
                                  <td class="text-left text-table">
                                    Student recognizes letter names, which may include
                                    consonants and/or vowels. <br>
                                    <i>Học sinh nhận biết tên chữ cái, bao gồm phụ âm
                                      hoặc/và nguyên âm </i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_reading_comprehension"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Letter-Sound Knowledge <br>
                                    Nhận biết chữ cái - âm
                                  </td>
                                  <td class="text-left text-table">
                                    Student articulates letter-sounds accurately and
                                    associates them with the letter name. <br>
                                    <i>Học sinh đọc chữ cái - âm chính xác và biết cách
                                      ghép chúng với tên chữ cái</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_reading_fluency"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="2"
                                    class="text-heading"
                                  >
                                    Handwriting <p
                                      class="vn-text-heading"
                                    >
                                      Viết chữ
                                    </p>
                                  </td>
                                  <td class="text-table">
                                    Fine-Motor Skills <br>
                                    Kỹ năng vận động tinh
                                  </td>
                                  <td class="text-left text-table">
                                    Student has good vision and hand-eye coordination
                                    skills and uses a pencil with confidence. <br>
                                    <i>Học sinh có khả năng quan sát, kỹ năng điều phối
                                      tay-mắt tốt và sử dụng bút thành thạo</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_writing_sentence_structure"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Accurary and Legibility <br>
                                    Chính xác và dễ đọc
                                  </td>
                                  <td class="text-left text-table">
                                    Student writes letters and words with convention and
                                    writing is neat and legible. <br>
                                    <i>>Học sinh viết đúng các nét của từ và chữ, chữ
                                      viết gọn gàng và dễ đọc</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_writing_creativity"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="2"
                                    class="text-heading"
                                  >
                                    Spelling <p
                                      class="vn-text-heading"
                                    >
                                      Đánh vần
                                    </p>
                                  </td>
                                  <td class="text-table">
                                    Vocabulary <br>
                                    Từ vựng
                                  </td>
                                  <td class="text-left text-table">
                                    Student is able to spell vocabulary words learned in
                                    the lesson. <br>
                                    <i>Học sinh có thể đánh vần các từ đã học trong bài
                                      học</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_ctp_speaking_expression"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Phonetic Spelling <br>
                                    Đánh vần
                                  </td>
                                  <td class="text-left text-table">
                                    Student uses decoding, letter-sounds, and other
                                    strategies to spell words. <br>
                                    <i>Học sinh sử dụng quy tắc, chữ cái- âm để đánh vần
                                      từ</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_ctp_teamwork"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr class="table-primary">
                                  <td>Category((<i>Kĩ năng</i>)</td>
                                  <td>Sub-Category(<i>Thành phần</i>)</td>
                                  <td>Description(<i>Miêu tả</i>)</td>
                                  <td><span class="text-danger">Score / 5 (*)</span></td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="3"
                                    class="text-heading"
                                  >
                                    Social Skills <p
                                      class="vn-text-heading"
                                    >
                                      Kỹ năng xã hội
                                    </p>
                                  </td>
                                  <td class="text-table">
                                    Behavior <br>
                                    Ứng xử
                                  </td>
                                  <td class="text-left text-table">
                                    Student is well-behaved and follows class rules.
                                    <br>
                                    <i>Học sinh ngoan ngoãn và tuân thủ nội quy lớp
                                      học.</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      id=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_social_skills_behavior"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Confidence <br>
                                    Tự tin
                                  </td>
                                  <td class="text-left text-table">
                                    Student shares ideas and thoughts to teachers and
                                    students with confidence. <br>
                                    <i>Học sinh chia sẻ ý tưởng, suy nghĩ với giáo viên
                                      và các bạn một cách tự tin</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_social_skills_confidence"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Participation <br>
                                    Tham gia
                                  </td>
                                  <td class="text-left text-table">
                                    Student raises their hand often and engages well
                                    with lesson content. <br>
                                    <i>Học sinh thường xuyên giơ tay tham gia xây dựng
                                      bài</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_social_skills_participation"
                                      :disabled="disabledScore"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        :value="score.score"
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    STUDENT'S GREATEST STRENGTH <br><i>Ưu
                                      điểm nổi trội nhất của học sinh</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <div
                                      v-if="checkRole()"
                                      class="row"
                                    >
                                      <div class="col-sm-12">
                                        <textarea
                                          placeholder="Teacher comment"
                                          v-model="issue.tc_gretest_strength"
                                          cols="30"
                                          rows="2"
                                          class="form-control"
                                          disabled
                                        />
                                      </div>
                                    </div>
                                    <div
                                      v-else
                                      class="col-sm-12"
                                    >
                                      <div class="row">
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Teacher comment"
                                            v-model="issue.tc_gretest_strength"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                            disabled
                                          />
                                        </div>
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Class Master comment"
                                            v-model="issue.cm_gretest_strength"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    WHAT NEEDS TO BE IMPROVED <br><i>Kỹ năng
                                      cần cải thiện</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <div
                                      v-if="checkRole()"
                                      class="row"
                                    >
                                      <div class="col-sm-12">
                                        <textarea
                                          placeholder="Teacher comment"
                                          v-model="issue.tc_need_improved"
                                          cols="30"
                                          rows="2"
                                          class="form-control"
                                          disabled
                                        />
                                      </div>
                                    </div>
                                    <div
                                      v-else
                                      class="col-sm-12"
                                    >
                                      <div class="row">
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Teacher comment"
                                            v-model="issue.tc_need_improved"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                            readonly
                                          />
                                        </div>
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Class Master comment"
                                            v-model="issue.cm_need_improved"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </b-card>
                </div>
              </div>
              <div
                class="row"
                v-show="!checkRole()"
              >
                <div class="col-12">
                  <b-card header-tag="header">
                    <div slot="header">
                      <strong>Đánh giá của phụ huynh đối với giáo viên</strong>
                    </div>
                    <div class="content-detail">
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    FEEDBACK FROM PARENT
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="col-sm-12">
                                          <textarea
                                            v-model="issue.parents_discussion"
                                            :disabled="disabledBook"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    Nhận xét đánh giá của phụ huynh
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-4">
                                        <b class="evaluation-text">Đánh giá
                                          chung</b>
                                        <select
                                          name=""
                                          class="form-control"
                                          v-model="issue.parents_evaluation"
                                          :disabled="disabledBook"
                                        >
                                          <option
                                            value=""
                                            disabled
                                          >
                                            Chọn đánh giá
                                          </option>
                                          <option value="3">
                                            Tốt
                                          </option>
                                          <option value="2">
                                            Trung bình
                                          </option>
                                          <option value="1">
                                            Cần cải thiện
                                          </option>
                                        </select>
                                      </div>
                                      <div class="col-sm-8">
                                        <b class="evaluation-text">Nội dung nhận
                                          xét </b>
                                        <textarea
                                          v-model="issue.parents_comment"
                                          cols="30"
                                          rows="2"
                                          class="form-control"
                                          :disabled="disabledBook"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    Đánh giá Học sinh
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-4">
                                        <strong>Đánh giá</strong>
                                        <select
                                          class="form-control"
                                          v-model="rank.rank_id"
                                          :disabled="disabledBook"
                                          required
                                        >
                                          <option
                                            value=""
                                            disabled
                                          >
                                            Chọn đánh
                                            giá
                                          </option>
                                          <option value="1">
                                            Tốt
                                          </option>
                                          <option value="7">
                                            Yếu
                                          </option>
                                          <option value="8">
                                            Cá biệt
                                          </option>
                                          <option value="9">
                                            Chăm sóc đặc biệt
                                          </option>
                                        </select>
                                      </div>
                                      <div class="col-8">
                                        <strong>Nội dung đánh giá</strong>
                                        <textarea
                                          class="form-control"
                                          v-model="rank.comment"
                                          cols="10"
                                          rows="10"
                                          :disabled="disabledBook"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </b-card>
                </div>
                <issue
                  :item="issue"
                  ref="form"
                  v-show="false"
                />
              </div>
              <div class="row">
                <div class="col-12 text-center">
                  <div
                    class="button-save-group"
                    id="button-save-group"
                  >
                    <button
                      class="btn btn-success"
                      @click="updateIssue"
                      v-show="showUpdateButton"
                    >
                      Update Issue
                    </button>
                    <button
                      class="btn btn-info"
                      @click="sendMail"
                      v-show="showUpdateButton"
                    >
                      Gửi mail
                    </button>
                    <ApaxButton
                      :markup="success"
                      :on-click="callPrintForm"
                      id="print-button"
                    >
                      Print Issue
                    </ApaxButton>
                    <button
                      class="btn btn-danger"
                      @click="cancelDetailModal"
                    >
                      Comeback
                    </button>
                  </div>
                </div>
              </div>
              <div
                class="row"
                v-show="!checkRole()"
              >
                <div class="col-12">
                  <b-card header-tag="header">
                    <div slot="header">
                      <strong>Issue List</strong>
                    </div>
                    <div class="content-detail">
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-striped table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th>Index</th>
                                  <th>Teacher</th>
                                  <!--<th>Sessions</th>-->
                                  <th>CM</th>
                                  <th>Teacher Created time</th>
                                  <th>CM updated time</th>
                                  <th>Options</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr
                                  v-for="(issue, i) in issue_list"
                                  :key="i"
                                >
                                  <td>{{ i+1 }}</td>
                                  <td>{{ issue.teacher_name }}</td>
                                  <!--<td>Sessions</td>-->
                                  <td>{{ cache.class_info.cm_name }}</td>
                                  <td>{{ issue.created_at }}</td>
                                  <td>{{ issue.cm_updated_at }}</td>
                                  <td>Options</td>
                                </tr>
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
          </b-modal>
          <b-modal
            size="lg"
            title="Add New Issue"
            class="modal-primary"
            v-model="addIssueModal"
            @ok="saveIssue"
            ok-variant="primary"
            id="addIssueModal"
          >
            <div class="animated fadeIn apax-form">
              <div class="row">
                <div class="col-12">
                  <b-card header>
                    <div slot="header">
                      <i class="fa fa-clipboard" /> <b class="uppercase"> <span
                        class="name-title"
                      >{{ issue.student_nick }} -- {{ issue.student_name }} / ({{ cache.class_info.class_name }})</span></b>
                    </div>
                    <div id="page-content">
                      <div class="row">
                        <div class="col-6">
                          <div class="row">
                            <div class="col-4">
                              <b class="book-title text-small">Book :</b>
                            </div>
                            <div class="col-8">
                              <div
                                class="form-group"
                                id="book-input"
                              >
                                <input
                                  type="text"
                                  class="form-control"
                                  v-model="book_name"
                                  readonly
                                >
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-4">
                                  <b class="book-title text-small">Term Start - End
                                    Date:</b>
                                </div>
                                <div class="col-8">
                                  {{ issue.book_from }} ~ {{ issue.book_to }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    CLASS TERM SUMMARY <br> <i>Tổng kết
                                      khóa</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr class="text-xs-left">
                                  <td
                                    width="50%"
                                    class="td-text"
                                  >
                                    <div class="row">
                                      <div class="col-sm-12 text-left">
                                        <textarea
                                          v-model="issue.trail_report"
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                        />
                                        <textarea
                                          v-show="session.user.role_id === 55 || session.user.role_id === 56"
                                          v-model="issue.cm_trail_report"
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                          placeholder="Nội dung dịch của CM"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-sm-12 text-left">
                                        <textarea
                                          v-model="issue.summary"
                                          id=""
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                        />
                                        <textarea
                                          v-show="session.user.role_id === 55 || session.user.role_id === 56"
                                          v-model="issue.cm_summary"
                                          cols="6"
                                          rows="6"
                                          class="form-control text-input"
                                          placeholder="Nội dung dịch của CM"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    HOW TO SUPPORT YOUR CHILD AT HOME
                                    <br><i>Cách hỗ trợ học sinh học tại nhà</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    Score
                                  </td>
                                  <td width="50%">
                                    Guideline
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-left text-table">
                                    1. Đảm bảo con phải
                                    hoàn thành học E-learning ở nhà hoặc tại trung tâm
                                  </td>
                                  <td class="text-left text-table">
                                    4. Giúp con làm bài tập về nhà phần từ vựng và viết
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-left text-table">
                                    2. Để cho con đọc to
                                    truyện phần Gallery
                                  </td>
                                  <td class="text-left text-table">
                                    5. Nghe các bài hát ABC
                                    để giúp nhận biết chữ cái và các âm
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-left text-table">
                                    3. Luyện tập với sách
                                    Palette (các câu mệnh lệnh)
                                  </td>
                                  <td class="text-left text-table">
                                    6. Ôn lại từ vựng và
                                    câu trả lời trong sách Atelier
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="3">
                                    SCORING GUIDELINES
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="10%">
                                    Score
                                  </td>
                                  <td width="15%">
                                    Guideline
                                  </td>
                                  <td width="75%">
                                    Explanation
                                  </td>
                                </tr>
                                <tr
                                  v-for="(score, i) in scores"
                                  :key="i"
                                >
                                  <td class="text-center text-table">
                                    {{ score.score }}
                                  </td>
                                  <td class="text-left text-table">
                                    {{ score.guideline }}
                                  </td>
                                  <td class="text-left text-table">
                                    {{ score.explanation }}
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th width="17%">
                                    Category(<i>Kĩ năng</i>)
                                  </th>
                                  <th width="15%">
                                    Sub-Category(<i>Thành phần</i>)
                                  </th>
                                  <th width="50%">
                                    Description(<i>Miêu tả</i>)
                                  </th>
                                  <th width="18%">
                                    <span
                                      class="text-danger"
                                    >Score / 5 (*)</span>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td
                                    rowspan="3"
                                    class="text-heading"
                                  >
                                    Classroom Duty
                                  </td>
                                  <td class="text-table">
                                    Attendance <br>
                                    Điểm danh
                                  </td>
                                  <td class="text-left text-table">
                                    Student attends class
                                    regularly. <br>
                                    <i>Học sinh đi học đều</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control input-small text-small"
                                      v-model="issue.scoring_classroom_duty_attendance"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Punctuality <br>
                                    Đúng giờ
                                  </td>
                                  <td class="text-left text-table">
                                    Student arrives to class on time.. <br>
                                    <i>Học sinh đến lớp đúng giờ</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_classroom_duty_punctuality"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Homework <br>
                                    Bài tập về nhà
                                  </td>
                                  <td class="text-left text-table">
                                    Student completes homework assignments (E-Learning
                                    and Homework) on time. <br>
                                    <i>Học sinh làm bài đầy đủ (E-learning và
                                      Notebook)</i>
                                  </td>
                                  <td>
                                    <select
                                      name=""
                                      id=""
                                      class="form-control text-small"
                                      v-model="issue.scoring_classroom_duty_homework"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr class="table-primary">
                                  <td>Category((<i>Kĩ năng</i>)</td>
                                  <td>Sub-Category(<i>Thành phần</i>)</td>
                                  <td>Description(<i>Miêu tả</i>)</td>
                                  <td><span class="text-danger">Score / 5</span></td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="2"
                                    class="text-heading"
                                  >
                                    ABC
                                  </td>
                                  <td class="text-table">
                                    Letter Knowledge <br>
                                    <i>Nhận biết chữ cái</i>
                                  </td>
                                  <td class="text-left text-table">
                                    Student recognizes letter names, which may include
                                    consonants and/or vowels. <br>
                                    <i>Học sinh nhận biết tên chữ cái, bao gồm phụ âm
                                      hoặc/và nguyên âm </i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_reading_comprehension"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Letter-Sound Knowledge <br>
                                    Nhận biết chữ cái - âm
                                  </td>
                                  <td class="text-left text-table">
                                    Student articulates letter-sounds accurately and
                                    associates them with the letter name.<br>
                                    <i>Học sinh đọc chữ cái - âm chính xác và biết cách
                                      ghép chúng với tên chữ cái</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_reading_fluency"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="2"
                                    class="text-heading"
                                  >
                                    Handwriting <p
                                      class="vn-text-heading"
                                    >
                                      Viết chữ
                                    </p>
                                  </td>
                                  <td class="text-table">
                                    Fine-Motor Skills <br>
                                    Kỹ năng vận động tinh
                                  </td>
                                  <td class="text-left text-table">
                                    Student has good vision and hand-eye coordination
                                    skills and uses a pencil with confidence. <br>
                                    <i>Học sinh có khả năng quan sát, kỹ năng điều phối
                                      tay-mắt tốt và sử dụng bút thành thạo</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_writing_sentence_structure"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Accurary and Legibility <br>
                                    Chính xác và dễ đọc
                                  </td>
                                  <td class="text-left text-table">
                                    Student writes letters and words with convention and
                                    writing is neat and legible. <br>
                                    <i>>Học sinh viết đúng các nét của từ và chữ, chữ
                                      viết gọn gàng và dễ đọc</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_writing_creativity"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="2"
                                    class="text-heading"
                                  >
                                    Spelling <p
                                      class="vn-text-heading"
                                    >
                                      Đánh vần
                                    </p>
                                  </td>
                                  <td class="text-table">
                                    Vocabulary <br>
                                    Từ vựng
                                  </td>
                                  <td class="text-left text-table">
                                    Student is able to spell vocabulary words learned in
                                    the lesson. <br>
                                    <i>Học sinh có thể đánh vần các từ đã học trong bài
                                      học</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_ctp_speaking_expression"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Phonetic Spelling <br>
                                    Đánh vần
                                  </td>
                                  <td class="text-left text-table">
                                    Student uses decoding, letter-sounds, and other
                                    strategies to spell words. <br>
                                    <i>Học sinh sử dụng quy tắc, chữ cái- âm để đánh vần
                                      từ</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_ctp_teamwork"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr class="table-primary">
                                  <td>Category((<i>Kĩ năng</i>)</td>
                                  <td>Sub-Category(<i>Thành phần</i>)</td>
                                  <td>Description(<i>Miêu tả</i>)</td>
                                  <td><span class="text-danger">Score / 5</span></td>
                                </tr>
                                <tr>
                                  <td
                                    rowspan="3"
                                    class="text-heading"
                                  >
                                    Social Skills <p
                                      class="vn-text-heading"
                                    >
                                      Kỹ năng xã hội
                                    </p>
                                  </td>
                                  <td class="text-table">
                                    Behavior <br>
                                    Ứng xử
                                  </td>
                                  <td class="text-left text-table">
                                    Student is well-behaved and follows class rules.
                                    <br>
                                    <i>Học sinh ngoan ngoãn và tuân thủ nội quy lớp
                                      học.</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_social_skills_behavior"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Confidence <br>
                                    Tự tin
                                  </td>
                                  <td class="text-left text-table">
                                    Student shares ideas and thoughts to teachers and
                                    students with confidence. <br>
                                    <i>Học sinh chia sẻ ý tưởng, suy nghĩ với giáo viên
                                      và các bạn một cách tự tin</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_social_skills_confidence"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="text-table">
                                    Participation <br>
                                    Tham gia
                                  </td>
                                  <td class="text-left text-table">
                                    Student raises their hand often and engages well
                                    with lesson content. <br>
                                    <i>Học sinh thường xuyên giơ tay tham gia xây dựng
                                      bài</i>
                                  </td>
                                  <td>
                                    <select
                                      class="form-control text-small"
                                      v-model="issue.scoring_social_skills_participation"
                                    >
                                      <option
                                        value=""
                                        disabled
                                      >
                                        Select Score
                                      </option>
                                      <option
                                        v-for="(score, index) in scores"
                                        :key="index"
                                      >
                                        {{ score.score }} -
                                        {{ score.guideline }}
                                      </option>
                                    </select>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    STUDENT'S GREATEST STRENGTH <br><i>Ưu
                                      điểm nổi trội nhất của học sinh</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <div
                                      v-if="checkRole()"
                                      class="row"
                                    >
                                      <div class="col-sm-12">
                                        <textarea
                                          placeholder="Teacher comment"
                                          v-model="issue.tc_gretest_strength"
                                          cols="30"
                                          rows="2"
                                          class="form-control"
                                        />
                                      </div>
                                    </div>
                                    <div
                                      v-else
                                      class="col-sm-12"
                                    >
                                      <div class="row">
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Teacher comment"
                                            v-model="issue.tc_gretest_strength"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Class Master comment"
                                            v-model="issue.cm_gretest_strength"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    WHAT NEEDS TO BE IMPROVED <br><i>Kỹ năng
                                      cần cải thiện</i>
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <div
                                      v-if="checkRole()"
                                      class="row"
                                    >
                                      <div class="col-sm-12">
                                        <textarea
                                          placeholder="Teacher comment"
                                          v-model="issue.tc_need_improved"
                                          cols="30"
                                          rows="2"
                                          class="form-control"
                                        />
                                      </div>
                                    </div>
                                    <div
                                      v-else
                                      class="col-sm-12"
                                    >
                                      <div class="row">
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Teacher comment"
                                            v-model="issue.tc_need_improved"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                        <div class="col-sm-6">
                                          <textarea
                                            placeholder="Class Master comment"
                                            v-model="issue.cm_need_improved"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </b-card>
                </div>
              </div>
              <div
                class="row"
                v-show="!checkRole()"
              >
                <div class="col-12">
                  <b-card header-tag="header">
                    <div slot="header">
                      <strong>Đánh giá của phụ huynh đối với giáo viên </strong>
                    </div>
                    <div class="content-detail">
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    FEEDBACK FROM PARENT
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="col-sm-12">
                                          <textarea
                                            v-model="issue.parents_discussion"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    Nhận xét đánh giá của phụ huynh
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-sm-6">
                                        <strong>Nhận xét</strong>
                                        <div class="col-sm-12">
                                          <textarea
                                            v-model="issue.parents_comment"
                                            cols="30"
                                            rows="2"
                                            class="form-control"
                                          />
                                        </div>
                                      </div>

                                      <div class="col-sm-6">
                                        <strong>Đánh giá</strong>
                                        <div class="col-sm-12">
                                          <select
                                            class="form-control"
                                            v-model="issue.parents_evaluation"
                                          >
                                            <option
                                              value=""
                                              disabled
                                            >
                                              Chọn đánh
                                              giá
                                            </option>
                                            <option value="3">
                                              Tốt
                                            </option>
                                            <option value="2">
                                              Trung bình
                                            </option>
                                            <option value="1">
                                              Cần cải thiện
                                            </option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <div class="table-responsive scrollable">
                            <table class="table table-bordered apax-table">
                              <thead>
                                <tr>
                                  <th colspan="2">
                                    Đánh giá Học sinh
                                  </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td width="50%">
                                    <div class="row">
                                      <div class="col-6">
                                        <strong>Đánh giá</strong>
                                        <select
                                          class="form-control"
                                          v-model="rank_score"
                                        >
                                          <option
                                            value=""
                                            disabled
                                          >
                                            Lựa chọn đánh
                                            giá
                                          </option>
                                          <option value="1">
                                            Tốt
                                          </option>
                                          <option value="7">
                                            Yếu
                                          </option>
                                          <option value="8">
                                            Cá biệt
                                          </option>
                                          <option value="9">
                                            Chăm sóc đặc biệt
                                          </option>
                                        </select>
                                      </div>
                                      <div class="col-6">
                                        <strong>Nội dung đánh giá</strong>
                                        <textarea
                                          class="form-control"
                                          v-model="rank_content"
                                          cols="10"
                                          rows="10"
                                        />
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-6" />
                        <div class="col-6">
                          <div class="pull-right button-save-group">
                            <button
                              class="btn btn-success"
                              @click="saveIssue"
                            >
                              Save Issue
                            </button>
                            <button
                              class="btn btn-warning"
                              @click="cancelModal"
                            >
                              Cancel
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </b-card>
                </div>
              </div>
              <div
                class="row"
                v-show="cache.class_info.user_role == 36"
              >
                <div class="col-9" />
                <div class="col-3">
                  <div class="pull-right button-save-group">
                    <button
                      class="btn btn-success"
                      @click="saveIssue"
                    >
                      Save Issue
                    </button>
                    <button
                      class="btn btn-warning"
                      @click="cancelModal"
                    >
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </b-modal>
          <b-modal
            title="NOTIFICATION"
            class="modal-success"
            size="sm"
            v-model="modal"
            @ok="closeModal"
            ok-variant="success"
          >
            <div v-html="message" />
          </b-modal>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import tree from 'vue-jstree'

import u from '../../../utilities/utility'
import search from '../../../components/Search'
import ApaxButton from '../../../components/Button'
import paging from '../../../components/Pagination'
import issue from '../../base/prints/issue'
import searchBranch from '../../../components/Selection'
import AddFeedback from './add'
import ClassSelectDate from '../../components/ClassSelectDate'

export default {
  name      : 'IssueList',
  components: {
    tree,
    search,
    paging,
    ApaxButton,
    searchBranch,
    issue,
    AddFeedback,
    ClassSelectDate,
  },
  data () {
    return {
      success              : 'success',
      class_transfers      : [],
      transfer             : [],
      disabledIssueBtnA1   : false,
      disabledIssueBtnA2   : false,
      disabledIssueBtnA3   : false,
      disabledIssueBtnB1   : false,
      disabledIssueBtnB2   : false,
      disabledIssueBtnB3   : false,
      disabledBook         : false,
      disabledRating       : false,
      disabledScore        : false,
      disabledUpdateButton : false,
      showUpdateButton     : false,
      book_list            : [],
      reserves             : [],
      message              : '',
      hasIssue             : true,
      modal                : false,
      completed            : false,
      disableSaveCm        : true,
      hideSaveCm           : 'hidden',
      placeholderSelectDate: 'Chọn ngày chủ nhiệm',
      formatSelectDate     : 'yyyy/MM/dd',
      clearSelectedDate    : true,
      disabledDaysOfWeek   : [],
      cm_assign_date       : '',
      cms                  : [],
      cm                   : '',
      item                 : {},
      scores               : [],
      books                : [],
      book                 : '',
      book_id              : '',
      book_name            : '',
      score                : '',
      issue_list           : [],
      rank                 : {
        rank_id: '',
        comment: '',
      },
      issue: {
        student_id                         : '',
        classdate                          : '',
        book_id                            : '',
        cm_id                              : '',
        class_id                           : '',
        teacher_id                         : '',
        tc_gretest_strength                : '',
        cm_gretest_strength                : '',
        tc_need_improved                   : '',
        cm_need_improved                   : '',
        trail_report                       : '',
        class_term                         : '',
        summary                            : '',
        scoring_classroom_duty_attendance  : '',
        scoring_classroom_duty_punctuality : '',
        scoring_classroom_duty_homework    : '',
        scoring_reading_comprehension      : '',
        scoring_reading_fluency            : '',
        scoring_writing_sentence_structure : '',
        scoring_writing_creativity         : '',
        scoring_ctp_speaking_expression    : '',
        scoring_ctp_teamwork               : '',
        scoring_social_skills_behavior     : '',
        scoring_social_skills_confidence   : '',
        scoring_social_skills_participation: '',
        parents_discussion                 : '',
        parents_comment                    : '',
        parents_evaluation                 : '',
        creator_id                         : '',
      },
      rank_score       : '',
      rank_content     : '',
      rank_score_edit  : '',
      rank_content_edit: '',
      issueDetailModal : false,
      addIssueModal    : false,
      html             : {
        class: {
          loading: false,
          button : {
            save_contracts: 'error',
            add_contract  : 'primary',
            up_semester   : 'success',
            print         : 'success',
          },
          display: {
            modal: {
              issue   : false,
              semester: false,
            },
            class_info       : 'display',
            contracts_list   : 'display',
            up_class_info    : 'display',
            up_contracts_list: 'display',
            filter           : {
              branch     : 'hidden',
              classes    : 'hidden',
              semester   : 'hidden',
              up_classes : 'hidden',
              up_semester: 'hidden',
            },
          },
        },
        content: {
          loading    : 'Đang tải dữ liệu...',
          label      : { search: 'Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh' },
          placeholder: { search: 'Từ khóa tìm kiếm' },
        },
        disable: {
          add_contracts: true,
          up_semester  : true,
          filter       : {
            branch     : true,
            semester   : true,
            up_semester: true,
          },
        },
      },
      action: { loading: false },
      url   : {
        api      : '/api/issues/',
        class    : '/api/issues/class/',
        students : '/api/issues/students/',
        classes  : '/api/issues/classes/',
        branches : '/api/issues/branches',
        semesters: '/api/issues/semesters',
        contracts: '/api/issues/contracts/',
      },
      filter: {
        class      : '',
        branch     : '',
        keyword    : '',
        semester   : '',
        up_class   : '',
        up_semester: '',
      },
      list: {
        students    : [],
        contracts   : [],
        up_students : [],
        up_contracts: [],
      },
      cache: {
        user_role        : '',
        class_info       : {},
        up_class_info    : {},
        selected_class   : {},
        selected_up_class: {},
        class            : '',
        up_class         : '',
        branch           : '',
        program          : '',
        semester         : '',
        up_semester      : '',
        classes          : [],
        up_classes       : [],
        branches         : [],
        semesters        : [],
        up_semesters     : [],
        contracts        : [],
        up_contracts     : [],
        class_dates      : [],
        up_class_dates   : [],
        checked_list     : [],
        up_checked_list  : [],
        check_all        : '',
        up_check_all     : '',
        available        : 0,
        up_available     : 0,
      },
      class_date   : {},
      up_class_date: {},
      showsave     : false,
      order        : {
        by: 's.id',
        to: 'DESC',
      },
      temp          : {},
      session       : u.session(),
      dialogFeedback: {
        display : false,
        student : null,
        program : null,
        clazz   : null,
        type    : null,
        readOnly: false,
        feedbackId: null,
      },
      selectedDate: '',
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
            if (contract.student_nick && contract.student_nick.length > 0)
              selected_list.push(contract.contract_id)
          })
        }
        this.cache.checked_list = selected_list
      },
    },
  },
  watch: {
    $route (to, from) {
      if (to !== from) {
        this.cache.students = []
        if (this.$route.params.type === 'class')
          this.getStudentByClass()
        else if (this.cache.date_selected) {
          this.selectedDate = this.cache.date_selected
        }
      }
    },
  },
  created () {
    this.cache.students = []
    this.start()
  },
  filters: {
    filterTuitionFee    : (name, price) => price && price > 1000 ? `${name} - ${this.filterFormatCurrency(price)}` : name,
    filterFormatCurrency: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ',
  },
  methods: {
    checkBookForEachIssue (book_id) {
      console.log('books id', book_id)
      u.a().get(`/api/issues/${book_id}/books/check-available-book`).then((response) => {
        const rs = response.data
        if (rs === 1)
          this.disabledBook = false
        else {
          this.disabledBook = true
          if (this.showUpdateButton)
            this.showUpdateButton = false
        }
      })
    },
    checkAvailableBook (book_list) {
      // let books = ['A1', 'A2', 'A3', 'B1', 'B2', 'B3'];
      console.log('books list', book_list)
      // let book_list = this.book_list
      if (book_list.indexOf('A1') == -1)
        this.disabledIssueBtnA1 = true

      if (book_list.indexOf('A2') == -1)
        this.disabledIssueBtnA2 = true

      if (book_list.indexOf('A3') == -1)
        this.disabledIssueBtnA3 = true

      if (book_list.indexOf('B1') == -1)
        this.disabledIssueBtnB1 = true

      if (book_list.indexOf('B2') == -1)
        this.disabledIssueBtnB2 = true

      if (book_list.indexOf('B3') == -1)
        this.disabledIssueBtnB3 = true
    },
    checkPermissionOfUser () {
      this.disabledRating = false
    },
    validationTxtLengt (txt) {
      if (txt != null)
        return true
      return false
    },
    resetall () {

    },
    saveclasscm () {

    },
    selectBook (id) {
      const selected_book = this.books.filter((item) => item.id === id)
      // console.log('test');
      this.book_name = selected_book[0].name
      this.book_id   = id
      console.log('selected book', selected_book)
      if (selected_book.length) {
        const current_book   = selected_book[0]
        this.issue.book_from = current_book.start_date
        this.issue.book_to   = current_book.end_date
      }
    },
    checkRole () {
      const role_id = this.session.user.role_id
      const resp    = parseInt(role_id) === 36
      return resp
    },
    link (semester = false) {
      let resp           = ''
      this.filter.branch = this.cache.branch
      if (semester) {
        const search = u.jss({
          class   : this.cache.up_class,
          branch  : this.cache.branch,
          semester: this.cache.up_semester,
        })
        resp         = `${this.url.up_contracts}`
      } else {
        const search     = u.jss({
          class   : this.cache.class,
          branch  : this.cache.branch,
          keyword : this.cache.keyword,
          semester: this.cache.semester,
        })
        const pagination = u.jss({
          spage: this.pagination.spage,
          ppage: this.pagination.ppage,
          npage: this.pagination.npage,
          lpage: this.pagination.lpage,
          cpage: this.pagination.cpage,
          total: this.pagination.total,
          limit: this.pagination.limit,
        })
        resp             = `${this.url.contracts}${pagination}/${search}`
      }
      return resp
    },
    load (data, semester = false) {
      u.log('Response Data', data)
      if (semester) {
        this.cache.up_contracts              = data.contracts
        this.cache.up_class_dates            = data.class_dates
        this.cache.up_available              = data.available
        this.cache.up_checked_list           = []
        html.class.up_display.modal.semester = true
      } else {
        this.cache.contracts                   = data.contracts
        this.cache.class_dates                 = data.class_dates
        this.pagination                        = data.pagination
        this.cache.available                   = data.available
        this.cache.checked_list                = []
        this.html.class.display.modal.register = true
      }
      this.action.loading = false
    },
    redirect (link) {
      const info            = link.toString().split('/')
      const page            = info.length > 1 ? info[1] : 1
      this.pagination.cpage = parseInt(page)
      if (!this.html.class.display.modal.register)
        this.action.loading = true

      u.a().get(this.link())
        .then((response) => {
          const data = response.data.data
          this.load(data)
          setTimeout(() => {
            this.action.loading = false
          }, data.duration)
        }).catch((e) => u.log('Exeption', e))
    },
    start () {
      if (u.authorized()) {
        u.a().get(this.url.branches)
          .then((response) => {
            const data                              = response.data.data
            this.cache.branches                     = data
            this.cache.branch                       = ''
            this.html.class.display.filter.branch   = 'display'
            this.html.class.display.filter.semester = 'display'
            this.html.disable.filter.branch         = false
          }).catch((e) => u.log('Exeption', e))
      } else {
        this.cache.branch = this.session.user.branch_id
        this.loadSemesters()
      }
    },
    loading () {
    },
    extract () {

    },
    loadContracts () {
      this.html.class.display.modal.semester = false
      if (!this.html.class.display.modal.register)
        this.action.loading = true

      u.a().get(this.link())
        .then((response) => {
          const data = response.data.data
          this.load(data)
          setTimeout(() => {
            this.action.loading = false
          }, data.duration)
        }).catch((e) => u.log('Exeption', e))
    },
    loadUpSemester () {
      this.html.class.display.modal.register = false
      if (!this.html.class.display.modal.semester)
        this.action.loading = true

      u.a().get(this.link(1))
        .then((response) => {
          const data = response.data.data
          this.load(data, 1)
          setTimeout(() => {
            this.action.loading = false
          }, data.duration)
        }).catch((e) => u.log('Exeption', e))
    },
    loadSemesters () {
      u.a().get(this.url.semesters)
        .then((response) => {
          const data                              = response.data.data
          this.cache.semesters                    = data
          this.filter.semester                    = ''
          this.html.class.display.filter.semester = 'display'
          this.html.disable.filter.semester       = false
        }).catch((e) => u.log('Exeption', e))
    },
    selectBranch (data) {
      this.cache.branch  = data.id
      this.filter.branch = data.id
      const branch_id    = this.cache.branch
      this.loadSemesters()
    },
    selectSemester () {
      this.cache.semester = this.filter.semester
      u.a().get(`${this.url.classes}${this.cache.branch}/${this.cache.semester}`)
        .then((response) => {
          const data                             = response.data.data
          this.cache.classes                     = data
          this.filter.class                      = ''
          this.html.class.display.filter.classes = 'display'
        }).catch((e) => u.log('Exeption', e))
    },
    selectUpSemester (selected_up_class) {
      this.cache.up_semester = this.filter.up_semester
      u.log('Semester', this.filter.up_semester)
      u.a().get(`${this.url.classes}${this.cache.branch}/${this.cache.up_semester}`)
        .then((response) => {
          const data                                = response.data.data
          this.cache.up_classes                     = data
          this.filter.up_class                      = ''
          this.html.class.display.filter.up_classes = 'display'
        }).catch((e) => u.log('Exeption', e))
    },
    handleSelectedDate (date) {
      this.action.loading = true
      u.a().get(`${this.url.students}${this.filter.class}/${date.cjrn_classdate}`)
        .then((response) => {
          this.cache.students      = response.data.data
          this.cache.date_selected = date
          this.action.loading      = false
        }).catch((e) => {
          this.action.loading = false
        })
    },
    getStudentByClass () {
      this.action.loading = true
      u.a().get(`${this.url.students}${this.filter.class}`)
        .then((response) => {
          this.cache.students = response.data.data
          this.action.loading = false
        }).catch((e) => {
          this.action.loading = false
        })
    },
    selectClass (selected_class) {
      if (selected_class.model.item_type === 'class') {
        this.showsave             = true
        this.cache.selected_class = selected_class
        this.cache.class          = selected_class.model.item_id
        this.filter.class         = this.cache.class
        this.action.loading       = true
        u.a().get(`${this.url.class}${this.filter.class}`)
          .then((response) => {
            this.cache.class_info                  = response.data.data
            this.html.disable.add_contracts        = false
            this.html.disable.up_semester          = false
            this.html.class.display.class_info     = 'display'
            this.html.class.display.contracts_list = 'display'
            this.action.loading                    = false
            if (this.$route.params.type === 'class')
              this.getStudentByClass()
          }).catch(() => {
            this.action.loading = false
          })
      } else {
        this.cache.class      = null
        this.filter.class     = null
        this.cache.class_info = null
      }
    },
    loadIssue (student_id, id) {
      // console.log('boook id', book_id);
      const user_role = this.cache.class_info.user_role
      if (user_role != 36) {
        this.disabledScore    = true
        this.showUpdateButton = true
      }
      if (user_role === 36) {
        this.disabledScore    = true
        this.disabledRating   = true
        this.showUpdateButton = false
      }
      this.checkBookForEachIssue(id)
      this.issueDetailModal = true
      const selected_book   = this.books.filter((item) => item.id === id)
      this.book_name        = selected_book[0].name
      // console.log('selected boook', selected_book);
      u.a().get(`/api/issues/${student_id}/issue`).then((response) => {
        const issue = response.data.issue
        const rank  = response.data.rank

        u.log(issue.parents_evaluation)
        this.issue = issue
        if (!issue.parents_evaluation)
          this.issue.parents_evaluation = ''

        if (rank)
          this.rank = rank
        else
          console.log('the rank is not definded')

        // console.log('test for rank', this.rank);
        // console.log('issssu',response.data);
      })
      this.loadIssueListByStudent(student_id)
    },
    loadIssueListByStudent (student_id) {
      u.a().get(`/api/issues/${student_id}/issue-list`).then((response) => {
        this.issue_list = response.data
        console.log('issue list =', response.data)
      })
    },
    // loadRankStudent(student_id){
    //    u.a().get(`./api/termstudent/${student_id}/rank`).then(response => {
    //     console.log('idrank',response.data)
    //   })
    // },
    addIssue (student_id, book_id) {
      console.log('book id', book_id)
      this.resetValue()
      this.book_id          = book_id
      this.issue.student_id = student_id
      this.addIssueModal    = true
      this.selectBook(this.book_id)
    },
    validScoring () {
      let valid = true
      if (this.issue.scoring_classroom_duty_attendance === '') {
        alert('Please select score for classroom duty attendance!')
        valid = false
      } else if (this.issue.scoring_classroom_duty_punctuality === '') {
        alert('Please select score for classroom duty punctuality!')
        valid = false
      } else if (this.issue.scoring_classroom_duty_homework === '') {
        alert('Please select score for classroom duty homework!')
        valid = false
      } else if (this.issue.scoring_reading_comprehension === '') {
        alert('Please select score for reading comprehension!')
        valid = false
      } else if (this.issue.scoring_reading_fluency === '') {
        alert('Please select score for reading fluency!')
        valid = false
      } else if (this.issue.scoring_writing_sentence_structure === '') {
        alert('Please select score for writing sentence structure!')
        valid = false
      } else if (this.issue.scoring_writing_creativity === '') {
        alert('Please select score for writing creativity!')
        valid = false
      } else if (this.issue.scoring_ctp_speaking_expression === '') {
        alert('Please select score for speaking expression!')
        valid = false
      } else if (this.issue.scoring_ctp_teamwork === '') {
        alert('Please select score for teamwork!')
        valid = false
      } else if (this.issue.scoring_social_skills_behavior === '') {
        alert('Please select score for social skills behavior!')
        valid = false
      } else if (this.issue.scoring_social_skills_confidence === '') {
        alert('Please select score for social skills confidence!')
        valid = false
      } else if (this.issue.scoring_social_skills_participation === '') {
        alert('Please select score for social skills participation!')
        valid = false
      }
      return valid
    },
    saveIssue () {
      if (this.validScoring()) {
        const book_id = this.book_id
        // alert(book_id);
        const data = {
          book_id     : book_id,
          class       : this.cache.class_info,
          issue       : this.issue,
          rank_score  : this.rank_score,
          rank_content: this.rank_content,
        }
        // console.log(data);
        u.a().post('/api/issues', data)
          .then((response) => {
            this.message      = `Created successfully.`
            this.modal        = true
            this.completed    = true
            this.rank_content = ''
          })
      }
    },
    updateIssue () {
      if (this.validScoring()) {
        const data     = {
          book : this.book,
          class: this.cache.class_info,
          issue: this.issue,
          rank : this.rank,

        }
        const issue_id = this.issue.id
        u.a().put(`/api/issues/${issue_id}`, data)
          .then((response) => {
            this.message   = `Updated successfully.`
            this.modal     = true
            this.completed = true
          })
      }
    },
    checkIssueExist () {
      this.hasIssue = true
    },
    completeIssue () {

    },
    completeUpSemester () {

    },
    cancelModal () {
      this.addIssueModal    = false
      this.issueDetailModal = false
    },
    cancelDetailModal () {
      this.issueDetailModal = false
    },
    callPrintForm () {
      // this.$refs.form.callPrintForm()
      window.open(`/print/issue/${this.issue.id}/1`, '_blank')
    },
    sendMail () {
      const url = `/api/issues/mail/${this.issue.student_id}/${this.issue.id}`
      u.a().get(url).then((response) => {
        if (response.data.code == 200)
          alert('Mail đã được gửi thành công')
        else
          alert(response.data.message)
      })
    },
    closeModal () {
      if (this.completed) {
        this.selectClass(this.cache.selected_class)
        this.modal = false
        this.resetValue()
      } else this.modal = false
    },
    resetValue () {
      this.issue.tc_gretest_strength                 = ''
      this.issue.cm_gretest_strength                 = ''
      this.issue.tc_need_improved                    = ''
      this.issue.cm_need_improved                    = ''
      this.issue.trail_report                        = ''
      this.issue.class_term                          = ''
      this.issue.summary                             = ''
      this.issue.scoring_classroom_duty_attendance   = ''
      this.issue.scoring_classroom_duty_punctuality  = ''
      this.issue.scoring_classroom_duty_homework     = ''
      this.issue.scoring_reading_comprehension       = ''
      this.issue.scoring_reading_fluency             = ''
      this.issue.scoring_writing_sentence_structure  = ''
      this.issue.scoring_writing_creativity          = ''
      this.issue.scoring_ctp_speaking_expression     = ''
      this.issue.scoring_ctp_teamwork                = ''
      this.issue.scoring_social_skills_behavior      = ''
      this.issue.scoring_social_skills_confidence    = ''
      this.issue.scoring_social_skills_participation = ''
      this.issue.parents_discussion                  = ''
      this.issue.parents_comment                     = ''
      this.issue.parents_evaluation                  = ''
    },
    handleCreateFeedback (student, readOnly) {
      this.dialogFeedback.student  = student
      this.dialogFeedback.program  = this.cache.program || {}
      this.dialogFeedback.clazz    = this.cache.class_info
      this.dialogFeedback.readOnly = readOnly
      this.dialogFeedback.feedbackId = student.feedback_id
      const semester               = _.get(this, 'cache.semesters', []).find((item) => (item.id === this.filter.semester))
      this.dialogFeedback.type     = _.get(semester, 'name')
      this.dialogFeedback.display  = !this.dialogFeedback.display
      this.dialogFeedback.date     = this.selectedDate.cjrn_classdate
    },
    handleHideAddFeedback () {
      if (this.$route.params.type === 'class')
        this.getStudentByClass()
      else
        this.handleSelectedDate(this.cache.date_selected)

      this.dialogFeedback = {
        display : false,
        student : null,
        program : null,
        clazz   : null,
        type    : null,
        readOnly: false,
        date    : null,
      }
    },
  },
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

    #add-contract___BV_modal_footer_ button:first-child {
        display: none !important;
    }

    .float-left {
        float: left;
    }

    .book-title {
        margin-left: 8px;
    }

    #book-input {
        margin-left: 12px;
    }

    .evaluation-text {
        margin-left: 10px;
    }

    .input-text {
        width: 570px;
    }

    .text-input {
        height: 60px;
    }

    .text-small {
        font-size: 10px;
    }

    .input-small {

    }

    .text-heading {
        font-size: 18px;
    }

    .text-table {
        font-size: 12px;
        font-weight: normal;
    }

    td {
        line-height: 15px;
        padding: 5px;
    }

    #btn-sm {
        margin-bottom: 5px;
        width: 30px;
        height: 30px;
        text-align: center;
        line-height: 20px;
    }

    .button-save-group {
        margin-right: 1px;
    }

    #button-save-group {
        margin-bottom: 10px;

    }

    .vn-text-heading {
        font-size: 12px;
        font-weight: normal;
        margin-top: 5px;
    }

    #print-button {
        height: 35px;
        margin-top: -3px;
    }

    td, th {
        text-transform: none !important;
    }
</style>
