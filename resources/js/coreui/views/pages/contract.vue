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
                                    <h4>CÔNG TY CỔ PHẦN GIÁO DỤC LOGIC LAB</h4>
                                    <p><i class="fa fa-map-marker"></i> Lô B1.1, Số 2 đường Đặng Thai Mai , Phường Quảng An, Quận Tây Hồ, 
     Thành phố Hà Nội, Việt Nam.</p>
                                </div>
                            </div>
                            <div class="print-content">
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
                                            <span class="label">Trung tâm con đã từng theo học về bồi dưỡng kỹ năng (nếu có):</span>
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
                                    <div class="col-sm-6">
                                        <div class="flex-box">
                                            <span>Thời gian liên lạc:</span>
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Giờ:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="flex-box"> 
                                            <span><input type="checkbox" />&nbsp;&nbsp; Ngày thường</span>
                                            <span><input type="checkbox" />&nbsp;&nbsp; Cuối tuần</span>
                                        </div>
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
                                <div class="border">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="flex-box" v-if="contract.product_id < 100">
                                                <span>Khóa học đăng ký: &nbsp;&nbsp;</span>
                                                <span class="label"><input type="checkbox" :checked="is_ucrea" disabled /><i></i>&nbsp;&nbsp; Ucrea</span>
                                                <span class="label"><input type="checkbox" :checked="is_bright" disabled /><i></i>&nbsp;&nbsp; Bright IG</span>
                                                <span class="label"><input type="checkbox" :checked="is_blkhole" disabled /><i></i>&nbsp;&nbsp; Black Hole</span>
                                            </div>
                                            <div class="flex-box" v-else>
                                                <span>Khóa học đăng ký: &nbsp;&nbsp;</span>
                                                <span class="label"><input type="checkbox" :checked="contract.product_id==102" disabled /><i></i>&nbsp;&nbsp; BrickMoto</span>
                                                <span class="label"><input type="checkbox" :checked="contract.product_id==103" disabled /><i></i>&nbsp;&nbsp; PreSchool</span>
                                                <span class="label"><input type="checkbox" :checked="contract.product_id==104" disabled /><i></i>&nbsp;&nbsp; Power Function</span>
                                                <span class="label"><input type="checkbox" :checked="contract.product_id==105" disabled /><i></i>&nbsp;&nbsp; Junior Robotics</span>
                                                <span class="label"><input type="checkbox" :checked="contract.product_id==106" disabled /><i></i>&nbsp;&nbsp; Micro:bit Programming</span>
                                                <span class="label"><input type="checkbox" :checked="contract.product_id==107" disabled /><i></i>&nbsp;&nbsp; Advanced Robotics</span>
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
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="flex-box">
                                                <span>Hình thức đóng: &nbsp;&nbsp;</span>
                                                <span class="label"><input type="checkbox" disabled /><i></i>&nbsp;&nbsp; Tiền mặt</span>
                                                <span class="label"><input type="checkbox" disabled /><i></i>&nbsp;&nbsp; Chuyển khoản</span>
                                                <span class="label"><input type="checkbox" disabled /><i></i>&nbsp;&nbsp; Quẹt thẻ ATM/Visa/Master card</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3>IV. ĐĂNG KÝ KHÓA HỌC</h3>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <span>Nay, tôi làm đơn này kính mong Ban Giám đốc Trung tâm cho cháu: {{student.name}} được nhập học tại {{contract.branch_name}}. </span>
                                    </div>
                                    <div class="col-sm-12">
                                        <span>Tôi cam kết thực hiện theo đúng “Nội quy nhập học” trong thời gian con học tại Trung tâm. Tôi đồng ý cho Logic Lab sử dụng thông tin và hình ảnh của con tôi cho các tài liệu truyền thông với điều kiện Logic Lab sẽ không gây bất cứ ảnh hưởng nào cho cháu.</span>
                                    </div>
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 center" >
                                        <span>.........., ngày</span><span class="dot-line">..........</span><span>tháng </span><span class="dot-line">..........</span><span>năm</span><span class="dot-line">...........</span>
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
                                   <h4>CÔNG TY CỔ PHẦN GIÁO DỤC LOGIC LAB</h4>
                                    <p><i class="fa fa-map-marker"></i> Lô B1.1, Số 2 đường Đặng Thai Mai , Phường Quảng An, Quận Tây Hồ, 
     Thành phố Hà Nội, Việt Nam.</p>
                                    <!-- <p><i class="fa fa-phone"></i>(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope"></i> cms@cmsedu.vn &nbsp;&nbsp;<i class="fa fa-globe"></i> www.cmsedu.vn</p> -->
                                    
                                </div>
                            </div>
                            <div class="print-content">
                                
                                <h3>II. XÁC NHẬN TỪ TRUNG TÂM</h3>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Tư vấn viên:</span>
                                            {{contract.contract_ec_name}}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
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
                                            <span class="label">Mã học sinh:</span>
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
                                        <div class="input-line">
                                            <span class="input-line">Thông tin hóa đơn:</span>
                                            <i class="doted"></i>
                                        </div>
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
                                        <span>Hình thức đóng:&nbsp;&nbsp;</span>
                                        <span><input type="checkbox" />&nbsp;&nbsp; Tiền mặt</span>
                                        <span><input type="checkbox" />&nbsp;&nbsp; Chuyển khoản</span>
                                        <span><input type="checkbox" />&nbsp;&nbsp; Quẹt thẻ ATM/Visa/Master card</span>
                                    </div>
                                </div>
                                <p style="margin-bottom: 0px;">+	Tiền mặt: Liên phiếu thu tiền có đủ chữ ký của Trưởng chi nhánh, đóng dấu trung tâm LogicLab và dấu “Đã thu tiền”.</p>
                                <p style="margin-bottom: 0px;">+	Chuyển khoản:</p>
                                <p style="margin-bottom: 0px;">Tên công ty: CÔNG TY CỔ PHẦN GIÁO DỤC LOGIC LAB</p>
                                <p style="margin-bottom: 0px;">Số TK: 79806789 </p>
                                <p style="margin-bottom: 0px;">Ngân hàng: Ngân hàng Techcombank</p>
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
                                            Nguồn khách hàng:&nbsp;&nbsp;
                                            <span class="label">
                                                <input type="checkbox" />&nbsp;&nbsp; Website
                                            </span>
                                            <span class="label">
                                                <input type="checkbox" />&nbsp;&nbsp; Facebook
                                            </span>
                                            <span class="label">
                                                <input type="checkbox" />&nbsp;&nbsp; Bạn bè
                                            </span>
                                            <span class="label">
                                                <input type="checkbox" />&nbsp;&nbsp; Thư của Trung tâm
                                            </span>
                                            <span class="label">
                                                <input type="checkbox" />&nbsp;&nbsp; Tờ rơi
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
                            <div style="height:65px"></div>
                            <div style="height:580px"></div>
                            <!-- END FORM -->
                            <!-- BEGIN RULES -->
                            
                            <div class="contract_new">
                                <div class="print-header">
                                    <div class="h-left">
                                        <span class="logo"><img src="/images/print-logo.png" /></span>   
                                    </div>
                                    <div class="h-right">
                                       <h4>CÔNG TY CỔ PHẦN GIÁO DỤC LOGIC LAB</h4>
                                    <p><i class="fa fa-map-marker"></i> Lô B1.1, Số 2 đường Đặng Thai Mai , Phường Quảng An, Quận Tây Hồ, 
     Thành phố Hà Nội, Việt Nam.</p>
                                    <!-- <p><i class="fa fa-phone"></i>(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope"></i> cms@cmsedu.vn &nbsp;&nbsp;<i class="fa fa-globe"></i> www.cmsedu.vn</p> -->
                                    </div>
                                </div>
                                <div class="print-content" v-if="contract.product_id > 100">
                                    <h2 class="title sub" style="font-size: 24px">NỘI QUY NHẬP HỌC BRICK4KIDz TẠI LOGIC LAB (Áp dụng từ ngày 24/4/2025)</h2>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p><b>I. Quy định chung</b></p>
                                            <p>1.	Thời lượng chương trình: 01 buổi/tuần hoặc 02 buổi/tuần tùy theo quy định chương trình học.</p> 
                                            <p>2.	Thời lượng chương trình một tháng :</p>
                                            <p>+ Đối với lộ trình 04 buổi/tháng. Số buổi học đăng ký 6 tháng: 24 buổi. Số buổi học đăng ký 12 tháng: 48 buổi</p> 
                                            <p>+ Đối với lộ trình 08 buổi/tháng. Số buổi học đăng ký 6 tháng: 48 buổi. Số buổi học đăng ký 12 tháng: 96 buổi</p>
                                            <p>3.	Số lượng Học sinh (HS) tối đa/lớp: </p>
                                            <p>- 10 HS/lớp với chương trình Pre-School</p>
                                            <p>- 15 HS/lớp với chương trình từ Brick Motor trở lên</p>
                                            <p>4.	Để đảm bảo hiệu quả giờ học, HS phải đi học đúng giờ. </p>
                                            <p>5.	Trong trường hợp cần thiết, Phụ Huynh (PH) trao đổi trực tiếp với giáo viên phụ trách lớp hoặc các bộ phận liên quan tại Trung tâm (TT) để được hỗ trợ.</p>
                                            <p>6.	Thời gian mở lớp mới: hàng tháng, tối thiểu 05 học sinh/lớp sẽ tiến hành khai giảng</p>
                                            <p>7.	HS được phép vào học giữa chương trình và tính theo số buổi học kể từ ngày bắt đầu. Mỗi bài học là 1 chủ đề riêng rẽ có tính kế thừa và phát triển.</p>
                                            <p>8.	Sử dụng hình ảnh của HS trong hoạt động truyền thông và quảng cáo của LogicLab.</p>
                                            <p><b>II. Quy định đặt cọc</b></p>
                                            <p>1. Số tiền đặt cọc tối thiểu: 20% giá trị gói học phí nhưng không thấp hơn 1.000.000 VNĐ.</p>
                                            <p>2. Thời gian hoàn thành học phí: 07 ngày (không bao gồm thứ 7, chủ nhật và các ngày nghỉ lễ) theo gói phí tại thời điểm đặt cọc hoặc theo chương trình ưu đãi hiện hành.</p>
                                            <p>3. Trong vòng 60 ngày, PH được quyền chuyển toàn bộ số tiền đặt cọc ban đầu cho HS khác, HS nhận chuyển cọc là HS mới (chưa đặt cọc/đóng phí tại LogicLab) hoặc là anh/chị/em ruột và được nhận các chương trình ưu đãi tại thời điểm đăng ký mới.</p>
                                            <p>4. Sau 60 ngày kể từ ngày đặt cọc PH không hoàn thiện học phí thì số tiền đặt cọc tối thiểu sẽ không còn giá trị sử dụng và không được hoàn lại. Nếu số tiền PH đặt cọc vượt quá số tiền cọc tối thiểu, số tiền chênh lệch so với tiền cọc tối thiểu chỉ được phép chuyển cho HS mới.</p>
                                            <p>5. HS nhận cọc được phép chuyển phí.</p>
                                            <p>6. LogicLab không áp dụng chính sách rút cọc.</p>
                                            <p><b>III. Quy định chuyển phí</b></p>
                                            <p>1.	Với HS chưa đi học: Số học phí được chuyển nhượng bằng toàn bộ số học phí đã nộp trừ 1.000.000 VNĐ (phí chuyển nhượng) và các chi phí ngân hàng phát sinh bao gồm phí quẹt thẻ, phí trả góp... (nếu có).</p>
                                            <p>2.	Với HS đã đi học: Số học phí được chuyển nhượng bằng số phí còn lại (sau khi tính tròn đầu sách) trừ 1.000.000 VNĐ (phí chuyển nhượng) và các chi phí ngân hàng phát sinh bao gồm phí quẹt thẻ, phí trả góp… (nếu có).</p>
                                            <p>3.	HS nhận chuyển phí là HS mới (chưa đặt cọc/đóng phí tại LogicLab) hoặc là anh/chị/em ruột. Không áp dụng phí chuyển với PH muốn chuyển học phí cho anh/chị/em ruột của HS đang theo học tại hệ thống TT LogicLab. Chuyển phí chỉ chuyển tiền không chuyển thời lượng học. HS nhận chuyển phí (trừ trường hợp anh/chị/em ruột) sẽ phải đóng thêm tiền cho tròn gói phí và không được nhận chương trình khuyến mãi đặc biệt áp dụng tại thời điểm đăng ký</p>
                                            <p>4.	HS đã nhận chuyển phí không được chuyển phí cho HS khác</p>
                                            <p>5.	Học bổng chỉ có giá trị chuyển nhượng với trường hợp anh, chị, em ruột.</p>
                                            <p>6.	LogicLab không áp dụng chính sách rút phí</p>
                                            
                                        </div>
                                        <div class="col-sm-6">
                                            <p><b>IV. Quy định học bù</b></p>
                                            <p>1. Điều kiện: PH thông báo nghỉ học tới LogicLab trước 2-4 tiếng trước khi lớp học bắt đầu. </p>
                                            <p>Số buổi học bù tối đa: 02 buổi/quý. Trường hợp HS nghỉ học nhưng PH không thông báo cho TT, HS sẽ mất phí buổi học đó và không được sắp xếp học bù.</p>
                                            <p>Trường hợp HS nghỉ ốm và có xác nhận của bệnh viện: HS sẽ được bù tất cả các buổi đã nghỉ.</p>
                                            <p>2. Sắp xếp lịch học bù: LogicLab sẽ liên hệ với PH để sắp xếp thời gian phù hợp cho HS và Giáo viên, buổi học bù phải trước buổi học tuần tiếp theo. Nếu HS không đến đúng lịch đã thỏa thuận, HS sẽ mất quyền được học bù buổi học đó.</p>
                                            <p>3. Trong trường hợp xảy ra các sự kiện bất khả kháng (dịch bệnh, thời tiết xấu,...) có công lệnh của Bộ giáo dục hoặc có thông báo nghỉ của LogicLab, TT sẽ tổ chức lớp học bù và thông báo tới PH.</p>
                                             <p><b>V. Quy định về chuyển lớp, chuyển Trung tâm</b></p>
                                            <p>1. Chuyển lớp: PH có nhu cầu chuyển lớp liên hệ với TT; TT xem xét tình trạng học tập hiện tại của HS, nếu có lớp phù hợp sẽ sắp xếp chuyển HS sang lớp mới. Số lần chuyển lớp: tối đa 2 lần/năm.</p>
                                            <p>2. Chuyển TT: PH có nhu cầu chuyển TT làm phiếu đăng ký chuyển TT, yêu cầu chuyển TT được duyệt khi có xác nhận của TT chuyển đi và TT tiếp nhận học sinh. Chuyển TT chỉ áp dụng với các trường hợp HS đã đóng đủ học phí.</p>
                                            
                                            <p><b>VI. Quy định Bảo lưu.</b></p>
                                            <p>1. Thủ tục bảo lưu</p>
                                            <p>- PH có nhu cầu bảo lưu làm đơn bảo lưu. Đơn bảo lưu chỉ được tính là hợp lệ khi có chữ ký xác nhận của PH.</p>
                                            <p>- Trong trường hợp PH không hoàn thiện đơn bảo lưu, yêu cầu bảo lưu bị hủy và LogicLab vẫn tiếp tục tính phí cho HS.</p>
                                            <p>2. Thời hạn bảo lưu:</p>
                                            <p>- Logic Lab cho phép đăng ký bảo lưu đối với HS mua gói học phí 06 tháng trở lên.</p>
                                            <p>- Thời gian bảo lưu 02 lần: Bảo lưu giữ chỗ (tối đa 04 buổi và trong 01 tháng); Bảo lưu không giữ chỗ (từ 05 buổi đến tối đa 3 tháng).</p>
                                            <p>- Trong trường hợp PH muốn gia hạn bảo lưu hoặc có đề xuất khác, PH cần liên hệ TT tối thiểu 7 ngày trước ngày kết thúc bảo lưu để được tư vấn và hỗ trợ. Sau thời gian bảo lưu mà HS không quay lại học, LogicLab sẽ tiếp tục tính số phí còn lại đến hết gói phí. - LogicLab không giới hạn số lần gia hạn bảo lưu, tuy nhiên tại thời điểm HS quay lại học nếu quá độ tuổi học của LogicLab thì sẽ không được LogicLab hoàn trả số học phí còn lại của gói phí.</p>
                                            <p>- Học sinh quay lại học tính từ đầu tháng mới. </p>
                                            <p><b>VII. Quy định về đảm bảo an toàn cho học sinh</b></p>
                                            <p>- Đăng ký người đưa/ đón HS từ buổi học đầu tiên</p>
                                            <p>- Người đưa/ đón HS phải đưa/đón HS ở địa điểm quy định của TT, đồng thời ký vào Phiếu đón trả HS. PH cần đưa đón con đúng giờ. Thời gian đón con không muộn quá 20 phút từ lúc tan ca học. Trường hợp quá 20 phút sau giờ tan lớp, PH chưa thu xếp đến đón con, PH cần gọi điện thông báo qua hotline của TT</p>
                                           
                                           
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
                                            <p class="center">Tôi đã đọc và cam kết thực hiện nội quy nhập học của LogicLab</p>
                                            <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                        </div>
                                    </div>
                                    <div class="margin150"></div>
                                </div>
                                <div class="print-content" v-else>
                                    <h2 class="title sub" style="font-size: 24px">NỘI QUY NHẬP HỌC LOGICMATH TẠI LOGIC LAB (Áp dụng từ ngày 24/04/2025)</h2>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p><b>I. Quy định chung</b></p>
                                            <p>1.	Thời lượng chương trình: 01 buổi/tuần.</p> 
                                            <p>2.	Thời lượng chương trình một tháng = 04 buổi. Số buổi học đăng ký 6 tháng = 24 buổi. Số buổi học đăng ký 12 tháng = 48 buổi</p> 
                                            <p>3.	Số lượng Học sinh (HS) tối đa/lớp: </p>
                                            <p>- 12 HS/lớp với lớp Ucrea Level 2; </p>
                                            <p>- 16 HS/lớp với các Level còn lại của chương trình Ucrea</p>
                                            <p>- 18 HS/lớp với chương trình Bright IG (IG), Black Hole (BH).</p>
                                            <p>4.	Để đảm bảo hiệu quả giờ học, HS phải đi học đúng giờ. </p>
                                            <p>5.	HS phải sử dụng sách & tài liệu bản quyền do LogicLab cung cấp.</p>
                                            <p>6.	Trong trường hợp cần thiết, Phụ Huynh (PH) trao đổi trực tiếp với giáo viên phụ trách lớp hoặc các bộ phận liên quan tại Trung tâm (TT) để được hỗ trợ.</p>
                                            <p>7.	Thời gian mở lớp mới:</p>
                                            <p>- Chương trình Ucrea: Khai giảng hàng tháng</p>
                                            <p>- Chương trình Bright IG và Black Hole: Khai giảng hàng quý.</p>
                                            <p>8.	HS vào học giữa chương trình IG, BH được phép vào học từ đầu sách tháng 2,3 của quý.</p>
                                            <p>9.	Sử dụng hình ảnh của HS trong hoạt động truyền thông, quảng cáo của LogicLab.</p>
                                            <p><b>II. Quy định đặt cọc</b></p>
                                            <p>1. Số tiền đặt cọc tối thiểu: 20% giá trị gói học phí nhưng không thấp hơn 1.000.000 VNĐ.</p>
                                            <p>2. Thời gian hoàn thành học phí: 07 ngày (không bao gồm thứ 7, chủ nhật và các ngày nghỉ lễ) theo gói phí tại thời điểm đặt cọc hoặc theo chương trình ưu đãi hiện hành.</p>
                                            <p>3. Trong vòng 60 ngày, PH được quyền chuyển toàn bộ số tiền đặt cọc ban đầu cho HS khác, HS nhận chuyển cọc là HS mới (chưa đặt cọc/đóng phí tại LogicLab) hoặc là anh/chị/em ruột và được nhận các chương trình ưu đãi tại thời điểm đăng ký mới.</p>
                                            <p>4. Sau 60 ngày kể từ ngày đặt cọc PH không hoàn thiện học phí thì số tiền đặt cọc tối thiểu sẽ không còn giá trị sử dụng và không được hoàn lại. Nếu số tiền PH đặt cọc vượt quá số tiền cọc tối thiểu, số tiền chênh lệch so với tiền cọc tối thiểu chỉ được phép chuyển cho HS mới.</p>
                                            <p>5. HS nhận cọc được phép chuyển phí.</p>
                                            <p>6. Không áp dụng chính sách rút cọc.</p>
                                            <p><b>III. Quy định chuyển phí</b></p>
                                            <p>1.	Với HS chưa đi học: Số học phí được chuyển nhượng bằng toàn bộ số học phí đã nộp trừ 1.000.000 VNĐ (phí chuyển nhượng) và các chi phí ngân hàng phát sinh bao gồm phí quẹt thẻ, phí trả góp... (nếu có).</p>
                                            <p>2.	Với HS đã đi học: Số học phí được chuyển nhượng bằng số phí còn lại (sau khi tính tròn đầu sách) trừ 1.000.000 VNĐ (phí chuyển nhượng) và các chi phí ngân hàng phát sinh bao gồm phí quẹt thẻ, phí trả góp… (nếu có).</p>
                                            <p>3.	HS nhận chuyển phí là HS mới (chưa đặt cọc/đóng phí tại LogicLab) hoặc là anh/chị/em ruột. Không áp dụng phí chuyển với PH muốn chuyển học phí cho anh/chị/em ruột của HS đang theo học tại hệ thống TT LogicLab. Chuyển phí chỉ chuyển tiền không chuyển thời lượng học. HS nhận chuyển phí (trừ trường hợp anh/chị/em ruột) sẽ phải đóng thêm tiền cho tròn gói phí và không được nhận chương trình khuyến mãi đặc biệt áp dụng tại thời điểm đăng ký</p>
                                            <p>4.	HS đã nhận chuyển phí không được chuyển phí cho HS khác</p>
                                            <p>5.	Học bổng chỉ có giá trị chuyển nhượng với trường hợp anh, chị, em ruột.</p>
                                            <p>6.	LogicLab không áp dụng chính sách rút phí</p>
                                            
                                        </div>
                                        <div class="col-sm-6">
                                            <p><b>IV. Quy định học bù</b></p>
                                            <p>1. Điều kiện: PH thông báo nghỉ học tới LogicLab trước 2-4 tiếng trước khi lớp học bắt đầu. </p>
                                            <p>Số buổi học bù tối đa: 02 buổi/quý. Trường hợp HS nghỉ học nhưng PH không thông báo cho TT, HS sẽ mất phí buổi học đó và không được sắp xếp học bù.</p>
                                            <p>Trường hợp HS nghỉ ốm và có xác nhận của bệnh viện: HS sẽ được bù tất cả các buổi đã nghỉ.</p>
                                            <p>2. Sắp xếp lịch học bù: LogicLab sẽ liên hệ với PH để sắp xếp thời gian phù hợp cho HS và Giáo viên, buổi học bù phải trước buổi học tuần tiếp theo. Nếu HS không đến đúng lịch đã thỏa thuận, HS sẽ mất quyền được học bù buổi học đó.</p>
                                            <p>3. Trong trường hợp xảy ra các sự kiện bất khả kháng (dịch bệnh, thời tiết xấu,...) có công lệnh của Bộ giáo dục hoặc có thông báo nghỉ của LogicLab, TT sẽ tổ chức lớp học bù và thông báo tới PH.</p>
                                             <p><b>V. Quy định về chuyển lớp, chuyển Trung tâm</b></p>
                                            <p>1. Chuyển lớp: PH có nhu cầu chuyển lớp liên hệ với TT; TT xem xét tình trạng học tập hiện tại của HS, nếu có lớp phù hợp sẽ sắp xếp chuyển HS sang lớp mới. Số lần chuyển lớp: tối đa 2 lần/năm.</p>
                                            <p>2. Chuyển TT: PH có nhu cầu chuyển TT làm phiếu đăng ký chuyển TT, yêu cầu chuyển TT được duyệt khi có xác nhận của TT chuyển đi và TT tiếp nhận học sinh. Chuyển TT chỉ áp dụng với các trường hợp HS đã đóng đủ học phí.</p>
                                            
                                            <p><b>VI. Quy định Bảo lưu.</b></p>
                                            <p>1. Thủ tục bảo lưu</p>
                                            <p>- PH có nhu cầu bảo lưu làm đơn bảo lưu. Đơn bảo lưu chỉ được tính là hợp lệ khi có chữ ký xác nhận của PH.</p>
                                            <p>- Trong trường hợp PH không hoàn thiện đơn bảo lưu, yêu cầu bảo lưu bị hủy và LogicLab vẫn tiếp tục tính phí cho HS.</p>
                                            <p>2. Thời hạn bảo lưu:</p>
                                            <p>- LogicLab cho phép đăng ký bảo lưu đối với HS mua gói học phí 12 tháng trở lên</p>
                                            <p>- Thời gian bảo lưu 01 lần: Bảo lưu giữ chỗ (tối đa 04 buổi và trong 01 đầu sách); Bảo lưu không giữ chỗ (từ 05 buổi đến tối đa 3 tháng).</p>
                                            <p>- Trong trường hợp PH muốn gia hạn bảo lưu hoặc có đề xuất khác, PH cần liên hệ TT tối thiểu 7 ngày trước ngày kết thúc bảo lưu để được tư vấn và hỗ trợ. Sau thời gian bảo lưu mà HS không quay lại học, LogicLab sẽ tiếp tục tính số phí còn lại đến hết gói phí. - LogicLab không giới hạn số lần gia hạn bảo lưu, tuy nhiên tại thời điểm HS quay lại học nếu quá độ tuổi học của LogicLab thì sẽ không được LogicLab hoàn trả số học phí còn lại của gói phí.</p>
                                            <p>- Học sinh quay lại học tính từ đầu sách mới. </p>
                                            <p><b>VII. Quy định về đảm bảo an toàn cho học sinh</b></p>
                                            <p>- Đăng ký người đưa/ đón HS từ buổi học đầu tiên</p>
                                            <p>- Người đưa/ đón HS phải đưa/đón HS ở địa điểm quy định của TT, đồng thời ký vào Phiếu đón trả HS. PH cần đưa đón con đúng giờ. Thời gian đón con không muộn quá 20 phút từ lúc tan ca học. Trường hợp quá 20 phút sau giờ tan lớp, PH chưa thu xếp đến đón con, PH cần gọi điện thông báo qua hotline của TT</p>
                                           
                                           
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
                                            <p class="center">Tôi đã đọc và cam kết thực hiện nội quy nhập học của LogicLab</p>
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