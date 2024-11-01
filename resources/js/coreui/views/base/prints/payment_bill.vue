<template>
  <div id="apax-printing-detail">
    <div>
      <abtn id="print-button"
            :markup="success"
            :onClick="printForm"
            >In Phiếu Thu</abtn>
    </div>
    <div id="apax-printing-payment_bill">
      <div class="print portrait A4">
        <div class="sheet payment_bill">
          <div class="detail">
            <div class="col-lg-4">
              <img class="im-logo" src="/static/img/images/big_logo.png" />
            </div>
            <div class="col-lg-8">
              <div class="row">
                <div class="col-lg-7">CÔNG TY CỔ PHẦN ANH NGỮ APAX</div>
                <div class="col-lg-2 align-center">Serial:</div>
                <div class="col-lg-3">{{infor.payment_serial}}</div>
                <div class="col-lg-7">{{infor.branch_name | strToUpperCase}}</div>
                <div class="col-lg-2 align-center">Số HĐ:</div>
                <div class="col-lg-3">{{infor.contract_number}}</div>
                <div class="col-lg-7">&nbsp;</div>
                <div class="col-lg-2 align-center">Số:</div>
                <div class="col-lg-3">{{infor.payment_number}}</div>
              </div>
            </div>
            <div class="col-lg-12 align-center doc-title">
              <h2 class="im-bold">PHIẾU THU</h2>
            </div>
            <div class="col-lg-12 align-center">
              {{infor.payment_info}}
            </div>
            <div class="doc-content">
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-lg-3">
                  Họ tên người nộp tiền
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                  {{infor.parent_name}}
                </div>
                <div class="col-lg-3">
                  Địa chỉ
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                 {{infor.address}}
                </div>
                <div class="col-lg-3">
                  Điện thoại
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                  <div class="row">
                    <div class="col-lg-4">
                     {{infor.parent_mobile}}
                    </div>
                    <div class="col-lg-3">
                      CMND
                    </div>
                    <div class="col-lg-1">:</div>
                    <div class="col-lg-4">
                      {{infor.parent_card}}
                    </div>
                  </div>
                </div>
                <div class="col-lg-3">
                  Hình thức thanh toán
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                  <div class="row">
                    <div class="col-lg-4">
                      {{infor.payment_type}}
                    </div>
                    <div class="col-lg-3">
                      Tình trạng
                    </div>
                    <div class="col-lg-1">:</div>
                    <div class="col-lg-4">
                      {{infor.status}}
                    </div>
                  </div>
                </div>
                <div class="col-lg-3">
                  Lý do nộp
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                  {{infor.reason}}
                </div>
                <div class="col-lg-3">
                  &nbsp;
                </div>
                <div class="col-lg-1">&nbsp;</div>
                <div class="col-lg-8">
                  <i class="dot">{{printLine(110)}}</i>
                </div>
                <div class="col-lg-3">
                  Số tiền
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                  {{infor.amount | formatMoney}}
                </div>
                <div class="col-lg-3">
                  Viết bằng chữ
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                  {{infor.amount}}
                </div>
                <div class="col-lg-3">
                  Kèm theo
                </div>
                <div class="col-lg-1">:</div>
                <div class="col-lg-8">
                 {{infor.bill_info}}
                </div>
                <div class="col-lg-3">
                  &nbsp;
                </div>
                <div class="col-lg-1">&nbsp;</div>
                <div class="col-lg-8">
                  <i class="dot">{{printLine(110)}}</i>
                </div>
                <div class="col-lg-3">
                  &nbsp;
                </div>
                <div class="col-lg-1">&nbsp;</div>
                <div class="col-lg-8">
                  <i class="dot">{{printLine(110)}}</i>
                </div>
                <div class="col-lg-3">
                  &nbsp;
                </div>
                <div class="col-lg-1">&nbsp;</div>
                <div class="col-lg-8">
                  <div class="row">
                    <div class="col-lg-5">
                      &nbsp;
                    </div>
                    <div class="col-lg-7 align-right">
                      <span class="">Ngày {{getDate('d')}} tháng {{getDate('m')}} năm {{getDate('y')}}</span>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">&nbsp;</div>
                <div class="col-lg-3">
                  &nbsp;
                </div>
                <div class="col-lg-1">&nbsp;</div>
                <div class="col-lg-8">
                  <div class="row">
                    <div class="col-lg-5">
                      Người nộp tiền<br />
                      <span class="font-italic">(Ký, họ tên)</span>
                    </div>
                    <div class="col-lg-2">
                      &nbsp;
                    </div>
                    <div class="col-lg-5">
                      Người nhận<br />
                      <span class="font-italic">(Ký, họ tên)</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
     <!--  <abtn id="print-buttonx"
            :markup="success"
            >In Giấy Nhập Học</abtn> -->
            <!-- <button @click="printPaymentBillForm">In giay nhap hoc</button> -->
    </div>
  </div>
</template>

<script>
  import abtn from '../../../components/Button'
  import u from '../../../utilities/utility'
  import spell from 'written-number'
  import moment from 'moment'
  spell.defaults.lang = 'vi'


  export default {
    name: 'dashboard',
    props: {
      infor: {
        type: Object,
        default(){
          return {
            branch_name: '',
            payment_serial: 'AE/17P1',
            contract_number: '000096 / 000096',
            payment_number: 'PT09 / 17-00099',
            payment_info: '36 - 12/09/2017 16:30pm',
            parent_name: '',
            address: '',
            parent_mobile: '',
            parent_card: '',
            payment_type: '',
            status: 'MF',
            reason: '',
            amount: 0,
            bill_info: ''
          }
        }
      }
    },
    data() {
      return {
        success: 'success',
      }
    },
    components: {
      abtn
    },
    created() { },
    watch: {
      infor (){
        if (this.infor.amount) {
          setTimeout(() => this.printForm(), 300)
        }
      }
    },
    methods: {
      printForm() {
        u.print('payment_bill', 'Phiếu thu')
      },
      print(id, tlt) {
        console.log(`ID: ${id} / tlt: ${tlt}`)
        const title = tlt || ''
        const contents = $(`#apax-printing-${id}`).html();
        const frame1 = $('<iframe />')
        frame1[0].name = 'frame1'
        frame1.css({ position: 'absolute', top: '-1000000px' })
        $('body').append(frame1)
        const frameDoc = frame1[0].contentWindow
          ? frame1[0].contentWindow
          : frame1[0].contentDocument.document
            ? frame1[0].contentDocument.document
            : frame1[0].contentDocument
        frameDoc.document.open()
        frameDoc.document.write('<html><body>')
        frameDoc.document.write('<header>')
        frameDoc.document.write(`<title>${title}</title>`)
        frameDoc.document.write(`<link rel="stylesheet" href="/static/css/prints/${id}.css" type="text/css" media="print"></header>`)
        frameDoc.document.write(contents)
        frameDoc.document.write('</body></html>')
        frameDoc.document.close()
        setTimeout(function () {
          window.frames['frame1'].focus()
          window.frames['frame1'].print()
          frame1.remove()
        }, 500)
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
          case 'd': resp = today.getDate()
            break
          case 'm': resp = today.getMonth() + 1
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
          default: resp = 'Học trải nghiệm'
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
