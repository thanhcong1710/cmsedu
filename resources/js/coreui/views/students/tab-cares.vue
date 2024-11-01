<template>
    <div class="tab-student-class-transfer apax-content">
        <b-card header-tag="header">
            <div slot="header">
                <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử chăm sóc</b>
            </div>
            <div class="panel">
                <div class="table-responsive scrollable">
                    <table class="table table-bordered table-striped apax-table">
                        <thead>
                        <tr class="text-sm">
                            <th class="width-50">STT</th>
                            <th class="width-150">Thời gian</th>
                            <th class="width-150">Người thực hiện</th>
                            <th class="width-150">Phương thức</th>
                            <th class="width-250">Nội dung chăm sóc</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, ind) in history" :key="ind">
                            <td>{{ ind + 1 }}</td>
                            <td>{{ item.created_at }}</td>
                            <td>{{ item.full_name }}</td>
                            <td>{{ item.method == 1 ? 'Nhắn tin': (item.method == 2 ? 'Gọi điện thoại' :'Trao đổi trực tiếp') }}</td>
                            <td>{{ item.note }}</td>
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
      }
    },
    watch: {
      student(data) {

      }
    },
    data() {
      return {
        history: [],
      }
    },
    created() {
      let id = this.$route.params.id
      //u.apax.$emit('apaxLoading', true)
      u.g(`/api/cares/by-crm-id/${id}`).then((res) => {
        if (res)
            this.history = res
      })

    },
    methods: {},
    components: {}
  }
</script>

<style scoped>

</style>
