import Vue from 'vue'
import u from '../../../coreui/utilities/utility'

const withRequest = (component, configs = {
  url     : '', params  : null, keyState: '',
}) => (Vue.component('withRequest', {
  render (createElement) {
    return createElement(component, { props: { [configs.keyState]: this.data } })
  },
  data () {
    return { data: null }
  },
  methods: {
    fetch () {
      u.g(configs.url, configs.params).then((response) => {
        this.data = response
      })
    },
  },
  mounted () {
    this.fetch()
  },
  beforeDestroy () {

  },
}))

export default withRequest
