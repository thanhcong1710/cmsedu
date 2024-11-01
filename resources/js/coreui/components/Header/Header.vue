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
  <header class="app-header navbar apax-nav-bar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button" @click="mobileSidebarToggle">&#9776;</button>
    <b-link class="navbar-brand" to="/dashboard"></b-link>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" @click="sidebarMinimize">&#9776;</button>
    <b-navbar-nav class="d-md-down-none">
      <!-- <b-nav-item class="px-3">Bảng Thông Báo</b-nav-item>
      <b-nav-item class="px-3">Hồ Sơ Cá Nhân</b-nav-item> -->
      <b-nav-item class="px-3" v-on:click="logout">Đăng Xuất</b-nav-item>
    </b-navbar-nav>
    <b-modal size="md" 
      ok-variant="primary"
      title="Đổi Mật Khẩu Tài Khoản" 
      class="modal-primary" 
      v-model="modal" 
      @ok="modal = false"
      hide-footer
      @close="modal = false">
      <profile :user="`${user_id}`" :mine=true :email="user_email" :profile="user_name"></profile>
    </b-modal>
    <b-modal size="lg" 
      ok-variant="success"
      title="Cập Nhật Thông Tin Tài Khoản" 
      class="modal-success" 
      v-model="update"
      hide-footer
      @ok="update = false" 
      @close="update = false">
      <div class="ada-form users-profile" id="update-profile">
        <b-card header-tag="header">
          <div slot="header">
            <i class="fa fa-user"></i> <b class="uppercase">Cập nhật thông tin tài khoản người dùng {{ user_name }}</b>
          </div>
          <div class="panel">
            <div class="row">
              <div class="col-md-9 mt-2">
                <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Tên Nhân Viên:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="user_name" class="form-control" type="text"/>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Tài Khoản:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="user_account" class="form-control" type="text"/>
                  </div>
                </div> -->
                <!-- <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Mã HRM Nhân Viên:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="user_hrm_id" class="form-control" type="text"/>
                  </div>
                </div> -->
                <div class="row" v-show="checkSuperiorShow">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Mã Leader:</b></label><br>                    
                  </div>
                  <div class="col-md-8 mb-2">                    
                    <input v-model="user_superior_id" class="form-control" type="text"/>
                    <i class="text-danger" style="font-size: 12px;">( Nếu không có Leader nhập mã G0000 )</i><br>
                  </div>
                </div>
                <div class="row" v-show="checkIsSuperiorShow">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Xác nhận chức danh:</b></label><br>                    
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="I_am_leader" type="checkbox"/>
                    <i class="text-danger" style="font-size: 12px;"> Tôi xác nhận tài khoản của mình là leader.</i><br>                    
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Số Điện Thoại:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="user_phone" class="form-control" type="text"/>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Email:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <input v-model="user_email" class="form-control" type="text"/>
                  </div>
                </div> -->
                <div class="row">
                  <div class="col-md-4 mt-2">
                    <label class="control-label tight bold"><b>Ngày Bắt Đầu:</b></label>
                  </div>
                  <div class="col-md-8 mb-2">
                    <datepicker
                      v-model="user_start_date"
                      :readonly="false"
                      :lang="'en'"
                      :bootstrapStyling=true
                      :full=false
                      format="YYYY-MM-DD"
                      placeholder="Chọn ngày bắt đầu làm việc"
                      input-class="form-control bg-white"
                      class="time-picker"
                      change="selectStartDate"
                    ></datepicker>
                  </div>
                </div>
              </div>
              <div class="col-md-3 mt-2">
                <div class="row avatar-frame">
                  <div class="user-avatar">
                  <file
                    :label="'Ảnh đại diện'"
                    :name="'upload_avatar'"
                    :field="'avatar'" 
                    :type="'img'"
                    :link="validFile(user.avatar)"
                    :onChange="uploadAvatar"
                    :title="'Tải lên 1 file ảnh đại diện với định dạng ảnh là: jpg, jpeg, png, gif.'"
                  >
                  </file>
                </div>
                </div>
              </div>
            </div>
            <br/>
            <div class="row">
              <div class="col-md-3 mt-2">
                <abt
                  :markup="'success'"
                  :icon="'fa fa-save'"
                  :customClass="'update-button'"
                  label="Lưu"
                  title="Cập nhật thông tin tài khoản người dùng!"
                  :disabled=false
                  :onClick="saveProfile"
                  >
                </abt>
              </div>
              <div class="col-md-9 mb-2">
                <div :class="`alert-message alert-${class_name}`" role="alert" v-show="message.length">
                  <div v-html="message"></div>
                </div>
              </div>
            </div><br/>
          </div>
        </b-card>  
      </div>
    </b-modal>
    <modal name="tool-bar" @before-close="beforeClose" width="90%" height="96%" :draggable="true" :adaptive="true" :scrollable="true" :resizable="true" classes="tools-bar">
      <div class="tool-bar-frame modal-primary ada-modal">
        <header class="modal-header">
          <h5 class="modal-title">Công cụ hỗ trợ</h5>
          <button @click="$modal.hide('tool-bar')" class="close">×</button>
        </header>
        <div class="modal-body tool-bar">
          <div class="col-md-12">
            <div class="row tool-frame last-date active">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Chọn Trung Tâm</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="branches"
                            label="name"
                            :searchable="true"
                            v-model="branch"
                            @change="selectBranch"
                            :disabled="false"
                            :onChange="selectBranch"
                            > 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Chọn Lớp Học</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="classes"
                            label="cls_name"
                            :searchable="true"
                            v-model="selectedclass"
                            @change="selectClass"
                            :disabled="false"
                            :onChange="selectClass"> 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Các Ngày Học Trong Tuần</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="class_date_list"
                            :class="'special'"
                            label="name"
                            multiple
                            v-model="classdates"
                            @change="selectClassdays"
                            :disabled="false"
                            :onChange="selectClassdays"> 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Chọn Sản Phẩm</label>
                        </div>
                        <div class="col-md-8">
                          <selection
                            :options="products"
                            label="name"
                            :searchable="true"
                            v-model="product"
                            @change="selectProduct"
                            :disabled="false"
                            :onChange="selectProduct"> 
                          </selection>
                        </div>
                      </div>
                    </div>
                    <div class="form-group ">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Ngày Bắt Đầu Học</label>
                        </div>
                        <div class="col-md-8">
                          <datepicker
                            v-model="start_date"
                            :readonly="false"
                            :lang="'en'"
                            :bootstrapStyling=true
                            :full=false
                            format="YYYY-MM-DD"
                            placeholder="Chọn ngày bắt đầu học"
                            input-class="form-control bg-white"
                            class="time-picker"
                            @change="calculateLastDate"
                          ></datepicker>
                        </div>
                      </div>
                    </div>
                    <div class="form-group  tool-input session-number current">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="control-label">Nhập Số Buổi Học</label>
                        </div>
                        <div class="col-md-8">
                          <input class="form-control" type="number" v-model="sessions" @change="calculatingEndate" />
                        </div>
                      </div>
                    </div>
                    <div class="form-group  tool-input last-date">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="filter-label control-label">Nhập ngày học cuối</label>
                        </div>
                        <div class="col-md-8">
                          <datepicker
                            v-model="end_date"
                            :readonly="false"
                            :lang="'en'"
                            :bootstrapStyling=true
                            :full=false
                            placeholder="Chọn ngày kết thúc"
                            input-class="form-control bg-white"
                            class="time-picker"
                            @change="calculateSession"
                          ></datepicker>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group ">
                      <ul class="ada-scrolling-list" id="holidays">
                        <li class="row">
                          <span class="item-content col-md-12">Các ngày nghỉ lễ (Từ ngày -> Đến ngày)</span>
                        </li>
                        <li class="row" v-for="(holiday, index) in holidays" :key="index">
                          <span class="item-content col-md-12"><i class="fa fa-check"></i><input class="input-full-lenght" type="text" :value="`${holiday.start_date} - ${holiday.end_date} (${ holiday.name })`" readonly /></span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-3">                    
                    <div class="form-group ">
                      <ul class="ada-scrolling-list" id="learning-days">
                        <li class="row">
                          <span class="item-content col-md-12" v-show="last_date_result !== ''"><i class="fa fa-star"></i><input class="input-full-lenght" type="text" :value="` Ngày học cuối: ${last_date_result}`" readonly /></span>
                          <span class="item-content col-md-12" v-show="total_session > 0"><i class="fa fa-star"></i> Tổng số buổi học: {{ total_session }}</span>
                        </li>
                        <li class="row" v-for="(learningdate, index) in learningdates" :key="index">
                          <span class="item-content col-md-12"><i class="fa fa-bookmark"></i><input class="input-full-lenght" type="text" :value="` Buổi thứ ${(index + 1)} kế tiếp: ${learningdate}`" readonly /></span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row tool-frame tuition-transfer">
              <div class="col-md-12">
                <h5>Tính Chuyển Phí</h5>
                <div class="content apax-form">
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Chọn Gói Phí Chuyển</label>
                                    <selection
                                      :options="tuitions"
                                      label="name"
                                      :searchable="true"
                                      v-model="tuition"
                                      @change="selectTuition"
                                      :disabled="false"
                                      :onChange="selectTuition"
                                      > 
                                    </selection>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Chọn Trung Tâm Nhận</label>
                                    <selection
                                      :options="branches"
                                      label="name"
                                      :searchable="true"
                                      v-model="branchTransfer"
                                      @change="selectBranchTransfer"
                                      :disabled="false"
                                      :onChange="selectBranchTransfer"
                                      > 
                                    </selection>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Số Tiền Chuyển</label>
                                    <input class="form-control" type="text" v-model="tfamnt" @change="calculateTransfer"/>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="control-label">Số Buổi Chuyển</label>
                                    <input class="form-control" type="number" v-model="tfsess" @change="calculateTransfer"/>
                                </div>-->
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Chọn Sản Phẩm Nhận</label>
                                    <selection
                                      :options="products"
                                      label="name"
                                      :searchable="true"
                                      v-model="productTransfer"
                                      @change="selectProductTransfer"
                                      :disabled="false"
                                      :onChange="selectProductTransfer"> 
                                    </selection>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Thông Tin Gói Phí Chuyển</label>
                                    <div class="detail-info">
                                        Tên: {{ tftfif.name }}<br/>
                                        ID Gói Phí: {{ tftfif.id }}<br/>
                                        Product: {{ tftfif.product_name }}<br/>
                                        Số Buổi: {{ tftfif.session }}<br/>
                                        Giá Gốc: {{ tftfif.price | formatMoney }}<br/>
                                        Triết Khấu: {{ tftfif.discount | formatMoney }}<br/>
                                        Thực Thu: {{ (tftfif.price - tftfif.discount) | formatMoney }}<br/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Thông Tin Gói Phí Nhận</label>
                                    <div class="detail-info">
                                        Tên: {{ rctfif.name }}<br/>
                                        ID Gói Phí: {{ rctfif.id }}<br/>
                                        Product: {{ rctfif.product_name }}<br/>
                                        Số Buổi: {{ rctfif.session }}<br/>
                                        Giá Gốc: {{ rctfif.price | formatMoney }}<br/>
                                        Triết Khấu: {{ rctfif.discount | formatMoney }}<br/>
                                        Thực Thu: {{ (rctfif.price - rctfif.discount) | formatMoney }}<br/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Tiền Đã Chuyển</label>
                                    <div class="detail-info">
                                        {{ tfamnt | formatMoney }}
                                    </div>
                                    <label class="control-label">Đơn Giá Gói Phí Nhận</label>
                                    <div class="detail-info">
                                        {{ sptfif | formatMoney }}
                                    </div>
                                    <label class="control-label">Kết Quả Tính Toán</label>
                                    <div v-if="specia === 0" class="detail-info">
                                        {{ tfamnt | formatMoney }} / {{ sptfif | formatMoney }} = {{
                                        Math.round(tfamnt/sptfif, 2) }}
                                    </div>
                                    <div v-if="specia === 1" class="detail-info">
                                        Chuyển ngang buổi từ HN - HCM gói April: {{ sstfif }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Buổi Chuyển Được</label>
                                    <div class="detail-info">
                                        {{ sstfif }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="row tool-frame user-list">
              <div class="col-md-12">
                <h5>Danh sách nhân viên trong trung tâm</h5>
                <div class="content apax-form">
                    <div class="col-md-12 pad-no">
                      <div class="table-responsive scrollable">
                        <table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
                          <thead>
                            <tr>
                              <th>STT</th>
                              <th>Tên Nhân Viên</th>
                              <th>Mã HRM</th>
                              <th>Tài Khoản</th>
                              <th>Chức Vụ</th>
                              <th>Phone</th>
                              <th>Email</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item, index) in user_list" v-bind:key="index">
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{index+1}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.full_name}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.hrm_id}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.username}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.title}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.phone}}
                              </td>
                              <td :title="`${item.username} bắt đầu công tác từ ngày ${item.start_date}, tại vị trí ${item.title}`">
                                {{item.email}}
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="modal-footer">
          <button type="button" @click="calculateLastDate" class="ada-btn primary active"><i class="fa fa-calendar"></i> Tính ngày học cuối</button>
          <button type="button" @click="calculateSession" class="ada-btn success"><i class="fa fa-calendar-check-o"></i> Tính số buổi học</button>
          <button type="button" @click="calculateTransfer" class="ada-btn error"><i class="fa fa-retweet"></i> Tính chuyển phí</button>
          <button type="button" @click="viewUsersListing" class="ada-btn caution active"><i class="fa fa-list"></i> Danh sách NVTT</button>
          <button type="button" @click="$modal.hide('tool-bar')" class="ada-btn danger">Close</button>
        </footer>
      </div>
    </modal>
    <notifications group="notify" />
    <notifications position="bottom right" group="message" />
    <notifications position="bottom center" group="process" />
    <notifications position="top left" group="alert" />
    <notifications position="top center" group="inform" />
    <notifications position="bottom left" group="warning" />
    <b-navbar-nav class="ml-auto">
      <!-- <b-nav-item class="d-md-down-none">
        <i class="icon-bell"></i><span class="badge badge-pill badge-danger">5</span>
      </b-nav-item> -->
      <b-nav-item-dropdown right>
        <template slot="button-content">
          <span class="d-md-down-none">Tài khoản {{user.title}}</span>
          <img :src="user.avatar" class="img-avatar" alt="admin@bootstrapmaster.com">
          <span class="d-md-down-none">{{user.name}}</span>
        </template>
        <b-dropdown-item @click="updateProfile"><i class="fa fa-user"></i>Cập Nhật Hồ Sơ</b-dropdown-item>
        <b-dropdown-item @click="$modal.show('tool-bar')"><i class="fa fa-wrench"></i>Công Cụ Hỗ Trợ</b-dropdown-item>
        <b-dropdown-item @click="downloadUsingGuide" v-show="usingGuide"><i class="fa fa-download"></i>Tải Hướng Dẫn Sử Dụng</b-dropdown-item>
        <b-dropdown-item @click="changePassword"><i class="fa fa-refresh"></i>Đổi Mật Khẩu</b-dropdown-item>
        <b-dropdown-item @click="logout"><i class="fa fa-lock"></i> Đăng Xuất</b-dropdown-item>
      </b-nav-item-dropdown>
    </b-navbar-nav>
    <AutoChangePassword />
  </header>
