<template>
  <div class="animated fadeIn apax-form apax-show-detail" id="edit-student-info" @click="html.dom.page.action">
    <div class="row">
      <div class="col-lg-12">
        <b-card header-tag="header">
          <div v-show="html.loading.action" class="ajax-load content-loading">
            <div class="load-wrapper">
              <div class="loader"></div>
              <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
            </div>
          </div>
          <div slot="header">
            <i class="fa fa-edit"></i> <b class="uppercase">Cập nhật hồ sơ học sinh</b>
            <div class="card-actions">
              <a href="skype:thanhcong1710?chat" target="_blank">
                <small className="text-muted"><i class="fa fa-skype"></i></small>
              </a>
            </div>
          </div>
          <div class="panel main-content">
            <div v-show="html.loading.action" class="ajax-load content-loading">
              <div class="load-wrapper">
                <div class="loader"></div>
                <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
              </div>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <file
                      :label="'Ảnh đại diện'"
                      :name="'upload_avatar'"
                      :field="'avatar'"
                      :type="'img'"
                      :link="validFile(obj.student.avatar)"
                      :onChange="uploadFile"
                      :title="'File ảnh đại diện!'"
                    >
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
                      :link="validFile(obj.student.attached_file)"
                      :onChange="uploadFile"
                      :title="'Tải xuống file đính kèm!'"
                    >
                    </file>
                  </div>
                </div>
