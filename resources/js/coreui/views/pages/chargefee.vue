<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-contract">
            <div class="print-box">
                <div class="container">
                    <div class="print-body">
                        <div class="inner">
                            <!-- BEGIN FORM -->
                            <div class="print-header">
                                <div class="h-left" style="width: 30%">
                                    <span class="logo"><img src="/images/print-logo.png" /></span>
                                </div>
                                <div class="h-right" style="border:none">
                                    <h4>CÔNG TY CỔ PHẦN GIÁO DỤC LOGIC LAB</h4>
                                    <p><i class="fa fa-map-marker"></i> Lô B1.1, Số 2 đường Đặng Thai Mai , Phường Quảng An, Quận Tây Hồ, 
     Thành phố Hà Nội, Việt Nam.</p>
                                </div>
                                <div class="h-right" style="border:none; text-align:center;width: 35%">
                                    <p><b>Mẫu số 01 - TT</b></p>
                                    <p>(Ban hành theo TT số 200/2014/TT-BTC </p>
                                    <p>Ngày 22/12/2014 của Bộ trưởng BTC)</p>
                                </div>
                            </div>
                            <div class="print-content">
                                <h2 class="title" style="margin-bottom:0px">PHIẾU THU TIỀN HỌC</h2>
                                <p style="text-align: center;margin-bottom: 20px">{{contract.text_1}}</p>

                                <div class="row">
                                    <div class="col-sm-8"></div>
                                    <div class="col-sm-4" >
                                        <p style="margin-bottom:0px">Số phiếu: C09.24.PTH0009</p>
                                        <p style="margin-bottom:0px">Nợ TK: {{contract.text_debt_amount}}</p>
                                        <p style="margin-bottom:0px">Có TK: {{contract.text_amount}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>Người nộp:</b></span>
                                            {{contract.gud_name1}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>Địa chỉ:</b></span>
                                            {{contract.address}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>Lý do nộp:</b></span>
                                            {{contract.text_2}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>Số tiền:</b></span>
                                            {{contract.text_amount}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>Bằng chữ:</b></span>
                                            {{contract.text_amount_words}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>HTTT:</b></span>
                                            {{contract.text_3}}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label"><b>Kèm theo:</b></span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6" >
                                        <span class="dot-line">..........</span><span>, ngày</span><span class="dot-line">..........</span><span>tháng </span><span class="dot-line">..........</span><span>năm</span><span class="dot-line">...........</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="center">Giám đốc TT</div>
                                        <p class="center">(Ký, họ tên, đóng dấu)</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="center">Giám đốc TT</div>
                                        <p class="center">(Ký, họ tên)</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="center">Người nộp tiền</div>
                                        <p class="center">(Ký, họ tên)</p>
                                    </div>
                                </div>
                                <div class="margin300"></div>
                                <br>
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
            u.g('/api/waitapprove/print/'+conId)
                  .then(response => {
                    this.contract = response
                    console.log(this.contract)
                  });

            setTimeout(function () {
                window.print();
            }, 1000)
        },
        methods: {
            printForm(){
                u.print('contract', 'Phiếu thu')
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