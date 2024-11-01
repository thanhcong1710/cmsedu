<template>
  <div class="tab-student-enrolment apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-list-ul"></i> <b class="uppercase">Trial Report</b>
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-striped table-bordered special apax-table">
            <thead>
              <tr class="text-sm">
                <th width="120px">STT Buổi Học</th>
                <th width="120px">Ngày học</th>
                <th width="">Nội dung đánh giá</th>
                <th width="">Ghi chú</th>
                <th width="230px">File đính kèm</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td width="120px">Buổi Học trải nghiệm</td>
                <td width="120px">
                  <input  class="form-control" type="date" v-model="reports.date1"/>
                </td>
                <td class="comment-txt">
                  <textarea class="form-control" rows="4" v-model="reports.comm1"></textarea>
                </td>
                <td class="note-txt">
                  <textarea class="form-control" rows="4" v-model="reports.note1"></textarea>
                </td>
                <td width="300px">
                  <file
                    :label="'Trial Report'"
                    :name="'trial_report'"
                    :alias="`Trial_Report-${(student.name || '').replace(/\s+/g, '_')}-${student.crm_id}${student.lms_id || ''}-Lession_1`"
                    :field="'file1'"
                    :type="'transfer_file'"
                    :short=8
                    :full=false
                    :link="validFile(reports.file1)"
                    :onChange="uploadFile"
                    :title="'Tải lên 1 file đính kèm định dạng: jpg, pdf, docx, xlsx.'"
                  >
                  </file>
                </td>
              </tr>
              <!--<tr>-->
                <!--<td width="120px">Buổi Học trải nghiệm 2</td>-->
                <!--<td width="120px">-->
                  <!--<input  class="form-control" type="date" v-model="reports.date2"/>-->
                <!--</td>-->
                <!--<td class="comment-txt">-->
                    <!--<textarea class="form-control" rows="4" v-model="reports.comm2"></textarea>-->
                <!--</td>-->
                <!--<td class="note-txt">-->
                   <!--<textarea class="form-control" rows="4" v-model="reports.note2"></textarea>-->
                <!--</td>-->
                <!--<td width="300px">-->
                  <!--<file-->
                    <!--:label="'Trial Report 2'"-->
                    <!--:name="'trial_report_2'"-->
                    <!--:alias="`Trial_Report-${student.name.replace(/\s+/g, '_')}-${student.crm_id}${student.lms_id || ''}-Lession_2`"-->
                    <!--:field="'file2'"-->
                    <!--:type="'transfer_file'"-->
                    <!--:short=8-->
                    <!--:full=false-->
                    <!--:link="validFile(reports.file2)"-->
                    <!--:onChange="uploadFile"-->
                    <!--:title="'Tải lên 1 file đính kèm định dạng: jpg, pdf, docx, xlsx.'"-->
                  <!--&gt;-->
                  <!--</file>-->
                <!--</td>-->
              <!--</tr>-->
              <!--<tr>-->
                <!--<td width="120px">Buổi Học trải nghiệm 3</td>-->
                <!--<td width="120px">-->
                  <!--<input  class="form-control" type="date" v-model="reports.date3"/>-->
                <!--</td>-->
                <!--<td class="comment-txt">-->
                    <!--<textarea class="form-control" rows="4" v-model="reports.comm3"></textarea>-->
                <!--</td>-->
                <!--<td class="note-txt">-->
                  <!--<textarea class="form-control" rows="4" v-model="reports.note3"></textarea>-->
                <!--</td>-->
                <!--<td width="300px">-->
                  <!--<file-->
                    <!--:label="'Trial Report 3'"-->
                    <!--:name="'trial_report_3'"-->
                    <!--:alias="`Trial_Report-${student.name.replace(/\s+/g, '_')}-${student.crm_id}${student.lms_id || ''}-Lession_3`"-->
                    <!--:field="'file3'"-->
                    <!--:type="'transfer_file'"-->
                    <!--:short=8-->
                    <!--:full=false-->
                    <!--:link="validFile(reports.file3)"-->
                    <!--:onChange="uploadFile"-->
                    <!--:title="'Tải lên 1 file đính kèm định dạng: jpg, pdf, docx, xlsx.'"-->
                  <!--&gt;-->
                  <!--</file>-->
                <!--</td>-->
              <!--</tr>-->
            </tbody>
          </table>
          <b-button type="button" class="trial-comment" variant="success" @click="updateTrialComment()"><i class="fa fa-print"></i> Cập Nhật Trial Report</b-button>
        </div>
      </div>
    </b-card>
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử đăng ký lớp học</b>
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th class="width-50">STT</th>
                <th class="width-150">Ngày thực hiện</th>
                <th class="width-150">Người thực hiện</th>
                <th class="width-150">Trung tâm</th>
                <th class="width-150">Kỳ học</th>
                <th class="width-150">Lớp</th>
                <th class="width-150">Sản phẩm</th>
                <th class="width-150">Chương trình</th>
                <th class="width-150">Gói phí</th>
                <th class="width-150">Số buổi theo gói phí</th>
                <th class="width-150">Số buổi học bổng</th>
                <th class="width-150">Tổng số buổi được học</th>
                <th class="width-150">Số buổi đã học</th>
                <th class="width-150">Loại khách hàng</th>
                <th class="width-150">Trạng thái</th>
                <th class="width-150">Ngày bắt đầu</th>
                <th class="width-150">Ngày kết thúc</th>
                <!-- <th class="width-150">Thao tác</th>
                <th class="width-150">Nhận xét của giáo viên</th> -->
                <th class="width-150">Điểm danh</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(enrolment, index) in list" :key="index">
                <td>{{index+1}}</td>
                <td>{{enrolment.created_at}}</td>
                <td>{{enrolment.creator}}</td>
                <td>{{enrolment.branch_name}}</td>
                <td>{{enrolment.semester}}</td>
                <td>{{enrolment.class_name}}</td>
                <td>{{enrolment.product_name}}</td>
                <td>{{enrolment.program_name}}</td>
                <td>{{enrolment.tuition_fee_name}}</td>
                <td>{{enrolment.real_sessions}}</td>
                <td>{{enrolment.bonus_sessions}}</td>
                <td>{{enrolment.summary_sessions}}</td>
                <td>{{enrolment.enrolment_real_sessions}}</td>
                <td>{{enrolment.contract_type | contractType}}</td>
                <td>{{enrolment.enrolment_status | contractStatus}}</td>
                <td>{{enrolment.start_date}}</td>
                <td>{{enrolment.last_date}}</td>
                <td>
                    <detail-attendance
                       button-name="Điểm danh"
                       title="Chi tiết điểm danh"
                       :class-name="enrolment.class_name"
                       :attendances="getAttendanceListByClassId(enrolment.class_id)"
                    />
                </td>
                <!-- <td>
                  <div v-if="enrolment.withdraw === 1" v-b-tooltip.hover title="Click vào đây để withdraw học sinh này luôn!" :class="enrolment.withdraw === 1 ? 'withdraw-button' : ''" @click="remove(enrolment)" v-html="studentStatus(enrolment.enrolment_status, enrolment.withdraw)"></div>
                  <div v-else v-html="studentStatus(enrolment.status, enrolment.withdraw_now)"></div>
                  <div v-if="((session.user.role_id >= 86868686 && enrolment.withdraw === 0 ) || enrolment.contract_type === 0) && enrolment.enrolment_status > 0" v-b-tooltip.hover title="Click vào đây để withdraw học sinh này luôn!" class="withdraw-button apax-btn primary" @click="remove(enrolment)"><i class="fa fa-close fa-lg"></i></div>
                </td>
                <td>
                  <span class="apax-btn detail disabled" v-if="enrolment.contract_type==0">
                    <span @click="showCommentTrialModal"><i class="fa fa-eye"></i></span>
                  </span>
                  <span class="apax-btn detail disabled info" v-else>
                  <span @click="showCommentModal"><i class="fa fa-eye"></i></span>
                  </span>
                </td> -->
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import file from '../../components/File'
import abt from '../../components/Button'
import datePicker from 'vue2-datepicker'
import detailAttendance from './detail-attendance'

