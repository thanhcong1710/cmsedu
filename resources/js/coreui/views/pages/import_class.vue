<template>
  <div
    class="animated"
    id="students-import"
  >
    <div class="col-12">
      <b-card header>
        <div slot="header">
          <i class="fa fa-filter" /> <b class="uppercase">Form Import</b>
        </div>
        <div
          id="filter_content"
          class="apax-form"
        >
          <div class="row">
            <div class="col-lg-12">
              <div class="panel">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-lg-7">
                      <label>File Import Contract:
                        <input
                          type="file"
                          ref="file"
                          @change="handleFileUpload()"
                        >
                      </label>
                      <button
                        @click="uploadFile()"
                        :disabled="stage"
                      >
                        Import
                      </button>
                      <strong class="result"><img
                        src="/img/pending.gif"
                        v-if="pending"
                      >{{ notify }}</strong>
                    </div>
                  </div>
                </div>
                <div
                  class="panel-content"
                  v-show="data && data.length>0"
                >
                  <div class="table-responsive scrollable">
                    <div v-for="(item, index) in data">
                      <div :key="index">
                        <div class="row">
                          <div class="col-md-6">
                            <ul>
                              <li>STT: <b>{{index+1}}</b></li>
                              <li>Mã trung tâm: <b>{{ item && item.class && item.class.branch_code }}</b></li>
                              <li>Học kỳ: <b>{{ item && item.class && item.class.semester_code }}</b></li>
                              <li>Lớp học: <b>{{ item && item.class && item.class.class_name }}</b></li>
                              <li>Ca học: <b>{{ item && item.class && item.class.school_shift }}</b></li>
                              <li>Ngày bắt đầu học: <b>{{ item && item.class && item.class.start_date }}</b></li>
                              <li>Ngày học trong tuần: <b>{{ item && item.class && item.class.day_of_week }}</b></li>
                              <li>Giáo viên: <b>{{ item && item.class && item.class.teacher_name }}</b></li>
                              <li>Phòng học: <b>{{ item && item.class && item.class.room_name }}</b></li>
                            </ul>
                          </div>
                          <div class="col-md-6">
                            <div v-if="Array.isArray(item.class.message)">
                              <ul
                                v-for="(mess, pos) in item.class.message"
                                :key="pos"
                                :style="item.class.message !=='Insert success' ? 'color:red' : 'color: green'"
                              >
                                <li style="text-align: left">
                                  {{ mess }}
                                </li>
                              </ul>
                            </div>
                            <div v-else>
                              {{ item.class.message || "OK" }}
                            </div>
                          </div>
                        </div>
                        <div class="row" v-if="item && item.students && item.students.length>0">
                          <div class="table-responsive scrollable">
                            <table
                              id="apax-printing-students-list"
                              class="table table-striped table-bordered apax-table"
                            >
                              <thead>
                                <tr >
                                  <th v-for="(filed, key) in item.students[0]"
                                      :key="key"
                                  >{{key}}</th>
                                  <th>Trạng thái</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr
                                  v-for="(student, index) in item.students"
                                  :key="index"
                                >
                                  <td v-for="(filed, key) in student"
                                      :key="key"><div v-if="key !== 'message'">{{filed}}</div></td>
                                  <td
                                    :style="student.message && student.message !=='Insert success' ? 'color:red' : 'color: green'"
                                  >
                                    <div v-if="Array.isArray(student.message)">
                                      <ul
                                        v-for="(mess, pos) in student.message"
                                        :key="pos"
                                      >
                                        <li style="text-align: left">
                                          {{ mess }}
                                        </li>
                                      </ul>
                                    </div>
                                    <div v-else>
                                      {{ student.message ||  "OK" }}
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <hr>
                      </div>
                    </div>
                  </div>
                  <button
                    v-show="executeImport"
                    class="btn btn-info"
                    @click="executeImportContract"
                  >
                    Thực hiện thêm vào cơ sở dữ liệu
                  </button> &nbsp;
                </div>
              </div>
            </div>
          </div>
        </div>
      </b-card>
    </div>
  </div>
</template>
<script>
import u from '../../utilities/utility'

export default {
  name      : 'Import',
  components: {},
  data () {
    return {
      file         : '',
      stage        : false,
      notify       : '',
      pending      : false,
      data         : [],
      executeImport: false,
    }
  },
  created () {
  },
  methods: {
    uploadFile () {
      const formData = new FormData()
      formData.append('file', this.file)
      this.pending   = true
      if (!this.stage) {
        this.stage = true
        u.p('/api/upload/classes', formData).then((response) => {
          this.data          = response.data
          this.executeImport = response.success
          this.notify        = response.message
          this.pending       = false
          this.stage         = false
        })
      }
    },
    executeImportContract () {
      const formData = new FormData()
      formData.append('file', this.file)
      this.pending   = true
      if (!this.stage) {
        this.stage = true
        u.p('/api/import/classes', formData).then((response) => {
          this.data          = response.data
          this.executeImport = response.success
          this.notify        = response.message
          this.pending       = false
          this.stage         = false
        })
      }
    },
    handleFileUpload () {
      this.file  = this.$refs.file.files[0]
      this.stage = false
    },
  },
}
</script>

<style>
  strong.result img {
    width: 30px;
  }
</style>
