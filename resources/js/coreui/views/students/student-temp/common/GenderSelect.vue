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
        :value="gender.value"
        v-for="(gender, index) in sources"
        :key="index"
      >
        {{ gender.label }}
      </option>
    </select>
  </div>
</template>

<script>
import { genderOptions } from '../../../../utilities/constants'

export default {
  name : 'ProvinceSelect',
  props: {
    value: {
      type   : Number,
      default: 0,
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
    return { sources: genderOptions }
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
