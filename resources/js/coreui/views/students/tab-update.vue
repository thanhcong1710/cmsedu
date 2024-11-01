<template>
  <div class="tab-student-update apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử cập nhật thông tin học sinh</b>        
      </div>
      <div class="panel">
        <div class="table-responsive scrollable">
          <table class="table table-striped table-bordered apax-table">
            <thead>
              <tr class="text-sm">
                <th class="text-center width-50">STT</th>
                <th class="width-150">Thời gian thực hiện</th>
                <th class="width-150">Người thực hiện</th>
                <th class="width-150">Chức danh</th>
                <th class="">Nội dung thay đổi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in list" :key="index">
                <td>{{(list.length - index) + 1}}</td>
                <td>{{item.updated_at | formatDate}}</td>
                <td>{{item.updator_name}}</td>
                <td>{{item.updator_title}}</td>
                <td><div class="align-left" v-html="item.content"></div></td>
              </tr>
              <tr>
                <td>1</td>
                <td>{{data.created_at | formatDate}}</td>
                <td>{{data.creator_name}}</td>
                <td>{{data.creator_title}}</td>
                <td><div class="align-left">Khởi tạo hồ sơ học sinh {{data.name}}</div></td>
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
      data: {
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
    components: {}
  }
</script>

<style scoped>
  
</style>
