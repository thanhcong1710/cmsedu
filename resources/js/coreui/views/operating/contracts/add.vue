<template>
    <div class="animated fadeIn apax-form" @click="html.dom.action.page">
        <div class="row">
            <div class="col-12">
                <div :class="html.loading.class ? 'loading' : 'standby'" class="ajax-loader">
                    <img :src="html.loading.source">
                </div>
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-id-card"></i> <b class="uppercase">Nhập học</b>
                        <div class="card-actions">
                            <a href="skype:thanhcong1710?chat" target="_blank">
                                <small className="text-muted"><i class="fa fa-skype"></i></small>
                            </a>
                        </div>
                    </div>
                    <div v-show="html.loading.action" class="ajax-load content-loading">
                        <div class="load-wrapper">
                            <div class="loader"></div>
                            <div v-show="html.loading.action" class="loading-text cssload-loader">{{
                                html.loading.content }}
                            </div>
                        </div>
                    </div>
                    <div id="page-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6 pad-no" :class="html.config.style">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Thông tin học sinh</h6>
                                                    </address>
                                                </div>
                                                <div class="col-12 pad-no" :class="html.dom.filter.branch.display">
                                                    <label class="control-label">Chọn Trung Tâm</label>
                                                    <branch
                                                            :onSelect="html.dom.filter.branch.action"
                                                            :options="html.dom.filter.branch.options"
                                                            :disabled="html.dom.filter.branch.disabled"
                                                            :placeholder="html.dom.filter.branch.placeholder">
                                                    </branch>
                                                    <br/>
                                                </div>
                                                <div class="col-12 pad-no" :class="html.dom.filter.search.display">
                                                    <label class="control-label">Tìm kiếm học sinh theo mã CMS hoặc
                                                        Tên</label>
                                                    <search
                                                            :endpoint="html.dom.filter.search.link"
                                                            :suggestStudents="html.dom.filter.search.find"
                                                            :onSelectStudent="html.dom.filter.search.action">
                                                    </search>
                                                    <br/>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Họ Tên</label>
                                                                <input class="form-control" :value="student.name"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã CMS</label>
                                                                <input class="form-control" :value="student.crm_id"
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Mã Hợp Đồng</label>
                                                                <input class="form-control" value=""
                                                                       type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Phụ huynh</label>
                                                        <input class="form-control" :value="student.parent_name"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Số điện thoại</label>
                                                        <input class="form-control" :value="student.parent_mobile"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Email</label>
                                                        <input class="form-control" :value="student.parent_email"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Địa Chỉ Nhà</label>
                                                        <input class="form-control" :value="student.address"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Trường Học</label>
                                                        <input class="form-control" :value="student.school"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Trung Tâm</label>
                                                        <input class="form-control" :value="student.branch_name"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Vùng</label>
                                                        <input class="form-control" :value="student.region_name"
                                                               type="text" readonly>
                                                    </div>
                                                </div> -->
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Học Sinh</label>
                                                        <input class="form-control"
                                                               :value="student.type === 1 ? 'VIP' : 'Thường'"
                                                               type="text" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">EC</label>
                                                                <input class="form-control"
                                                                       :value="student.ec_name" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">EC Leader</label>
                                                                <input class="form-control"
                                                                       :value="student.ec_leader_name" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Giám đốc trung tâm</label>
                                                                <input class="form-control"
                                                                       :value="student.ceo_branch_name" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Giám đốc vùng</label>
                                                                <input class="form-control"
                                                                       :value="student.ceo_region_name" type="text"
                                                                       readonly>
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Thông tin cộng tác viên"
                                                                       class="control-label">Thông tin cộng tác viên</label>
                                                                <textarea readonly
                                                                          class="description form-control" rows="5"
                                                                          :value="refInfo(this.student.ref_info)"></textarea>
                                                                <input class="form-control"
                                                                       :value="student.ref_code" type="hidden">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pad-no" :class="html.config.style">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6 class="text-main">Thông tin đăng ký chương trình học</h6>
                                                    </address>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="filter-label control-label">Chọn Loại Khách Hàng</label><br/>
                                                                    <select
                                                                        id="customer-types"
                                                                        class="selection customer-type form-control"
                                                                        :disabled="html.dom.disabled.customer_type"
                                                                        v-model="data.customer_type"
                                                                        @change="html.dom.action.customer_type(data.customer_type)">
                                                                    <option value="" disabled> Chọn loại khách hàng
                                                                    </option>
                                                                    <!-- <option value="0">Học trải nghiệm</option> -->
                                                                    <option value="1">Chính thức</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-md-6" :class="html.dom.display.receive">
                                                            <div class="form-group checkbox-inrow">
                                                                <label class="control-label">Chỉ nhận chuyển phí</label>
                                                                <input class="pass-trial" type="checkbox" value="1"
                                                                       @change="html.dom.action.receive"
                                                                       v-model="receive">
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="filter-label control-label">Chương trình học</label><br/>
                                                                <select id="products-list"
                                                                        class="selection product form-control"
                                                                        :disabled="html.dom.disabled.product"
                                                                        v-model="product"
                                                                        @change="html.dom.action.product(product)">
                                                                    <option value="" disabled> Chọn chương trình học</option>
                                                                    <option :value="product.product_id"
                                                                            v-for="(product, idx) in html.dom.list.product"
                                                                            :key="idx">
                                                                        {{ product.product_name }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ca học<span class="text-danger">(*)</span></label>
                                                                <select id="customer-types" class="form-control" v-model="data.shift">
                                                                    <option value disabled>Chọn ca học</option>
                                                                      <option :value="shift.id" v-for="(shift, idx) in list_shift" :key="idx">
                                                                        {{ shift.name }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="filter-label control-label">Thông tin chương Trình Học</label><br/>
                                                                <input class="form-control" :disabled="html.dom.disabled.product" type="text" v-model="program"
                                                                    placeholder="Thông tin chương trình học.">
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="filter-label control-label">Gói Học Phí</label><br/>
                                                                <select id="tuition-fees"
                                                                        class="selection tuition-fee form-control"
                                                                        :disabled="html.dom.disabled.tuition_fee"
                                                                        v-model="tuition_fee"
                                                                        @change="html.dom.action.tuition_fee(tuition_fee)">
                                                                    <option value="" disabled> Chọn gói học phí</option>
                                                                    <option :value="tuition_fee.tuition_fee_id"
                                                                            v-for="(tuition_fee, idx) in html.dom.list.tuition_fee"
                                                                            :key="idx">
                                                                        {{ tuition_fee.tuition_fee_name }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 ready" :class="html.dom.display.payload">
                                                            <div class="form-group">
                                                                <label class="control-label">Loại Thu Phí</label>
                                                                <select
                                                                        id="select_payload_type"
                                                                        class="selection payload-type form-control"
                                                                        :disabled="html.dom.disabled.payload"
                                                                        v-model="data.payload"
                                                                        @change="html.dom.payload.action(data.payload)">
                                                                    <option value="" disabled> Chọn loại thu phí
                                                                    </option>
                                                                    <option value="0">Một lần</option>
                                                                    <option value="1">Nhiều lần</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no amount" :class="html.dom.display.payment">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover title="Giá gốc của gói phí"
                                                                       class="control-label">Giá gốc</label>
                                                                <input class="form-control" type="text" readonly
                                                                       v-model="data.price">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6" v-show="false">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Tỷ lệ phần trăm chiết khấu"
                                                                       class="control-label">Tỷ lệ chiết khấu</label>
                                                                <input class="form-control" type="text" readonly
                                                                       v-model="data.receivable">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no discount" :class="html.dom.display.discount" v-show="false">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Giá mới sau khi đã tính chiết khấu"
                                                                       class="control-label">Số tiền sau chiết
                                                                    khấu</label>
                                                                <input class="form-control" type="text"
                                                                       v-model="data.discounted" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no amount" :class="html.dom.display.point">
                                                    <div class="row">
                                                        <div class="col-md-12 pad-no amount"
                                                             :class="html.dom.display.sibling">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Số tiền này sẽ trừ vào giá gốc trước khi tính chiết khấu"
                                                                       class="control-label">Số tiền giảm trừ anh em
                                                                    cùng học (Trừ sau chiết khấu)</label>
                                                                <input :disabled="html.dom.disabled.sibling"
                                                                       class="form-control" type="text"
                                                                       @input="html.dom.action.discount"
                                                                       v-model="data.sibling">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Mã chiết khấu/giảm giá"
                                                                       class="control-label">Mã chiết khấu/giảm
                                                                    giá</label>
                                                                <!--<discount-code-select v-model="data.coupon" :fee-id="fee_id"-->
                                                                                      <!--:disabled="html.dom.disabled.point"/>-->

                                                                <multiselect
                                                                        placeholder="Chọn mã chiết khấu giảm giá"
                                                                        select-label="Enter để chọn mã này"
                                                                        v-model="data.coupon"
                                                                        :options="coupon_list"
                                                                        label="code"
                                                                        :close-on-select="true"
                                                                        :hide-selected="true"
                                                                        :multiple="false"
                                                                        :searchable="true"
                                                                        track-by="code"
                                                                        @input="selectCoupon"
                                                                >
                                                                    <span slot="noResult">Không tìm thấy mã chiết khấu giảm giá phù hợp</span>
                                                                </multiselect>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 pad-no amount">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Mã Voucher"
                                                                       class="control-label">Mã voucher</label>
                                                                <input class="form-control" type="text"
                                                                       v-model="data.coupon_code" @change="checkCoupon">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12" v-if="detailCoupon">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Chi tiết mã chiết khấu/giảm giá"
                                                                       class="control-label">Chi tiết mã chiết khấu/giảm
                                                                    giá</label>
                                                                <textarea disabled
                                                                          class="description form-control" rows="5"
                                                                          v-model="detailCoupon"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Số tiền này sẽ trừ vào giá gốc trước khi tính chiết khấu"
                                                                       class="control-label">Số tiền giảm trừ theo mã chiết khấu
                                                                </label>
                                                                <input disabled
                                                                       class="form-control" type="text"
                                                                       @input="html.dom.action.calculate"
                                                                       v-model="data.point">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label v-b-tooltip.hover
                                                                       title="Số buổi học bổng"
                                                                       class="control-label">Số buổi học bổng
                                                                </label>
                                                                <input disabled
                                                                       class="form-control" type="text"
                                                                       v-model="data.bonus_sessions">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no amount" :class="html.dom.display.point">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label v-b-tooltip.hover
                                                                               title="Số tiền này sẽ trừ vào giá sau chiết khấu"
                                                                               class="control-label">Số tiền
                                                                            Voucher</label>
                                                                        <input :disabled="true"
                                                                               class="form-control" type="text"
                                                                               @input="html.dom.action.calculate"
                                                                               v-model="data.voucher"
                                                                               >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label v-b-tooltip.hover
                                                                               title="Số tiền này sẽ trừ vào giá sau chiết khấu"
                                                                               class="control-label">Số tiền chiết khấu
                                                                            Khác</label>
                                                                        <input :disabled="html.dom.disabled.other"
                                                                               class="form-control" type="text"
                                                                               @input="html.dom.action.calculate"
                                                                               v-model="data.other">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no" :class="html.dom.display.amount">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group price-must-charge">
                                                                <label class="control-label">Số tiền phải đóng</label>
                                                                <input class="form-control" type="text"
                                                                       v-model="data.must_charge" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6" :class="html.dom.display.sessions">
                                                            <div class="form-group tuition-fee-type">
                                                                <label class="control-label">Số buổi học</label>
                                                                <input class="form-control" type="text" readonly
                                                                       v-model="data.sessions">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no" :class="html.dom.display.detail">
                                                    <div class="form-group detail-description">
                                                        <label class="control-label">Chi tiết diễn giải (*)</label>
                                                        <textarea :disabled="html.dom.disabled.detail"
                                                                  class="description form-control" rows="5"
                                                                  placeholder="Chi tiết các khoản khấu trừ"
                                                                  v-model="data.detail"></textarea>
                                                        <input type="hidden" v-model="data.bill_info"/>
                                                        <input type="hidden" v-model="data.total_discount"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no">
                                                    <div class="form-group expected-class">
                                                        <label class="control-label">Lớp học mong muốn</label>
                                                        <input :disabled="html.dom.disabled.expected_class"
                                                               class="form-control" type="text"
                                                               v-model="data.expected_class">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pad-no">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group select-date">
                                                                <label class="control-label">Ngày dự kiến học</label>
                                                                <calendar
                                                                        class="form-control calendar"
                                                                        :value="data.start_date"
                                                                        :transfer="true"
                                                                        :format="html.config.format_date"
                                                                        :disabled-days-of-week="html.config.disable_days_of_week"
                                                                        :clear-button="html.config.clear_date"
                                                                        :placeholder="'Chọn ngày dự kiến học'"
                                                                        :pane="1"
                                                                        :disabled="html.dom.disabled.start_date"
                                                                        :onDrawDate="html.dom.action.draw_start_date"
                                                                        :lang="html.dom.action.lang"
                                                                        :not-before="data.previous_date"
                                                                        @input="html.dom.action.start_date"
                                                                ></calendar>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group select-date">
                                                                <label class="control-label">Ngày dự kiến kết
                                                                    thúc</label>
                                                                <calendar
                                                                        class="form-control calendar"
                                                                        :value="data.end_date"
                                                                        :transfer="true"
                                                                        :format="html.config.format_date"
                                                                        :disabled-days-of-week="html.config.disable_days_of_week"
                                                                        :clear-button="html.config.clear_date"
                                                                        :placeholder="'Ngày dự kiến kết thúc'"
                                                                        :pane="1"
                                                                        :disabled=true
                                                                        :lang="html.dom.action.lang"
                                                                ></calendar>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" v-if="isShowEdit()">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Ngày tạo phiếu ĐK (cyber)</label>
                                                                <datepicker
                                                                        :value="data.back_date"
                                                                        placeholder="Chọn ngày tạo"
                                                                        format="YYYY-MM-DD"
                                                                        @input="this.changeMonth"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer col-lg-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="col-md-12">
                                                    <div class="form-group price-must-charge">
                                                        <label class="control-label">Ghi Chú Nhập Học</label>
                                                        <input class="form-control" type="text" v-model="data.note"
                                                               placeholder="Ghi chú nhập học."
                                                               :disabled="html.dom.disabled.note">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2 mt-2">
                                                <br/>
                                                <apaxbtn
                                                        :onClick="html.dom.action.save"
                                                        :disabled="html.dom.disabled.save"
                                                        :icon="html.config.save_icon"
                                                        :label="html.config.save_label"
                                                        :title="html.config.save_title"
                                                        :markup="html.config.save_markup">
                                                </apaxbtn>
                                                <apaxbtn
                                                        :onClick="html.dom.action.reset"
                                                        :disabled="html.dom.disabled.reset"
                                                        :icon="html.config.reset_icon"
                                                        :label="html.config.reset_label"
                                                        :title="html.config.reset_title"
                                                        :markup="html.config.reset_markup">
                                                </apaxbtn>
                                                <apaxbtn
                                                        :onClick="html.dom.action.cancel"
                                                        :disabled="html.dom.disabled.cancel"
                                                        :icon="html.config.cancel_icon"
                                                        :label="html.config.cancel_label"
                                                        :title="html.config.cancel_title"
                                                        :markup="html.config.cancel_markup">
                                                </apaxbtn>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-card>
                <b-modal
                        size="sm"
                        ok-variant="primary"
                        :title="html.config.modal_title"
                        :class="html.config.modal_class"
                        v-model="html.config.modal"
                        @close="html.dom.action.modal">
                    <div v-html="data.modal">
                    </div>
                    <template slot="modal-footer">
                        <b-btn variant="primary"
                               @click="html.dom.action.modal"
                        >Close
                        </b-btn>
                    </template>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>

    import calendar from 'vue2-datepicker'
    import u from '../../../utilities/utility'
    import branch from '../../../components/Selection'
    import search from '../../../components/StudentSearch'
    import apaxbtn from '../../../components/Button'
    import DiscountCodeSelect from './DiscountCodeSelect'
    import Datepicker from '../../base/common/DatePicker'
    import { getDateCustom } from '../../base/common/utils'
    import multiselect from 'vue-multiselect'

    export default {
        name: 'Add-Contract',
        components: {
            calendar,
            branch,
            search,
            apaxbtn,
            DiscountCodeSelect,
            Datepicker,
            multiselect
        },
        data() {
            const model = u.m('contracts').page
            u.set(model.html.page.url, {
                holidays: '/api/info/[branch]/holidays',
                products: '/api/contracts/products'
            }, 0)
            model.receive = false
            model.product = null
            model.program = null
            model.tuition_fee = null
            model.customer_type = null
            model.html.dom = {
                filter: {
                    branch: {
                        display: 'hidden',
                        options: [],
                        disabled: true,
                        placeholder: 'Xin vui lòng nhập tên trung tâm vào đây để giới hạn phạm vi tìm kiếm trước',
                        action: branch => this.selectBranch(branch)
                    },
                    search: {
                        link: 0,
                        display: 'hidden',
                        find: keyword => this.searchSuggestStudent(keyword),
                        action: student => this.selectStudent(student)
                    }
                },
                display: {
                    point: 'hidden',
                    trial: 'hidden',
                    realy: 'hidden',
                    amount: 'hidden',
                    detail: 'hidden',
                    receive: 'hidden',
                    voucher: 'hidden',
                    sibling: 'hidden',
                    payload: 'hidden',
                    payment: 'hidden',
                    subinfo: 'hidden',
                    sessions: 'hidden',
                    continue: 'hidden',
                    discount: 'hidden',
                    pass_trial: 'hidden',
                    tuition_fee: 'hidden'
                },
                disabled: {
                    note: true,
                    save: true,
                    other: true,
                    point: true,
                    trial: true,
                    realy: true,
                    reset: true,
                    detail: true,
                    cancel: false,
                    voucher: true,
                    sibling: true,
                    payload: true,
                    product: true,
                    program: true,
                    end_date: true,
                    start_date: true,
                    tuition_fee: true,
                    customer_type: true,
                    expected_class: true
                },
                action: {
                    save: () => this.saveForm(),
                    modal: () => this.okModal(),
                    reset: () => this.resetForm(),
                    cancel: () => this.exitForm(),
                    page: () => this.selectStartDate(),
                    receive: () => this.checkOnlyReceive(),
                    continue: () => this.checkContinueClass(),
                    draw_end_date: e => this.onDrawEndDate(e),
                    discount: () => this.recalculateDiscount(),
                    calculate: () => this.recalculateDiscount(),
                    draw_start_date: e => this.onDrawStartDate(e),
                    product: product => this.selectProduct(product),
                    program: program => this.selectProgram(program),
                    start_date: start_date => this.selectStartDate(start_date),
                    tuition_fee: tuition_fee => this.selectTuitionFee(tuition_fee),
                    customer_type: customer_type => this.selectCustomerType(customer_type),
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
                list: {
                    product: [],
                    program: [],
                    tuition_fee: []
                }
            }
            model.html.config = {
                style: '',
                modal: false,
                holidays: [],
                classdays: [2, 5],
                clear_date: true,
                cancel_icon: 'fa-sign-out',
                cancel_title: 'Thoát form nhập học',
                cancel_label: 'Thoát',
                cancel_markup: 'error',
                save_icon: 'fa-save',
                save_title: 'Lưu bản ghi nhập học mới',
                save_label: 'Lưu',
                save_markup: 'success',
                reset_icon: 'fa-recycle',
                reset_title: 'Nhập lại nội dung thông tin',
                reset_label: 'Hủy',
                reset_markup: 'warning',
                disable_days_of_week: [],
                format_date: 'YYYY-MM-DD',
                modal_title: 'Thông Báo',
                modal_class: 'modal-success'
            }
            model.calling = false
            model.temp = {}
            model.data = {
                vip: 0,
                note: '',
                price: 0,
                point: 0,
                other: 0,
                branch: 0,
                modal: '',
                style: '',
                detail: '',
                sibling: 0,
                student: 0,
                receive: 0,
                voucher: 0,
                coupon: '',
                payload: '',
                product: '',
                program: '',
                continue: 0,
                sessions: 0,
                end_date: '',
                pass_trial: 0,
                receivable: 0,
                discounted: 0,
                bill_info: '',
                must_charge: 0,
                start_date: '',
                tuition_fee: '',
                other_amount: 0,
                customer_type: '',
                total_discount: 0,
                previous_date: '',
                expected_class: '',
                latest_contract: {},
                new_price_amount: 0,
                discounted_amount: 0,
                store_information: {},
                must_charge_amount: 0,
                discount_percentage: 0,
                total_point_sibling: 0,
                calculated_discount: 0,
                total_voucher_other: 0,
                ref_code: '',
                back_date: '',
                bonus_sessions:0,
                tmp_bonus_sessions:0,
                bonus_amount:0,
                tmp_bonus_amount:0,
                shift:'',
                coupon_code:'',
                coupon_amount:0,
                coupon_session:0,
                tuition_fee_price_min:0,
            }
            model.calling = false
            model.cache = model.data
            model.student = {
                name: '',
                nick: '',
                type: '',
                school: '',
                ec_name: '',
                address: '',
                parent_name: '',
                branch_name: '',
                region_name: '',
                parent_email: '',
                parent_mobile: '',
                ec_leader_name: '',
                ceo_branch_name: '',
                ceo_region_name: '',
                ref_info:null,
                ref_code:'',
            }
            model.fee_id = 0
            model.coupon_list = []
            model.list_shift = []
            return model
        },
        created() {
            this.start()
        },
        computed: {
            detailCoupon() {
                const name = _.get(this, 'data.coupon.name');
                const percent = _.get(this, 'data.coupon.percent');
                const discount = _.get(this, 'data.coupon.discount');
                const bonus_sessions = _.get(this, 'data.coupon.bonus_sessions');
                const bonus_amount = _.get(this, 'data.coupon.bonus_amount');
                const price = _.get(this, 'data.coupon.price');
                if (!name || (!discount && !bonus_sessions) || !price ) {
                    this.data.point = 0;
                    this.recalculateDiscount();
                    return ''
                }
                this.data.point = discount;
                this.recalculateDiscount();
                return `- Tên: ${name}\n- Tỷ lệ chiết khấu: ${percent}%\n- Giá gốc: ${this.format(price)}\n- Số tiền chiết khấu: ${this.format(discount)}\n- Học bổng: ${Number(bonus_sessions)} buổi`
                
            }
        },
        methods: {
            getListDiscountCode (feeId) {
            this.html.loading.action = true
            u.a()
              .get(`/api/discount-codes/available/${feeId}/${this.cache.branch}?type=1`)
              .then((response) => {
                this.data.coupon = {}
                this.coupon_list  = [...response.data.data]
                this.html.loading.action = false
              })
            },
            changeMonth(value){

              this.html.page.back_date = value//getDateCustom(value)
              this.data.back_date = getDateCustom(value)
            },
            isShowEdit(){
            if (u.session().user.role_id == 999999999){
              return true
            }
            else
              return false
            },
            start() {
                if (u.authorized() || u.session().user.branches.length >1) {
                    this.html.dom.filter.branch.display = 'display'
                    this.html.dom.filter.branch.disabled = false
                    this.html.loading.action = false
                } else {
                    this.data.branch = parseInt(this.session.user.branch_id)
                    this.ready()
                    u.a().get(`/api/shifts/branch/${this.data.branch}?status=1`).then(response => {
                        this.list_shift = response.data;
                    })
                }
                this.html.dom.disabled.cancel = false
            },
            okModal() {
                if (this.completed) {
                    this.exitForm()
                } else this.html.config.modal = false
            },
            ready() {
                this.html.dom.filter.search.display = 'display'
                this.html.dom.filter.search.disabled = false
                this.html.dom.filter.search.link = this.cache.branch
                this.getHolidays(this.cache.branch)
            },
            isDate: v => v instanceof Date,
            stringify: v => !this.isDate(v) ? null : this.moment(v).format('YYYY-MM-DD'),
            filled: v => String(v).replace(/^(\d)$/, '0$1'),
            format: (num) => num && num > 1000 ? u.currency(num, 'đ') : '0đ',
            amount: (txt) => txt ? parseInt(txt.replace('đ', '').replace(/,/g, ''), 10) : 0,
            siblings(v, n) {
                var REG = /\d+/g
                v = v.match(REG)
                return this.stringify(new Date(v[0], v[1] - 1, v[2] * 1 + n * 1))
            },
            onDrawStartDate(e) {
                let date = e.date
                if (this.data.previous_date > date.getTime()) {
                    e.allowSelect = false
                }
            },
            onDrawEndDate(e) {
                let date = e.date
                if (this.moment(this.data.end_date) > this.moment(date.getTime())) {
                    e.allowSelect = false
                }
            },
            getHolidays(branch_id) {
                u.g(u.pr(this.html.page.url.holidays, 'branch', branch_id))
                    .then(response => {
                        this.html.config.holidays = response
                        this.html.loading.action = false
                    }).catch(e => console.log(e))
            },
            searchSuggestStudent(keyword) {
                if (keyword && keyword.length > 3 && this.calling === false) {
                    this.calling = true
                    return new Promise((resolve, reject) => {
                        u.g(`${this.html.page.url.apis}search/students/${this.cache.branch}/${keyword}`)
                            .then((response) => {
                                const resp = response.length ? response : [{
                                    label: 'Không tìm thấy',
                                    branch_name: 'Không có kết quả nào phù hợp'
                                }]
                                this.calling = false
                                resolve(resp)
                            }).catch(e => console.log(e))
                    })
                }
            },
            calculateDiscount() {
                const the_point = u.fmc(this.data.point)
                const the_other = u.fmc(this.data.other)
                const the_voucher = u.fmc(this.data.voucher)
                const the_sibling = u.fmc(this.data.sibling)
                this.data.point = the_point.s
                this.data.other = the_other.s
                this.data.voucher = the_voucher.s
                this.data.sibling = the_sibling.s
                const number_session = parseInt(this.cache.tuition_fee.tuition_fee_session, 10)
                this.data.sessions = number_session.toString()
                const selected_tuition_price = this.cache.tuition_fee.tuition_fee_price
                const selected_tuition_discount = this.cache.tuition_fee.tuition_fee_discount
                const selected_tuition_receivable = this.cache.tuition_fee.tuition_fee_receivable
                this.data.price = this.format(selected_tuition_price)
                const low_discount = selected_tuition_discount / 100000
                const low_price = selected_tuition_price / 100000
                const percentage = (low_discount / low_price) * 100
                this.data.discount_percentage = u.pct(percentage, 1)
                this.data.total_point_sibling =/* the_point.n + // trường này là tiền mã chiết khấu giảm giá*/ the_sibling.n
                const left_percent = (100 - u.pct(percentage, 1)) / 100
                this.data.calculated_discount = this.data.total_point_sibling > 0 ? Math.round(selected_tuition_receivable - this.data.total_point_sibling) : selected_tuition_receivable
                this.data.total_discount = this.data.total_point_sibling > 0 ? this.data.total_point_sibling + Math.floor(this.data.calculated_discount / 1000) * 1000 : selected_tuition_receivable
                this.data.discounted_amount = selected_tuition_receivable
                this.data.discounted = this.format(selected_tuition_receivable)
                this.data.new_price_amount = selected_tuition_receivable
                this.data.receivable = `${u.pct(percentage, 1)}%`
                this.data.total_voucher_other = the_voucher.n + the_other.n
                this.data.must_charge_amount = this.data.discounted_amount - this.data.total_voucher_other - the_point.n - the_sibling.n
                this.data.must_charge = this.format(this.data.must_charge_amount)
                this.data.bill_info = ''
                this.data.detail = ''
                if (!this.temp.must_charge || this.temp.must_charge === '') {
                    this.temp.must_charge = this.data.must_charge
                    this.temp.voucher = this.data.voucher
                    this.temp.sibling = this.data.sibling
                    this.temp.point = this.data.point
                    this.temp.other = this.data.other
                }
            },
            recalculateDiscount() {
                this.calculateDiscount()
                const the_point = u.fmc(this.data.point)
                const the_other = u.fmc(this.data.other)
                const the_voucher = u.fmc(this.data.voucher)
                const the_sibling = u.fmc(this.data.sibling)
                const price = parseInt(this.cache.tuition_fee.tuition_fee_receivable, 10)
                if (this.data.must_charge_amount < 0) {
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Lưu ý!',
                        type: 'caution dark',
                        duration: 3000,
                        text: 'Tổng tiền khuyến mại không thể vượt quá số tiền phải đóng!'
                    })
                    this.data.point = this.temp.point
                    this.data.other = this.temp.other
                    this.data.voucher = this.temp.voucher
                    this.data.sibling = this.temp.sibling
                    this.data.must_charge = this.temp.must_charge
                } else {
                    this.html.dom.disabled.detail = false
                    this.data.bill_info += `Chiết khấu (${u.pct(this.data.discount_percentage, 1)}%): ${this.format(parseInt((this.cache.tuition_fee.tuition_fee_discount) / 1000 * 1000))}<br/>------------------------------<br/>Giá Thực Thu: ${this.format(parseInt(this.data.discounted_amount))}<br/><br/><br/>`
                    this.data.detail += `Chiết khấu (${u.pct(this.data.discount_percentage, 1)}%): ${this.format(parseInt((this.cache.tuition_fee.tuition_fee_discount) / 1000 * 1000))}\n------------------------------\nGiá Thực Thu: ${this.format(parseInt(this.data.discounted_amount))}\n\n\n`
                    if (parseInt(the_point.n)) {
                        this.data.bill_info += `Tiền chiết khấu: ${the_point.s}<br/>`
                        this.data.detail += `Giảm trừ theo Mã chiết khấu: ${the_point.s}đ\n`
                    }
                    if (parseInt(the_sibling.n)) {
                        this.data.bill_info += `Anh Chị Em: ${the_sibling.s}<br/>`
                        this.data.detail += `Giảm trừ Anh Chị Em: ${the_sibling.s}\n`
                    }
                    // if (parseInt(the_point.n) || parseInt(the_sibling.n)) {
                    //   this.data.bill_info += `Chiết khấu (${u.pct(this.data.discount_percentage, 1)}%): ${this.format(parseInt((this.cache.tuition_fee.tuition_fee_price - this.data.discounted_amount) / 1000 * 1000))}<br/>------------------------------<br/>Giá Thực Thu: ${this.format(parseInt(this.data.discounted_amount))}<br/><br/><br/>`
                    //   this.data.detail += `Chiết khấu (${u.pct(this.data.discount_percentage, 1)}%): ${this.format(parseInt((this.cache.tuition_fee.tuition_fee_price - this.data.discounted_amount) / 1000 * 1000))}\n------------------------------\nGiá Thực Thu: ${this.format(parseInt(this.data.discounted_amount))}\n\n\n`
                    // }
                    if (parseInt(the_voucher.n)) {
                        this.data.bill_info += `Voucher: ${the_voucher.s}<br/>`
                        this.data.detail += `Khấu trừ Voucher: ${the_voucher.s}\n`
                    }
                    if (parseInt(the_other.n)) {
                        this.data.bill_info += `Khấu trừ khác: ${the_other.s}<br/><br/><br/>`
                        this.data.detail += `Khấu trừ Khác: ${the_other.s}\n`
                    }
                    const tong_khau_tru = parseInt(this.data.total_voucher_other, 10) + parseInt(this.data.total_point_sibling, 10) + parseInt(the_point.n);
                    if (tong_khau_tru) {
                        this.data.detail += `------------------------------\nTổng khấu trừ: ${this.format(tong_khau_tru)}\n`
                        this.data.detail += `\nSố tiền còn lại phải đóng:\n ${this.format(this.data.discounted_amount)} - ${this.format(tong_khau_tru)}\n------------------------------\n = ${this.data.must_charge}`
                    }
                }
            },
            selectStartDate(selected_start_date = '') {
                const start_date = selected_start_date ? selected_start_date : this.data.start_date
                if(!u.validDate(start_date)) return;

                let sessions = this.cache.tuition_fee && this.cache.tuition_fee.tuition_fee_session ? parseInt(this.cache.tuition_fee.tuition_fee_session, 10) : 3
                if (start_date) {
                    const start_moment = moment(start_date)
                    this.html.config.classdays = [start_moment.day()]
                    if (this.data.latest_contract) {
                        const class_days = this.data.latest_contract.enrolment_schedule ? this.data.latest_contract.enrolment_schedule.toString().split(',') : ''
                        if (class_days.length) {
                            const buffer = []
                            class_days.map(d => {
                                buffer.push(parseInt(d, 10))
                                return d
                            })
                            this.html.config.classdays = buffer
                        }
                    }
                    this.data.start_date = start_date
                    const k = this.data.product && this.data.product.product_id ? parseInt(this.data.product.product_id, 10) : 0
                    if (!this.data.customer_type || parseInt(this.data.customer_type, 10) === 0) {
                        this.data.end_date = this.moment(start_date).format('YYYY-MM-DD')
                    } else if (parseInt(this.data.customer_type, 10) === 1) {
                        const sessions = this.cache.tuition_fee && this.cache.tuition_fee.tuition_fee_session ? parseInt(this.cache.tuition_fee.tuition_fee_session, 10) : 0
                        const classSchedule = u.calEndDate(sessions, this.html.config.classdays, this.html.config.holidays[k], start_date)
                        this.data.end_date = classSchedule.end_date
                        this.end_date = classSchedule.end_date
                    }
                } else {
                    this.data.end_date = ''
                    this.end_date = ''
                }
                 this.toggleButtonsBar()
            },
            selectBranch(branch) {
                this.data.branch = branch.id
                this.ready()
                u.a().get(`/api/shifts/branch/${branch.id}?status=1`).then(response => {
                    this.list_shift = response.data;
                })
            },
            selectStudent(student) {
                if(student.student_id){
                    this.resetForm(0)
                    this.html.config.style = 'apax-show-detail'
                    this.html.loading.action = true
                    this.html.loading.class = true
                    this.data.student = student.student_id
                    u.g(`${this.html.page.url.products}/${this.cache.branch}/${this.data.student}`)
                    .then(response => {
                        if (response) {
                            if(response.has_error==0){
                                this.data.latest_contract = response.information.latest_contract
                                if (this.data.latest_contract && this.data.latest_contract.enrolment_schedule) {
                                    const class_days = this.data.latest_contract.enrolment_schedule.toString().split(',')
                                    if (class_days.length) {
                                        this.html.config.classdays = []
                                        for(let i in class_days){
                                        this.html.config.classdays.push(parseInt(class_days[i]))
                                        }
                                        u.log(this.html.config.classdays)
                                    }
                                }
                                if (response.information.latest_date && response.information.latest_contract) {
                                    const k = this.data.product.product_id ? parseInt(this.data.product.product_id, 10) : 1
                                    const date_info = u.calEndDate(2, this.html.config.classdays, this.html.config.holidays[k], response.information.latest_date)
                                    this.data.start_date = date_info.end_date
                                } else {
                                    this.data.start_date = response.information.latest_date && response.information.latest_date != this.moment().format('YYYY-MM-DD') ? this.moment(response.information.latest_date).add(1, 'days').format('YYYY-MM-DD') : this.moment().format('YYYY-MM-DD')
                                }
                                /***** Logic change on 08/01/2019 *****/
                                if (response.information.latest_contract) {
                                    let lc = response.information.latest_contract
                                    if (lc.type === 0) {
                                        if (lc.enrolment_last_date === null && lc.enrolment_schedule === null) {
                                            this.data.start_date = this.moment(lc.end_date).add(1, 'days').format('YYYY-MM-DD')
                                        } else {
                                            this.data.start_date = this.moment(lc.enrolment_last_date).add(1, 'days').format('YYYY-MM-DD')
                                        }
                                    } else {
                                        this.data.start_date = this.moment().format('YYYY-MM-DD')
                                    }
                                } else if (response.information.latest_contract && response.information.latest_contract.status === 7) {
                                    this.data.start_date = this.moment(response.information.latest_contract.enrolment_last_date).add(1, 'days').format('YYYY-MM-DD')
                                } else {
                                    this.data.start_date = response.information.latest_date && response.information.latest_date != this.moment().format('YYYY-MM-DD') ? this.moment(response.information.latest_date).add(1, 'days').format('YYYY-MM-DD') : this.moment().format('YYYY-MM-DD')
                                }
            
                                /***** End change on 08/01/2019 *****/
                                if (u.session().user.role_id == 999999999){
                                this.data.previous_date = this.moment(this.data.start_date).add(-180, 'days').format('YYYY-MM-DD')
                                }
                                else{
                                this.data.previous_date = this.moment(this.data.start_date).add(-30, 'days').format('YYYY-MM-DD')
                                }

                                this.selectStartDate(this.data.start_date)
                                this.html.dom.list.product = response.products
                                this.student = response.student
                                this.data.vip = parseInt(response.student.type) === 1
                                this.html.dom.disabled.product = true
                                this.html.dom.disabled.program = true
                                this.html.dom.disabled.customer_type = false
                                this.html.dom.display.receive = 'display'
                                this.data.product = null
                                this.data.program = null
                                this.product = ''
                                this.program = ''
                                this.receive = false
                                this.ajax_loading = false
                                this.html.loading.action = false
                                this.html.loading.class = false
                                // if (this.data.start_date !== this.moment().format('YYYY-MM-DD')) {
                                //     this.selectStartDate()
                                // }
                                this.$notify({
                                    group: 'apax-atr',
                                    title: 'Lưu ý!',
                                    type: 'primary',
                                    duration: 3000,
                                    text: 'Xin vui lòng chọn loại khách hàng!'
                                })
                            }else{
                                alert(response.message)
                                location.reload()
                            }
                        } else {
                            // alert('Không thể xử lý được vì dữ liệu về chương trình học của kỳ học này chưa đầy đủ, xin vui lòng kiểm tra kỹ các dữ liệu liên qua trước khi thực hiện!')
                            // this.exitForm()
                        }
                    }).catch(e => console.log(e))
                }
            },
            selectCustomerType(customer_type_id) {
                this.data.tuition_fee = ''
                this.tuition_fee = ''
                this.cache.product = null
                this.cache.program = null
                this.data.product = null
                this.data.program = null
                this.product = ''
                this.program = ''
                this.resetMinForm()
                this.data.customer_type = customer_type_id
                // if (customer_type_id === 1 || customer_type_id === '1') {
                    this.toggleButtonsBar()
                    this.html.dom.disabled.product = false
                    this.html.dom.disabled.program = true
                    this.$notify({
                        group: 'apax-atr',
                        title: 'Lưu ý!',
                        type: 'info',
                        duration: 3000,
                        text: 'Xin vui lòng chọn 1 gói sản phẩm!'
                    })
                if (customer_type_id === 0 || customer_type_id === '0'){
                    this.html.dom.disabled.tuition_fee = true
                    this.html.dom.disabled.voucher = true
                    this.html.dom.disabled.other = true
                    this.html.dom.disabled.detail = true
                }else{
                    this.showCalculationObjects()
                }
                // } else if (customer_type_id === 0 || customer_type_id === '0') {
                //     this.toggleButtonsBar(1)
                //     this.receive = false
                //     this.hideCalculationObjects()
                //     u.set(this.html.dom.disabled, {
                //         product: true,
                //         program: true,
                //         end_date: true,
                //         start_date: false,
                //         expected_class: false
                //     })
                // }
            },
            checkOnlyReceive() {
                if (this.receive) {
                    this.resetMinForm()
                    this.html.dom.disabled.customer_type = true
                } else {
                    this.html.dom.disabled.customer_type = false
                }
                this.cache.product = null
                this.cache.program = null
                this.data.product = null
                this.data.program = null
                this.product = ''
                this.program = ''
                this.data.customer_type = 1
                this.customer_type = 1
                this.data.tuition_fee = ''
                this.tuition_fee = ''
                this.toggleButtonsBar(1)
                u.set(this.html.dom.disabled, {
                    end_date: true,
                    product: false,
                    program: true,
                    tuition_fee: true,
                    start_date: false,
                    expected_class: true
                })
                this.$notify({
                    group: 'apax-atr',
                    title: 'Lưu ý!',
                    type: 'info',
                    duration: 3000,
                    text: 'Xin vui lòng chọn 1 gói sản phẩm!'
                })
                this.selectStartDate()
            },
            selectProduct(product_id) {
                this.product = product_id
                if (this.receive) {
                    u.set(this.html.dom.display, {
                        tuition_fee: 'hidden',
                        trial: 'hidden',
                        realy: 'hidden',
                        subinfo: 'hidden',
                        pass_trial: 'hidden',
                        payment: 'hidden',
                        voucher: 'hidden',
                        discount: 'hidden',
                        amount: 'hidden',
                        sessions: 'hidden',
                        detail: 'hidden'
                    })
                    this.html.dom.disabled.expected_class = false
                    this.html.dom.disabled.tuition_fee = true
                    this.data.tuition_fee = ''
                    this.html.dom.disabled.note = false
                    this.html.dom.disabled.save = false
                    this.html.dom.disabled.reset = false
                    this.$notify({
                        group: 'notify',
                        title: 'Lưu Ý',
                        text: 'Xin vui lòng chọn ngày dự kiến học!'
                    })
                } else {
                    this.product = product_id
                    this.data.product = this.html.dom.list.product[`product_id${product_id}`]
                    this.cache.product = this.html.dom.list.product[`product_id${product_id}`]
                    this.html.dom.list.tuition_fee = this.cache.product.tuition_fees
                    this.data.tuition_fee = null
                    this.tuition_fee = ''
                    if (parseInt(this.data.customer_type, 10) === 1) {
                        u.set(this.html.dom.display, {
                            tuition_fee: 'display',
                            trial: 'hidden',
                            realy: 'hidden',
                            subinfo: 'display',
                            pass_trial: 'display',
                            payment: 'display',
                            voucher: 'display',
                            discount: 'display',
                            amount: 'display',
                            sessions: 'display',
                            detail: 'display'
                        })
                        this.resetCalculationFields()
                        this.showCalculationObjects()
                        this.html.dom.disabled.tuition_fee = false
                        this.$notify({
                            group: 'apax-atr',
                            title: 'Lưu ý!',
                            type: 'primary',
                            duration: 3000,
                            text: 'Xin vui lòng chọn 1 gói học phí và điền thông tin chương trình học!'
                        })
                    } else {
                        u.set(this.html.dom.disabled, {
                            end_date: true,
                            start_date: false,
                            tuition_fee: true,
                            expected_class: false
                        })
                        this.$notify({
                            group: 'apax-atr',
                            title: 'Lưu ý!',
                            type: 'primary',
                            duration: 3000,
                            text: 'Xin vui lòng điền thông tin chương trình học!'
                        })
                    }
                    this.toggleButtonsBar(1)
                }
                this.selectStartDate()
            },
            selectProgram(program_id) {
                this.resetCalculationFields()
                this.program = program_id
                this.data.program = this.html.dom.list.program[`program_id${program_id}`]
                this.cache.program = this.html.dom.list.program[`program_id${program_id}`]
                this.html.dom.list.tuition_fee = this.cache.program.tuition_fees
                this.html.dom.disabled.start_date = false
                this.html.dom.disabled.expected_class = false
                // this.data.customer_type = 1
                this.data.tuition_fee = ''
                this.tuition_fee = ''
                if (this.data.customer_type === 0 || this.data.customer_type === '0' || this.data.customer_type === '' || this.data.customer_type === 1) {
                    this.hideCalculationObjects()
                    this.html.dom.disabled.tuition_fee = true
                } else if (this.data.customer_type === 1 || this.data.customer_type === '1') {
                    if (this.receive) {
                        this.hideCalculationObjects()
                        this.html.dom.disabled.tuition_fee = true
                    } else {
                        this.showCalculationObjects()
                        this.html.dom.disabled.tuition_fee = false
                        this.$notify({
                            group: 'apax-atr',
                            title: 'Lưu ý!',
                            type: 'primary',
                            duration: 3000,
                            text: 'Xin vui lòng chọn 1 gói học phí!'
                        })
                    }
                }
            },
            selectTuitionFee(tuition_fee_id) {
                this.data.coupon_code=''
                this.data.voucher=0
                this.data.coupon_amount=0
                this.data.coupon_session=0
                this.data.tmp_bonus_sessions=0
                this.data.tmp_bonus_amount=0
                this.data.bonus_sessions=0
                this.data.bonus_amount=0
                this.data.end_date = ''
                this.tuition_fee = tuition_fee_id
                this.data.tuition_fee = this.html.dom.list.tuition_fee[`tuition_fee_id${tuition_fee_id}`]
                this.cache.tuition_fee = this.html.dom.list.tuition_fee[`tuition_fee_id${tuition_fee_id}`]
                this.getListDiscountCode(this.cache.tuition_fee.tuition_fee_id)
                const number_session = parseInt(this.cache.tuition_fee.tuition_fee_session, 10)
                const selected_tuition_discount = this.cache.tuition_fee.tuition_fee_discount
                this.data.tuition_fee_price_min = this.cache.tuition_fee.tuition_fee_price_min
                this.data.sessions = number_session.toString()
                u.set(this.temp, {
                    point: 0,
                    other: 0,
                    voucher: 0,
                    sibling: 0,
                    sessions: this.data.sessions,
                    discount: selected_tuition_discount,
                    must_charge: this.cache.tuition_fee.tuition_fee_receivable
                })
                this.calculateDiscount()
                const pay_type = parseInt(this.cache.tuition_fee.tuition_fee_type)
                this.html.dom.display.payload = 'display'
                this.html.dom.display.point = 'display'
                if (this.student.sibling) {
                    this.html.dom.display.sibling = 'display'
                    this.html.dom.disabled.sibling = false
                }
                this.data.bill_info = ''
                this.data.detail = ''
                if (this.student.type === 1 && 1==2) {
                    this.data.total_discount = this.data.must_charge_amount
                    const vip_discount = this.data.must_charge
                    this.data.must_charge = 0
                    this.data.other = vip_discount
                    this.html.dom.disabled.other = false
                    this.data.bill_info += `Chiết khấu (${u.pct(this.data.discount_percentage, 2)}%): ${this.format(selected_tuition_discount)}<br/>Học sinh VIP được miễn phí: ${vip_discount}<br/>------------------------------<br/>Giá Thực Thu: 0đ<br/><br/><br/>`
                    this.data.detail += `Chiết khấu (${u.pct(this.data.discount_percentage, 2)}%): ${this.format(selected_tuition_discount)}\nHọc sinh VIP được miễn phí: ${vip_discount}\n------------------------------\nGiá Thực Thu: 0đ\n\n\n`
                } else {
                    u.set(this.html.dom.disabled, {
                        point: false,
                        other: false,
                        voucher: false,
                        discount: false
                    })
                    this.data.bill_info += `Chiết khấu (${u.pct(this.data.discount_percentage, 2)}%): ${this.format(selected_tuition_discount)}<br/>------------------------------<br/>Giá Thực Thu: ${this.data.must_charge}<br/><br/><br/>`
                    this.data.detail += `Chiết khấu (${u.pct(this.data.discount_percentage, 2)}%): ${this.format(selected_tuition_discount)}\n------------------------------\nGiá Thực Thu: ${this.data.must_charge}\n\n\n`
                }
                if (this.data.latest_contract) {
                    this.html.dom.display.subinfo = 'display'
                    if (parseInt(this.program) === this.data.latest_contract.program_label) {
                        this.html.dom.display.continue = 'display'
                    } else {
                        this.html.dom.display.continue = 'hidden'
                    }
                }
                if (parseInt(this.student.sibling) > 0) {
                    this.html.dom.disabled.sibling = 'display'
                    this.html.dom.disabled.sibling = false
                }
                this.html.dom.disabled.detail = false
                this.html.dom.disabled.pay = true
                this.data.payload = this.cache.tuition_fee.tuition_fee_type
                u.set(this.html.dom.disabled, {
                    start_date: false,
                    end_date: false,
                    expected_class: false
                })
                this.html.dom.disabled.start_date = false
                this.selectStartDate()
                this.$notify({
                    group: 'apax-atr',
                    title: 'Lưu ý!',
                    type: 'caution',
                    duration: 3000,
                    text: 'Xin vui lòng chọn 1 ngày dự kiến học!'
                })
            },
            validate() {
                let mess = ''
                let resp = true
                if (!this.receive) {
                    if (this.data.customer_type === '') {
                        mess += ' - Chưa chọn loại khách hàng<br/>'
                        resp = false
                    }
                    if ((!this.data.product || parseInt(this.data.product.product_id, 10) === 0)) {
                        mess += ' - Chưa chọn gói sản phẩm<br/>'
                        resp = false
                    }
                    // this.data.program = this.program
                    // if ((!this.data.program || this.data.program === '') && parseInt(this.data.customer_type, 10) === 1) {
                    //     mess += ' - Chưa nhập thông tin chương trình học<br/>'
                    //     resp = false
                    // }
                    if ((!this.data.tuition_fee || parseInt(this.data.tuition_fee.tuition_fee_id, 10) === 0) && parseInt(this.data.customer_type, 10) === 1 && !this.receive) {
                        mess += ' - Chưa chọn gói học phí<br/>'
                        resp = false
                    }
                    if(!_.get(this, 'data.coupon.code') && parseInt(_.get(this, 'data.customer_type')) === 1){
                        mess += ' - Chưa chọn mã chiết khấu giảm giá<br/>'
                        resp = false
                    }
                }
                if (this.data.end_date === '' && !this.receive) {
                    mess += ' - Chưa chọn ngày dự kiến học<br/>'
                    resp = false
                }
                if (!this.data.note) {
                    mess += ' - Nội dung ghi chú là bắt buộc<br/>'
                    resp = false
                }
                if (!this.data.shift) {
                    mess += ' - Ca học là bắt buộc<br/>'
                    resp = false
                }
                if(this.data.tuition_fee_price_min && this.data.must_charge_amount < this.data.tuition_fee_price_min){
                    mess += ' - Số tiền phải đóng phải lớn hơn chính sách phí công ty ban hành<br/>'
                    resp = false
                }
                if (!resp) {
                    this.$notify({
                        group: 'apax-atc',
                        title: 'Chú Ý Dữ Liệu Nhập Học Chưa Đầy Đủ!',
                        type: 'danger',
                        duration: 3000,
                        text: `Xin vui lòng kiểm tra lại các danh mục dữ liệu sau đây:<br/>-----------------------------------------------<br/>${mess}`
                    })
                }
                return resp
            },
            saveForm() {
                if (this.validate()) {
                    const data = {
                        contract: {},
                        student: {},
                        apis: {}
                    }
                    // u.log('Product', this.data.product, 'Program', this.data.program)
                    const timestamp = this.moment().format('YYMMDDHHiiss')
                    const the_end_date = this.data.end_date
                    data.student = this.student
                    data.student.crm_id = this.student.crm_id
                    data.contract = Object.assign({}, this.data)
                    data.contract.point = this.amount(data.contract.point) < 1000 ? this.amount(data.contract.point) * 1000 : this.amount(data.contract.point)
                    data.contract.other = this.amount(data.contract.other) < 1000 ? this.amount(data.contract.other) * 1000 : this.amount(data.contract.other)
                    data.contract.sibling = this.amount(data.contract.sibling) < 1000 ? this.amount(data.contract.sibling) * 1000 : this.amount(data.contract.sibling)
                    data.contract.voucher = this.amount(data.contract.voucher) < 1000 ? this.amount(data.contract.voucher) * 1000 : this.amount(data.contract.voucher)
                    data.contract.product = this.product ? parseInt(this.product, 10) : 0
                    data.contract.program = this.data.program
                    data.contract.tuition_fee = this.data.tuition_fee && this.data.tuition_fee.tuition_fee_id ? parseInt(this.data.tuition_fee.tuition_fee_id) : 0
                    data.contract.customer_type = parseInt(this.data.customer_type, 10)
                    data.contract.previous_date = this.moment(this.data.previous_date).format('YYYY-MM-DD')
                    data.contract.receive = this.receive ? 1 : 0
                    data.contract.sessions = parseInt(this.data.customer_type, 10) === 0 ? 3 : data.contract.sessions
                    data.contract.coupon = _.get(this, 'data.coupon.code')
                    data.bonus_sessions = this.data.bonus_sessions
                    data.bonus_amount = this.data.bonus_amount
                    delete data.contract.style
                    delete data.contract.modal
                    delete data.contract.student
                    delete data.contract.latest_contract
                    delete data.contract.store_information
                    // u.log('saving contract data', data.contract)
                    this.storeContract(data)
                }
            },
            storeContract(data) {
                this.html.loading.action = true
                this.html.loading.class = true
                data.end_date = this.data.end_date
                u.p('/api/contracts/add', data)
                .then(response => {
                    const inserted_contract = response
                    this.completed = true
                    this.html.loading.action = false
                    this.html.loading.class = false
                    u.apax.$emit('apaxPopup', {
                        on: true,
                        content: `Biên bản nhập học của học viên <br/><b>${inserted_contract.name}</b><br/> đã được lưu thành công.`,
                        title: 'Tạo bản ghi nhập học mới thành công!',
                        class: 'modal-success',
                        size: 'md',
                        confirm: {
                            primary: {
                                button: 'OK',
                                action: () => { this.exitForm() },
                            }
                        },
                        variant: 'apax-ok'
                    })
                    this.resetForm()
                }).catch(e => console.log(e))
            },
            resetForm(all = true) {
                const selected_branch = this.cache.branch
                const public_holidays = this.html.config.holidays
                this.temp = {}
                u.set(this.data, {
                    vip: 0,
                    price: 0,
                    point: 0,
                    other: 0,
                    modal: '',
                    style: '',
                    detail: '',
                    sibling: 0,
                    receive: 0,
                    voucher: 0,
                    payload: '',
                    product: '',
                    program: '',
                    student: {},
                    continue: 0,
                    sessions: 0,
                    end_date: '',
                    pass_trial: 0,
                    receivable: 0,
                    discounted: 0,
                    bill_info: '',
                    must_charge: 0,
                    start_date: '',
                    tuition_fee: '',
                    other_amount: 0,
                    customer_type: '',
                    total_discount: 0,
                    previous_date: '',
                    expected_class: '',
                    latest_contract: {},
                    new_price_amount: 0,
                    discounted_amount: 0,
                    store_information: {},
                    must_charge_amount: 0,
                    discount_percentage: 0,
                    total_point_sibling: 0,
                    calculated_discount: 0,
                    total_voucher_other: 0
                })
                this.student = {}
                u.set(this.html.dom.list, {
                    product: [],
                    program: [],
                    tuition_fee: []
                })
                u.set(this.html.dom.display, {
                    point: 'hidden',
                    trial: 'hidden',
                    realy: 'hidden',
                    amount: 'hidden',
                    detail: 'hidden',
                    receive: 'hidden',
                    voucher: 'hidden',
                    sibling: 'hidden',
                    point: 'hidden',
                    payload: 'hidden',
                    payment: 'hidden',
                    subinfo: 'hidden',
                    sessions: 'hidden',
                    continue: 'hidden',
                    discount: 'hidden',
                    pass_trial: 'hidden',
                    tuition_fee: 'hidden'
                })
                u.set(this.html.dom.disabled, {
                    save: true,
                    other: true,
                    point: true,
                    trial: true,
                    realy: true,
                    reset: true,
                    detail: true,
                    cancel: false,
                    voucher: true,
                    sibling: true,
                    payload: true,
                    product: true,
                    program: true,
                    end_date: true,
                    start_date: true,
                    tuition_fee: true,
                    customer_type: true,
                    expected_class: true
                })
                this.product = ''
                this.program = ''
                this.tuition_fee = ''
                if (all) {
                    this.cache.branch = selected_branch
                    u.set(this.html.config, {
                        style: '',
                        modal: false,
                        holidays: [],
                        classdays: [2, 5],
                        clear_date: true,
                        cancel_icon: 'fa-sign-out',
                        cancel_title: 'Thoát form nhập học',
                        cancel_label: 'Thoát',
                        cancel_markup: 'error',
                        save_icon: 'fa-save',
                        save_title: 'Lưu bản ghi nhập học mới',
                        save_label: 'Lưu',
                        save_markup: 'success',
                        reset_icon: 'fa-recycle',
                        reset_title: 'Nhập lại nội dung thông tin',
                        reset_label: 'Hủy',
                        reset_markup: 'warning',
                        disable_days_of_week: [],
                        format_date: 'YYYY-MM-DD',
                        modal_title: 'Thông Báo',
                        modal_class: 'modal-success'
                    })
                    this.html.config.holidays = public_holidays
                    this.html.dom.filter.branch.disabled = true
                    this.html.dom.filter.branch.display = 'hidden'
                    this.html.dom.filter.search.display = 'hidden'
                    this.html.dom.disabled.note = true
                    this.html.dom.disabled.save = true
                    this.html.dom.disabled.reset = true
                    $('#sugget_student').val('')
                    this.start()
                    this.$router.push('/contracts')
                    this.$router.push('/contracts/add')
                }
            },
            toggleButtonsBar(flag = false) {
                if (flag) {
                    u.set(this.html.dom.disabled, {
                        note: true,
                        save: true,
                        reset: true
                    })
                } else {
                    u.set(this.html.dom.disabled, {
                        note: false,
                        save: false,
                        reset: false
                    })
                }
            },
            hideCalculationObjects() {
                u.set(this.html.dom.display, {
                    trial: 'hidden',
                    realy: 'hidden',
                    point: 'hidden',
                    amount: 'hidden',
                    detail: 'hidden',
                    sibling: 'hidden',
                    payload: 'hidden',
                    subinfo: 'hidden',
                    payment: 'hidden',
                    voucher: 'hidden',
                    discount: 'hidden',
                    sessions: 'hidden',
                    pass_trial: 'hidden',
                    tuition_fee: 'hidden'
                })
            },
            showCalculationObjects() {
                u.set(this.html.dom.display, {
                    trial: 'hidden',
                    realy: 'hidden',
                    point: 'hidden',
                    sibling: 'hidden',
                    detail: 'display',
                    payload: 'hidden',
                    amount: 'display',
                    subinfo: 'display',
                    payment: 'display',
                    voucher: 'display',
                    discount: 'display',
                    sessions: 'display',
                    pass_trial: 'display',
                    tuition_fee: 'display'
                })
            },
            resetCalculationFields() {
                u.set(this.data, {
                    price: 0,
                    point: 0,
                    other: 0,
                    sibling: 0,
                    receive: 0,
                    voucher: 0,
                    sessions: 0,
                    discount: 0,
                    receivable: 0,
                    discounted: 0,
                    must_charge: 0,
                    other_amount: 0,
                    total_discount: 0,
                    new_price_amount: 0,
                    discounted_amount: 0,
                    must_charge_amount: 0,
                    discount_percentage: 0,
                    total_point_sibling: 0,
                    calculated_discount: 0,
                    total_voucher_other: 0,
                    detail: '',
                    bill_info: '',
                    tuition_fee: '',
                    expected_class: '',
                })
                u.set(this.temp, {
                    tuition_price: 0,
                    new_fee_price: 0,
                    tuition_session: 0,
                    tuition_receivable: 0,
                    new_fee_receivable: 0,
                    tuition_price_origin: 0,
                    after_discounted_fee: 0,
                    total_discounted_value: 0,
                    final_discounted_value: 0,
                    origin_discounted_value: 0,
                    tuition_receivable_origin: 0
                })
                this.cache.amount = 0
                this.cache.tuition_fee = ''
            },
            resetMinForm() {
                this.hideCalculationObjects()
                this.resetCalculationFields()
                this.data.product = null
                this.data.program = null
                this.product = ''
                this.program = ''
            },
            exitForm() {
                this.$router.push('/contracts')
            },
            refInfo(info) {
              if (!info)
                return ''
              const ref = info.split("*")
              if (ref.length >1) {
                this.data.ref_code = ref[0]
                return `- Mã: ${ref[0]}\n- CTV: ${ref[1]}\n- Địa chỉ: ${ref[2]}\n- Người đại diện: ${ref[3]}`
              }
            },
            selectCoupon(data){
                if(data){
                    const t_bonus_sessions = this.data.bonus_sessions -  this.data.tmp_bonus_sessions
                    const t_bonus_amount = this.data.bonus_sessions -  this.data.tmp_bonus_sessions
                    this.data.bonus_sessions = t_bonus_sessions + data.bonus_sessions
                    this.data.bonus_amount = t_bonus_amount + data.bonus_amount
                    this.data.tmp_bonus_sessions = data.bonus_sessions
                    this.data.tmp_bonus_amount = data.bonus_amount
                }
            },
            checkCoupon(){
                if(this.data.coupon_code!=''){
                    this.html.loading.action = true;
                    const params = {
                        coupon_code:this.data.coupon_code,
                        branch_id: this.student.branch_id
                    }
                    u.p(`/api/partner_voucher/get_data_voucher`, params).then(response => {
                        this.html.loading.action = false;
                        if(response.status){
                            this.data.voucher = response.coupon_amount
                            this.data.tmp_bonus_sessions = this.data.tmp_bonus_sessions ? this.data.tmp_bonus_sessions:0
                            this.data.tmp_bonus_amount = this.data.tmp_bonus_amount ? this.data.tmp_bonus_amount:0
                            this.data.bonus_sessions = this.data.tmp_bonus_sessions+response.coupon_session
                            this.data.bonus_amount = this.data.tmp_bonus_amount+response.bonus_amount
                        }else{
                            alert(response.message);
                            this.data.voucher = 0
                            this.data.bonus_sessions = this.data.tmp_bonus_sessions
                            this.data.bonus_amount = this.data.tmp_bonus_amount
                        }
                        this.recalculateDiscount();
                    });
                }
            },
        }
    }

</script>

<style scoped lang="scss">
    .hidden {
        display: none;
    }
    .display {
        display: block;
    }
    textarea.description {
        height: 120px;
    }
    h6.text-main {
        padding: 0 0 10px 0;
        margin: 0 0 10px 0;
        border-bottom: 1px solid rgb(195, 202, 223);
    }
    .pass-trial {
        float: left;
        margin: 5px 5px 0 0;
    }
    .checkbox-inrow {
        padding: 35px 0 0 0;
    }
    .panel-footer {
        padding: 20px 0 10px 0;
    }
</style>
