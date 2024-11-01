<template>
  <div
    v-if="message"
    class="app-notification-container"
  >
    <div class="d-none d-lg-block app-notification">
      {{ message }}
    </div>
  </div>
</template>

<script>
import u from '../utilities/utility'

export default {
  name: 'Notification',
  data () {
    return { message: null }
  },
  created () {
    this.getNotification()
  },
  methods: {
    getNotification () {
      u.g('/api/notification/get-message').then((response) => {
        this.message = response
      })
    },
  },
}
</script>

<style scoped>
  .app-notification {
    position: absolute;
    animation-name: running;
    animation-duration: 30s;
    overflow: visible;
    height: auto;
    width: auto;
    animation-iteration-count: infinite;
    animation-direction: normal;
    animation-timing-function: linear;
    white-space: nowrap;
  }

  .app-notification-container {
    overflow: hidden;
    height: 45px;
    display: flex;
    align-items: center;
    position: relative;

  }

  @keyframes running {
    0% {
      transform:translateX(130%);
    }
    100% {
      transform:translateX(-130%);
    }
  }
</style>
