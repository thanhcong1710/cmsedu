<template>
  <div :id="tableID" :class="tableClass" class="apax-table container">
    <code>query: {{ query }}</code>
    <dataTable v-bind="$data" />
  </div>
</template>

<script>

  import dataTable from 'vue2-datatable-component'

	export default {
    name: "apax-table",
		props: {
      tableID: {
        type: String,
        default: null
      },
      tableClass: {
        type: String,
        default: null
      },
      handleQuery: {
        type: Function,
        default: null
      },
      sample: {
        type: Array,
        default: () => {
          return []
        }
      },
      sortBy: {
        type: Array,
        default: () => {
          return []
        }
      },
      more: {
        type: Array,
        default: () => {
          return []
        }
      },
      header: {
        type: Array,
        default: () => {
          return []
        }
      },
      source: {
        type: Array,
        default: () => {
          return []
        }
      }
    },
		data () {
			return {
        supportBackup: true,
        supportNested: true,
        tblClass: 'table table-striped table-bordered',
        tblStyle: 'color: #333',
        pageSizeOptions: [10, 15, 20, 25, 30, 35, 40, 45, 50],
        columns: (() => {
          const cols = this.header
          // const cols = [
          //   { title: 'UID', field: 'uid', label: 'User ID', sortable: true, visible: 'true' },
          //   { title: 'Email', field: 'email', visible: false, thComp: 'FilterTh', tdComp: 'Email' },
          //   { title: 'Username', field: 'name', thComp: 'FilterTh', tdStyle: { fontStyle: 'italic' } },
          //   { title: 'Country', field: 'country', thComp: 'FilterTh', thStyle: { fontWeight: 'normal' } },
          //   { title: 'IP', field: 'ip', visible: false, tdComp: 'IP' },
          //   { title: 'Age', field: 'age', sortable: true, thClass: 'text-info', tdClass: 'text-success' },
          //   { title: 'Create time', field: 'createTime', sortable: true, colClass: 'w-240', thComp: 'CreatetimeTh', tdComp: 'CreatetimeTd' },
          //   { title: 'Color', field: 'color', explain: 'Favorite color', visible: false, tdComp: 'Color' },
          //   { title: 'Language', field: 'lang', visible: false, thComp: 'FilterTh' },
          //   { title: 'PL', field: 'programLang', explain: 'Programming Language', visible: false, thComp: 'FilterTh' },
          //   { title: 'Operation', tdComp: 'Opt', visible: 'true' }
          // ]
          const groupsDef = {
            Normal: this.sample,
            Sortable: this.sortBy,
            Extra: this.more
          }
          return cols.map(col => {
            Object.keys(groupsDef).forEach(groupName => {
              if (groupsDef[groupName].includes(col.title)) {
                col.group = groupName
              }
            })
            return col
          })
        })(),
        data: this.source,
        total: 0,
        selection: [],
        summary: {},
        query: {},
        xprops: {
          eventbus: new Vue()
        }
      }
		},
		methods: {
      checkSelected(page_item) {
        return this.currentPage == page_item
      },
      linking(page_item) {
        return `${this.rootLink}/${page_item}`
      },
      navigate(link) {
        this.routing ? this.routing(link) : ''
        this.$emit('routing', link)
      }
    },
    computed: {

    },
		watch: {
      query: {
        handler (query) {
          if (this.handleQuery) {
            this.handleQuery(query).then(({ rows, total, summary }) => {
              this.data = rows
              this.total = total
              this.summary = summary
            })
          }
        },
        deep: true
      },
      source: function(data) {
        this.selection = Array.isArray(data) ? data : []
      },
      lastPage (){
        this.listType = this.lastPage > 9 ? 'list' : this.listStyle
        this.pages_list = range(1, this.lastPage+1)
      }
		}
	}
</script>

<style lang="scss" scoped>

</style>
