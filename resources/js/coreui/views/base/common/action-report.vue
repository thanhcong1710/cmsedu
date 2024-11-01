<template>
  <div class="print-btn-group">
    <button
      class="apax-btn full detail"
      @click="onSearch"
    >
      <i class="fa fa-eye"> &nbsp;Tìm kiếm</i>
    </button> &nbsp;
    <button
      class="apax-btn full print"
      @click="onExport"
    >
      <i class="fa fa-print"> &nbsp;Xuất báo cáo</i>
    </button>&nbsp;
    <button
      class="apax-btn full reset"
      @click="onClean"
    >
      <i class="fa fa-ban"> &nbsp;Bỏ lọc</i>
    </button> &nbsp;
    <router-link
      class="apax-btn full remove"
      :to="'/forms'"
    >
      Quay lại
    </router-link><br>
  </div>
</template>

<script>
export default {
  name : 'ActionReport',
  props: {
    onSearch: {
      type   : Function,
      default: () => {},
    },
    onExport: {
      type   : Function,
      default: () => {},
    },
    onClean: {
      type   : Function,
      default: () => {},
    },
    onBack: {
      type   : Function,
      default: () => {},
    },
  },
}
</script>

<style scoped>
  .print-btn-group{
    display: flex;
    width: 100%;
    justify-content: center;
    margin-top: 20px;
  }
</style>
