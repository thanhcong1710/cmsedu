<template>
  <div
    class="animated fadeIn apax-form"
    id="add-new-student"
  >
    <div class="row">
      <div class="col-lg-12">
        <b-card header-tag="header">
          <header-component
            title="Chi tiết học sinh"
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
                    :read-only="readOnly"
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
        <div id="exampleAccordion" data-children=".item">
          <div class="item">
            <div class="card">
              <header class="card-header"><div><strong data-toggle="collapse" data-parent="#exampleAccordion" href="#exampleAccordion1" aria-expanded="true" aria-controls="exampleAccordion1"><i class="fa fa-list"></i> Thông tin khác <span class="text-danger"> [ - ]</span></strong></div></header>
              <div class="card-body">
              <div class="panel main-content collapse" id="exampleAccordion1" role="tabpanel">
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
                </div>
              </div>
              </div>
            </div>
          </div>
        </div>
        <b-card header>
          <div slot="header">
            <i class="fa fa-list" /> <b class="uppercase">Chăm sóc</b>
          </div>
          <div class="controller-bar table-header">
            <b-button type="button" v-if="showEdit()" variant="secondary" class="btn btn-success" @click="modalCreate()"><i class="fa fa-plus" /> Thêm mới</b-button>
          </div>
          <div
                  id="list_content"
                  class="panel-heading"
          >
            <div class="panel-body">
              <div class="table-responsive scrollable">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                  <tr class="text-sm">
                    <th>STT</th>
                    <th>Phương thức</th>
                    <th>Nội dung chăm sóc</th>
                    <th>Trạng thái khách hàng</th>
                    <th>Điểm</th>
                    <th>Người thực hiện</th>
                    <th>Ngày chăm sóc</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr
                          v-for="(care, index) in this.state.cares"
                          :key="index"
                  >
                    <td>{{ index+1 }}</td>
                    <td>{{contactMethod(care.contact_method_id)}}</td>
                    <td>{{ care.note }}</td>
                    <td>{{ care.quality_name }}</td>
                    <td>{{ care.score }}</td>
                    <td>{{ care.full_name }}</td>
                    <td>{{ care.created_at.substr(0,10) }}</td>
                    <td>{{ care.stored_date}}</td>
                    <td>
                      <button
                              class="apax-btn detail"
                              @click="detailCare(care,'view')"
                      >
                        <span class="fa fa-eye" />
                      </button>
                      <button
                              class="apax-btn edit"
                              @click="detailCare(care,'edit')"
                              v-if="showViewCare(care.creator_id)"
                      >
                        <i class="fa fa-edit" />
                      </button>
                      <button
                              @click="deleteTempCare(care.id)"
                              class="apax-btn remove"
                              v-if="showViewCare(care.creator_id)"
                      >
                        <i class="fa fa-times" />
                      </button>
                    </td>
                  </tr>
                  </tbody>
                </table>
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
                          class="apax-btn full btn-success"
                          @click.stop="handleAddStudent()"
                          v-if="showEdit()"
                  >
                    <i class="fa fa-user-plus" /> Checkin học sinh
                  </button>
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
    <b-modal ref="my-modal-view" title="Thông tin chăm sóc" size="lg" v-model="myModal" @ok="myModal = false" hide-footer>
      <div class="animated fadeIn apax-form">
        <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Ngày </label>
                <datepicker
                        style="width:100%;"
                        v-model="care_date"
                        placeholder="Chọn thời gian chăm sóc"
                        format="YYYY-MM-DD"
                        :read-only="readOnly"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <contact-select-component
                        label="Phương thức"
                        v-model="contact_method_id"
                        :read-only="readOnly"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6 pad-no">
              <div class="form-group">
                <quality-select
                        label="Trạng thái khách hàng"
                        v-model="contact_quality_id"
                        :read-only="readOnly"
                />
              </div>
            </div>
            <div class="col-md-6 pad-no">
              <div class="form-group">
                <label class="control-label">Điểm</label>
                <input type="text" :value="score"  class="form-control"  readonly/>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label class="control-label">Nội dung</label>
            <textarea v-model="care_note" class="form-control" rows="4" readonly></textarea>
          </div>
        </div>
      </div>
      </div>
    </b-modal>
    <b-modal ref="my-modal" title="Thông tin chăm sóc" size="lg" v-model="largeModal" @ok="yourOkFn"  ok-title="Lưu" @close="largeModal = false">
      <div class="animated fadeIn apax-form">
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Ngày</label>
                  <datepicker
                          style="width:100%;"
                          v-model="care_date"
                          placeholder="Chọn thời gian chăm sóc"
                          format="YYYY-MM-DD"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <contact-select-component
                          label="Phương thức"
                          v-model="contact_method_id"
                          :read-only="readOnly"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6 pad-no">
                <div class="form-group">
                  <quality-select
                          label="Trạng thái khách hàng"
                          v-model="contact_quality_id"
                          :read-only="readOnly"
                  />
                </div>
              </div>
              <div class="col-md-6 pad-no">
                <div class="form-group">
                  <label class="control-label">Điểm</label>
                  <input type="text" :value="score"  class="form-control"  readonly/>
                  <input type="hidden" :value="care_id"  class="form-control"/>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Nội dung</label>
              <textarea v-model="care_note" class="form-control" rows="4"></textarea>
            </div>
          </div>
          <div class="col-md-12"><span class="text-danger" v-if="form_error"> (*) Bạn phải chọn Ngày, Trạng thái khách hàng, Nội dung</span></div>
        </div>
      </div>
    </b-modal>
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
import Datepicker from '../../../reports/common/DatePicker'
import ContactSelectComponent from '../common/ContactSelect'
import QualitySelect from '../common/QualitySelect'
import { scoreContractArray } from '../../../../utilities/constants'

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
    Datepicker,
    ContactSelectComponent,
    QualitySelect,
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
      care_note              : null,
      contact_method_id              : 1,
      contact_quality_id              : 0,
      score              : 0,
      care_id              : 0,
      form_error              : false,
      care_date              : null,
      myModal: false,
      largeModal: false,
      student_temp_id: this.$route.params.id,
    }
  },
  computed: {
    getBranchIds () {
      return [this.branch_id]
    },
    readOnly () {
      return null //_.get(this.$route, 'params.type') === 'info'
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
    'contact_quality_id': function (newValue, oldValue) {
      if (newValue && newValue !== oldValue)
        this.score = scoreContractArray[newValue]
    },
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
    handleAddStudent () {
      this.$router.push(`/student-checkin/add?temp_id=${this.$route.params.id}`)
    },
    showEdit(){
      return this.state.readOnly ? 0: 1
    },
    showViewCare(careId){
      let stdId = this.state.readOnly
      if (stdId)
        return 0
      else if(!stdId && this.state.uid != careId && this.state.ecTitle === "EC")
        return 0
      else
        return 1
    },
    deleteTempCare(care_id){
      this.actions.delStudentTempCare(care_id)
    },
    modalCreate(){
      this.care_id = 0
      this.care_date = null
      this.contact_method_id = 1
      this.contact_quality_id = 0
      this.care_note = null
      this.$refs['my-modal'].show()
    },
    detailCare(cares, view){
      this.care_id = cares.id
      this.care_date = new Date(cares.created_at)
      this.contact_method_id = cares.contact_method_id
      this.contact_quality_id = cares.contact_quality_id
      this.care_note = cares.note
      if (view === 'view')
        this.$refs['my-modal-view'].show()
      else
        this.$refs['my-modal'].show()
    },
    yourOkFn(bvModalEvt){
      bvModalEvt.preventDefault()
      this.handleSubmit()

    },
    checkFormValidity() {
      return (!this.care_note || !this.care_date|| !this.contact_quality_id) ? false : true
    },
    handleSubmit() {
      if (!this.checkFormValidity()) {
        this.form_error = true
        return false
      }
      this.form_error = false
      this.actions.care_save(this.$data)
    },
    contactMethod(method){
      return method != 1 ?"Gặp trực tiếp":"Điện thoại"
    },
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
