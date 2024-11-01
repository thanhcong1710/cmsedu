<template>
  <div class="tab-student-tuition-transfer apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử điểm danh</b>
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-bordered table-striped apax-table">
            <thead>
              <tr class="text-sm">
                <th>STT</th>
                <th>Ngày điểm danh</th>
                <th>Lớp học</th>
                <th>Trạng thái điểm danh</th>
                <th>Ghi chú</th>
                <th>Người điểm danh</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list" :key="index">
                <td>{{index+1}}</td>
                <td>{{ item.attendance_date }} </td>
                <td>{{ item.cls_name }}</td>
                <td>{{ item.status | genStatusAttendance}}</td>
                <td>{{ item.note }}</td>
                <td>{{ item.creator_name }}</td>
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

  export default {
    props: {
      list: {
        type: Array,
        default: () => []
      },
      tuition: {
        type: Object,
        default: null
      }
    },
    watch: {
      student(data) {

      }
    },
    data() {
      return {}
    },
    created() {},
    methods: {},
    components: {},
    filters:{
      genStatusAttendance(status){
        if(status==1){
          return "Có đi học";
        }else if(status==0){
          return "Không điểm danh";
        }else if(status==2){
          return "Vắng mặt";
        }else if(status==3){
          return "Đi muộn";
        }else if(status==4){
          return "Về sớm";
        }else if(status==5){
          return "Vắng mặt có lý do";
        }else if(status==6){
          return "Nghỉ lễ";
        }else if(status==7){
          return "Nghỉ có phép";
        }
      }
    }
  }
</script>

<style scoped>

</style>
