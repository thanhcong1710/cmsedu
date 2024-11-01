<template>
  <div class="animated fadeIn apax-form" :class="contract.status === 0 ? 'ex-contract' : ''">
    <div class="row">
      <div class="col-12">
        <div :class="html.loading.class ? 'loading' : 'standby'" class="ajax-loader">
          <img :src="html.loading.source">
        </div>
        <b-card header>
          <div slot="header">
            <i class="fa fa-clipboard"></i> <b class="uppercase">Nội dung đăng ký chương trình học của học viên: {{student.name}} {{contract.only_give_tuition_fee_transfer === 1 ? '(Hồ sơ này chỉ để nhận chuyển phí)' : ''}}</b>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">  
                <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
            </div>
          </div>
          <div id="page-content">
            <div class="col-lg-12">
              <div class="row">
                <div class="col-md-6 pad-no apax-show-detail">
                  <div class="col-md-12">
                    <address>
                      <h6 class="text-main">Thông tin học sinh</h6>
                    </address>
                  </div>
                  <div class="col-md-12 pad-no">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Họ Tên</label>
                          <input class="form-control" :value="student.name" type="text" readonly>                          
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Mã CMS</label>
                          <input class="form-control" :value="student.crm_id" type="text" readonly>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label">Mã Hợp Đồng</label>
                          <input class="form-control" :value="contract.accounting_id" type="text" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Nguồn</label>
                          <input class="form-control" :value="student.source | studentSource" type="text" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Facebook</label>
                          <input class="form-control" :value="student.facebook" type="text" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Ngày Sinh</label>
                          <input class="form-control" :value="student.date_of_birth | formatDate" type="text" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Giới Tính</label>
                          <input class="form-control" :value="student.gender | genderToName" type="text" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Email</label>
                          <input class="form-control" :value="student.email" type="text" readonly>                          
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Số Điện Thoại</label>
                          <input class="form-control" :value="student.phone" type="text" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Phụ Huynh</label>
                      <input class="form-control" :value="student.parent_name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Số Điện Thoại</label>
                      <input class="form-control" :value="student.parent_mobile" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Email</label>
                      <input class="form-control" :value="student.parent_email" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Địa Chỉ Nhà</label>
                      <input class="form-control" :value="student.address" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Trường Học</label>
                      <input class="form-control" :value="student.school" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Trung Tâm</label>
                      <input class="form-control" :value="student.branch_name" type="text" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Khu Vực</label>
                          <input class="form-control" :value="student.zone_name" type="text" readonly>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Đối tượng khách hàng</label>
                          <input class="form-control" :value="student.type | customerType" type="text" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 pad-no apax-show-detail">
                  <div class="col-md-12 pad-no">
                    <address>
                      <h6 class="text-main">Nội dung đăng ký chương trình học</h6>
                    </address>
                    <div class="col-md-12 pad-no">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Gói Sản Phẩm</label>
                            <input class="form-control" :value="contract.product_name" type="text" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Chương Trình Học <!--<button :disabled="html.dom.disabled.update_program" :class="html.dom.display.update_program" class="button-save-program" title="Cập nhật lại chương trình" value="Lưu" @click="html.dom.action.update_program" >Lưu</button>--></label>
                            <!--<div v-if="first === contract.id && contract.editable_program === 1">
                              <select v-model="contract.program_id" id="select_programs" class="selection program form-control" @change="html.dom.action.select_program">
                                <option :value="program.id" v-for="(program, idx) in programs" :key="idx">{{ program.name }}</option>
                              </select>
                            </div>-->
                            <div>
                              <input class="form-control" :value="contract.program_label" type="text" readonly>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no" :class="html.dom.display.receive">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Gói Học Phí</label>
                            <input class="form-control" :value="contract.tuition_fee_name" type="text" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Hình Thức Đóng Phí</label>
                            <input class="form-control" :value="contract.payload | payloadType" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no" :class="html.dom.display.receive">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Giá Gốc</label>
                            <input class="form-control" :value="contract.tuition_fee_price | formatMoney" type="text" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Tổng Số Buổi</label>
                            <input class="form-control" :value="contract.total_sessions" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no" :class="html.dom.display.receive">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Số Tiền Phải Đóng</label>
                            <input class="form-control" :value="contract.must_charge | formatMoney" type="text" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Số Tiền Đã Đóng</label>
                            <input class="form-control" :value="contract.total_charged | formatMoney" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Loại Hợp Đồng</label>
                            <input class="form-control" :value="contract.type | contractType" type="text" readonly>
                          </div>
                        </div>
                        <div v-if="displayFeeInfo" class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Mã chiết khấu/giảm giá</label>
                            <input class="form-control" :value="contract.coupon" type="text" readonly>
                          </div>
                        </div>
                        <div v-else class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Lớp Học Mong Muốn</label>
                            <input class="form-control" :value="contract.expected_class" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no" v-if="displayFeeInfo">
                      <div class="row">
                        <div class="col-md-12 pad-no" :class="html.dom.display.receive">
                          <div class="form-group">
                            <label class="control-label">Chi Tiết Mã chiết khấu giảm giá</label>
                            <div class="detail-bill-info-frame">
                              <div class="detail-bill-info" v-html="convertToHTML(detailCoupon(contract.discount_code))"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no">
                      <div class="row">
                        <div class="col-md-12 pad-no" :class="html.dom.display.receive">
                          <div class="form-group">
                            <label class="control-label">Chi Tiết Các Khoản Khấu Trừ</label>
                            <div class="detail-bill-info-frame">
                              <div class="detail-bill-info" v-html="convertToHTML(contract.description)"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no">
                      <div class="row">
                        <div class="col-md-6" :class="html.dom.display.receive">
                          <div class="form-group">
                            <label class="control-label">Số Buổi Học Còn Lại</label>
                            <input class="form-control" :value="contract.real_sessions" type="text" readonly>
                          </div>
                        </div>
                        <div class="col-md-6" v-if="displayFeeInfo">
                          <div class="form-group">
                            <label class="control-label">Lớp Học Mong Muốn</label>
                            <input class="form-control" :value="contract.expected_class" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Ngày Dự Kiến Học <button :disabled="html.dom.disabled.update_start_date" :class="html.dom.display.update_start_date" class="button-save-start-date" title="Cập nhật ngày bắt đầu" value="Lưu" @click="html.dom.action.update_start_date" >Lưu</button></label>
                            <div v-if="first === contract.id && contract.editable_start_date === 1">
                              <calendar
                                class="form-control calendar"
                                :value="contract.start_date"
                                :transfer="true"
                                format="YYYY-MM-DD"
                                :disabled-days-of-week=[]
                                :clear-button="false"
                                :placeholder="'Chọn ngày dự kiến học'"
                                :pane="1"
                                :disabled="true"
                                @input="html.dom.action.select_start_date"
                                :onDrawDate="html.dom.action.draw_start_date"
                                :lang="html.dom.action.lang"
                                :not-before="previous_date"
                              ></calendar>
                            </div>
                            <div v-else>
                              <input class="form-control" :value="contract.start_date | formatDate" type="text" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">Dự Kiến Kết Thúc</label>
                            <input class="form-control" :value="contract.end_date | formatDate" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 pad-no smx">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group"><input type="hidden" v-model="data.current_ec" />
                            <label class="control-label">EC <button :disabled="html.dom.disabled.update_ec" :class="html.dom.display.update_ec" class="button-save-ec" title="Cập nhật EC" value="Lưu" @click="html.dom.action.update_ec" >Lưu</button></label>
                            <select v-model="ec" :disabled="html.dom.disabled.select_ec" id="select_ec" class="selection ec form-control" @change="html.dom.action.select_ec">
                              <option :value="ec.ec_id" v-for="(ec, idx) in ecs" :key="idx">{{ ec.ec_name }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">EC Leader</label>
                            <input class="form-control" :value="contract.contract_ec_leader_name" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--<div class="col-md-12 pad-no smx">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group"><input type="hidden" v-model="data.current_cm" />
                            <label class="control-label">CS <button :disabled="html.dom.disabled.update_cm" :class="html.dom.display.update_cm" class="button-save-cm" title="Cập nhật CM" value="Lưu" @click="html.dom.action.update_cm" >Lưu</button></label>
                            <select v-model="cm" :disabled="html.dom.disabled.select_cm" id="select_cm" class="selection cm form-control" @change="html.dom.action.select_cm" readonly>
                              <option :value="cm.cm_id" v-for="(cm, idx) in cms" :key="idx">{{ cm.cm_name }}</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="control-label">OM</label>
                            <input class="form-control" :value="contract.contract_om_name" type="text" readonly>
                          </div>
                        </div>
                      </div>
                    </div>-->
                    <div class="col-md-12 pad-no smx">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Giám Đốc Trung Tâm</label>
                                <input class="form-control" :value="contract.contract_ceo_branch_name" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Giám Đốc Vùng</label>
                                <input class="form-control" :value="contract.contract_ceo_region_name" type="text" readonly>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer col-lg-12 apax-show-detail">
              <div class="row">
                <div class="col-md-6">
                  <div class="col-md-12">
                    <div class="form-group price-must-charge">
                      <label class="control-label">Ghi Chú</label>
                      <input class="form-control" type="text" v-model="contract.note" placeholder="" readonly >
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="col-md-12 mb-2 mt-2">
                    <br/>
                    <div class="form-group button-bar">
                      <apabtn v-show="checkQuit(contract)"
                        :onClick="html.dom.quit.action"
                        :disabled="html.dom.quit.disabled"
                        :icon="html.dom.quit.icon"
                        :label="html.dom.quit.label"
                        :title="html.dom.quit.title"
                        :markup="html.dom.quit.markup">
                      </apabtn>
                      <apabtn
                        :onClick="html.dom.back.action"
                        :disabled="html.dom.back.disabled"
                        :icon="html.dom.back.icon"
                        :label="html.dom.back.label"
                        :title="html.dom.back.title"
                        :markup="html.dom.back.markup">
                      </apabtn>
                      <apabtn
                        :onClick="html.dom.print.action"
                        :disabled="html.dom.print.disabled"
                        :icon="html.dom.print.icon"
                        :label="html.dom.print.label"
                        :title="html.dom.print.title"
                        :markup="html.dom.print.markup">
                      </apabtn>
                      <apabtn v-show="checkIsAdmin() || !contract.accounting_id"
                        :onClick="html.dom.retry.action"
                        :disabled="html.dom.retry.disabled"
                        :icon="html.dom.retry.icon"
                        :label="html.dom.retry.label"
                        :title="html.dom.retry.title"
                        :markup="html.dom.retry.markup">
                      </apabtn>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="panel apax-full-width">
                <address class="panel-heading">
                  <h6 class="panel-title">Danh sách đăng ký chương trình học</h6>
                </address>
                <div class="panel-body">
                  <!-- <div class="newtoolbar overhidden">
                    <div id="demo-custom-toolbar2" class="table-toolbar-left">
                      <search
                          :id="search_id"
                          :name="search_name"
                          :label="search_label"
                          :onSearch="searchBy"
                          :disabled="disableSearch"
                          :placeholder="search_placeholder"
                        >
                      </search>
                    </div>
                  </div> -->
                  <div class="table-responsive scrollable">
                    <table class="table table-striped table-bordered apax-table">
                      <thead>
                        <tr class="text-sm">
                          <th>Mã bản ghi</th>
                          <th>Loại HS</th>
                          <th>Mã HĐ</th>
                          <th>Loại HĐ</th>
                          <th>EC</th>
                          <th>CM</th>
                          <th>Chương trình</th>
                          <th>Gói phí</th>
                          <th>Số buổi</th>
                          <th>Số tiền phải đóng</th>
                          <th>Ngày bắt đầu</th>
                          <th>Ngày kết thúc</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in contracts" :key="index">
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.code}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{student.type | customerType}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.accounting_id}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.type | contractType}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.contract_ec_name}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.contract_cm_name}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.program_name}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.tuition_fee_name}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.total_sessions}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.must_charge | formatMoney}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.start_date}}</div></td>
                          <td><div v-b-tooltip.hover title="Nhấp vào để xem chi tiết" class="link-me" @click="load(item.id)">{{item.end_date}}</div></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
      <div class="printing-area hidden">
        <print :infor="printing"></print>
        <trail :infor="trailing"></trail>
      </div>
    </div>
  </div>
