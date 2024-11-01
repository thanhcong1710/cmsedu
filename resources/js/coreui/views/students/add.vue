<template>
  <div class="animated fadeIn apax-form" id="add-new-student" @click="html.dom.page.action">
    <div class="row">
      <div class="col-lg-12">
        <b-card header-tag="header">
          <div slot="header">
            <i class="fa fa-file-text"></i> <b class="uppercase">Thêm mới học sinh</b>
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
          <div class="panel main-content">
            <div class="panel-body">
              <div class="row wrapinfor">
                <div class="col-sm-4">
                  <div class="form-group">
                    <file
                      :label="'Ảnh đại diện'"
                      :name="'upload_avatar'"
                      :field="'avatar'"
                      :type="'img'"
                      :onChange="uploadFile"
                      :title="'Tải lên 1 file ảnh đại diện với định dạng ảnh là: jpg, jpeg, png, gif.'">
                    </file>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <file
                      :label="'File đính kèm'"
                      :name="'upload_file'"
                      :field="'attached_file'"
                      :type="'doc'"
                      :onChange="uploadFile"
                      :title="'Tải lên 1 file đính kèm với định dạng tài liệu: pdf, doc, docx, xls, xlsx.'">
                    </file>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <inp :label="'Mã CMS'" v-model="obj.student.cms_id" :readonly=true :placeholder="'Sẽ tự động cập nhật khi lưu thông tin học sinh'" />
                  <input type="hidden" class="form-check-input" id="checkin" v-model="obj.student.checked">
                </div>
                <div class="col-sm-4">
                  <inp :label="'Mã Cyber'" v-model="obj.student.accounting_id" :readonly=true :placeholder="'Sẽ tự động cập nhật khi lưu thông tin học sinh'" />
