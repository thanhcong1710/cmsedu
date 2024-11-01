<template>
  <multiselect
    placeholder="Chọn mã chiết khấu giảm giá"
    select-label="Enter để chọn mã này"
    v-model="products"
    :options="options"
    label="code"
    :close-on-select="true"
    :hide-selected="true"
    :multiple="false"
    :searchable="true"
    track-by="code"
    :disabled="disabled"
  >
    <span slot="noResult">Không tìm thấy mã chiết khấu giảm giá phù hợp</span>
  </multiselect>
</template>

<script>
import u from '../../../utilities/utility'
import multiselect from 'vue-multiselect'
export default {
  name      : 'ProductSelect',
  components: { multiselect },
  props     : { value: {}, feeId : { type: Number, default: 0 }},
  data () {
    return {
      options : [], products: null, disabled: true,
    }
  },
  created () {
    this.getProducts()
  },
  computed: {
    localValue: {
      get () {},
      set () {}
    }
  },
  watch: {
    products (value) {
      this.$emit('input', value)
    },
    value (value) {
      this.products = value
    },
  },
  methods: {
    getProducts () {
      this.disabled = true
      u.a()
        .get(`/api/discount-codes/available/${this.feeId}`)
        .then((response) => {
          this.options  = [...response.data.data]
          this.disabled = false
        })
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
