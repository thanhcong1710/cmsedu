<template>
  <div>
    <slot v-bind="{ state, actions }" />
  </div>
</template>

<script>
  import u from '../../../../utilities/utility'
  import MixinList from '../mixin/list'
  export default {
    mixins: [MixinList],
    data () {
      return { students: [], pagination: { total: 0 }, filter:{}, title:'Danh sách học viên (dữ liệu thô)'}
    },
    computed: {
      state () {
        return { students: this.students, pagination: this.pagination, title:this.title }
      },
      actions () {
        return {
          getTemplate   : this.getTemplate,
          pagingChange  : this.pagingChange,
          search        : this.search,
          getIndexOfPage: this.getIndexOfPage,
          deleteStudent : this.deleteStudent,
          getClassNameForRow: this.getClassNameForRow,
          showFilter: this.showFilter,
          source: this.getSource,
        }
      },
    },
    mounted () {
      this.search()
    },
    methods: {
      pagingChange (value) {
        this.pagination = value
      },
      getIndexOfPage (index) {
        const { cpage, limit } = this.pagination
        return (parseInt(cpage) - 1) * parseInt(limit) + index + 1
      },
      search (data) {
        if (data){
          this.pagination.cpage = 1
          this.filter = data
        }
        if (!data && u.session().user.id >0){
          data = this.filter
        }
        let list ='';
        $.each(u.session().user.branches, function(key, value) {
           list += value.id +","
        });

        const params = {
          ...data,
          limit: this.pagination.limit,
          page : this.pagination.cpage,
          u_id : u.session().user.id,
          title : u.session().user.title,
          brd_id : list.slice(0, -1),
        }
        u.apax.$emit('apaxLoading', true)
        u.g(`/api/student-temp/list?${new Date().getTime()}`, params).then((res) => {
          this.students         = res.data
          this.pagination.total = res.total
          this.title         = res.title
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
      showFilter () {
        if (u.session().user.id == 415 || u.session().user.id == 1)
          return true
        else
          return false
      },
    },
  }
</script>