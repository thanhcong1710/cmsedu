<template>
  <div>
    <slot v-bind="{ state, actions }" />
  </div>
</template>

<script>
import u from '../../../../utilities/utility'

export default {
  data () {
    return { roles: [] }
  },
  computed: {
    state () {
      return { roles: this.roles }
    },
    actions () {
      return { }
    },
  },

  mounted () {
    this.getRoles()
  },
  methods: {
    getRoles () {
      u.g('/api/role/list').then((res) => {
        this.roles = res
      })
    },
  },
}
</script>
