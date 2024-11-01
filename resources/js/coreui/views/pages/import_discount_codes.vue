<template>
  <div
    class="animated"
    id="students-import"
  >
    <div class="col-12">
      <b-card header>
        <div slot="header">
          <i class="fa fa-filter" /> <b class="uppercase">Form Import Discount Code</b>
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
                      <label>File Import DiscountCode:
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
                      <button
                        @click="downloadTemplateContract"
                        :disabled="stage"
                      >
                        Download template
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
                          <th>Mã chiết khấu</th>
                          <th>Tên</th>
                          <th>Tỷ lệ chiết khấu (%)</th>
                          <th>Ngày bắt đầu</th>
                          <th>Ngày kết thúc</th>
                          <th>Giá gốc</th>
                          <th>Tiền chiết khấu</th>
                          <th>Trạng thái</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(student, index) in students"
                          :key="index"
                        >
                          <td>{{ student.stt }}</td>
                          <td>{{ student.code }}</td>
                          <td>{{ student.name }}</td>
                          <td>{{ student.percent }}</td>
                          <td>{{ student.start_date }}</td>
                          <td>{{ student.end_date }}</td>
                          <td>{{ student.price }}</td>
                          <td>{{ student.discount }}</td>
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
import u from  '../../utilities/utility'

export default {
  name      : 'Import',
  components: {},
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
  created () {
  },
  methods: {
    uploadFile () {
      const formData = new FormData()
      formData.append('file', this.file)
      this.pending   = true
      if (!this.stage) {
        this.stage = true
        u.p('/api/upload/discount-codes', formData).then((response) => {
          this.students      = response.data
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
        u.p('/api/import/discount-codes', formData).then((response) => {
          this.students      = response.data
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
    downloadTemplateContract () {
      window.open('/api/discount-codes/download-template', '_blank')
    },
  },
}
</script>

<style>
  strong.result img {
    width: 30px;
  }
</style>
