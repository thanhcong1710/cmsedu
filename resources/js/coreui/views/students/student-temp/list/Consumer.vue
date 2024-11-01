<template>
  <div
          class="animated fadeIn apax-form"
          id="students-management"
  >
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-filter" /> <b class="uppercase">Bộ lọc</b>
          </div>
          <div id="students-list">
            <div class="row">
              <div class="col-md-4">
                <branch-select-component
                        :local="local()"
                        v-model="filters.branch_id"
                        track-by="id"
                        label="Trung tâm"
                        :multiple="false"
                />
              </div>
              <div class="col-md-4">
                <ec-select-component
                        v-model="filters.ec_id"
                        label="EC"
                        :branch-ids="getBranchIds"
                        track-by="id"
                        :multiple="false"
                />
              </div>
              <div class="col-md-4" v-if="actions.showFilter()">
                <date-select-component
                        v-model="filters.date"
                        :range="true"
                        placeholder="Chọn ngày có dữ liệu"
                        label="Ngày có dữ liệu"
                        track-by="YYYY-MM-DD"
                />
              </div>
              <div class="col-md-4">
                <text-field-component
                        v-model="filters.name"
                        placeholder="Tìm theo Tên, Số điện thoại"
                        label="Từ khóa"
                />
              </div>
              <div class="col-md-4">
                <date-select-component
                        v-model="filters.care_date"
                        :range="false"
                        placeholder="Chọn ngày"
                        label=" Ngày chăm sóc"
                        track-by="YYYY-MM-DD"
                />
              </div>
              <div class="col-md-4" v-if="actions.showFilter()">
                <type-student-temp-select
                        v-model="filters.type"
                        label="Trạng thái"
                />
              </div>
              <div class="col-md-4" v-if="actions.showFilter()">
                <label class="control-label">Nguồn chi tiết</label>
                <select v-model="filters.note" class="filter-selection customer-type form-control" id="">
                  <option value="">Chọn ghi chú</option>
                  <option :value="s.id" v-for="(s, index) in options.notes" :key="index">{{ s.name }}</option>
                </select>
              </div>
            </div>
            <div class="row" v-if="actions.showFilter()">
              <div class="col-md-4">
                <date-select-component
                        v-model="filters.import_date"
                        :range="false"
                        placeholder="Chọn ngày"
                        label=" Ngày import (Data Hub)"
                        track-by="YYYY-MM-DD"
                />
              </div>
              <div class="col-md-4">
                <label class="control-label">Chọn nguồn (Data Hub)</label>
                <select v-model="filters.source" class="filter-selection customer-type form-control" id="">
                  <option value="">Chọn nguồn</option>
                  <option :value="s.id" v-for="(s, index) in options.sources" :key="index">{{ s.name }}</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="control-label">Trạng thái (Data Hub)</label>
                <select v-model="filters.import_cs" class="filter-selection customer-type form-control" id="">
                  <option value=""> Chọn</option>
                  <option value="1">Đã đẩy lên CareSoft</option>
                  <option value="2">Đang chờ đẩy</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button
                        type="button"
                        class="apax-btn full detail"
                        @click="actions.search($data.filters)"
                >
                  <i class="fa fa-search" /> Tìm kiếm
                </button>
                <button
                        type="button"
                        class="apax-btn full detail warning"
                        @click="reset()"
                >
                  <i class="fa fa fa-slack" /> Bỏ lọc
                </button>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book" /> <strong>{{state.title}}</strong>
          </div>
          <div class="controller-bar table-header">
            <router-link to="/student-temp/new">
              <button
                      type="button"
                      class="apax-btn full detail"
              >
                <i class="fa fa-plus" /> Thêm mới học sinh
              </button>
            </router-link>
            &nbsp;
            <router-link to="/student-temp/import">
              <button
                      type="button"
                      class="apax-btn full detail"
              >
                <i class="fa fa-file-excel-o" /> Nhập danh sách học sinh
              </button>
            </router-link>
          </div>
          <div class="table-responsive scrollable">
            <table
                    id="apax-printing-students-list"
                    class="table table-striped table-bordered apax-table"
            >
              <thead>
              <tr>
                <th rowspan="2">
                  STT
                </th>
                <th rowspan="2">
                  Trung tâm
                </th>
                <th rowspan="2">
                  EC
                </th>
                <th rowspan="2">
                  Ngày có dữ liệu
                </th>
                <th rowspan="2">
                  Học sinh
                </th>
                <th colspan="4">
                  Phụ huynh 1
                </th>
                <th rowspan="2">
                  Địa chỉ
                </th>
                <th rowspan="2">
                  Nguồn
                </th>
                <th rowspan="2">
                  Ghi chú
                </th>
                <th rowspan="2">
                  Trạng thái
                </th>
                <th
                        rowspan="2"
                >
                  Hành động
                </th>
              </tr>
              <tr>
                <th>Họ tên</th>
                <th>Nghề nghiệp</th>
                <th>Số điện thoại</th>
                <th>Email</th>
              </tr>
              </thead>
              <tbody v-if="state.students && state.students.length>0">
              <tr
                      v-for="(student, index) in state.students"
                      :key="index"
                      style="cursor: pointer;"
                      class="hover-row"
                      :class="actions.getClassNameForRow(student)"
              >
                <td>{{ actions.getIndexOfPage(index) }}</td>
                <td @click="goToPage(student)">{{ student.branch_name }}</td>
                <td @click="goToPage(student)">{{ student.ec_name }}</td>
                <td @click="goToPage(student)">{{ student.created_at }}</td>
                <td @click="goToPage(student)">{{ student.name }}<br>{{ student.date_of_birth }}<br>{{ student.gender === "M" ? 'nam' : 'nữ' }}</td>
                <td @click="goToPage(student)">{{ student.gud_name1 }}</td>
                <td @click="goToPage(student)">{{ student.gud_job1 }}</td>
                <td>{{ student.gud_mobile1 }}</td>
                <td>{{ student.gud_email1 }}</td>
                <td @click="goToPage(student)">{{ student.address }}</td>
                <td>{{ student.source }}</td>
                <td>{{ student.note }}</td>
                <td>{{getTempType(student.type, student.std_id)}}</td>
                <td>
                    <span v-if="!student.std_id"
                          class="apax-btn detail" title="Checkin học sinh"
                          @click.stop="handleAdd(student)"
                    >

                      <i class="fa fa-user-plus" />
                    </span>
                  <span v-if="student.std_id"
                        class="apax-btn print" title="Xem thông tin học sinh"
                        @click.stop="handleView(student)"
                  >

                      <i class="fa fa-eye" />
                    </span>
                  <span
                          class="apax-btn edit" title="Cập nhật học sinh"
                          @click.stop="handleEdit(student)"
                  >

                      <i class="fa fa-pencil" />
                    </span>
                  <span
                          class="apax-btn error"
                          v-if="student.type === 1"
                          @click.stop="actions.deleteStudent(student)"
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
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
  import Paging from '../../../reports/common/PagingReport'
  import BranchSelectComponent from '../../../reports/common/branch-select'
  import EcSelectComponent from '../../../reports/common/EcSelect'
  import DateSelectComponent from '../../../reports/common/DatePicker'
  import TextFieldComponent from '../common/TextField'
  import TypeStudentTempSelect from '../common/TypeStudentTempSelect'
  import u from '../../../../utilities/utility'
  export default {
    props     : ['state', 'actions'],
    components: {
      Paging, BranchSelectComponent, EcSelectComponent, DateSelectComponent, TextFieldComponent, TypeStudentTempSelect,
    },
    data () {
      return {
        filters:{
            branch_id: null, ec_id    : null, date     : null, name     : null, type     : -1,import_date:null, source:"", import_cs:"",note:"",care_date:null
        },
        options:{
          sources:[],notes:[]
        }
      }
    },
    methods: {
      local(){
        if(u.session().user.id == 415 || u.session().user.role_id == 999999999)
          return true
        else
          return false
      },
      getSource (s) {
        u.g(`/api/student-temp-ext/get-source?s=${s}&${new Date().getTime()}`).then((res) => {
          this.options.sources = res
        })
      },
      getSourceNote (s) {
        u.g(`/api/student-temp-ext/get-source-note?s=${s}&${new Date().getTime()}`).then((res) => {
          this.options.notes = res
        })
      },
      reset(){
        this.$router.go()
        /*this.filters.name = null
        this.filters.branch_id = null
        this.filters.ec_id = null
        this.filters.date = null
        this.filters.type = -1
        this.filters.import_date = null
        this.filters.care_date = null
        this.filters.import_cs = ""
        this.filters.source = ""
        this.filters.note = ""
        this.actions.search(null)*/
      },
      getTempType (type, std_id) {
        if (type == 1)
          return "Dữ liệu bị trùng"
        else if(std_id >0)
          return "Đã là học sinh"
      },
      goToPage (student) {
        this.$router.push(`/student-temp/${student && student.id}/info`)
      },
      handleEdit (student) {
        this.$router.push(`/student-temp/${student.id}/edit`)
      },
      handleAdd (student) {
        this.$router.push(`/student-checkin/add?temp_id=${student.id}`)
      },
      handleView (student) {
        this.$router.push(`/students/${student.std_id}`)
      },
    },
    computed: {
      getBranchIds () {
        if (this.filters.branch_id){
          this.getSourceNote(this.filters.branch_id)
          this.getSource(this.filters.branch_id)
        }
        //this.actions.filters  = this.filters
        return this.filters.branch_id ? [this.filters.branch_id] : null
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
