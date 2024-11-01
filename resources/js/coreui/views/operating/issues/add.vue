<template>
  <b-modal
    size="lg"
    :title="isBrightIGType ? 'Nhận xét học sinh học IB' : 'Nhận xét học sinh học UCREA'"
    class="modal-primary"
    v-model="display"
    id="add-feedback"
    hide-footer
    @close="handleCloseModal"
  >
    <div v-if="this.loading">
      Loading...
    </div>
    <div
      v-else
      class="animated fadeIn apax-form"
    >
      <div class="row">
        <div
          class="col-6"
          style="text-align: right"
        >
          <b>Tên học sinh: </b>
        </div>
        <div class="col-6">
          <span>{{ student && student.student_name }}</span>
        </div>
        <div
          class="col-6"
          style="text-align: right"
        >
          <b>Mã CMS: </b>
        </div>
        <div class="col-6">
          <span>{{ student && student.crm_id }}</span>
        </div>
        <div
          class="col-6"
          style="text-align: right"
        >
          <b>Chương trình: </b>
        </div>
        <div class="col-6">
          <span>{{ clazz && clazz.program_name }}</span>
        </div>
        <div
          class="col-6"
          style="text-align: right"
        >
          <b>Lớp học: </b>
        </div>
        <div class="col-6">
          <span>{{ clazz && clazz.class_name }}</span>
        </div>
        <div
          class="col-6"
          style="text-align: right"
          v-if="$route.params.type === 'date'"
        >
          <b>Ngày học: </b>
        </div>
        <div
          class="col-6"
          v-if="$route.params.type === 'date'"
        >
          <span>{{ date }}</span>
        </div>
      </div>
      <div
        v-if="isClass"
        class="animated fadeIn apax-form"
      >
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">1. Trình độ phù hợp với học sinh</label>
              <div>
                <select-product
                  v-model="form.level"
                  placeholder="Chọn trình độ"
                  :multiple="false"
                  track-by="accounting_id"
                  :disabled="readOnly"
                />
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">2. Nhận xét chung</label>
              <div>
                <textarea
                  :readOnly="readOnly"
                  class="form-control discount-description"
                  type="text"
                  style="min-height: 150px;"
                  v-model="form.general_comment"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else-if="isBrightIGType">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">1. Nội dung bài học</label>
              <div>
                <textarea
                  :readOnly="readOnly"
                  class="form-control discount-description"
                  type="text"
                  v-model="form.lesson_content"
                />
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">2. Nhận xét chung</label>
              <div>
                <textarea
                  :readOnly="readOnly"
                  class="form-control discount-description"
                  type="text"
                  v-model="form.general_comment"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">1. Kỹ năng tư duy cơ bản</label>
              <div>
                <textarea
                  class="form-control discount-description"
                  type="text"
                  :readOnly="readOnly"
                  v-model="form.base_skills"
                />
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">2. Kỹ năng tư duy toán học</label>
              <div>
                <textarea
                  class="form-control discount-description"
                  type="text"
                  :readOnly="readOnly"
                  v-model="form.math_skills"
                />
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">2. Kỹ năng tư duy logic</label>
              <div>
                <textarea
                  class="form-control discount-description"
                  type="text"
                  :readOnly="readOnly"
                  v-model="form.logic_skills"
                />
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">4. Kỹ năng tư duy sáng tạo</label>
              <div>
                <textarea
                  class="form-control discount-description"
                  type="text"
                  :readOnly="readOnly"
                  v-model="form.creative_skills"
                />
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">5. Nhận xét chung</label>
              <div>
                <textarea
                  :readOnly="readOnly"
                  class="form-control discount-description"
                  type="text"
                  v-model="form.general_comment"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row action">
        <button
          v-if="!readOnly"
          class="btn btn-success"
          @click="handleSaveFeedback"
        >
          <i class="fa fa-save"> Lưu</i>
        </button> &nbsp;
        <button
          v-if="readOnly && $route.params.type === 'date'"
          class="btn btn-success"
          @click="printFeedback"
        >
          <i class="fa fa-print"> In đơn nhận xét</i>
        </button> &nbsp;
        <button
          class="btn btn-red"
          @click="handleCloseModal"
        >
          <i class="fa fa-close"> Đóng</i>
        </button> &nbsp;
      </div>
    </div>
  </b-modal>
