<template>
  <div class="text-center paging">
    <nav aria-label="Page navigation">
      <paging
        :root-link="pagination.url"
        :id="pagination.id"
        :list-style="pagination.style"
        :custom-class="pagination.class"
        :first-page="pagination.spage"
        :previous-page="pagination.ppage"
        :next-page="pagination.npage"
        :last-page="pagination.lpage"
        :current-page="pagination.cpage"
        :pages-items="pagination.total"
        :pages-limit="pagination.limit"
        :page-list="pagination.pages"
        :routing="changePage"
      />
    </nav>
    <select
      class="form-control paging-limit"
      v-model="pagination.limit"
      @change="onChange"
      style="width: auto;height: 30px !important;border: 0.5px solid #dfe3e6 !important;padding-top: 5px;"
    >
      <option
        v-for="(item, index) in pagination.limitSource"
        :value="item"
        :key="index"
      >
        {{ item }}
      </option>
    </select>
  </div>
</template>

<script>
import paging from '../../components/Pagination'

export default {
  name      : 'PagingReport',
  components: { paging },
  data () {
    return {
      pagination: {
        url        : '',
        id         : '',
        style      : 'line',
        class      : '',
        spage      : 1,
        ppage      : 1,
        npage      : 0,
        lpage      : 1,
        cpage      : 1,
        total      : 0,
        limit      : 20,
        limitSource: [
          10,
          20,
          30,
          40,
          50,
        ],
        pages: [],
      },
    }
  },
  props: {
    title   : { type: String, default: '' },
    onChange: { type: Function, default: null },
    total   : {
      type   : Number,
      default: 0,
    },
  },
  created () {
    this.$emit('input', this.pagination)
  },
  watch: {
    total: function (newValue, oldValue) {
      if (newValue !== oldValue) {
        this.pagination.total = newValue
        this.pagination.lpage = Math.ceil(
          this.pagination.total / this.pagination.limit
        )
        this.pagination.ppage = this.pagination.cpage > 0 ? this.pagination.cpage - 1 : 0
        this.pagination.npage = this.pagination.cpage + 1
        this.$emit('input', this.pagination)
      }
    },
  },
  methods: {
    changePage (link) {
      const info            = link.toString().split('/')
      const page            = info.length > 1 ? info[1] : 1
      this.pagination.cpage = parseInt(page)
      this.pagination.ppage = this.pagination.cpage > 0 ? this.pagination.cpage - 1 : 0
      this.pagination.npage = this.pagination.cpage + 1
      this.onChange()
      this.$emit('input', this.pagination)
    },
  },
}
</script>
