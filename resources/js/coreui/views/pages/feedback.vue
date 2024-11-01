<template>
  <div id="apax-printing-detail">
    <div id="apax-printing-tuition-tran">
      <div class="print-box">
        <div class="container">
          <div class="print-body">
            <div class="inner">
              <div
                v-show="flags.requesting"
                class="ajax-load content-loading"
              >
                <div class="load-wrapper">
                  <div class="loader" />
                  <div
                    v-show="flags.requesting"
                    class="loading-text cssload-loader"
                  >
                    Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...
                  </div>
                </div>
              </div>
              <div class="print-header">
                <div class="h-left">
                  <span class="logo"><img src="/images/print-logo.png"></span>
                </div>
                <div class="h-right">
                  <h4>CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ</h4>
                  <p>
                    <i class="fa fa-map-marker" /> Tầng 4, tòa 21T2 Hapulico Complex, 81 Vũ Trọng Phụng, Thanh Xuân, Hà
                    Nội
                  </p>
                  <p>
                    <i class="fa fa-phone" />(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope" /> cms@cmsedu.vn
                    &nbsp;&nbsp;<i class="fa fa-globe" /> www.cmsedu.vn
                  </p>
                </div>
              </div>
              <hr>
              <div
                class="print-content"
                style="margin-top: 50px"
              >
                <h2 class="title">
                  NHẬN XÉT HỌC SINH
                </h2>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="input-line">
                      <span class="label"><b>Họ và tên:</b> {{ item && item.student && item.student.name }}</span>
                      <i class="doted" />
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="input-line">
                      <span class="label"><b>Lớp:</b> {{ item && item.class && item.class.cls_name }}</span>
                      <i class="doted" />
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="input-line">
                      <span class="label"><b>Ngày học:</b> {{ $route.params.date }}</span>
                      <i class="doted" />
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="input-line">
                      <span class="label"><b>Giáo viên:</b> {{ item && item.class && item.class.teacher && item.class.teacher.ins_name }}</span>
                      <i class="doted" />
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="input-line">
                      <span class="label"><b>Tên bài học:</b></span>
                      <i class="doted" />
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="input-line">
                      <span class="label"><b>Mục tiêu:</b></span>
                      <i class="doted" />
                    </div>
                  </div>
                </div>
                <table
                  class="table table-bordered sign"
                  style="margin-top: 50px"
                >
                  <thead>
                    <tr>
                      <th><strong>CÁC KỸ NĂNG</strong></th>
                      <th><strong>HOẠT ĐỘNG</strong></th>
                    </tr>
                  </thead>
                  <tbody v-if="isUrea">
                    <tr>
                      <td class="table-content">
                        <span class="label">Kỹ năng tư duy cơ bản</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.base_skills }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="table-content">
                        <span class="label">Kỹ năng tư duy toán học</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.math_skills }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="table-content">
                        <span class="label">Kỹ năng tư duy logic</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.logic_skills }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="table-content">
                        <span class="label">Kỹ năng tư duy sáng tạo</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.creative_skills }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="table-content">
                        <span class="label">Nhận xét chung</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.general_comment }}</span>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr>
                      <td class="table-content">
                        <span class="label">Nội dung bài học</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.lesson_content }}</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="table-content">
                        <span class="label">Nhận xét chung</span>
                      </td>
                      <td class="table-content">
                        <span class="label">{{ item && item.feedback && item.feedback.general_comment }}</span>
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
</template>

<script>
import u from '../../utilities/utility'

export default {
  name: 'Feedback',
  data () {
    return {
      item : {},
      flags: { form_loading: false },
    }
  },
  created () {
    this.getPrintData()
  },
  computed: {
    isUrea () {
      return _.get(this, 'item.type') === 'UCREA'
    },
  },
  methods: {
    getPrintData () {
      this.flags.form_loading = true
      u.a().get(`/api/feedback/print/${this.$route.params.class_id}/${this.$route.params.student_id}/${this.$route.params.date}`)
        .then((resp) => {
          this.flags.form_loading = false
          this.item               = resp.data.data
          // setTimeout(function () {
          //   window.print()
          // }, 500)
        })
    },
  },
}
</script>

<style scoped>
  .table-content {
    text-align: left;
    text-transform: none;
    font-weight: unset;
  }
</style>
