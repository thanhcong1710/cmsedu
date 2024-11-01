<template functional>
  <div
    class="animated fadeIn apax-form"
    id="students-management"
  >
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book" /> <strong>Nhập danh sách học viên</strong>
          </div>
          <div class="controller-bar table-header">
            <router-link to="/student-temp">
              <button
                type="button"
                class="apax-btn full detail"
              >
                <i class="fa fa-reply" /> Trở về
              </button>
            </router-link>
            &nbsp;
            <button
              type="button"
              class="apax-btn full detail"
              @click="props.actions.getTemplate"
            >
              <i class="fa fa-arrow-circle-o-down" /> Tải tệp mẫu nhập dữ liệu
            </button>
            <label>Chọn tệp dữ liệu:
              <input
                type="file"
                ref="file"
                @change="props.actions.changeFile"
              >
            </label>
            <button
              v-if="props.state.allowImport"
              type="button"
              class="apax-btn full detail"
              @click="props.actions.importStudent"
            >
              <i class="fa fa-save" /> Lưu
            </button>
          </div>

        </b-card>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
  a {
    color: blue;
  }

  .avatar-frame {
    padding: 3px 0 0 0;
    line-height: 40px;
  }

  .avatar {
    height: 29px;
    width: 29px;
    margin: 0 auto;
    overflow: hidden;
  }

  p.filter-lbl {
    width: 40px;
    height: 35px;
    float: left;
  }

  .filter-selection {
    width: calc(100% - 40px);
    float: left;
    padding: 3px 5px;
    height: 35px !important;
  }

  .drag-me-up {
    margin: -25px -15px -15px;
  }

  .import-row-error {
    background-color: rgba(255, 0, 0, 0.37) !important;
  }

  .import-row-warning {
    background-color: rgba(255, 165, 0, 0.22) !important;
  }
</style>
