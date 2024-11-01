<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-contract">
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
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="print-content">
                                <h2 class="title">ĐƠN BẢO LƯU HỌC PHÍ</h2>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ tên học sinh: <b>{{item.name}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Chương trình học: <b>{{item.product_name}}</b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Ngày giờ học: <b>{{item.class_time}}</b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-line">
                                            <span class="label">Phòng: <b>{{item.room_name}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Thời gian học ở lớp hiện tại: Từ ngày: <b>{{item.before_reserve_start_date}}</b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">đến: <b>{{item.before_reserve_end_date}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Ngày nộp phí: <b>{{item.charged_at}}</b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-line">
                                            <span class="label">Số phí: <b>{{item.total_fee | formatMoney}}</b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số phiếu thu: <b>{{item.payment_accounting_id}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã học: <b>{{item.done_sessions}}</b> buổi</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-line">
                                            <span class="label">Số buổi còn lại: <b>{{item.number_of_session_reserved}}</b> buổi</span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã bảo lưu: <b>{{item.reserved_sessions}}</b> buổi</span>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Số buổi còn lại ở lớp hiện tại: <b>{{item.done_sessions + item.number_of_session_reserved}}</b> buổi</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Số học phí còn lại: <b>{{item.amount_reserved | formatMoney}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Bằng chữ: <b>{{item.amount_reserved_text}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ và tên phụ huynh: <b>{{item.gud_name}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">SĐT liên hệ: <b>{{item.gud_mobile}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Nay tôi làm đơn này xin bảo lưu học phí trong: <b>{{item.session}}</b> (buổi) </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Kể từ ngày: <b>{{item.reserve_start_date}}</b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="input-line">
                                            <span class="label">đến hết ngày: <b>{{item.reserve_end_date}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Ngày tạo đơn: <b>{{item.created_at}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lý do: <b>{{item.note}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Đề nghị Trung tâm CMS <b>{{item.branch_name}}</b> cho tôi được bảo lưu học phí trong thời gian trên. Tôi xác nhận đã đọc và hiểu về quy định bảo lưu của CMS. Tôi cam kết sẽ tuân thủ quy định bảo lưu của CMS và không có bất cứ khiếu nại gì liên quan đến quy định bảo lưu sau này.</p>
                                        <p>Xin chân thành cảm ơn!</p>

                                    </div>
                                </div>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <table class="table table-bordered sign">
                                    <thead>
                                        <tr>
                                            <td colspan="3"><i>CẤP TRUNG TÂM</i></td>
                                        </tr>
                                        <tr>
                                            <th><strong>Phụ huynh HS</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>CSKH</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>GĐ trung tâm</strong><i>Ký ghi rõ họ tên</i></th>
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
                                        </tr>
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <td colspan="3"><i>CẤP CÔNG TY</i></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3"><strong>TP QA&IC</strong><i>Ký ghi rõ họ tên</i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3"> 
                                                <div class="input-line" style="width:300px;margin:auto;">
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
                                                <p>1. Với các trường hợp bảo lưu theo quy định, GĐTT ký duyệt đơn bảo lưu và chuyển lại đơn bảo lưu scan cho QA&IC.</p>
                                                <p>2. Với các trường hợp bảo lưu ngoài quy định, đơn bảo lưu phải được cấp công ty phê duyệt.</p>
                                                <p>3. Đơn bảo lưu phải có chữ ký của phụ huynh mới được chấp nhận là hợp lệ; trong trường hợp bất khả kháng, PH không thể tới TT, phải đính kèm xác nhận của Phụ huynh qua tin nhắn/email.  Đơn bảo lưu học phí theo quy định của nội quy nhập học sẽ được xét duyệt và trả lời trong 07 ngày làm việc.</p>
                                                <p>4. Phụ huynh học sinh nộp lại hóa đơn/phiếu thu học phí gốc cho CSKH, nhân viên CSKH đính kèm hóa đơn theo đơn này.</p>
                                                <p>5. Khi học sinh nhập học trở lại, phụ huynh vui lòng liên hệ với nhân viên CSKH trước 10 ngày làm việc.</p>
                                                <p>6. Sau khi hết thời hạn bảo lưu, mọi trường hợp gia hạn hay đề xuất khác PH cần liên hệ với Trung tâm tối thiểu 7 ngày trước ngày kết thúc bảo lưu để được tư vấn và hỗ trợ.</p>
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
    name: 'Đơn bảo lưu học phí',
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
      getPrintData(){
        this.flags.form_loading = true;
        u.a().get(`/api/reserves-info/print/${this.$route.params.id}`)
          .then(resp => {
            this.flags.form_loading = false;
            this.item = resp.data.data;
            setTimeout(function () {
              window.print();
            }, 500)
          })
      }
    }
  }
</script>
<style lang="scss">
  @import '/static/css/bootstrap/print.css';
</style>
