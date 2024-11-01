<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-tuition-wd">
            <div class="print-box">
                <div class="container">
                    <div class="print-body">
                        <div class="inner">
                            <!-- BEGIN FORM -->
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
                                <div style="height:50px"></div>
                                <h2 class="title">ĐƠN RÚT HỌC PHÍ</h2>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ và tên phụ huynh:</span>
                                            {{student.parent_name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ tên học sinh:</span>
                                            {{student.name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Chương trình:</span>
                                            {{sclass.prog_name}}
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Ngày giờ học:</span>
                                            {{sclass.day_time}}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-line">
                                            <span class="label">Phòng:</span>
                                            {{sclass.clse_room_name}}
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Thời gian: Từ ngày: </span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">đến:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Ngày nộp phí:</span>
                                            {{payment.charge_date | formatDate}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số phí:</span>
                                            {{this.total_charged_all | formatMoney}}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-line">
                                            <span class="label">Số phiếu thu:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã học:</span>
                                            {{withdrawal.done_sessions}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số buổi còn lại:</span>
                                            {{withdrawal.left_sessions}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Số học phí còn lại:</span>
                                            {{withdrawal.real_amount | formatMoney}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Số phí quy đổi theo chính sách rút học phí:</span>
                                            {{withdrawal.refun_amount | formatMoney}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Nay tôi làm đơn này xin rút học phí còn lại của con tôi. Tôi đồng ý với chính sách rút học phí của CMS đã nêu trong Phiếu đăng ký nhập học và học phí còn lại của con tôi được chuyển khoản vào tài khoản của tôi (không nhận tiền mặt).</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Thông tin chuyển khoản:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Chủ tài khoản:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Số tài khoản:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Tên Ngân hàng:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Chi nhánh:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lý do:</span>
                                            <i class="doted"></i>
                                        </div>
                                        <p class="input-line">
                                            <i class="doted"></i>
                                        </p>
                                        <p class="input-line">
                                            <i class="doted"></i>
                                        </p>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <i class="doted"></i>
                                        </div><div class="input-line">
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Đề nghị Trung tâm CMS {{student.branch_name}} <i class="dot-line">............................</i> xem xét và tạo điều kiện cho tôi. Tôi cam đoan sẽ không khiếu nại gì sau này.</p>
                                        <p>Xin chân thành cảm ơn!</p>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 text-right">
                                        <span>Hà Nội, ngày</span><i class="dot-line">........</i><span>tháng</span><i class="dot-line">.........</i><span>năm</span><i class="dot-line">.............</i>
                                    </div>
                                </div>
                                <table class="table table-bordered sign">
                                    <thead>
                                        <tr>
                                            <th><strong>Giám đốc trung tâm</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Kế toán</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>CSKH</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Phụ huynh học sinh</strong><i>Ký ghi rõ họ tên</i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày:</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày:</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày:</span>
                                                    <i class="doted"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                    <span class="label">Ngày:</span>
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
                                                <p>1. Đơn Rút học phí sẽ được xét duyệt và trả lời trong 07 ngày làm việc.</p>
                                                <p>2. Nhân viên kế toán giữ bản gốc, nhân viên CSKH giữ 01 bản photo.</p>
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
        name: 'tuition_withdraw',
        data() {
            return {
                withdrawal_fees: [],
                withdrawal: {},
                contract: {},
                student: {},
                sclass: {},
                payment: {},
                total_charged_all: 0

            }
        },
        created() {
            let storageData = localStorage.getItem(`tuitionwd_${this.$route.params.id}`);
            // console.log(storageData);
            if(storageData){
                // this.withdrawal_fees = JSON.parse(storageData).withdrawal_fees;
                this.withdrawal = JSON.parse(storageData).withdrawal;
                this.total_charged_all = JSON.parse(storageData).total_charged_all
                let conId = parseInt(this.withdrawal.contract_id);
                // console.log(conId);
                u.g('/api/contracts/'+conId)
                  .then(response => {
                    this.student = response.student
                    this.contract = response.contracts.length ? response.contracts[0] : []
                  });
                u.p('/api/classes/bycontract',{conId: conId})
                  .then(response => {
                    this.sclass = response ? response : {class_day_time:'',cls_name:''};
                  });
                u.p('/api/payment/bycontract',{conId: conId})
                  .then(response => {
                    this.payment = response;
                  });
            }

            localStorage.removeItem(`tuitionwd_${this.$route.params.id}`);
            setTimeout(function () {
                window.print();
            }, 1000)
        },
        methods: {
            getPrintData(){
              u.print('tuition_withdraw', 'Đơn rút học phí')
            }
        }
    }
</script>
