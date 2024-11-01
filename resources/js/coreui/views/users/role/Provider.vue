<template>
  <div>
    <slot v-bind="{ state, actions }" />
    <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" @ok="callback" v-model="modal"  ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
      <div v-html="message">
      </div>
    </b-modal>
  </div>
</template>

<script>
import u from '../../../utilities/utility'
import ToggleButton from 'vue-js-toggle-button'
import Vue from 'vue'

Vue.use(ToggleButton)

export default {
  data () {
    return { roles: null, user: null , modal: false, message:''}
  },
  computed: {
    state () {
      return { roles: this.roles, user: this.user }
    },
    actions () {
      return { checkRoleEnable: this.checkRoleEnable, changeRole: this.changeRole }
    },
  },

  mounted () {
    this.getRole()
  },
  methods: {
    callback(){
      this.$router.go(0)
    },
    checkRoleEnable (userRoles, roleChecking) {
      return userRoles && userRoles.includes(parseInt(roleChecking))
    },
    getRole () {
      u.apax.$emit('apaxLoading', true)
      u.g(`/api/role/get-role-for-user/${this.$route.params.id}`).then((res) => {
        this.roles = res.roles
        this.user  = res.user
      }).finally(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
    changeRole (role, status) {
      const params = { role_id: role.id, status }
      u.apax.$emit('apaxLoading', true)
      u.p(`/api/term-user-branch/${this.user.id}/${this.user.branch_id}?${new Date().getTime()}`, params).then((res) => {
        if (res.error_code == 1){
          this.message = res.msg
          this.modal = true
        }

      }).finally(() => {
        u.apax.$emit('apaxLoading', false)
      })
    },
  },
}
</script>
