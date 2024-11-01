<template>
    <div class="wrapper">
      <div class="animated fadeIn">
          <b-row>
            <b-col cols="12">
                <b-card header-tag="header" footer-tag="footer">
                  <div slot="header">
                      <i class="fa fa-align-justify"></i> <strong>Danh sách báo cáo</strong>
                  </div>

                </b-card>
            </b-col>
        </b-row>
      </div>
    </div>
</template>

<script>
import axios from 'axios'
import select from 'vue-select'
import u from '../../../utilities/utility'
// import Vue from 'vue'


// Vue.component('vue-select', require('vue-select2'))
export default {
  name: 'Form-Report',
  components: {
    "vue-select": select
  },
  data () {
    return {
      show: false
    }

  },
  created(){

  },
  methods: {
    print(){
      this.show = false;

    },
    addBranchs(){
      this.show = true
    }
  }
}
</script>

<style scoped>

</style>
