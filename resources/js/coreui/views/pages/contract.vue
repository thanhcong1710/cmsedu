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
                                </div>
                            </div>
                            <div class="print-content">
                                <h2 class="title">PHIẾU ĐĂNG KÝ NHẬP HỌC</h2>
                                <h3>I. THÔNG TIN HỌC SINH</h3>
                                <div class="row" style="margin-top:20px;">
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Tên học sinh:</span>
                                            {{student.name}}
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <span>Giới tính:&nbsp;&nbsp;</span>
                                        <span class="label r">Nam &nbsp;<input type="checkbox" :checked="gender ? true : false" disabled /><i></i></span>
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="label r">Nữ &nbsp;<input type="checkbox" :checked="gender ? false : true" disabled /><i></i></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Ngày sinh:</span>
                                            {{student.date_of_birth | formatDate}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Đang học tại trường:</span>
                                            {{student.school}}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-line">
                                            <span class="label">Lớp:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Địa chỉ nhà:</span>
                                            {{student.address}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Trung tâm con đã từng theo học về toán tư duy (nếu có):</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h3>II. THÔNG TIN PHỤ HUYNH</h3>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">1. Họ và tên Mẹ:</span>
                                            {{student.gud_name1}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Ngày sinh:</span>
                                            {{student.gud_birthday1 | formatDate}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">SĐT:</span>
                                            {{student.gud_mobile1}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Email:</span>
                                            {{student.gud_email1}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Nghề nghiệp:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">2. Họ và tên Bố:</span>
                                            {{student.gud_name2}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Ngày sinh:</span>
                                            {{student.gud_birthday2 | formatDate}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">SĐT:</span>
                                            {{student.gud_mobile2}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Email:</span>
                                            {{student.gud_email1}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Nghề nghiệp:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h3>II. THÔNG TIN LIÊN HỆ</h3>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="flex-box">
                                            <span>Kênh liên lạc với phụ huynh:</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; SMS</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; Email</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; Gọi điện</span>
                                            <div class="input-line" style="width: 292px;">
                                                <span class="label">Khác:</span>
                                                <i class="doted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="flex-box">
                                            <span>Thời gian liên lạc:</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; Ngày thường</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; Cuối tuần</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Giờ:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="flex-box">
                                            <span><input type="checkbox" />&nbsp;&nbsp; Giờ hành chính</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; Ngoài giờ hành chính</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Người nhận kết quả học tập của con:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lưu ý đặc biệt cho bố mẹ (nếu có):</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h3>IV. ĐĂNG KÝ KHÓA HỌC</h3>
                                <div class="border">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <span>Khóa học đăng ký:</span>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="flex-box">
                                                <span class="label"><input type="checkbox" :checked="is_ucrea" disabled /><i></i>&nbsp;&nbsp; Ucrea</span>
                                                <span class="label"><input type="checkbox" :checked="is_bright" disabled /><i></i>&nbsp;&nbsp; Bright IG</span>
                                                <span class="label"><input type="checkbox" :checked="is_blkhole" disabled /><i></i>&nbsp;&nbsp; Black Hole</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="input-line">
                                                <span class="label">Thời lượng khóa học:</span>
                                                {{contract.total_sessions}} <span>buổi</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="input-line">
                                                <span class="label">Địa điểm học:</span>
                                                {{contract.branch_name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <span>Nay, tôi làm đơn này kính mong Ban Giám đốc Trung tâm cho cháu: {{student.name}} được nhập học tại {{contract.branch_name}}. </span>
                                    </div>
                                    <div class="col-sm-12">
                                        <span>Tôi cam kết thực hiện theo đúng theo nội quy nhập học tại Trung tâm trong thời gian con học tại Trung tâm.</span>
                                    </div>
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 center" >
                                        <span>Hà Nội, ngày</span><span class="dot-line">..........</span><span>tháng </span><span class="dot-line">..........</span><span>năm</span><span class="dot-line">...........</span>
                                        <div class="center">Phụ huynh</div>
                                        <p class="center">(Ký và ghi rõ họ, tên)</p>
                                    </div>
                                </div>
                                <div class="margin300"></div>
                                <br>
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
                                <div style="height:50px"></div>
                                <hr/>
                                <h3>II. XÁC NHẬN TỪ TRUNG TÂM</h3>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Tư vấn viên:</span>
                                            {{contract.contract_ec_name}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Mã TVV:</span>
                                            {{contract.contract_ec_code}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Trung tâm:</span>
                                            {{student.branch_name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Mã học viên:</span>
                                            {{student.cms_id}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Lớp:</span>
                                            {{std_class.cls_name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Giá trị khóa học:</span>
                                            {{contract.tuition_fee_price | formatMoney}}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Học phí sau ưu đãi:</span>
                                            {{contract.must_charge | formatMoney}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số buổi theo gói phí:</span>
                                            {{contract.real_sessions}} buổi
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số buổi tặng:</span>
                                            {{contract.bonus_sessions}} buổi
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line" v-if="contract.total_charged>0">
                                            <span class="label">Số tiền đã đóng:</span>
                                            {{contract.total_charged | formatMoney}}
                                        </div>
                                        <div class="input-line" v-else>
                                            <span class="label">Số tiền đã đóng:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Số tiền còn lại:</span>
                                            {{contract.debt_amount | formatMoney}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Thời gian hoàn phí:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <span class="test-line">Thông tin hóa đơn:</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <span>Phương thức thanh toán:</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span><input type="checkbox" />&nbsp;&nbsp; Trả thẳng</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span><input type="checkbox" />&nbsp;&nbsp; Trả góp</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Hình thức đóng:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Lịch học đăng ký:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Người giới thiệu:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Điểm kiểm tra năng lực tư duy (Placement test):</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="">
                                            <span class="label" v-for="(item, index) in sources" v-bind:key="index">
                                                <input type="checkbox" />&nbsp;&nbsp; {{item.name}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><input type="checkbox" />&nbsp;&nbsp; Nguồn khác:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="italic">Lưu ý: Ngoài thông tin học sinh thì những thông tin của phụ huynh được đánh dấu * là bắt buộc</p>
                                <div class="row">
                                    <div class="col-sm-6 center">
                                        <strong>Tư vấn viên</strong>
                                        <strong>(Ký và ghi rõ họ tên)</strong>
                                    </div>
                                    <div class="col-sm-6 center">
                                        <strong>Thu ngân</strong>
                                        <strong>(Ký và ghi rõ họ tên)</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="margin200"></div>
                            <div style="height:450px"></div>
                            <!-- END FORM -->
                            <!-- BEGIN RULES -->
                            <div v-if="contract.created_at<'2021-04-01 00:00:00'">
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
                                    <h2 class="title sub">NỘI QUY NHẬP HỌC TẠI TRUNG TÂM</h2>
                                    <strong class="italic">(Áp dụng từ 01/08/2020)</strong>
                                    <table class="table table-bordered tbl-rules">
                                        <tbody>
                                            <tr>
                                                <td style="width:200px;"><strong>I. Quy định chung</strong></td>
                                                <td><ol><li>Thời lượng chương trình: 01 buổi/tuần. Số lượng học sinh tối đa/lớp là 16 học sinh đối với chương trình Ucrea,  Bright IG và Black Hole;</li>
                                                <li>Học sinh phải đi học đúng giờ, nếu đi muộn 15 phút sau giờ học chính thức, học sinh sẽ phải chờ đến tiết thứ 2 mới được vào lớp;</li>
                                                <li>Để đảm bảo hiệu quả trong lớp học, học sinh phải sử dụng sách bản quyền do CMS cung cấp, không sử dụng sách photo;</li>
                                                <li>Phụ huynh không vào lớp học trong giờ học. Trong trường hợp muốn trao đổi hay góp ý với giáo viên, phụ huynh vui lòng liên hệ trước với chuyên viên CSKH;</li>
                                                <li>Trong trường hợp xảy ra các sự kiện bất khả kháng (dịch bệnh, thời tiết xấu,...) có công lệnh của Bộ giáo dục hoặc có thông báo nghỉ của CMS, trung tâm sẽ tổ chức lớp học bù và thông báo tới quý phụ huynh;</li>
                                                <li>Phí ghi danh trả 1 lần: 500.000 VNĐ/1 học viên.</li>
                                                <li>Quý phụ huynh vui lòng hoàn thành học phí đúng thời hạn yêu cầu;</li></ol>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>II. Quy định về lớp học </strong></td>
                                                <td><p style="margin-bottom:0px">Thời gian mở lớp:</p>
                                                <p style="margin-bottom:0px">-	Đối với chương trình Ucrea: Khai giảng hàng tháng</p>
                                                <p style="margin-bottom:0px">-	Chương trình Bright IG và Black Hole: Khai giảng hàng quý.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>III. Chuyển lớp (cùng TT hoặc giữa các TT trong hệ thống CMS)</strong></td>
                                                <td><ol><li>Học sinh được yêu cầu đổi lớp hoặc trung tâm học 01 lần trong suốt khóa học (áp dụng cho khóa học từ 06 tháng trở lên). CMS sẽ thực hiện yêu cầu chuyển lớp nếu có một lớp phù hợp hiện còn chỗ trống;</li>
                                                <li>Thời điểm chuyển lớp:  PH đăng ký Phiếu chuyển lớp, CMS sẽ sắp xếp lớp học phù hợp và chuyển lớp cho HS vào đầu tháng sau (sau khi HS kết thúc sách đang học)</li>
                                                <li>Chuyển TT/địa điểm học: CMS sẽ chuyển cho HS nếu Trung tâm PH yêu cầu có lớp phù hợp.</li></ol>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>IV. Tổ chức học bù</strong></td>
                                                <td><ol style="margin-bottom: 0px;"><li>Điều kiện: PH thông báo nghỉ học cho CMS trước 04 tiếng trước khi lớp học bắt đầu</li>
                                                <p style="margin-bottom:0px">-	Số buổi học bù tối đa: 02 buổi/ 1 level (3 tháng)</p>
                                                <p style="margin-bottom:0px">-	Thời lượng học bù: 60 phút/ buổi</p>
                                                <p style="margin-bottom:0px">-	Sắp xếp lịch học bù: CMS sẽ liên hệ với PH để sắp xếp thời gian phù hợp cho HS và Giáo viên. Nếu HS không đến đúng lịch đã thỏa thuận, HS sẽ mất quyền được học bù buổi học đó.</p>
                                                </ol>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="margin150"></div>
                                <div style="height:10px"></div>
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
                                    <table class="table table-bordered tbl-rules">
                                        <tbody>
                                            <tr>
                                                <td style="width:200px;"><strong>V. Bảo lưu học phí</strong></td>
                                                <td><ol style="margin-bottom: 0px;"><li>HS được bảo lưu 01 lần trong suốt quá trình học tại CMS</li>
                                                <li>Chỉ áp dụng cho khóa học 6 tháng trở lên</li>
                                                <li>Thời gian bảo lưu: </li>
                                                <p style="margin-bottom:0px">-	Với HS học chưa quá 50% thời lượng khóa học: Gói 6 tháng được bảo lưu tối đa 4 buổi, gói 12 tháng trở lên được bảo lưu tối đa 8 buổi.</p>
                                                <p style="margin-bottom:0px">-	Với HS học quá 50% thời lượng khóa học: chỉ được bảo lưu với gói 12 tháng trở lên và thời gian bảo lưu tối đa 4 buổi.</p>
                                                <li>KH bảo lưu từ 5 buổi trở lên, sau bảo lưu mất quyền rút phí, chuyển phí. Thủ tục quay lại học: PH liên hệ với CSKH trước 10 ngày để sắp xếp phù hợp</li>
                                                <li>Thời gian bảo lưu không có tính lũy kế.</li>
                                                </ol>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:200px;"><strong>VI. Đặt cọc, chuyển phí</strong></td>
                                                <td><ol><li>Chỉ áp dụng cho khóa học 06 tháng trở lên</li>
                                                <li>PH chỉ được chuyển phí từ buổi đầu tiên của tháng. Trường hợp HS không thể sắp xếp theo lịch học bình thường để hết quý trước đó. Trung tâm hỗ trợ sắp xếp để HS học với lớp khác có cùng lộ trình đến hết quý hoặc sắp xếp lịch học riêng với Giáo viên tối đa 02 buổi (60 phút/buổi) trong vòng 01 tháng.</li>
                                                <li>Đặt cọc: </li>
                                                <p style="margin-bottom:0px">-	Quý phụ huynh cần đặt cọc tối thiểu 1.000.000 VNĐ.</p>
                                                <p style="margin-bottom:0px">-	Thời gian hoàn thành học phí: 14 ngày (kể cả thứ 7, chủ nhật). Nếu sau 14 ngày PH không hoàn phí thì sẽ không được áp dụng chương trình ưu đãi tại thời điểm đặt cọc</p>
                                                <p style="margin-bottom:0px">-	Sau 60 ngày kể từ ngày đặt cọc PH không hoàn thiện học phí thì số tiền đặt cọc sẽ không còn giá trị sử dụng và không được hoàn lại.</p>
                                                <li>CMS không hoàn lại tiền cọc đã đóng, tuy nhiên Phụ huynh được lựa chọn chuyển cọc cho người khác, và CMS sẽ không áp dụng khấu trừ phí ghi danh của phụ huynh chuyển cọc. Học sinh nhận chuyển cọc phải là học sinh mới (chưa đăng ký/ đặt cọc tại CMS trước đây), đồng thời sẽ hưởng các chương trình ưu đãi tại thời điểm đăng ký mới</li>
                                                <li>Chuyển phí:</li>
                                                <p style="margin-bottom:0px">-	TH học sinh chưa đi học: số học phí được chuyển nhượng được tính bằng 100% giá trị học phí</p>
                                                <p style="margin-bottom:0px">-	TH học sinh đã đi học: học phí đã học được tính tròn đầu sách. Số học phí còn lại của level sau khi trừ phí ghi danh sẽ được chuyển nhượng toàn bộ cho HS khác. HS nhận chuyển phí phải là học sinh mới (chưa đăng ký/ đặt cọc tại CMS trước đây) hoặc trường hợp là anh/chị/em ruột. Không áp dụng phí chuyển đổi đối với phụ huynh muốn chuyển học phí cho anh/chị/em ruột của học sinh đang theo học tại các hệ thống trung tâm của CMS. Học bổng của gói đóng không có giá trị chuyển nhượng.</p>
                                                <p style="margin-bottom:0px">-	Giá trị học phí còn lại được quy đổi thành số tiền theo giá trị gốc của gói học phí; </p>
                                                </ol>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="margin200"></div>
                                <div style="height:20px"></div>
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
                                    <table class="table table-bordered tbl-rules">
                                        <tbody>
                                            <tr>
                                                <td style="width:200px;"></td>
                                                <td><ol> <p style="margin-bottom:0px;margin-left:-20px;">6.  Rút phí:</p>
                                                <p style="margin-bottom:0px">Từ 01/12/2019, CMS không áp dụng chính sách rút phí. Đối với các trường hợp rút phí đóng thừa hoặc rút phí đối với các học sinh trước ngày 01/12/2019, việc rút phí sẽ được tính như sau:</p>
                                                <p style="margin-bottom:0px">-	TH rút phí do đóng thừa: KH được nhận lại phần phí thừa sau khi khấu trừ đồng thời 2% phí quẹt thẻ tín dụng, 3.3% phí trả góp nếu kỳ hạn 6 tháng, 8.3% phí trả góp với kỳ hạn 12 tháng (nếu có)</p>
                                                <p style="margin-bottom:0px">-	TH rút phí đối với học sinh đăng ký trước ngày 01/12/2019: KH được nhận lại 70% học phí còn lại sau khi khấu trừ đồng thời 2% phí quẹt thẻ tín dụng, 3.3% phí trả góp nếu kỳ hạn 6 tháng, 8.3% phí trả góp với kỳ hạn 12 tháng (nếu có).</p></ol>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>VII. Quy định về đảm bảo an toàn cho học sinh</strong></td>
                                                <td><ol><li>Phụ huynh học sinh lưu ý: </li>
                                                <p style="margin-bottom:0px">-	Đăng ký người đưa/ đón HS từ buổi học đầu tiên</p>
                                                <p style="margin-bottom:0px">-	Người đưa/ đón HS phải đưa/đón HS ở địa điểm quy định của Trung tâm, đồng thời ký vào phiếu xác nhận đón trả HS.</p>
                                                <li>Phụ huynh cần đưa đón con đúng giờ. Trường hợp 15 phút sau giờ tan lớp, phụ huynh chưa thu xếp đến đón con, phụ huynh cần gọi điện thông báo qua hotline của TT. Thời gian đón con không muộn quá 30 phút từ lúc tan ca học;</li>
                                                <li>Trong trường hợp phụ huynh không lên được TT, cần người hỗ trợ đưa đón tại tầng 01 tòa nhà, PH có thể gọi nhờ chuyên viên của CMS qua số hotline Trung tâm.</li>
                                                <li>Sau khi phụ huynh đón con thì Trung tâm sẽ không chịu trách nhiệm về an toàn của con.</li></ol>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>VIII.   Sử dụng hình ảnh trong lớp học</strong></td>
                                                <td><p>Đồng ý cho Trung tâm sử dụng mọi hình ảnh của học sinh học tại trung tâm vào mục đích giáo dục và hoạt động của trung tâm.</p></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-sm-6"></div>
                                        <div class="col-sm-6 center">
                                            <span>......., ngày</span><span class="dot-line">......</span><span>tháng </span><span class="dot-line">......</span><span>năm</span><span class="dot-line">........</span>
                                        </div>
                                        <div class="col-sm-6 center">
                                        </div>
                                        <div class="col-sm-6 center">
                                            <p class="center">Phụ huynh học sinh xác nhận đã đọc kỹ và cam kết thực hiện (Ký và ghi rõ họ tên)</p>
                                        </div>
                                    </div>
                                    <div class="margin150"></div>
                                </div>
                            </div>
                            <div class="contract_new" v-else>
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
                                    <h2 class="title sub">NỘI QUY NHẬP HỌC TẠI CMS</h2>
                                    <strong class="italic">(Áp dụng từ ngày 01/12/2022)</strong>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p><b>I. Quy định chung</b></p>
                                            <p>1.	Thời lượng chương trình: 01 buổi/tuần.</p> 
                                            <p>2.	Số lượng Học sinh (HS) tối đa/lớp: </p>
                                            <p>- 12 HS/lớp với lớp Ucrea Level 2; </p>
                                            <p>- 16 HS/lớp với các Level còn lại của chương trình Ucrea</p>
                                            <p>- 18 HS/lớp với chương trình Bright IG (IG), Black Hole (BH).</p>
                                            <p>3.	Để đảm bảo hiệu quả giờ học, HS phải đi học đúng giờ. </p>
                                            <p>4.	HS phải sử dụng sách & tài liệu bản quyền do CMS cung cấp.</p>
                                            <p>5.	Trong trường hợp cần thiết, Phụ Huynh (PH) trao đổi trực tiếp với giáo viên phụ trách lớp hoặc các bộ phận liên quan tại Trung tâm (TT) để được hỗ trợ.</p>
                                            <p>6.	Thời gian mở lớp:</p>
                                            <p>- Chương trình Ucrea: Khai giảng hàng tháng</p>
                                            <p>- Chương trình Bright IG và Black Hole: Khai giảng hàng quý.</p>
                                            <p>7.	HS vào học giữa chương trình:</p>
                                            <p>- HS IG, BH được phép vào học từ đầu sách tháng 2,3 của quý.</p>
                                            <p><b>II. Quy định đặt cọc</b></p>
                                            <p>1. Số tiền đặt cọc tối thiểu: 10% giá trị gói học phí nhưng không thấp hơn 1.000.000 VNĐ.</p>
                                            <p>2. Thời gian hoàn thành học phí: 14 ngày (kể cả thứ 7, chủ nhật) hoặc theo chương trình ưu đãi hiện hành.</p>
                                            <p>3. Trong vòng 60 ngày, PH được quyền chuyển toàn bộ số tiền đóng cọc ban đầu cho HS khác, HS nhận chuyển cọc là HS mới (chưa đặt cọc/đóng phí tại CMS) hoặc là anh/chị/em ruột và được nhận các chương trình ưu đãi tại thời điểm đăng ký mới.</p>
                                            <p>4. Sau 60 ngày kể từ ngày đặt cọc PH không hoàn thiện học phí thì số tiền đặt cọc tối thiểu sẽ không còn giá trị sử dụng và không được hoàn lại. Nếu số tiền PH đặt cọc vượt quá số tiền cọc tối thiểu, số tiền chênh lệch so với tiền cọc tối thiểu chỉ được phép chuyển cho HS mới.</p>
                                            <p>5. HS nhận cọc được phép chuyển phí.</p>
                                            <p><b>III. Quy định chuyển phí</b></p>
                                            <p>1.	Với HS chưa đi học: HS được chuyển nhượng toàn bộ học phí đã nộp.</p>
                                            <p>2.	Với HS đã đi học: số học phí được chuyển nhượng bằng số phí còn lại (sau khi tính tròn đầu sách và quy đổi theo giá tại thời điểm mua) trừ một triệu đồng (phí chuyển nhượng)và các chi phí ngân hàng phát sinh (quẹt thẻ, trả góp,…).</p>
                                            <p>3.	HS nhận chuyển phí là HS mới (chưa đặt cọc/đóng phí tại CMS) hoặc là anh/chị/em ruột. Không áp dụng phí chuyển với PH muốn chuyển học phí cho anh/chị/em ruột của HS đang theo học tại hệ thống TT CMS. Chuyển phí chỉ chuyển tiền không chuyển thời lượng học</p>
                                            <p>4.	HS đã nhận chuyển phí không được chuyển phí cho HS khác</p>
                                            <p>5.	Học bổng chỉ có giá trị chuyển nhượng với trường hợp anh, chị, em ruột.</p>
                                            <p>6.	CMS không áp dụng chính sách rút phí</p>
                                            
                                        </div>
                                        <div class="col-sm-6">
                                            <p><b>IV. Quy định học bù</b></p>
                                            <p>1. Điều kiện: PH thông báo nghỉ học tới CMS trước 2-4 tiếng trước khi lớp học bắt đầu. </p>
                                            <p>Số buổi học bù tối đa: 02 buổi/quý. Trường hợp HS nghỉ học nhưng PH không thông báo cho TT, HS sẽ mất phí buổi học đó và không được sắp xếp học bù.</p>
                                            <p>Trường hợp HS nghỉ ốm và có xác nhận của bệnh viện: HS sẽ được bù tất cả các buổi đã nghỉ.</p>
                                            <p>2. Sắp xếp lịch học bù: CMS sẽ liên hệ với PH để sắp xếp thời gian phù hợp cho HS và Giáo viên, buổi học bù phải trước buổi học tuần tiếp theo. Nếu HS không đến đúng lịch đã thỏa thuận, HS sẽ mất quyền được học bù buổi học đó.</p>
                                            <p>3. Trong trường hợp xảy ra các sự kiện bất khả kháng (dịch bệnh, thời tiết xấu,...) có công lệnh của Bộ giáo dục hoặc có thông báo nghỉ của CMS, TT sẽ tổ chức lớp học bù và thông báo tới PH.</p>
                                             <p><b>V. Quy định về chuyển lớp, chuyển Trung tâm</b></p>
                                            <p>1. Chuyển lớp: PH có nhu cầu chuyển lớp liên hệ với TT; TT xem xét tình trạng học tập hiện tại của HS, nếu có lớp phù hợp sẽ sắp xếp chuyển HS sang lớp mới. Số lần chuyển lớp: tối đa 2 lần/năm.</p>
                                            <p>2. Chuyển TT: PH có nhu cầu chuyển TT làm phiếu đăng ký chuyển TT, yêu cầu chuyển TT được duyệt khi có xác nhận của TT chuyển đi và TT tiếp nhận học sinh. Chuyển TT chỉ áp dụng với các trường hợp HS đã đóng đủ học phí.</p>
                                            
                                            <p><b>VI. Quy định Bảo lưu.</b></p>
                                            <p>1. PH có nhu cầu bảo lưu làm đơn bảo lưu; đơn bảo lưu chỉ được tính là hợp lệ khi có chữ ký xác nhận của PH; trong trường hợp PH không hoàn thiện đơn bảo lưu, yêu cầu bảo lưu bị hủy và CMS vẫn tiếp tục tính phí cho HS.</p>
                                            <p>2. Thời hạn bảo lưu:</p>
                                            <p>- Học sinh mua gói phí 12 tháng trở lên: bảo lưu tối đa 01 lần.</p>
                                            <p>- Thời gian bảo lưu 01 lần tối đa là 06 tháng. Sau thời gian bảo lưu học sinh không quay lại học, CMS sẽ tiếp tục tính số phí còn lại đến hết gói phí. </p>
                                            <p>+ Bảo lưu giữ chỗ: tối đa 04 buổi và trong 01 đầu sách. </p>
                                            <p>- Bảo lưu không giữ chỗ: Từ 05 buổi trở lên đến 06 tháng.</p>
                                            <p>- Học sinh quay lại học tính từ đầu sách mới. </p>
                                            <p><b>VII. Quy định về đảm bảo an toàn cho học sinh</b></p>
                                            <p>1.	PHHS lưu ý: </p>
                                            <p>- Đăng ký người đưa/ đón HS từ buổi học đầu tiên</p>
                                            <p>Người đưa/ đón HS phải đưa/đón HS ở địa điểm quy định của Trung tâm, đồng thời ký vào Phiếu đón trả HS. PH cần đưa đón con đúng giờ. Thời gian đón con không muộn quá 30 phút từ lúc tan ca học. Trường hợp quá 30 phút sau giờ tan lớp, phụ huynh chưa thu xếp đến đón con, phụ huynh cần gọi điện thông báo qua hotline của TT.</p>
                                           
                                           
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-6"></div>
                                        <div class="col-sm-6 center">
                                            <span>......., ngày</span><span class="dot-line">......</span><span>tháng </span><span class="dot-line">......</span><span>năm</span><span class="dot-line">........</span>
                                        </div>
                                        <div class="col-sm-6 center">
                                            <p class="center"><b>Thay mặt BLĐ công ty</b></p>
                                            <p class="center">GĐTT</p>
                                            <p class="center"><i>(Ký, đóng dấu TT)</i></p>
                                        </div>
                                        <div class="col-sm-6 center">
                                            <p class="center"><b>Xác nhận của PH học sinh</b></p>
                                            <p class="center">Tôi đã đọc và cam kết thực hiện nội quy nhập học của CMS Edu</p>
                                            <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                        </div>
                                    </div>
                                    <div class="margin150"></div>
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
        name : 'Contract',
        data() {
            return {
                contract: {},
                student: {},
                gender : true,
                std_class : {},
                is_ucrea : false,
                is_bright : false,
                is_blkhole : false,
                sources:[],
            }
        },
        created() {
            
            let conId = this.$route.params.id;
            u.g('/api/contracts/'+conId)
                  .then(response => {
                    this.student = response.student;
                    this.contract = response.contracts.length ? response.contracts[0] : [];
                    this.gender = response.student.gender.toLowerCase() == 'm' ? true : false;
                    switch(response.student.prod_id){
                        case 1 : this.is_ucrea = true;
                        break;
                        case 2 : this.is_bright = true;
                        break;
                        case 3 : this.is_blkhole = true;
                        break;
                    }
                  });
            u.g(`/api/sources`).then(response => {
                this.sources = response
            })    
            u.p('/api/classes/bycontract',{conId: this.$route.params.id})
              .then(response => {
                this.std_class = response ? response : {cls_name:''};
                // switch(response.product_id){
                //     case 1 : this.is_ucrea = true;
                //     break;
                //     case 2 : this.is_bright = true;
                //     break;
                //     case 3 : this.is_blkhole = true;
                //     break;
                // }
              });

            setTimeout(function () {
                window.print();
            }, 1000)
        },
        methods: {
            printForm(){
                u.print('contract', 'Phiếu đăng ký nhập học')
            },

            formatDate: (inputtime) => inputtime ? moment(inputtime).format('DD/MM/YYYY') : '',
            prepareText: txt => txt && txt.length ? u.sub(txt, 20) : '',
            format: (num, c) => {
                let crc = c ? c === '-' ? '' : c : 'đ'
                return num && num > 1000 ? u.currency(num, crc) : `0${crc}`
            },
            checkDisplay(str) {
                const check = parseInt(this.info.debt_amount, 10)
                return isNaN(parseInt(check, 10)) || check === 0 ? '' : str
            },
            getDate (type) {
                const today = new Date()
                let resp = ''
                switch (type) {
                    case 'd': resp = today.getDate()
                        break
                    case 'm': resp = today.getMonth()+1
                        break
                    case 'y': resp = today.getFullYear()
                        break
                }
                return resp
            },
            loadType(type) {
                let resp = ''
                switch (type) {
                    case 1: resp = 'Chính thức'
                        break
                    case 2: resp = 'Tái phí'
                        break
                    default : resp = 'Học trải nghiệm'
                        break
                }
                return resp
            },
            printLine(length, char) {
                const num = parseInt(length, 10) ? parseInt(length, 10) : 300
                const cha = char ? char : '.'
                let space = cha
                for (let i = 0; i < num; i += 1) {
                    space += cha
                }
                return space
            },
            // spellNumber (num) {
            //     return spell(num)
            // }
        }
    }
</script>
<style>
.contract_new p{
    margin-bottom: 0px;
    font-size: 14px;
    line-height: 18px;
}
</style>