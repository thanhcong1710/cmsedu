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
        :value="contact_method_id.value"
        v-for="(contact_method_id, index) in sources"
        :key="index"
      >
        {{ contact_method_id.label }}
      </option>
    </select>
  </div>
</template>

<script>
import { contactOptions } from '../../../../utilities/constants'

export default {
  name : 'ContactSelect',
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
    return { sources: contactOptions }
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
