<template>
  <component
    :is="component"
    :label="data && data.label"
    :value="data && data.value"
    :clazz="data && data.clazz"
    :name="data && data.name"
    v-model="data.name"
    v-if="component"
  />
</template>
<script>
export default {
  name : 'Element',
  props: ['data', 'type'],
  data () {
    return { component: null, [this.data.name]: '' }
  },
  watch: {
    input (newValue, oldValue) {
      if (newValue !== oldValue)
        this.$emit('input', newValue)
    },
  },
  methods: {
    loader () {
      if (!this.type)
        return null

      const type = this.type.replace(/\b./g, function (m) {
        return m.toUpperCase()
      })
      return () => require(`./${type}`)
    },
  },
  mounted () {
    const type     = this.type.replace(/\b./g, function (m) {
      return m.toUpperCase()
    })
    this.component = require(`./${type}`)
  },
}
</script>
