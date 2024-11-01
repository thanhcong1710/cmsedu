<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <div v-show="flags.requesting" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.requesting" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
            </div>
          </div>

          <div slot="header">
            <i class="fa fa-clipboard"></i> <b class="uppercase">Nội dung bảo lưu</b>
          </div>
          <div id="page-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="panel">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-6 pad-no">
                        <div class="col-md-12">
                          <address>
                            <h6 class="text-main">Thông tin học sinh</h6>
                          </address>
                        </div>

                        <div class="col-md-12 pad-no">
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <label class="control-label">Trung tâm</label>
                                <input class="form-control" :value="reserve.branch_name"
                                       type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Mã Cyber</label>
                                <input class="form-control" :value="reserve.accounting_id"
                                       type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Mã CMS</label>
                                <input class="form-control" :value="reserve.crm_id"
                                       type="text" readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Họ Tên</label>
                                <input class="form-control" :value="reserve.student_name"
                                       type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Tên Tiếng Anh</label>
                                <input class="form-control" :value="reserve.nick"
                                       type="text" readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Sản phẩm</label>
                                <input class="form-control" :value="reserve.product_name"
                                       type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Chương trình</label>
                                <input class="form-control"
                                       :value="reserve.program_name" type="text"
                                       readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Lớp</label>
                                <input class="form-control"
                                       :value="reserve.class_name" type="text"
                                       readonly>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số phí đã đóng</label>
                                <input class="form-control"
                                       :value="meta_data.total_fee | formatMoney" type="text"
                                       readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Tổng số buổi học</label>
                                <input class="form-control" :value="meta_data.total_session"
                                       type="text" readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số phí bảo lưu</label>
                                <input class="form-control" :value="meta_data.amount_reserved | formatMoney"
                                       type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi chưa học xin bảo lưu</label>
                                <input class="form-control"
                                       :value="meta_data.number_of_session_reserved" type="text"
                                       readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-6 pad-no">
                        <div class="col-md-12">
                          <address>
                            <h6 class="text-main">Thông tin Bảo lưu</h6>
                          </address>
                        </div>
                        <!--<div class="col-md-12">-->
                        <!--<div class="form-group">-->
                        <!--<label class="filter-label control-label">Hình thức bảo lưu</label><br/>-->
                        <!--<input class="form-control" readonly :value="reserve.type | reserveType">-->
                        <!--</div>-->
                        <!--</div>-->
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi bảo lưu</label>
                                <input class="form-control" readonly :value="reserve.session">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Lý do bảo lưu</label>
                                <textarea class="form-control" readonly rows="5" v-model="reserve.note"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày bắt đầu bảo lưu</label>
                                <input class="form-control" readonly :value="reserve.start_date"/>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày kết thúc bảo lưu</label>
                                <input class="form-control" :value="reserve.end_date" type="text" readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày bắt đầu</label>
                                <input class="form-control" :value="meta_data.start_date" type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày kết thúc</label>
                                <input class="form-control" :value="reserve.new_end_date" type="text" readonly>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <input type="checkbox" disabled readonly v-model="reserve.is_reserved"> Giữ chỗ trong lớp
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group"  v-if="show_attached_file">
                                <label class="control-label">File đính kèm</label>
                                <table class="table table-bordered apax-table">
                                    <tbody>
                                    <tr v-for="(fl, index) in list_view_file" :key="index">
                                        <td>
                                          <a target="_blank" type="button" class="btn btn-primary btn-upload" :href="fl | genDownloadUrl" download><i class="fa fa-download"></i>&nbsp; Tải về file đính kèm {{index+1}}</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                              </div>
                              <div class="form-group" v-if="!show_attached_file && reserve.type">
                                <file
                                  :label="'File đính kèm'"
                                  :name="'upload_transfer_file'"
                                  :field="'attached_file'"
                                  :type="'transfer_file'"
                                  :full="false"
                                  :onChange="uploadFile"
                                  :title="'Tải lên 1 file đính kèm với định dạng tài liệu: jpg, png, pdf, doc, docx.'"
                                  :customClass="'no-current-file'"
                                  :multi="true"
                                >
                                </file>
                                <table class="table table-bordered apax-table">
                                    <tbody>
                                    <tr v-for="(fl, index) in list_file" :key="index">
                                        <td>{{fl.name}}</td>
                                        <td>
                                            <button @click="removeFile(fl)" class="apax-btn remove">
                                                <span class="fa fa-times"></span>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                              </div>
                            </div>
                            <div class="col-md-6"  v-if="!show_attached_file && reserve.type ">
                              <button class="btn btn-info print-button" @click="sendQLCL">
                                <span v-if="reserve.status==0">Upload và gửi QLCL phê duyệt</span>
                                <span v-else>Upload</span>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div>
            <button class="btn btn-success print-button" @click="callPrintForm">In đơn bảo lưu</button>
          </div>
        </b-card>
      </div>
    </div>

    <div class="row">
      <div class="col-12">

        <b-card header-tag="header" footer-tag="footer">
          <div v-show="flags.form_loading" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
            </div>
          </div>

          <div slot="header">
            <strong>Danh sách bảo lưu</strong>
            <router-link class="back-btn" :to="`/reserves`"><i class="fa fa-reply"></i> Quay lại</router-link>
          </div>
          <div class="content-detail">
            <div class="table-responsive scrollable">
              <table class="table table-striped table-bordered apax-table">
                <thead>
                <tr class="text-sm">
                  <th>STT</th>
                  <th>Thời gian đăng ký</th>
                  <th>Nguời đăng ký</th>
                  <!--<th>Mã EFFECT</th>-->
                  <!--<th>Mã CMS</th>-->
                  <!--<th>Tên học sinh</th>-->
                  <th>Loại bảo lưu</th>
                  <th>Trung tâm</th>
                  <th>Lớp</th>
                  <th>Số buổi bảo lưu</th>
                  <th>Ngày bắt đầu bảo lưu</th>
                  <th>Giữ chỗ</th>
                  <!-- <th>Người duyệt lần 1</th>
                  <th>Ngày duyệt lần 1</th> -->
                  <th>Người duyệt</th>
                  <th>Ngày duyệt</th>
                  <th>Trạng thái</th>
                  <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in reserves" :key="index">
                  <td>{{ index+1 }}</td>
                  <td>{{ item.created_at }}</td>
                  <td>{{ item.creator }}</td>
                  <!--<td>{{ item.accounting_id }}</td>-->
                  <!--<td>{{ item.crm_id }}</td>-->
                  <!--<td>{{ item.student_name }}</td>-->
                  <td>{{ item.type | reserveType(item.is_supplement) }}</td>
                  <td>{{ item.branch_name }}</td>
                  <td>{{ item.class_name }}</td>
                  <td>{{ item.session }}</td>
                  <td>{{ item.start_date }}</td>
                  <td>
                    <span class="text-success" v-if="item.is_reserved==1"><i class="fa fa-check"></i></span>
                    <span class="text-danger" v-else><i class="fa fa-times"></i></span>
                  </td>
                  <!-- <td>{{ item.approver }}</td>
                  <td>{{ item.approved_at }}</td> -->
                  <td>{{ item.final_approver }}</td>
                  <td>{{ item.final_approved_at }}</td>
                  <td>
                    <span>
                        <span v-if="item.status == 0" class="text-warning">Chờ duyệt</span>
                        <span v-else-if="item.status == 2" class="text-success">Đã duyệt</span>
                        <span v-else-if="item.status == 3" class="text-danger">Từ chối</span>
                        <span v-else-if="item.status == 4" class="text-danger">Đã hủy</span>
                    </span>
                  </td>
                  <td>
                    <span class="apax-btn edit disabled" v-if="item.can_edit_reserve && item.next_reserve == 0 && (item.status==2 ||item.status==0)">
                      <router-link title="Nhấp vào để sửa ngày kết thúc bảo lưu đã duyệt" class="link-me" :to="`/reserves/edit/${item.id}`"><i class="fa fa-pencil"></i></router-link>
                    </span>
                    <span class="apax-btn detail disabled" @click="showReserve(item)">
                      <i class="fa fa-eye"></i>
                    </span>
                    <span class="apax-btn remove disabled" v-if="item.status==0">
                      <span title="Nhấp vào để hủy bảo lưu" class="link-me" @click="cancelReserve(item)"><i class="fa fa-ban"></i></span>
                    </span>
                  </td>
                </tr>
                </tbody>
              </table>
              <div class="text-center">
                <nav aria-label="Page navigation">

                </nav>
              </div>
            </div>
          </div>
        </b-card>
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="callback" ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
          <div v-html="html.modal.message">
          </div>
        </b-modal>
      </div>
    </div>

  </div>