export default {
  props: {
    list: {
      type: Array,
      default: () => []
    },
    student: {
      type: Object,
      default() {
        return {
          name: '',
          lms_id: '',
          crm_id: ''
        }
      }
    },
    reports: {
      type: Object,
      default() {
        return {
          date1: '',
          comm1: '',
          note1: '',
          file1: '',
          date2: '',
          comm2: '',
          note2: '',
          file2: '',
          date3: '',
          comm3: '',
          note3: '',
          file3: '',
        };
      }
    }
  },
  data() {
    return {
      session: u.session()
    }
  },
  watch: {
    reports(data) {}
  },
  created() {
  },
  components: { file, abt, datePicker, "detail-attendance": detailAttendance },
  methods: {
    getAttendanceListByClassId(classId){
      return _.get(this, `student.attendance[${classId}]`)
    },
    callback() {
      this.html.dom.modal.display = false;
      if (this.expired) {
        u.go(this.$router, "/login");
      }
      if (this.completed) {
        u.go(this.$router, "/students/list/1");
      }
    },
    uploadFile(file, param = null) {
      u.log('PR', param)
      if (param) {
        this.reports[param] = file;
      }
    },
    validFile(file) {
      let resp = ''
      if (file) {
        resp = typeof file === "string" ? file : "";
        if (typeof file === "object") {
          resp = `${file.type},${file.data}`;
        }
      }
      return resp;
    },
    studentStatus(v, w = 0) {
        let resp = ""
        if (w) {
            resp = parseInt(v) === 1 ? '<div class="alert-label apax-label">Withdraw</div>' : parseInt(v) === 2 ? '<div class="primary-label apax-label">Transfering</div>' : '<div class="disable-label apax-label">Withdraw</div>'
        } else {
            resp = parseInt(v) === 1 ? '<div class="success-label apax-label">Active</div>' : parseInt(v) === 2 ? '<div class="primary-label apax-label">Transfering</div>' : '<div class="disable-label apax-label">Withdraw</div>'
        }
        return resp
    },
    remove(enrolment) {
        const confirmation = confirm("Bạn có chắc là muốn withdraw học sinh này không?")
        if (confirmation) {
            u.g(`${this.url.withdraw}${enrolment.enrolment_id}`)
            .then(response => {
              alert('Đã withdraw học sinh thành công!')
              this.$router.push('/students')
            }).catch(e => u.log('Exeption', e))
        }
    },
    updateTrialComment() {
      let valid = true
      let messa = '\n'
      if (this.reports.date1 || this.reports.comm1 || this.reports.file1) {
        if (!this.reports.date1) {
          valid = false
          messa += 'Ngày của buổi Học trải nghiệm 1 chưa được chọn.\n';
        }
        if (!this.reports.comm1) {
          valid = false
          messa += 'Nội dung đánh giá của buổi Học trải nghiệm 1 chưa được nhập.\n'
        }
        if (!this.reports.file1) {
          valid = false
          messa += 'Tệp đính kèm báo cáo của buổi Học trải nghiệm 1 chưa tải lên.\n'
        }
      }
      if (this.reports.date2 || this.reports.comm2 || this.reports.file2) {
        if (!this.reports.date2) {
          valid = false
          messa += 'Ngày của buổi Học trải nghiệm 2 chưa được chọn.\n'
        }
        if (!this.reports.comm2) {
          valid = false
          messa += 'Nội dung đánh giá của buổi Học trải nghiệm 2 chưa được nhập.\n'
        }
        if (!this.reports.file2) {
          valid = false
          messa += 'Tệp đính kèm báo cáo của buổi Học trải nghiệm 2 chưa tải lên.\n'
        }
      }
      if (this.reports.date3 || this.reports.comm3 || this.reports.file3) {
        if (!this.reports.date3) {
          valid = false
          messa += 'Ngày của buổi Học trải nghiệm 3 chưa được chọn.\n'
        }
        if (!this.reports.comm3) {
          valid = false
          messa += 'Nội dung đánh giá của buổi Học trải nghiệm 3 chưa được nhập.\n'
        }
        if (!this.reports.file3) {
          valid = false
          messa += 'Tệp đính kèm báo cáo của buổi Học trải nghiệm 3 chưa tải lên.\n'
        }
      }
      let confirmed = true
      if (!valid) {
        alert(`Chú ý! Thông tin báo cáo Học trải nghiệm chưa hoàn chỉnh:\n------------------------------------------------------------------------------------\n${messa}\n------------------------------------------------------------------------------------\n`)
      } else {
        if (!this.reports.date1 && !this.reports.comt1 && !this.reports.file1 && !this.reports.date2 && !this.reports.comt2 && !this.reports.file2 && !this.reports.date3 && !this.reports.comt3 && !this.reports.file3) {
          alert(`Chú ý! Xin vui lòng nhập nội dung báo cáo Học trải nghiệm.`)
        } else {
          confirmed = confirm('Bạn có chắc chắn muốn cập nhật báo cáo Học trải nghiệm?');
          if (confirmed) {
            const data = {
              stuid: this.$route.params.id,
              date1: this.reports.date1 || '',
              comt1: this.reports.comm1 || '',
              note1: this.reports.note1 || '',
              file1: this.reports.file1 || [],
              date2: this.reports.date2 || '',
              comt2: this.reports.comm2 || '',
              note2: this.reports.note2 || '',
              file2: this.reports.file2 || [],
              date3: this.reports.date3 || '',
              comt3: this.reports.comm3 || '',
              note3: this.reports.note3 || '',
              file3: this.reports.file3 || []
            }
            u.log('Meta Data', data)
            u.p('/api/students/update/trial/comment', data)
            .then(response => {
              this.$notify({
                  group: 'apax-atc',
                  title: 'Thông Báo!',
                  type: 'success dark',
                  duration: 3000,
                  text: 'Báo cáo Học trải nghiệm đã được cập nhật thành công!'
              })
            })
          }
        }
      }
    },
    studentStatus(v) {
      return parseInt(v) === 1 ? '<div class="success-label apax-label">Active</div>' : parseInt(v) === 2 ? '<div class="primary-label apax-label">Transfering</div>' : '<div class="alert-label apax-label">Withdraw</div>'
    }
  },

};
</script>

<style scoped>
#enrolment-history table.special.apax-table td {
  padding: 0px 5px!important;
  vertical-align: middle;
  margin: 0!important;
}
.apax-file.upload {
  margin: 10px 0 0 0;
  padding: 0;
}
</style>
