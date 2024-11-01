<template>
  <div class="animated fadeIn apax-form" id="class register">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i> <strong>Class List of CTP</strong>
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
                        <label class="filter-label control-label">Semester: </label>
                      </div>
                      <div class="col-md-8 filter-selection">
                        <div class="row form-group">
                          <select
                            @change="selectSemester"
                            v-model="filter.semester"
                            :disabled="html.disable.filter.semester"
                            class="filter-selection semester form-control"
                          >
                            <option value="">Please choose one semester</option>
                            <option :value="semester.id" v-for="(semester, ind) in cache.semesters" :key="ind">{{semester.name}}</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 filter-classes" :class="html.class.display.filter.classes">
                    <div class="row">
                      <label class="filter-label control-label">Class: </label>
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
                          Syllabus:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.class_name }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Class Period:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.class_time }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Instructor:
                        </div>
                        <div class="col-md-9 field-detail" >
                          <span v-html="cache.class_info.teachers_name"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Number of Students:
                        </div>
                        <div class="col-md-9 field-detail" >
                          {{ cache.class_info.total_students, cache.class_info.class_max_students | totalPerMax }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          Class Day and Time:
                        </div>
                        <div class="col-md-9 field-detail" >
                          <span v-html="cache.class_info.time_and_place"></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" >
                      <div class="row">
                        <div class="col-md-3 text-right field-label" >
                          CM:
                        </div>
                        <div class="col-md-7 field-detail" >
                          <div class="row">
                            <div class="col-md-5">
                              <input type="text" class="form-control" :value="cache.class_info.cm_name" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2 field-detail" >
                          <!-- <input type="file" class="form-control" v-model="attached_file"> -->
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
                        <div class="table-responsive scrollable">
                          <table class="table table-striped table-bordered apax-table">
                            <thead>
                              <tr>
                                <th>
                                   <b-form-checkbox class="check-item" id="select-all" v-model="selectAll" ></b-form-checkbox>
                                </th>
                                <th>LMS</th>
                                <th>Students Name</th>
                                <th>Students Nick</th>
                                <th>CTP</th>
                                <!-- <th>CM</th>
                                <th>CM Updated Time</th> -->
                                <!-- <th>Actions</th>  -->
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(student, ind) in cache.students" v-bind:key="ind">
                                <td align="right">
                                  <b-form-checkbox class="check-item" v-model="cache.checked_list" @change.native="toggleSelectRow()" value="" number></b-form-checkbox>
                                </td>
                                <td>{{ student.lms_id }}</td>
                                <td>{{ student.student_name }}</td>
                                <td>{{ student.student_nick }}</td>
                                <td>
                                  <ul class="ctp-videos-list">
                                    <li v-for="(video, index) in student.videos" v-bind:key="index">
                                      <button v-b-tooltip.hover :title="video.title" class="btn ctp-video btn-sm btn-info btn-small" @click="loadCTP(student.lms_id, video.id)" :disabled="disabledCtp">
                                        <img :src="video.thumb" />
                                      </button>
                                    </li>
                                  </ul>
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
          <b-modal
              size="lg"
              title="CTP Detail"
              class="modal-primary"
              v-model="ctpDetailModal"
              @ok="closeVideoCTP"
              id="ctpDetailModel"
            >
            <div class="animated fadeIn apax-form">
              <div class="row">
                <div class="col-12">
                  <b-card header>
                    <div slot="header">
                      <i class="fa fa-clipboard"></i> <b class="uppercase">CTP Detail</b>
                    </div>
                    <div id="page-content">
                      <div class="row">
                        <div class="col-12">
                          <div class="row">
                            <div class="col-3">
                              <img src="static/img/avatars/noavatar.png" alt="" class="form-control std-img">
                            </div>
                            <div class="col-9">
                              <p><strong>{{ctp.student.name}}</strong></p>
                              <p>({{ctp.student.nick}}/{{ctp.student.lms_id}})</p>
                            </div>
                          </div>
                        </div>                                
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <b-card header>
                            <div slot="header">
                                <i class="fa fa-list"></i> <b class="uppercase">Students</b>
                            </div>
                            <div id="page-content">
                              <div class="row">
                                <div class="col-3" v-for="(member, index) in ctp.students" v-bind:key="index">
                                  <div class="row">
                                    <div class="col-5">
                                      <img src="static/img/avatars/noavatar.png" alt="" class="form-control std-img">
                                    </div>
                                    <div class="col-7">
                                      <p><strong>{{member.name}}</strong></p>
                                      <p>({{member.nick}}/{{member.lms_id}})</p>                                                
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </b-card>
                        </div> 
                      </div>
                      <div class="row">
                        <div class="col-12">
                        <b-card header>
                          <div slot="header">
                              <i class="fa fa-file-movie-o"></i> <b class="uppercase">Video Information</b>
                          </div>
                          <div id="page-content">
                            <div class="row">
                              <div class="col-6">
                                <strong class="ctp-text">Video CTP</strong><br/>
                                <video v-show="ctp.video.link_video" autoplay name="media" ref="videoRef" src="" controls width="640" height="360" style="width:640px;height:360px;" :poster="`http://#/thumbnail/${ctp.video.cjamvi_thumbnail_image}`">
                                </video>                                
                              </div>
                              <div class="col-6">
                                <strong>Đã giử CTP</strong> <input type="checkbox" checked class="form-control">
                              </div>  
                            </div>
                          </div>
                        </b-card>
                        </div> 
                      </div>
                    </div>
                  </b-card>
                </div>
              </div>
            </div>
          </b-modal>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>

import md5 from 'js-md5'
import moment from 'moment'
import tree from 'vue-jstree'
import file from '../../../components/File'

import u from '../../../utilities/utility'
import search from '../../../components/Search'
import apaxButton from '../../../components/Button'
import paging from '../../../components/Pagination'
import searchBranch from '../../../components/Selection'

export default {
  name: 'CTP-List',
  components: {
    tree,
    search,
    paging,
    apaxButton,
    searchBranch
  },
  data () {
    return {
      attached_file: '',
      book_ids: [],
      selectedBook: '',
      reserves: [],
      message: "",
      hasIssue: true,
      modal: false,
      completed: false,
      disableSaveCm: true,
      disabledCtp: false,
      hideSaveCm: 'hidden',
      placeholderSelectDate: 'Chọn ngày chủ nhiệm',
      formatSelectDate: 'yyyy/MM/dd',
      clearSelectedDate: true,
      disabledDaysOfWeek: [],
      cm_assign_date: '',
      cms: [],
      cm: '',
      ctp: {
        student: {
          name: '',
          nick: '',
          lms_id: 0
        },
        students: [],
        video: {
          title: '',
          cjamvi_thumbnail_image: '',
          link_video: ''
        }
      },
      item: {},
      scores: [],
      books: [],
      book: '',
      score: '',
      issue_list: [],
      issue: {
        student_id: '',
        classdate: '',
        book_id: '',
        cm_id: '',
        class_id: '',
        teacher_id: '',
        student_id: '',
        tc_gretest_strength: '',
        cm_gretest_strength: '',
        tc_need_improved: '',
        cm_need_improved: '',
        trail_report: '',
        class_term: '',
        summary: '',
        scoring_classroom_duty_attendance: '',
        scoring_classroom_duty_punctuality: '',
        scoring_classroom_duty_homework: '',
        scoring_reading_comprehension: '',
        scoring_reading_fluency: '',
        scoring_writing_sentence_structure: '',
        scoring_writing_creativity: '',
        scoring_ctp_speaking_expression: '',
        scoring_ctp_teamwork: '',
        scoring_social_skills_behavior: '',
        scoring_social_skills_confidence: '',
        scoring_social_skills_participation: '',
        parents_discussion: '',
        parents_comment: '',
        parents_evaluation: '',
        creator_id: ''
      },
      rank_score: '',
      rank_content: '',
      ctpDetailModal: false,
      addCTPModal: false,
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
              issue: false,
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
        }
      },
      action: {
        loading: false,
      },
      url: {
        api: '/api/issues/',
        class: '/api/ctp/class/',
        classes: '/api/issues/classes/',
        branches: '/api/issues/branches',
        semesters: '/api/issues/semesters',
        contracts: '/api/issues/contracts/'
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
        students: [],
        contracts: [],
        up_students: [],
        up_contracts: []
      },
      cache: {
        user_role: '',
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
        up_available: 0
      },
      class_date: {},
      up_class_date: {},
      showsave: false,
      order: {
        by: 's.id',
        to: 'DESC'
      },
      temp: {},
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
      }
    }
  },
  watch: {
  },
  created () {
    this.start()
    // this.checkIssueExist()
    u.a().get( `/api/issues/scores/list`).then(response => {
        this.scores = response.data
    })
    u.a().get( `/api/issues/books/list`).then(response => {
        this.books = response.data
    })
  },
  filters: {
    filterTuitionFee: (name, price) => price && price > 1000 ? `${name} - ${this.filterFormatCurrency(price)}` : name,
    filterFormatCurrency: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ'
  },
  watch: {
  },
  methods: {
    resetall(){

    },
    saveclasscm(){

    },
    prepareData(data) {
      const list = []
      if (data.length) {
        data.map(item=>{
          item.videos = this.listVideos(item.videos)
          list.push(item)
          return item
        })
      }
      return list
    },
    listVideos(videos) {
      const videosList = []
      if (videos && videos.length > 29) {
        const list = videos.split(';')
        if (list.length) {
          list.map(video=>{
            const item = video.split('|')
            videosList.push({
              id: item[0],
              title: item[1],
              thumb: '',
              link: item[3]
            })
            return video
          })
        }
      }
      return videosList
    },
    selectBook(id) {
      const selected_book = this.books.filter(item => item.id === id)
      if (selected_book.length) {
        const current_book = selected_book[0]
        this.issue.book_from = current_book.start_date
        this.issue.book_to = current_book.end_date
      }
    },
    checkRole() {
      const role_id = this.session.user.role_id
      const resp = parseInt(role_id) === 36
      return resp
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
    load(data, semester = false){
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
    loadUpSemester() {
      this.html.class.display.modal.register = false
      if (!this.html.class.display.modal.semester) {
        this.action.loading = true
      }
      u.a().get(this.link(1))
      .then(response => {
        const data = response.data.data
        this.load(data, 1)
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
      const branch_id = this.cache.branch
      this.loadSemesters()
    },
    selectSemester() {
      this.cache.semester = this.filter.semester
      u.log('Semester', this.filter.semester)
      u.a().get(`${this.url.classes}${this.cache.branch}/${this.cache.semester}`)
        .then(response => {
          const data = response.data.data
          this.cache.classes = data
          this.filter.class = ''
          this.html.class.display.filter.classes = 'display'
        }).catch(e => u.log('Exeption', e))
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
        this.showsave = true
        // u.log('Program', selected_class.model)
        this.cache.selected_class = selected_class
        this.action.loading = true
        this.cache.class = selected_class.model.item_id
        this.filter.class = this.cache.class
        u.a().get(`${this.url.class}${this.filter.class}`)
        .then(response => {
            const data = response.data.data
            this.cache.class_info = data.class
            const readyData = this.prepareData(data.students)
            this.cache.students = readyData
            // u.log('Class Data', this.cache.class_info)
            this.html.disable.add_contracts = false
            this.html.disable.up_semester = false
            this.html.class.display.class_info = 'display'
            this.html.class.display.contracts_list = 'display'
            this.action.loading = false
            this.getBookInfoByClass()
          }).catch(e => u.log('Exeption', e))
      }
    },
    loadCTP(student_id, ctp_id){
      u.g(`./api/ctp/${student_id}/${ctp_id}/get-ctp-info`).then(response => {
        this.ctp = response
        this.ctpDetailModal = true
        this.$refs.videoRef.src = response.video.link_video
        this.$refs.videoRef.play()
      })
    },
    closeVideoCTP() {
      this.$refs.videoRef.src = ''
      this.$refs.videoRef.pause()
    },
    loadIssueListByStudent(student_id){
      u.a().get(`./api/issues/${student_id}/issue-list`).then(response => {
        this.issue_list = response.data
      })
    },
    addIssue(student_id){
      this.issue.student_id = student_id
      this.addCTPModal = true
      this.resetValue()
  },
  validScoring() {
    let valid = true
      if (this.issue.scoring_classroom_duty_attendance === '') { 
        alert('Please select score for classroom duty attendance!') 
        valid = false
      }else if (this.issue.scoring_classroom_duty_punctuality === '') { 
        alert('Please select score for classroom duty punctuality!') 
        valid = false
      }else if (this.issue.scoring_classroom_duty_homework === '') { 
        alert('Please select score for classroom duty homework!') 
        valid = false
      }else if (this.issue.scoring_reading_comprehension === '') { 
        alert('Please select score for reading comprehension!') 
        valid = false
      }else if (this.issue.scoring_reading_fluency === '') { 
        alert('Please select score for reading fluency!') 
        valid = false
      }else if (this.issue.scoring_writing_sentence_structure === '') { 
        alert('Please select score for writing sentence structure!') 
        valid = false
      }else if (this.issue.scoring_writing_creativity === '') { 
        alert('Please select score for writing creativity!') 
        valid = false
      }else if (this.issue.scoring_ctp_speaking_expression === '') { 
        alert('Please select score for speaking expression!') 
        valid = false
      }else if (this.issue.scoring_ctp_teamwork === '') { 
        alert('Please select score for teamwork!') 
        valid = false
      }else if (this.issue.scoring_social_skills_behavior === '') { 
        alert('Please select score for social skills behavior!') 
        valid = false
      }else if (this.issue.scoring_social_skills_confidence === '') { 
        alert('Please select score for social skills confidence!') 
        valid = false
      }else if (this.issue.scoring_social_skills_participation === '') { 
        alert('Please select score for social skills participation!') 
        valid = false
      }
      return valid
    },
    saveIssue(){
      if (this.validScoring()) {
        const data = {
          book: this.book,
          class: this.cache.class_info,
          issue: this.issue
        }
        u.a().post('/api/issues', data)
        .then((response) => {
          this.message = `Created successfully.`
          this.modal = true
          this.completed = true
        })
      }
    },
    toggleSelectRow(){

    },
    updateIssue(){
      if (this.validScoring()) {
        const data = {
          book: this.book,
          class: this.cache.class_info,
          issue: this.issue
        }
        const issue_id  = this.issue.id
        u.a().put(`/api/issues/${issue_id}`, data)
        .then((response) => {
          this.message = `Updated successfully.`
          this.modal = true
          this.completed = true
        })
      }
    },
    getBookInfoByClass(){
      console.log('line 910');
    },
    checkIssueExist(){
      this.hasIssue = true;
    },
    completeIssue() {

    },
    completeUpSemester() {

    },
    cancelModal(){
      this.addIssueModal = false
      this.ctpDetailModal = false
    },
    closeModal() {
      if (this.completed) {
        this.selectClass(this.cache.selected_class)
        this.modal = false
      } else this.modal = false
      this.resetValue()
    },
    saveCTP(){

    },
    resetValue(){
      this.issue.book_id = '';
      this.issue.tc_gretest_strength = '';
      this.issue.cm_gretest_strength = '';
      this.issue.tc_need_improved = '';
      this.issue.cm_need_improved = '';
      this.issue.trail_report = '';
      this.issue.class_term = '';
      this.issue.summary = '';
      this.issue.scoring_classroom_duty_attendance = '';
      this.issue.scoring_classroom_duty_punctuality = '';
      this.issue.scoring_classroom_duty_homework = '';
      this.issue.scoring_reading_comprehension = '';
      this.issue.scoring_reading_fluency = '';
      this.issue.scoring_writing_sentence_structure = '';
      this.issue.scoring_writing_creativity = '';
      this.issue.scoring_ctp_speaking_expression = '';
      this.issue.scoring_ctp_teamwork = '';
      this.issue.scoring_social_skills_behavior = '';
      this.issue.scoring_social_skills_confidence = '';
      this.issue.scoring_social_skills_participation = '';
      this.issue.parents_discussion = '';
      this.issue.parents_comment = '';
      this.issue.parents_evaluation = '';
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
.float-left{
    float: left;
}
.book-title{
    margin-left: 8px;
}
#book-input{
    margin-left: 12px;
}
.evaluation-text{
    margin-left: 10px;
}
ul.ctp-videos-list {
  width:100%;
  list-style: none;
  float:left;
  margin:0;
}
ul.ctp-videos-list li {
  list-style: none;
  display: inline;
}
ul.ctp-videos-list li .ctp-video {
  width: 32px;
  margin: 0 5px;
  border-radius: 1px;
  height: 32px;
  background: #fff1f1;
  border: 1px solid #b76969;
  padding: 0;
}
ul.ctp-videos-list li .ctp-video img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}
.input-text{
    width: 570px;
}
.text-input{
    height: 60px;
}
.text-small{
  font-size: 10px;
}
.input-small{

}
.text-heading{
    font-size: 18px;
}
.text-table{
  font-size: 12px;
  font-weight: normal;
}
td{
  line-height: 15px;
  padding: 5px;
}
#btn-sm{
  margin-bottom: 5px;
  width: 30px;
  height: 30px;
  text-align: center;
  line-height: 20px;
}
.button-save-group{
  margin-right: 10px;
}
.btn-small{
  margin-top: 4px;
  border-radius: 20%;
}

.ctp-text{
  margin-left: 100px;
}
.std-img{
  width: 100px;
  height: 100px;
}
.std-button{
  margin-top: 30px;
}

</style>
