<template>
  <div class="animated fadeIn apax-form">
    <loader
      :active="loader.processing"
      :spin="loader.spin"
      :text="loader.text"
      :duration="loader.duration"
    />
    
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div slot="header">
           <strong>
              <i class="fa fa-list"></i> Danh sách đã import checkin
            </strong>
          </div>
            <a href="/static/doc/doc/template_import_checkin.xlsx" target="blank">
              <button class="apax-btn full detail">
                <i class="fa fa-file-excel-o"></i> Tải file mẫu import
              </button>
            </a>
           <button class="apax-btn full error" @click="showImport()">
              <i class="fa fa-upload"></i> Import
            </button>
         <table class="table table-bordered apax-table">
            <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="width-150">File import</th>
                <th class="width-150">Thời gian</th>
                <th class="width-150">Người tạo</th>
                <th class="width-150">Kết quả</th>
                <th class="width-150">Tải file kết quả</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item,index) in logs" :key="index">
                <td>{{(pagination.ppage * pagination.limit) + index + 1}}</td>
                <td>{{item.file_name}}</td>
                <td>{{item.created_at}}</td>
                <td>{{item.creator_name}}</td>
                <td>{{item.count_import_success}}|{{item.count_import}}</td>
                <td> <button class="apax-btn edit" @click="exportExcel(item)">
                    <i class="fa fa-file-excel-o"></i> 
                  </button></td>
              </tr>
            </tbody>
          </table>

          <div class="text-center">
            <nav aria-label="Page navigation">
              <appPagination
                :rootLink="router_url"
                :id="pagination_id"
                :listStyle="list_style"
                :customClass="pagination_class"
                :firstPage="pagination.spage"
                :previousPage="pagination.ppage"
                :nextPage="pagination.npage"
                :lastPage="pagination.lpage"
                :currentPage="pagination.cpage"
                :pagesItems="pagination.total"
                :pageList="pagination.pages"
                :pagesLimit="20"
                :routing="goTo"
              ></appPagination>
            </nav>
          </div>
        </b-card>
      </div>
    </div>
    <b-modal size="lg" v-model="importModal" title="Import danh sách checkin" ok-variant="success" class="modal-success">
      <b-container fluid>
        <b-row class="mb-1">
          <b-col cols="12">
            <div class="row">
              <div class="col-8">
                  <input
                    type="file"
                    class="form-control"
                    id="fileUploadExcel"
                    @change="fileChanged"
                  >
              </div>
              <div class="col-2">
                <button class="btn btn-info" @click="btnUpload">Import</button>
              </div>
            </div>
          </b-col>
        </b-row>
      </b-container>
      <div slot="modal-footer" class="w-100">
        <b-btn size="sm" class="float-right" variant="primary" @click="closeModal">Đóng</b-btn>
      </div>
    </b-modal>
  </div>
  
</template>

<script>
import Pagination from "../../../components/Pagination";
import loader from "../../../components/Loading";
import u from "../../../utilities/utility";

export default {
  components: {
    appPagination: Pagination,
    loader,
  },
  data() {
    return {
      logs: [],
      router_url: "/check-tmk/list",
      pagination_id: "check-tmk",
      pagination_class: "orders paging list",
      list_style: "line",
      pagination: {
        limit: 20,
        spage: 0,
        ppage: 0,
        npage: 0,
        lpage: 0,
        cpage: 0,
        total: 0,
        pages: []
      },
      loader: {
        spin: "mini",
        duration: 500,
        processing: false,
        text: "Đang tải dữ liệu..."
      },
      search: {
        status: "",
      },
      modal: {
        display: false,
        title: 'Thông Báo',
        class: 'modal-success',
        message: '',
        done: () => this.closeMessage()
      },
      importModal: false,
      attached_file:"",
      file_name:"",
    };
  },
  created() {
    this.searchLog()
  },
  methods: {
    searchLog() {
      var url = "/api/checkin-import/list/1/";
      this.key = "";
      this.value = "";
      var status = this.search.status ? this.search.status : "";
      if (status) {
        this.key += "status,";
        this.value += this.search.status + ",";
      }
      this.key = this.key ? this.key.substring(0, this.key.length - 1) : "_";
      this.value = this.value
        ? this.value.substring(0, this.value.length - 1)
        : "_";
      url += this.key + "/" + this.value;
      this.get(url);
    },
    goTo(link) {
      this.getOrders(link);
    },
    get(link) {
      this.ajax_loading = true;
      u.a()
        .get(link)
        .then(response => {
          this.logs = response.data.logs;
          this.pagination = response.data.pagination;
          this.ajax_loading = false;
        })
        .catch(e => console.log(e));
    },
    makePagination(meta, links) {
      let pagination = {
        current_page: data.current_page,
        last_page: data.last_page,
        next_page_url: data.next_page_url,
        prev_page_url: data.prev_page_url
      };
      this.pagination = pagination;
    },
    showImport() {
      this.importModal = true
    },
    closeModal() {
      this.importModal = false
    },
    fileChanged(e) {
      const fileReader = new FileReader();
      const fileName = e.target.value.split("\\").pop();
      this.file_name = fileName
      fileReader.readAsDataURL(e.target.files[0]);
      fileReader.onload = e => {
        this.attached_file = e.target.result;
      };
    },
    btnUpload() {
      const cf = confirm("Bạn có muốn Import?")
      if (cf) {
        this.importModal = false
        this.loader.processing = true
        let url = `/api/check-checkin-import/check-checkin-import-excel`
        let dataUpload = {
          files: this.attached_file,
          file_name: this.file_name,
        }
        u.p(url, dataUpload)
          .then(response => {
            this.loader.processing = false
            this.attached_file = ""
            $("#fileUploadExcel").val("")

            u.lg(response)

            this.modal.display = true
            this.modal.title = 'THÔNG BÁO'
            if (!response.error) {
              this.modal.message = 'Import file thành công!'
            } else {
              this.modal.message = response.message
            }
            this.searchLog()
          })
          .catch(e => console.log(e))
      }
    },
    exportExcel(data) {
      this.loader.processing = true
      var urlApi = "/api/checkin-import/export/"+data.id
      var tokenKey = u.token()
      u.g(urlApi,{},1,1)
        .then(response => {
          saveAs(response, "Kết quả import checkin.xlsx");
          this.loader.processing = false;
        })
        .catch(e => {
          this.loader.processing = false;
        });
    },
    clearSearch(){
      location.reload();
    },
    closeMessage() {
      this.modal.display = false
    },
  },
};
</script>

<style scoped lang="scss">
.table-header {
  margin-bottom: 5px;
  margin-left: -15px;
}
.img img {
  width: 100px;
}
.cl-btn a {
  color: #fff;
}
.card-footer {
  padding-top: 4px !important;
  padding-bottom: 4px !important;
}
</style>
