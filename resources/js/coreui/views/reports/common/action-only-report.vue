<template>
  <div class="print-btn-group">
    <button
      class="apax-btn full print"
      @click="onExport"
    >
      <i class="fa fa-print"> &nbsp;Xuất báo cáo</i>
    </button>
    <button v-show="addBtn"
            class="apax-btn full btn-success"
            @click="onExportList"
    >
      <i class="fa fa-file-word-o"> &nbsp;{{addBtn}}</i>
    </button>
    <button
      class="apax-btn full reset"
      @click="onClean"
    >
      <i class="fa fa-ban"> &nbsp;Bỏ lọc</i>
    </button>
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
    onExportList: {
      type   : Function,
      default: () => {},
    },
    addBtn: {
      type   : String,
      default: null,
    },
  },
}
</script>

<style scoped>
  .apax-btn{margin: 0 2px;}
  .print-btn-group{
    display: flex;
    width: 100%;
    justify-content: center;
    margin-top: 20px;
  }
</style>
