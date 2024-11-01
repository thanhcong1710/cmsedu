<template>
  <div class="form-group">
    <label
      class="control-label"
      v-if="label"
    >{{ label }}<span
      class="text-danger"
      v-if="required"
    > (*)</span></label>
    <input
      class="form-control"
      type="text"
      v-model="localValue"
      :placeholder="placeholder"
      :readonly="readOnly"
    >
  </div>
</template>
<script>
export default {
  props: [
    'label',
    'value',
    'required',
    'placeholder',
    'readOnly',
  ],
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
