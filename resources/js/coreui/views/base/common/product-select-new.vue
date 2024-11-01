<template>
  <multiselect
    :placeholder="placeholder || 'Chọn gói sản phẩm (Tất cả)'"
    select-label="Enter để chọn sản phẩm này"
    v-model="products"
    :options="options"
    label="name"
    :close-on-select="true"
    :hide-selected="true"
    :multiple="multiple"
    :searchable="true"
    :track-by="trackBy"
    @select="selectItem">
  >
    <span slot="noResult">Không tìm thấy sản phẩm phù hợp</span>
  </multiselect>
</template>

<script>
import u from '../../../utilities/utility'
import multiselect from 'vue-multiselect'
export default {
  name      : 'ProductSelect',
  components: { multiselect },
  props     : {
    value      : {},
    placeholder: {},
    onSelectProduct: Function,
    multiple   : { type: Boolean, default: true },
    trackBy    : { type: String, default: 'id' },
  },
  data () {
    return {
      options : [], products: null,
    }
  },
  created () {
    this.getProducts()
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
        .get(`/api/all/products`)
        .then((response) => {
          this.options  = response.data
          this.disabled = false
          if (this.value && typeof this.value === 'string' && this.options)
            this.products = this.options.find((option) => option[this.trackBy] === this.value)
        })
    },
    selectItem(product) {
      this.isLoading = false
      this.$emit('doSelectProduct', product)
      this.onSelectProduct ? this.onSelectProduct(product) : null
    }
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
