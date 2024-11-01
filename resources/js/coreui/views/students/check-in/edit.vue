<template>
  <div class="animated fadeIn apax-form" id="add-new-student" @click="html.dom.page.action">
    <div class="row">
      <div class="col-lg-12">
        <b-card header-tag="header">
          <div slot="header">
            <i class="fa fa-file-text"></i> <b class="uppercase"> Edit checkin</b>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">
                <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <loader :active="processing" :duration="duration" />
          <div class="panel main-content">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Mã CMS không được trùng">Mã CMS </label>
                    <input class="form-control" v-model="obj.student.cms_id" disabled />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Họ tên của học viên">Tên học sinh <span class="text-danger"> (*)</span></label>
                    <div class="row">
                      <div class="col-sm-4 no-right-padding">
                          <input class="form-control" type="text" placeholder="Họ" v-validate="'required'" v-model="obj.student.firstname" name="gud_firstname1" maxlength="200" @change="genStudentName">
                      </div>
                      <div class="col-sm-4 no-padding">
                          <input class="form-control" type="text" placeholder="Đệm" v-model="obj.student.midname" name="gud_midname1" maxlength="200" @change="genStudentName">
                      </div>
                      <div class="col-sm-4 no-left-padding">
                          <input class="form-control" type="text" placeholder="Tên" v-validate="'required'" v-model="obj.student.lastname" name="gud_lastname1" maxlength="200" @change="genStudentName">
                      </div>
                    </div>
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
                        @change="selectBirthDay"
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
                    <label class="control-label" v-b-tooltip.hover title="Thông tin tỉnh thành của học viên là bắt buộc">Tỉnh/Thành phố<span class="text-danger"> (*)</span></label>
                    <vue-select
                      label="name"
                      placeholder="Chọn Tỉnh/Thành Phố"
                      :options="html.data.province.list"
                      v-model="obj.student.province"
                      :searchable="true"
                      language="tv-VN"
                      :onChange="getDistrict"
                    ></vue-select>
                    <span v-show="errors.has('province')" class="error-inform line">
                      <i v-show="errors.has('province')" class="fa fa-warning"></i>
                      <span v-show="errors.has('province')" class="error help is-danger">Tỉnh thành là bắt buộc!</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Thông tin Quận Huyện của học viên">Quận/Huyện <span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.district_id"
                            name="district_id" v-validate data-vv-rules="required">
                      <option value="">Chọn quận/huyện</option>
                      <option :value="district.id" v-for="(district, index) in html.data.district.list" :key="index">{{ district.name }}</option>
                    </select>
                    <span v-show="errors.has('district_id')" class="error-inform line">
                      <i v-show="errors.has('district_id')" class="fa fa-warning"></i>
                      <span v-show="errors.has('district_id')" class="error help is-danger">Quận/Huyện là bắt buộc!</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Địa chỉ<span class="text-danger"> (*)</span></label>
                    <input class="form-control" type="text" v-model="obj.student.address" name="address" v-validate="'required'" maxlength="200">
                    <span v-show="errors.has('address')" class="error-inform line">
                      <i v-show="errors.has('address')" class="fa fa-warning"></i>
                      <span v-show="errors.has('address')" class="error help is-danger">Địa chỉ là bắt buộc</span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Cấp trường<span class="text-danger"> (*)</span></label>
                        <select class="form-control" id="" v-model="obj.student.school_level" name="school_level" v-validate data-vv-rules="required" @change="getSchools($event)" :disabled="obj.student.district_id == ''">
                            <option value="" disabled>Chọn cấp trường</option>
                            <option value="Mẫu giáo">Mẫu giáo</option>
                            <option value="Tiểu học">Tiểu học</option>
                        </select>
                        <span v-show="errors.has('school_level')" class="error-inform line">
                        <i v-show="errors.has('school_level')" class="fa fa-warning"></i>
                        <span v-show="errors.has('school_level')" class="error help is-danger">Cấp trường là bắt buộc!</span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Trường học<span class="text-danger"> (*)</span></label>
                        <select class="form-control" id="" v-model="obj.student.select_school" name="select_school" v-validate data-vv-rules="required" @change="selectSchool" :disabled="obj.student.district_id == ''">
                            <option :value="school.name" v-for="(school, index) in html.data.schools.list" :key="index">{{ school.name }}</option>
                            <option value="Other">Khác...</option>
                        </select>
                        <input
                            v-show="obj.student.select_school == 'Other' || (Array.isArray(html.data.schools.list) && html.data.schools.list.length == 0)"
                            class="form-control input-top"
                            type="text"
                            v-model="obj.student.school"
                            placeholder="Nhập tên trường học"
                            maxlength="200"
                             :disabled="obj.student.district_id == ''"
                             @input="validShoolName"
                        >
                        <div class="the-last-button" id="hide-school" v-show="obj.student.select_school == 'Other'" @click="deInputSchool">x</div>
                    </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Tên phụ huynh của học viên là thông tin bắt buộc">Họ tên phụ huynh 1 <span class="text-danger"> (*)</span></label>
                    <div class="row">
                      <div class="col-sm-3 no-right-padding">
                        <select class="form-control" v-model="obj.student.gud_gender1"
                                name="gender" v-validate data-vv-rules="required" >
                          <option value=""></option>
                          <option value="M">Ông</option>
                          <option value="F">Bà</option>
                        </select>
                      </div>
                      <div class="col-sm-3 no-padding">
                          <input class="form-control" type="text" placeholder="Họ" v-validate="'required'" v-model="obj.student.gud_firstname1" name="gud_firstname1" maxlength="200" @change="genGudName1">
                      </div>
                      <div class="col-sm-3 no-padding">
                          <input class="form-control" type="text" placeholder="Đệm" v-model="obj.student.gud_midname1" name="gud_midname1" maxlength="200" @change="genGudName1">
                      </div>
                      <div class="col-sm-3 no-left-padding">
                          <input class="form-control" type="text" placeholder="Tên" v-validate="'required'" v-model="obj.student.gud_lastname1" name="gud_lastname1" maxlength="200" @change="genGudName1">
                      </div>
                      <span v-show="errors.has('gud_name1')" class="error-inform line">
                              <i v-show="errors.has('gud_name1')" class="fa fa-warning"></i>
                              <span v-show="errors.has('gud_name1')" class="error help is-danger">Tên của phụ huynh là bắt buộc</span>
                          </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Số điện thoại của phụ huynh học viên là thông tin bắt buộc">Số điện thoại <span class="text-danger"> (*)</span></label>
                    <input class="form-control"
                           :readonly="((html.data.sibling.id >0 || obj.student.sibling_crm_id) ||1==1) ? true :false"
                           type="text"
                           v-validate="{ required: true, regex:/^0[0-9]{8,11}$/ }"
                           name="gud_mobile1"
                           v-model="obj.student.gud_mobile1" @input="validParentPhone1">
                    <span v-show="errors.has('gud_mobile1')" class="error-inform line">
                        <i v-show="errors.has('gud_mobile1')" class="fa fa-warning"></i>
                        <span v-show="errors.has('gud_mobile1')" class="error help is-danger">Số điện thoại là bắt buộc, phải bắt đầu bằng số 0 và có từ 9 - 12 số</span>
                      </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Ngày sinh phụ huynh 1 <span class="text-danger"> (*)</span></label>
                    <datePicker
                      id="gud1-birth-day"
                      class="form-control calendar"
                      v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('gud_birth_day1') }"
                      :value="obj.student.gud_birth_day1"
                      v-model="obj.student.gud_birth_day1"
                      placeholder="Chọn ngày sinh phụ huynh 1"
                      lang="lang"
                      @change="selectGudBirthDay1"
                    >
                    </datePicker>
                  </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label" >Nghề nghiệp phụ huynh 1 <span class="text-danger"> (*)</span></label>
                      <vue-select 
                          label="name" 
                          placeholder="Chọn Nghề nghiệp"
                          :options="html.data.jobs.list" 
                          v-model="obj.student.job1" 
                          :searchable="true"
                          language="tv-VN"
                          :onChange="saveJob1"
                      ></vue-select>
                    </div>
                  </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" >Họ tên phụ huynh 2 </label>
                    <div class="row">
                      <div class="col-sm-3 no-right-padding">
                        <select class="form-control" v-model="obj.student.gud_gender2"
                                name="gender" v-validate data-vv-rules="required" >
                          <option value=""></option>
                          <option value="M">Ông</option>
                          <option value="F">Bà</option>
                        </select>
                      </div>
                      <div class="col-sm-3 no-padding">
                          <input class="form-control" type="text" placeholder="Họ" v-validate="'required'" v-model="obj.student.gud_firstname2" name="gud_firstname1" maxlength="200" @change="genGudName2">
                      </div>
                      <div class="col-sm-3 no-padding">
                          <input class="form-control" type="text" placeholder="Đệm" v-model="obj.student.gud_midname2" name="gud_midname1" maxlength="200"  @change="genGudName2">
                      </div>
                      <div class="col-sm-3 no-left-padding">
                          <input class="form-control" type="text" placeholder="Tên" v-validate="'required'" v-model="obj.student.gud_lastname2" name="gud_lastname1" maxlength="200" @change="genGudName2">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Số điện thoại phụ huynh 2</label>
                    <input class="form-control"
                           :readonly="((html.data.sibling.id >0 || obj.student.sibling_crm_id) ||1==1) ? true :false"
                           type="text"
                           v-validate="{ required: true, regex:/^0[0-9]{8,11}$/ }"
                           name="gud_mobile2"
                           v-model="obj.student.gud_mobile2" @input="validParentPhone2" disabled>
                  </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Ngày sinh phụ huynh 2</label>
                      <datePicker
                        id="gud2-birth-day"
                        class="form-control calendar"
                        v-validate="'required'" :class="{'input': true, 'is-danger': errors.has('gud_birth_day2') }"
                        :value="obj.student.gud_birth_day2"
                        v-model="obj.student.gud_birth_day2"
                        placeholder="Chọn ngày sinh phụ huynh 2"
                        lang="lang"
                        @change="selectGudBirthDay2"
                      >
                      </datePicker>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label" >Nghề nghiệp phụ huynh 2</label>
                      <vue-select 
                          label="name" 
                          placeholder="Chọn Nghề nghiệp"
                          :options="html.data.jobs.list" 
                          v-model="obj.student.job2" 
                          :searchable="true"
                          language="tv-VN"
                          :onChange="saveJob2"
                      ></vue-select>
                    </div>
                  </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Nguồn tiếp nhận học sinh là bắt buộc">Từ Nguồn <span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.source" name="source" readonly="true">
                      <option value="">Chọn nguồn từ</option>
                      <option :value="source.id" v-for="(source, i) in html.data.sources.list" :key="i">{{ source.name }}</option>
                    </select>
                    <span v-show="errors.has('source')" class="error-inform line">
                          <i v-show="errors.has('source')" class="fa fa-warning"></i>
                          <span v-show="errors.has('source')" class="error help is-danger">Nguồn tiếp nhận học sinh là bắt buộc!</span>
                        </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Độ tuổi học sinh là bắt buộc">Độ tuổi <span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.school_grade" name="school_grade">
                      <option value="">Chọn độ tuổi</option>
                      <option :value="school.id" v-for="(school, i) in html.data.school_grade.list" :key="i">{{ school.name }}</option>
                    </select>
                    <span v-show="errors.has('school_grade')" class="error-inform line">
                          <i v-show="errors.has('school_grade')" class="fa fa-warning"></i>
                          <span v-show="errors.has('school_grade')" class="error help is-danger">Độ tuổi học sinh là bắt buộc!</span>
                        </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Địa chỉ email của phụ huynh">Email <span class="text-danger"> (*)</span> </label>
                    <input class="form-control" type="email" v-model="obj.student.gud_email1" name="gud_email1" v-validate="'required|email'">
                    <span v-show="errors.has('gud_email1')" class="error-inform line">
                        <i v-show="errors.has('gud_email1')" class="fa fa-warning"></i>
                        <span v-show="errors.has('gud_email1')" class="error help is-danger">Email phải đúng định dạng!</span>
                      </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Đối tượng khách hàng</label>
                    <select class="form-control" v-model="obj.student.type" name="type">
                      <option value="" disabled>Chọn đối tượng khách hàng</option>
                      <option value="0">Thường</option>
                      <option value="1">VIP</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Ghi chú</label>
                    <textarea
                            class="description form-control" rows="8" style="margin-top: 0px; margin-bottom: 0px; height: 90px;"
                            v-model="obj.student.note"></textarea>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Ngày/Giờ Checkin</label>
                    <datePicker
                            id="checkin-at"
                            class="form-control calendar"
                            :value="obj.student.checkin_at"
                            v-model="obj.student.checkin_at"
                            placeholder="Chọn ngày giờ"
                            lang="lang"
                            type="datetime"
                            format="YYYY-MM-DD HH:mm"
                    >
                    </datePicker>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Mã CMS của anh/chị em học cùng</label>
                    <input
                      :disabled="sibling_id_exist ? true : false"
                      class="form-control"
                      type="text"
                      v-model="obj.student.sibling_id"
                      maxlength="200"
                      @change="inputSibling"
                    >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Tên anh/chị/em học cùng</label>
                    <input
                      v-bind:readonly="true"
                      class="form-control"
                      type="text"
                      v-model="html.data.sibling.name"
                      maxlength="200"
                    >
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
          <loader :active="processing" :duration="duration" />
          <div class="panel main-content">
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4" :class="html.dom.branch.display">
                  <div class="form-group">
                    <label class="control-label">Trung tâm</label>
                    <branch v-show="!editBranch()"
                            v-model="obj.student.branch_id"
                            :onSelect="getEcCsList"
                            :options="html.data.branch.list"
                            :disabled="editBranch()"
                            :placeholder="obj.student.branch_name">
                    </branch>

                    <input v-show="editBranch()"
                            v-bind:readonly="true"
                            class="form-control"
                            type="text"
                            v-model="obj.student.branch_name"
                    >
                    <!--<select class="form-control" :disabled="editBranch()" v-model="obj.student.branch_id" @change="getEcCsList()">-->
                      <!--<option value="">Chọn trung tâm</option>-->
                      <!--<option :value="branch.id" v-for="(branch, i) in html.data.branch.list" :key="i">{{ branch.name }}</option>-->
                    <!--</select>-->
                  </div>
                </div>
                <div class="col-sm-2" :class="html.dom.ec.display">
                  <div class="form-group">
                    <label class="control-label">EC<span class="text-danger"> (*)</span></label>
                    <vue-select
                            label="ec_name"
                            :options="html.data.ec.list"
                            :searchable="true"
                            language="tv-VN"
                            placeholder="Vui lòng chọn EC"
                            :disabled="false"
                            v-model="html.data.ec.model"
                    >
                    </vue-select>
                  </div>
                </div>
                <div class="col-sm-2" :class="html.dom.ec.display">
                  <div class="form-group">
                    <label class="control-label">CS<span class="text-danger"> (*)</span></label>
                    <vue-select
                      label="cm_name"
                      :options="html.data.cm.list"
                      :searchable="true"
                      language="tv-VN"
                      placeholder="Vui lòng chọn CS"
                      :disabled="false"
                      v-model="html.data.cm.model"
                    >
                    </vue-select>
                  </div>
                </div>
                <!-- <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" :class="showEdit()"><span>Click để đánh dấu học thử <input :disabled="html.dom.shift_id.disabled" type="checkbox" value="1" v-model="start_trial" @change="startTrialLearing"></span></label>
                    <select class="form-control" :class="html.dom.shift_id.display" v-model="obj.student.shift_id" :disabled="html.dom.shift_id.disabled">
                      <option value="">Chọn ca học thử </option>
                      <option :value="cls.id" v-for="(cls, index) in search.list_shift" :key="index">{{cls.name}}</option>
                    </select>
                  </div>
                </div> -->
