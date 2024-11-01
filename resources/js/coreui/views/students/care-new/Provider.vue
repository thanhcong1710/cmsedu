<template>
  <div>
    <slot v-bind="{ state, actions }" />
  </div>
</template>

<script>
  import u from '../../../utilities/utility'
  import MixinList from '../../base/common/mixin/list'
  import {getDate} from "../../base/common/utils";
  export default {
    mixins: [MixinList],
    data () {
      return { students: [], pagination: { total: 0 }, crmId: {} }
    },
    computed: {
      state () {
        return { students: this.students, pagination: this.pagination, crmId:this.crmId }
      },
      actions () {
        return {
          getTemplate   : this.getTemplate,
          pagingChange  : this.pagingChange,
          search        : this.search,
          getIndexOfPage: this.getIndexOfPage,
          deleteStudentCare : this.deleteStudentCare,
          getClassNameForRow: this.getClassNameForRow,
          care_save: this.care_save
        }
      },
    },
    mounted () {
      this.search()
    },
    methods: {
      deleteStudentCare(id = 0){
        if(confirm("Bạn có chắc chắn muốn xóa ?")){
          u.apax.$emit('apaxLoading', true)
          u.put(`/api/student-temp/care-delete`, {'temp_care_id':id}).then((response) => {
            u.apax.$emit('apaxLoading', false)
            this.$router.go()
          })
        }
      },
      pagingChange (value) {
        this.pagination = value
      },
      getIndexOfPage (index) {
        const { cpage, limit } = this.pagination
        return (parseInt(cpage) - 1) * parseInt(limit) + index + 1
      },
      search (data) {
        if (!data && u.session().user.id >2)
          data = {branch_id:u.session().user.branch_id}
        else if (!data && u.session().user.id <=2)
          data = {branch_id:u.session().user. branches[0].id}
        const params = {
          ...data,
          limit: this.pagination.limit,
          page : this.pagination.cpage,
          u_id : u.session().user.id,
          s_id : this.$route.params.id,
          title : u.session().user.title,
        }
        u.apax.$emit('apaxLoading', true)
        u.g(`/api/student-care/list?${new Date().getTime()}`, params).then((res) => {
          this.students         = res.data.cares
          this.crmId         = res.data.info
          this.pagination.total = res.total
          u.apax.$emit('apaxLoading', false)
        })
      },
      getTemplate () {
        u.getFile('/api/student-temp/template')
      },
      getClassNameForRow (student) {
        if (_.get(student, 'type') === 1)
          return 'import-row-warning'
        return null
      },
      care_save(data) {
        let std_temp_id = 0
          data.std_temp_id = std_temp_id
          data.crm_id = this.crmId.crm_id
          data.ec_id = u.session().user.id
          data.care_date = getDate(data.care_date)
        const params = {
          ...data,
        }
        u.apax.$emit('apaxLoading', true)
        const method = u.p;
        method(`/api/student-temp/care-add`, params).then(() => {

          u.apax.$emit('apaxLoading', false)
          this.$router.go()
        })
      },
    },
  }
</script>