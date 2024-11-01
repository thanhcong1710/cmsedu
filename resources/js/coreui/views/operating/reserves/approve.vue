<template>
  <div class="animated fadeIn apax-form">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <loader :active="html.loader.processing" :spin="html.loader.spin" :text="html.loader.text" :duration="html.loader.duration" />
          <div slot="header">
            <i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
          </div>
          <div class="content-detail">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <SearchBranch
                        :searchId="html.filter.branch.id"
                        :onSearchBranchReady="prepareSearch"
                        :onSelectBranch="selectBranch"
                        :placeholderBranch="html.filter.branch.placeholderSearchBranch"
                        :limited="true"
                      >
                      </SearchBranch>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Tên học sinh/Mã CMS/Mã Cyber</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i class="fa fa-search"></i>
                      </p>
                      <input type="text" class="form-control filter-selection"
                             v-model="filter.lms_effect_id" @input="validKey"
                             :disabled="html.filter.lms_effect.disabled"/>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Giữ chỗ trong lớp</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i class="fa fa-search"></i>
                      </p>
                      <select type="text" class="form-control filter-selection"
                              v-model="filter.is_reserved" :disabled="html.filter.is_reserved.disabled">
                        <option value="-1">Tất cả</option>
                        <option value="1">Có</option>
                        <option value="0">Không</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="filter-label control-label">Loại bảo lưu</label><br/>
                      <p class="input-group-addon filter-lbl">
                        <i class="fa fa-search"></i>
                      </p>
                      <select type="text" class="form-control filter-selection"
                              v-model="filter.is_extended" :disabled="html.filter.is_extended.disabled">
                        <option value="-1">Tất cả</option>
                        <option value="0">Bảo lưu theo quy định</option>
                        <option value="1">Ngoài quy định</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <button class="apax-btn full edit" @click="filterData(1)"><i class="fa fa-search"></i> Tìm kiếm
            </button>
            <button class="apax-btn full" @click="removeFilter()"><i class="fa fa-ban"></i> Bỏ lọc</button>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">

        <b-card header-tag="header" footer-tag="footer">
          <loader :active="html.loader.processing" :spin="html.loader.spin" :text="html.loader.text" :duration="html.loader.duration" />
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
                  <th>Mã Cyber</th>
                  <th>Mã CMS</th>
                  <th>Tên học sinh</th>
                  <th>Loại bảo lưu</th>
                  <th>Trung tâm</th>
                  <th>Lớp</th>
                  <th>Số buổi bảo lưu</th>
                  <th>Ngày bắt đầu bảo lưu</th>
                  <th>Giữ chỗ</th>
                  <th>Trạng thái</th>
                  <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(reserve, index) in reserves" :key="index">
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{(pagination.ppage * pagination.limit) + index + 1}}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.created_at }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.creator }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.accounting_id }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.crm_id }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.student_name }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.type | reserveType }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.branch_name }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.class_name }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.session }}
                    </span>
                  </td>
                  <td>
                    <span v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me pointer" @click="showReserve(reserve)">
                      {{ reserve.start_date }}
                    </span>
                  </td>
                  <td>
                    <span class="text-success" v-if="reserve.is_reserved==1"><i class="fa fa-check"></i></span>
                    <span class="text-danger" v-else><i class="fa fa-times"></i></span>
                  </td>
                  <td>
                    <span v-if="reserve.status == 0" class="text-warning">Chờ duyệt</span>
                    <span v-else-if="reserve.status == 1" class="text-success">Đã duyệt</span>
                    <span v-else class="text-danger">Từ chối</span>
                  </td>
                  <td>
                    <button class="apax-btn detail" v-b-tooltip.hover :title="`Nhấp vào để xem chi tiết`" @click="showReserve(reserve)">
                      <i class="fa fa-eye"></i>
                    </button>
                    <button class="apax-btn edit text-center" v-b-tooltip.hover :title="`Nhấp vào để duyệt`" @click="confirmApprove(reserve)">
                      <i class="fa fa-paper-plane"></i>
                    </button>
                    <button class="apax-btn remove text-center" v-b-tooltip.hover :title="`Nhấp vào để từ chối`" @click="confirmDeny(reserve)">
                      <i class="fa fa-ban"></i>
                    </button>
                  </td>
                </tr>
                </tbody>
              </table>
              <div class="text-center">
                <nav aria-label="Page navigation">
                  <paging
                    :rootLink="pagination.url"
                    :id="pagination.id"
                    :listStyle="pagination.style"
                    :customClass="pagination.class"
                    :firstPage="pagination.spage"
                    :previousPage="pagination.ppage"
                    :nextPage="pagination.npage"
                    :lastPage="pagination.lpage"
                    :currentPage="pagination.cpage"
                    :pagesItems="pagination.total"
                    :pagesLimit="pagination.limit"
                    :pageList="pagination.pages"
                    :routing="redirect">
                  </paging>
                </nav>
              </div>
            </div>
          </div>
        </b-card>
        <b-modal title="THÔNG BÁO" class="modal-primary" size="sm" v-model="html.modal.show" @ok="closeModal" ok-variant="primary" ok-only :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
          <div v-html="html.modal.message">
          </div>
        </b-modal>
        <b-modal title="Xác nhận" class="modal-primary" size="sm" v-model="html.approve_modal.show" @ok="approve" ok-variant="primary" :no-close-on-backdrop="true" :no-close-on-esc="true" :hide-header-close="true">
          <div>Bạn đã chắc chắn duyệt bảo lưu này?</div>
        </b-modal>
        <b-modal title="Xác nhận" class="modal-primary" size="sm" v-model="html.deny_modal.show" hide-footer>
          <div class="form-group">
            <div class="form-group">
              <label class="control-label">Ý kiến phản hồi</label>
              <textarea class="form-control" v-model="html.deny_modal.comment"></textarea>
              <span class="text-danger" :class="html.deny_modal.validReason">Thông tin này không được để trống</span>
            </div>
          </div>
          <button class="mt-3 apax-btn full remove" @click="deny">OK</button>
          <button class="mt-3 apax-btn full" @click="closeDenyModal">Hủy</button>
        </b-modal>
      </div>
    </div>

    <b-modal title="Chi tiết bảo lưu"
             :modal-class="`modal-primary modal-no-padding`" size="xl"
             v-model="html.detail_modal.show"
             ok-variant="primary"
             ok-only
             :no-close-on-backdrop="true"
             :no-close-on-esc="true"
             :hide-header-close="true"
    >
      <div class="row apax-form">
        <div class="col-12">
          <b-card header>
            <loader :active="html.loader.processing" :spin="html.loader.spin" :text="html.loader.text" :duration="html.loader.duration" />
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
                                  <label class="control-label">Mã EFFECT</label>
                                  <input class="form-control" :value="reserve.accounting_id"
                                         type="text" readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Mã LMS</label>
                                  <input class="form-control" :value="reserve.lms_id"
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
                              <!-- <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Tên Tiếng Anh</label>
                                  <input class="form-control" :value="reserve.nick"
                                         type="text" readonly>
                                </div>
                              </div> -->
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
                                         :value="reserve.meta_data.total_fee | formatMoney" type="text"
                                         readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Tổng số buổi học</label>
                                  <input class="form-control" :value="reserve.meta_data.total_session"
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
                                  <input class="form-control" :value="reserve.meta_data.amount_reserved | formatMoney"
                                         type="text" readonly>
                                </div>
                              </div>
                              <template v-if="reserve.version == 1">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Số buổi chưa học xin bảo lưu</label>
                                  <input class="form-control"
                                         :value="reserve.meta_data.number_of_session_reserved" type="text"
                                         readonly>
                                </div>
                              </div>
                            </template>
                            <template v-else>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Số buổi chưa học xin bảo lưu</label>
                                  <input class="form-control"
                                         :value="reserve.meta_data.number_of_sessions_reserved" type="text"
                                         readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Số buổi chính thức chưa học</label>
                                  <input class="form-control"
                                         :value="reserve.meta_data.number_of_real_sessions_reserved" type="text"
                                         readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">Số buổi học bổng chưa học</label>
                                  <input class="form-control"
                                         :value="reserve.meta_data.number_of_sessions_reserved - reserve.meta_data.number_of_real_sessions_reserved" type="text"
                                         readonly>
                                </div>
                              </div>
                            </template>
                            </div>
                          </div>
                        </div>


                        <div class="col-md-6 pad-no position-relative">
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
                                  <input class="form-control" :value="reserve.meta_data.start_date" type="text" readonly>
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
                              <div class="col-md-6" v-if="reserve.attached_file">
                                <div class="form-group">
                                  <a target="_blank" type="button" class="btn btn-primary btn-upload" :href="reserve.attached_file | genDownloadUrl" download><i class="fa fa-download"></i>&nbsp; Tải về file đính kèm</a>
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
            </div>
          </b-card>
        </div>
      </div>
      <div slot="modal-footer">
        <div class="row">
          <div class="col-md-12">
            <div class="buttons">
              <button class="apax-btn full edit" @click="confirmApprove(reserve)" :disabled="button_disabled"><i class="fa fa-paper-plane"></i> Duyệt</button>
              <button class="apax-btn full remove" @click="confirmDeny(reserve)" :disabled="button_disabled"><i class="fa fa-ban"></i> Từ chối</button>
              <button class="apax-btn full" @click="closeDetailModal()"><i class="fa fa-sign-out"></i> Thoát</button>
            </div>
          </div>
        </div>
      </div>
    </b-modal>
  </div>

