<template>
  <div
    class="form-group"
  >
    <label class="control-label">{{ label }}<span
      v-if="required"
      class="text-danger"
    > (*)</span></label>
    <input
      class="form-control"
      :type="inputType"
      v-model="computedVal"
      :name="name"
      @blur="handleBlur"
      @focus="handleFocus"
      :disabled="disabled"
    >
    <span
      v-show="message"
      class="error-inform line"
    >
      <i class="fa fa-warning" />
      <span
        class="error help is-danger"
      >{{ message }}</span>
    </span>
  </div>
</template>
<script>
export default {
  name : 'Text',
  props: {
    required: {
      type   : Boolean,
      default: false,
    },
    label: {
      type   : String,
      default: null,
    },
    name: {
      type   : String,
      default: null,
    },
    value: {
      type   : String,
      default: null,
    },
    disabled: {
      type   : Boolean,
      default: false,
    },
    inputType: {
      type   : String,
      default: 'text',
    },
  },
  data () {
    return { val: this.value || '', message: '' }
  },
  methods: {
    handleBlur () {
      if (!this.val)
        this.message = `${this.label} là bắt buộc!`
    },
    handleFocus () {
      this.message = null
    },
  },
  watch: {
    value (newValue, oldValue) {
      if (newValue !== oldValue)
        this.val = newValue
    },
  },
  computed: {
    computedVal: {
      get () {
        this.$emit('input', this.val)
        return this.val
      },
      set (value) {
        this.val = value
      },
    },
  },
}
</script>

<style scoped>

</style>