</template>
<style scoped language="scss">

</style>

<script>
import u from '../../utilities/utility'
import a from '../../utilities/authentication'
import file from '../../components/File'
import abt from '../../components/Button'
import profile from '../../components/Profile'
import selection from 'vue-select'
import moment from 'moment'
import cookies from 'vue-cookies'
import datepicker from 'vue2-datepicker'
import HeaderDropdown from './HeaderDropdown.vue'
import HeaderDropdownAccnt from './HeaderDropdownAccnt.vue'
import HeaderDropdownTasks from './HeaderDropdownTasks.vue'
import HeaderDropdownNotif from './HeaderDropdownNotif.vue'
import HeaderDropdownMssgs from './HeaderDropdownMssgs.vue'
import AutoChangePassword  from '../AutoChangePassword.vue'

export default {
  name: "Header",
  data() {
    return {
      update: false,
      user_id: '',
      message: '',
      user_name: '',
      user_email: '',
      class_name: '',
      user_phone: '',
      superior_id: '',
      user_account: '',
      user_avatar: '',
      modal: false,
      tftfid: 0,
      tfamnt: 0,
      brchid: 0,
      prodid: 0,
      tftfif: '',
      rctfif: '',
      sptfif: 0,
      sstfif: 0,
      tfsess: 0,
      lang: 'en',
      item: '',
      list: [],
      class: {},
      user_list: [],
      total_session: 0,
      user_superior_id: '',
      last_date_result: '',
      branch: '',
      learningdate: '',
      learningdates: [],
      date2: '',
      dates2: [],
      total: '',
      product: '',
      classid: 55,
      branchid: 0,
      productid: 0,
      tuition: '',
      classes: [],
      holidays: [],
      branches: [],
      tuitions: [],
      holidates: [],
      tuitionids: [],
      tuition_id: [],
      productTransfer: '',
      branchTransfer: '',
      selectedclass: '',
      products: [
        {id: 1, name: 'iGarten'},
        {id: 2, name: 'April'},
        {id: 3, name: 'CDI 4.0'}
      ],
      classdays: [2,4],
      useholidays: 1,
      class_date_list: [
        {id: 0, name: '0 - Sunday'},
        {id: 1, name: '1 - Monday'},
        {id: 2, name: '2 - Tuesday'},
        {id: 3, name: '3 - Wednesday'},
        {id: 4, name: '4 - Thursday'},
        {id: 5, name: '5 - Friday'},
        {id: 6, name: '6 - Saturday'}],
      classdates: [{id: 2, name: '2 - Tuesday'},{id: 4, name: '4 - Thursday'}],
      start_date: this.moment().format('YYYY-MM-DD'),
      end_date: '',
      begin: '',
      end: '',
      specia: 0,
      sessions: 24,
      user: {
        id: '',
        name: '',
        title: '',
        email: '',
        avatar: null
      },
      I_am_leader: false,
      user_start_date: '',
      checkSuperiorShow: true,
      checkIsSuperiorShow: false,
      usingGuide: false,
      usingGuideUrl: ''
    }
  },
  components: {
    abt,
    file,
    profile,
    selection,
    datepicker,
    HeaderDropdownAccnt,
    HeaderDropdownTasks,
    HeaderDropdownNotif,
    HeaderDropdownMssgs,
    AutoChangePassword
  },
  created(){
    const session = u.session()
    this.checkUserRole(session)
    this.user = session.user
    this.user_id = this.user.id
    this.user_name = this.user.name
    this.user_phone = this.user.phone
    this.user_email = this.user.email
    this.user_avatar = this.user.avatar
    // this.user_hrm_id = this.user.hrm_id
    // this.user_account = this.user.nick
    this.user_start_date = this.user.start_date
    this.user_superior_id = this.user.superior_id
    if (this.user.hrm_id === this.user.superior_id) {
      this.I_am_leader = true
    }
    if (u.role(this.user.role_id) === 'ec_leader') {
      this.checkSuperiorShow = false
      this.checkIsSuperiorShow = true
    }
    const information = session.info
    this.user_list = information.users_list
    const branch_ids = this.user.branch_id.split(',')
    const branch_id = branch_ids[0]
    this.branch = this.user.branch_id
    this.branchid = this.user.branch_id
    this.branches = information.branches
    this.products = information.products
    this.holidays = information.holidays
    this.tuitions = information.tuitions
    this.classes = information.classes
    if (u.chk.boss() && u.role(this.user.role_id) === 'ec') {
      this.update = true
      this.class_name = 'danger'
      this.message = 'Tài khoản của bạn chưa được cập nhật thông tin mã HRM (Mã G) thủ trưởng, xin vui lòng bổ sung thông tin này trong hồ sơ tài khoản của bạn để kích hoạt các chức năng của phần mềm.'
    } 
  },
  methods: {
    checkUserRole(data){
      const role_id = data.user.role_id
      // console.log('test rollllll', role_id);
      if(role_id ===83){
        this.usingGuide = true
      }else if(role_id ===55 ||role_id ===56 ){
        this.usingGuide = true
      }else if(role_id ===68 ||role_id ===69){
        this.usingGuide = true
      }
    },
    downloadUsingGuide(){
      // console.log('test', this.user.role_id);
      const role_id = this.user.role_id
      var p2 = `/api/users/download-using-guide/${role_id}`;
      window.open(p2, '_blank');
    },
    calculatingEndate() {

    },
    selectTuition(val) {
      this.tftfid = val.id
      this.calcTransfer()
    },
    selectBranchTransfer(val) {
      this.brchid = val.id
      this.calcTransfer()
    },
    selectProductTransfer(val) {
      this.prodid = val.id
      this.calcTransfer()
    },
    logout() {
      a.logout(this.$router)
    },
    updateProfile() {
      this.update = true
    },
    selectBranch(branch) {
      this.branchid = branch.id
      u.g(`${u.url.host}/api/settings/branch/class/${branch.id}`)
      .then((response) => {
        this.classes = response.classes
      }).catch(e => u.log('Exeption', e))
    },
    selectProduct(product) {
      this.productid = product.id
      if (this.productid && this.classid) {
        u.g(`/api/settings/holidates/${this.classid}/${this.productid}`)
        .then(response => {
          this.holidays = response.holidays
          this.holidates = response.holidates
        })
      }
    },
    selectClass(classinfo) {
      this.classid = classinfo.id
      if (this.productid && this.classid) {
        u.g(`/api/settings/holidates/${this.classid}/${this.productid}`)
        .then(response => {
          this.holidays = response.holidays
          this.holidates = response.holidates
        })
      }
    },
    selectClassdays(val) {
      let valid = true
      let markup = ''
      let message = ''
      if (val.length < 2 || val.length > 4) {
        valid = false
        markup = 'danger'
        message = 'Số lượng buổi học thường là 2 buổi trên 1 tuần.'
      } else if (val.length !== 2) {
        markup = 'caution'
        message = 'Lưu ý, mỗi tuần thường chỉ có 2 buổi học.'
      }
      if (valid) {
        this.classdays = []
        val.map(itm=>this.classdays.push(itm.id))
      }
      if (message !== '') {
        this.$notify({
          group: 'apax-atc',
          title: 'Lưu Ý',
          type: markup,
          duration: 700,
          text: message
        })
      }
    },
    calculateLastDate() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.last-date').addClass('active')
      $('.tool-bar .tool-input.current').removeClass('current')
      $('.tool-bar .tool-input.session-number').addClass('current')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.primary').addClass('active')
      if (this.start_date && this.sessions && this.classdays.length && this.holidates.length) {
        const start_date = this.moment(this.start_date).format('YYYY-MM-DD')
        const info = u.getRealSessions(parseInt(this.sessions, 10), this.classdays, this.holidates, start_date)
        this.last_date_result = info.end_date
        this.learningdates = info.dates
        this.total_session = 0
      }
    },
    calculateSession() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.last-date').addClass('active')
      $('.tool-bar .tool-input.current').removeClass('current')
      $('.tool-bar .tool-input.last-date').addClass('current')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.success').addClass('active')
      if (this.start_date && this.end_date && this.classdays.length && this.holidates.length) {
        const start_date = this.moment(this.start_date).format('YYYY-MM-DD')
        const end_date = this.moment(this.end_date).format('YYYY-MM-DD')
        const info = u.calcDoneSessions(start_date, end_date, this.holidates, this.classdays)
        this.total_session = info.total
        this.learningdates = info.dates
        this.last_date_result = ''
      }
    },
    calculateTransfer() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.tuition-transfer').addClass('active')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.error').addClass('active')
      this.calcTransfer()
    },
    viewUsersListing() {
      $('.tool-bar .tool-frame.active').removeClass('active')
      $('.tool-bar .tool-frame.user-list').addClass('active')
      $('.tools-bar .ada-btn.active').removeClass('active')
      $('.tools-bar .ada-btn.caution').addClass('active')
    },
    beforeClose() {
      
    },
    changeSetting() {
      // this.$notify({
      //   group: 'apax-atc',
      //   title: 'Lưu Ý',
      //   type: 'info dark',
      //   duration: 100000,
      //   text: 'Xin vui lòng chọn loại khách hàng!'
      // })
      
      // u.apax.$emit('apaxModal', {
      //   body: '<b style="color:red">content body modal</b>',
      //   title: 'title  of modal',
      //   class: 'modal-primary',
      //   bopen:() => {
      //     u.log('MODAL BEFORE OPENED', 'Method emit before open modal successful!')
      //   },
      //   opened(){
      //     u.log('MODAL EVENT OPENED', 'function emit successful!')
      //   },
      //   bclose(){
      //     u.log('MODAL BEFORE CLOSED', 'function emit successful!')
      //   },
      //   closed:() => {
      //     u.log('MODAL EVENT CLOSED', 'function emit successful!')
      //   }
      // })
      // this.$modal.show('apax-modal')

      // u.apax.$emit('apaxPopup', {
      //   on: true,
      //   show(){ u.log('POPUP HAS BEEN SHOWED') },
      //   content: '<b style="color:green">content body popup</b>',
      //   title: 'title  of popup',
      //   class: 'modal-primary',
      //   size: 'lg',
      //   close(){ u.log('POPUP HAS BEEN CLOSED') },
      //   action: () => { u.log('POPUP ACTION OK') },
      //   variant: 'apax-ok'
      // })
    },
    saveProfile() {
      const data = {
        id: this.user.id,
        full_name: this.user_name,
        action_self_update_profile: true,
        // email: this.user_email,
        phone: this.user_phone,
        avatar: this.user.avatar,
        is_leader: this.I_am_leader,
        // hrm_id: this.user_hrm_id,
        // username: this.user_account,
        start_date: this.user_start_date,
        superior_id: this.user_superior_id
      }
      let valid = true
      let msg = ''
      if (data.full_name === '') {
        valid = false
        msg += '<i style="color:red">Tên nhân viên là trường bắt buộc không thể để trống.</i><br>'
      }
      // if (data.hrm_id === '') {
      //   valid = false
      //   msg += '<i style="color:red">Mã HRM nhân viên là trường bắt buộc không thể để trống.</i><br>'
      // }
      if (data.start_date === '') {
        valid = false
        msg += '<i style="color:red">Ngày bắt đầu làm việc là trường bắt buộc không thể để trống.</i><br>'
      }
      // if (data.username === '') {
      //   valid = false
      //   msg += '<i style="color:red">Tài khoản đăng nhập là trường bắt buộc không thể để trống.</i><br>'
      // }
      // if (data.email === '') {
      //   valid = false
      //   msg += '<i style="color:red">Email nhân viên là trường bắt buộc không thể để trống.</i><br>'
      // } else if (u.vld.email(String(data.email).toLowerCase()) && String(data.email).toLowerCase().indexOf('apaxenglish.com') === -1) {
      //   valid = false
      //   msg += '<i style="color:red">Email nhân viên không hợp lệ vui lòng kiểm tra đây phải là mail của ApaxEnglish.</i><br>'
      // }
      if (valid) {
        const user = this.user
        u.p(`/api/users/${user.id}/update-users-profile`, data).then(response => {
          console.log(response)
          if(response.success == true) {
            this.class_name = 'success'
            alert('Thông tin tài khoản đã được cập nhật thành công!')
            this.message = `Thông tin tài khoản người dùng <b>${data.full_name} (${data.username})</b> đã được cập nhật thành công!`
            a.logout(this.$router)
          } else {
              this.class_name = 'danger'
              this.message = response.message
          }
        })
      } else {
        this.class_name = 'danger'
        this.message = msg
      }
    },
    uploadAvatar(file, param = null) {
      if (param) {
        this.user[param] = file
      }
    },
    validFile(file) {
      let resp = file && (typeof file === 'string') ? file : ''
      if (file && typeof file === 'object') {
        resp = `${file.type},${file.data}`
      }
      return resp
    },
    imageChanged(e) {
      var fileReader = new FileReader();
      fileReader.readAsDataURL(e.target.files[0])
      fileReader.onload = (e) => {
        this.user.avatar = e.target.result
      }
    },
    onDrawStartDate(e) {
      let date = e.date
      // if (this.current_start_date > date.getTime()) {
      //   e.allowSelect = false
      // }
    },
    sidebarToggle(e) {
      e.preventDefault();
      document.body.classList.toggle("sidebar-hidden");
    },
    sidebarMinimize(e) {
      e.preventDefault();
      document.body.classList.toggle("sidebar-minimized");
    },
    mobileSidebarToggle(e) {
      e.preventDefault();
      document.body.classList.toggle("sidebar-mobile-show");
    },
    asideToggle(e) {
      e.preventDefault();
      document.body.classList.toggle("aside-menu-hidden");
    },
    selectStartDate(start_date) {
      u.log('START DATE', start_date)
    },
    changePassword() {
      this.modal = true
    },

    // load() {
    //   u.g(`/api/settings/holidays/${this.classid}/${this.productid}`)
    //       .then(response => {
    //     const data = response
    //     if (parseInt(this.useholidays) === 1) {          
    //       this.list = data.holidays
    //     } else {          
    //       this.list = []
    //     }        
    //     const start = this.start
    //     const sessions = parseInt(this.sessions, 10)
    //     // this.classdays = data.classdays.length ? data.classdays : this.classdates.split(',')
    //     // this.classdates = this.classdays.join(',')
    //     this.classdays = this.classdates.split(',')
    //     this.begin = this.start
    //     const d1 = '2018-04-12'
    //     // console.log(`data.holidays: ${JSON.stringify(data.holidays)}\n\n\n`)
    //     const dc = u.checkInDaysRange(d1, data.holidays)
    //     // console.log(`Check the date "${d1}" in holidays is: ${dc}\n\n\n\n\n`)
    //     u.log('USE HOLIDAYS', parseInt(this.useholidays), this.list)
    //     let x = null
    //     let z = null
    //     if (parseInt(this.useholidays) === 1) {
    //       u.log('xxxxx')
    //       x = u.getRealSessions(sessions, this.classdays, data.holidays, start)
    //       u.log('Result last date: ', x)
    //       this.end = x.end_date
    //       this.dates1 = x.dates
    //     } else {
    //       u.log('zzzzz')
    //       this.holidays = []
    //       x = u.getRealSessions(sessions, this.classdays, [], start)
    //       u.log('Result last date: ', x)
    //       this.end = x.end_date
    //       this.dates1 = x.dates
    //     }        
    //     if (parseInt(this.useholidays) === 1) {
    //       u.log('xxxxx')
    //       this.holidays = data.holidays
    //       z = u.calcDoneSessions(this.begin, this.end, data.holidays, this.classdays)
    //       u.log('Result done sess: ', z)
    //       this.dates2 = z.dates
    //       this.total = z.total
    //     } else {
    //       u.log('zzzzz')
    //       this.holidays = []
    //       z = u.calcDoneSessions(this.begin, this.end, [], this.classdays)
    //       u.log('Result done sess: ', z)
    //       this.dates2 = z.dates
    //       this.total = z.total
    //     }        
    //   }).catch(e => console.log(e));
    // },
    calcTransfer() {
      if (this.tftfid && this.tfamnt && this.brchid && this.prodid) {
        const params = {
          tftfid: this.tftfid,
          tfamnt: this.tfamnt,
          brchid: this.brchid,
          prodid: this.prodid,
          tfsess: this.tfsess
        }
        u.g(`/api/scope/test/transfer/${JSON.stringify(params)}`)
          .then(response => {
            u.log('response', response)
            this.tftfif = response.transfer_tuition_fee
            this.rctfif = response.receive_tuition_fee
            this.sptfif = response.single_price
            this.sstfif = response.sessions
            this.specia = response.special
        }).catch(e => console.log(e))
      }
    },
    prepare() {
      const z = u.calcDoneSessions(this.begin, this.end, this.holidays, this.classdays)
      this.dates2 = z.dates
      this.total = z.total
    },
    doSelect(val) {
      u.log('Select', val)
    },
    withDraw(){
      var x = confirm("Bạn có chắc withdraw ?");

      if (x)
        u.a().get(`/api/daily-checking-withdraw-status`).then(response => {
          console.log(response);
        })
      else
        return false;
    }
  }
};
</script>
<style scoped lang="scss">
  #update-profile, #update-profile .card-header, #update-profile .card-body, #update-profile .card-body b, #update-profile .card-body input {
    color: #555;
  }
  .alert-message.alert-danger {
    margin: 0!important;
  }
  .modal-body.tool-bar {
    height:calc(100% - 60px);
  }
  .tool-input {
    display:none;
  }
  .tool-input.current {
    display:block;
  }
  ul.dropdown-menu {
    height: 230px!important;
  }
</style>