<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      
  </div>
</div>
</template>

<script>
import u from '../../../utilities/utility'

export default {
  name: 'Edit-Discount',
  data() {
    return {
      discount: {
        name: '',
        description: ''
      }
    }
  },
  created(){
    u.a().get(`/api/discounts/${this.$route.params.id}`).then(response =>{
      this.discount = response.data
    })
  },
  methods: {
    updateDiscount(){
      u.a().put(`/api/discounts/${this.$route.params.id}`, this.discount).then(res =>{
        this.$router.push('/discounts-forms')
      })
    },
  }
}
</script>