<!--                  <input type="hidden" class="form-check-input" id="checkin" v-model="obj.student.checked">-->
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Họ tên của học viên là thông tin bắt buộc, viết in hoa không dấu">Tên học sinh <span class="text-danger"> (*)</span></label>
                    <p class="control has-icon has-icon-right">
                      <input name="student_name" class="form-control" v-model="obj.student.name" @input="validName"
                      v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('student_name') }" type="text" placeholder="Tên học sinh">
                      <span v-show="errors.has('student_name')" class="error-inform line">
                        <i v-show="errors.has('student_name')" class="fa fa-warning"></i>
                        <span v-show="errors.has('student_name')" class="error help is-danger">Tên là bắt buộc, dạng ký tự thường và viết không dấu!</span>
                      </span>
                    </p>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group select-date">
                    <label class="control-label" v-b-tooltip.hover title="Ngày sinh của học viên là thông tin bắt buộc, định dạng dd/mm/yyyy có thể nhấp vào ô nhập để chọn trên lịch biểu">Ngày sinh<span class="text-danger"> (*)</span></label>
                    <p class="control has-icon has-icon-right">
                      <datePicker
                        id="birth-day"
                        class="form-control calendar"
                        v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('date_of_birth') }"
                        :value="obj.student.date_of_birth"
                        v-model="obj.student.date_of_birth"
                        placeholder="Chọn ngày sinh nhật"
                        lang="lang"
                      >
                      </datePicker>
                      <span v-show="errors.has('date_of_birth')" class="error-inform line">
                        <i v-show="errors.has('date_of_birth')" class="fa fa-warning"></i>
                        <span v-show="errors.has('date_of_birth')" class="error help is-danger">Ngày sinh là dữ liệu bắt buộc!</span>
                      </span>
                    </p>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Giới tính của học viên là thông tin bắt buộc">Giới tính<span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.gender"
                      name="gender" v-validate data-vv-rules="required">
                      <option value="">Chọn giới tính</option>
                      <option value="M">Nam</option>
                      <option value="F">Nữ</option>
                    </select>
                    <span v-show="errors.has('gender')" class="error-inform line">
                      <i v-show="errors.has('gender')" class="fa fa-warning"></i>
                      <span v-show="errors.has('gender')" class="error help is-danger">Giới tính là bắt buộc!</span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Số điện thoại của học viên">Số điện thoại </label>
                    <input class="form-control"
                      type="text"
                      name="phone"
                      v-validate=""
                      v-model="obj.student.phone"
                      @input="validPhone">
                    <span v-show="errors.has('phone')" class="error-inform line">
                      <i v-show="errors.has('phone')" class="fa fa-warning"></i>
                      <span v-show="errors.has('phone')" class="error help is-danger"></span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" >Tên thường gọi/ Nick name</label>
                    <input class="form-control" type="text" v-model="obj.student.nick" name="nick">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Thông tin tỉnh thành của học viên là bắt buộc">Tỉnh/Thành phố<span class="text-danger"> (*)</span></label>
                      <!-- <select class="form-control" v-model="obj.student.province_id" @change="getDistrict"
                      name="province" v-validate data-vv-rules="required">
                      <option value="">Chọn Tỉnh/Thành phố</option>
                      <!-<option :value="province.id" v-for="(province, index) in html.data.province.list" :key="index">{{ province.name }}</option> -->

                    <!-- </select> -->
                    <vue-select
                      label="name"
                      placeholder="Chọn tỉnh..."
                      :options="html.data.province.list"
                      v-model="obj.student.province"
                      :searchable="true"
                      language="en-US"
                      :onChange="getDistrict"
                    ></vue-select>
                    <span v-show="errors.has('province')" class="error-inform line">
                      <i v-show="errors.has('province')" class="fa fa-warning"></i>
                      <span v-show="errors.has('province')" class="error help is-danger">Tỉnh thành là bắt buộc!</span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Đối tượng khách hàng</label>
                    <select class="form-control" v-model="obj.student.type" name="type">
                      <option value="">Chọn đối tượng khách hàng</option>
                      <option value="0">Thường</option>
                      <option value="1">VIP</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Địa chỉ<span class="text-danger"> (*)</span></label>
                    <input class="form-control" type="text" v-model="obj.student.address" name="address" v-validate="'required'">
                    <span v-show="errors.has('address')" class="error-inform line">
                      <i v-show="errors.has('address')" class="fa fa-warning"></i>
                      <span v-show="errors.has('address')" class="error help is-danger">Địa chỉ là bắt buộc</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Quận/Huyện</label>
                    <select class="form-control" v-model="obj.student.district_id">
                      <option value="" disabled>Chọn Quận/Huyện</option>
                      <option :value="district.id" v-for="(district, index) in html.data.district.list" :key="index">{{ district.name }}</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Facebook</label>
                    <input class="form-control" type="text" v-model="obj.student.facebook">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Trường học<span class="text-danger"> (*)</span></label>
                    <input class="form-control" type="text" v-model="obj.student.school" name="school" v-validate="'required'">
                    <span v-show="errors.has('school')" class="error-inform line">
                      <i v-show="errors.has('school')" class="fa fa-warning"></i>
                      <span v-show="errors.has('school')" class="error help is-danger">Trường học là bắt buộc</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                  <label class="control-label">Độ tuổi<span class="text-danger"> (*)</span></label>
                  <select class="form-control" id="" v-model="obj.student.school_grade"
                    name="school_grade">
                    <option :value="school_grade.class_id" v-for="(school_grade, index) in html.data.school_grade.list" :key="index">{{ school_grade.class_name }}</option>
                  </select>
                    <span v-show="errors.has('school_grade')" class="error-inform line">
                      <i v-show="errors.has('school_grade')" class="fa fa-warning"></i>
                      <span v-show="errors.has('school_grade')" class="error help is-danger">Độ tuổi là bắt buộc</span>
                    </span>
                </div>
              </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label class="control-label" v-b-tooltip.hover title="Nguồn tiếp nhận học sinh là bắt buộc">Từ Nguồn <span class="text-danger"> (*)</span></label>

                        <select class="form-control" v-model="obj.student.source" name="source" @change="selectOther">
                          <option value="">Chọn nguồn từ</option>
                          <option :value="source.id" v-for="(source, i) in html.data.sources.list" :key="i">{{ source.name }}</option>
                        </select>
                        <span v-show="errors.has('source')" class="error-inform line">
                          <i v-show="errors.has('source')" class="fa fa-warning"></i>
                          <span v-show="errors.has('source')" class="error help is-danger">Nguồn tiếp nhận học sinh là bắt buộc!</span>
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-8">
                      <div class="form-group">
                        <label class="control-label">Ghi chú</label>
                        <input class="form-control" type="text" v-model="obj.student.note">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="control-label" v-b-tooltip.hover title="Khi nhập vào mã CMS hoặc LMS hệ thống sẽ tự động tìm kiếm">Mã CMS của anh/chị em học cùng</label>
                        <input :readonly="(html.data.sibling.id) ? true :false" class="form-control" type="text" placeholder="Nhập mã CMS của anh chị em học cùng" @change="inputSibling" v-model="obj.student.sibling_id" >
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="control-label">Tên anh/chị/em học cùng</label>
                        <input class="form-control" type="text" v-model="html.data.sibling.name" readonly>
                      </div>
                    </div>
                    <div class="col-sm-6" :class="html.dom.ctv.display">
                      <div class="form-group">
                        <label class="control-label">Mã cộng tác viên</label>
                        <vue-select
                                label="name"
                                placeholder="Chọn mã CTV..."
                                v-model="obj.student.ref_code"
                                :options="html.data.collaborator.list"
                                :searchable="true"
                                :onChange="getDetail"
                        ></vue-select>
                      </div>
                    </div>
                    <div class="col-sm-6" :class="html.dom.ctv.display">
                      <div class="form-group">
                        <label class="control-label">Thông tin CTV</label>
                        <textarea readonly
                                  class="description form-control" rows="8" style="min-height: 115px;background: #f4f9ff; border: 1px solid #e1edff;"
                                  :value="obj.ref_info"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
        </b-card>
        <br/>
        <b-card header>
          <div slot="header">
            <strong>Thông tin phụ huynh</strong>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
            </div>
          </div>
          <div class="panel main-content">
            <div class="panel-body">
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label" v-b-tooltip.hover title="Phụ huynh 1 của học viên là thông tin bắt buộc">Phụ huynh 1 <span class="text-danger"> (*)</span></label>
                      <input class="form-control"
                              type="text"
                              @input="upercaseNameGud1"
                              v-validate="'required'"
                              v-model="obj.student.gud_name1"
                              name="gud_name1">
                      <span v-show="errors.has('gud_name1')" class="error-inform line">
                        <i v-show="errors.has('gud_name1')" class="fa fa-warning"></i>
                        <span v-show="errors.has('gud_name1')" class="error help is-danger">Phụ huynh 1 là bắt buộc</span>
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label class="control-label" v-b-tooltip.hover title="Số điện thoại của phụ huynh học viên là thông tin bắt buộc">Số điện thoại <span class="text-danger"> (*)</span></label>
                      <input class="form-control"
                             :readonly="(html.data.sibling.id) ? true :false"
                              type="text"
                              v-validate="'required'"
                              name="gud_mobile1"
                              v-model="obj.student.gud_mobile1" @input="validParentPhone1">
                      <span v-show="errors.has('gud_mobile1')" class="error-inform line">
                        <i v-show="errors.has('gud_mobile1')" class="fa fa-warning"></i>
                        <span v-show="errors.has('gud_mobile1')" class="error help is-danger">Số điện thoại của phụ huynh là bắt buộc, phải bắt đầu bằng số 0 và có từ 10 số</span>

                      </span>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label class="control-label" v-b-tooltip.hover title="Địa chỉ email của phụ huynh học viên">Email <span class="text-danger"></span></label>
                      <input class="form-control" type="text" v-model="obj.student.gud_email1" name="gud_email1" v-validate="'required|email'">
