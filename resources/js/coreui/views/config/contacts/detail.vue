<template>
    <div class="wrapper">
      <div class="animated fadeIn">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <strong>Thông Tin Loại Hình Liên Lạc</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                  </div>
                  <div class="content-detail">
                  </div>  
                </b-card>
            </b-col>
        </b-row>
      </div> 
    </div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'

export default {
  name: 'Contact-Detail',
  data () {
    return {
      
    }
  },
  methods: {
    
  }
}
</script>

<style scoped>

</style>