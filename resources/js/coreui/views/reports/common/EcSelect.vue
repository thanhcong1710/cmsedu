<template>
  <div
      class="form-group"
  >
    <label
        class="control-label"
        v-if="label"
    >{{ label }}<span
        class="text-danger"
        v-if="required"
    > (*)</span></label>
    <multiselect
        placeholder="Chọn EC"
        select-label="Enter để chọn tên EC này"
        v-model="localValue"
        :options="options"
        label="name"
        :close-on-select="true"
        :hide-selected="true"
        :multiple="multiple"
        :searchable="true"
        track-by="id"
        return="id"
        :disabled="disabled"
    >
      <span slot="noResult">Không tìm thấy ec phù hợp</span>
    </multiselect>
  </div>
</template>
<script>
  import u from '../../../utilities/utility'
  import multiselect from 'vue-multiselect'

  export default {
    name: 'EccSelect',
    components: {multiselect},
    props: {
      value: {},
      branchIds: {},
      label: {type: String, default: null},
      multiple: {type: Boolean, default: true},
      autoSetBranchDefault: {type: Boolean, default: true},
      trackBy: {type: String, default: null},
      required: {type: Boolean, default: false},
      params: {type: Object, default: null},
      readOnly: {type: Boolean, default: false},
    },
    data() {
      return {
        options: [], branches: [], branchId: null, loading: false,
      }
    },
    mounted() {
      this.getEcs(this.getBranchIds)
    },
    computed: {
      disabled() {
        return this.readOnly || this.loading || !this.branchIds
      },
      localValue: {
        get() {
          if (this.trackBy)
            return this.options.find((item) => (item[this.trackBy] === this.value))

          return this.value
        },
        set(value) {
          let data = Array.isArray(value) ? value : [value]
          if (this.trackBy)
            data = data.map((item) => item[this.trackBy])

          if (!this.multiple && data.length > 0)
            data = data[0]
          this.$emit('input', data)
        },
      },
      getBranchIds() {
        if (this.branchIds)
          return this.branchIds

        if (this.autoSetBranchDefault) {
          const branchId = _.get(u.session(), 'user.branches[0].id', null)
          return branchId ? [branchId] : null
        }
        return null
      },
    },
    watch: {
      branchIds(newValue, oldValue) {
        if (newValue !== oldValue)
          this.getEcs(newValue)
      },
    },
    methods: {
      getEcs(branchIds) {
        var roles = [676767, 686868, 7676767, 7777777, 86868686, 88888888, 999999999,56,69]
        if (!roles.includes(parseInt(u.session().user.role_id))) return
        const filterBranchIds = Array.isArray(branchIds) ? branchIds.filter(Boolean) : null
        if (!filterBranchIds) return

        this.loading = true
        u.g(`/api/users/get-ec-cm-list?${new Date().getTime()}`, {
          ...this.params,
          branch_ids: filterBranchIds
        }).then((response) => {
          this.options = response.data
          this.loading = false
        })
      },
    },
  }
</script>

<style lang="scss">
  .multiselect__placeholder {
    color: #3e515b;
  }

  .multiselect__tags {
    border: none !important;
  }

  .multiselect {
    border: 1px solid #d6caca !important;
  }

  .multiselect__select {
    height: 100%;
    right: 0;
    top: 0;
  }

  .multiselect--disabled {
    background: none !important;

    .multiselect__select {
      top: 0;
      height: 100% !important;
    }
  }

</style>
