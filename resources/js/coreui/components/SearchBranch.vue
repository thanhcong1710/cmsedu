<template>
  <div class="suggetion-search">
    <input type="hidden" :v-model="selectedBranch" />
    <autocomplete
      url="/api/scope/branch/"
      anchor="branch_name"
      label=""
      name="search_suggest_branch"
      id="search_suggest_branch"
      ref="filter_branch"
      className="search-autocomplete"
      :title="title"
      :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item' }"
      :min="3"
      :debounce="10"
      :filterByAnchor="true"
      :placeholder="placeholderBranch"
      :disabled="disableSearch"
      :onShouldGetData="searchBranch"
      :onSelect="selectItem">
    </autocomplete>
    <div :class="{active: isLoading}" class="icon-group loading"><img src="/static/img/images/loading/tgl.gif"></div>
    <div :class="{active: !isLoading}" class="icon-group search"><i v-b-tooltip.hover title="Nhập thông tin tìm kiếm" class="fa fa-search"></i></div>
  </div>
</template>


<script>
import u from '../utilities/utility'
import Autocomplete from 'vue2-autocomplete-js'

export default {
  props:{
    title: String,
    // selectedBranch: Object,
    placeholderBranch: {
      type: String,
      default: 'Tìm kiếm trung tâm theo tên đơn vị'
    },
    responseBranches: Array,
    onSearchBranchReady: Function,
    onSelectBranch: Function,
    beforeSearchBranch: Function,
    afterSearchBranch: Function,
  },
  data () {
    return {
      name: '',
      disableSearch: true,
      selectedBranch: Object,
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
  created () {
    // this.onSearchBranchReady ? this.onSearchBranchReady(response.data.data) : null
  },
  methods: {
    searchBranch (value) {
      this.beforeSearchBranch ? this.beforeSearchBranch(value) : null
      this.isLoading = true
      // u.log('Searching ', this.isLoading)
      // u.log('Search: ', value)
      if ((value.substr(-1) === ' ' || value.length % 4 === 0) && this.isLoading) {
        return new Promise((resolve, reject) => {
          u.a().get('/api/scope/branch/' + value)
            .then((response) => {
            let resp = response.data.data
            resp = resp.length ? resp : [{ branch_name: 'Không tìm thấy trung tâm nào có tên phù hợp'}]
            this.afterSearchBranch ? this.afterSearchBranch(resp) : null
            this.$emit('responseBranches', resp)
            this.isLoading = false
            // u.log('Done ', this.isLoading)
            // u.log(`Response for: ${value}`, response.data.branches)
            resolve(resp)
          })
        })
      }
    },
    selectItem(branch) {
      this.isLoading = false
      // u.log(`selected branch: ${JSON.stringify(branch)}`)
      this.$emit('doSelectBranch', branch)
      // this.selectedBranch = this.selectedBranch ? branch : null
      this.onSelectBranch ? this.onSelectBranch(branch) : null
    }
  }
}
</script>

<style scoped language="scss">
.suggetion-search {
  width:100%;
  position: relative;
  margin: 0 0 20px 0;
}
.suggetion-search.hidden {
  display: none;
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
</style>
