<template>
  <div
    class="animated fadeIn apax-form"
    id="add-new-student"
  >
    <div class="row">
      <div class="col-lg-12">
        <b-card header-tag="header">
          <header-component
            title="Thêm mới học sinh"
            :hidden-skype="true"
          />
          <div class="panel main-content">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4">
                  <text-field-component
                    label="Họ tên phụ huynh 1"
                    :required="true"
                    v-model="gud_name1"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Số điện thoại"
                    :required="true"
                    v-model="gud_mobile1"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <date-picker-component
                    v-model="gud_date_of_birth1"
                    label="Ngày sinh"
                    placeholder="Chọn ngày sinh"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Nghề nhiệp"
                    v-model="gud_job1"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Email"
                    v-model="gud_email1"
                    :read-only="readOnly"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <text-field-component
                    label="Họ tên học sinh"
                    v-model="name"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <date-picker-component
                    v-model="birthday"
                    label="Ngày sinh"
                    placeholder="Chọn ngày sinh"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <gender-select-component
                    label="Giới tính"
                    v-model="gender"
                    :read-only="readOnly"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <branch-select-component
                    label="Trung tâm"
                    v-model="branch_id"
                    track-by="id"
                    :required="getEcReq"
                    :multiple="false"
                  />
                </div>
                <div class="col-sm-4">
                  <ec-select-component
                    label="EC"
                    v-model="ec_id"
                    :multiple="false"
                    :branch-ids="getBranchIds"
                    :auto-set-branch-default="false"
                    track-by="id"
                    :required="getEcReq"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <date-picker-component
                    label="Ngày có dữ liệu"
                    placeholder="Chọn ngày có dữ liệu"
                    v-model="date"
                    :read-only="readOnly"
                  />
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <b-card header-tag="header">
          <header-component
            title="Thông tin khác"
            :hidden-skype="true"
          />
          <div class="panel main-content">
            <div class="panel-body">
              <!-- Phụ huynh 2 -->
              <div class="row">
                <div class="col-sm-4">
                  <text-field-component
                    label="Họ tên phụ huynh 2"
                    v-model="gud_name2"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Số điện thoại"
                    v-model="gud_mobile2"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <date-picker-component
                    v-model="gud_date_of_birth2"
                    label="Ngày sinh"
                    placeholder="Chọn ngày sinh"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Nghề nhiệp"
                    v-model="gud_job2"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Email"
                    v-model="gud_email2"
                    :read-only="readOnly"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <province-select-component
                    v-model="province_id"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <district-select-component
                    v-model="district_id"
                    :province-id="province_id"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Địa chỉ"
                    v-model="address"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <source-select-component
                    name="Từ nguồn"
                    v-model="source"
                    :read-only="readOnly"
                  />
                </div>
                <div class="col-sm-4">
                  <text-field-component
                    label="Ghi chú"
                    v-model="note"
                    :read-only="readOnly"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <button
                    type="button"
                    class="apax-btn full detail"
                    @click="save"
                  >
                    <i class="fa fa-save" /> Lưu
                  </button>
                  <router-link to="/student-temp">
                    <button
                      type="button"
                      class="apax-btn full detail"
                    >
                      <i class="fa fa-reply" /> Hủy bỏ
                    </button>
                  </router-link>
                  <button
                          type="button"
                          class="apax-btn error full"
                          @click="remove"
                          v-if="showDelete"
                  >
                    <i class="fa fa-trash" /> Xóa
                  </button>
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
import HeaderComponent from '../../../reports/common/header-report'
import TextFieldComponent from '../common/TextField'
import DatePickerComponent from '../../../reports/common/DatePicker'
import ProvinceSelectComponent from '../common/ProvinceSelect'
import DistrictSelectComponent from '../common/DictrictSelect'
import BranchSelectComponent from '../../../reports/common/branch-select'
import EcSelectComponent from '../../../reports/common/EcSelect'
import SourceSelectComponent from '../common/SourceSelect'
import GenderSelectComponent from '../common/GenderSelect'

export default {
  components: {
    HeaderComponent,
    TextFieldComponent,
    DatePickerComponent,
    ProvinceSelectComponent,
    DistrictSelectComponent,
    BranchSelectComponent,
    EcSelectComponent,
    SourceSelectComponent,
    GenderSelectComponent,
  },
  props: ['state', 'actions'],
  data () {
    return {
      name              : null,
      birthday          : null,
      gender            : 1,
      gud_name1         : null,
      gud_date_of_birth1: null,
      gud_mobile1       : null,
      gud_job1          : null,
      gud_email1        : null,
      gud_name2         : null,
      gud_date_of_birth2: null,
      gud_mobile2       : null,
      gud_job2          : null,
      gud_email2        : null,
      branch_id         : null,
      ec_id             : null,
      date              : new Date(),
      province_id       : null,
      district_id       : null,
      address           : null,
      source            : null,
      note              : null,
    }
  },
  computed: {
    getBranchIds () {
      return [this.branch_id]
    },
    readOnly () {
      return false//_.get(this.$route, 'params.type') === 'info'
    },
    getEcReq(){
      return this.state.ecId ? false : true
    },
    showDelete () {
      let path = this.$route.path
      if (path.includes("info") || path.includes("edit"))
        return 1
      else
        return 0
    },
  },
  watch: {
    'state.student': function (newValue, oldValue) {
      if (newValue && newValue !== oldValue) {
        this.name               = newValue.name
        this.birthday           =  newValue.date_of_birth && new Date(newValue.date_of_birth)
        this.gender             = newValue.gender === 'M' ? 1 : 0
        this.gud_name1          = newValue.gud_name1
        this.gud_date_of_birth1 = newValue.gud_date_of_birth1 && new Date(newValue.gud_date_of_birth1)
        this.gud_mobile1        = newValue.gud_mobile1
        this.gud_job1           = newValue.gud_job1
        this.gud_email1         = newValue.gud_email1
        this.gud_name2          = newValue.gud_name2
        this.gud_date_of_birth2 = newValue.gud_date_of_birth2 && new Date(newValue.gud_date_of_birth2)
        this.gud_mobile2        = newValue.gud_mobile2
        this.gud_job2           = newValue.gud_job2
        this.gud_email2         = newValue.gud_email2
        this.branch_id          = newValue.branch_id
        this.ec_id              = newValue.ec_id
        this.date               = newValue.date && new Date(newValue.date)
        this.province_id        = newValue.province_id
        this.district_id        = newValue.district_id
        this.address            = newValue.address
        this.source             = newValue.source
        this.note               = newValue.note
      }
    },
  },
  methods: {
    save () {
      if (this.state.ecId)
          this.ec_id = this.state.uid
      this.actions.save(this.$data)
    },
    remove () {
      this.actions.deleteStudent(this.state.student, true)
    },
  },
}
</script>

<style scoped lang="scss">
  a {
    color: blue;
  }

  .avatar-frame {
    padding: 3px 0 0 0;
    line-height: 40px;
  }

  .avatar {
    height: 29px;
    width: 29px;
    margin: 0 auto;
    overflow: hidden;
  }

  p.filter-lbl {
    width: 40px;
    height: 35px;
    float: left;
  }

  .filter-selection {
    width: calc(100% - 40px);
    float: left;
    padding: 3px 5px;
    height: 35px !important;
  }

  .drag-me-up {
    margin: -25px -15px -15px;
  }

</style>
