<template>
  <div
    class="animated"
    id="students-import"
  >
    <div class="col-12">
      <b-card header>
        <div>
          <div v-if="loading">
            loading
          </div>
          <div>
            <button
              class="btn btn-success"
              @click="migrateProvinceAccountingId"
            >
              <i class="fa fa-print"> &nbsp;Migrate Province Accounting Id</i>
            </button>
            <p>{{ provinceMessage }}</p>
          </div>
          <div>
            <button
              class="btn btn-success"
              @click="migrateDistrictAccountingId"
            >
              <i class="fa fa-print"> &nbsp;Migrate District Accounting Id</i>
            </button>
            <p>{{ districtMessage }}</p>
          </div>
        </div>
      </b-card>
    </div>
  </div>
</template>

<script>
import u from '../../utilities/utility'

export default {
  name: 'Migrate',
  data () {
    return {
      provinceMessage: '', districtMessage: '', loading        : false,
    }
  },
  methods: {
    migrateProvinceAccountingId () {
      this.loading         = true
      this.provinceMessage = null
      u.p('api/migrate/province/accounting-id').then((response) => {
        this.provinceMessage = `success: ${response.success || 0}   ---   error: ${response.error || 0}   ---   message: ${response.message} `
        this.loading         = false
      }
      )
    },
    migrateDistrictAccountingId () {
      this.loading         = true
      this.districtMessage = null
      u.p('api/migrate/district/accounting-id').then((response) => {
        this.districtMessage = `success: ${response.success || 0}   ---   error: ${response.error || 0}   ---   message: ${response.message}`
        this.loading         = false
      }
      )
    },
  },
}
</script>