<!--                <div class="col-sm-4">-->
<!--                  <div class="form-group" v-if="obj.student.status_transfer === null">-->
<!--                    <label class="control-label" :class="vshowCM()"><span>Click để Chuyển Trung Tâm <input type="checkbox" value="1" v-model="start_transfer" @change="transferBranch"></span></label>-->
<!--                    <select class="form-control" :class="html.dom.transfer_bch.display" :disabled="html.dom.transfer_bch.disabled" v-model="transfer_branch_id" @change="getUsersByBranch()">-->
<!--                      <option value="">Chọn trung tâm</option>-->
<!--                      <option :class="branch.id == obj.student.branch_id ? 'hidden':''" :value="branch.id" v-for="(branch, i) in html.data.branch.list" :key="i">{{ branch.name }}</option>-->
<!--                    </select>-->
<!--                  </div>-->
<!--                  <div class="form-group" v-if="obj.student.status_transfer === 0">-->
<!--                    <label class="control-label"><b class="text-warning">Học sinh đang chờ duyệt chuyển trung tâm</b></label>-->
<!--                  </div>-->
<!--                  <div class="form-group" v-if="obj.student.status_transfer === 1">-->
<!--                    <label class="control-label"><b class="text-warning">Học sinh đang chờ duyệt trung tâm đến</b></label>-->
<!--                  </div>-->
<!--                  <div class="form-group" v-if="obj.student.status_transfer === 3">-->
<!--                    <label class="control-label"><b class="text-warning">Trung tâm chuyển đã từ chối</b></label>-->
<!--                  </div>-->
<!--                  <div class="form-group" v-if="obj.student.status_transfer === 4">-->
<!--                    <label class="control-label"><b class="text-warning">Trung tâm nhận đã từ chối</b></label>-->
<!--                  </div>-->
<!--                  <div class="form-group" v-if="obj.student.status_transfer == 2">-->
<!--                    <label class="control-label"><b class="text-warning">Học sinh đã được chuyển trung tâm thành công</b></label>-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="col-sm-4">-->
<!--                  <div class="form-group" :class="html.dom.transfer_bch.display">-->
<!--                    <label class="control-label">EC (TT mới)<span class="text-danger"></span></label>-->
<!--                    <vue-select-->
<!--                      label="ec_name"-->
<!--                      :options="html.data.ecNew.list"-->
<!--                      :searchable="true"-->
<!--                      language="tv-VN"-->
<!--                      placeholder="Vui lòng chọn EC"-->
<!--                      :disabled="html.dom.ecNew.disabled"-->
<!--                      v-model="html.data.ecNew.item"-->
<!--                    >-->
<!--                    </vue-select>-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="col-sm-4">-->
<!--                  <div class="form-group" :class="html.dom.transfer_bch.display">-->
<!--                    <label class="control-label">CS (TT mới)<span class="text-danger"></span></label>-->
<!--                    <vue-select-->
<!--                      label="cm_name"-->
<!--                      :options="html.data.csNew.list"-->
<!--                      :searchable="true"-->
<!--                      language="tv-VN"-->
<!--                      placeholder="Vui lòng chọn CS"-->
<!--                      :disabled="html.dom.ecNew.disabled"-->
<!--                      v-model="html.data.csNew.item"-->
<!--                    >-->
<!--                    </vue-select>-->
<!--                  </div>-->
<!--                </div>-->
              </div>
            </div>
            <div class="panel-footer" style="padding-top: 20px">
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
        </b-card>
        <b-modal :title="html.dom.modal.title" :class="html.dom.modal.class" size="md" v-model="html.dom.modal.display" @ok="" ok-variant="primary" ok-only @hide="html.dom.modal.done">
          <div v-html="html.dom.modal.message">
          </div>
        </b-modal>
        <br/>
      </div>
    </div>
  </div>