</template>

<script>
  import u from '../../../utilities/utility'
  import SearchBranch from '../../../components/SearchBranchForTransfer'
  import paging from '../../../components/Pagination'
  import search from '../../../components/Search'
  import loader from '../../../components/Loading'

  export default {
    name: 'List-Reserve',
    components: {
      SearchBranch,
      paging,
      search,
      loader
    },
    data() {
      return {
        filter: {
          lms_effect_id: '',
          branch_id: '0',
          is_extended: '-1',
          is_reserved: '-1'
        },
        branches: [],
        branch: '',
        reserves: [],
        reserve: {
          meta_data: {}
        },
        button_disabled: true,
        search_id: 'contract_search',
        search_name: 'search-contract',
        search_label: 'Tìm kiếm theo mã LMS, Tên học sinh hoặc Tên tiếng Anh',
        search_placeholder: 'Từ khóa tìm kiếm',
        disableSearch: true,
        placeholderSearchBranch: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước.',
        html: {
          modal: {
            show: false,
            message: ''
          },
          detail_modal: {
            show: false,
            message: ''
          },
          approve_modal: {
            show: false,
            message: ''
          },
          deny_modal: {
            show: false,
            message:'',
            comment: '',
            validReason: ''
          },
          loader: {
            spin: 'mini',
            duration: 500,
            processing: false,
            text: 'Đang xử lý dữ liệu...'
          },
          filter: {
            branch: {
              id: 'search_branch',
              placeholder: 'Tìm kiếm trung tâm',
            },
            lms_effect: {
              disabled: false,
            },
            is_reserved: {
              disabled: false,
            },
            is_extended: {
              disabled: false
            }
          }
        },
        pagination: {
          url: '',
          id: '',
          style: 'line',
          class: '',
          spage: 1,
          ppage: 1,
          npage: 1,
          lpage: 1,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: []
        },
        flags: {
          form_loading: false,
          requesting: false
        }
      }
    },
    created() {
      this.html.loader.processing = true;
      u.a().get('/api/branches').then(response => {
        this.branches = response.data;
      })
      this.getReserves()
    },
    methods: {
      prepareSearch() {

      },
      selectBranch(data) {
        this.filter.branch_id = parseInt(data.id);
      },
      closeModal(){
        this.html.modal.show = false;
      },
      getReserves(page_url) {
        let data = JSON.stringify(this.filter);
        let pagination = JSON.stringify(this.pagination);
        page_url = page_url || `/api/reserves-info/requests?filter=${data}&pagination=${pagination}`;
        u.a().get(page_url)
          .then(response => {
            this.html.loader.processing = false;

            if(response.data.code == 200){
              this.reserves = response.data.data.items;
              this.pagination = response.data.data.pagination
            }else{
              this.html.modal.message = "<span class='text-danger'>"+response.data.message+"</span>";
              this.html.modal.show = true;
            }
          }).catch(e => {
          this.html.loader.processing = false;
        });
      },
      showReserve(reserve){
        this.reserve = reserve;
        this.button_disabled = false;

        if((typeof reserve.meta_data) == 'string'){
          this.reserve.meta_data = JSON.parse(reserve.meta_data);
        }
        this.html.detail_modal.show = true
      },
      approve() {
        if(this.html.loader.processing === false){
          this.html.detail_modal.show = false
          this.html.loader.processing = true;
          let reserve_id = this.reserve.id;
          let uri = '/api/reserves/'+reserve_id+'/final-approve';
          u.put(uri, null, false, true).then((response) => {
            this.button_disabled = true;
            this.html.loader.processing = false;
            this.html.approve_modal.show = false;
            this.html.deny_modal.show = false;
            if(response.code == 200){
              let message = "<span class='text-success'>Thành công</span>";
              this.showMessage(message);

              this.filterData();
            }else{
              let message = "<span class='text-danger'>"+response.message+"</span>";
              this.showMessage(message);
            }
          });
        }
      },
      deny() {
        if(this.html.loader.processing === false) {
          this.html.loader.processing = true;
          let cm = this.html.deny_modal.comment;
          let reserve_id = this.reserve.id;
          if(cm){
            let uri = '/api/reserves/'+reserve_id+'/deny';
            u.a().put(uri, {comment: cm}).then((response) => {
              this.button_disabled = true;
              this.html.loader.processing = false;
              this.html.approve_modal.show = false;
              this.html.deny_modal.show = false;
              if(response.data.code == 200){
                let message = "<span class='text-success'>Thành công</span>";
                this.showMessage(message);
                this.filterData();
              }else{
                let message = "<span class='text-danger'>"+response.data.message+"</span>";
                this.showMessage(message);
              }
            });
          }else{
            this.button_disabled = true;
            this.html.loader.processing = false;
            this.html.deny_modal.show = true;
            this.html.deny_modal.validReason = 'display';
          }
        }
      },
      closeDenyModal(){
        this.html.deny_modal.show = false;
      },
      showMessage(message){
        this.html.modal.message = message;
        this.html.modal.show = true;
      },
      confirmApprove(reserve){
        this.reserve = reserve;
        this.html.approve_modal.show = true;
      },
      confirmDeny(reserve){
        this.reserve = reserve;
        this.html.deny_modal.comment = '';
        this.html.deny_modal.show = true;
      },
      filterData(type = 0) {
        if (this.html.loader.processing === false) {
          this.html.loader.processing = true;
          if (type) {
            this.resetPagination();
          }
          let data = JSON.stringify(this.filter);
          let pagination = JSON.stringify(this.pagination);
          u.a().get(`/api/reserves-info/requests?filter=${data}&pagination=${pagination}`)
            .then(response => {
              this.html.loader.processing = false
              this.reserves = response.data.data.items
              this.pagination = response.data.data.pagination
            });
        }
      },
      removeFilter() {
        this.filter = {
          lms_effect_id: '',
          branch_id: '0',
          is_reserved: '-1',
          is_extended: '-1',
        };
        $("#search_branch").val('');
        this.filterData(1);
      },
      redirect(link) {
        const info = link.toString().split('/');
        const page = info.length > 1 ? info[1] : 1;
        this.pagination.cpage = parseInt(page);
        this.filterData();
      },
      resetPagination() {
        this.pagination = {
          url: '',
          id: '',
          style: 'line',
          class: '',
          spage: 1,
          ppage: 1,
          npage: 1,
          lpage: 1,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: []
        }
      },
      closeDetailModal() {
        this.html.detail_modal.show = false
      },
      validKey(){
        this.filter.lms_effect_id = u.uniless(this.filter.lms_effect_id)
        this.filter.lms_effect_id = this.filter.lms_effect_id.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '')
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
  .buttons button:disabled{
    cursor: not-allowed;
  }
</style>