<!--                      <span v-show="errors.has('gud_email1')" class="error-inform line">-->
<!--                        <i v-show="errors.has('gud_email1')" class="fa fa-warning"></i>-->
<!--                        <span v-show="errors.has('gud_email1')" class="error help is-danger">Email của phụ huynhlà bắt buộc</span>-->
<!--                      </span>-->
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label class="control-label">Ngày sinh</label>
                      <datePicker
                        id="gud1-birth-day"
                        class="form-control calendar"
                        v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('gud_birth_day1') }"
                        :value="obj.student.gud_birth_day1"
                        v-model="obj.student.gud_birth_day1"
                        placeholder="Chọn ngày sinh phụ huynh 1"
                        lang="lang"
                      >
                      </datePicker>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Ngành nghề/ Cơ quan công tác</label>
                      <input class="form-control" type="text" v-model="obj.student.gud_card1" >
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Phụ huynh 2</label>
                      <input class="form-control" type="text" @input="upercaseNameGud2" v-model="obj.student.gud_name2">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label class="control-label">Số điện thoại</label>
                      <input class="form-control" type="text" v-model="obj.student.gud_mobile2" @input="validParentPhone2">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label class="control-label">Email</label>
                      <input class="form-control" type="text" v-model="obj.student.gud_email2">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label class="control-label">Ngày sinh</label>
                      <datePicker
                        id="gud2-birth-day"
                        class="form-control calendar"
                        v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('gud_birth_day2') }"
                        :value="obj.student.gud_birth_day2"
                        v-model="obj.student.gud_birth_day2"
                        placeholder="Chọn ngày sinh phụ huynh 2"
                        lang="lang"
                      >
                      </datePicker>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Ngành nghề/ Cơ quan công tác</label>
                      <input class="form-control" type="text" v-model="obj.student.gud_card2" >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <br/>
        <b-card header>
          <div slot="header">
            <strong>Thông tin trung tâm</strong>
          </div>
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
            </div>
          </div>
          <div class="panel main-content">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4" :class="html.dom.branch.display">
                  <div class="form-group">
                    <label class="control-label">Trung tâm</label>
                    <multiselect
                      v-model="obj.student.branch"
                      :options="html.data.branch.list"
                      :clear-on-select="false"
                      :close-on-select="true"
                      :searchable="true"
                      placeholder="Chọn một trung tâm"
                      track-by="name"
                      :preselect-first="html.dom.branch.selectFirst"
                      label="name"
                      @select="getUsersByBranch"
                    >
                    </multiselect>
                  </div>
                </div>
                <div class="col-sm-4" :class="html.dom.ec.display">
                  <div class="form-group">
                    <label class="control-label">EC<span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.ec_id" :disabled="html.dom.ec.disabled" name="ec" v-validate data-vv-rules="required">
                      <option :value="ec.ec_id" v-for="(ec, index) in html.data.ec.list" :key="index">{{ ec.ec_name }}</option>
                    </select>
                    <span v-show="errors.has('ec')" class="error-inform line">
                      <i v-show="errors.has('ec')" class="fa fa-warning"></i>
                      <span v-show="errors.has('ec')" class="error help is-danger">Thông tin EC là bắt buộc!</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4" :class="html.dom.cm.display">
                  <div class="form-group">
                    <label class="control-label">CS<span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.cm_id" :disabled="html.dom.cm.disabled" name="cm" v-validate data-vv-rules="required">
                      <option :value="cs.cm_id" v-for="(cs, index) in html.data.cm.list" :key="index">{{ cs.cm_name }}</option>
                    </select>
                    <span v-show="errors.has('cm')" class="error-inform line">
                      <i v-show="errors.has('cm')" class="fa fa-warning"></i>
                      <span v-show="errors.has('cm')" class="error help is-danger">Thông tin CS là bắt buộc!</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="inner-button-bars">
                    <abt
                      :markup="html.dom.button.save.markup"
                      :icon="html.dom.button.save.icon"
                      :label="html.dom.button.save.label"
                      :title="html.dom.button.save.title"
                      :disabled="html.dom.button.save.disabled"
                      :onClick="html.dom.button.save.action"
                      >
                    </abt>
                    <abt
                      :markup="html.dom.button.reset.markup"
                      :icon="html.dom.button.reset.icon"
                      :label="html.dom.button.reset.label"
                      :title="html.dom.button.reset.title"
                      :disabled="html.dom.button.reset.disabled"
                      :onClick="html.dom.button.reset.action"
                      >
                    </abt>
                    <abt
                      :markup="html.dom.button.cancel.markup"
                      :icon="html.dom.button.cancel.icon"
                      :label="html.dom.button.cancel.label"
                      :title="html.dom.button.cancel.title"
                      :disabled="html.dom.button.cancel.disabled"
                      :onClick="html.dom.button.cancel.action"
                      >
                    </abt>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </b-card>
        <b-modal :title="html.dom.modal.title" :class="html.dom.modal.class" size="md" v-model="html.dom.modal.display" @ok="html.dom.modal.done" ok-variant="primary">
          <div v-html="html.dom.modal.message">
          </div>
        </b-modal>
        <br/>
      </div>
    </div>
  </div>
