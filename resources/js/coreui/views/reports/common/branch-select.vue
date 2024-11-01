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
    allBranch: { type: Boolean, default: false },
    myData: [],
    local: { type: Boolean, default: false },
  },
  data () {
    return { options: [] }
  },
  computed: {
    localValue: {
      get () {
        if (this.trackBy &&  this.options.length > 0){
          return this.options.filter((item) => (Array.isArray(this.value) ? this.value.includes(item[this.trackBy]) : item[this.trackBy] === this.value))
        }
        return this.value
      },
      set (value) {
        let data = Array.isArray(value) ? value : [value]
        if (this.trackBy  &&  this.options.length > 0)
          data = data.map((item) => item[this.trackBy])

        if (!this.multiple && data.length > 0)
          data = data[0]

        this.$emit('input', data)
      },
    },
  },
  created () {
    if (this.myData){
      this.options = this.myData
    }
    this.options = []
    var total = u.session().user.branches.length
    if (total >=2){
      let all = {id: 0, name: "Tất cả các trung tâm", role: "999999999", title: "Quản Trị Hệ Thống"}
      this.options    = u.session().user.branches
      //this.options[total] = all
      if (this.allBranch){
        this.options.push(all)
      }
      //this.options.unshift(all)
    }

    if (total >= 1){
      this.options    = u.session().user.branches
      this.readOnly = false
    }
    
    if (total == 1 &&  [55,56,68,69].includes(u.session().user.role_id)){
      this.options    = u.session().user.branches
      this.readOnly = true
    }
    
    if (!this.local)
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
