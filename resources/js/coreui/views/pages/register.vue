<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-trail_register">
            <div class="print portrait A4 custom">
                <div class="sheet trail_register">
                    <div class="detail">
                        <div class="row">
                            <div class="col-sm-3 text-center">
                                <img class="im-logo" src="/static/img/images/icon/apax-logo.png"/>
                            </div>
                            <div class="col-sm-6 align-center">
                                <h2 class="trial-title">PHIẾU ĐĂNG KÝ LỚP HỌC</h2>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <h3 class="font-italic align-center trial-title">
                                    Permission Note
                                </h3>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6 text-center">
                                <span class="font-italic">
                                    Ngày/date {{getDate('d')}} tháng/month {{getDate('m')}} năm/year {{getDate('y')}}
                                </span>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <div class="doc-content position-relative">
                            <div class="clearfix"></div>

                            <div class="col-sm-12">
                                &nbsp;
                            </div>
                            <div class="col-sm-12 field line">
                                <div class="row">
                                    <div class="col-sm-12">Nhân viên tư vấn/ <span class="font-italic">Consultant</span>: {{info.ec_name}}</div>
                                </div>
                            </div>
                            <div class="col-sm-12 field line">
                                <div class="row">
                                    <div class="col-sm-12">Nhóm/ <span class="font-italic">Group</span>: {{info.ec_leader_name}}</div>
                                </div>
                            </div>
                            <div class="col-sm-12 field line">
                                Họ và tên học sinh/ <span class="font-italic">Student's full name</span>: {{info.student_name}}
                            </div>
                            <div class="col-sm-12 field line">
                                <div class="row">
                                    <div class="col-sm-6">Lớp/ <span class="font-italic">Class</span>: {{info.class}}</div>
                                    <div class="col-sm-6">Nickname: {{info.student_nick}}</div>
                                </div>
                            </div>
                            <div class="col-sm-12 field line">
                                <div class="row">
                                    <div class="col-sm-6">Số phí/ <span class="font-italic">Tuition Fee</span>: {{info.tuition_fee_price | formatMoney}}</div>
                                    <div class="col-sm-6">Số tháng/ <span class="font-italic">Months</span>: {{info.tuition_fee_name}}</div>
                                </div>
                            </div>
                            <div class="col-sm-12 field line">
                                Hình thức thanh toán/<span class="font-italic">Payment Type</span>:
                                <span class="cash-itm">Tiền mặt/ <span class="font-italic">Cash</span> <i :class="checkPayment(info.payment_method, 0)"></i></span>
                                <span class="card-itm">Thẻ/ <span class="font-italic">Card</span> <i :class="checkPayment(info.payment_method, 2)"></i></span>
                                <span class="tran-itm">Chuyển khoản/ <span class="font-italic">Transfer</span> <i :class="checkPayment(info.payment_method, 1)"></i></span>
                            </div>
                            <div class="col-sm-12 field line">
                                <div class="row">
                                    <div class="col-sm-4">Chương trình ưu đãi/ <span class="font-italic">Promotion</span>:</div>
                                    <div class="col-sm-8" v-html="info.bill_info"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 field line">
                                Thời gian học/ <span class="font-italic">Duration</span>: {{formatTime(info.start_date)}} - {{formatTime(info.last_date)}}
                            </div>
                            <div class="col-sm-12 field line">
                                Được phép vào lớp với cam kết như sau/ <span class="font-italic">Allowed to attend the class as committed that</span>:
                                <br><br>{{printLine(210)}}
                                <br><br>{{printLine(210)}}
                            </div><br>
                            <div class="col-sm-12 line">
                                <div class="row p-rela">
                                    <div class="signature-pdf border-l-r percent-20">
                                        Nhân viên tư vấn<br/>
                                        <span>Consutant</span>
                                    </div>
                                    <div class="signature-pdf border-l-r percent-19">
                                        Trưởng bộ phận<br/>
                                        <span>Team Leader</span>
                                    </div>
                                    <div class="signature-pdf border-l-r percent-20">
                                        Phòng Kế toán<br/>
                                        <span>Accounting Dept.</span>
                                    </div>
                                    <div class="signature-pdf border-l-r percent-22">
                                        Phòng CSKH<br/>
                                        <span>CSO’s verification</span>
                                    </div>
                                    <div class="signature-pdf percent-19">
                                        Giám đốc trung tâm<br/>
                                        <span>Branch Manager</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 line">
                                <div class="strike mar-top-20"></div>
                            </div><br/>
                            <div class="col-sm-12 line italic bold mar-bottom-20">
                                (Phần dành cho trường hợp đặt cọc và hoàn thành phí)
                            </div>
                            <div class="col-sm-12 line">
                                <div class="row">
                                    <div class="col-sm-7">Số phí đã đóng: {{info.charged_total | formatMoney}}</div>
                                    <div class="col-sm-5">Số hóa đơn: </div>
                                </div>
                            </div>
                            <div class="col-sm-12 line">
                                Số phí còn thiếu: {{info.debt_amount | formatMoney}}
                            </div>
                            <div class="col-sm-12 line mar-bottom-20">
                                Ngày cam kết hoàn thành phí:
                            </div>
                            <div class="col-sm-12 line">
                                <div class="row">
                                    <div class="col-sm-7">Số phí đã hoàn thành: </div>
                                    <div class="col-sm-5">Số hóa đơn: </div>
                                </div>
                            </div>
                            <div class="col-sm-12 line mar-bottom-20">
                                Ngày:
                            </div>
                            <div class="col-sm-12 line">
                                <div class="row p-rela">
                                    <div class="col-sm-4 signature-pdf align-center"> Phòng Kế toán
                                    </div>
                                    <div class="col-sm-4 signature-pdf align-center"> Phòng CSKH
                                    </div>
                                    <div class="col-sm-4 signature-pdf align-center">Giám đốc trung tâm
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
    import spell from 'written-number'
    import moment from 'moment'

    spell.defaults.lang = 'vi'


    export default {
        name: 'dashboard',
        data() {
            return {
                success: 'success',
                info: {
                    contract_ec_name: '',
                    team: '',
                    student_name: '',
                    school: '',
                    birthday: '',
                    parent_name: '',
                    parent_email: '',
                    parent_mobile: '',
                    student_nick: '',
                    debt_amount: '',
                    charged_total: '',
                    expected_class: ''
                }
            }
        },
        created() {
            let contract = localStorage.getItem(`e_${this.$route.params.id}`);
            if(contract){
                this.info = JSON.parse(contract);
                u.log(this.info);
                localStorage.removeItem(`e_${this.$route.params.id}`);
                setTimeout(function () {
                    window.print();
                }, 1000)
            }
        },
        methods: {

            printForm() {
                u.print('trail_register', 'Phiếu Học Thử')
            },

            formatTime: (inputtime) => moment(inputtime).format('DD/MM/YYYY'),
            prepareText: txt => txt && txt.length ? u.sub(txt, 20) : '',
            format: (num, c) => {
                let crc = c ? c === '-' ? '' : c : ''
                return num && num > 1000 ? u.currency(num, crc) : `0${crc}`
            },
            checkPayment(method=0, type=0) {
                return method === type ? 'fa fa-check-square-o' : 'fa fa-square-o'
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
    .cash-itm {
        margin:0 10px 0 5px;
    }
    .card-itm {
        margin:0 10px 0 5px;
    }
    .tran-itm {
        margin:0 10px 0 5px;
    }
    @import '/static/css/bootstrap/print.css';
</style>
