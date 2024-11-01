<template>
	<div id="page-content">
        <div class="row">
            <div class="col-lg-12">
	            <b-card>
		            <form @submit.prevent="studentRegister" method="post">
		                <div class="panel">
		                    <div class="panel-heading">
		                        <div class="panel-control"></div>
		                        <h3 class="panel-title">Đăng ký học</h3>
		                    </div>
		                    <div class="panel-body">
		                        <div class="row">
		                            <div class="col-md-6 pad-no">
		                                <div class="col-md-12">
		                                    <address>
		                                        <strong class="text-main">Thông tin học sinh</strong><br>
		                                    </address>
		                                </div>
		                                <div class="col-md-12">
		                                    <label class="control-label">Tìm học sinh</label>
		                                    <div class="input-group mar-btm">
		                                        <input placeholder="Search" class="form-control" type="text" readonly>
		                                        <span class="input-group-btn">
		                                            <button class="btn btn-primary" type="button">Tìm kiếm</button>
		                                        </span>
		                                    </div>
		                                </div>
		                                <div class="col-md-12 pad-no">
		                                    <div class="col-md-4">
		                                        <div class="form-group">
		                                            <label class="control-label">Mã CRM</label>
		                                            <input class="form-control" type="text" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-4">
		                                        <div class="form-group">
		                                            <label class="control-label">Mã EFFECT</label>
		                                            <input class="form-control" type="text" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-4">
		                                        <div class="form-group">
		                                            <label class="control-label">Mã LMS</label>
		                                            <input class="form-control" type="text" readonly>
		                                        </div>
		                                    </div>
		                                </div>

		                                <div class="col-md-12">
		                                    <div class="form-group">
		                                        <label class="control-label">Phụ huynh</label>
		                                        <input class="form-control" type="text" value="load từ server" readonly>
		                                    </div>
		                                </div>
		                                <div class="col-md-12 pad-no">
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Số điện thoại</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Email</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                </div>
		                                <div class="col-md-12">
		                                    <div class="form-group">
		                                        <label class="control-label">Trung tâm</label>
		                                        <input class="form-control" type="text" value="load từ server" readonly>
		                                    </div>
		                                </div>
		                                <div class="col-md-12 pad-no">
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">EC</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">EC Leader</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                </div>
		                                <div class="col-md-12 pad-no">
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Giám đốc trung tâm</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Giám đốc vùng</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-md-6 pad-no">
		                                <div class="col-md-12">
		                                    <address>
		                                        <strong class="text-main">Thông tin đăng ký chương trình học</strong><br>
		                                    </address>
		                                </div>
		                                <div class="col-md-12">
		                                    <label class="control-label">Chương trình</label>
		                                    <div class="input-group mar-btm">
		                                        <input placeholder="Search" class="form-control" type="text" readonly>
		                                        <span class="input-group-btn">
		                                            <button class="btn btn-primary" type="button">Tìm kiếm</button>
		                                        </span>
		                                    </div>
		                                </div>

		                                <div class="col-md-12">
		                                    <div class="form-group">
		                                        <label class="control-label">Loại khách hàng</label>
		                                        <select class="form-control">
		                                            <option>Học trải nghiệm</option>
		                                            <option>Chính thức</option>
		                                            <option>Tái phí cùng lớp</option>
		                                            <option>Tái phí khác lớp</option>
		                                        </select>
		                                    </div>
		                                </div>
		                                <div class="col-md-12">
		                                    <div class="form-group">
		                                        <label class="control-label">Gói phí</label>
		                                        <select class="form-control">
		                                            <option>3 tháng</option>
		                                            <option>6 tháng</option>
		                                            <option>9 tháng</option>
		                                            <option>12 tháng</option>
		                                        </select>
		                                    </div>
		                                </div>
		                                <div class="col-md-12 pad-no">
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Số buổi</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Đơn giá</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                </div>
		                                <div class="col-md-12 pad-no">
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Giá trị gói</label>
		                                            <input class="form-control" type="text" value="load từ server" readonly>
		                                        </div>
		                                    </div>
		                                    <div class="col-md-6">
		                                        <div class="form-group">
		                                            <label class="control-label">Số tiền phải đóng</label>
		                                            <input class="form-control" type="text">
		                                        </div>
		                                    </div>
		                                </div>
		                                <div class="col-md-12">
		                                    <div class="form-group">
		                                        <label class="control-label">Chi tiết diễn giải (*)</label>
		                                        <textarea class="form-control" rows="5" placeholder="Chi tiết các khoản khấu trừ"></textarea>
		                                    </div>
		                                </div>
		                                <div class="col-md-12">
		                                    <div class="col-md-6">
		                                        <div class="form-group select-date">
		                                            <label class="control-label">Ngày dự kiến học</label>
		                                            <input class="form-control" type="text">
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="panel-footer">
		                        <div class="row">
		                            <div class="col-sm-9 col-sm-offset-3 text-right button-register">
		                                <button class="btn btn-success" type="submit">Lưu</button>
		                                <button class="btn btn-warning" type="reset">Hủy</button>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		                <div class="panel">
		                    <div class="panel-heading">
		                        <div class="panel-control"></div>
		                        <h3 class="panel-title">Lịch sử đăng ký</h3>
		                    </div>
		                    <div class="panel-body">

		                        <div class="table-responsive scrollable">
		                            <table class="table table-bordered table-striped">
		                                <thead>
		                                <tr class="text-sm">
		                                    <th class="width-50">STT</th>
		                                    <th class="width-150">Tên học viên</th>
		                                    <th class="width-150">Mã CRM</th>
		                                    <th class="width-150">Mã LMS</th>
		                                    <th class="width-150">Mã EFFECT</th>
		                                    <th class="width-150">Trung tâm</th>
		                                    <th class="width-150">Chương trình</th>
		                                    <th class="width-100">Gói phí</th>
		                                    <th class="width-100">Số buổi</th>
		                                    <th class="width-150">Giá trị gói</th>
		                                    <th class="width-150">Số tiền phải đóng</th>
		                                    <th class="width-100">Ngày dự kiến học</th>
		                                    <th class="width-100"></th>
		                                </tr>
		                                </thead>
		                                <tbody>
		                                <tr>
		                                    <td>1</td>
		                                    <td>Đặng Thị Yến</td>
		                                    <td>EFFECT20171124107</td>
		                                    <td>155508</td>
		                                    <td>THV-LHA-03024</td>
		                                    <td>HN - Trung tâm Láng Hạ</td>
		                                    <td>Seedbed 1</td>
		                                    <td>12 tháng</td>
		                                    <td>96</td>
		                                    <td>33.600.000</td>
		                                    <td>23.800.000</td>
		                                    <td>30/11/2017</td>
		                                    <td>
		                                    	<button class="btn btn-info btn-icon" data-toggle="tooltip" title="Chi tiết"><i class="demo-pli-information"></i>
		                                    	</button>
		                                    </td>
		                                </tr>
		                                </tbody>
		                            </table>
		                        </div>
		                    </div>
		                </div>
		            </form>
	            </b-card>
            </div>
        </div>
    </div>
</template>

<script>
	export default {

	}
</script>
<style scoped>
	.panel-footer{
		height: 60px;
		padding-bottom: 10px;
	}
	.button-register {
		margin-top: 15px;
	}
</style>