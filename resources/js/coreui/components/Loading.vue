<template>
  <div v-show="active" class="ajax-load content-loading">
    <div class="load-wrapper">
      <div :class="spin" class="loader"></div>
      <div v-show="show" :class="spin"  class="loading-text cssload-loader">
        {{ text }}
      </div>
    </div><input type="hidden" :val="duration">
    <div v-if="spin!=''" :class="spin" class="progress-group">
      <div class="progress-group-bars">
        <div class="progress progress-xs">
          <div class="progress-bar bg-danger" role="progressbar" :style="`width: ${percentage}%`" :aria-valuenow="percentage" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
      <div class="progress-group-header">
        <i class="fa fa-flash progress-group-icon"></i>
        <div>{{ text }}</div>
        <div class="ml-auto font-weight-bold">{{percentage}}%</div>
      </div>
    </div>
    <div v-else class="progress pct-bar">
      <div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" role="progressbar" :style="`width: ${percentage}%`" :aria-valuenow="percentage" aria-valuemin="0" aria-valuemax="100">
        {{percentage}}%
      </div>
    </div>
  </div>
</template>

<script>
import { clearInterval } from 'timers';

export default {
  props:{
    active: {
      type: Boolean,
      default: false
    },
    show: {
      type: Boolean,
      default: true
    },
    spin: {
      type: String,
      default: ''
    },
    text: {
      type: String,
      default: 'Đang xử lý...' 
    },
    duration: {
      type: Number,
      default: 100
    }
  },
  data() {
    return {
      percentage: 0,
      timeout: null
    }
  },
  watch: {
    active() {
      if (this.active) {
        this.percentage = 0
        this.start()
      } else {
        this.percentage = 100
        this.reset()
        this.timeout = setInterval(function(){ this.completed() }.bind(this), 500)
      }
    },
    percentage() {
      if (this.percentage === 0) {
        this.reset()
        this.percentage = 0
        this.progress() 
      }
    }
  },
  created() {
    this.start()
  },
  methods: {
    start() {
      this.timeout = setInterval(function(){ this.progress() }.bind(this), this.duration)
    },
    reset() {
      if (this.timeout && this.timeout.hasOwnProperty('close')) {
        clearInterval(this.timeout)
      }
    },
    progress() {
      if (this.percentage < 96) {
        this.percentage+=1
        this.timeout = setInterval(function () { this.progress() }.bind(this), this.duration)
      }
    },
    completed() {
      if (this.timeout && this.timeout.hasOwnProperty('close')) {
        clearInterval(this.timeout)
      }
    }
  }
}
</script>

<style scoped language="scss">


</style>