</template>

<script>
import calendar from 'vue2-datepicker'
import u from '../../../utilities/utility'
import search from '../../../components/Search'
import apabtn from '../../../components/Button'
import print from '../../base/prints/contract'
import trail from '../../base/prints/trail_register'

export default {
  name: 'Contract-Detail',
  components: {
    calendar,
    search,
    apabtn,
    print,
    trail
  },
  data () {
    const model = u.m('contracts').page
    model.ec = null
    model.ecs = []
    model.cm = null
    model.cms = []
    model.curec = 0
    model.curcm = 0
    model.first = 0
    model.curprogram = 0
    model.curstartdate = ''
    model.start_date = ''
    model.student = {}
    model.program = {}
    model.contract = {}
    model.programs = []
    model.contracts = []
    model.printing = {}
    model.trailing = {}
    model.holidays = [],
    model.classdays = [2, 5],
    model.latest_contract = {}
    model.previous_date = ''
    model.html.dom = {
      display: {
        receive: 'hidden',
        update_ec: 'hidden',
        update_cm: 'hidden',
        update_program: 'hidden',
        update_start_date: 'hidden'
      },
      disabled: {
        select_ec: true,
        select_cm: true,
        update_ec: true,
        update_cm: true,
        update_program: true,
        select_start_date: false,
        update_start_date: true
      },
      action: {
        select_ec: () => this.selectEc(),
        select_cm: () => this.selectCm(),
        update_ec: () => this.updateEc(),
        update_cm: () => this.updateCm(),
        select_program: () => this.selectProgram(),
        update_program: () => this.updateProgram(),
        select_start_date: start_date => this.selectStartDate(start_date),
        update_start_date: () => this.updateStartDate(),
        draw_start_date: e => this.onDrawStartDate(e),
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
      quit: {
        label: 'Bỏ Cọc',
        icon: 'fa-times-circle',
        title: 'Bỏ cọc, hủy hợp đồng nhập học này',
        markup: 'primary',
        disabled: false,
        action: () => this.quitContract()
      },
      back: {
        label: 'Thoát',
        icon: 'fa-sign-out',
        title: 'Quay lại danh sách nhập học',
        markup: 'error',
        disabled: false,
        action: () => this.exitForm()
      },
      print: {
        label: 'In bản ghi nhập học',
        icon: 'fa-print',
        title: 'In nội dung bản ghi nhập học đang xem',
        markup: 'success',
        disabled: false,
        action: () => this.printForm()
      },
      retry: {
        label: 'Gửi lại phiếu ĐK lên cyber',
        icon: 'fa fa-angellist',
        title: 'Gửi lại phiếu ĐK lên cyber',
        markup: 'apax-btn full print',
        disabled: false,
        action: () => this.retryToCyber()
      }
    }
    model.cache.student = model.student
    model.cache.contract = model.contract
    model.cache.contracts = model.contracts
    return model
  },
  created(){
    this.start()
  },
  computed: {
    displayFeeInfo() {
      return parseInt(this.contract.type, 10) !== 0
    }
  },
  watch: {
    // 'ec': function() {
    //   if (this.ec != this.exEcID) {
    //     this.disableSaveEc = false
    //     this.hideSaveEc = 'display'
    //   } else {
    //     this.disableSaveEc = true
    //     this.hideSaveEc = 'hidden'
    //   }
    // }
  },
  methods: {
    start() {
      u.g(`${this.html.page.url.apis}${this.$route.params.id}`)
      .then(response => {
        this.html.loading.action = false
        this.student = response.student
        this.programs = response.programs
        this.contract = response.contracts.length ? response.contracts[0] : []
        this.first = this.contract.id
        this.contracts = response.contracts
        this.ecs = response.ecs
        this.cms = response.cms
        this.ec = this.contract.ec_id
        this.cm = this.contract.cm_id
        this.curec = this.student.ec_id
        this.curcm = this.student.cm_id
        this.curprogram = this.contract.program_id
        this.html.dom.print.disabled = false
        const session_data = u.session()
        this.holidays = session_data.info.holidays
        this.previous_date = this.moment(this.contract.start_date).format('YYYY-MM-DD')
        if (response.relation_info && response.relation_info.latest_contract) {
          this.latest_contract = response.relation_info.latest_contract
          if (this.latest_contract && this.latest_contract.enrolment_schedule) {
              const class_days = this.latest_contract.enrolment_schedule.toString().split(',')
              if (class_days.length) {
                  this.classdays = class_days
              }
          }
        }
        if (response.relation_info && response.relation_info.latest_date) {
          this.previous_date = this.moment(response.relation_info.latest_date).format('YYYY-MM-DD')
        }
        if (this.contract.only_give_tuition_fee_transfer === 0 && this.contract.type !== 0) {
          this.html.dom.display.receive = 'display'
        }
        if (u.ca('edit_contract_ec') && this.contract.type > 0) {
          this.html.dom.disabled.select_ec = true //false
        } else {
          this.html.dom.display.update_ec = 'hidden'
        }
        if (u.ca('edit_contract_cm') && this.contract.type > 0) {
          this.html.dom.disabled.select_cm = false
        } else {
          this.html.dom.display.update_cm = 'hidden'
        }        
        if(this.contract.type == 4){
            this.html.dom.print.disabled = true
        }else{
            this.html.dom.print.disabled = false
        }
      })
    },
    detailCoupon(discountCode){
      const name = _.get(discountCode, 'name');
      const percent = _.get(discountCode, 'percent');
      const discount = _.get(discountCode, 'discount');
      const price = _.get(discountCode, 'price');
      if (!name || !percent || !discount || !price) return ''

      return `- Tên: ${name} <br/>- Tỷ lệ chiết khấu: ${percent}% <br />- Giá gốc: ${this.format(price)}<br/>- Số tiền chiết khấu: ${this.format(discount)})`
    },
    format: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ',
    selectEc() {
      if (this.contract.type > 0) {
        this.html.dom.disabled.update_ec = false
        this.html.dom.display.update_ec = 'display'
      }
    },    
    selectCm() {
      if (this.contract.type > 0) {
        this.html.dom.disabled.update_cm = false
        this.html.dom.display.update_cm = 'display'
      }
    },
    selectProgram() {
      this.html.dom.disabled.update_program = false
      this.html.dom.display.update_program = 'display'
    },
    selectStartDate(selected_start_date) {      
      this.html.dom.disabled.update_start_date = false
      this.html.dom.display.update_start_date = 'display'
      if (this.latest_contract) {
          const class_days = this.latest_contract.enrolment_schedule ? this.latest_contract.enrolment_schedule.toString().split(',') : ''
          if (class_days.length) {
              const buffer = []
              class_days.map(d => {
                  buffer.push(parseInt(d, 10))
                  return d
              })
              this.classdays = buffer
          }
      }
      const start_date = selected_start_date ? selected_start_date : this.contract.start_date
      let sessions = this.contract.tuition_fee_session ? parseInt(this.contract.tuition_fee_session, 10) : 3
      if (start_date) {
          this.contract.start_date = start_date
          const k = this.contract.product_id ? parseInt(this.contract.product_id, 10) : 0
          const sessions = this.contract.tuition_fee_session ? parseInt(this.contract.tuition_fee_session, 10) : 0
          const classSchedule = u.getRealSessions(sessions, this.classdays, this.holidays[k], start_date)
          // const classSchedule = u.getRealSessions(sessions, this.classdays, [], start_date)
          this.contract.end_date = classSchedule.end_date
      } else {
          this.contract.end_date = ''
      }
    },
    onDrawStartDate(e) {
        let date = e.date
        if (this.moment(this.previous_date) > this.moment(date.getTime())) {
            e.allowSelect = false
        }
    },
    checkIsAdmin(){
      if (u.session().user.role_id == 999999999)
        return true
      else
        return false
    },
    checkQuit(contract) {
      let resp = false
      if (contract.only_give_tuition_fee_transfer == 0 && parseInt(contract.status, 10) > 0 && parseInt(contract.type, 10) > 0 && contract.status == 7 && parseInt(contract.debt_amount, 10) >= 0) {
        resp = true
      }
      if (parseInt(contract.type, 10) > 0 && parseInt(contract.debt_amount, 10) === 0) {
        resp = false
      }
      return resp
    },
    updateEc() {
      u.g(`/api/contracts/update/staff/${this.ec}/${this.$route.params.id}/1`)
      .then(response => {
        this.curec = this.ec
        this.contract.contract_ec_leader_name = response.leader_name
        this.html.dom.display.update_ec = 'hidden'
        this.html.dom.disabled.select_ec = true
        this.$notify({
            group: 'apax-atc',
            title: 'Thông Báo!',
            type: 'success',
            duration: 3000,
            text: 'Thông tin về EC đã được cập nhật thành công!'
        })
      })
    },
    updateCm() {
      u.g(`/api/contracts/update/staff/${this.cm}/${this.$route.params.id}/0`)
      .then(response => {
        this.curcm = this.cm
        this.contract.contract_om_name = response.leader_name
        this.html.dom.display.update_cm = 'hidden'
        this.html.dom.disabled.select_cm = true
        this.$notify({
            group: 'apax-atc',
            title: 'Thông Báo!',
            type: 'success',
            duration: 3000,
            text: 'Thông tin về CM đã được cập nhật thành công!'
        })
      })
    },
    updateProgram() {
      u.g(`/api/contracts/update/staff/${this.contract.program_id}/${this.$route.params.id}/2`)
      .then(response => {
        this.curprogram = this.program
        this.contract.program_name = response.program_name || this.contract.program_name
        this.html.dom.display.update_program = 'hidden'
        this.html.dom.disabled.select_program = true
        this.$notify({
            group: 'apax-atc',
            title: 'Thông Báo!',
            type: 'success',
            duration: 3000,
            text: 'Thông tin về chương trình học đã được cập nhật thành công!'
        })
      })
    },
    updateStartDate() {
      u.g(`/api/contracts/update/staff/{"start_date":"${this.contract.start_date}","end_date":"${this.contract.end_date}"}/${this.$route.params.id}/3`)
      .then(response => {
        this.curstartdate = response.start_date
        this.contract.start_date = response.start_date || this.contract.start_date
        this.contract.end_date = response.end_date || this.contract.end_date
        this.html.dom.disabled.select_start_date = false
        this.$notify({
            group: 'apax-atc',
            title: 'Thông Báo!',
            type: 'success',
            duration: 3000,
            text: 'Thông tin về ngày bắt đầu học đã được cập nhật thành công!'
        })
      })
    },
    convertToHTML(str) {
      return str ? str.replace(/\n/g, '<br />') : ''
    },
    searchBy(word) {
      // const key = word != '' ? word : '_'
      // this.keyword = key
      // // console.log(`FILTER BY \nCustomer Type: ${this.customer_type}\nProgram ID: ${this.program}\nTuition Fee ID: ${this.tuition_fee}`)
      // const ctype = this.customer_type > 0 ? this.customer_type : 0
      // const proid = this.program > 0 ? this.program : 0
      // const tuitd = this.tuition_fee > 0 ? this.tuition_fee : 0
      // const filter = `${ctype.toString()},${proid.toString()},${tuitd.toString()}`
      // this.get(`/api/contracts/list/1/${key}/${filter}`)
    },
    load(id) {
        if(this.contract.type == 4){
            this.html.dom.print.disabled = true
        }else{
            this.html.dom.print.disabled = false
        }
      this.contract = _.head(this.contracts.filter(item => item.id === id))
      this.ec = this.contract.ec_id
      this.cm = this.contract.cm_id
    },
    quitContract() {
      u.p('/api/contracts/quit', this.contract)
      .then(response => {
        if (response.ok) {
          u.apax.$emit('apaxPopup', {
            on: true,
            content: `Hồ sơ nhập học mã: ${response.code}<br/>Đã được hủy thành công!`,
            title: 'Hồ Sơ Nhập Học Đã Bị Hủy Do Bỏ Cọc',
            class: 'modal-danger',
            size: 'md',
            variant: 'apax-ok'
          })
          this.contract.status = 0
          this.html.dom.quit.disabled = true
        }
      })
    },
    // printForm() {
    //   let printing_data = {}
    //   if (this.contract.type === 0) {
    //     printing_data = {
    //       contract_ec_name: this.contract.contract_ec_name,
    //       contract_ec_leader_name: this.contract.contract_ec_leader_name,
    //       team: this.contract.contract_ec_leader_name,
    //       debt_amount: this.contract.debt_amount,
    //       student_name: this.student.name,
    //       school: this.student.school,
    //       birthday: this.student.date_of_birth,
    //       parent_name: this.student.parent_name,
    //       parent_mobile: this.student.parent_mobile,
    //       parent_email: this.student.parent_email,
    //       student_nick: this.student.nick,          
    //       expected_class: this.contract.expected_class
    //     }
    //     localStorage.setItem(`contract_${this.contract.id}`, JSON.stringify(printing_data))
    //     window.open(`/print/trial-register/${this.contract.id}`,'_blank')
    //     // this.trailing = printing_data
    //   } else if(this.contract.type === 1) {
    //     printing_data = {
    //       student_name: this.student.name,
    //       birthday: this.student.date_of_birth,
    //       student_gender: this.student.gender,
    //       address: this.student.address,
    //       school: this.student.school,
    //       parent_name: this.student.parent_name,
    //       parent_mobile: this.student.parent_mobile,
    //       parent_email: this.student.parent_email,
    //       program_name: this.contract.program_name,
    //       tuition_fee_name: this.contract.tuition_fee_name,
    //       start_date: this.contract.start_date,
    //       tuition_fee_price: this.contract.tuition_fee_price,
    //       bill_info: this.contract.bill_info,
    //       must_charge: this.contract.must_charge,
    //       contract_ec_name: this.contract.contract_ec_name,
    //       contract_ec_leader_name: this.contract.contract_ec_leader_name,
    //       debt_amount: this.contract.debt_amount,
    //       payment_amount: this.contract.total_charged
    //     }
    //     localStorage.setItem(`contract_${this.contract.id}`, JSON.stringify(printing_data))
    //     window.open(`/print/contract/${this.contract.id}`,'_blank')
    //     // this.printing = printing_data
    //   }
    // },
    printForm() {
      if (this.contract.type === 0) {
        window.open(`/print/trial-register/${this.contract.id}`,'_blank')
        // this.trailing = printing_data
      } else{
        window.open(`/print/contract/${this.contract.id}`,'_blank')
        // this.printing = printing_data
      }
    },
    exitForm() {
      this.$router.push('/contracts')
    },
    retryToCyber(){
      const data = {
        id: this.contract.id,
      }
      const cf = confirm("Bạn có chắc rằng gửi lại phiếu đăng ký "+this.contract.accounting_id+" lên Cyber?");
      if (cf === true){
        u.p(`/api/contracts/retry-cyber`, data).then(response => {
          u.apax.$emit('apaxPopup', {
            on: true,
            content: `Bạn đã gửi lại phiếu đăng ký ${this.contract.accounting_id} lên Cyber thành công!`,
            title: 'Thông báo',
            class: 'modal-success',
            size: 'md',
            variant: 'apax-ok'
          })
        })
      }
    }
  }
}
</script>

<style scoped>
#description-detail {
  margin:0 0 15px 0;
  height:115px;
  background: #EEEDED;
}
.apax-full-width {
  padding: 20px 15px 5px 15px;
  margin: 15px 0 0 0;
  border-top:1px solid #f1f1f1;
}
.smx input, .smx select {
  font-size: 11px;
}
i.tip {
  font-size:10px;
  color:#888888;
}
.apax-table tr td div {
  cursor: pointer;
}
button.hidden {
  display:none;
}
.button-save-ec, .button-save-cm, .button-save-program, .button-save-start-date {
  border: 1px solid rgb(241, 32, 32);
  padding: 3px 12px;
  display: inline-block;
  text-align: center;
  right: 15px;
  top: 0;
  position: absolute;
  float: right;
  color: #fde2e2;
  margin: 0 0 0 10px;
  font-size: 11px;
  background-color: #f74343;
}
.button-save-ec:hover, .button-save-cm:hover, .button-save-program:hover, .button-save-start-date:hover  {
  box-shadow: 0 -1px #550000; 
  color: #ffffff;
  padding: 2px 12px 4px;
  background-color: #c90000;
  text-shadow: 0 1px 0 #333;
  border: 1px solid rgb(155, 0, 0);
}
.detail-bill-info-frame {
  height: 113px;
  overflow-y: scroll;
  background: #f4f9ff;
  border: 1px solid #e1edff;
  -webkit-box-shadow: 0 -1px 0 #a1b7da;
  box-shadow: 0 -1px 0 #a1b7da;
}
.detail-bill-info {
  font-weight: 500;
  font-size: 11px;
  background: #f4f9ff;
  color: #165792;    
  padding: 10px;
  pointer-events: none;
  text-shadow: 0 1px 1px #FFF;
}
.button-bar {
  padding:0 0 0 15px;
}
</style>
