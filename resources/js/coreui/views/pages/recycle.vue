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
                                <h2 class="title">ĐƠN TÁI TỤC</h2>

                                <div class="row">
                                    <div class="col-sm-12 sub-title">
                                        <h4>THÔNG TIN HỌC VIÊN</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line"><span class="label">Họ tên học sinh: </span> {{ student.student_name }}</div></div>
                                    <div class="col-sm-6"><div class="input-line"><span class="label">Nickname: </span>{{ student.student_nick }}</div></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lớp học hiện tại: </span><i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <span class="label">Mã học viên: {{student.crm_id}}</span>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line"><span class="label">Họ và tên bố/mẹ: </span>{{student.parent_name}}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6"><div class="input-line"><span class="label">Di động: </span>{{student.parent_mobile}}</div></div>
                                    <div class="col-sm-6"><div class="input-line"><span class="label">Email: </span>{{student.parent_email}}</div></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Phương thức liên lạc: </span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Điện thoại: </span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-line">
                                            <span class="label">Email: </span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>THÔNG TIN TÁI TỤC</h4>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">
                                                        Cấp độ
                                                    </th>
                                                    <th class="text-center">
                                                        Thời gian hết phí
                                                    </th>
                                                    <th class="text-center">
                                                        Thời gian tái phí
                                                    </th>
                                                    <th class="text-center">
                                                        Số hoá đơn
                                                    </th>
                                                    <th class="text-center">
                                                        Số phí đã đóng
                                                    </th>
                                                    <th class="text-center">
                                                        Ghi chú
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="text-center">{{student.program_name}}</td>
                                                    <td class="text-center">{{student.timeoutFee}}</td>
                                                    <td class="text-center">{{formatTime(student.start_date)}}</td>
                                                    <td class="text-center">{{student.tuition_fee_name}}</td>
                                                    <td class="text-center" v-if="student.total_charged>0">{{student.total_charged | formatMoney}}</td>
                                                    <td class="text-center" v-else>...</td>
                                                    <td class="text-center">{{student.notes}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 field line height mar-top-5">
                                        Tôi, {{student.parent_name}}, đồng ý cho CMS sử dụng thông tin và hình ảnh của con tôi
                                        cho các tài liệu truyền thông với điều kiện CMS sẽ không gây bất cứ ảnh hưởng
                                        nào cho bé.
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 text-right">
                                        <span>Hà Nội, ngày </span><i class="dot-line">..........</i><span>tháng </span><i class="dot-line">..........</i><span>năm </span><i class="dot-line">............</i>
                                    </div>
                                </div>    
                                <table class="table table-bordered sign">
                                    <thead>
                                        <tr>
                                            <th><strong>Xác nhận của CSKH</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Xác nhận của kế toán</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Phụ huynh học sinh</strong><i>Ký ghi rõ họ tên</i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-line">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-line">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-8"></div>
                                    <div class="col-sm-4">
                                        <strong>Xác nhận của GĐ Trung tâm</strong>
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
        name: 'dashboard',
        data() {
            return {
                success: 'success',
                item: '',
                student: {},
                recycleInfo: {},
                customerName: '',
                std_class : {}
            }
        },
        created() {
            let contract = localStorage.getItem(`contract_${this.$route.params.id}`);
            if(contract){
                this.student = JSON.parse(contract);
                // u.log(this.student);
                // let conId = parseInt(this.$route.params.id);
                // u.p('/api/classes/bycontract',{conId: conId})
                //   .then(response => {
                //     this.std_class = response ? response : {cls_name: ''};
                //   });
                localStorage.removeItem(`contract_${this.$route.params.id}`);
                setTimeout(function () {
                    window.print();
                }, 500)
            }
        },
        methods: {
            formatTime: (inputtime) => moment(inputtime).format('DD/MM/YYYY'),
            prepareText: txt => txt && txt.length ? u.sub(txt, 20) : '',
            format: (num, c) => {
                let crc = c ? c === '-' ? '' : c : 'đ'
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
    .recycle{
        padding-top: 150px
    }
</style>