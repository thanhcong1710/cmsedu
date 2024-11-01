<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-enrolment">
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
                                    <h4>CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ</h4>
                                    <p><i class="fa fa-map-marker"></i> Tầng 4, tòa 21T2 Hapulico Complex, 81 Vũ Trọng Phụng, Thanh Xuân, Hà Nội</p>
                                    <p><i class="fa fa-phone"></i>(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope"></i> cms@cmsedu.vn &nbsp;&nbsp;<i class="fa fa-globe"></i> www.cmsedu.vn</p>    
                                </div>
                            </div>
                            <div class="print-content">
                                <h2 class="title">PHIẾU NHẬP HỌC
                                    <strong>Số PN:..................... Ngày: ....../....../......</strong>
                                    <strong>Số PDK:.....................</strong>
                                </h2>
                                
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="input-line">
                                            <span class="label">Tên học viên:</span>
                                            {{student.name}}
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="input-line">
                                            <span class="label">Giới tính:</span>
                                            {{student.gender | genderToName}}
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Ngày sinh:</span>
                                            {{student.date_of_birth | formatDate}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Địa chỉ:</span>
                                            {{student.address}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Họ tên Cha/Mẹ học viên:</span>
                                            {{student.parent_name}}
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">SĐT liên hệ:</span>
                                            {{student.parent_mobile}}
                                        </div>
                                    </div>
                                </div>
                                <div class="border">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-line">
                                                <span class="label">Khóa học đăng ký:</span>
                                                {{contract.tuition_fee_name}}
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="input-line">
                                                <span class="label">Thời gian:</span>
                                                <i class="doted"></i>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="input-line">
                                                <span class="label">Địa điểm Đăng ký:</span>
                                                {{student.branch_name}}
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="input-line">
                                                <span class="label">Địa điểm học:</span>
                                                {{student.branch_name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-5">
                                        <span class="label">Thời gian khóa học: </span>
                                        {{contract.tuition_fee_session}}<span> buổi</span>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="flex-box">
                                            <span class="label">Bắt đầu: {{contract.enrolment_start_date | formatDate}}</span>
                                            <span class="label"> Kết thúc: {{contract.enrolment_last_date | formatDate}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <span class="label">Tổng giá trị khóa học sau KM/CK: {{contract.must_charge | formatMoney}}</span>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="flex-box">
                                            <span>Học phí </span><i class="dot-line">.......................</i>
                                            <span>Giáo trình </span><i class="dot-line">.......................</i>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <span class="label">Tổng số tiền đã nộp: {{contract.total_charged | formatMoney}}</span>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="flex-box">
                                            <span>Học phí</span><i class="dot-line">.......................</i>
                                            <span>Giáo trình</span><i class="dot-line">.......................</i>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <span class="label">Số tiền còn phải nộp: {{contract.debt_amount | formatMoney}}</span>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="flex-box">
                                            <span>Học phí</span><i class="dot-line">.......................</i>
                                            <span>Giáo trình</span><i class="dot-line">.......................</i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6"></div>
                                    <div class="col-lg-6">
                                        <span>Hà Nội, ngày</span><i class="dot-line">.....</i><span>tháng</span><i class="dot-line">......</i><span>năm</span><i class="dot-line">......</i>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 center">
                                        <strong>GIÁM ĐỐC TRUNG TÂM</strong>
                                        <strong>(Ký và ghi rõ họ tên)</strong>
                                    </div>
                                    <div class="col-lg-6 center">
                                        <strong>CSKH PHỤ TRÁCH</strong>
                                        <strong>(Ký và ghi rõ họ tên)</strong>
                                    </div>
                                </div>
                                <div class="margin200"></div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Giáo viên phụ trách: </span>
                                            {{sclass.full_name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Mã lớp học:</span>
                                            {{sclass.cls_name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Trình độ:</span>
                                            {{contract.product_name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Tình trạng cấp sách và học cụ:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-line">
                                            <span class="label">Ghi chú:</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6"></div>
                                    <div class="col-lg-6">
                                        <span>Hà Nội, ngày</span><i class="dot-line">.....</i><span>tháng</span><i class="dot-line">......</i><span>năm</span><i class="dot-line">......</i>
                                        <div class="center">
                                            <strong>CSKH PHỤ TRÁCH</strong>
                                            <strong>(Ký và ghi rõ họ tên)</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="margin200"></div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="s-note">
                                            <p><span>Lưu ý: Phiếu nhập học in 4 liên</span></p>
                                            <p>- Liên 1 lưu tại quầy.</p>
                                            <p>- Liên 2: Chuyển kế toán hội sở</p>
                                            <p>- Liên 3: Chuyển cho Giáo viên phụ trách lớp học</p>
                                            <p>- Liên 4: Chuyển cho Phụ huynh/học viên</p>
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
        name: 'Enrolment',
        data() {
            return {
                contract: {},
                student: {},
                gender : true,
                sclass : '',
                is_ucrea : false,
                is_bright : false,
                is_blkhole : false
            }
        },
        created() {
            let conId = this.$route.params.id;
            u.g('/api/contracts/'+conId)
                  .then(response => {
                    this.student = response.student;
                    this.contract = response.contracts.length ? response.contracts[0] : [];
                    this.gender = response.student.gender.toLowerCase() == 'm' ? true : false;
                  });
            u.p('/api/classes/bycontract',{conId: conId})
              .then(response => {
                this.sclass = response;
                // console.log(response)
              });
            // window.print();
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