</template>

<script>

import u from '../../utilities/utility'
import inp from '../../components/Input'
import abt from '../../components/Button'
import file from '../../components/File'
import datePicker from 'vue2-datepicker'
import multiselect from 'vue-multiselect'
import select from "vue-select";

export default {

  data(){
    const model = u.m('student').page
		model.html.dom = {
      page: {
        action: () => this.checkRequired()
      },
      modal: {
        display: false,
        title: 'Thông Báo',
        class: 'modal-success',
        message: '',
        done: () => this.callback()
      },
      branch: {
        display: '',
        disabled: true,
        selectFirst: true,
        placeholder: 'Chọn một trung tâm'
      },
      ec: {
        display: 'hidden',
        disabled: true
      },
      cm: {
        display: 'hidden',
        disabled: true
      },
      ctv: {
        display: 'hidden',
        disabled: true
      },
      button: {
        save: {
          label: 'Lưu',
          icon: 'fa-save',
          title: 'Lưu thông tin học sinh mới',
          markup: 'success',
          disabled: true,
          action: () => this.saveForm()
        },
        reset: {
          label: 'Hủy',
          icon: 'fa-recycle',
          title: 'Nhập lại nội dung thông tin',
          markup: 'error',
          disabled: true,
          action: () => this.resetForm()
        },
        cancel: {
          label: 'Thoát',
          icon: 'fa-sign-out',
          title: 'Thoát form thêm học sinh',
          markup: 'warning',
          disabled: false,
          action: () => this.exitForm()
        }
      }
    }
    model.obj = {
      student: {
        crm_id: '',
        name: '',
        type: 0,
        date_of_birth: '',
        phone: '',
        email: '',
        gender: '',
        school: '',
        address: '',
        job_title: '',
        province: null,
        province_id: '',
        district_id: '',
        school_grade: '',
        gud_mobile1: '',
        gud_name1: '',
        gud_email1: '',
        gud_card1: '',
        gud_birth_day1: '',
        gud_mobile2: '',
        gud_name2: '',
        gud_email2: '',
        gud_card2: '',
        gud_birth_day2: '',
        avatar: {},
        branch: {},
        branch_id: '',
        ec_id: '',
        cs_id: '',
        cm_id: '',
        source: 1,
        tracking: 1,
        facebook: '',
        checked: true,
        sibling_id: '',
        attached_file: {},
        temp_id: 0,
        ref_code: null,
      },
        temp:{},
        ref_info:'',
		}
		model.html.data = {
      sources: {
        item: '',
        list: []
      },
      school_grade: {
        item: '',
        list: []
      },
      province: {
        item: '',
        list: []
      },
      district: {
        item: '',
        list: []
      },
      branch: {
        item: '',
        list: []
      },
      ec: {
        item: '',
        list: []
      },
      cm: {
        item: '',
        list: []
      },
      sibling: {
        id: '',
        name: ''
      },
      collaborator: {
        item: '',
        list: []
      },
    }
    return model
  },

  components: {
    inp,
    abt,
    file,
    datePicker,
    multiselect,
    "vue-select": select
  },

  created(){
    if (this.$route.query.temp_id){
        u.g(`/api/student-temp/${this.$route.query.temp_id}`)
            .then(response => {
                this.obj.temp = response.data
                this.useTemp(this.obj.temp)
            })
    }
    if (u.authorized() || parseInt(_.get(this.session, 'user.role_id', 0)) === 80) {//role telesale
      this.obj.student.job_title = this.session.user.title_id
      this.html.data.school_grade.list = this.session.info.school_grades
      this.html.data.school_grade.item = ''
      this.html.data.branch.list = this.session.user.branches

      this.html.dom.ec.display = ''
      this.html.dom.cm.display = ''
      this.html.dom.branch.display = ''
      this.html.data.ec.list = this.session.info.ecslist
      this.html.data.cm.list = this.session.info.cmslist
      this.html.loading.action = false
      if (this.session.user.branches.length == 1) {
        this.html.dom.branch.selectFirst = true;
        this.html.dom.branch.display = 'hidden';
        this.getUsersByBranch(this.session.user.branches[0]);
      }

    } else {
      u.g('/api/students/get/ec/of/users/branch')
      .then(data => {
        this.obj.student.job_title = data.title_id
        this.html.data.school_grade.list = data.school_grades
        this.html.data.school_grade.item = ''
        this.html.data.branch.list = data.branches;
        this.obj.student.branch_id = parseInt(data.branch_id) > 0 ? data.branch_id : ''
        this.html.loading.action = false
        switch (data.title_id) {
          case 1 : {
            this.obj.student.branch_id = ''
            this.html.dom.branch.display = ''
            this.html.dom.ec.display = ''
            this.html.dom.branch.disabled = false
          } break
          case 2 : {
            this.html.data.ec.list = data.ecs
            this.html.data.cm.list = data.cms
            this.obj.student.ec_id = data.ec_id
            this.html.dom.cm.display = ''
            this.html.dom.cm.disabled = false
          } break
          case 3 : {
            this.html.data.ec.list = data.ecs
            this.obj.student.ec_id = ''
            this.html.dom.ec.display = ''
            this.html.data.ec.item = ''
            this.html.dom.ec.disabled = false
            this.html.data.cm.list = data.cms
            this.html.dom.cm.display = ''
            this.html.dom.cm.disabled = false
          } break
          case 4 : {
            this.html.dom.ec.display = ''
            this.html.dom.cm.display = ''
            this.html.data.ec.list = data.ecs
            this.html.data.cm.list = data.cms
            // this.html.data.ec.item = data.ec_id
            // this.obj.student.ec_id = data.ec_id
            // this.html.data.cm.item = data.cm_id
            // this.obj.student.cm_id = data.cm_id

            this.html.dom.ec.disabled = false
            this.html.dom.cm.disabled = false
          } break
        }
        if (data.branches.length == 1) {
          this.html.dom.branch.selectFirst = true;
          this.html.dom.branch.display = 'hidden'
          this.getUsersByBranch(data.branches[0]);
        }
      })
    }
    u.g(`/api/provinces`)
      .then(response => {
      this.html.data.province.list = response
    })
    u.g(`/api/collaborator/list`)
      .then(response => {
        this.html.data.collaborator.list = response
    })
    u.g(`/api/sources`)
      .then(response => {
        this.html.data.sources.list = response
      })
  },
  methods: {
    useTemp(data){
          this.obj.student.ec_id = data.student.ec_id
          this.obj.student.gender = data.student.gender
          this.obj.student.name = data.student.name
          this.obj.student.gud_name1 = data.student.gud_name1
          this.obj.student.gud_mobile1 = data.student.gud_mobile1
          this.obj.student.gud_email1 = data.student.gud_email1
          if (data.student.date_of_birth != "1970-01-01")
              this.obj.student.date_of_birth = new Date(data.student.date_of_birth)

          if (!u.authorized() || parseInt(_.get(this.session, 'user.role_id', 0)) != 80) {
              let branchObj = {"id": data.student.branch_id, "name": data.student.branch_name}
              this.obj.student.branch = branchObj
              this.getUsersByBranch(branchObj)
          }
    },
    callback(data) {
      this.html.dom.modal.display = false
      if (this.expired) {
        u.go(this.$router, '/login')
      }
      if (this.completed) {
        if (data.id) {
          u.go(this.$router, `/contracts/${data.id}/create`)
        } else {
          u.go(this.$router, '/students/list/1')
        }
      }
    },
    onDrawDate (e) {
      let date = e.date
      if (new Date().getTime() > date.getTime()) {
        e.allowSelect = false
      }
    },
    getUsersByBranch(obj) {
      this.obj.student.branch_id = obj.id
      const branchId = obj.id
      if (typeof (branchId) != 'undefined' && branchId ) {
        u.g(`/api/students/get/ec/of/a/branch/${branchId}`).then(data => {
          if (data.ecs.length) {
            this.html.data.ec.list = data.ecs
            this.html.data.cm.list = data.cms
            this.html.data.ec.item = ''
            this.html.dom.ec.disabled = false
            this.html.data.cm.item = ''
            this.html.dom.cm.disabled = false
            this.html.dom.cm.display = ''
          }
        })
      }
    },
    selectBirthDay(date) {
      this.obj.student.date_of_birth = date
    },
    saveForm(){
      //this.checkRequired();
      this.inputSibling()
      const condition = {
        name: this.obj.student.name,
        parent: this.obj.student.gud_name1,
        mobile: this.obj.student.gud_mobile1,
        mobile2: this.obj.student.gud_mobile2,
        branch_id: u.session().user.role_id == 999999999 ? this.obj.student.branch_id : u.session().user.branch_id,
        sibling:this.html.data.sibling.id
      }
      this.checkDublicateStudent(condition, true)
    },
    reject(msg) {
      this.html.dom.modal.message = `${msg}`
      // this.obj.student.name = ''
      // this.obj.student.gud_name1 = ''
      // this.obj.student.gud_mobile1 = ''
      this.html.dom.modal.title = 'Cảnh Báo'
      this.html.dom.modal.class = 'modal-primary'
      this.html.dom.modal.display = true
    },
    resetForm() {
      this.obj.student = u.rs(this.obj.student)
      this.obj.student.checked = true
      this.html.dom.button.save.disabled = true
      this.html.dom.button.reset.disabled = true
    },
    exitForm() {
      this.$router.push('/students')
    },
    uploadFile(file, param = null) {
      if (param) {
        this.obj.student[param] = file
      }
      // u.log('DATA OK', this.obj.student)
    },
    selectOther() {
      if (this.obj.student.source == 28)
        this.html.dom.ctv.display = ''
      else{
        this.html.dom.ctv.display = 'hidden'
        this.obj.student.ref_code = null
        this.obj.ref_info= null
      }

    },
    validateInput() {
      let validated = true
      let alert_msg = ''
      if (this.obj.student.name == '') {
        validated = false
        alert_msg+= '(*) Tên học viên là bắt buộc<br/>'
      }
      if(this.obj.student.phone) {
        if (this.obj.student.phone.length < 10 || this.obj.student.phone.length > 10) {
          validated = false
          alert_msg+= '(*) Số điện thoại học viên chỉ được dài từ 10 chữ số<br/>'
        }
      }
      if (this.obj.student.address == '') {
        validated = false
        alert_msg+= '(*) Địa chỉ là bắt buộc<br/>'
      }
      if (this.obj.student.school_grade == '') {
        validated = false
        alert_msg+= '(*) Độ tuổi là bắt buộc<br/>'
      }
      if (this.obj.student.date_of_birth == '' || this.obj.student.date_of_birth == 'Invalid date') {
        validated = false
        alert_msg+= '(*) Ngày sinh là bắt buộc<br/>'
      }
      if (this.obj.student.gender == '') {
        validated = false
        alert_msg+= '(*) Giới tính là bắt buộc<br/>'
      }
      if (this.obj.student.school == '') {
        validated = false
        alert_msg+= '(*) School là bắt buộc<br/>'
      }
      if (this.obj.student.province_id == '') {
        validated = false
        alert_msg+= '(*) Tỉnh thành phố là bắt buộc<br/>'
      }
      // if (this.obj.student.email == '') {
      //   validated = false
      //   alert_msg+= '(*) Email là bắt buộc<br/>'
      // }
      // if (this.obj.student.phone == '') {
      //   validated = false
      //   alert_msg+= '(*) Số điện thoại học viên là bắt buộc<br/>'
      // }
      if (this.obj.student.gud_name1 == '') {
        validated = false
        alert_msg+= '(*) Tên phụ huynh là bắt buộc<br/>'
      }
      if (this.obj.student.gud_mobile1 == '') {
        validated = false
        alert_msg+= '(*) Số điện thoại phụ huynh là bắt buộc<br/>'
      }
      else{
        if (this.obj.student.gud_mobile1.length < 10 || this.obj.student.gud_mobile1.length > 10) {
          validated = false
          alert_msg+= '(*) Số điện thoại phụ huynh chỉ được dài từ 10 chữ số<br/>'
        }
        if (this.obj.student.gud_mobile1.substr(0, 1) !== '0' || this.obj.student.gud_mobile1.substr(0, 2) == '00') {
          validated = false
          alert_msg+= '(*) Số điện thoại  phụ huynh phải bắt đầu = số 0 và khác 00<br/>'
        }
      }
      if (this.obj.student.gud_mobile1 == this.obj.student.gud_mobile2) {
        validated = false
        alert_msg+= '(*) Số điện thoại phụ huynh 2 không hợp lệ<br/>'
      }
      if (this.obj.student.source == '') {
        validated = false
        alert_msg+= '(*) Nguồn tiếp nhận học sinh là bắt buộc<br/>'
      }
      if (this.obj.student.ec_id === undefined || this.obj.student.ec_id.toString() === '' || parseInt(this.obj.student.ec_id, 10) === 0) {
        validated = false
        alert_msg+= '(*) Thông tin EC là bắt buộc<br/>'
      }
      if (this.obj.student.cm_id === undefined || this.obj.student.cm_id.toString() === '' || parseInt(this.obj.student.cm_id, 10) === 0) {
        validated = false
        alert_msg+= '(*) Thông tin CS là bắt buộc<br/>'
      }
      if (validated == false) {
        alert_msg = `Dữ liệu học sinh chưa hợp lệ:<br/>-----------------------------------<br/><br/><p class="text-danger">${alert_msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
        this.html.dom.modal.message = alert_msg
        this.html.dom.modal.title = 'Cảnh Báo'
        this.html.dom.modal.class = 'modal-warning'
        this.html.dom.modal.display = true
      }
      return validated
    },
    checkRequired() {
      if (this.obj.student.name != '' && this.obj.student.date_of_birth != ''
        && this.obj.student.gender != '' && this.obj.student.province_id != ''
        // && this.obj.student.email != '' && this.obj.student.phone != ''
        && (this.title != 2 && this.obj.student.ec_id != '')
        && this.obj.student.gud_name1 != '' && this.obj.student.gud_mobile1 != '') {
          this.html.dom.button.save.disabled = false
          this.html.dom.button.reset.disabled = false
      }

      if (this.obj.student.name != '' && this.obj.student.gud_name1 != '' && this.obj.student.gud_mobile1 != '') {
        // const condition = {
        //   name: this.obj.student.name,
        //   parent: this.obj.student.gud_name1,
        //   mobile: this.obj.student.gud_mobile1,
        //   mobile2: this.obj.student.gud_mobile2,
        //   branch_id: this.obj.student.branch_id
        // }
        //
        // this.checkDublicateStudent(condition)
      }
    },
    store() {
      if (this.validateInput() == true) {
        this.html.loading.action = true
        let confirmed = true
        if (parseInt(this.obj.student.type) === 1) {
          confirmed = confirm('Học sinh này đang được đặt là VIP, bạn có chắc thông tin này là chính xác không?')
        }
        if (confirmed) {
          if (!this.obj.student.ref_code){
            u.apax.$emit('apaxPopup', {
              on: true,
              content: `Chưa chọn cộng tác viên, bạn có chắc chắn muốn lưu thông tin học sinh ? <br/> Nhấn  <b>OK</b> để tiếp tục <b>Lưu</b> <br/>hoặc nhấn <b>x</b> để quay lại chọn cộng tác viên.`,
              title: 'Thông báo',
              class: 'modal-success',
              size: 'md',
              hidden: () => { this.handlerCallback() },
              confirm: {
                  dark: {
                    button: 'HỦY',
                    action: () => { this.handlerCallback() },
                  },
                  primary: {
                    button: 'OK',
                    action: () => { this.goToNextStep()},
                  }
              },
              variant: 'apax-ok'
            })
          }
          else{
            this.goToNextStep()
          }
        }
      }
    },
    imageChanged(e) {
      var fileReader = new FileReader();
      fileReader.readAsDataURL(e.target.files[0])
      fileReader.onload = (e) => {
        this.student.avatar = e.target.result
      }
    },
    fileChanged(e) {
      const fileReader = new FileReader();
      const fileName = e.target.value.split( '\\' ).pop();
      $('#atch-file').html(fileName)
      fileReader.readAsDataURL(e.target.files[0])
      fileReader.onload = (e) => {
        this.student.attached_file = e.target.result
      }
    },
    validName() {
      this.obj.student.name = u.upperWord(this.obj.student.name)
    },
    validPhone() {
      this.obj.student.phone = u.onlynum(this.obj.student.phone)
    },
    validParentPhone1() {
      this.obj.student.gud_mobile1 = u.onlynum(this.obj.student.gud_mobile1)
    },
    alertMessage(message, type) {
        switch (type) {
          case 'warning':
            this.html.dom.modal.class = 'modal-warning';
            this.html.dom.modal.title = 'Cảnh Báo';
            break;
          case 'error':
            this.html.dom.modal.class = 'modal-danger';
            this.html.dom.modal.title = 'Lỗi'
            break;
          default:
            this.html.dom.modal.class = 'modal-primary';
            this.html.dom.modal.title = 'Thông Báo'
            break;
        }
        this.html.dom.modal.message = message;
      this.html.dom.modal.display = true;
    },
    validParentPhone2() {
      this.obj.student.gud_mobile2 = u.onlynum(this.obj.student.gud_mobile2)
    },
    validParentCard1() {
      this.obj.student.gud_card1 = u.onlynum(this.obj.student.gud_card1)
    },
    validParentCard2() {
      this.obj.student.gud_card2 = u.onlynum(this.obj.student.gud_card2)
    },
    validNick() {
      this.obj.student.nick = u.uniless(this.obj.student.nick)
    },
    upercaseNameGud1() {
      this.obj.student.gud_name1 = u.ufc(this.obj.student.gud_name1)
    },
    upercaseNameGud2() {
      this.obj.student.gud_name2 = u.ufc(this.obj.student.gud_name2)
    },
    getDistrict(data = null){
      if (data && typeof data === 'object') {
        const province_id = data.id
        this.obj.student.province = data
        this.obj.student.province_id = province_id
        u.g(`/api/provinces/${province_id}/districts`).then(response => {
          this.html.data.district.list = response
          this.obj.student.district_id = this.html.data.district.list[0].id
        })
      }
    },
    checkDublicateStudent(condition, save = false) {
      u.a().post(`/api/students/check`,condition).then(response => {
        if (parseInt(response.data.data.existed) > 0) {
          this.reject(response.data.data.msg)
        } else {
          if (save) {
            this.store()
          }
        }
      })
    },
    checkExistParentPhone(condition) {
      if (this.html.data.sibling.name == '') {
        u.a().get(`/api/students/check-phone-parent/${this.obj.student.gud_mobile1}`).then(response => {
          if (response.data.data == 1) {
            this.alertMessage('Số điện thoại của phụ huynh đã bị trùng trong cơ sở dữ liệu!', 'error');
          } else {
            this.checkDublicateStudent(condition, true)
          }
        })
      } else {
          // if(this.html.data.sibling.gud_mobile1 !=this.obj.student.gud_mobile1){
            //this.alertMessage('Số điện thoại của phụ huynh của anh chị em học cùng không giống nhau!', 'error');
          // }else{
              this.checkDublicateStudent(condition, true)
          // }
      }
    },
    inputSibling(){
      let crm_id = this.obj.student.sibling_id
      if (!isNaN(crm_id) || crm_id.length > 4) {
        // crm_id = !isNaN(crm_id) ? parseInt(crm_id) : crm_id.substring(3)
        u.g(`/api/students/sibling/${crm_id}`).then(response => {
          if (response && u.is.has(response, 'name')) {
            if (u.is.has(response, 'sibling_id') && parseInt(response.id) === 0) {
              this.obj.student.id = ''
            }
            this.html.data.sibling = response
            if (response.id >0){
              this.html.data.sibling.id = response.id
              this.obj.student.gud_mobile1 = response.gud_mobile1
            }
          }
        })
      }
    },
    getDetail(data = null){
      this.obj.student.ref_code = data
      let ref_detail = ''
      if (data && typeof data === 'object') {
        ref_detail = `- Mã: ${data.id}\n- CTV: ${data.school_name}\n- Địa chỉ: ${data.address}\n- Người đại diện: ${data.personal_name}\n- SĐT: ${data.phone_number}\n- Email: ${data.email}`
      }
      this.obj.ref_info = ref_detail
    },
    goToNextStep(){
      const that = this
      this.html.loading.action = true
      this.obj.student.job_title = this.job_title
      this.obj.student.temp_id = parseInt(this.$route.query.temp_id)
      this.obj.student.date_of_birth = this.moment(this.obj.student.date_of_birth, 'DD/MM/YYYY').format('YYYY-MM-DD'),
      u.p('/api/students/add', this.obj.student).then(response => {
        if (response.success) {
          that.completed = true
          that.html.loading.action = false
          u.apax.$emit('apaxPopup', {
            on: true,
            content: `Hồ sơ học viên mới:<br/><b>${response.success}</b><br/> đã được lưu thành công!`,
            title: 'Hồ Sơ Học Sinh Đã Được Lưu Thành Công',
            class: 'modal-success',
            size: 'md',
            hidden: () => { that.callback(response) },
            confirm: {
                primary: {
                  button: 'OK',
                  action: () => { that.callback(response) },
                }
            },
            variant: 'apax-ok'
          })
        }
      })
    },
    handlerCallback(){
      this.html.loading.action = false
    },
  }
}

</script>
<style scoped>

.error-inform {
  font-size: 10px;
}
.content-container{
  margin: 0px;
  padding: 0rem;
}
button .primary{
  background: green;
}
#page-content{
  margin-top: -10px;
  margin-left: -10px;
}
.panel{
  background: white;
}
.main-content{
  padding: 20px;
}
.panel-footer .button {
  margin-right: -50px;
}
#checked-box{
  margin-left: 20px;
}
.form-check{
  margin-left: 30px;
  margin-top: 30px;
}
.error{
  color: red;
}
.apax-form .hidden {
  display: none;
}
.btn-save{
  margin-left: 250px;
}
.button-reset-img{
  width: 60px;
  height: 25px;
  font-size: 12px;
  margin-left: 178px;
}
.button-reset-file{
  width: 60px;
  height: 25px;
  font-size: 12px;
  margin-left: 172px;
}
.text-small{
  color: white;
  text-align: center;
}
.wrapinfor{
  margin-top:4%;
}
</style>