<!--                <div class="col-sm-4">-->
<!--                  <div class="form-check">-->
<!--                    <input type="checkbox" class="form-check-input" id="checkin" v-model="obj.student.checked">-->
<!--                    <label class="form-check-label" for="checked-in" >checked in</label>-->
<!--                  </div>-->
<!--                </div>-->
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <label class="control-label" v-b-tooltip.hover title="Mã CMS không được trùng">Mã CMS </label>
                  <input class="form-control" v-model="obj.student.crm_id" :disabled="html.dom.edit.disabled" placeholder="Sẽ tự động cập nhật khi lưu thông tin học sinh" readonly/>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="Họ tên của học viên là thông tin bắt buộc, viết in hoa không dấu">Tên học sinh <span class="text-danger"> (*)</span></label>
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
              </div>
              <div class="row mt-3">

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
                <!-- <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" v-b-tooltip.hover title="">Số điện thoại </label>
                    <input class="form-control"
                           type="text"
                           name="phone"
                           v-validate=""
                           v-model="obj.student.phone"
                           @input="validPhone">
                    <span v-show="errors.has('phone')" class="error-inform line">
                      <i v-show="errors.has('phone')" class="fa fa-warning"></i>
                      <span v-show="errors.has('phone')" class="error help is-danger">Số điện thoại</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Tên thường gọi/ Nick name</label>
                    <input class="form-control" type="text" v-model="obj.student.email" name="email">
                  </div>
                </div>
              </div> 
              <div class="row">-->

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
                    <label class="control-label" v-b-tooltip.hover title="Thông tin tỉnh thành của học viên là bắt buộc">Tỉnh/Thành phố<span class="text-danger"> (*)</span></label>
                    <select class="form-control" v-model="obj.student.province_id" @change="getDistrict"
                      name="province" v-validate data-vv-rules="required">
                      <option value="">Chọn Tỉnh/Thành phố</option>
                      <option :value="province.id" v-for="(province, index) in html.data.province.list" :key="index">{{ province.name }}</option>
                    </select>
                    <span v-show="errors.has('province')" class="error-inform line">
                      <i v-show="errors.has('province')" class="fa fa-warning"></i>
                      <span v-show="errors.has('province')" class="error help is-danger">Tỉnh thành là bắt buộc!</span>
                    </span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Quận/Huyện <span class="text-danger"></span></label>
                    <select class="form-control" v-model="obj.student.district_id">
                      <option value="" disabled>Chọn Quận/Huyện</option>
                      <option :value="district.id" v-for="(district, index) in html.data.district.list" :key="index">{{ district.name }}</option>
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
<!--                <div class="col-sm-4">-->
<!--                  <div class="form-group">-->
<!--                    <label class="control-label">Nickname</label>-->
<!--                    <input class="form-control" type="text" v-model="obj.student.nick" @input="validNick">-->
<!--                  </div>-->
<!--                </div>-->
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
                    <label class="control-label">Độ tuổi<span class="text-danger"> (*)</span></label>
                    <select class="form-control" id="" v-model="obj.student.school_grade" name="school_grade">
                      <option :value="school_grade.class_id" v-for="(school_grade, index) in html.data.school_grade.list" :key="index">{{ school_grade.class_name }}</option>
                    </select>
                    <span v-show="errors.has('school_grade')" class="error-inform line">
                      <i v-show="errors.has('school_grade')" class="fa fa-warning"></i>
                      <span v-show="errors.has('school_grade')" class="error help is-danger">Độ tuổi là bắt buộc</span>
                    </span>
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
                <!-- <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label">Facebook</label>
                    <input class="form-control" type="text" v-model="obj.student.facebook">
                  </div>
                </div> -->
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label class="control-label">Ghi chú</label>
                        <input class="form-control" type="text" v-model="obj.student.note">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="control-label" v-b-tooltip.hover title="Khi nhập vào mã CMS hệ thống sẽ tự động tìm kiếm">Mã CMS anh/chị em học cùng</label>
                        <input class="form-control"  @input="inputSibling" @change="inputSibling" type="text" placeholder="Mã CMS của anh chị em học cùng"  v-model="obj.student.sibling_id" />
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label class="control-label">Tên anh/chị/em học cùng</label>
                        <input class="form-control" placeholder="Tên của anh chị em học cùng" type="text" :value="obj.student.sibling_name" readonly />
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
          <div class="panel main-content">
            <div v-show="html.loading.action" class="ajax-load content-loading">
              <div class="load-wrapper">
                <div class="loader"></div>
                <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
              </div>
            </div>
            <div class="panel-body">
              <div class="col-lg-12">
                <div class="row">
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
                              type="text"
                              v-validate="'required'"
                              name="gud_mobile1"
                              v-model="obj.student.gud_mobile1" @input="validParentPhone1" :disabled="role != 999999999">
                      <span v-show="errors.has('gud_mobile1')" class="error-inform line">
                        <i v-show="errors.has('gud_mobile1')" class="fa fa-warning"></i>
                        <span v-show="errors.has('gud_mobile1')" class="error help is-danger">Số điện thoại của phụ huynh là bắt buộc</span>
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Email <span class="text-danger"> (*)</span></label>
                      <input class="form-control" type="text" v-model="obj.student.gud_email1">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Ngày sinh <span class="text-danger"> (*)</span></label>
                      <datePicker
                        id="gud2-birth-day"
                        class="form-control calendar"
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
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
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
                      <label class="control-label">Số điện thoại</label>
                      <input class="form-control" type="text" v-model="obj.student.gud_mobile2" @input="validParentPhone2">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Email</label>
                      <input class="form-control" type="text" v-model="obj.student.gud_email2">
                    </div>
                  </div>
                  <div class="col-sm-4">
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
          <div class="panel main-content">
            <div v-show="html.loading.action" class="ajax-load content-loading">
              <div class="load-wrapper">
                <div class="loader"></div>
                <div v-show="html.loading.action" class="loading-text cssload-loader">{{ html.loading.content }}</div>
              </div>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-4" :class="html.dom.branch.display">
                  <div class="form-group">
                    <label class="control-label">Trung tâm</label>
                    <!-- <input class="form-control" type="text" readonly="true" :value="obj.student.branch_name" /> -->
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
                      :disabled="true"
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
        <b-modal :title="html.dom.modal.title" :class="html.dom.modal.class" size="sm" v-model="html.dom.modal.display" @ok="html.dom.modal.done" ok-variant="primary">
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
import Multiselect from "vue-multiselect";
import select from 'vue-select'

