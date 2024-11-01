<template>
  <div>
    <button
      class="btn btn-info"
      @click="showModal"
      title="Điểm danh"
    >
      <i
        class="fa fa-list"
        aria-hidden="true"
      />
    </button>
    <b-modal
      :title="title"
      class="modal-primary"
      size="lg"
      v-model="modal.show"
      @ok="closeModal"
      ok-variant="primary"
      ok-only
    >
      <h4 v-if="className">
        Lớp: {{ className }}
      </h4>
      <table
        class="table detail-attendance-table"
      >
        <tr>
          <th>Ngày</th>
          <th>Lớp học</th>
          <th>Điểm danh</th>
<!--          <th>Bài tập về nhà</th>-->
          <th>Ghi chú</th>
        </tr>
        <tbody v-if="attendances">
          <tr
            v-for="(attendance, index) in attendances"
            :key="index"
            class="table-tr"
          >
            <td>{{ attendance.attendance_date }}</td>
            <td>{{ attendance.class_name }}</td>
            <td>{{ getLabelAttendanceStatusByValue(attendance.status) }}</td>
<!--            <td>-->
<!--              <div :class="attendances.homework? 'success-label apax-label' : 'alert-label apax-label'">-->
<!--                {{ attendance.homework ? "Đã làm bài tập" : "Chưa làm bài tập" }}-->
<!--              </div>-->
<!--            </td>-->
            <td>{{ attendance.note }}</td>
          </tr>
        </tbody>
        <tr v-else>
          <td
            colspan="6"
            class="text-center alert-warning"
          >
            <div>Data not found!</div>
          </td>
        </tr>
      </table>
    </b-modal>
  </div>
</template>

<script>
import { getLabelAttendanceStatusByValue } from '../../utilities/constants'
export default {
  components: {},
  props     : {
    title: {
      type   : String,
      default: 'Attendance detail',
    },
    attendances: {
      type   : Array,
      default: () => [],
    },
    className: {
      type   : String,
      default: null,
    },
  },
  watch: {
    student (data) {

    },
  },
  data () {
    return { modal: { show: false } }
  },
  created () {
  },
  methods: {
    showModal () {
      this.modal.show = true
    },
    getLabelAttendanceStatusByValue,
    closeModal () {
      this.modal.show = false
    },
  },
}
</script>
<style scoped>
  .detail-attendance-table{
    margin-top: 20px;
  }
  .table-tr{
    margin-top: 10px;
  }
</style>
