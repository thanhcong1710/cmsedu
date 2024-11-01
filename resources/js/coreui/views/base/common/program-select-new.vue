<template>
  <multiselect
    placeholder="Chọn chương trình (Tất cả)"
    select-label="Enter để chọn chương trình này"
    v-model="programs"
    :options="options"
    label="name"
    :close-on-select="true"
    :hide-selected="true"
    :multiple="true"
    :searchable="true"
    track-by="id"
  >
    <span slot="noResult">Không tìm thấy trương trình phù hợp</span>
  </multiselect>
</template>

<script>
import u from '../../../utilities/utility'
import multiselect from 'vue-multiselect'
export default {
  name : 'ProgramSelect',
  props: {
    branchId: 0,
    productId: 0,
    value: {},
  },
  components: { multiselect },
  data () {
    return {
      options : [], programs: null, disabled: true,
    }
  },
  created () {
    this.getProgram()
  },
  watch: {
    programs (value) {
      this.$emit('input', value)
    },
    value (value) {
      this.programs  = value
    },
    branchIds () {
      this.getProgram()
    },
    productIds () {
      this.getProgram()
    },
  },
  methods: {
    getProgram () {
      if (this.branchId && this.productId){
        u.a().get(`/api/all/programs/${this.branchId}/${this.productId}`).then((response) => {
          this.disabledProgram = false
          this.options         = response.data
        })
      }
      // u.a().get(`/api/all/programs/${this.branchIds.toString()}/${this.productIds.toString()}`).then((response) => {

    },
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
