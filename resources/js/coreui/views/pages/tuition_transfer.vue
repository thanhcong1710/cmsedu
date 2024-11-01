<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-tuition-tran">
            <div class="print-box">
                <div class="container">
                    <div class="print-body">
                        <div class="inner">
                            <div v-show="flags.requesting" class="ajax-load content-loading">
                                <div class="load-wrapper">
                                    <div class="loader"></div>
                                    <div v-show="flags.requesting" class="loading-text cssload-loader">Đang xử lý dữ liệu, xin vui lòng chờ trong giây lát...</div>
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
                                <h2 class="title">ĐƠN CHUYỂN PHÍ/ CHUYỂN CỌC</h2>
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Họ tên học sinh: {{item.from_student_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Mã HV: {{item.from_crm_id}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Chương trình học: {{item.from_product_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Lớp học: {{item.from_class_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Ngày nộp phí: {{item.charge_date}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-line">
                                            <span class="label">Số phí: {{item.total_charged | formatMoney}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số phiếu thu: {{item.contract_accounting_id}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã học: {{item.done_sessions}} buổi</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Số buổi còn lại: {{item.transferred_sessions}} buổi</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Số học phí còn lại: {{item.transferred_amount | formatMoney}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Bằng chữ: {{item.transferred_amount_text}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="input-line">
                                            <span class="label">Họ tên phụ huynh: {{item.from_gud_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">SĐT liên hệ: {{item.from_gud_mobile}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Nay tôi làm đơn này xin chuyển phí sang cho học sinh: {{item.to_student_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Mã HV: {{item.to_crm_id}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                     <div class="col-sm-7">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="input-line">
                                            <span class="label">Họ và tên phụ huynh: {{item.to_gud_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">SĐT liên hệ: {{item.to_gud_mobile}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Chương trình học: {{item.to_product_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Số học phí chuyển: {{item.received_amount | formatMoney}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Bằng chữ: {{item.received_amount_text}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lý do chuyển phí:</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">{{item.note}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Đề nghị Trung tâm CMS {{item.from_branch_name}}<i class="dot-line">...........</i>   xem xét và tạo điều kiện cho tôi chuyển phí. Tôi đồng ý với điều khoản đã thỏa thuận với CMS trong Nội quy nhập học và cam đoan sẽ không có bất kỳ khiếu nại gì sau này.</p>
                                        <p>Xin chân thành cảm ơn!</p>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 text-right">
                                        <span>......, ngày</span><i class="dot-line">.........</i><span>tháng</span><i class="dot-line">..........</i><span>năm</span><i class="dot-line">............</i>
                                    </div>
                                </div>
                                <table class="table table-bordered sign">
                                    <thead>
                                        <tr>
                                            <th><strong>Phụ huynh HS</strong><i>Ký, ghi rõ họ tên</i></th>
                                            <th><strong>GĐTT</strong><i>Ký, ghi rõ họ tên</i></th>
                                            <th><strong>Kế toán trưởng</strong><i>Ký, ghi rõ họ tên</i></th>
                                            <th><strong>P. Tổng Giám Đốc</strong><i>Ký, ghi rõ họ tên</i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="flex-box">
                                            <strong>Lưu ý:</strong>
                                            <div class="note">
                                                <p>1. Đơn chuyển phí sẽ được xét duyệt và trả lời trong 07 ngày làm việc.</p>
                                                <p>2. Nhân viên CSKH giữ bản gốc, nhân viên kế toán giữ 01 bản photo.</p>
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
</template>

<script>
    import u from '../../utilities/utility'

    export default {
        name: 'tuition_transfer',
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
        getPrintData() {
          this.flags.form_loading = true;
          u.g(`/api/tuition-transfers/print/${this.$route.params.id}`)
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
