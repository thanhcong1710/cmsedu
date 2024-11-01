<template>
  <div
    v-if="disabled"
    class="app-notification-system-container"
  >
    Hệ thống đang bảo trì, xin vui lòng quay lại sau!
  </div>
</template>

<script>
import u from '../utilities/utility'

export default {
  name: 'NotificationSystem',
  data () {
    return { message: null, disabled: false }
  },
  created () {
    this.getEnableSystem()
    this.interval = setInterval(() => {
      this.getEnableSystem()
    }, 5 * 60 * 1000)
  },
  beforeDestroy () {
    clearInterval(this.interval)
  },
  methods: {
    checkRoot () {
      return _.get(u.session(), 'user.role_id') == '999999999'
    },
    getEnableSystem () {
      u.g('/api/stats').then((response) => {
        this.disabled = parseInt(response && response.system_enabled) === 1
        this.$emit('enableChange', !this.disabled)
        const isRoot  = this.checkRoot()
        if (isRoot && this.disabled === true)
          this.$emit('enableChange', true)
      })
    },
  },
}
</script>

<style scoped>
  .app-notification-system-container {
    height: calc(100vh - 40px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;

  }

</style>
