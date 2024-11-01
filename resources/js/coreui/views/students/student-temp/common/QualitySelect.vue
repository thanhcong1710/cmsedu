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
        :value="contact_quality_id.value"
        v-for="(contact_quality_id, index) in sources"
        :key="index"
      >
        {{ contact_quality_id.label }}
      </option>
    </select>
  </div>
</template>

<script>
import { qualityOptions } from '../../../../utilities/constants'

export default {
  name : 'QualitySelect',
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
    return { sources: qualityOptions }
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
