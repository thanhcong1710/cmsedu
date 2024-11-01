<template>
  <div>
    <slot v-bind="{ state, actions }" />
  </div>
</template>

<script>
import u from '../../../utilities/utility'

export default {
  data () {
    return { students: [], allowImport: false }
  },
  computed: {
    state () {
      return { students: this.students, allowImport: this.allowImport }
    },
    actions () {
      return {
        importStudent     : this.importStudent,
        changeFile        : this.changeFile,
        deleteStudent     : this.deleteStudent,
        getTemplate       : this.getTemplate,
        getClassNameForRow: this.getClassNameForRow,
      }
    }
  },

  methods: {
    importStudent () {
      u.apax.$emit('apaxLoading', true)
      u.p(`/api/student-temp/import?${new Date().getTime()}`, { students: this.students }).then((response) => {
        this.students    = response.data
        this.allowImport = false
        u.apax.$emit('apaxLoading', false)
      })
    },
    validateFile () {
      const formData = new FormData()
      formData.append('file', this.file)
      u.apax.$emit('apaxLoading', true)
      u.p(`/api/student-temp/validate-import2?${new Date().getTime()}`, formData).then((response) => {
        //console.log("xxxxxxxxxxxxxxxxxxxxxxx",response)
        u.apax.$emit('apaxLoading', false)
      })
    },
    changeFile (event) {
      this.file = _.get(event, 'target.files[0]')
      this.validateFile()
    },
    getTemplate () {
      u.apax.$emit('apaxLoading', true)
      u.getFile(`/api/student-temp/template?${new Date().getTime()}`).then(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
    getClassNameForRow (student) {
      if (_.get(student, 'message.error.length'))
        return 'import-row-error'

      if (_.get(student, 'message.warning.length'))
        return 'import-row-warning'

      return null
    },
  },
}
</script>
