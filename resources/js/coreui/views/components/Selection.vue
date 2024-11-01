<template>
  <div class="apax-selection default input-prepend input-group" :id="id" :class="cls">
    <v-select
      v-model="selected"
      :branch-id="branchId"
      :label="name"
      :filterable="filter"
      :options="options"
      :placeholder="placeholder"
      :disabled="disabled"
      @input="onChange"
    ></v-select>
  </div>
</template>

<script>
import u from '../../utilities/utility'
import vSelect from 'vue-select'

export default {
  name: 'apax-selection',
  components: {
    'v-select': vSelect
  },
  data (){
    return {
      search: '',
      options: [],
      session: u.session(),
      selected: null,
    }
  },
  props: {
    id: {
      type: String,
      default: null
    },
    cls: {
      type: String,
      default: null
    },
    filter: {
      type: Boolean,
      default: true
    },
    url: {
      type: String,
      default: '/api/settings/branches'
    },
    name: {
      type: String,
      default: 'name'
    },
    label: {
      type: String,
      default: null
    },
    placeholder: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: true
    },
    onStart: {
      type: Function,
      default: null
    },
    onSelect: {
      type: Function,
      default: null
    },
    branchId:{
      type: String,
      default: null
    }
  },
  created() {
    const start = this.onStart ? this.onStart() : new Promise((resolve, reject) => {
      if (u.authorized()) {
        u.g(this.url)
        .then((response) => {
          if (response.length) {
            resolve(response)
          }
        }).catch(e => u.log('Exeption', e))
      } else {
        this.options = this.session.user.branches
      }
    })
    start.then(val => {
      this.options = val
      if (this.branchId){
        let item = val.find(item => item.id == this.branchId )
        this.selected = val[val.indexOf(item)]
      }
    })
  },
  methods: {
    onChange(value) {
      this.onSelect ? this.onSelect(value) : null
      this.$emit('onSelect', value)
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
