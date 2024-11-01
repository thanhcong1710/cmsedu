<template>
  <div
    class="animated"
    id="students-import"
  >
    <div class="col-12">
      <b-card header>
        <div slot="header">
          <i class="fa fa-filter" /> <b class="uppercase">Import bảo lưu</b>
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
                    <div class="col-lg-12">
                      <div style="display:flex; flex-direction: row">
                        <div style="flex: 1; display: flex; flex-direction: row">
                          <input
                            type="file"
                            ref="file"
                            @change="handleFileUpload()"
                          >
                          <button
                            v-if="enableValidateButton"
                            @click="uploadFile()"
                            :disabled="stage"
                          >
                            Kiểm tra dữ liệu
                          </button>
                          <strong class="result"><img
                            src="/img/pending.gif"
                            v-if="stage"
                          >{{ notify }}</strong>
                        </div>
                        <button
                          @click="downloadTemplate"
                          :disabled="stage"
                        >
                          Tải mẫu
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="panel-content"
                  v-show="students && students.length>0"
                >
                  <div class="table-responsive scrollable">
                    <table
                      id="apax-printing-students-list"
                      class="table table-striped table-bordered apax-table"
                    >
                      <thead>
                        <tr>
                          <th>STT</th>
                          <th>Mã học viên</th>
                          <th>Ngày bắt đầu</th>
                          <th>Số buổi bảo lưu</th>
                          <th>Loai bảo lưu</th>
                          <th>Ghi chú</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(student, index) in students"
                          :key="index"
                        >
                          <td>{{ student.stt }}</td>
                          <td>{{ student.student_code }}</td>
                          <td>{{ student.start_date }}</td>
                          <td>{{ student.number_of_reserves }}</td>
                          <td>{{ student.reserve_type }}</td>
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
                              {{ student.message || "OK" }}
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <button
                    v-show="executeImport"
                    class="btn btn-info"
                    @click="handleExecuteImport"
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
  name: 'Import',
  data () {
    return {
      file         : '',
      stage        : false,
      notify       : '',
      pending      : false,
      students     : [],
      executeImport: false,
    }
  },
  computed: {
    enableValidateButton () {
      return this.file !== ''
    },
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
        u.p('/api/reserves/validate-import', formData).then((response) => {
          this.students      = response.data
          this.executeImport = response.success
          this.notify        = response.message
          this.pending       = false
          this.stage         = false
        })
      }
    },
    handleExecuteImport () {
      const formData = new FormData()
      formData.append('file', this.file)
      this.pending   = true
      if (!this.stage) {
        this.stage = true
        u.p('/api/reserves/exec-import', formData).then((response) => {
          this.students      = response.data
          this.executeImport = response.success
          this.notify        = response.message
          this.pending       = false
          this.stage         = false
        })
      }
    },
    handleFileUpload () {
      this.file = this.$refs.file.files[0]
    },
    downloadTemplate () {
      window.open('/api/reserves/download-template-import', '_blank')
    },
  },
}
</script>

<style>
  strong.result img {
    width: 30px;
  }
</style>