</template>

<script>
  import u from '../../../utilities/utility'
  import SearchBranch from '../../../components/SearchBranchForTransfer'
  import paging from '../../../components/Pagination'
  import search from '../../../components/Search'
  import reserve from '../../base/prints/reserve'
  import ApaxButton from '../../../components/Button'
  import file from '../../../components/File'

  export default {
    name: 'List-Reserve',
    components: {
      SearchBranch,
      ApaxButton,
      paging,
      search,
      reserve,
      file
    },
    data() {
      return {
        session: u.session().user,
        show_attached_file:'',
        html: {
          modal: {
            show: false,
            message: ''
          }
        },
        flags: {
          form_loading: false,
          requesting: false
        },
        success: 'success',
        branches: [],
        branch: '',
        pagination: {},
        reserves: [],
        item: {},
        reserve: {

        },
        meta_data: {},
        search_id: 'contract_search',
        search_name: 'search-contract',
        search_label: 'Tìm kiếm theo mã CMS, Tên học sinh hoặc Tên tiếng Anh',
        search_placeholder: 'Từ khóa tìm kiếm',
        disableSearch: true,
        placeholderSearchBranch: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
        list_file:[],
        list_view_file:[],
      }
    },
    created() {
      this.flags.form_loading = true;
      u.a().get('/api/branches').then(response => {
        this.branches = response.data;
      })
      this.getReserves()

    },
    methods: {
      uploadFile(file, param = null) {
          if (param) {
              this.list_file.push(file)
          }
      },
      removeFile(file){
          const new_list_file = []
          this.list_file.map(item => {
              if (item.name != file.name) {
                  new_list_file.push(item)
              }
              return item
          })
          this.list_file = new_list_file
      },
      callback(){
        this.html.modal.show = false;
      },
      getReserves(page_url) {
        page_url = page_url || '/api/reserves-info/' + this.$route.params.id;
        u.a().get(page_url)
          .then(response => {
            if(response.data.code == 200){
              this.reserves = response.data.data
              if(this.reserves.length){
                this.showReserve(this.reserves[0]);
              }
            }else{
              this.html.modal.message = "<span class='text-danger'>"+response.data.message+"</span>";
              this.html.modal.show = true;
            }

            this.flags.form_loading = false;
          }).catch(e => {
          this.flags.form_loading = false;
        });
      },
      showReserve(item){
        const tmp_reserve = item
        this.show_attached_file = tmp_reserve.attached_file
        this.reserve = item;
        this.list_view_file = item.attached_file.split('|*|');
        if(item.meta_data && ((typeof item.meta_data) == 'string')){
          this.meta_data = JSON.parse(item.meta_data);
        }else{
          this.meta_data = {};
        }
        // const id = this.reserve.id
        // this.callPrintFunc(id);
      },
      callPrintFunc(reserve_id){
        u.a().get(`/api/reserves-info/print/${reserve_id}`).then(response => {
          this.item = response.data.data;
          console.log(` this is reserve ${this.item}`);
          // console.log(this.branches);
        })
      },
      callPrintForm() {
        window.open(`/print/reserve/${this.reserve.id}`, '_blank');
      },
      sendQLCL() {
        if(!this.list_file.length){
          alert("Upload file đính kèm trước khi gửi yêu cầu phê duyệt");
        }else{
          let data = {
              reserve_id: this.reserve.id,
              attached_file : this.list_file
            };
            this.flags.requesting = true;
            u.p('/api/reserves/send_qlcl', data)
              .then(response => {
                this.flags.requesting = false;
                alert("Upload và gửi QLCL phê duyệt thành công");
                location.reload();
              }).catch(e => {

            });
        }
      },
      cancelReserve(item) {
        this.flags.requesting = true;
        const delStdConf = confirm("Bạn có chắc rằng muốn hủy bảo lưu này không?");
        if (delStdConf === true) {
          u.g(`/api/reserves/cancel_reserve/${item.id}`)
              .then(response => {
                this.flags.requesting = false;
                // alert("Hủy bảo lưu thành công");
                location.reload();
              }).catch(e => {

            });
        }
        
      }
    }
  }
</script>

<style scoped>
  .apax-form .btn-upload{
    width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    text-align: left;
  }
  .card-header .back-btn{
    font-size: 12px;
    padding: 2px 10px;
    background: #a4b7c1;
    color: #151b1e;
    text-shadow: none;
    text-transform: none;
    text-decoration: none;
    float: right;
    position: absolute;
    right: 34px;
    top: 9px;
    line-height: 20px;
  }
</style>
