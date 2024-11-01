<template>
    <div class="wrapper">
      <div class="animated fadeIn">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <strong>Chi Tiết Nội Dung Hình Thức Thu Phí</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                  </div>
                  <div class="content-detail">
                  </div> 
                   <ApaxButton
                      :markup="success"
                      :onClick="printForm"
                     >In Giấy Nhập Học
                    </ApaxButton> 
                </b-card>
            </b-col>
        </b-row>
      </div> 
    </div>
</template>

<script>
import axios from 'axios'
import u from '../../../utilities/utility'
import ApaxButton from '../../../components/Button'
import spell from 'written-number'

spell.defaults.lang = 'vi'

export default {
  name: 'Payment-Detail',
  components: {
        ApaxButton
    },
  data () {
    return {
      success: 'success',
      
    }
  },
  methods: {
    printForm() {
      // print('apax-printing-content', 'html')
      u.print('contract', 'Giấy Nhập Học')
    },
  }
}
</script>

<style scoped>

</style>