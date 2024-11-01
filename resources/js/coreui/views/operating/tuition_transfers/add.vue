<template>
    <div class="animated fadeIn apax-form" @click="html.action.page">
        <div class="row">
            <div class="col-12 apax-show-detail">
                <b-card header>
                    <div slot="header">
                        <i class="fa fa-id-card"></i>
                        <b class="uppercase">Tạo Chuyển Phí</b>
                        <!--  <i class="fa fa-id-card"></i> <b class="uppercase">Tạo Bản Ghi Chuyển Phí</b> -->
                        <div class="card-actions">
                            <a href="skype:thanhcong1710?chat" target="_blank">
                                <small class="text-muted">
                                    <i class="fa fa-skype"></i>
                                </small>
                            </a>
                        </div>
                    </div>
                    <loader
                        :active="html.processing.form"
                        text="Đang xử lý..."
                        :duration="html.duration.form"
                    />
                    <div id="page-content tuition-transfer-form">
                        <div class="row">
                            <div class="col-lg-12">
                                <loader
                                    :active="html.processing.from"
                                    text="Đang tải dữ liệu..."
                                    :duration="html.duration.from"
                                />
                                <loader
                                    :active="html.processing.to"
                                    text="Đang tải dữ liệu..."
                                    :duration="html.duration.to"
                                />
                                <div class="panel">
                                    <div class="panel-body tuition-transfer-form">
                                        <div class="row">
                                            <div class="col-md-6" :class="html.config.style">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6
                                                            class="text-main first-upper"
                                                        >Học Sinh Chuyển Phí {{data.from.student_name}}</h6>
                                                    </address>
                                                </div>
                                                <div
                                                    class="col-12 pad-no"
                                                    :class="html.display.from_branch"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label"
                                                        >Chọn Trung Tâm Học Sinh Chuyển Phí</label>
                                                        <branch
                                                            label="name"
                                                            :filterable="true"
                                                            :options="data.from.branch_list"
                                                            v-model="data.branch_info"
                                                            @input="html.action.from_branch"
                                                            :disabled="html.disabled.from_branch"
                                                            placeholder="Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước"
                                                        ></branch>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-12 pad-no"
                                                    :class="html.display.from_search"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label"
                                                        >Chọn Học Sinh Chuyển Phí</label>
                                                        <search
                                                            :displayIcon="html.loading.from"
                                                            :endpoint="html.action.from_link"
                                                            :disabled="html.disabled.from_search"
                                                            :suggestStudents="html.action.from_search"
                                                            :onSelectStudent="html.action.from_load"
                                                            placeholder="Tìm kiếm học sinh theo mã CRM/LMS hoặc Tên"
                                                        ></search>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.from_detail"
                                                >
                                                    <div class="form-group">
                                                        <label class="control-label">Thông Tin Hồ Sơ Học Sinh</label>
                                                        <div class="profile-detail">
                                                            <div class="from-student information item col-md-12">
                                                                <div class="student info detail row">
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4"> Họ tên:</div>
                                                                            <div class="col-md-8">{{data.from.student.name}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Mã CMS:</div>
                                                                            <div class="col-md-8">{{data.from.student.crm_id}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Mã Cyber:</div>
                                                                            <div class="col-md-8">{{data.from.student.accounting_id}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Phụ huynh:</div>
                                                                            <div class="col-md-8">{{data.from.student.gud_name1}} 
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Điện thoại:</div>
                                                                            <div class="col-md-8">{{data.from.student.gud_mobile1}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Email:</div>
                                                                            <div class="col-md-8">{{data.from.student.gud_email1}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Địa chỉ:</div>
                                                                            <div class="col-md-8">{{data.from.student.address}}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.from_contracts"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-12 pad-no">
                                                            <div class="form-group">
                                                                <label
                                                                    class="control-label"
                                                                >Các gói phí được chuyển</label>
                                                                <div
                                                                    v-for="(data_item, idx) of data.from.contracts"
                                                                    :key="idx"
                                                                >
                                                                   <div :class="`transfering contract item col-md-12 `+data_item.class">                                        
                                                                        <div class="contract detail row">
                                                                            <div class="info line col-md-12">
                                                                                <h5>{{idx+1}} . Hợp đồng số ({{data_item.accounting_id }}) - {{data_item.tuition_fee_name}}</h5>
                                                                            </div>
                                                                            <div class="info line col-md-12">
                                                                                <div class="row detail-info">
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Thời gian: 
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                Từ {{data_item.start_date}} tới {{data_item.end_date}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Đã đóng:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{f(data_item.total_charged)}} ({{data_item.real_sessions}} buổi)
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Đã học:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{f(data_item.done_amount)}} ({{data_item.done_sessions}} buổi)
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Học bổng:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{data_item.bonus_sessions}} buổi
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Đã học học bổng:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{data_item.done_bonus_sessions}} buổi
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Còn lại:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{f(data_item.left_amount)}} ({{data_item.left_sessions}} buổi)
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Sản phẩm:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{data_item.product_name}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12" v-if="data_item.class_name">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">Lớp học</div>
                                                                                            <div class="info line col-md-8">{{data_item.class_name}}</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div style="width:100%; margin-top: 10px" v-if="data_item.from_sessions !== undefined" >
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        class="control-label"
                                                                                    >Số Tiền Chuyển</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        :value="f(data_item.from_amount)"
                                                                                        type="text"
                                                                                        disabled="true"
                                                                                    >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        class="control-label"
                                                                                    >Số Buổi Chuyển</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        :value="data_item.from_sessions"
                                                                                        type="text"
                                                                                        disabled="true"
                                                                                    >
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
                                            <!--End - hiển thị các gói phí -->
                                            <div class="col-md-6" :class="html.config.style">
                                                <div class="col-md-12">
                                                    <address>
                                                        <h6
                                                            class="text-main first-upper"
                                                        >Học Sinh Nhận Phí</h6>
                                                    </address>
                                                </div>
                                                <div
                                                    class="col-12 pad-no"
                                                    :class="html.display.to_branch"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label"
                                                        >Chọn Trung Tâm Học Sinh Nhận Phí</label>
                                                        <branch
                                                            label="name"
                                                            :filterable="true"
                                                            :options="data.to.branch_list"
                                                            @input="html.action.to_branch"
                                                            :disabled="html.disabled.to_branch"
                                                            :placeholder="html.config.to_placeholder"
                                                            v-model="data.to_branch_info"
                                                        />
                                                    </div>
                                                </div>
                                                <!-- Học sinh nhận phí -->
                                                <div
                                                    class="col-12 pad-no"
                                                    :class="html.display.to_search"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label"
                                                        >Chọn Học Sinh Nhận Phí</label>
                                                        <search
                                                            :displayIcon="html.loading.to"
                                                            :endpoint="html.action.to_link"
                                                            :disabled="html.disabled.to_search"
                                                            :suggestStudents="html.action.to_search"
                                                            :onSelectStudent="html.action.to_load"
                                                            placeholder="Tìm kiếm học sinh theo mã CRM/LMS hoặc Tên"
                                                        ></search>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.to_detail"
                                                >
                                                    <div class="form-group">
                                                        <label class="control-label">Thông Tin Hồ Sơ Học Sinh</label>
                                                        <div class="profile-detail">
                                                            <div class="from-student information item col-md-12">
                                                                <div class="student info detail row">
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4"> Họ tên:</div>
                                                                            <div class="col-md-8">{{data.to.student.name}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Mã CMS:</div>
                                                                            <div class="col-md-8">{{data.to.student.crm_id}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Mã Cyber:</div>
                                                                            <div class="col-md-8">{{data.to.student.accounting_id}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Phụ huynh:</div>
                                                                            <div class="col-md-8">{{data.to.student.gud_name1}} 
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Điện thoại:</div>
                                                                            <div class="col-md-8">{{data.to.student.gud_mobile1}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Email:</div>
                                                                            <div class="col-md-8">{{data.to.student.gud_email1}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Địa chỉ:</div>
                                                                            <div class="col-md-8">{{data.to.student.address}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info line col-md-12" v-if="data.to.sibling==1">
                                                                        <div class="row">
                                                                            <div class="col-md-4">Quan hệ</div>
                                                                            <div class="col-md-8" style="color:red;font-weight:bold">Anh chị em</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12 pad-no"
                                                    :class="html.display.to_contract_info"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-12 pad-no">
                                                            <div class="form-group">
                                                                <label class="control-label" >Gói phí hiện tại</label>
                                                                <div class="transfering contract item col-md-12 ">                                        
                                                                    <div class="contract detail row">
                                                                        <div class="info line col-md-12">
                                                                            <h5>Hợp đồng số ({{data.to.contract.accounting_id }}) - {{data.to.contract.tuition_fee_name}}</h5>
                                                                        </div>
                                                                        <div class="info line col-md-12">
                                                                            <div class="row detail-info">
                                                                                <div class="info line col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="info line col-md-4">
                                                                                            Thời gian: 
                                                                                        </div>
                                                                                        <div class="info line col-md-8">
                                                                                            Từ {{data.to.contract.start_date}} tới {{data.to.contract.end_date}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                 <div class="info line col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="info line col-md-4">
                                                                                            Phải đóng:
                                                                                        </div>
                                                                                        <div class="info line col-md-8">
                                                                                            {{f(data.to.contract.must_charge)}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="info line col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="info line col-md-4">
                                                                                            Đã đóng:
                                                                                        </div>
                                                                                        <div class="info line col-md-8">
                                                                                            {{f(data.to.contract.total_charged)}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                 <div class="info line col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="info line col-md-4">
                                                                                            Còn phải đóng:
                                                                                        </div>
                                                                                        <div class="info line col-md-8">
                                                                                            {{f(data.to.contract.debt_amount)}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="info line col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="info line col-md-4">
                                                                                            Sản phẩm:
                                                                                        </div>
                                                                                        <div class="info line col-md-8">
                                                                                            {{data.to.contract.product_name}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="info line col-md-12" v-if="data.to.contract.class_name">
                                                                                    <div class="row">
                                                                                        <div class="info line col-md-4">Lớp học</div>
                                                                                        <div class="info line col-md-8">{{data.to.contract.class_name}}</div>
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
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.to_contracts"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-12 pad-no">
                                                            <div class="form-group">
                                                                <label
                                                                    class="control-label"
                                                                >Các gói phí được nhận</label>
                                                                <div
                                                                    v-for="(data_item, idx) of data.to.contracts"
                                                                    :key="idx"
                                                                >
                                                                   <div :class="`transfering contract item col-md-12 `+data_item.class">                                        
                                                                        <div class="contract detail row">
                                                                            <div class="info line col-md-12">
                                                                                <h5>{{idx+1}} . Hợp đồng số ({{data_item.accounting_id }}) - {{data_item.tuition_fee_name}}</h5>
                                                                            </div>
                                                                            <div class="info line col-md-12">
                                                                                <div class="row detail-info">
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Thời gian: 
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                Từ {{data_item.start_date}} tới {{data_item.end_date}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Đã đóng:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{f(data_item.total_charged)}} ({{data_item.real_sessions}} buổi)
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Đã học:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{f(data_item.done_amount)}} ({{data_item.done_sessions}} buổi)
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Học bổng:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{data_item.bonus_sessions}} buổi
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12"  v-if="data.to.sibling==1">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Đã học học bổng:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{data_item.done_bonus_sessions}} buổi
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Còn lại:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{f(data_item.left_amount)}} ({{data_item.left_sessions}} buổi)
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">
                                                                                                Sản phẩm:
                                                                                            </div>
                                                                                            <div class="info line col-md-8">
                                                                                                {{data_item.product_name}}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="info line col-md-12" v-if="data_item.class_name">
                                                                                        <div class="row">
                                                                                            <div class="info line col-md-4">Lớp học</div>
                                                                                            <div class="info line col-md-8">{{data_item.class_name}}</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div style="width:100%; margin-top: 10px" v-if="data_item.to_sessions !== undefined" >
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        class="control-label"
                                                                                    >Số Tiền Nhận</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        :value="f(data_item.to_amount)"
                                                                                        type="text"
                                                                                        disabled="true"
                                                                                    >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        class="control-label"
                                                                                    >Số Buổi Nhận</label>
                                                                                    <input
                                                                                        class="form-control"
                                                                                        :value="data_item.to_sessions"
                                                                                        type="text"
                                                                                        disabled="true"
                                                                                    >
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
                                        <div class="row">
                                            <div class="col-md-12" :class="html.display.to_transfer_info">
                                                <address>
                                                    <h6
                                                        class="text-main first-upper" style="padding:0px 15px;"
                                                    >Tổng số</h6>
                                                </address>
                                            </div>
                                            <div class="col-md-6" :class="html.config.style">
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.to_transfer_info"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    class="control-label"
                                                                >Tổng Số Tiền Còn Lại</label>
                                                                <input
                                                                    class="form-control"
                                                                    :value="data.from.transfer_amount | formatMoney"
                                                                    type="text"
                                                                    :disabled="html.disabled.transfer_amount"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" :class="html.config.style">
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.to_transfer_info"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    class="control-label"
                                                                >Tổng Số Tiền Nhận</label>
                                                                <input
                                                                    class="form-control"
                                                                    :value="data.to.total_amount | formatMoney"
                                                                    type="text"
                                                                    :disabled="html.disabled.receiver_amount"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.to_new_contract"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            class="filter-label control-label"
                                                        >
                                                            Gói Sản Phẩm Quy Đổi
                                                            <strong class="text-danger h6">*</strong>
                                                        </label>
                                                        <select
                                                            id="products-list"
                                                            class="selection product form-control"
                                                            v-model="data.contract.product_id"
                                                            @change="html.action.to_product"
                                                        >
                                                            <option
                                                                value
                                                                disabled
                                                            >Chọn gói sản phẩm</option>
                                                            <option
                                                                :value="product.id"
                                                                v-for="(product, idx) in data.to.list_product"
                                                                :key="idx"
                                                            >{{ product.name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12"
                                                    :class="html.display.to_setting"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-6" v-if="!data.to.sibling">
                                                            <div class="form-group">
                                                                <label
                                                                    class="control-label"
                                                                >Truy thu chi phí phát sinh</label>
                                                                <input
                                                                    class="form-control"
                                                                    v-model="data.amount_truythu"
                                                                    type="text"  
                                                                    @change="calculateDiscount"                                                              >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group select-date">
                                                                <label class="control-label">
                                                                    Ngày Chuyển Phí
                                                                    <strong class="text-danger h6">*</strong>
                                                                </label>
                                                                <datepicker
                                                                    id="transfer-date"
                                                                    class="form-control calendar"
                                                                    :value="data.from.transfer_date"
                                                                    v-model="data.from.transfer_date"
                                                                    placeholder="Chọn ngày chuyển phí"
                                                                    :not-before="data.to.none_before_transfer_date"
                                                                    @change="html.action.to_transfer_date"
                                                                    lang="lang"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    class="control-label"
                                                                >Lý Do Chuyển Phí <strong class="text-danger h6">*</strong></label>
                                                               <!-- <select class="form-control" v-model="data.reason_id">
                                                                    <option value="">Chọn lý do chuyển phí</option>
                                                                    <option v-for="(reason, idx) in data.reasons"
                                                                        :value="reason.id"
                                                                        :key="idx">
                                                                        {{ reason.description }}
                                                                    </option>
                                                                </select> -->
                                                                <textarea class="form-control" v-model="data.from.transfer_note"  style="height:60px;" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-12 pad-no"
                                                    :class="html.display.buttons"
                                                >
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <apaxbtn
                                                                :onClick="html.action.save"
                                                                :disabled="html.disabled.save"
                                                                icon="fa-save"
                                                                label="Lưu"
                                                                title="Lưu bản ghi chuyển phí mới"
                                                                markup="success"
                                                            ></apaxbtn>
                                                            <apaxbtn
                                                                :onClick="html.action.cancel"
                                                                :disabled="html.disabled.cancel"
                                                                icon="fa-sign-out"
                                                                label="Thoát"
                                                                title="Thoát form tạo chuyển phí"
                                                                markup="error"
                                                            ></apaxbtn>
                                                        </div>
                                                        <div
                                                            class="col-md-3"
                                                            :class="html.display.to_setting"
                                                        >
                                                            <div class="form-group">
                                                                <file
                                                                    :label="'File đính kèm'"
                                                                    name="'upload_transfer_file'"
                                                                    :field="'attached_file'"
                                                                    :type="'transfer_file'"
                                                                    :full="false"
                                                                    :onChange="html.action.upload_file"
                                                                    :title="'Tải lên 1 file đính kèm với định dạng tài liệu: jpg, png, pdf, doc, docx.'"
                                                                    :customClass="'no-current-file'"
                                                                ></file>
                                                                <div class="remove-file">
                                                                    <span
                                                                        class="remove-file-button"
                                                                        @click="html.action.remove_file"
                                                                        v-show="html.action.remove_attached_file"
                                                                    >
                                                                        <i
                                                                            class="fa fa-times-circle"
                                                                        ></i> Hủy đính kèm file
                                                                    </span>
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
                    </div>
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import datepicker from "vue2-datepicker"
import search from "../../../components/StudentSearch"
import loader from "../../../components/Loading"
import apaxbtn from "../../../components/Button"
import branch from "vue-select"
import file from "../../../components/File"
import u from "../../../utilities/utility"

export default {
    name: "Add-Tuition-Transfer",
    components: {
        datepicker,
        apaxbtn,
        loader,
        branch,
        search,
        file
    },
    data() {
        const model = u.m("contracts").page
        model.html = {
            action: {
                from_recal: () => this.recalculate(),
                from_branch: branch => this.selectFromBranch(branch),
                from_link: 0,
                from_search: keyword => this.searchSuggestFromStudent(keyword),
                from_load: student => this.selectFromStudent(student),
                upload_file: (file, param) => this.uploadFile(file, param),
                remove_file: "",
                remove_attached_file: false,
                to_transfer_date: date => this.selectTransferDate(date),
                to_branch: branch => this.selectToBranch(branch),
                to_link: 0,
                to_search: keyword => this.searchSuggestToStudent(keyword),
                to_load: student => this.selectToStudent(student),
                to_product: () => this.selectProduct(),
                to_start_date: date => this.selectStartDate(date),
                page: () => this.handle(),
                save: () => this.saveForm(),
                reset: () => this.resetForm(),
                cancel: () => this.cancelForm(),
                update: () => this.updateInformation()
            },
            lable: {
                update: "Sửa"
            },
            loading: {
                from: "hidden",
                to: "hidden"
            },
            display: {
                from_contracts: "hidden",
                from_contracts2: "hidden",
                from_addition: "hidden",
                from_summary: "hidden",
                from_branch: "hidden",
                from_search: "hidden",
                from_detail: "hidden",
                to_transfer_info: "hidden",
                to_new_contract: "hidden",
                to_contract: "hidden",
                to_contract_info: "hidden",
                to_setting: "hidden",
                to_summary: "hidden",
                to_branch: "hidden",
                to_search: "hidden",
                to_detail: "hidden",
                to_date: "hidden",
                buttons: "hidden",
                to_contracts: "hidden",
            },
            disabled: {
                transfer_amount: true,
                transfer_session: true,
                from_branch: true,
                from_reason: true,
                from_search: true,
                from_transfer_date: true,
                to_search: true,
                to_branch: true,
                to_product: true,
                to_start_date: true,
                select_product: true,
                receiver_amount: true,
                receiver_session: true,
                to_expected_class: true,
                save: true,
                edit: true,
                reset: true,
                cancel: false
            },
            processing: {
                form: false,
                from: false,
                to: false
            },
            duration: {
                form: 800,
                from: 600,
                to: 500
            },
            config: {
                calling: false,
                style: "",
                page: "",
                to_placeholder: "Vui lòng chọn học sinh chuyển phí trước"
            },
            branch_info: ""
        }
        model.data = {
            from: {
                branch: 0,
                amount: 0,
                session: 0,
                student: {},
                student_id: 0,
                contracts: [],
                contracts_today: [],
                branch_list: [],
                student_name: "",
                student_detail: "",
                transfer_date: "",
                none_after: "",
                renewed: null,
                transfer_note: "",
                addition_amount: 0,
                transfer_amount: 0,
                amount_truythu:0,
                addition_session: 0,
                transfer_sessions: 0,
                transfer_contracts: [],
                calculated_contracts: [],
                reserved_all: 0,
                total_available_amount:0,
            },
            to: {
                branch: 0,
                amount: 0,
                session: 0,
                student: {},
                student_id: 0,
                contracts: [],
                branch_list: [],
                student_detail: "",
                transfer_product: null,
                contract: {},
                products: [],
                program_label: "",
                start_date: "",
                none_before: "",
                none_before_transfer_date: "",
                end_date: "",
                none_after: "",
                transfer_amount: 0,
                transfer_sessions: 0,
                total_amount: 0,
                total_session: 0,
                receive_contract: [],
                receive_contract_info: [],
                expected_class: "",
                attached_file: null,
                calculated_contracts: [],
                total_available_sessions: 0,
                sibling:0,
            },
            temp: {
                update: false,
                transfer_amount: 0,
                transfer_session: 0,
                total_sessions: 0,
                the_last_date: ""
            },
            contract: {
                date: "",
                branch_id: 0,
                product: null,
                product_id: 0,
                product_name: "",
                expected_class: ""
            },
            branch_info: "",
            to_branch_info: "",
            check_hoc_thu: "",
            clone_contract_tranfer: [],
            disabled_extend_input: false,
            reasons:[],
            reason_id:'',
        }
        model.disabled_extend_input = false
        return model
    },
    created() {
        u.i(this)
        this.start()
         u.g(`/api/tuition-transfers/get_all_reasons?type=2`).then(response => {
            this.data.reasons = response
        })
    },
    methods: {
        start() {
            //chọn branch mặc định theo OM
            if (u.session().user.role_id != 999999999) {
                this.data.branch_info = this.session.user.branches.filter(
                    item => item.id == this.session.user.branch_id
                )[0]
            }
            //this.data.branch_info = this.session.user.branches.filter(item => item.id == this.session.user.branch_id)[0]
            this.data.to.none_before = this.moment().format("YYYY-MM-DD")
            this.data.to.none_before_transfer_date = this.moment().format(
                "YYYY-MM-DD"
            )
            this.data.from.branch_list = this.session.user.branches
            this.data.to.branch_list = this.session.info.branches
            this.html.display.from_branch = "display"
            this.html.display.to_branch = "display"
            this.html.disabled.from_branch = false
        },
        handle() {},
        updateInformation() {
            if (this.data.temp.update === false) {
                const confirmation = confirm(
                    "Bạn sẽ phải chịu trách nhiệm về những thông tin chỉnh sửa này, bạn có chắc là muốn tiếp tục không?"
                )
                if (confirmation) {
                    this.html.disabled.transfer_amount = false
                    this.html.disabled.transfer_session = false
                    this.html.lable.update = "Hủy"
                    this.data.temp.update = true
                }
            } else {
                this.data.from.transfer_sessions = this.data.temp.transfer_sessions
                this.data.from.transfer_amount = this.data.temp.transfer_amount
                this.data.temp.update = false
                this.html.disabled.transfer_amount = true
                this.html.disabled.transfer_session = true
                this.html.lable.update = "Sửa"
            }
        },
        selectFromBranch(branch) {
            this.data.from.branch = branch.id
            this.html.display.from_search = "display"
        },
        searchSuggestFromStudent(keyword) {
            if (
                keyword &&
                keyword.length > 3 &&
                this.html.config.calling === false &&
                this.data.from.branch != undefined
            ) {
                keyword = keyword.trim()
                keyword = keyword.replace(
                    /[~`!#$%^&*[,\]./<>?'\\:"|\t]/gi,
                    ""
                )
                this.html.loading.from = "display"
                this.html.config.calling = true
                return new Promise((resolve, reject) => {
                    u.g(
                        `/api/tuition-transfers/suggest-sender/${keyword}/${
                            this.data.from.branch
                        }`
                    )
                        .then(response => {
                            const resp = response.length
                                ? response
                                : [
                                      {
                                          label:
                                              "Không tìm thấy kết quả nào phù hợp",
                                          student_name:
                                              "Không có kết quả nào phù hợp"
                                      }
                                  ]
                            this.html.config.calling = false

                            this.html.loading.from = "hidden"

                            resolve(resp)
                        })
                        .catch(e => {
                            //u.lg(e)
                            this.html.loading.from = "hidden"
                        })
                })
            }
        },
        selectFromStudent(student) {
            if (this.html.config.calling === false) {
                this.html.config.calling = true
                this.data.from.student_id = student.student_id
                this.html.processing.from = true
                this.html.display.to_detail = "hidden"
                this.html.display.to_setting = "hidden"
                this.html.display.to_contract = "hidden"
                this.html.display.to_contract_info = "hidden"
                this.html.display.to_search = "hidden"
                this.html.display.to_new_contract = "hidden"
                this.html.disabled.save = true
                this.html.disabled.to_branch = true
                this.html.display.to_transfer_info = "hidden"
                this.data.to_branch_info = ""
                const session = u.session()
                const user_info = session.user
                u.g(`/api/tuition-transfers/contracts/sender/${student.student_id}`).then(response => {
                    this.html.config.calling = false
                    let alert = false                    
                    if (response.has_error == 0) {
                        this.data.from.total_available_sessions = response.total_available_sessions
                        this.data.from.total_available_amount = response.total_available_amount
                        this.data.from.renewed = response.renew_information
                        this.data.from.student = response.student
                        this.data.from.contracts = response.contracts
                        this.data.clone_contract_tranfer = response.contracts
                        this.data.from.reserved_all = response.reserved_all
                        this.data.from.transfer_contracts = []
                        this.data.from.contracts_today = []
                        if (this.data.clone_contract_tranfer.length) {
                            this.html.display.from_detail = "display"
                            this.html.display.from_contracts = "display"
                            this.html.display.from_contracts2 = "hidden"
                            this.html.disabled.to_branch = false
                            this.html.to_placeholder =
                                "Vui lòng chọn trung tâm để giới hạn phạm vi tìm kiếm trước."
                        } else {
                            alert = u.genErrorHtml(
                                `Học sinh <b>${
                                    response.student.name
                                }</b> hiện không có gói phí khả dụng nào có thể dùng để chuyển phí.`
                            )
                        }
                    } else {
                        this.data.from.transfer_contracts = []
                        this.html.disabled.to_branch = true
                        this.html.config.calling = false // trước đó bằng true nhưng nếu HS chưa đủ phí thì không action_form_search
                        this.data.to.student_detail = ""
                        this.data.from.student_detail = ""
                        this.html.display.to_detail = "hidden"
                        this.html.display.to_setting = "hidden"
                        this.html.display.to_contract = "hidden"
                        this.html.display.to_contract_info = "hidden"
                        this.html.display.from_detail = "hidden"
                        this.html.display.from_summary = "hidden"
                        this.html.display.from_contracts = "hidden"
                        this.html.display.from_contracts2 = "hidden"
                        this.html.display.to_new_contract = "hidden"
                        alert = u.genErrorHtml(response.message)
                    }
                    if (alert) {
                        this.$notify({
                            group: "apax-atc",
                            title: "Cảnh Báo!",
                            type: "caution dark",
                            duration: 5000,
                            text: alert
                        })
                    }
                    this.html.display.buttons = "display"
                    this.html.processing.from = false
                    this.html.disabled.from_branch = true
                    this.html.disabled.from_search = true
                })
            }
        },
        selectToBranch(branch) {
            this.data.to.branch = branch.id
            if (branch && branch != "") {
                this.html.display.to_search = "display"
            }
            this.data.contract.branch_id = branch.id
        },
        searchSuggestToStudent(keyword) {
            if (
                keyword &&
                keyword.length > 3 &&
                this.html.config.calling === false &&
                this.data.to.branch != undefined
            ) {
                keyword = keyword.trim()
                keyword = keyword.replace(
                    /[~`!#$%^&*[,\]./<>?'\\:"|\t]/gi,
                    ""
                )
                this.html.config.calling = true
                this.html.loading.to = "display"
                return new Promise((resolve, reject) => {
                    u.g(
                        `/api/tuition-transfers/suggest-receiver/${keyword}/${
                            this.data.to.branch
                        }/${this.data.from.student_id}`
                    )
                        .then(response => {
                            const resp = response.length
                                ? response
                                : [
                                      {
                                          label:
                                              "Không tìm thấy kết quả nào phù hợp",
                                          student_name:
                                              "Không có kết quả nào phù hợp"
                                      }
                                  ]
                            this.html.config.calling = false
                            this.html.loading.to = "hidden"
                            resolve(resp)
                        })
                        .catch(e => {
                            u.lg(e)
                            this.html.loading.to = "hidden"
                        })
                })
            }
        },
        selectToStudent(student) {
            if (this.html.config.calling === false) {
                this.html.config.calling = true
                let url = `/api/tuition-transfers/contracts/receiver/${student.student_id}/${this.data.from.student.id}`
                u.g(url).then(response => {
                    this.html.config.calling = false
                    this.html.display.to_transfer_info = "hidden"
                    if (response.warning !== "") {
                        this.data.to.contract = null
                        this.data.to.student_detail = ""
                        this.data.to.receive_contract = []
                        this.html.display.to_detail = "hidden"
                        this.html.display.to_setting = "hidden"
                        this.html.display.to_contract = "hidden"
                        this.html.display.to_contract_info = "hidden"
                        this.html.display.to_new_contract = "hidden"
                        this.html.disabled.from_transfer_date = true
                        this.data.to.list_product = this.session.info.products
                        u.apax.$emit("apaxPopup", {
                            on: true,
                            content: u.genErrorHtml(response.warning),
                            title: "Cảnh Báo",
                            class: "modal-danger",
                            size: "md",
                            confirm: {
                                primary: {
                                    button: "OK",
                                    action() {
                                        u.lg("")
                                    }
                                }
                            },
                            variant: "danger"
                        })
                    } else {
                        this.data.to.student = response.student
                        this.html.display.to_detail = "display"
                        this.html.display.to_setting = "display"
                        this.html.display.from_summary = "display"
                        this.html.disabled.from_reason = false
                        this.data.to.contract = response.contract
                        this.data.to.receive_contract = []
                        this.html.display.to_contract = "hidden"
                        this.data.to.list_product = this.session.info.products
                        this.data.to.sibling = response.sibling
                        this.html.display.to_contract_info = this.data.to.sibling ? "hidden":"display";
                        this.html.display.to_new_contract = this.data.to.sibling ? "display":"hidden"; 
                        this.html.disabled.from_transfer_date = this.data.to.sibling ? true : false;
                        this.data.contract.product_id = response.product;
                        if(!this.data.to.sibling){
                            const new_data_from_contracts = []
                            this.data.from.contracts.map(item => {
                                if (parseInt(item.total_charged, 10) > 0) {
                                    new_data_from_contracts.push(item)
                                }
                                return item
                            })
                            this.data.from.contracts = new_data_from_contracts
                        }
                    }
                    this.html.processing.to = false
                    this.html.disabled.to_branch = true
                    this.html.disabled.to_search = true
                    this.disabled_extend_input = false
                })
            }
        },
        prepareTransfermation() {            
            if (this.data.from.contracts.length == 0) {
                this.html.disabled.save = true
                this.$notify({
                    group: "apax-atc",
                    title: "Cảnh Báo!",
                    type: "caution dark",
                    duration: 5000,
                    text: u.genErrorHtml(
                        "Không thể thực hiện chuyển phí vì không tồn tại số buổi học hoặc không có gói hợp lệ"
                    )
                })
                return false
            }  
            if(this.data.from.total_available_amount < u.fmc(this.data.amount_truythu).n){
                this.html.disabled.save = true
                this.$notify({
                    group: "apax-atc",
                    title: "Cảnh Báo!",
                    type: "caution dark",
                    duration: 5000000,
                    text: u.genErrorHtml(
                        "Số tiền truy thu phải nhỏ hơn số tiền còn lại!"
                    )
                })
                return false
            }
            const receive_contract = this.data.to.contract ? this.data.to.contract : this.data.contract
            const transferPrepareData = {
                transfering_data: {
                    sender: this.data.from.student,
                    receiver: this.data.to.student,
                    receiver_contract: receive_contract,
                    transfer_contracts: this.data.from.contracts,
                    transfer_date: this.data.from.transfer_date,
                    sibling_product: this.data.contract.product_id,
                    sibling: this.data.to.sibling,
                    amount_truythu: u.fmc(this.data.amount_truythu).n,
                }
            }            
            this.html.processing.to = true
            this.html.processing.from = true
            u.p(
                "/api/tuition-transfers/prepare-transfer-data",
                transferPrepareData
            ).then(response => {
                this.html.processing.to = false
                this.html.processing.from = false
                if (response.alert) {
                    this.html.disabled.save = true
                    this.$notify({
                        group: "apax-atc",
                        title: "Cảnh Báo!",
                        type: "caution dark",
                        duration: 500000,
                        text: u.genErrorHtml(response.alert)
                    })                    
                    return false
                } else {
                    this.data.from.total_sessions = 0
                    this.data.from.transfer_amount = 0
                    this.data.to.transfer_amount = 0
                    this.data.to.transfer_sessions = 0
                    this.html.disabled.from_search = true
                    this.html.disabled.to_search = true
                    this.html.disabled.select_product = true
                    this.html.disabled.to_start_date = true
                    this.html.disabled.from_transfer_date = false
                    this.html.disabled.save = false
                    this.html.disabled.reset = false
                    this.html.disabled.cancel = false
                    const reserved_all = this.data.from.reserved_all
                    this.data.to.total_amount = response.total_transfered_amount
                    this.data.to.total_session = response.total_transfered_sessions
                    this.data.from.transfer_amount = response.total_transfer_amount
                    this.data.from.transfer_sessions = response.total_transfer_session
                    console.log( this.data.to.total_session)
                    if (response.transfered_contracts.length) {
                        this.data.to.contracts = response.received_contracts
                        this.data.from.contracts = response.transfered_contracts
                    }
                    this.html.display.to_transfer_info = "display"
                    this.html.display.to_contracts = this.data.to.sibling ? "display":"hidden"
                    this.html.display.to_contract_info = this.data.to.sibling ? "hidden":"display"
                    this.data.to.transfer_amount = response.total_transfered_amount
                    this.data.to.transfer_sessions = response.total_transfered_sessions
                }
            })
        },
        selectTransferDate(date) {
            if(date!=null){
                this.data.from.transfer_date = moment(date).format("YYYY-MM-DD")
                this.data.contract.date = moment(date).format("YYYY-MM-DD")
                this.prepareTransfermation()
                this.html.disabled.edit = false
                this.disabled_extend_input = true
            }else{
                this.html.disabled.save = true
                this.disabled_extend_input = false
            }
        },
        selectProduct() {
            if (parseInt(this.data.contract.product_id, 10)) {
                const selected_product = this.data.to.list_product.filter(
                    item => {
                        return (
                            parseInt(item.id, 10) ===
                            parseInt(this.data.contract.product_id, 10)
                        )
                    }
                )
                this.data.contract.product = selected_product.length
                    ? selected_product[0]
                    : null
                this.data.contract.product_name = this.data.contract.product.name
                if (
                    this.check_hoc_thu != undefined &&
                    this.check_hoc_thu.length > 0
                ) {
                    if (this.check_hoc_thu.enrolment_status == 0) {
                        this.data.to.none_before = this.moment(
                            this.check_hoc_thu.end_date
                        )
                            .add(1, "days")
                            .format("YYYY-MM-DD")
                    }
                    if (
                        this.check_hoc_thu.enrolment_status == 1 &&
                        this.check_hoc_thu.status == 6
                    ) {
                        this.data.to.none_before = this.moment(
                            this.check_hoc_thu.enrolment_last_date
                        )
                            .add(1, "days")
                            .format("YYYY-MM-DD")
                    }
                }
                this.data.contract.date = moment().format("YYYY-MM-DD")
                this.html.disabled.from_transfer_date = false
                this.$notify({
                    group: "apax-atc",
                    title: "Lưu ý!",
                    type: "primary",
                    duration: 3000,
                    text: "Xin vui lòng chọn ngày chuyển phí!"
                })
                // this.html.disabled.to_start_date = false
            }
        },
        selectStartDate(date) {
            this.data.contract.date = moment(date).format("YYYY-MM-DD")
            this.html.disabled.from_transfer_date = false
            this.$notify({
                group: "apax-atc",
                title: "Lưu ý!",
                type: "primary",
                duration: 3000,
                text: "Xin vui lòng chọn ngày chuyển phí!"
            })
        },
        uploadFile(file, param = null) {
            if (param) {
                this.data.to.attached_file = file
            }
        },
        cancelForm() {
            this.$router.push("/tuition-transfers")
        },
        resetForm() {
            this.$router.push("/tuition-transfers")
        },
        saveForm() {
            const that = this
            if (this.data.from.transfer_note == '') {
                alert("Bạn phải nhập Lý Do Chuyển Phí trước khi lưu lại!");
                return false;
            }
            this.html.processing.to = true
            this.html.processing.from = true
            if(!this.data.to.sibling){
                this.data.to.contracts = [this.data.to.contract]
            }
            const transferPrepareData = {
                transfermation_data: {
                    sender: this.data.from.student,
                    receiver: this.data.to.student,
                    receiver_contracts: this.data.to.contracts,
                    transfer_date: this.data.from.transfer_date,
                    transfer_note: this.data.from.transfer_note,
                    transfer_contracts: this.data.from.contracts,
                    to_total_amount: this.data.to.total_amount,
                    to_total_sessions: this.data.to.total_session,
                    from_total_amount: this.data.from.transfer_amount,
                    from_total_sessions: this.data.from.transfer_sessions,
                    file: this.data.to.attached_file,
                    sibling_product: this.data.contract.product_id,
                    sibling: this.data.to.sibling,
                    amount_truythu: u.fmc(this.data.amount_truythu).n,
                    reason_id : this.data.reason_id
                }
            }
            this.html.disabled.save = true
            //Lưu bản ghi chuyển phí
            const tongbuoiconlai = this.data.from.transfer_sessions
            if (u.session().user.role_id != 999999999) {
                u.p("/api/tuition-transfers/save", transferPrepareData).then(response => {
                    //u.lg(response, 'Stored data')
                    this.html.processing.to = false
                    this.html.processing.from = false
                    u.apax.$emit("apaxPopup", {
                        on: true,
                        content: response,
                        title: "Thông Báo",
                        class: "modal-success",
                        size: "md",
                        confirm: {
                            success: {
                                button: "OK",
                                action() {
                                    that.$router.push("/tuition-transfers")
                                }
                            }
                        },
                        variant: "success"
                    })
                })
            } else {
                u.p("/api/tuition-transfers/save", transferPrepareData).then(
                    response => {
                        //u.lg(response, 'Stored data')
                        this.html.processing.to = false
                        this.html.processing.from = false
                        u.apax.$emit("apaxPopup", {
                            on: true,
                            content: `Bản ghi chuyển phí mã "<b>${response}</b>" đã được khởi tạo thành công và chờ được phê duyệt!\n`,
                            title: "Thông Báo",
                            class: "modal-success",
                            size: "md",
                            confirm: {
                                success: {
                                    button: "OK",
                                    action() {
                                        that.$router.push("/tuition-transfers")
                                    }
                                }
                            },
                            variant: "success"
                        })
                    }
                )
            }
        },
        calculateDiscount() {
            const amount_truythu = u.fmc(this.data.amount_truythu);
            this.data.amount_truythu = amount_truythu.s;
            if(this.data.from.transfer_date ){
                this.prepareTransfermation()
            }
        },
        f(text){
            return u.f(text)
        },
    }
}
</script>
<style type="text/css">
.dropdown.v-select.single.searchable.disabled input {
    visibility: hidden !important;
}
.tuition-transfer-form .profile-detail .col-md-8{
    height:28px;
}
</style>
