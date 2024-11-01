<template>
  <div class="branch-selection apax-selection default input-prepend input-group" :id="id" :class="cls">
    <v-select
      :label="name"
      :filterable="filter"
      :options="options"
      :placeholder="placeholder"
      :disabled="disabled"
      v-model="defaultBranch"
    ></v-select>
  </div>
</template>

<script>
  import u from '../utilities/utility'
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
        session: u.session()
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
      value: {
        type: Object
      }
    },
    created() {
      const start = this.onStart ? this.onStart() : new Promise((resolve, reject) => {
        this.options = this.session.user.branches
      })
      start.then(val => {
        this.options = val
      })
    },

    methods: {

    },
    watch: {

    },
    computed: {
      defaultBranch: {
        get:function () {
          if(this.value && u.live(this.value.id) && parseInt(this.value.id) > 0) {
            return this.value
          }
        },
        set: function (newValue) {
          this.onSelect ? this.onSelect(newValue) : null
          this.$emit('input', newValue)
        }
      }
    }

  }
</script>
<style>
  .branch-selection .dropdown.v-select.single.searchable.disabled input {
    background: transparent;
    border: none;
  }
</style>
