<template>
  <div
    class="form-group"
  >
    <label
      class="control-label"
      v-if="hasLabel"
    >{{ label }}</label>
    <date-picker-vue
      style="width:100%;"
      @change="change"
      :clearable="true"
      :range="range"
      :lang="lang"
      :format="format"
      :placeholder="placeholder"
      :bootstrap-styling="true"
      input-class="form-control bg-white"
      class="time-picker"
      :type="type"
      v-model="localValue"
      :disabled="readOnly"
      :not-before="notbefore"
      :not-after="notafter"
    />
  </div>
</template>

<script>
import DatePickerVue from 'vue2-datepicker'
import moment from 'moment'
export default {
  name      : 'DatePicker',
  components: { DatePickerVue },
  props     : {
    change: { type: Function, default: () => {} },
    range : {
      type   : Boolean,
      default: false,
    },
    placeholder: { type: String },
    format     : { type: String, default: 'YYYY-MM-DD' },
    notbefore  : { type: String, default: null },
    notafter  : { type: String, default: null },
    type       : { type: String, default: 'date' },
    value      : {},
    label      : { type: String, default: '' },
    trackBy    : { type: String, default: null },
    readOnly   : { type: Boolean, default: false },
  },
  computed: {
    hasLabel () {
      return this.label && this.label !== ''
    },
    localValue: {
      get () {
        if (this.trackBy) {
          if (Array.isArray(this.value))
            return this.value.map((item) => (moment(item, this.trackBy).toDate()))

          return moment(this.value, this.trackBy).toDate()
        }

        return this.value
      },
      set (value) {
        const finalValue = this.trackBy
          ? (Array.isArray(value) ? value.map((item) => (moment(item).format(this.trackBy)))
            : moment(value).format(this.trackBy)) : value
        this.$emit('input', finalValue)
      },
    },
  },
  data () {
    return {
      lang: {
        days: [
          'CN',
          'T2',
          'T3',
          'T4',
          'T5',
          'T6',
          'T7',
        ],
        months: [
          'Tháng 1',
          'Tháng 2',
          'Tháng 3',
          'Tháng 4',
          'Tháng 5',
          'Tháng 6',
          'Tháng 7',
          'Tháng 8',
          'Tháng 9',
          'Tháng 10',
          'Tháng 11',
          'Tháng 12',
        ],
        pickers: [
          '',
          '',
          '7 ngày trước',
          '30 ngày trước',
        ],
      },
    }
  },
}
</script>
