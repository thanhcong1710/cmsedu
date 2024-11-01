<!--
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 *  Apax ERP System
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */-->

<template>
  <div class="app flex-row align-items-center">
    <div class="container">
      <h1>Confirm Account Information</h1>
    </div>
  </div>
</template>

<style scoped>

</style>

<script>
import u from "../../utilities/utility";

export default {
  methods: {

  },
  created() {
    const start = '2018-04-16'
    const end = '2018-06-03'
    const holidays = [{"start_date":"2018-04-25","end_date":"2018-04-25"},{"start_date":"2018-04-28","end_date":"2018-04-28"},{"start_date":"2018-04-29","end_date":"2018-04-29"},{"start_date":"2018-04-30","end_date":"2018-04-30"},{"start_date":"2018-05-01","end_date":"2018-05-02"},{"start_date":"2018-06-03","end_date":"2018-06-03"}]
    const class_days = [0,6]
    const sessions = 1
    const x = u.getRealSessions(sessions, class_days, holidays, start)
    const q = u.e('Content encrypt', 'secret key')
    const p = u.d(q, 'secret key')
    u.log('Result', q, p)
  }
}
</script>
