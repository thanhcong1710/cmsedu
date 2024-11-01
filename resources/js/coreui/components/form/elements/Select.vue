<template>
  <div
    class="form-group"
  >
    <label class="control-label">{{ label }}<span
      v-if="required"
      class="text-danger"
    > (*)</span></label>
    <div class="form-group">
      <select
        name=""
        id=""
        class="form-control"
        v-model="computedVal"
      >
        <option
          v-for="(option, index) in options"
          :key="index"
          :value="option.value"
          :disabled="option.disabled"
        >
          {{ option.label }}
        </option>
      </select>
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
    options: {
      type   : Array,
      default: () => [],
    },
    value: {
      type   : String,
      default: null,
    },
    defaultValue: {
      type   : String,
      default: null,
    },
  },
  data () {
    return { val: this.value || this.defaultValue || '', message: '' }
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
