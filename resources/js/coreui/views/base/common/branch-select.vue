<template>
  <div
    class="form-group"
  >
    <label
      class="control-label"
      v-if="label"
    >{{ label }}<span
      class="text-danger"
      v-if="required"
    > (*)</span></label>
    <multiselect
      placeholder="Chọn trung tâm"
      select-label="Enter để chọn trung tâm này"
      v-model="localValue"
      :options="options"
      label="name"
      :close-on-select="true"
      :hide-selected="true"
      :multiple="multiple"
      :searchable="true"
      track-by="id"
      :disabled="readOnly"
    >
      <span slot="noResult">Không tìm thấy trung tâm phù hợp</span>
    </multiselect>
  </div>
</template>

<script>
import u from '../../../utilities/utility'
import multiselect from 'vue-multiselect'
export default {
  name      : 'BranchSelect',
  components: { multiselect },
  props     : {
    value   : {},
    label   : {},
    multiple: { type: Boolean, default: true },
    trackBy : { type: String, default: null },
    required: { type: Boolean, default: false },
    readOnly: { type: Boolean, default: false },
  },
  data () {
    return { options: [] }
  },
  computed: {
    localValue: {
      get () {
        if (this.trackBy){
          return this.options.filter((item) => (Array.isArray(this.value) ? this.value.includes(item[this.trackBy]) : item[this.trackBy] === this.value))
        }
        return this.value
      },
      set (value) {
        let data = Array.isArray(value) ? value : [value]
        if (this.trackBy)
          data = data.map((item) => item[this.trackBy])

        if (!this.multiple && data.length > 0)
          data = data[0]

        this.$emit('input', data)
      },
    },
  },
  created () {
    this.options    = u.session().user.branches
    this.localValue = this.options.length === 1 ? this.options : [this.options[0]]
  },
}
</script>

<style>
  .multiselect__placeholder {
    color: #3e515b;
  }

  .multiselect__tags {
    border: none !important;
  }

  .multiselect {
    border: 1px solid #c2cfd6 !important;
  }
</style>
