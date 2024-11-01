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
            <i class="fa fa-clipboard"></i> <b class="uppercase">Cập nhật bảo lưu</b>
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
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Số buổi bảo lưu</label>
                                <input class="form-control" v-model="reserve.session" @change="getResult" >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Lý do bảo lưu</label>
                                <textarea class="form-control" rows="5" v-model="reserve.note" ></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày bắt đầu bảo lưu</label>
                                <calendar
                                  class="form-control calendar"
                                  format="YYYY-MM-DD"
                                  v-model="reserve.start_date"
                                  :onDrawDate="onDrawDate"
                                  :lang="html.lang"
                                  :not-after="reserve.last_end_date"
                                  :transfer="true"
                                  :pane="1"
                                  @input="getResult"
                                  :disabled="start_date_disabled"
                                ></calendar>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày kết thúc bảo lưu</label>
                                <input class="form-control" v-model="reserve.end_date" type="text" readonly>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày bắt đầu</label>
                                <input class="form-control" v-model="meta_data.start_date" type="text" readonly>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="control-label">Ngày kết thúc</label>
                                <input class="form-control" v-model="reserve.new_end_date" type="text" readonly>
                              </div>
                            </div>
                          </div>
                          <!-- <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <input type="checkbox" disabled readonly v-model="reserve.is_reserved"> Giữ chỗ trong lớp
                              </div>
                            </div>
                          </div> -->
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group" >
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
                                  v-if="reserve.status==0"
                                >
                                </file>
                                
                              </div>
                            </div>
                            <div class="col-md-6"  v-if="reserve.status==0">
                              <button class="btn btn-info print-button" @click="sendQLCL">Upload và gửi QLCL phê duyệt</button>
                            </div>
                            <div class="col-md-12">
                              <table class="table table-bordered apax-table">
                                  <tbody>
                                  <tr v-for="(fl, index) in list_file" :key="index" v-if="fl">
                                      <td><a target="_blank" type="button" :href="fl | genDownloadUrl" download>
                                        {{fl}}</a></td>
                                      <td v-if="reserve.status==0">
                                          <button @click="removeFile(fl)" class="apax-btn remove">
                                              <span class="fa fa-times"></span>
                                          </button>
                                      </td>
                                  </tr>
                                  <tr v-for="(fl, index) in list_file_new" :key="index">
                                      <td>{{fl.name}}</td>
                                      <td v-if="reserve.status==0">
                                          <button @click="removeFileNew(fl)" class="apax-btn remove">
                                              <span class="fa fa-times"></span>
                                          </button>
                                      </td>
                                  </tr>
                                  </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="panel-footer">
                          <div class="col-sm-12 col-sm-offset-3">
                            <ApaxButton
                              markup="success"
                              :onClick="saveEdit"
                            >Lưu
                            </ApaxButton>

                            <ApaxButton
                              :onClick="exitAddReserve"
                            >Thoát
                            </ApaxButton>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
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
  import calendar from 'vue2-datepicker'

  export default {
    name: 'List-Reserve',
    components: {
      calendar,
      SearchBranch,
      ApaxButton,
      paging,
      search,
      reserve,
      file
    },
    data() {
      return {
        show_attached_file:'',
        html: {
          modal: {
            show: false,
            message: ''
          },
          lang: {
              days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
              months: [
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
              ],
              pickers: ["", "", "7 ngày trước", "30 ngày trước"]
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
        cache_date:'',
        cache_session:'',
        meta_data: {},
        search_id: 'contract_search',
        search_name: 'search-contract',
        search_label: 'Tìm kiếm theo mã CMS, Tên học sinh hoặc Tên tiếng Anh',
        search_placeholder: 'Từ khóa tìm kiếm',
        disableSearch: true,
        placeholderSearchBranch: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
        list_file:[],
        list_view_file:[],
        list_file_new:[],
        start_date_disabled:false,
        session:'',
        not_before_date:'2020-01-15'
      }
    },
    created() {
      this.flags.form_loading = true;
      u.a().get('/api/branches').then(response => {
        this.branches = response.data;
      })
      this.getReserves()
      this.session = u.session()
    },
    methods: {
      getReserves(page_url) {
        page_url = page_url || '/api/reserves/edit/' + this.$route.params.id;
        this.flags.requesting= true;
        u.a().get(page_url)
          .then(response => {
            if(response.data.code == 200){
              const tmp = response.data.data
              this.reserve = tmp
              this.cache_date = tmp.start_date
              this.cache_session = tmp.session
              this.meta_data = JSON.parse(this.reserve.meta_data);
              if(this.reserve.status==2 && this.session.user.role_id!= '999999999'){
                this.start_date_disabled = true
              }
              this.list_file = this.reserve.attached_file.split('|*|');
            }else{
              this.html.modal.message = "<span class='text-danger'>"+response.data.message+"</span>";
              this.html.modal.show = true;
            }

            this.flags.requesting = false;
          }).catch(e => {
          this.flags.requesting= false;
        });
      },
      getResult(){
        if(this.reserve.start_date && this.reserve.session && (this.cache_date && this.cache_date!=this.reserve.start_date)|| (this.cache_session && this.cache_session!=this.reserve.session)){
          this.flags.requesting= true;
          u.p('/api/reserves/get-result-edit/' + this.$route.params.id,{reserve:this.reserve}).then(response => {
              const tmp = response
              this.reserve = tmp
              this.meta_data = JSON.parse(this.reserve.meta_data);
            this.flags.requesting = false;
          }).catch(e => {
            this.flags.requesting = false;
          });
        }
      },
      exitAddReserve() {
        this.$router.push('/reserves');
      },
      saveEdit(){
        if(!this.reserve.start_date){
          alert("Ngày bắt đầu bảo lưu không để trống")
          return false;
        }
        if(!this.reserve.session){
          alert("Số buổi bảo lưu không để trống")
          return false;
        }
        if(this.reserve.session > this.cache_session && this.reserve.status==2 && this.session.user.role_id!= '999999999'){
          alert("Số buổi bảo lưu phải nhỏ hơn số buổi đã được duyệt")
          return false;
        }
        this.flags.requesting= true;
          u.p('/api/reserves/save-edit/' + this.$route.params.id,{reserve:this.reserve}).then(response => {
            alert("Cập nhật bản ghi bảo lưu thành công!")
            this.flags.requesting = false;
            this.exitAddReserve();
          }).catch(e => {
            this.flags.requesting = false;
          });
      },
      onDrawDate (e) {
          let date = e.date;
          date = u.convertDateToString(date);
          if (this.session.user.role_id!= '999999999' && this.isGreaterThan(this.reserve.last_end_date,date)) {
              e.allowSelect = false;
          }
      },
      isGreaterThan(_from, _to){
          let _from_time = new Date(_from); // Y-m-d
          let _to_time = new Date(_to); // Y-m-d
          return (_from_time.getTime() > _to_time.getTime())?true:false;
      },
      removeFile(file){
          const new_list_file = []
          this.list_file.map(item => {
              if (item != file) {
                  new_list_file.push(item)
              }
              return item
          })
          this.list_file = new_list_file
      },
      sendQLCL() {
        if(!this.list_file.length){
          alert("Upload file đính kèm trước khi gửi yêu cầu phê duyệt");
        }else{
          let data = {
              reserve_id: this.reserve.id,
              attached_file : this.list_file_new,
              attached_file_curr : this.list_file
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
      uploadFile(file, param = null) {
          if (param) {
              this.list_file_new.push(file)
          }
      },
      removeFileNew(file){
          const new_list_file = []
          this.list_file_new.map(item => {
              if (item.name != file.name) {
                  new_list_file.push(item)
              }
              return item
          })
          this.list_file_new = new_list_file
      },
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
