<template functional>
  <div
    class="animated fadeIn apax-form"
    id="students-management"
  >
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book" /> <strong>Nhập danh sách học viên</strong>
          </div>
          <div class="controller-bar table-header">
            <router-link to="/student-temp">
              <button
                type="button"
                class="apax-btn full detail"
              >
                <i class="fa fa-reply" /> Trở về
              </button>
            </router-link>
            &nbsp;
            <button
              type="button"
              class="apax-btn full detail"
              @click="props.actions.getTemplate"
            >
              <i class="fa fa-arrow-circle-o-down" /> Tải tệp mẫu nhập dữ liệu
            </button>
            <label>Chọn tệp dữ liệu:
              <input
                type="file"
                ref="file"
                @change="props.actions.changeFile"
              >
            </label>
            <button
              v-if="props.state.allowImport"
              type="button"
              class="apax-btn full detail"
              @click="props.actions.importStudent"
            >
              <i class="fa fa-save" /> Lưu
            </button>
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
                  <th colspan="5">
                    Phụ huynh 1
                  </th>
                  <th colspan="5">
                    Phụ huynh 2
                  </th>
                  <th rowspan="2">
                    Tỉnh/TP
                  </th>
                  <th rowspan="2">
                    Quận/Huyện
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
                    Trang thái
                  </th>
                  <th
                    rowspan="2"
                    v-if="props.state.students && props.state.students[0] && props.state.students[0].id"
                  >
                    Hành động
                  </th>
                </tr>
                <tr>
                  <th>Họ tên</th>
                  <th>Ngày sinh</th>
                  <th>Nghề nghiệp</th>
                  <th>Số điện thoại</th>
                  <th>Email</th>
                  <th>Họ tên</th>
                  <th>Ngày sinh</th>
                  <th>Nghề nghiệp</th>
                  <th>Số điện thoại</th>
                  <th>Email</th>
                </tr>
              </thead>
              <tbody v-if="props.state.students && props.state.students.length>0">
                <tr
                  v-for="(student, index) in props.state.students"
                  :key="index"
                  :class="props.actions.getClassNameForRow(student)"
                >
                  <td>{{ student.stt }}</td>
                  <td>{{ student.branch_accounting_id }}</td>
                  <td>{{ student.ec_accounting_id }}</td>
                  <td>{{ student.date }}</td>
                  <td>{{ student.name }}<br>{{ student.birthday }}<br>{{ student.gender }}</td>
                  <td>{{ student.gud_name1 }}</td>
                  <td>{{ student.gud_birth_day1 }}</td>
                  <td>{{ student.gud_job1 }}</td>
                  <td>{{ student.gud_mobile1 }}</td>
                  <td>{{ student.gud_email1 }}</td>
                  <td>{{ student.gud_name2 }}</td>
                  <td>{{ student.gud_birth_day2 }}</td>
                  <td>{{ student.gud_job2 }}</td>
                  <td>{{ student.gud_mobile2 }}</td>
                  <td>{{ student.gud_email2 }}</td>
                  <td>{{ student.province }}</td>
                  <td>{{ student.district }}</td>
                  <td>{{ student.address }}</td>
                  <td>{{ student.source }}</td>
                  <td>{{ student.note }}</td>
                  <td>
                    <div style="width: 150px; text-align: left;">
                      <div
                        v-for="(message, i) in student.message && student.message.error"
                        :key="i"
                      >
                        <li style="color: #ff0017;">
                          {{ message }}
                        </li>
                      </div>
                      <div
                        v-for="(message, i) in student.message && student.message.warning"
                        :key="i"
                      >
                        <li style="color: #1596ff;">
                          {{ message }}
                        </li>
                      </div>
                      <div v-if="student.message && student.message.success">
                        <li style="color: #44d63a;">
                          {{ student.message.success }}
                        </li>
                      </div>
                    </div>
                  </td>
                  <td v-if="student && student.id">
                    <button
                      type="button"
                      class="apax-btn full detail"
                      @click="props.actions.deleteStudent(student)"
                    >
                      Xóa
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

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

  .import-row-error {
    background-color: rgba(255, 0, 0, 0.37) !important;
  }

  .import-row-warning {
    background-color: rgba(255, 165, 0, 0.22) !important;
  }
</style>
