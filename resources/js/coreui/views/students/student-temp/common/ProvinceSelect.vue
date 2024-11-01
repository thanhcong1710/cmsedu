<template>
  <div class="form-group">
    <label class="control-label">Tỉnh/Thành phố</label>
    <select
      class="form-control"
      v-model="localValue"
      :disabled="disabled"
    >
      <option
        :value="0"
        disabled
      >
        Chọn Tỉnh/Thành phố
      </option>
      <option
        :value="province.id"
        v-for="(province, index) in provinces"
        :key="index"
      >
        {{ province.name }}
      </option>
    </select>
  </div>
</template>

<script>
import u from '../../../../utilities/utility'

export default {
  name : 'ProvinceSelect',
  props: {
    value: {
      type   : Number,
      default: 0,
    },
    readOnly: {
      type   : Boolean,
      default: false,
    },
  },
  data () {
    return { provinces: [] }
  },
  mounted () {
    this.search()
  },
  computed: {
    localValue: {
      get () {
        return this.value
      },
      set (value) {
        this.$emit('input', value)
      },
    },
    disabled () {
      return this.readOnly || this.loading
    },
  },
  methods: {
    search () {
      this.loading = true
      u.g('/api/provinces/list').then((res) => {
        this.provinces = res.data
        this.loading   = false
      })
    },
  },
}
</script>

<style scoped>

</style>
