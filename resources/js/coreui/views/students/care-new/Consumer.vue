<template>
  <div
          class="animated fadeIn apax-form"
          id="students-management"
  >
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book" /> <strong>Lịch sử chăm sóc</strong>
          </div>
          <div class="controller-bar table-header">
            <b-button type="button" variant="secondary" class="btn btn-success" @click="modalCreate()"><i class="fa fa-plus" /> Thêm mới</b-button>
          </div>
          <div class="table-responsive scrollable">
            <table
                    id="apax-printing-students-list"
                    class="table table-striped table-bordered apax-table"
            >
              <thead>
              <tr>
                <th>
                  STT
                </th>
                <th>
                    Mã CMS
                </th>
                <th>
                    Mã Cyber
                </th>
                <th>
                  Tên Học Sinh
                </th>
                <th>
                  Phương Thức
                </th>
                <th>
                  Nội Dung Chăm Sóc
                </th>
                <th>
                  Trạng Thái Khách Hàng
                </th>
                <th>
                  Điểm
                </th>
                <th>
                  Người Thực Hiện
                </th>
                <th>
                  Ngày chăm sóc
                </th>
                <th>
                  Ngày tạo
                </th>
                <th>
                  Thao Tác
                </th>
              </tr>
              </thead>
              <tbody v-if="state.students && state.students.length>0">
              <tr
                      v-for="(student, index) in state.students"
                      :key="index"
                      :class="actions.getClassNameForRow(student)"
              >
                <td>{{ actions.getIndexOfPage(index) }}</td>
                <td>{{ state.crmId.crm_id}}</td>
                <td>{{ state.crmId.accounting_id}}</td>
                <td>{{ state.crmId.name}}</td>
                <td>{{ student.contact_method_id == 1 ?"Gọi điện thoại":"Gặp trực tiếp" }}</td>
                <td>{{ student.note}}</td>
                <td>{{ student.title }}</td>
                <td>{{ student.score }}</td>
                <td>{{ student.full_name }}</td>
                <td>{{ student.created_at.substr(0,10) }}</td>
                <td>{{ student.stored_date }}</td>
                <td>
                  <span
                        class="apax-btn print" title="Xem thông tin học sinh"
                        @click="handleEdit(student, 1)"
                  >

                      <i class="fa fa-eye" />
                    </span>
                  <span
                          class="apax-btn edit" title="Cập nhật học sinh"
                          @click="handleEdit(student, 2)"
                  >

                      <i class="fa fa-pencil" />
                    </span>
                  <span
                          class="apax-btn error"
                          @click="remove(student.id)"
                  >
                    <i class="fa fa-trash" />
                  </span>
                </td>
              </tr>
              </tbody>
            </table>
            <paging
                    :on-change="actions.search"
                    :value="state.pagination"
                    @input="actions.pagingChange"
                    :total="state.pagination.total"
            />
          </div>
          <div class="row">
            <div class="col-sm-12">
                <button
                        type="button"
                        class="apax-btn full detail"
                        @click="back()"
                >
                  <i class="fa fa-reply" /> Quay lại
                </button>
            </div>
          </div>
        </b-card>
      </div>
    </div>
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
    <b-modal ref="my-modal-1" title="Thông tin chăm sóc" size="lg" v-model="myModal"  @ok="myModal = false" hide-footer>
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
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Nội dung</label>
              <textarea v-model="care_note" class="form-control" rows="4" :read-only="readOnly"></textarea>
            </div>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
  import Paging from '../../base/common/PagingReport'
  import BranchSelectComponent from '../../base/common/branch-select'
  import EcSelectComponent from '../../base/common/EcSelect'
  import DateSelectComponent from '../../base/common/DatePicker'
  import TextFieldComponent from '../../base/common/TextField'
  import TypeStudentTempSelect from '../../base/common/TypeStudentTempSelect'
  import Datepicker from '../../base/common/DatePicker'
  import ContactSelectComponent from '../../base/common/ContactSelect'
  import QualitySelect from '../../base/common/QualitySelect'
  import { scoreContractArray } from '../../../utilities/constants'
  export default {
    props     : ['state', 'actions'],
    components: {
      Paging, BranchSelectComponent, EcSelectComponent, DateSelectComponent, TextFieldComponent, TypeStudentTempSelect, Datepicker,ContactSelectComponent,QualitySelect
    },
    data () {
      return {
        branch_id: null,
        ec_id    : null,
        date     : null,
        name     : null,
        type     : -1,
        care_id:0,
        care_date:null,
        care_note:null,
        care_note: null,
        contact_method_id: 1,
        contact_quality_id: 0,
        score: 0,
        care_id: 0,
        form_error: false,
        care_date: null,
        myModal: false,
        largeModal: false,
      }
    },
    watch: {
      'contact_quality_id': function (newValue, oldValue) {
        if (newValue && newValue !== oldValue)
          this.score = scoreContractArray[newValue]
      },
    },
    methods: {
      back () {
        this.$router.go(-1)
      },
      yourOkFn(bvModalEvt){
        bvModalEvt.preventDefault()
        this.handleSubmit()
      },
      modalCreate(){
        this.care_id = 0
        this.care_date = null
        this.contact_method_id = 1
        this.score = 0
        this.contact_quality_id = 0
        this.care_note = null
        this.$refs['my-modal'].show()
      },
      remove (id) {
        this.actions.deleteStudentCare(id)
      },
      handleEdit (student, m) {
        this.care_id = student.id
        this.care_date = new Date(student.created_at)
        this.contact_method_id = student.contact_method_id
        this.contact_quality_id = student.contact_quality_id
        this.care_note = student.note
        if (m == 1)
          this.$refs['my-modal-1'].show()
        else
          this.$refs['my-modal'].show()
      },
      handleAdd (student) {
        this.$router.push(`/students/add-student?temp_id=${student.id}`)
      },
      handleView (student) {
        this.$router.push(`/students/${student.std_id}`)
      },
      handleSubmit() {
        if (!this.checkFormValidity()) {
          this.form_error = true
          return false
        }
        this.form_error = false
        this.actions.care_save(this.$data)
      },
      checkFormValidity() {
        return (!this.care_note || !this.care_date|| !this.contact_quality_id) ? false : true
      },
    },
    computed: {
      readOnly () {
        return null
      },
      getBranchIds () {
        console.log("bbbbbbbbbbbbbbbb",this.branch_id)
        this.state.branch_id = this.branch_id
        return [this.branch_id]
      },
    },
  }
</script>

<style scoped lang="scss">
  .hover-row:hover{
    background-color: #c2c2c2ad !important;
  }
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

  .import-row-error {
    background-color: rgba(255, 0, 0, 0.37) !important;
  }

  .import-row-warning {
    background-color: rgba(255, 165, 0, 0.22) !important;
  }

</style>
