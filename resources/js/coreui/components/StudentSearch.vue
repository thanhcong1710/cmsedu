<template>
  <div class="suggetion-search">
    <input type="hidden" :v-model="selectedStudent" />
    <autocomplete
      url="/api/students/suggest/"
      anchor="label"
      label="branch_name"
      name="search_sugget_student"
      id="sugget_student"
      className="search-autocomplete"
      :title="title"
      :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item' }"
      :min="3"
      :options="response"
      :debounce="10"
      :filterByAnchor="true"
      :placeholder="placeholderStudent"
      :onShouldGetData="suggestStudents"
      :onSelect="selectItem">
    </autocomplete>
    <!-- <div :class="{active: isLoading}" class="icon-group loading"><img src="/static/img/images/loading/tgl.gif"></div> -->
    <div :class="{active: !isLoading}" class="icon-group search"><i v-b-tooltip.hover title="Nhập thông tin tìm kiếm" class="fa fa-search"></i></div>
  </div>
</template>

<style scoped language="scss">

.suggetion-search {
  width:100%;
  position: relative;
}
.suggetion-search .icon-group {
  position: absolute;
  right: 0;
  padding:1px;
  height: 35px;
  width: 43px;
  top:0;
  display: none;
}
.suggetion-search .icon-group.active {
  display: block;
}
.suggetion-search .icon-group.loading {
  padding: 4px 0 0 0;
  text-align: center;

}
.suggetion-search .icon-group.search {
  text-align: center;
  padding: 7px 4px 0 5px;
}
#sugget_student {
  border: .5px solid #e07d7d !important;
  background: #fcf2f2;
  box-shadow: 0px -1px 0px #a13939;
  padding: 1px 20px 5px 15px;
  font-size: 14px;
  text-shadow: 1px 1px 0px #ffffff;
  color: #7d2525;
}
</style>

<script>
import u from '../utilities/utility'
import Autocomplete from 'vue2-autocomplete-js'

export default {
  props:{
    title: String,
    endpoint: {
      type: Number,
      default: 0
    },
    placeholderStudent: {
      type: String,
      default: 'Tìm kiếm học sinh theo tên hoặc mã CMS'
    },
    response: {
      type: Array,
      default: () => []
    },
    suggestStudents: {
      type: Function,
      default: null
    },
    selectedStudent:  {
      type: Object,
      default: null
    },
    onSelectStudent:  {
      type: Function,
      default: null
    },
    beforeSearchStudent:  {
      type: Function,
      default: null
    },
    afterSearchStudent:  {
      type: Function,
      default: null
    }
  },
  data () {
    return {
      name: '',
      isLoading: false,
      searchClass: {
        active: this.ajaxloading ? false : true,
        'icon-group': true,
        search: true
      }
    }
  },
  components: {
    Autocomplete
  },
  watch: {
    endpoint() {
      console.log(`Endpoint: ${this.endpoint}`)
    }
  },
  methods: {
    selectItem(student) {
      this.$emit('doSelectStudent', student)
      this.onSelectStudent ? this.onSelectStudent(student) : null
    }
  }
}
</script>
