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
      type="number"
      v-model="computedVal"
      :name="name"
      :max="max"
      :min="min"
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
    disabled: {
      type   : Boolean,
      default: false,
    },
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
    max: {
      type   : Number,
      default: null,
    },
    min: {
      type   : Number,
      default: null,
    },
    defaultValue: {
      type   : Number,
      default: null,
    },
    value: {
      type   : Number,
      default: null,
    },
  },
  data () {
    return { val: this.value || null, message: null }
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
        this.computedVal = newValue
    },
    computedVal (newValue, oldValue) {
      if (newValue === oldValue) return

      const v = parseFloat(newValue)
      if ((this.min && parseFloat(this.min) > v) || (this.max && parseFloat(this.max) < v))
        this.computedVal = oldValue
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