</template>

<script>
import u from '../../../utilities/utility'
import SelectProduct from '../../reports/common/product-select'

export const FORM_TYPE = {
  BRIGHT_IG : 'BRIGHT IG',
  BLACK_HOLE: 'BLACK HOLE',
  UCREA     : 'UCREA',
}
export default {
  name      : 'AddFeedback',
  components: { SelectProduct },
  props     : {
    displayModal: {
      type   : Boolean,
      default: false,
    },
    feedbackId: {
      type   : Number,
      default: 0,
    },
    type: {
      type   : String,
      default: FORM_TYPE.BRIGHT_IG,
    },
    student: {
      type   : Object,
      default: () => ({ name: '', accounting_id: null }),
    },
    program: {
      type   : Object,
      default: () => ({ name: '' }),
    },
    clazz: {
      type   : Object,
      default: () => ({ class_name: '' }),
    },
    readOnly: {
      type   : Boolean,
      default: false,
    },
    date: {
      type   : String,
      default: '',
    },
  },
  data () {
    return {
      display : false,
      loading : true,
      feedback: null,
      form    : this.initFormData(),
    }
  },
  watch: {
    displayModal () {
      this.display = this.displayModal
    },
    feedbackId (newId, oldId) {
      if (newId !== oldId)
        this.getFeedback(newId)
    },
  },
  computed: {
    isClass () {
      return this.$route.params.type === 'class'
    },
    isBrightIGType () {
      return this.type === FORM_TYPE.BRIGHT_IG || this.type === FORM_TYPE.BLACK_HOLE
    },
  },
  created () {
    this.getFeedback(this.feedbackId)
  },
  methods: {
    initFormData () {
      return {
        lesson_content : '',
        general_comment: '',
        base_skills    : '',
        math_skills    : '',
        logic_skills   : '',
        creative_skills: '',
        level          : '',
      }
    },
    getFeedback (feedbackId) {
      if (!feedbackId) {
        this.loading = false
        return
      }

      this.loading = true
      u.a().get(`/api/feedback/${feedbackId}`)
        .then((response) => {
          const feedback = response.data.data
          this.feedback  = feedback
          if (feedback) {
            this.form = {
              lesson_content : feedback.lesson_content,
              general_comment: feedback.general_comment,
              base_skills    : feedback.base_skills,
              math_skills    : feedback.math_skills,
              logic_skills   : feedback.logic_skills,
              creative_skills: feedback.creative_skills,
              level          : feedback.level,
            }
          }
          this.loading = false
        })
    },
    handleCloseModal () {
      this.form     = this.initFormData()
      this.feedback = null
      this.$emit('close')
    },
    handleSaveFeedback () {
      const classId   = _.get(this, 'clazz.class_id')
      const studentId = _.get(this, 'student.student_id')
      const params    = Object.assign({}, this.form, {
        program      : this.program,
        clazz        : this.clazz,
        student      : this.student,
        student_id   : studentId,
        class_id     : classId,
        classdate    : this.date,
        level        : this.form.level && this.form.level.accounting_id,
        feedback_type: this.$route.params.type === 'class' ? 1 : 0,
      })
      const promise   = this.feedback ? u.a().put(`/api/feedback/${this.feedback.id}`, params)
        : u.a().post(`/api/feedback`, params)
      promise.then(() => {
        this.handleCloseModal()
      })
    },
    printFeedback () {
      const classId   = _.get(this, 'clazz.class_id')
      const studentId = _.get(this, 'student.student_id')
      window.open(`/print/feedback/${classId}/${studentId}/${this.date}`, '_blank')
    },
  },
}
</script>

<style scoped>
    .action {
        margin-top: 20px;
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        padding-right: 14px;
    }

    .row {
        margin-top: 10px;
    }
</style>