export default {

  data(){
    const model = u.m('student').page
    model.role = u.session().user.role_id
    model.error = false
		model.html.dom = {
      edit: {
        disabled: true
      },
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
        display: 'hidden',
        disabled: true
      },
      ec: {
        display: 'hidden',
        disabled: true
      },
      cm: {
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
        id: 0,
        crm_id: '',
        stu_id: '',
        name: '',
        type: 0,
        date_of_birth: '',
        phone: '',
        email: '',
        gender: '',
        school: '',
        address: '',
        job_title: '',
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
        gud_gender1:'',
        gud_gender2:'',
        avatar: '',
        branch_id: '',
        cm_id: '',
        ec_id: '',
        cs_id: '',
        source: 1,
        tracking: 1,
        facebook: '',
        checked: true,
        sibling_id: '',
        sibling_name: '',
        sibling_gud_mobile1:'',
        attached_file: ''
      }
		}
		model.html.data = {
      jobs: {
        item: '',
        list: []
      },
      sources: {
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
        list: []
      },
      cs: {
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
      }
    }
    return model
  },

  components: {
    inp,
    abt,
    file,
    datePicker,
    Multiselect,
    "vue-select": select,
  },

  created(){
    this.start()
    this.checkUserRole()
    document.body.addEventListener('scroll', this.checkRequired())
  },
  destroyed () {
    document.body.removeEventListener('scroll', this.checkRequired())
  },
  methods: {
    checkUserRole(){
      let role = this.role
      if(role == 999999999 || role == 56){
        this.html.dom.edit.disabled = false
      }
    },
    start() {
      this.html.loading.action = true
      u.g(`/api/gud_job/all_list`)
        .then(response => {
          console.log(response)
          this.html.data.jobs.list = response
        })
      u.g(`/api/sources`)
        .then(response => {
          this.html.data.sources.list = response
        })
      u.g(`/api/provinces`)
      .then(response => {
        this.html.data.province.list = response
        u.g(`/api/students/${this.$route.params.id}`).then((response) => {
          const student = response.student
          this.obj.student = student
          this.obj.student.branch = {branch_id: student.branch_id, name: student.branch_name}
          this.obj.student.job1 = this.html.data.jobs.list.filter(item => item.id == this.obj.student.gud_job1)[0]
          this.obj.student.job2 = this.html.data.jobs.list.filter(item => item.id == this.obj.student.gud_job2)[0]
          if( this.obj.student.school_level=="Tiểu học"){
            this.getSchools()
            this.obj.student.select_school = this.obj.student.school
          }
          this.html.data.district.list = response.student.districts
          this.student_branch_id = this.obj.student.branch_id
          this.html.data.ec.item = this.obj.student.ec_id
          this.html.data.cm.item = this.obj.student.cm_id
          this.obj.student.cm_id = student.cm_id
          this.getDistrict()
          this.getUsersByBranch(this.obj.student.branch);
          this.html.dom.cm.display = ''
          u.g('/api/students/get/ec/of/users/branch')
          .then(data => {
            this.obj.student.job_title = data.title_id
            this.html.data.school_grade.list = data.school_grades
            this.html.data.school_grade.item = ''
            this.html.data.branch.list = data.branches;
            // this.obj.student.branch_id = parseInt(data.branch_id) > 0 ? data.branch_id : ''
            switch (data.title_id) {
              case 1 : {
                //this.obj.student.branch_id = ''
                this.html.dom.branch.display = ''
                this.html.dom.ec.display = ''
                this.html.dom.cm.display = ''
                this.html.dom.branch.disabled = false
              } break
              case 2 : {
                this.html.data.ec.list = data.ecs
                this.html.data.cm.list = data.cms
                // this.obj.student.cm_id = ''
                // this.obj.student.ec_id = data.ec_id
                this.html.dom.cm.display = ''
                // this.html.data.cm.item = ''
                this.html.dom.cm.disabled = false
              } break
              case 3 : {
                this.html.data.ec.list = data.ecs
                this.html.data.cm.list = data.cms
                // this.obj.student.ec_id = ''
                this.obj.student.cm_id = data.cm_id
                this.html.dom.ec.display = ''
                this.html.data.ec.item = ''
                this.html.dom.ec.disabled = false
              } break
              case 4 : {
                this.html.dom.ec.display = ''
                this.html.dom.cm.display = ''
                this.html.data.ec.list = data.ecs
                this.html.data.cm.list = data.cms
                this.html.data.ec.item = data.ec_id
                this.html.data.cm.item = data.om_id || data.cm_id
                // this.obj.student.ec_id = data.ec_id
                // this.obj.student.cm_id = data.om_id || data.cm_id
                this.html.dom.cm.disabled = false
                this.html.dom.ec.disabled = false
              } break
            }
            this.html.loading.action = false
            this.resetCM()
            this.inputSibling()
            if(this.obj.student.is_active_class){
              this.html.dom.cm.disabled = true
            }else{
              if([999999999,56].indexOf(u.session().user.role_id)!=-1){
                this.html.dom.cm.disabled = false
              }else{
                this.html.dom.cm.disabled = true
              }
            }
          })
        })
      })
    },
    callback() {
      this.html.dom.modal.display = false
      if (this.expired) {
        u.go(this.$router, '/login')
      }
      if (this.completed) {
        u.go(this.$router, '/students/list/1')
      }
    },
    onDrawDate (e) {
      let date = e.date
      if (new Date().getTime() > date.getTime()) {
        e.allowSelect = false
      }
    },
    getUsersByBranch(branch){
      this.obj.student.branch_id = branch.branch_id
      u.g(`/api/students/get/ec/of/a/branch/${this.obj.student.branch_id}`).then(data => {
        if (data.ecs.length) {
          this.html.data.ec.list = data.ecs
          this.html.data.ec.item = ''
          this.html.dom.ec.disabled = false
        }
        if (data.cms.length) {
          this.html.data.cm.list = data.cms
          this.html.data.cm.item = ''
          this.html.dom.cm.disabled = false
        }
      })
    },
    selectBirthDay(date) {
      this.obj.student.date_of_birth = date
    },
    saveForm(){
      this.validateExist()
    },
    reject() {
      this.html.dom.modal.message = `Thông tin về học viên ${this.obj.student.name} đã bị trùng trong cơ sở dữ liệu!`
      this.html.dom.modal.title = 'Cảnh Báo'
      this.html.dom.modal.class = 'modal-primary'
      this.html.dom.modal.display = true
    },
    resetForm() {
      this.start()
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
        this.checkDublicateStudent(condition, true)
      }
    },
    inputSibling(){
      let crm_id = this.obj.student.sibling_id
      if ((!isNaN(crm_id) || crm_id.length > 4) && crm_id!=null) {
        /// crm_id = !isNaN(crm_id) ? parseInt(crm_id) : crm_id.substring(4)
        u.g(`/api/students/sibling/${crm_id}?student_id=${this.obj.student.id}`).then(response => {
          if (response && response.id && u.is.has(response, 'name')) {
            this.obj.student.sibling_name = response.name
            this.obj.student.sibling_gud_mobile1 = response.gud_mobile1
            if (response.id >0){
              this.html.data.sibling.id = response.id
              this.obj.student.gud_mobile1 = response.gud_mobile1
            }
            if (u.is.has(response, 'sibling_id') && parseInt(response.id) === 0) {
              this.obj.student.id = ''
            }
          }else if(response && !response.id){
            this.html.dom.modal.message = `Mã CMS anh/chị em học cùng không hợp lệ!`
            this.obj.student.sibling_name = `Mã CMS anh/chị em học cùng không hợp lệ!`
            this.html.dom.modal.title = 'Cảnh Báo'
            this.html.dom.modal.class = 'modal-warning'
            //this.html.dom.modal.display = true
            //this.obj.student.sibling_id=''
          }
        })
      }
    },
    validateInput() {
      let validated = true
      let alert_msg = ''
      if (this.obj.student.crm_id == '') {
        validated = false
        alert_msg+= '(*) Mã CMS là bắt buộc<br/>'
      }
      if (this.obj.student.phone && (this.obj.student.phone.length < 10 || this.obj.student.phone.length > 10)) {
        validated = false
        alert_msg+= '(*) Số điện thoại học viên chỉ được dài từ 10 chữ số<br/>'
      }
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
        validated = false
        alert_msg+= '(*) Địa chỉ là bắt buộc<br/>'
      }
      if ((this.obj.student.date_of_birth == '' || this.obj.student.date_of_birth == 'Invalid date') && this.role !=80 && this.role !=81) {
        validated = false
        alert_msg+= '(*) Ngày sinh là bắt buộc<br/>'
      }
      if (this.obj.student.gender == '') {
        validated = false
        alert_msg+= '(*) Giới tính là bắt buộc<br/>'
      }
      if ((this.obj.student.school_level == '' || this.obj.student.school_level == null) && this.role !=80 && this.role !=81) {
        validated = false
        alert_msg+= '(*) Cấp trường là bắt buộc<br/>'
      }
      this.obj.student.school =this.obj.student.school.trim()
      if (this.obj.student.school == '' && this.role !=80 && this.role !=81) {
        validated = false
        alert_msg+= '(*) Trường học là bắt buộc<br/>'
      }
      if (this.obj.student.province_id == '') {
        validated = false
        alert_msg+= '(*) Tỉnh thành phố là bắt buộc<br/>'
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
      if (this.obj.student.gud_name1 == '') {
        validated = false
        alert_msg+= '(*) Họ tên phụ huynh 1 là bắt buộc<br/>'
      }
      if (this.obj.student.gud_job1 == '' && this.role !=80 && this.role !=81) {
        validated = false
        alert_msg+= '(*) Nghề nghiệp phụ huynh 1 là bắt buộc<br/>'
      }
      if (this.obj.student.gud_birth_day1 == '') {
        validated = false
        alert_msg+= '(*) Ngày sinh phụ huynh 1 là bắt buộc<br/>'
      }
      if ((this.obj.student.gud_email1 == '' || this.obj.student.gud_email1 == null) && this.role !=80 && this.role !=81 ) {
        validated = false
        alert_msg+= '(*) Email là bắt buộc<br/>'
      }
      if (this.obj.student.gud_email1 && !u.vld.email(this.obj.student.gud_email1.trim())&& this.role !=80 && this.role !=81) {
        validated = false
        alert_msg+= ' Email phụ huynh không đúng định dạng<br/>'
      }
      if (this.obj.student.source == '') {
        validated = false
        alert_msg+= '(*) Nguồn tiếp nhận học sinh là bắt buộc<br/>'
      }
      if (this.obj.student.gud_mobile1 == '') {
        validated = false
        alert_msg+= '(*) Số điện thoại phụ huynh là bắt buộc<br/>'
      }
      if (this.title != 2 && this.obj.student.ec_id == '') {
        validated = false
        alert_msg+= '(*) Thông tin EC là bắt buộc<br/>'
      }
      if (this.obj.student.cm_id === undefined || this.obj.student.cm_id === '') {
        validated = false
        alert_msg+= '(*) Thông tin CS là bắt buộc<br/>'
      }
      if (this.obj.student.school_grade === undefined || this.obj.student.school_grade === '') {
        validated = false
        alert_msg+= '(*) Thông tin Độ tuổi là bắt buộc<br/>'
      }
      // if (this.title != 3 && this.obj.student.cm_id == '') {
      //   validated = false
      //   alert_msg+= '(*) Thông tin CS là bắt buộc<br/>'
      // }
      if (!validated) {
        alert_msg = `Dữ liệu học sinh chưa hợp lệ:<br/>-----------------------------------<br/><br/><p class="text-danger">${alert_msg}</p><br/>Vui lòng nhập đúng trước khi lưu lại!<br/>`
        this.html.dom.modal.message = alert_msg
        this.html.dom.modal.title = 'Cảnh Báo'
        this.html.dom.modal.class = 'modal-warning'
        this.html.dom.modal.display = true
      }
      return validated
    },
    validateExist(){
      let validated = true
      let alert_msg = ''
      let stu_id = this.obj.student.stu_id
      if(!stu_id){
        stu_id = null
      }
      const data = {
        student_id: this.$route.params.id,
        crm_id: this.obj.student.crm_id,
        stu_id: stu_id,
        name: this.obj.student.name,
        gud_mobile1: this.obj.student.gud_mobile1,
        gud_mobile2: this.obj.student.gud_mobile2,
      }

      // u.a().post(`/api/students/check-edit-exist-information`, data).then(response => {
      //   const rs = response.data
      //   if(rs === -1){
      //     alert("Trùng mã CMS")
      //     return false
      //   }else

         // {
          //if (this.obj.student.sibling_name == '' || this.obj.student.sibling_name == null) {
             if (this.obj.student.gud_mobile1) {
               u.a().get(`/api/students/check-phone-parent-edit/${this.obj.student.gud_mobile1}/${this.$route.params.id}/${this.obj.student.branch_id}?gud=1&sibling=${this.html.data.sibling.id}`).then(response => {
                 if (response.data.data.existed == 1) {
                   this.error = true
                   alert("Số điện thoại của phụ huynh 1"+response.data.data.msg)
                   return false
                 }
                 else{
                   if (this.obj.student.gud_mobile2){
                     u.a().get(`/api/students/check-phone-parent-edit/${this.obj.student.gud_mobile2}/${this.$route.params.id}/${this.obj.student.branch_id}?gud=2&sibling=${this.html.data.sibling.id}`).then(response => {
                       if (response.data.data.existed == 1) {
                         alert("Số điện thoại của phụ huynh 2"+response.data.data.msg)
                         return false
                       }else{
                         this.store()
                       }
                     })
                   }
                   else{
                     this.store()
                   }
                 }
               })
             }
            //}
            // else {
            //     // if(this.obj.student.sibling_gud_mobile1 !=this.obj.student.gud_mobile1){
            //     //   alert("Số điện thoại của phụ huynh của anh chị em học cùng không giống nhau")
            //     //   return false
            //     // }else{
            //       this.store()
            //     // }
            // }

       // }
      // })
    },
    checkRequired() {
      if (this.obj.student.name != '' && this.obj.student.date_of_birth != ''
        && this.obj.student.gender != '' && this.obj.student.province_id != ''
        && this.obj.student.email != '' && this.obj.student.phone != '' && (this.title != 2 && this.obj.student.ec_id != '')
        && this.obj.student.gud_name1 != '' && this.obj.student.gud_mobile1 != '') {
          this.html.dom.button.save.disabled = false
          this.html.dom.button.reset.disabled = false
      }
      this.inputSibling()
      this.resetCM()
    },
    resetCM() {
      if (this.cache && this.obj.student && this.obj.student.cm_id && this.obj && this.obj.student && this.obj.student.cm_id === '') {
        this.obj.student.cm_id = this.obj.student.cm_id
      }
    },
    store() {
      if (this.validateInput()) {
        const student_data = this.obj.student
        if (this.obj.student.stu_id) {
          this.html.loading.action = true
          let schg = 'other'
          for(let i in this.html.data.school_grade.list){
              if(parseInt(this.obj.student.school_grade) === parseInt(this.html.data.school_grade.list[i].class_id)){
                  schg = this.html.data.school_grade.list[i].class_name
              }
          }
          this.html.loading.action = true
        }
        this.saveUpdateStudent(student_data)
      }
    },
    saveUpdateStudent(student_data) {
      student_data.job_title = this.job_title
      // student_data.date_of_birth = this.moment(this.obj.student.date_of_birth, 'DD/MM/YYYY').format('YYYY-MM-DD')
      // student_data.gud_birth_day1 = this.moment(this.obj.student.gud_birth_day1, 'DD/MM/YYYY').format('YYYY-MM-DD')
      // student_data.gud_birth_day2 = this.moment(this.obj.student.gud_birth_day2, 'DD/MM/YYYY').format('YYYY-MM-DD')
      u.p(`/api/students/update/${this.$route.params.id}`, student_data).then(response => {
        if (response.success) {
          this.completed = true
          let msg_content = `Hồ sơ học viên mới:<br/><b>${response.success}</b><br/> đã được lưu thành công!`
          this.html.loading.action = false
          u.apax.$emit('apaxPopup', {
            on: true,
            content: msg_content,
            title: 'Hồ Sơ Học Sinh Đã Được Lưu Thành Công',
            class: 'modal-success',
            size: 'md',
            hidden: () => { this.callback() },
            confirm: {
                primary: {
                  button: 'OK',
                    action: () => { this.callback() },
                }
            },
            variant: 'apax-ok'
          })
        }
      })
    },
    selectBirthDay() {
      this.obj.student.date_of_birth = this.moment(this.obj.student.date_of_birth, 'DD/MM/YYYY').format('YYYY-MM-DD')
    },
    selectGudBirthDay1(){
      this.obj.student.gud_birth_day1 = this.moment(this.obj.student.gud_birth_day1, 'DD/MM/YYYY').format('YYYY-MM-DD')
    },
    selectGudBirthDay2(){
      this.obj.student.gud_birth_day2 = this.moment(this.obj.student.gud_birth_day2, 'DD/MM/YYYY').format('YYYY-MM-DD')
    },
    validFile(file) {
      let resp = (typeof file === 'string') ? file : ''
      if (typeof file === 'object') {
        // resp = `${file.type},${file.data}`
      }
      return resp
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
    getDistrict(){
      const province_id = this.obj.student.province_id
      u.g(`/api/provinces/${province_id}/districts`).then(response => {
        this.html.data.district.list = response
        // this.obj.student.district_id = this.html.data.district.list[0].id
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
    },
    getSchools(e){
      const school_level = this.obj.student.school_level ? this.obj.student.school_level : e.target.value ? e.target.value : 'Tiểu học'
      const district_id = parseInt(this.obj.student.district_id, 10)
      const province_id = parseInt(this.obj.student.province_id, 10)
      if (school_level && district_id > 0 && province_id > 0) {        
        u.g(`/api/get/${province_id}/${district_id}/${school_level}/schools`).then(response => {
          this.html.data.schools.list = response
        })        
      }
    },
    selectSchool () {
      if (this.obj.student.select_school != 'Other') {
        this.obj.student.school = this.obj.student.select_school
      } else {
        this.obj.student.school = ''
      }
    },
    validShoolName() {
      this.obj.student.school = this.obj.student.school.replace(/[~`!@#$%^&*()=+{}[,\]./<>?;'\\:"|]/gi, '');
    },
    deInputSchool () {
      if (Array.isArray(this.html.data.schools.list) && this.html.data.schools.list.length > 0) {
        this.obj.student.select_school = ''
        this.obj.student.school = ''
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

</style>
