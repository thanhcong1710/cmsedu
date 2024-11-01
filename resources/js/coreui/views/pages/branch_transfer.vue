<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-branch-tran">
            <div class="print-box">
                <div class="container">
                    <div class="print-body">
                        <div class="inner">
                            <div v-show="flags.form_loading" class="ajax-load content-loading">
                                <div class="load-wrapper">
                                    <div class="loader"></div>
                                    <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...
                                    </div>
                                </div>
                            </div>
                            <div class="print-header">
                                <div class="h-left">
                                    <span class="logo"><img src="/images/print-logo.png" /></span>
                                </div>
                                <div class="h-right">
                                    <h4>CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ CMS</h4>
                                    <p><i class="fa fa-map-marker"></i> Tầng 4, 21T2 Hapulico Complex, 01 Nguyễn Huy Tưởng, Phường Thanh Xuân Trung, Quận Thanh Xuân, Thành phố Hà Nội, Việt Nam</p>
                                    <!-- <p><i class="fa fa-phone"></i>(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope"></i> cms@cmsedu.vn &nbsp;&nbsp;<i class="fa fa-globe"></i> www.cmsedu.vn</p> -->
                                </div>
                            </div>
                            <div class="print-content">
                                <h2 class="title">PHIẾU ĐĂNG KÝ CHUYỂN ĐỔI TRUNG TÂM</h2>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <strong>1. Phần dành cho học sinh</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ & tên học sinh: {{item.student_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Mã CRM: {{item.crm_id}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Mã Cyber: {{item.accounting_id}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ & tên phụ huynh: {{item.gud_name1}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">SĐT liên hệ: {{item.gud_mobile1}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Xin chuyển từ trung tâm: {{item.from_branch_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">đến trung tâm: {{item.to_branch_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lý do: {{item.note}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <strong>2. Phần dành cho trung tâm</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Ngày đóng phí: {{item.charge_date}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số phí: {{f(item.total_charged)}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Chương trình học: {{item.product_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã học:  {{item.done_sessions!=='' ? item.done_sessions+' buổi':''}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số buổi còn lại: {{item.transferred_sessions+' buổi'}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Học phí còn lại: {{f(item.transferred_amount)}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="input-line">
                                            <span class="label"> Bằng chữ: {{item.transferred_amount_text}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Ngày chuyển Trung tâm: {{item.transfer_date}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Ngày bắt đầu học tại trung tâm nhận: {{item.transfer_date}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6">
                                        <span>Hà Nội, ngày</span><i class="dot-line">.........</i><span>tháng</span><i class="dot-line">.........</i><span>năm</span><i class="dot-line">...........</i>
                                    </div>
                                </div>
                                <table class="table table-bordered sign">
                                    <thead>
                                        <tr>
                                            <th><strong>Xác nhận của Phụ huynh</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Xác nhận của CSKH</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Xác Nhận Của Kế toán</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Phê duyệt của GĐTT</strong><i>Ký ghi rõ họ tên</i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-line">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="" style="text_align:center">
                                                    {{item.creator_name}}<br>
                                                    <span style="font-weight: normal;">({{item.created_date}})</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="" style="text_align:center">
                                                     {{item.accounting_name}}<br>
                                                     <span style="font-weight: normal;">({{item.account_approved_date}})</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="" style="text_align:center">
                                                     {{item.to_approver_name}}<br>
                                                     <span style="font-weight: normal;">({{item.to_approved_date}})</span>
                                                </div>
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
</template>

<script>
    import u from '../../utilities/utility'
    export default {
        name: 'branch_transfer',
        data() {
          return {
            item: {},
            flags: {
              form_loading: false
            }
          }
        },
        created() {
            this.getPrintData();
        },
        methods: {
            f(text){
                return u.f(text)
            },
            getPrintData() {
                this.flags.form_loading = true;
                u.g(`/api/branch-transfers/print/${this.$route.params.id}`)
                .then(resp => {
                    this.flags.form_loading = false;
                    this.item = resp;
                    setTimeout(function () {
                    window.print();
                    }, 500)
                })
            }
        }
    }
</script>
