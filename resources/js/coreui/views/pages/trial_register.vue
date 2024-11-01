<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-trail_register">
            <div class="print-box">
                <div class="container">
                    <div class="print-body">
                        <div class="inner">
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
                                <h2 class="title">PHIẾU ĐĂNG KÝ HỌC TRẢI NGHIỆM
                                    <span class="font-italic center">
                                            Ngày/date {{getDate('d')}} tháng/month {{getDate('m')}} năm/year {{getDate('y')}}
                                    </span>
                                </h2>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        Nhân viên tư vấn/ <span class="font-italic">Consultant</span>: {{contract.contract_ec_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        Nhóm/ <span class="font-italic">Team</span>: {{contract.contract_ec_leader_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 field line">
                                        Họ và tên học sinh/ <span class="font-italic">Student's full name</span>: {{student.name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">Trường/ <span class="font-italic">School</span>: {{student.school}}</div>
                                    <div class="col-sm-6">Ngày sinh <span class="font-italic">D.O.B</span>: {{student.date_of_birth | formatDate}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 field line">
                                        Họ và tên phụ huynh/ <span class="font-italic">Parent's name</span>: {{student.parent_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 field line">
                                        Thư điện tử/ <span class="font-italic">E-mail</span>: {{student.parent_email}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 field line">
                                        Số điện thoại/ <span class="font-italic">Phone number</span>: {{student.parent_mobile}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">Lớp đăng kí/ <span class="font-italic">Class: </span> {{std_class.cls_name}}</div>
                                    <div class="col-sm-6">Nickname: {{student.nick}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">Ngày bắt đầu 1/ <span class="font-italic">Start: </span> {{contract.enrolment_start_date}}</div>
                                    <div class="col-sm-4">Ngày kết thúc 1/ <span class="font-italic">End: </span> {{contract.enrolment_last_date}}</div>
                                    <div class="col-sm-4">Số buổi: 1</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">Các yêu cầu khác/
                                        <span class="font-italic">Other requirements</span>:
                                    </div>
                                    <div class="col-sm-7"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 center">
                                        <div class="">
                                            Tư vấn viên
                                        </div>
                                        <p>(Ký và ghi rõ họ tên)</p>
                                    </div>
                                    <div class="col-sm-6 center">
                                        <div class="">
                                            Nhân viên CSKH
                                        </div>
                                        <p>(Ký và ghi rõ họ tên)</p>
                                    </div>
                                    
                                </div>
                                <div class="margin200"></div>
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
    import spell from 'written-number'
    import moment from 'moment'

    spell.defaults.lang = 'vi'


    export default {
        name: 'trail_register',
        data() {
            return {
                contract: {},
                student: {},
                gender : true,
                std_class : {}
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
                
            u.p('/api/classes/bycontract',{conId: this.$route.params.id})
              .then(response => {
                this.std_class = response ? response : {cls_name: ''};
              });
            setTimeout(function () {
                window.print();
            }, 1000)
        },
        methods: {

            printForm() {
                u.print('trail_register', 'Phiếu Học Trải Nghiệm')
            },

            formatTime: (inputtime) => moment(inputtime).format('DD/MM/YYYY'),
            prepareText: txt => txt && txt.length ? u.sub(txt, 20) : '',
            format: (num, c) => {
                let crc = c ? c === '-' ? '' : c : ''
                return num && num > 1000 ? u.currency(num, crc) : `0${crc}`
            },
            getDate(type) {
                const today = new Date()
                let resp = ''
                switch (type) {
                    case 'd':
                        resp = today.getDate()
                        break
                    case 'm':
                        resp = today.getMonth() + 1
                        break
                    case 'y':
                        resp = today.getFullYear()
                        break
                }
                return resp
            },
            loadType(type) {
                let resp = ''
                switch (type) {
                    case 1:
                        resp = 'Chính thức'
                        break
                    case 2:
                        resp = 'Tái phí'
                        break
                    default:
                        resp = 'Học trải nghiệm'
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
            spellNumber(num) {
                return spell(num)
            }
        }
    }
</script>
<style lang="scss">
    @import '/static/css/bootstrap/print.css';
</style>