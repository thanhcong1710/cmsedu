<template>
  <div class="tab-student-info apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-address-card-o"></i> <b class="uppercase">Hồ sơ học sinh: {{data.name}}</b>
      </div>
      <div class="panel">
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-4">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Mã CMS</label>
                    <input class="form-control" type="text" :value="data.crm_id" readonly>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Mã Cyber</label>
                    <input class="form-control" type="text" :value="data.accounting_id" readonly>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tên học sinh  <span class="text-danger"> (*)</span></label>
                <input class="form-control" type="text" :value="data.name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group select-date">
                <label class="control-label">Ngày sinh  <span class="text-danger"> (*)</span></label>
                <input class="form-control" type="text" :value="data.date_of_birth | formatDate" readonly>
              </div>
            </div>
            <!--<div class="col-sm-4">-->
              <!--<div class="form-group">-->
                <!--<label class="control-label">Mã EFFECT</label>-->
                <!--<input class="form-control" type="text" :value="data.accounting_id" readonly>-->
              <!--</div>-->
            <!--</div>-->
            <!--<div class="col-sm-4">-->
              <!--<div class="form-group">-->
                <!--<label class="control-label">Mã LMS</label>-->
                <!--<input class="form-control" type="text" :value="data.stu_id" readonly>-->
              <!--</div>-->
            <!--</div>-->
          </div>
          <div class="row">

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Giới tính</label>
                <input class="form-control" type="text" :value="data.gender | genderToName" readonly>
              </div>
            </div>
            <!-- <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Số điện thoại</label>
                <input class="form-control" type="text" :value="data.phone" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group select-date">
                <label class="control-label">Tên thường gọi/ Nick name</label>
                <input class="form-control" type="text" :value="data.email" readonly>
              </div>
            </div> -->
          </div>

          <div class="row">

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tỉnh/Thành phố</label>
                <input class="form-control" type="text" :value="data.province_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Quận/Huyện</label>
                <input class="form-control" type="text" :value="data.district_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Địa chỉ</label>
                <input class="form-control" type="text" :value="data.address" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Đối tượng khách hàng</label>
                <input class="form-control" type="text" :value="data.type == 1 ? 'VIP' : 'Bình thường'" readonly>
              </div>
            </div>
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Trường học</label>
                <input class="form-control" type="text" :value="data.school" readonly>
              </div>
            </div>
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">School grade</label>
                <input class="form-control" type="text" :value="data.school_grade_name" readonly>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Từ nguồn</label>
                <select class="form-control" v-model="data.source" name="source" v-validate readonly>
                  <option value="">Chọn nguồn từ</option>
                  <option :value="source.id" v-for="(source, i) in data.sources" :key="i">{{ source.name }}</option>
                </select>
              </div>
            </div>
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Trạng thái</label>
                <input class="form-control" type="text" :value="data.contract_type|contractType" readonly>
              </div>
            </div>
            <!-- <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Facebook</label>
                <input class="form-control" type="text" :value="data.facebook" readonly>
              </div>
            </div> -->
          </div>
          <div class="row">
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Giáo viên</label>
                <input class="form-control" type="text" :value="data.teacher_name" readonly>
              </div>
            </div>
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Ghi chú</label>
                <div class="readonly ada-text-frame" v-html="data.student_note"></div>
              </div>
            </div>
            <div class="col-sm-4" style="padding-right: 7.5px !important;">
              <div class="form-group">
                <label class="control-label">Mã cộng tác viên</label>
                <div class="readonly ada-text-frame" v-html="data.ref_code"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </b-card>
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-group"></i> <b class="uppercase">Thông tin phụ huynh học sinh: {{data.name}}</b>
      </div>
      <div class="panel">
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Họ tên phụ huynh 1 <span class="text-danger"> (*)</span></label>
                <input class="form-control" type="text" :value="data.gud_name1" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Số điện thoại  <span class="text-danger"> (*)</span></label>
                <input class="form-control" type="text" :value="data.gud_mobile1" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Email</label>
                <input class="form-control" type="text" :value="data.gud_email1" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Ngày sinh</label>
                <input class="form-control" type="text" :value="data.gud_birth_day1" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Nghề nghiệp</label>
                <input class="form-control" type="text" :value="data.gud_job1_name" readonly>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Họ tên phụ huynh 2</label>
                <input class="form-control" type="text" :value="data.gud_name2" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Số điện thoại</label>
                <input class="form-control" type="text" :value="data.gud_mobile2" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Email</label>
                <input class="form-control" type="text" :value="data.gud_email2" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Ngày sinh</label>
                <input class="form-control" type="text" :value="data.gud_birth_day2" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Nghề nghiệp</label>
                <input class="form-control" type="text" :value="data.gud_job2_name" readonly>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label">Mã anh/chị em học cùng</label>
                <input class="form-control" type="text" :value="data.sibling_id" readonly>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label">Tên anh/chị em</label>
                <input class="form-control" type="text" :value="data.sibling_name" readonly>
              </div>
            </div>
          </div>
        </div>
      </div>
    </b-card>
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-mortar-board"></i> <b class="uppercase">Thông tin trung tâm và chương trình học của học sinh: {{data.name}}</b>
      </div>
      <div class="panel">
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Trung tâm</label>
                <input class="form-control" type="text" :value="data.branch_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">EC</label>
                <input class="form-control" type="text" :value="data.ec_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">CS</label>
                <input class="form-control" type="text" :value="data.cm_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Sản phẩm đang học</label>
                <input class="form-control" type="text" v-model="data.product_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Chương trình đang học</label>
                <input class="form-control" type="text" :value="data.program_name" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Lớp đang học</label>
                <input class="form-control" type="text" :value="data.class_name" readonly>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label">Tổng số buổi học</label>
                <input class="form-control" type="text" :value="data.contract_real_sessions" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" v-if="data.teacher_id > 0">Ngày bắt đầu học</label>
                <label class="control-label" v-if="!data.teacher_id">Ngày dự kiến bắt đầu học</label>
                <input class="form-control" type="text" :value="data.class_start_date | formatDate" readonly>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" v-if="data.teacher_id > 0">Ngày kết thúc học</label>
                <label class="control-label" v-if="!data.teacher_id">Ngày dự kiến thúc học</label>
                <input class="form-control" type="text" :value="data.class_end_date | formatDate" readonly>
              </div>
            </div>
          </div>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
import u from "../../utilities/utility";

export default {
  props: {
    data: {
      type: Object,
      default: {
        crm_id: '',
        accounting_id: '',
        lms_stu_id: '',
        name: '',
        date_of_birth: '',
        gender: '',
        phone: '',
        email: '',
        province_name: '',
        district_name: '',
        address: '',
        type: 0,
        nick: '',
        school: '',
        school_grade_name: '',
        gud_name1: '',
        gud_email1: '',
        gud_birth_day1: '',
        gud_card1: '',
        gud_name2: '',
        gud_email2: '',
        gud_birth_day2: '',
        gud_card2: '',
        checked: 0,
        attached_file: '',
        note: ''
      }
    }
  },
  watch: {
    data(dat) {
      // u.log('PROPED', dat);
    }
  },
  data() {
    return {};
  },
  created() {},
  methods: {},
  components: {}
};
</script>

<style scoped>
.text-danger {
  color: #f86c6b !important;
}
</style>
