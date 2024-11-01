<template>
  <div
    class="form-group"
  >
    <label class="control-label">{{ label }}<span
      v-if="required"
      class="text-danger"
    > (*)</span></label>
    <datepicker
      style="width:100%;"
      v-model="computedVal"
      @change="change"
      :clearable="true"
      :lang="lang"
      format="YYYY-MM-DD"
      :placeholder="placeholder"
      :bootstrap-styling="true"
      class="time-picker"
      input-class="form-control bg-white"
      :disabled="disabled"
    />
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
import datepicker from 'vue2-datepicker'
import { getDate } from '../../../views/reports/common/utils'

export default {
  name      : 'Text',
  components: { datepicker },
  props     : {
    required: {
      type   : Boolean,
      default: false,
    },
    change: {
      type   : Function,
      default: () => {
      },
    },
    placeholder: {
      type   : String,
      default: 'Chọn ngày',
    },
    lang: {
      type   : String,
      default: 'en',
    },
    label: {
      type   : String,
      default: null,
    },
    name: {
      type   : String,
      default: null,
    },
    defaultValue: {
      type   : String,
      default: null,
    },
    value: {
      type   : String,
      default: null,
    },
    clazz: {
      type   : String,
      default: null,
    },
    disabled: {
      type   : Boolean,
      default: false,
    },
    onChange: {
      type   : Function,
      default: () => {},
    },
  },
  data () {
    return { val: new Date(`${this.defaultValue}`) || '', message: null }
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
      if (newValue !== oldValue) {
        this.val = new Date(`${newValue}`)
        if (this.onChange)
          this.onChange(newValue)
      }
    },
  },
  computed: {
    computedVal: {
      get () {
        this.$emit('input', getDate(this.val))
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
