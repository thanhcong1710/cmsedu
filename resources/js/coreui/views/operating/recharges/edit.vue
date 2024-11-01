<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>Thông tin học sinh</strong>
                        </div>
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <label class="control-label">Tên học sinh</label>
                                    <input class="form-control" type="text" readonly disabled>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Mã EFFECT</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Mã LMS</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Trung tâm</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Kỳ học</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Sản phẩm</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Chương trình</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Lớp</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Tổng số buổi học</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Số phí đã đóng</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Số phí còn lại</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Số buổi còn lại</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Ngày học cuối</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </b-col>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>Thông tin tái phí</strong>
                        </div>
                        <div class="content-detail">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Kỳ học</label>
                                        <select class="form-control">
                                            <option>Kỳ học 1</option>
                                            <option>Kỳ học 2</option>
                                            <option>Kỳ học 3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Sản phẩm</label>
                                        <select class="form-control">
                                            <option>iGarten</option>
                                            <option>April English</option>
                                            <option>CMS4.0</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Chương trình</label>
                                        <select class="form-control">
                                            <option>Seedbed 1</option>
                                            <option>Seedbed 2</option>
                                            <option>Seed 1</option>
                                            <option>Seed 2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Gói phí</label>
                                        <select class="form-control">
                                            <option>3 tháng</option>
                                            <option>9 tháng</option>
                                            <option>12 tháng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Số buổi</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Giá gốc</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Giá thực thu</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Voucher</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Tích điểm</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Chiết khấu</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Khác</label>
                                        <input class="form-control" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Số tiển phải đóng</label>
                                        <input class="form-control" type="text" readonly disabled>
                                    </div>
                                </div>
                                <div class="col-md-9 col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">Chi tiết diễn giải</label>
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div slot="footer" class="text-right">
                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-dot-circle-o"></i> Lưu</button>
                            <button type="reset" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i> Nhập lại</button>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import u from '../../../utilities/utility'

    export default {
        name: 'Edit-Recharge'
    }
</script>

<style scoped>

</style>