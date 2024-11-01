<template>
  <multiselect
    placeholder="Chọn loại khách hàng (Tất cả)"
    select-label="Enter để chọn loại khách hàng"
    v-model="studentTypes"
    :options="options"
    label="name"
    :close-on-select="true"
    :hide-selected="true"
    :multiple="true"
    :searchable="true"
    track-by="id"
  />
</template>

<script>
import multiselect from 'vue-multiselect'
export default {
  name      : 'StudentTypeSelect',
  components: { multiselect },
  props     : { value: {} },
  data () {
    return {
      options: [
        { id: 1, name: 'Chính Thức' },
        { id: 2, name: 'Học trải nghiệm' },
        { id: 3, name: 'Withraw' },
        { id: 4, name: 'Pending' },
        { id: 5, name: 'Đặt cọc' },
        { id: 6, name: 'Tiềm năng' },
      ],
      studentTypes: null,
    }
  },
  watch: {
    studentTypes (value) {
      this.$emit('input', value)
    },
    value (value) {
      this.studentTypes = value
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
