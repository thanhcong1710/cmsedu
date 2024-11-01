<template>
  <div class="form-group">
    <label class="control-label">Từ nguồn</label>
    <select
      class="form-control"
      v-model="localValue"
      :disabled="disabled"
    >
      <option
        :value="0"
        disabled
      >
        Chọn nguồn
      </option>
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
import { sourceForStudentTempOptions } from '../../../utilities/constants'

export default {
  name : 'ProvinceSelect',
  props: {
    value: {
      type   : String,
      default: null,
    },
    readOnly: {
      type   : Boolean,
      default: false,
    },
  },
  data () {
    return { sources: sourceForStudentTempOptions }
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
}
</script>

<style scoped>

</style>
