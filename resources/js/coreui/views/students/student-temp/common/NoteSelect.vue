<template>
  <div class="form-group">
    <label
      class="control-label"
      v-if="label"
    >{{ label }}</label>
    <select
      class="form-control"
      v-model="localValue"
      :disabled="disabled"
    >
      <option
        :value="contact_method_id.note"
        v-for="(contact_method_id, index) in sources"
        :key="index"
      >
        {{ contact_method_id.note }}
      </option>
    </select>
  </div>
</template>

<script>
//import { contactOptions } from '../../../../utilities/constants'
import u from '../../../../utilities/utility'
export default {
  name : 'NoteSelect',
  props: {
    value: {
      type   : String,
      default: "",
    },
    label: {
      type   : String,
      default: null,
    },
    readOnly: {
      type   : Boolean,
      default: false,
    },
  },
  data () {
    return {sources: [], loading: false}
  },
  mounted () {
    this.search()
  },
  computed: {
    localValue: {
      get () {
        return this.value
      },
      set (value) {
        this.$emit('input', value)
      },
    },
    disabled () {
      return this.readOnly
    },
  },
  methods: {
    search () {
      const params = { branch_id:u.session().user.branch_id }
      u.g('/api/student-temp-ext/get-source-note', params).then((res) => {
        this.sources = res
      })
    },
  },
}
</script>

<style scoped>

</style>
