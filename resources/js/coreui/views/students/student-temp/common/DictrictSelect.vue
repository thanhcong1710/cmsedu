<template>
  <div class="form-group">
    <label class="control-label">Quận/Huyện</label>
    <select
      class="form-control"
      v-model="localValue"
      :disabled="disabled"
    >
      <option
        :value="0"
        disabled
      >
        Chọn Quận/Huyện
      </option>
      <option
        :value="district.id"
        v-for="(district, index) in districts"
        :key="index"
      >
        {{ district.name }}
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
    provinceId: {
      type   : Number,
      default: 0,
    },
    readOnly: {
      type   : Boolean,
      default: false,
    },
  },
  data () {
    return { districts: [], loading: false }
  },
  mounted () {
    if (this.provinceId > 0)
      this.search(this.provinceId)
  },
  computed: {
    disabled () {
      return this.readOnly || !this.provinceId || parseInt(this.provinceId) <= 0 || this.loading
    },
    localValue: {
      get () {
        return this.$props.value
      },
      set (value) {
        this.$emit('input', value)
      },
    },
  },
  watch: {
    provinceId (newValue, oldValue) {
      if (newValue !== oldValue)
        this.search(newValue)
    },
  },
  methods: {
    search (provinceId) {
      this.loading = true
      const params = { province_id: provinceId }
      u.g('/api/district/list', params).then((res) => {
        this.districts = res.data
        this.loading   = false
      })
    },
  },
}
</script>

<style scoped>

</style>