</template>

<script>

  import u from '../../../utilities/utility'
  import inp from '../../components/Input'
  import abt from '../../components/Button'
  import file from '../../components/File'
  import loader from '../../../components/Loading'
  import branch from '../../components/Selection'
  import datePicker from 'vue2-datepicker'
  import select from 'vue-select'
  import search from "../../components/StudentSearch"

  export default {

    data(){
      const model = u.m('student').page
              model.sibling_id_exist = null,
              model.start_trial = false,
              model.start_transfer = false,
              model.duration = 100,
              model.processing = false,
              model.transfer_branch_id = '',
              model.search={
                shift_list:[]
              },
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
                  placeholder: 'Chọn trung tâm',
                  display: 'hidden',
                  disabled: true
                },
                enrolment: {
                  display: 'hidden',
                  disabled: true,
                },
                transfer: {
                  display: 'hidden',
                  disabled: true,
                },
                ec: {
                  display: 'hidden',
                  disabled: true,
                },
                cm: {
                  display: 'hidden',
                  disabled: true
                },
                ecNew: {
                  display: 'hidden',
                  disabled: true
                },
                shift_id: {
                  display: 'hidden',
                  disabled: true
                },
                transfer_bch: {
                  display: 'hidden',
                  disabled: true
                },
                sources: [],
                coupon: {
                  require: 0,
                  validate: 1,
                  message: ""
                },
                button: {
                  save: {
                    label: 'Lưu',
                    icon: 'fa-save',
                    title: 'Lưu thông tin',
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
                    title: 'Thoát',
                    markup: 'warning',
                    disabled: false,
                    action: () => this.exitForm()
                  }
                }
              }
      model.obj = {
        student: {
          crm_id: '',
          cms_id: '',
          accounting_id: '',
          lms_stu_id: '',
          name: '',
          type: 0,
          date_of_birth: '',
          phone: '',
          email: '',
          gender: '',
          school: '',
          address: '',
          job_title: '',
          province: '',
          province_id: '',
          district_id: '',
          district: '',
          school_level: '',
          school_grade: '',
          ex_where: '',
          ex_level: '',
          gud_mobile1: '',
          gud_name1: '',
          gud_title: '',
          gud_firstname1: '',
          gud_midname1: '',
          gud_lastname1: '',
          gud_card1: '',
          gud_birth_day1: '',
          gud_mobile2: '',
          gud_name2: '',
          gud_email2: '',
          gud_card2: '',
          gud_job1: '',
          gud_job2: '',
          gud_gender1: '',
          gud_gender2: '',
          job1: '',
          gud_birth_day2: '',
          avatar: {},
          branch_id: '',
          cm_id: '',
          ec_id: '',
          source: '',
          checked: true,
          sibling_id: '',
          attached_file: {},
          extra: "",
          extra2: "",
          partner_code: '',
          class_id: '',
          trial_date: '',
          note: '',
          new_branch_id: '',
          new_ec_id: '',
          new_cs_id: '',
          status_transfer: '',
          checkin_at: '',
          role_id: '',
        }
      }
      model.html.data = {
        jobs: {
          item: '',
          list: []
        },
        schools: {
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
          list: [],
          model:'',
        },
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
        cm: {
          item: '',
          list: []
        },
        sibling: {
          id: '',
          name: ''
        },
        tmp_ec:{
          list:[],
          list_all:[],
        },
        sources: {
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
      loader,
      datePicker,
      "vue-select": select,
      search,
      branch,
    },

    created() {
      u.i(this)
      this.processing = true
      const sessions = u.session()
      this.sources = sessions.info.partners
      this.html.dom.branch.display = ''
      this.html.dom.ec.display = ''
      this.html.dom.branch.disabled = false

      // this.html.dom.shift_id.display = 'hidden'
      // this.html.dom.shift_id.disabled = true
      u.g(`/api/sources`)
        .then(response => {
          this.html.data.sources.list = response
        })
      u.g('/api/settings/branches?all=1')
        .then(data => {
          this.html.data.branch.list = data
        })
      u.g(`/api/provinces`)
              .then(response => {
                this.html.data.province.list = response
              })
      u.g(`/api/gud_job/all_list`)
        .then(response => {
          this.html.data.jobs.list = response
        })
      u.g(`/api/school-grades/list`)
        .then(response => {
          this.html.data.school_grade.list = response
        })
      u.g(`/api/get-student-checkin/${this.$route.params.id}`).then(data => {
        if (data){
          this.obj.student = data.student
          this.html.data.ec.list = data.ec
          this.html.data.cm.list = data.cs
          this.html.data.ec.model = {"ec_name":this.obj.student.ec_name,"ec_id":this.obj.student.ec_id}
          this.html.data.cm.model = {"cm_name":this.obj.student.cm_name,"cm_id":this.obj.student.cm_id}
          this.obj.student.province = {"name":this.obj.student.province_name,"id":this.obj.student.province_id}
          this.obj.student.job1 = this.html.data.jobs.list.filter(item => item.id == this.obj.student.gud_job1)[0]
          this.obj.student.job2 = this.html.data.jobs.list.filter(item => item.id == this.obj.student.gud_job2)[0]
          if( this.obj.student.school_level=="Tiểu học"){
            this.getSchools()
            this.obj.student.select_school = this.obj.student.school
          }
          this.getListTrialClass()
          this.getListShift()
          this.sibling_id_exist = this.obj.student.sibling_id
          if (this.sibling_id_exist){
            this.obj.student.sibling_id = this.obj.student.sibling_crm_id
            this.html.data.sibling.name = this.obj.student.sibling_name
          }
          if (this.obj.student.shift_id){
            this.html.dom.shift_id.display = ''
            this.html.dom.shift_id.disabled = true
            this.start_trial = true
          }
        else{
              this.html.dom.shift_id.disabled = false
          }
        }})
    },
    methods: {
      getEcCsList(branch_data){
        this.obj.student.branch_id = branch_data.id
        const id = this.obj.student.branch_id
        this.html.data.ec.model = ""
        this.html.data.cm.model = ""
        this.obj.student.cm_id=""
        this.obj.student.ec_id=""
        u.g(`/api/students/get/ec/of/a/branch/${id}`).then(data => {
          if (data.ecs.length ) {
            this.html.data.ec.list = data.ecs
            this.html.data.ec.item = ''
            this.html.dom.ec.disabled = false
          }
          if (data.cms.length) {
            this.html.data.cm.list = data.cms
            this.html.data.cm.item = ''
            this.html.dom.cm.disabled = false
          }
          this.html.data.tmp_ec.list = data.ecs
          this.html.data.tmp_ec.list_all = data.ecs_all
        })
        this.start_trial = false
        this.getListShift()
      },
      editBranch(){
        let roles = [80,81,999999999]
        let uid  = parseInt(u.session().user.role_id)
        if (roles.includes(uid)){
          return  false
        }
        else
          return  true
      },
      showEdit(){
        let roles = [55,56,999999999,68,69]
        let uid  = parseInt(u.session().user.role_id)
        if (roles.includes(uid)){
          return  ''
        }
        else
          return  'hidden'
      },
      vshowCM(){
        if (u.session().user.role_id == 56 || u.session().user.role_id == 999999999)
          return ''
        else
          return 'hidden'
      },
      callback(data) {
        this.html.dom.modal.display = false
        if (this.expired) {
          u.go(this.$router, '/login')
        }
        if (this.completed) {
          u.go(this.$router, '/student-checkin')
        }
      },
      onDrawDate (e) {
        let date = e.date
        if (new Date().getTime() > date.getTime()) {
          e.allowSelect = false
        }
      },
      selectSchool () {
        if (this.obj.student.select_school != 'Other') {
          this.obj.student.school = this.obj.student.select_school
        } else {
          this.obj.student.school = ''
        }
      },
      deInputSchool () {
        if (Array.isArray(this.html.data.schools.list) && this.html.data.schools.list.length > 0) {
          this.obj.student.select_school = ''
          this.obj.student.school = ''
        }
      },
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
            this.html.dom.ecNew.disabled = false
          }
        })
      },
      selectBirthDay(date) {
        this.obj.student.date_of_birth = date
      },
      saveForm() {
        this.processing = true
        const condition = {
          name: this.obj.student.name,
          parent: this.obj.student.gud_name1,
          mobile: this.obj.student.gud_mobile1
        }
        this.checkDublicateStudent(condition, true)
      },
      reject(msg) {
        this.$notify({
          group: 'apax-atc',
          title: 'Thông báo!',
          type: 'danger',
          duration: 5000,
          text: `${msg}`
        })
        this.processing = false
      },
      changeClass(){
        if (!this.obj.student.class_id)
          this.obj.student.trial_date = ''
      },
      resetForm() {
        location.reload()
      },
      exitForm() {
        this.$router.push('/student-checkin')
      },
      uploadFile(file, param = null) {
        if (param) {
          this.obj.student[param] = file
        }
        // u.log('DATA OK', this.obj.student)
      },
      validateInput() {
        let validated = true
        let alert_msg = ''
        this.obj.student.firstname = this.obj.student.firstname.trim()
        if (this.obj.student.firstname == '') {
          validated = false
          alert_msg+= '(*) Họ của học sinh là bắt buộc<br/>'
        }
        this.obj.student.lastname = this.obj.student.lastname.trim()
        if (this.obj.student.lastname == '') {
          validated = false
          alert_msg+= '(*) Tên của học sinh là bắt buộc<br/>'
        }
        if (this.obj.student.address == '') {
          this.obj.student.address =this.obj.student.address.trim()
          validated = false
          alert_msg+= '(*) Địa chỉ là bắt buộc<br/>'
        }
        // if ((this.obj.student.date_of_birth == '' || this.obj.student.date_of_birth == null || this.obj.student.date_of_birth == 'Invalid date')&& u.session().user.role_id !=80 && u.session().user.role_id !=81 ) {
        //   validated = false
        //   alert_msg+= '(*) Ngày sinh là bắt buộc<br/>'
        // }
        if (this.obj.student.gender === '') {
          validated = false
          alert_msg+= '(*) Giới tính là bắt buộc<br/>'
        }
        // if ((this.obj.student.school_level == '' || this.obj.student.school_level == null) && u.session().user.role_id !=80 && u.session().user.role_id !=81) {
        //   validated = false
        //   alert_msg+= '(*) Cấp trường là bắt buộc<br/>'
        // }
        // if (this.obj.student.school == '' && u.session().user.role_id !=80 && u.session().user.role_id !=81) {
        //   this.obj.student.school =this.obj.student.school.trim()
        //   validated = false
        //   alert_msg+= '(*) Trường học là bắt buộc<br/>'
        // }
        if (this.obj.student.school_grade == '') {
          validated = false
          alert_msg+= '(*) Lớp học là bắt buộc<br/>'
        }
        if (this.obj.student.province_id == '' || this.obj.student.province_id == null) {
          validated = false
          alert_msg+= '(*) Tỉnh thành phố là bắt buộc<br/>'
        }
        if (this.obj.student.district_id == '' || this.obj.student.district_id == null) {
          validated = false
          alert_msg+= '(*) Quận huyện là bắt buộc<br/>'
        }
        if (this.obj.student.source == '') {
          validated = false
          alert_msg+= '(*) Nguồn tiếp nhận học sinh là bắt buộc<br/>'
        }
        if(this.obj.student.gud_gender1==''||this.obj.student.gud_gender1==null){
          validated = false
          alert_msg+= '(*) Tiền tố tên phụ huynh 1 là bắt buộc<br/>'
        }
        if (this.obj.student.gud_name1 == '') {
          this.obj.student.gud_name1 = this.obj.student.gud_name1.trim()
          validated = false
          alert_msg+= '(*) Họ tên phụ huynh 1 là bắt buộc<br/>'
        }
        if (this.obj.student.gud_mobile1 == '') {   
          this.obj.student.gud_mobile1 = this.obj.student.gud_mobile1.trim()
          validated = false
          alert_msg+= '(*) Số điện thoại phụ huynh là bắt buộc<br/>'
        }else{
          if (this.obj.student.gud_mobile1.length < 10 || this.obj.student.gud_mobile1.length > 10) {
            validated = false
            alert_msg+= '(*) Số điện thoại phụ huynh chỉ được dài từ 10 chữ số<br/>'
          }
          if (this.obj.student.gud_mobile1.substr(0, 1) !== '0') {
            validated = false
            alert_msg+= '(*) Số điện thoại  phụ huynh phải bắt đầu = số 0<br/>'
          }
        }
        // if (this.obj.student.gud_job1 == '' && u.session().user.role_id !=80 && u.session().user.role_id !=81) {
        //   validated = false
        //   alert_msg+= '(*) Nghề nghiệp phụ huynh 1 là bắt buộc<br/>'
        // }
        if (this.obj.student.gud_birth_day1 == '') {
          validated = false
          alert_msg+= '(*) Ngày sinh phụ huynh 1 là bắt buộc<br/>'
        }
        // if ((this.obj.student.gud_email1 == '' || this.obj.student.gud_email1 == null)&& u.session().user.role_id !=80 && u.session().user.role_id !=81) {
        //   validated = false
        //   alert_msg+= '(*) Email là bắt buộc<br/>'
        // }
        if (this.obj.student.gud_email1 && !u.vld.email(this.obj.student.gud_email1.trim()) && u.session().user.role_id !=80 && u.session().user.role_id !=81) {
          validated = false
          alert_msg+= ' Email phụ huynh không đúng định dạng<br/>'
        }
        if (!this.html.data.ec.model.ec_id) {
          validated = false
          alert_msg+= '(*) Chọn EC là bắt buộc<br/>'
        }
        if (!this.html.data.cm.model.cm_id) {
          validated = false
          alert_msg+= '(*) Chọn CS là bắt buộc<br/>'
        }
        if (this.transfer_branch_id >0) {
          if (!this.html.data.ecNew.item || !this.html.data.csNew.item){
            validated = false
            alert_msg+= '(*) Hãy chọn CS, EC ở trung tâm mới<br/>'
          }
        }
        if (this.obj.student.checkin_at == "0000-00-00 00:00:00" || this.obj.student.checkin_at == null) {
          validated = false
          alert_msg+= 'Ngày/Giờ checkin là bắt buộc<br/>'
        }
        if (!validated) {
          alert_msg = `Dữ liệu học sinh chưa hợp lệ:<br/>-----------------------------------<br/><br/><p class="text-danger">${alert_msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          this.html.dom.modal.message = alert_msg
          this.html.dom.modal.title = 'Cảnh Báo'
          this.html.dom.modal.class = 'modal-warning'
          this.html.dom.modal.done = function(){ return false; }
          this.html.dom.modal.display = true
        }
        return validated
      },
      checkRequired() {
        this.obj.student.gud_name1 = this.obj.student.gud_name1.trim();
        if (this.obj.student.name != '' && this.obj.student.date_of_birth != ''
                && this.obj.student.gender != '' && this.obj.student.province_id != ''
                && this.obj.student.gud_name1 != '' && this.obj.student.gud_mobile1 != '') {
          this.html.dom.button.save.disabled = false
          this.html.dom.button.reset.disabled = false
        }
        this.inputSibling()
        if (this.obj.student.name != '' && this.obj.student.gud_name1 != '' && this.obj.student.gud_mobile1 != '') {
          const condition = {
            name: this.obj.student.name,
            parent: this.obj.student.gud_name1,
            mobile: this.obj.student.gud_mobile1,
            parent2: this.obj.student.gud_name2,
            mobile2: this.obj.student.gud_mobile2
          }
          this.checkDublicateStudent(condition)
        }
      },
      store() {
        if (this.validateInput()) {
          // u.p('/api/students/check_dublicate', this.obj.student).then(response => {
          //  if(!response.result){
          //    let alert_msg = `Dữ liệu học sinh chưa hợp lệ:<br/>-----------------------------------<br/><br/><p class="text-danger">Tên học sinh và số điện thoại đã tồn tại ở trung tâm</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
          //    this.html.dom.modal.message = alert_msg
          //    this.html.dom.modal.title = 'Cảnh Báo'
          //    this.html.dom.modal.class = 'modal-warning'
          //    this.html.dom.modal.done = function(){ return false; }
          //    this.html.dom.modal.display = true
          //    this.processing = false
          //  }else{
          this.obj.student.ec_id = this.html.data.ec.model.ec_id
          this.obj.student.cm_id = this.html.data.cm.model.cm_id
          const that = this
          this.obj.student.job_title = this.job_title
          // this.obj.student.date_of_birth = this.moment(this.obj.student.date_of_birth, 'DD/MM/YYYY').format('YYYY-MM-DD')
          // this.obj.student.gud_birth_day1 = this.moment(this.obj.student.gud_birth_day1, 'DD/MM/YYYY').format('YYYY-MM-DD')
          // this.obj.student.gud_birth_day2 = this.moment(this.obj.student.gud_birth_day2, 'DD/MM/YYYY').format('YYYY-MM-DD')
          this.obj.student.trial_date = this.moment(this.obj.student.trial_date, 'DD/MM/YYYY').format('YYYY-MM-DD')

          if (this.transfer_branch_id){
            this.obj.student.new_ec_id =  this.html.data.ecNew.item.ec_id
            this.obj.student.new_cs_id =  this.html.data.csNew.item.cm_id
            this.obj.student.new_branch_id =  this.transfer_branch_id
          }
          this.obj.student.checkin_at = this.moment(this.obj.student.checkin_at).format('YYYY-MM-DD HH:mm')
          u.put(`/api/checkin/${this.obj.student.id}/update`, this.obj.student).then(response => {
            if (response.success) {
              this.html.dom.button.save.disabled = true
              this.html.dom.button.reset.disabled = true
              this.completed = true
              this.processing = false

              let msg_content = `Cập nhật check in:<br/><b>${response.success}</b><br/> đã được lưu thành công!`
              this.html.dom.modal.message = msg_content
              this.html.dom.modal.title = 'Check In Học Sinh Đã Được Lưu Thành Công'
              this.html.dom.modal.class = 'modal-success'
              this.html.dom.modal.done = () => this.callback()
              this.html.dom.modal.display = true
            }
          })
          //   }
          // })
        } else {
          this.processing = false
        }
      },
      selectBirthDay(date) {
      this.obj.student.date_of_birth = this.moment(this.obj.student.date_of_birth, 'DD/MM/YYYY').format('YYYY-MM-DD')
      },
      selectGudBirthDay1(){
        this.obj.student.gud_birth_day1 = this.moment(this.obj.student.gud_birth_day1, 'DD/MM/YYYY').format('YYYY-MM-DD')
      },
      selectGudBirthDay2(){
        this.obj.student.gud_birth_day2 = this.moment(this.obj.student.gud_birth_day2, 'DD/MM/YYYY').format('YYYY-MM-DD')
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
      validShoolName() {
        this.obj.student.school = this.obj.student.school.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      validName() {
        this.obj.student.name = u.uniless(this.obj.student.name)
        this.obj.student.name = this.obj.student.name.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      validCode() {
        this.obj.student.partner_code = this.obj.student.partner_code.toString().toUpperCase()
      },
      validPhone() {
        this.obj.student.phone = u.onlynum(this.obj.student.phone)
      },
      validParentPhone1() {
        this.obj.student.gud_mobile1 = u.onlynum(this.obj.student.gud_mobile1)
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
        this.obj.student.gud_name1 = this.obj.student.gud_name1.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      upercaseFirstNameGud1() {
        this.obj.student.gud_firstname1 = u.ufc(this.obj.student.gud_firstname1)
        this.obj.student.gud_firstname1 = this.obj.student.gud_firstname1.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      upercaseMidNameGud1() {
        this.obj.student.gud_midname1 = u.ufc(this.obj.student.gud_midname1)
        this.obj.student.gud_midname1 = this.obj.student.gud_midname1.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      upercaseLastNameGud1() {
        this.obj.student.gud_lastname1 = u.ufc(this.obj.student.gud_lastname1)
        this.obj.student.gud_lastname1 = this.obj.student.gud_lastname1.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      upercaseNameGud2() {
        this.obj.student.gud_name2 = u.ufc(this.obj.student.gud_name2)
        this.obj.student.gud_name2 = this.obj.student.gud_name2.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
      },
      upercaseSchool() {
        this.obj.student.school = u.ufc(this.obj.student.school)
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.obj.student.province = data
          this.obj.student.province_id = province_id
          u.g(`/api/provinces/${province_id}/districts`).then(response => {
            this.html.data.district.list = response
          })
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.obj.student.district = data
          this.obj.student.district_id = district_id
        }
      },
      getSchools(e){
        const school_level = this.obj.student.school_level ? this.obj.student.school_level : e.target.value ? e.target.value : 'Tiểu học'
        const district_id = parseInt(this.obj.student.district_id, 10)
        const province_id = parseInt(this.obj.student.province_id, 10)
        if (school_level && district_id > 0 && province_id > 0) {        
          u.g(`/api/get/${province_id}/${district_id}/${school_level}/schools`).then(response => {
            this.html.data.schools.list = response
            // this.obj.student.school = ""
          })        
        }
      },
      saveJob1(data = null){
        if (data && typeof data === 'object') {
          const gud_job1 = data.id
          this.obj.student.job1 = data
          this.obj.student.gud_job1 = gud_job1
        }else{
          this.obj.student.job1 = ""
          this.obj.student.gud_job1 = ""
        }
      },
      saveJob2(data = null){
        if (data && typeof data === 'object') {
          const gud_job2 = data.id
          this.obj.student.job2 = data
          this.obj.student.gud_job2 = gud_job2
        }else{
          this.obj.student.job2 = ""
          this.obj.student.gud_job2 = ""
        }
      },
      checkDublicateStudent(condition, save = false) {
        condition.type = 'checkin'
        condition.id = this.obj.student.id
        condition.branch_id = this.obj.student.branch_id
        condition.t = moment().toDate().getTime()
        condition.hub = [80,81].includes(this.obj.student.role_id) ? true:false
        if (this.html.data.sibling.id > 0 || this.obj.student.sibling_crm_id){
          condition.sibling = true
        }
        u.p(`/api/students/check`,condition).then(response => {
          if (parseInt(response.existed) > 0) {
            this.reject(response.msg)
          } else {
            if (save) {
              this.store()
            }
          }
        })
      },
      inputSibling(){
        let crm_id = this.obj.student.sibling_id
        if (crm_id != '' && (!isNaN(crm_id) || crm_id.length > 4) && crm_id !=  null) {
          // crm_id = !isNaN(crm_id) ? parseInt(crm_id) : crm_id.substring(3)
          u.g(`/api/students/sibling/${crm_id}`).then(response => {
            if (response && u.is.has(response, 'name')) {
              if (u.is.has(response, 'sibling_id') && parseInt(response.id) === 0) {
                //this.obj.student.id = ''
              }
              this.html.data.sibling = response
              if (this.html.data.sibling.id >0){
                this.obj.student.gud_mobile1 = this.html.data.sibling.gud_mobile1
              }
            }
          })
        }
      },
      checkRequireCoupon() {
        if (this.obj.student.source != "") {
          u.a()
                  .get("/api/setting/partner/" + this.obj.student.source)
                  .then(response => {
                    if (response.data.require == 1) {
                      this.html.dom.coupon.require = 1;
                    } else {
                      this.html.dom.coupon.require = 0;
                    }
                  });
        }
        this.obj.student.partner_code = "";
        this.html.dom.coupon.validate = 1;
      },
      checkCoupon() {
        if (this.obj.student.partner_code == "") {
          this.html.dom.coupon.validate = 1;
        } else {
          // u.a()
          //         .get(
          //                 "/api/setting/check_coupon/" +
          //                 this.obj.student.partner_code +
          //                 "?partner_id=" +
          //                 this.obj.student.source
          //         )
          //         .then(response => {
          //           this.html.dom.coupon.validate = response.data.status;
          //           this.html.dom.coupon.message = response.data.message;
          //         });
        }
      },
      getEC(data = null){
        // if (data && typeof data === 'object') {
        //   this.obj.student.ec_id = data.ec_id
        // }
      },
      transferBranch(){
        if (this.start_transfer){
          this.html.dom.transfer_bch.display = ''
          this.html.dom.transfer_bch.disabled = false
        }
        else{
          this.html.dom.transfer_bch.display = 'hidden'
          this.html.dom.transfer_bch.disabled = true
          this.html.data.ecNew.item = ''
          this.transfer_branch_id = ''
        }
      },
      startTrialLearing(){
        if (this.start_trial){
          this.html.dom.shift_id.display = ''
          this.html.dom.shift_id.disabled = false
        }
        else{
          this.html.dom.shift_id.display = 'hidden'
          this.html.dom.shift_id.disabled = true
          this.obj.student.shift_id = ''
        }

        // if(this.is_all_ec){
        //   this.html.data.ec.list = this.html.data.tmp_ec.list_all
        // }else{
        //   this.html.data.ec.list = this.html.data.tmp_ec.list
        // }
        // this.obj.student.ec_id = ''
        // this.html.data.ec.model = this.html.data.ec.list.filter(item => item.ec_id == this.obj.student.ec_id)[0]
      },
      getListTrialClass(){
        u.g(`/api/checkin/get-trial-class/${this.obj.student.branch_id}`).then(response => {
          this.search.shift_list = response
          this.processing = false
        })
        .catch(e => {
          //u.lg(e)
          this.search.loading_from = "hidden"
        })
      },
      getListShift(){
        u.g(`/api/shift/list/${this.obj.student.branch_id}`).then(response => {
          this.search.list_shift = response
        })
          .catch(e => {
            this.search.loading_from = "hidden"
          })
      },
      genGudName1(){
        this.obj.student.gud_name1 = this.obj.student.gud_midname1 ? this.obj.student.gud_firstname1.trim()+ " " + this.obj.student.gud_midname1.trim()+ " " + this.obj.student.gud_lastname1.trim() : this.obj.student.gud_firstname1.trim()+ " " + this.obj.student.gud_lastname1.trim();
        this.obj.student.gud_name1 = this.obj.student.gud_name1.trim();
      },
      genGudName2(){
        this.obj.student.gud_name2 = this.obj.student.gud_midname2 ? this.obj.student.gud_firstname2.trim()+ " " + this.obj.student.gud_midname2.trim()+ " " + this.obj.student.gud_lastname2.trim() : this.obj.student.gud_firstname2.trim()+ " " + this.obj.student.gud_lastname2.trim();;
        this.obj.student.gud_name2 = this.obj.student.gud_name2.trim();
      },
      genStudentName(){
        this.obj.student.name = this.obj.student.midname ? this.obj.student.firstname.trim()+ " " + this.obj.student.midname.trim()+ " " + this.obj.student.lastname.trim() : this.obj.student.firstname.trim()+ " " + this.obj.student.lastname.trim();;
        this.obj.student.name = this.obj.student.name.trim();
      }
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
