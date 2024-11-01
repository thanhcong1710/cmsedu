<template>
  <multiselect
    track-by="cjrn_id"
    label="cjrn_classdate"
    v-model="localValue"
    :placeholder="placeholder"
    :options="options"
    :searchable="true"
    :open-direction="'below'"
    select-label
    :allow-empty="false"
    :disabled="disabled"
  />
</template>

<script>
import Multiselect from 'vue-multiselect'
import u from '../../utilities/utility'
export default {
  name      : 'ClassSelectDate',
  components: { Multiselect },
  props     : {
    classId    : {},
    placeholder: { type: String, default: 'Chọn ngày cần điểm danh' },
    value      : {},
  },
  data () {
    return { options: [], localValue: null }
  },
  created () {
    this.getSchedule()
  },
  watch: {
    localValue (newValue, oldValue) {
      if (newValue !== oldValue)
        this.$emit('input', newValue)
    },
    value (newValue, oldValue) {
      if (newValue !== oldValue)
        this.localValue = newValue
    },
    classId (newValue, oldValue) {
      if (newValue !== oldValue) {
        this.options = []
        this.getSchedule()
      }
    },
  },
  methods: {
    getSchedule () {
      this.disabled = true
      if (!this.classId || typeof this.classId !== 'number') return
      u.a()
        .get(`/api/schedules/class-id/${this.classId}`)
        .then((response) => {
          this.options  = response.data.data
          this.disabled = false
          if (this.value)
            this.localValue = this.value
        })
    },
  },
}
</script>

<style lang="scss">
  .multiselect__placeholder {
    color: #3e515b;
  }

  .multiselect__tags {
    border: none !important;
  }

  .multiselect {
    border: 1px solid #d6caca !important;
  }
  .multiselect__select{
    height: 100%;
    right: 0;
    top: 0;
  }
  .multiselect--disabled{
    background: none !important;
    .multiselect__select{
      top: 0;
      height: 100% !important;
    }
  }

</style>
