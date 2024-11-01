<template>
  <div>
    <slot v-bind="{ state, actions }" />
  </div>
</template>

<script>
import u from '../../../../utilities/utility'
import ToggleButton from 'vue-js-toggle-button'
import Vue from 'vue'
Vue.use(ToggleButton)

export default {
  data () {
    return {
      role     : null, modules  : null, roleNames: null, loading  : false,
    }
  },
  computed: {
    state () {
      return {
        role   : this.role, modules: this.modules, roles  : this.roles,
      }
    },
    actions () {
      return {
        getStatus       : this.getStatus, changeRole      : this.changeRole, changeRoleStatus: this.changeRoleStatus,
      }
    },
    roleEnable () {
      const items = JSON.parse(this.role.functions)
      const res   = {}
      items.forEach((item) => {
        const arr          = item.split(':')
        const module       = arr[0]
        const actionString = arr[1]
        res[module]        = actionString.split('.')
      })
      return res
    },
  },

  mounted () {
    this.getRole()
  },
  methods: {
    getStatus (roleKey, moduleKey) {
      const module = moduleKey.substring(3, moduleKey.length)
      const role   = roleKey.substring(4, roleKey.length)
      const m      = this.roleEnable[module]
      if (!m) return false

      return m.includes(role)
    },
    getRole () {
      u.g(`/api/role/${this.$route.params.id}`).then((res) => {
        this.role    = res.role
        this.modules = res.modules
        this.roles   = res.roles
      })
    },
    changeRole (roleKey, moduleKey, status) {
      const module = moduleKey.substring(3, moduleKey.length)
      const role   = roleKey.substring(4, roleKey.length)
      this.update({
        module, role, status,
      })
    },
    changeRoleStatus (target) {
      u.apax.$emit('apaxLoading', true)
      u.put(`/api/role/${this.$route.params.id}/status?${new Date().getTime()}`, { status: target.value }).then((res) => {

      }).finally(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
    update (params) {
      u.apax.$emit('apaxLoading', true)
      u.put(`/api/role/${this.$route.params.id}?${new Date().getTime()}`, params).then((res) => {

      }).finally(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
  },
}
</script>
