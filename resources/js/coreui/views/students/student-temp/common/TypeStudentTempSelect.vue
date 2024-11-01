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
        :value="province.value"
        v-for="(province, index) in sources"
        :key="index"
      >
        {{ province.label }}
      </option>
    </select>
  </div>
</template>

<script>
import { typeStudentTempOptions } from '../../../../utilities/constants'

export default {
  name : 'TypeStudentTempSelect',
  props: {
    value: {
      type   : Number,
      default: 0,
    },
    label: {
      type   : String,
      default: null,
    },
      placeholder: {
          type   : String,
          default: null,
      },
      disabled: {
          type   : Boolean,
          default: false,
      },
  },
  data () {
    return { sources: typeStudentTempOptions }
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
  },
}
</script>

<style scoped>

</style>
