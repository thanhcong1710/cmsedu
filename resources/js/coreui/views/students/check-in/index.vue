<template>
  <div class="animated fadeIn apax-form" @keyup="binding" id="students-management">
    <div class="row">
      <div class="col-12">
        <b-card header>
          <loader :spin="spin" :text="text" :active="processing" :duration="duration"/>
          <div slot="header">
            <i class="fa fa-filter"></i>
            <b class="uppercase">Bộ lọc</b>
          </div>
          <div id="students-list">
            <div class="row">
              <div class="col-lg-12" v-show="html.dom.show.basic">
                <div class="row">
                 <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <br />
                      <suggestion
                        class="select-branch"
                        :onSelect="html.dom.filter.branch.action"
                        :disabled="html.dom.filter.branch.disabled"
                        :placeholder="html.dom.filter.branch.placeholder"
                      ></suggestion>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Từ khóa</label>
                      <br />
                        <input
                          id="input_keyword"
                          name="search[keyword]"
                          class="form-control"
                          v-model="html.data.filter.keyword"
                          :placeholder="html.dom.filter.search.placeholder"
                          @input="validate_keyword()"
                        >
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <br />
                        <select  class="form-control" v-model="html.data.filter.status">
                          <option value="-1"> Chọn  trạng thái </option>
                          <option value="0"> Chờ duyệt đi</option>
                          <option value="1"> Chờ duyệt đến</option>
                          <option value="2"> Đã chuyển TT</option>
                          <option value="3"> TT chuyển từ chối</option>
                          <option value="4"> TT nhận từ chối</option>
                          <option value="5"> Đã checkin</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Nguồn</label>
                      <br />
                      <select class="form-control" v-model=" html.data.filter.source" name="source">
                        <option value="">Chọn nguồn từ</option>
                        <option :value="source.id" v-for="(source, i) in html.data.sources_list" :key="i">{{ source.name }}</option>
                      </select>
                    </div>
                  </div>
                   <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Thời gian tạo</label>
                      <br />
                       <date-picker
                        style="width:100%;"
                        v-model="html.data.filter.dateRange"
                        :clearable="true"
                        range
                        format="YYYY-MM-DD"
                        id="apax-date-range"
                        placeholder="Chọn thời gian tìm kiếm từ ngày đến ngày"
                        ></date-picker>
                      </div>
                    </div>
                    <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Thời gian checkin</label>
                      <br />
                       <date-picker
                        style="width:100%;"
                        v-model=" html.data.filter.dateRangeCheckin"
                        :clearable="true"
                        range
                        format="YYYY-MM-DD"
                        id="apax-date-range"
                        placeholder="Chọn thời gian tìm kiếm từ ngày đến ngày"
                        ></date-picker>
                      </div>
                    </div>
                    <div class="col-sm-3" v-if="['999999999',80,81].indexOf(session.user.role_id) != -1">
                    <div class="form-group">
                      <label class="control-label">Người tạo</label>
                      <br />
                        <select class="form-control" v-model="html.data.filter.keyword_creator">
                          <option value="">Chọn người tạo</option>
                          <option :value="creat.id" v-for="(creat, i) in html.data.creator_list" :key="i">{{creat.label}}</option>
                        </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div slot="footer" class="text-center">
            <div class="text-center">
              <!-- <router-link to="/student-checkin/add">
                <button type="button" class="apax-btn full error" v-if="['19','20', '21', '22', '23'].indexOf(session.user.branch_id) != -1">
                  <i class="fa fa-plus"></i> Thêm mới
                </button>
              </router-link> -->
              <button @click="html.dom.filter.submitSearch.action" class="apax-btn full edit">
                <i class="fa fa-filter" aria-hidden="true"></i> Lọc
              </button>
              <button @click="html.dom.filter.clearSearch.action" class="apax-btn full default"><i class="fa fa-slack"></i> Bỏ lọc</button>
              <button v-on:click="changeContent()" class="apax-btn full" :class="checked ? 'detail' : 'print'"><i class="fa" :class="checked ? 'fa-list' : 'fa-list-ul'"></i> {{ list_title }}</button>
              <button v-if="(['999999999',686868,81,7676767,676767].indexOf(session.user.role_id) != -1 && html.data.filter.status==5) || session.user.role_id=='999999999' || session.user.id=='140'" @click="extract()" class="apax-btn full edit"><i class="fa fa-file-word-o"></i> Trích xuất</button>
              <!-- <router-link to="/student-checkin/import" >
                  <button class="apax-btn full detail"><i class="fa fa-upload"></i> Import</button>
              </router-link> -->
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <div class="row drag-me-up">
      <div class="col-12">
        <b-card header>
          <div slot="header">
            <i class="fa fa-address-book"></i>
            <strong>Danh sách Checkin</strong>
          </div>
          <loader :spin="spin" :text="text" :active="processing" :duration="duration"/>
          <div class="table-responsive scrollable">
            <table
              id="apax-printing-students-list"
              class="table table-striped table-bordered apax-table"
            >
              <thead>
              <tr>
                <th>STT</th>
                <th>Tên học sinh</th>
                <th>Mã CRM</th>
                <!-- <th>Trải nghiệm</th> -->
                <th>Giới tính</th>
                <th>Trường</th>
                <th>Nguồn</th>
                <th>Ngày sinh</th>
                <th>Họ tên bố mẹ</th>
                <th>Điện thoại phụ huynh</th>
                <th>Địa chỉ</th>
                <th>EC Tư Vấn</th>
                <th>CS</th>
                <th>Trung tâm</th>
                <th>Trung tâm chuyển đến</th>
                <th>Thời điểm</th>
                <th>Ngày/Giờ checkin</th>
                <th>Sản phẩm</th>
                <th>Người tạo</th>
                <th>Đã checkin</th>
                <th>Trạng thái</th>
                <th>{{ action_title }}</th>
                <th :class="vshow()">Phê duyệt</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(item, index) in list" v-bind:key="index" :class="vshowRow(item)">
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{(html.pagination.ppage * html.pagination.limit) + 1 + index}}</router-link>
                </td>
                <td class="text-center">
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.crm_id}}</router-link>
                </td>
                <!-- <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.shift_id  > 0 ? 'Đã học thử':'Chưa học thử'}}</router-link>
                </td> -->
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  ><span v-if="item.gender != null">{{item.gender | genderToName}}</span><span v-else></span></router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.school}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.source_name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.date_of_birth}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.gud_name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.gud_mobile1}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.address}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.ec_name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.cm_name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.branch_name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.to_branch_name}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.created_at}}</router-link>
                </td>
                <td>
                  <router-link
                    v-b-tooltip.hover
                    class="link-me"
                    :title="`${html.page.title}`"
                    :to="`student-checkin/${item.id}/detail`"
                  >{{item.checkin_at !='0000-00-00 00:00:00' ? item.checkin_at : ''}}</router-link>
                </td>
                <td>
                  <span>{{item.type_product==2 ? 'Accelium' : 'CMS'}}</span>
                </td>
                <td class="text-center">
                  <span>{{item.creator_name}}</span>
                </td>
                <td class="text-center">
                  <input type="checkbox" v-model="item.checked" :disabled="item.checked==1" @change="checkedStudent(item)">
                </td>
                <td class="text-center">
                  <span v-html="vshowStatus(item)"></span>
                </td>
                <td class="text-center">
                     <span class="apax-btn detail" :class="vshowCol()" >
                      <router-link v-b-tooltip.hover class="link-me" title="Nhấp vào để xem thông tin" :to="`/student-checkin/${item.id}/detail`"><i class="fa fa-eye"></i></router-link>
                    </span>
                  <span class="apax-btn edit" :class="vshowEdit(item.creator_id)">
                      <router-link v-b-tooltip.hover class="link-me" title="Nhấp vào để sửa thông tin" :to="`/student-checkin/${item.id}/edit`"><i class="fa fa-pencil"></i></router-link>
                    </span>
                  <span class="apax-btn remove" @click="remove(item)" v-if="session.user.role_id == '999999999' && item.status==0">
                          <i v-b-tooltip.hover title="Nhấp vào để xóa bỏ" class="fa fa-remove"></i>
                        </span>
                  <button class="apax-btn error" @click="vshowTransfer(item.name,item.id,index,item.branch_id,item)" :class="onlyCSLeader()" title="Chuyển trung tâm checkin">
                    <i class="fa fa-exchange"></i>
                  </button>
                  <button v-if="item.checked==1" class="apax-btn print" @click="vshowConvertAction(item.id,index,item.branch_id)" :class="vshowConvert()" title="Chuyển học sinh từ checkin sang học sinh">
                    <i class="fa fa-paper-plane"></i>
                  </button>
                  <span>{{item.cm_name_active}}</span>
                </td>
                <td class="text-center" :class="vshow()">
                  <button class="apax-btn print" @click="modalApprove(item,index)" :class="vshowApprover(item)" title="Duyệt chuyển trung tâm checkin ">
                    <i class="fa fa-paper-plane"></i>
                  </button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="text-center">
            <nav aria-label="Page navigation">
              <paging
                :rootLink="html.pagination.url"
                :id="html.pagination.id"
                :listStyle="html.pagination.style"
                :customClass="html.pagination.class"
                :firstPage="html.pagination.spage"
                :previousPage="html.pagination.ppage"
                :nextPage="html.pagination.npage"
                :lastPage="html.pagination.lpage"
                :currentPage="html.pagination.cpage"
                :pagesItems="html.pagination.total"
                :pagesLimit="html.pagination.limit"
                :pageList="html.pagination.pages"
                :routing="redirect"
              ></paging>
            </nav>
          </div>
        </b-card>
      </div>
    </div>
    <b-modal title="DUYỆT CHUYỂN TRUNG TÂM" class="modal-primary" size="sm" v-model="html.dom.modal_approve.display" ok-title="Đồng ý" @ok="action(1)" cancel-variant="primary" ok-variant="success" @cancel="action(2)" cancel-title="Từ chối" >
      <div v-html="html.dom.modal_approve.content">
      </div>
    </b-modal>
    <b-modal title="Thông báo" class="modal-primary" size="sm" v-model="html.dom.modal_approve.show" :hide-footer="true">
      <div v-html="html.dom.modal_approve.message">
      </div>
    </b-modal>
    <b-modal title="CHUYỂN TRUNG TÂM" :cancel-disabled="!html.data.ecNew.item || !html.data.csNew.item" class="modal-primary" size="large" v-model="html.dom.modal_transfer.display" @cancel="actionTransfer(1)" cancel-title="Xác nhận" ok-title="Bỏ qua" @ok="actionTransfer(2)" ok-variant="primary">
      <div class="form-group"><label>HS: <b>{{transfer_student_name.toUpperCase()}}</b></label></div>
      <div class="form-group">
        <select class="form-control" v-model="transfer_branch_id" @change="getUsersByBranch()">
          <option value="">Chọn trung tâm</option>
          <option v-show="branch.id != transfer_from_branch_id" :value="branch.id" v-for="(branch, i) in branch_list" :key="i">{{ branch.name }}</option>
        </select>
      </div>
      <div class="form-group">
        <label class="control-label">EC</label>
        <vue-select
          @input="quyen()"
          label="ec_name"
          :options="html.data.ecNew.list"
          :searchable="true"
          language="tv-VN"
          placeholder="Vui lòng chọn EC"
          :disabled="html.dom.ecNew.disabled"
          v-model="html.data.ecNew.item"
        >
        </vue-select>
      </div>
      <div class="form-group">
        <label class="control-label">CS</label>
        <vue-select
          label="cm_name"
          :options="html.data.csNew.list"
          :searchable="true"
          language="tv-VN"
          placeholder="Vui lòng chọn CS"
          :disabled="html.dom.csNew.disabled"
          v-model="html.data.csNew.item"
        >
        </vue-select>
      </div>
    </b-modal>
  </div>
</template>

<script>
  import suggestion from "../../components/Selection";
  import loader from "../../../components/Loading";
  import paging from "../../components/Pagination";
  import search from "../../components/Search";
  import u from "../../../utilities/utility";
  import DatePicker from '../../../components/DatePicker'
  import Multiselect from "vue-multiselect";
  import axios from "axios";
  import select from "vue-select";
  import saveAs from "file-saver";
  export default {
    components: {
      suggestion,
      loader,
      paging,
      search,
      saveAs,
      DatePicker,
      Multiselect,
      "vue-select": select,
      axios
    },

    data() {
      var current = new Date();
      const model = u.m("checkin").list
      model.transfer_student_name =""
      model.spin = "mini"
      model.transfer_branch_id = ""
      model.transfer_student_id = ""
      model.transfer_index_id = ""
      model.transfer_from_branch_id = ""
      model.transfer_from_ec_id = ""
      model.transfer_from_cm_id = ""
      model.checked = false
      model.act = null
      model.list_title = "Đã Active "
      model.action_title = "Thao tác"
      model.duration = 300
      model.text = "Đang tải dữ liệu..."
      model.processing = false
      model.report_name = "Danh Sach Hoc Sinh - "
      model.router_detail ="Xem thông Tin Học Sinh Checkin"
      model.html.dom = {
        csNew: {
          display: 'hidden',
          disabled: true,
        },
        ecNew: {
          display: 'hidden',
          disabled: true,
        },
        full: "hidden",
        filter: {
          branch: {
            options: [],
            disabled: true,
            display: "hidden",
            action: branch => this.selectBranch(branch),
            placeholder: "Tất cả",
          },
          search: {
            disabled: true,
            action: keyword => this.searching(keyword),
            placeholder: "Từ khóa tìm kiếm"
          },
          ec: {
            data: [],
            disabled: true,
            action: () => this.selectEC(),
            placeholder: "Lọc theo EC"
          },
          cm: {
            data: [],
            disabled: true,
            action: () => this.selectCM(),
            placeholder: "Lọc theo CM"
          },
          gender: {
            disabled: true,
            action: () => this.selectGender(),
            placeholder: "Lọc theo giới tính"
          },
          customer_type: {
            disabled: true,
            action: () => this.selectCustomerType(),
            placeholder: "Lọc theo loại khách hàng"
          },
          student_status: {
            disabled: true,
            action: () => this.selectStudentStatus(),
            placeholder: "Lọc theo trạng thái"
          },
          field: {
            disabled: true,
            action: () => this.selectField(),
            placeholder: "Tìm theo dữ liệu"
          },
          source: {
            disabled: true,
            action: () => this.selectSource(),
            placeholder: "Lọc theo nguồn từ"
          },
          product: {
            disabled: true,
            action: product => this.selectProduct(product),
            placeholder: "Lọc theo sản phẩm"
          },
          program: {
            disabled: true,
            action: () => this.selectProgram(),
            data: [],
            placeholder: "Lọc theo sản phẩm"
          },
          tuition_fee: {
            disabled: true,
            action: () => this.selectTuitionFee(),
            data: [],
            placeholder: "Lọc theo gói học phí"
          },
          checkin: {
            action: () => this.checkinAction()
          },
          renew: {
            action: () => this.renewAction()
          },
          type: {
            action: () => this.typeAction()
          },
          status: {
            action: () => this.statusAction()
          },
          submitSearch: {
            action: () => this.searchAction1()
          },
          zone: {
            disabled: true,
            action: () => this.selectZone(),
            placeholder: "Xin vui chọn khu vực",
            multiple: true
          },
          region: {
            disabled: true,
            action: () => this.selectRegion(),
            placeholder: "Xin vui chọn vùng",
            multiple: true
          },
          datechange: {
            action: () => this.dateChange()
          },
          district: {
            disabled: false,
            item: "",
            list: []
          },
          province: {
            disabled: false,
            item: "",
            list: []
          },
          showAdvance: {
            action: () => this.showAdvance()
          },
          showBasic: {
            action: () => this.showBasic()
          },
          clearSearch: {
            action: () => this.clearSearch()
          }
        },
        selectmultiple: {
          value: "",
          options: [
            { name: "Vùng 1", value: 1 },
            { name: "Vùng 2", value: 2 },
            { name: "Vùng 3", value: 3 },
            { name: "Vùng 4", value: 4 },
            { name: "Vùng 5", value: 5 },
            { name: "Vùng 6", value: 6 },
            { name: "Vùng 7", value: 7 },
            { name: "Vùng 8", value: 8 },
            { name: "HCM - Vùng 1", value: 9 },
            { name: "HCM - Vùng 2", value: 10 },
            { name: "HCM - Vùng 3", value: 11 }
          ],
          methods: {
            customLabel(option) {
              return `${option.library} - ${option.language}`;
            }
          }
        },
        show: {
          basic: true,
          advance: false,
          buttonAdvance: true,
          buttonBasic: false,
          product_column: false,
          program_column: false
        },
        modal_transfer:{
          display: false,
          content:'',
          item:0,
          show:false,
          message:'',
          from_to:'',
        },
        modal_approve:{
          display: false,
          content:'',
          item:0,
          show:false,
          message:'',
          from_to:'',
        },
        currentIndex:'',
      };
      model.html.data = {
        ecNew: {
          item: '',
          list: [],
          model:'',
        },
        csNew: {
          item: '',
          list: [],
          model:'',
        },
        creator_list:[],
        sources_list:[],
        filter: {
          dateRange: '',
          dateRangeCheckin: '',
          startDate:'',
          startDateCheckin:'',
          endDate:'',
          endDateCheckin:'',
          // ec: "",
          // cm: "",
          // field: "",
          branch: "",
          keyword: "",
          keyword_creator:"",
          source: "",
          // gender: "",
          // product: "",
          // program: "",
          // tuition_fee: "",
          // customer_type: "",
          // student_status: "",
          // renew: "",
          // zone: "",
          // region: "",
          // checkin: "",
          // date_range: "",
          // status: -1,
          // type: "",
          // district_id: "",
          // province: "",
          // province_id: ""
        },
        datepicker: {
          value: "",
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
            pickers: ["", "", "7 ngày trước", "30 ngày trước"],
            placeholder: {
              date: "Select Date",
              dateRange: "Select Date Range"
            }
          }
        }
      };
      model.branch_list = []
      model.html.order.by = "s.id";
      model.cache.filter = model.html.data.filter;
      model.session = u.session();
      return model;
    },

    created() {
      u.g(`/api/sources`)
        .then(response => {
          this.html.data.sources_list = response
        })
      u.g(`/api/get_all_user_by_role?roles=80,81`)
        .then(response => {
          this.html.data.creator_list = response
        })
      this.start();
    },

    computed: {},

    methods: {
      onlyCSLeader(){
        if (this.checked) return 'hidden'
        if (u.session().user.role_id == 56 || u.session().user.role_id == 55  || u.session().user.role_id == 999999999 || u.session().user.role_id == 69 || u.session().user.role_id == 99)
          return ''
        else
          return 'hidden'
      },
      vshowCol(){
        if (this.checked) return 'hidden'
      },
      vshowEdit(creator_id){
        if (this.checked) return 'hidden'
        // if ((u.session().user.role_id == 80 || u.session().user.role_id == 81) && creator_id != u.session().user.id){
        //   return 'hidden'
        // }
        let roles = [55,56,999999999,68,69,99,81,80]
        let uid  = parseInt(u.session().user.role_id)
        if (roles.includes(uid))
        {
          return ''
        }
        else{
          return 'hidden'
        }
      },
      vshowRow(item){
        if (item.status == 0 || this.checked || item.status_transfer == 0)
          return ''
        else
          return 'hidden'
      },
      vshow(){
        if (u.session().user.role_id == 686868 || u.session().user.role_id == 676767 || u.session().user.role_id == 99 || u.session().user.role_id == 999999999){
          return ''
        }
        else{
          return 'hidden'
        }
      },
      reloadPage(){
        this.get(this.link(), this.load);
      },
      vshowTransfer(name,id,i,branch_id,student){
        // if (shift_id >0){
        //   alert('Học sinh đã học thử nên không thể chuyển trung tâm.')
        //   this.reloadPage()
        //   return false
        // }
        this.resetModalTransfer()
        this.transfer_student_id = id
        this.transfer_index_id = i
        this.transfer_from_branch_id = branch_id
        this.transfer_from_ec_id = student.ec_id
        this.transfer_from_cm_id = student.cm_id
        this.transfer_student_name = name
        this.processing = true;
        u.g(`/api/checkin/branch-transfer/${id}/${this.transfer_from_branch_id}`, null).then(response => {
          this.processing = false;
          if (parseInt(response.status) == 99){
            this.html.dom.modal_transfer.display = true
          }
          else{
            this.html.dom.modal_approve.show = true
            this.html.dom.modal_approve.message = response.success
          }
        })
      },
      vshowConvertAction(id,i,branch_id){
        u.g(`/api/checkin/branch-transfer/${id}/${branch_id}`, null).then(response => {
            if (parseInt(response.status) != 99 && parseInt(response.status) != 2){
               alert("Học sinh đang chờ duyệt chuyển trung tâm, không thể chuyển đổi học sinh")
              this.reloadPage()
              return false
            }else if(parseInt(response.status) == 99 && parseInt(response.validate_info) == 0){
              alert("Thiếu thông tin học sinh  vui lòng cập nhật trước khi chuyển đổi từ checkin sang học sinh");
              u.go(this.$router, `/student-checkin/${id}/edit`)
            }else{
              // let that = this
              let popup = confirm("Bạn có chắc muốn chuyển đổi từ checkin sang học sinh không?");
              if (popup === true) {
                u.p(`/api/checkin/convert-student/${id}`, null).then(response => {
                  if (response.success) {
                    // this.html.dom.modal_approve.show = true
                    // this.html.dom.modal_approve.message = response.success
                    // that.list[i].status = 2
                    alert("Chuyển danh sách học sinh thành công")
                    this.reloadPage()
                    return false
                  }
                })
              }
            }
        });
      },
      vshowConvert(){
        if (this.checked) return 'hidden'
        if (u.session().user.role_id == 56 || u.session().user.role_id == 55 || u.session().user.role_id == 999999999){
          return ''
        }
        else{
          return 'hidden'
        }
      },
      vshowApprover(item){
        if (this.checked) return 'hidden'
       let userId = u.session().user.id;
        // if (item.from_approver_id == userId || item.to_approver_id == userId){
        //    return 'hidden'
        // }
        // else{
            if ((item.status_transfer == 0 || item.status_transfer == 1) && (((u.session().user.role_id == 686868 || u.session().user.role_id == 676767 || u.session().user.role_id == 99) && u.session().user.branch_id.split(',').indexOf(item.to_branch_id.toString())>-1 && item.status_transfer == 1) || ((u.session().user.role_id == 686868 || u.session().user.role_id == 676767|| u.session().user.role_id == 99)&& u.session().user.branch_id.split(',').indexOf(item.from_branch_id.toString())>-1 && item.status_transfer == 0)  || u.session().user.role_id == 999999999 || u.session().user.role_id == 7777777 ))
              return ''
            else{
              return 'hidden'
            }
        // }
      },
      vshowStatus(item){
        if (this.checked) return ''
       if (item.status_transfer == 0)
         return 'CHỜ DUYỆT ĐI'
        else if (item.status_transfer == 1)
         return 'CHỜ DUYỆT ĐẾN'
       else if (item.status_transfer == 2)
         return 'ĐÃ CHUYỂN TT'
       else if (item.status_transfer == 3)
         return 'TT CHUYỂN TỪ CHỐI'
       else if (item.status_transfer == 4)
         return 'TT NHẬN TỪ CHÔI'
      },
      modalApprove(item,i){
          this.html.dom.modal_approve.content = `${item.modal}<br/> <br/>`
          this.html.dom.modal_approve.display = true
          this.html.dom.modal_approve.item = item.id
          this.html.dom.currentIndex = i
        if (item.status_transfer === 0)
          this.html.dom.modal_approve.from_to = 'from'
        else if(item.status_transfer == 1)
          this.html.dom.modal_approve.from_to = 'to'

      },
      resetModalTransfer(){
        this.transfer_student_name = null
        this.transfer_student_id = null
        this.transfer_index_id = null
        this.transfer_from_branch_id = null
        this.html.data.ecNew.item = null
        this.html.data.csNew.item = null
      },
      actionTransfer(status){
        if (status == 2){
          this.html.dom.modal_transfer.display = false
        }
        else{
          let params = {
            'std_id':this.transfer_student_id,
            'status':status,
            'from':this.transfer_from_branch_id,
            'to':this.transfer_branch_id,
            'ec_id':this.html.data.ecNew.item.ec_id,
            'cm_id':this.html.data.csNew.item.cm_id,
            'from_ec_id':this.transfer_from_ec_id,
            'from_cm_id':this.transfer_from_cm_id,
          }
          //this.processing = true;
          u.p(`/api/checkin/branch-transfer`, params).then(response => {
            //this.processing = false;
            // this.html.dom.modal_approve.show = true
            // this.html.dom.modal_approve.message = response.success
            alert(response.success)
            this.reloadPage()
          })
        }
      },
      action(status){
        let params = {'status':status,'f':this.html.dom.modal_approve.from_to}
        //let that = this
        u.put(`/api/checkin/${this.html.dom.modal_approve.item}/approve`, params).then(response => {
          if (response.success) {
            alert(response.success)
            this.reloadPage()
            // this.html.dom.modal_approve.show = true
            // this.html.dom.modal_approve.message = response.success
            // that.list[that.html.dom.currentIndex].from_approver_id = response.from_approver_id
            // that.list[that.html.dom.currentIndex].to_approver_id = response.to_approver_id
            // let item = response.item
            // if(item){
            //   that.list[that.html.dom.currentIndex].cm_name = item.tmp_cm_name
            //   that.list[that.html.dom.currentIndex].ec_name = item.tmp_ec_name
            //   that.list[that.html.dom.currentIndex].branch_name = item.tmp_branch_name
            // }

          }
        })
      },
      canAction() {
        let arr_roles = [55, 56,68, 69,99, 80,81, 686868, 7777777, 999999999];
        return arr_roles.indexOf(parseInt(u.session().user.role_id)) == -1 ? false : true;
      },
      start() {
        this.processing = true;
        this.html.dom.filter.gender.disabled = false;
        this.html.dom.filter.search.disabled = false;
        this.html.dom.filter.field.disabled = false;
        this.html.dom.filter.source.disabled = false;
        this.html.dom.filter.product.disabled = false;
        this.html.dom.filter.customer_type.disabled = false;
        this.html.dom.filter.student_status.disabled = false;
        if (u.authorized()) {
          this.html.dom.filter.branch.disabled = false;
        }
        else{
          this.html.dom.filter.branch.disabled = false;
          // this.cache.filter.branch = u.session().user.branches[0].id;
          // this.html.data.filter.branch = u.session().user.branches[0];
          this.html.dom.filter.branch.placeholder = u.session().user.branches[0].name;
        }

        this.html.pagination = {
          spage: 0,
          ppage: 0,
          npage: 0,
          lpage: 0,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: []
        };
        this.searching();
        this.checkPermission();
        this.getAllBranch();
      },
      activeProfile(obj) {
        const delStdConf = confirm("Bạn có chắc rằng muốn chuyển học sinh này từ danh sách checkin  sang danh sách học sinh không?");
        if (delStdConf === true) {
          this.processing = true;
          u.g(`/api/checkin/active-profile/${obj.id}/${obj.branch_id}/${obj.ec_id}`).then(response => {
            alert('Chuyển sang danh sách học sinh thành công')
            this.searching()
          });
        }
      },
      changeContent() {
        this.list = []
        this.processing = true;
        if (this.checked) {
          this.list_title = "Chưa Active"
          this.action_title = "Thao tác"
          this.router_detail ="Xem thông Tin Học Sinh Checkin"
        } else {
          this.list_title = "Đã Active"
          this.action_title = "Người Active"
          this.router_detail ="Hồ Sơ Học Sinh"
        }
        this.checked = !this.checked
        this.searching()
      },
      checkPermission() {
        let user_role_id = u.session().user.role_id;
        let list_role = u.r;
        this.html.data.filter.branch = "";
        let branches = u.session().user.branches;
        this.html.dom.filter.zone.disabled = true;
        this.html.dom.filter.region.disabled = true;
        u.g(`/api/provinces`).then(response => {
          this.html.dom.filter.province.list = response;
        });
        // if (user_role_id >= list_role.zone_ceo) {
        //     this.html.dom.filter.zone.disabled = false
        // }
        if (user_role_id >= list_role.region_ceo) {
          this.html.dom.filter.region.disabled = false;
          this.html.dom.filter.zone.disabled = false;
        }
        if (user_role_id >= list_role.ec_leader) {
          // this.cache.filter.branch = branches[0].id
          this.loadECCMS();
        }
        if (branches.length == 1) {
          this.html.dom.filter.branch.placeholder = branches[0].name;
          this.html.dom.full = "";
          this.html.data.filter.branch = this.session.user.branch_id;
          this.cache.filter.branch = this.session.user.branch_id;
          u.g(`${this.html.page.url.load}${this.session.user.branch_id}`)
            .then(response => {
              this.html.dom.filter.ec.disabled = false;
              this.html.dom.filter.cm.disabled = false;
              this.html.dom.filter.tuition_fee.disabled = false;
            })
            .catch(e => console.log(e));
        } else {
          this.html.dom.filter.branch.disabled = false;
        }
        if (user_role_id == list_role.om || user_role_id == list_role.cm) {
          this.loadECCMS();
        }
        if (user_role_id >= list_role.branch_ceo) {
          this.html.dom.filter.branch.disabled = false;
        }
      },
      link(root) {
        let branches = u.session().user.branches;
        if (branches.length == 1) {
          this.cache.filter.branch = this.session.user.branch_id;
        }
        this.html.data.filter.branch = this.cache.filter.branch;
        const sort = u.jss(this.html.order);
        const search = u.jss(this.html.data.filter);
        const pagination = u.jss({
          spage: this.html.pagination.spage,
          ppage: this.html.pagination.ppage,
          npage: this.html.pagination.npage,
          lpage: this.html.pagination.lpage,
          cpage: this.html.pagination.cpage,
          total: this.html.pagination.total,
          limit: this.html.pagination.limit
        });
        console.log(search);
        console.log(pagination);
        console.log(sort);
        return this.checked
          ? `${this.html.page.url.list}${pagination}/${search}/${sort}?act=checked`
          : `${this.html.page.url.list}${pagination}/${search}/${sort}`
      },
      get(link, callback) {
        this.processing = true;
        u.g(link)
          .then(response => {
            const data = response;
            callback(data);
          })
          .catch(e => {});
      },
      load(data) {
        if (data){
          this.act = data.act
          this.cache.data = data;
          this.list = data.list;
          this.html.pagination = data.pagination;
        }

        this.processing = false;
      },
      searching(word = "") {
        const key =
          u.live(word) && word != "" ? word : this.html.data.filter.keyword;
        this.cache.filter.keyword = key ? key : "";
        this.html.data.filter.keyword = key ? key : "";
        this.searchAction();
      },
      selectGender() {
        10;
        if (this.cache) {
          this.cache.filter.gender = this.html.data.filter.gender;
          this.get(this.link('/api/checkin'), this.load);
        }
      },
      selectCustomerType() {
        if (this.cache) {
          this.cache.filter.customer_type = this.html.data.filter.customer_type;
          this.get(this.link(), this.load);
        }
      },
      selectStudentStatus() {
        if (this.cache) {
          this.cache.filter.student_status = this.html.data.filter.student_status;
          this.get(this.link(), this.load);
        }
      },
      selectField() {
        if (this.cache) {
          this.cache.filter.field = this.html.data.filter.field;
          this.get(this.link(), this.load);
        }
      },
      selectSource() {
        if (this.cache) {
          this.cache.filter.source = this.html.data.filter.source;
          this.get(this.link(), this.load);
        }
      },
      selectEC() {
        if (this.cache) {
          this.cache.filter.ec = this.html.data.filter.ec;
          this.get(this.link(), this.load);
        }
      },
      selectCM() {
        if (this.cache) {
          this.cache.filter.cm = this.html.data.filter.cm;
          this.get(this.link(), this.load);
        }
      },
      binding(e) {
        if (e.key == "Enter") {
          this.searching();
        }
      },
      redirect(link) {
        const info = link.toString().split("/");
        const page = info.length > 1 ? info[1] : 1;
        this.html.pagination.cpage = parseInt(page);
        this.get(this.link(), this.load);
      },
      extract() {
        let filename = this.report_name;
        const arr_branches = u.session().user.arr_branches;
        for (let i = 0; i < arr_branches.length; i++) {
          if (arr_branches[i].id == this.search.branch_id) {
            filename += arr_branches[i].name;
          }
        }
        const tokenKey = u.token();
        this.processing = true;
        u.e(this.link(`/api/exel/export-checkin-list/${this.filename}/`))
          .then(response => {
            this.processing = false;
            saveAs(response, `${this.filename}.xlsx`);
          })
          .catch(e => console.log(e));
      },
      loadECCMS() {
        if (this.session.user.role_id === u.r.ec) {
          // this.cache.filter.ec = this.session.user.id
        }
        if (this.session.user.role_id === u.r.cm) {
          // this.cache.filter.cm = this.session.user.id
        }
        const brc =
          this.cache.filter.branch === "" ? 0 : this.cache.filter.branch;
        u.g(`${this.html.page.url.list}load/eccms/filter/${brc}`)
          .then(response => {
            this.html.dom.filter.ec.data = response.ecs;
            this.html.dom.filter.cm.data = response.cms;
            if (response.ecs.length > 1) {
              this.html.dom.filter.ec.disabled = false;
            }
            if (response.cms.length > 1) {
              this.html.dom.filter.cm.disabled = false;
            }
          })
          .catch(e => console.log(e));
      },
      selectBranch(data) {
        if (data) {
          this.cache.filter.branch = data.id;
          this.html.data.filter.branch = data.id;
          this.get(this.link(), this.load);
          this.loadECCMS();
          u.g(`${this.html.page.url.load}${data.id}`)
            .then(response => {
              this.html.dom.full = "";
              this.html.dom.filter.ec.disabled = false;
              this.html.dom.filter.cm.disabled = false;
              this.html.dom.filter.program.disabled = true;
              this.html.dom.filter.tuition_fee.disabled = false;
            })
            .catch(e => console.log(e));
        }
      },
      getDistrict(data = null) {
        if (data && typeof data === "object") {
          const province_id = data.id;
          this.html.data.filter.province = data;
          this.html.data.filter.province_id = province_id;
          u.g(`/api/provinces/${province_id}/districts`).then(response => {
            this.html.dom.filter.district.list = response;
            this.html.data.filter.district_id = "";
          });

          if (this.cache) {
            this.cache.filter.province_id = this.html.data.filter.province_id;
            this.get(this.link(), this.load);
          }
        } else {
          this.html.data.filter.province = "";
          this.html.data.filter.province_id = "";
        }
      },
      selectDistrict() {
        if (this.cache) {
          this.cache.filter.district_id = this.html.data.filter.district_id;
          this.get(this.link(), this.load);
        }
      },
      selectZone() {
        if (this.cache) {
          this.cache.filter.zone = this.html.data.filter.zone;
          this.get(this.link(), this.load);
        }
      },
      selectRegion() {
        // u.log("Select Region");
      },
      checkinAction() {
        // u.log("Checkin Action");
      },
      renewAction() {
        // u.log("Renew Action");
      },
      searchAction() {
        this.get(this.link(), this.load);
        this.html.dom.show.program_column = false;
        this.html.dom.show.product_column = false;
        if (this.html.data.filter.program) {
          this.html.dom.show.program_column = true;
        }
        if (this.html.data.filter.product) {
          this.html.dom.show.product_column = true;
        }
      },
      validate_keyword() {
        this.html.data.filter.keyword = this.html.data.filter.keyword.replace(
          /[~`!#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi,
          ""
        );

      },
      searchAction1() {
        // const key = u.live(word) && word != '' ? word : this.html.data.filter.keyword
        // this.cache.filter.keyword = key ? key : ''
        this.html.data.filter.keyword = this.html.data.filter.keyword.trim();
        const startDate = this.html.data.filter.dateRange!='' && this.html.data.filter.dateRange[0] ?`${u.dateToString(this.html.data.filter.dateRange[0])}`:''
        const endDate = this.html.data.filter.dateRange!='' && this.html.data.filter.dateRange[1] ?`${u.dateToString(this.html.data.filter.dateRange[1])}`:''
        const startDateCheckin = this.html.data.filter.dateRangeCheckin!='' && this.html.data.filter.dateRangeCheckin[0] ?`${u.dateToString(this.html.data.filter.dateRangeCheckin[0])}`:''
        const endDateCheckin = this.html.data.filter.dateRangeCheckin!='' && this.html.data.filter.dateRangeCheckin[1] ?`${u.dateToString(this.html.data.filter.dateRangeCheckin[1])}`:''
        this.html.data.filter.startDate = startDate
        this.html.data.filter.endDate = endDate
        this.html.data.filter.startDateCheckin = startDateCheckin
        this.html.data.filter.endDateCheckin = endDateCheckin

        this.html.pagination.spage = 0;
        this.html.pagination.ppage = 0;
        this.html.pagination.npage = 0;
        this.html.pagination.lpage = 0;
        this.html.pagination.cpage = 1;
        this.html.pagination.total = 0;
        this.html.pagination.limit = 20;

        this.get(this.link(), this.load);
        this.html.dom.show.program_column = false;
        this.html.dom.show.product_column = false;
        if (this.html.data.filter.program) {
          this.html.dom.show.program_column = true;
        }
        if (this.html.data.filter.product) {
          this.html.dom.show.product_column = true;
        }
      },
      dateChange() {
        this.get(this.link(), this.load);
      },
      typeAction() {
        if (this.cache) {
          this.cache.filter.type = this.html.data.filter.type;
          this.get(this.link(), this.load);
        }
      },
      selectProduct(product_id) {
        let branch = this.html.data.filter.branch;
        if (branch == "") {
          alert("Vui lòng chọn trung tâm!");
          this.html.data.filter.product = "";
          return false;
        }

        axios.get(`/api/all/programs/${branch}/${product_id}`).then(res => {
          this.html.dom.filter.program.data = res.data;
          this.html.dom.filter.program.disabled = false;
        });

        if (this.cache) {
          this.cache.filter.product = this.html.data.filter.product;
          this.get(this.link(), this.load);
        }
      },
      selectProgram() {
        if (this.cache) {
          this.cache.filter.product = this.html.data.filter.product;
          this.cache.filter.program = this.html.data.filter.program;
          this.get(this.link(), this.load);
        }
      },
      statusAction() {
        // do search
      },
      showAdvance() {
        this.html.dom.show.advance = true;
        this.html.dom.show.buttonAdvance = false;
        this.html.dom.show.buttonBasic = true;
      },
      showBasic() {
        this.html.dom.show.advance = false;
        this.html.dom.show.buttonAdvance = true;
        this.html.dom.show.buttonBasic = false;
        this.clearFilter();
      },
      clearSearch() {
        location.reload();
        // this.clearFilter();
        // this.get(this.link(), this.load);
      },
      clearFilter() {
        this.html.data.filter.ec = "";
        this.html.data.filter.field = "";
        this.html.data.filter.branch = "";
        this.html.data.filter.keyword = "";
        this.html.data.filter.student_status = "";
        this.html.data.filter.renew = "";
        this.html.data.filter.zone = "";
        this.html.data.filter.region = "";
        this.html.data.filter.checkin = "";
        this.html.data.filter.date_range = "";
        this.html.data.filter.district_id = "";
        this.html.data.filter.province = "";
        this.html.data.filter.province_id = "";
        this.html.data.filter.gender = "";
      },
      selectTuitionFee() {
        // u.log("Select Tuition Fee");
      },
      showError(){
        alert('Học sinh không có tên hoặc số điện thoại phụ huynh trung tâm vào cập nhật lại hồ sơ học sinh trước khi thêm mới khách hàng');
      },
      getAllBranch(){
        u.g('/api/settings/branches?all=1')
          .then(data => {
            this.branch_list = data
          })
      },
      quyen(){console.log("ddddddddddddddxxx",this.html.data.ecNew.item)},
      getUsersByBranch(){
        u.g(`/api/students/get/ec/of/a/branch/${this.transfer_branch_id}`).then(data => {
          if (data.ecs) {
            this.html.data.ecNew.list = data.ecs
            this.html.data.ecNew.item = ''
            this.html.dom.ecNew.disabled = false
          }
          if (data.cms) {
            this.html.data.csNew.list = data.cms
            this.html.data.csNew.item = ''
            this.html.dom.csNew.disabled = false
          }
        })
      },
      checkedStudent(student){
        var r = confirm("Bạn chắc chắn học sinh trên đã lên trung tâm checkin!");
        if (r == true) {
          u.g(`/api/checkin/update_checked/${student.id}`).then(data => {
            alert('Cập nhật checkin thành công')
          })
        }else{
          student.checked = false
        } 
      },
      remove(student){
        var r = confirm("Bạn chắc chắn muốn xóa checkin của học sinh trên");
        if (r == true) {
          u.g(`/api/checkin/delete_checked/${student.id}`).then(data => {
            alert('Xóa checkin thành công')
            this.searchAction1()
          })
        }
      },
      extract() {
        this.processing = true
        var params ={
          search:this.html.data.filter
        }
        var urlApi = this.checked?'/api/export/checkin-list-export?act=checked':'/api/export/checkin-list-export'
        var tokenKey = u.token()
        u.g(urlApi, params, 1, 1)
          .then(response => {
            saveAs(response, "Danh sách học sinh checkin.xlsx");
            this.processing = false;
          })
          .catch(e => {
            this.processing = false;
          });
      },
    }
  };
</script>

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
    margin: -25px -15px 0;
  }
  .apax-multiple-select {
    border-right: 1px solid #bbd5e8;
    border-bottom: 1px solid #bbd5e8;
    border-top: 1px solid #bbd5e8;
  }
  .search-province {
    width: 100%;
    height: 36px;
  }
</style>
<style>
  .apax-multiple-select .multiselect__tags {
    height: 36px;
    overflow-y: auto;
    border-radius: unset;
  }
  .multiselect--active {
    z-index: 3;
  }
  .apax-multiple-select .multiselect__tag,
  .apax-multiple-select .multiselect__tag-icon:focus,
  .apax-multiple-select .multiselect__tag-icon:hover {
    color: #fff;
    background: red;
  }
  .apax-multiple-select .multiselect__tag-icon:after {
    color: #fff;
  }
  .mx-datepicker-top > span:nth-child(1):after,
  .mx-datepicker-top > span:nth-child(2):after {
    content: unset;
  }
  .search-province .dropdown-toggle {
    height: 36px;
  }
  .search-province input[type="search"]::placeholder {
    margin-top: -2px;
  }
  .v-select .vs__selected-options {
    padding: 0px;
  }
  .search-province.disabled,
  .select-branch .disabled,
  .search-province.disabled button.disabled,
  .search-province.disabled .open-indicator,
  .search-province.disabled .dropdown-toggle input,
  .v-select.disabled,
  .v-select.disabled .dropdown-toggle,
  .v-select.disabled .dropdown-toggle .clear,
  .select-branch .disabled input::placeholder,
  .v-select.disabled .open-indicator,
  .v-select.disabled .selected-tag .close,
  .multiselect--disabled,
  .multiselect--disabled .multiselect__select,
  .multiselect--disabled .multiselect__tags,
  .multiselect--disabled .multiselect__input,
  .multiselect--disabled .multiselect__single,
  .multiselect--disabled .multiselect__input::placeholder,
  .search-province.disabled .clear {
    background-color: #f7ebeb;
    background: #f7ebeb;
    color: #c82727;
    font-weight: 500;
  }
  .v-select.disabled .dropdown-toggle input {
    margin-top: -1px;
    height: 33px;
    border-right: none;
    background: #f7ebeb;
    color: #c82727;
    border-bottom: none;
  }
  .mx-calendar-content .today {
    color: #fff !important;
    font-size: 14px !important;
    background: red !important;
  }
  .mx-datepicker-btn-confirm {
    background: #06a015;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
  }
  .mx-calendar-month > a {
    font-size: 10px;
  }
  .mx-calendar-month > a.current,
  .mx-calendar-table td.current,
  .mx-calendar-year > a.current {
    background: red;
    color: #fff;
  }
  .multiselect__select {
    height: 34px;
  }
  .multiselect.apax-multiple-select {
    width: calc(100% - 45px);
  }
  .search-province.searchable {
    width: calc(100% - 40px);
  }
</style>
