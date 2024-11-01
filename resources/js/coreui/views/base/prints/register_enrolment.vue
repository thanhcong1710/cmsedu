<template>
  <div id="apax-printing-detail">
    <div>
      <abtn id="print-button"
            :markup="success"
            :onClick="printForm"
            >In Phiếu Thu</abtn>
    </div>
    <div id="apax-printing-register_enrolment">
      <div class="print portrait A4">
        <div class="sheet register_enrolment">
          <div class="detail">
            <div class="row">
              <div class="col-lg-4">
                <img class="im-logo" src="http://apaxenglish.com/public/images/logo_apax.png" />
              </div>
              <div class="col-lg-5 align-center">
                <h2>PHIẾU ĐĂNG KÝ LỚP HỌC</h2>
              </div>
              <div class="col-lg-3">
                &nbsp;
              </div>
            </div>
            <div class="row">
              <div class="col-lg-4">
                &nbsp;
              </div>
              <div class="col-lg-5">
                <h3 class="font-italic align-center">
                  Permission Note
                </h3>
              </div>
              <div class="col-lg-3">
                &nbsp;
              </div>
            </div>
            <div class="row">
              <div class="col-lg-4">
                &nbsp;
              </div>
              <div class="col-lg-5">
                <span class="font-italic align-center">
                  <span class="">Ngày/date {{getDate('d')}} tháng/month {{getDate('m')}} năm/year {{getDate('y')}}</span>
                </span>
              </div>
              <div class="col-lg-3">
                &nbsp;
              </div>
            </div>
            <div class="doc-content">
              <div class="clearfix"></div>

              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="col-lg-12 field line">
               <div class="col-lg-4">Nhân viên tư vấn/ <span class="font-italic">Consultant</span>:</div> <div class="col-lg-4">{{info.ec_name}}</div>
               <div class="col-lg-2">Nhóm/ <span class="font-italic">Group</span>:</div> <div class="col-lg-2">{{info.ec_leader_id}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Họ và tên học sinh/ <span class="font-italic">Student's full name</span>:</div>
                <div class="col-lg-7">{{info.student_name}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Lớp/ <span class="font-italic">Class</span>:</div>
                <div class="col-lg-3">{{info.class}}</div>
                <div class="col-lg-2">Nickname</div>
                <div class="col-lg-3">{{info.student_nick}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số phí/ <span class="font-italic">Tuition Fee</span>:</div>
                <div class="col-lg-3">{{info.tuition_fee_price | formatMoney}}</div>
                <div class="col-lg-3">Số tháng/ <span class="font-italic">Months</span>:</div>
                <div class="col-lg-2">{{info.tuition_fee_name}}</div>
              </div>
              <div class="col-lg-12 field line" style="font-size: 14px">
                Hình thức thanh toán/ <span class="font-italic">Payment Type</span>:
                Tiền mặt/ <span class="font-italic">Cash </span>&nbsp;&nbsp;<i class="square-o"></i>
                Thẻ/ <span class="font-italic">Card </span>&nbsp;&nbsp;<i class="square-o"></i>
                Chuyển khoản/ <span class="font-italic">Transfer </span>&nbsp;&nbsp;<i class="square-o"></i>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Chương trình ưu đãi/ <span class="font-italic">Promotion</span>:</div>
                <div class="col-lg-8" v-html="info.bill_info"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thời gian học/ <span class="font-italic">Duration</span>:</div>
                <div class="col-lg-8">{{info.start_date}} - {{info.end_date}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-12"> Được cho phép với cam kết như sau/ <span class="font-italic">Allowed to attend the class as committed that</span>:</div>
                <div class="col-lg-12">{{printLine(240)}}</div>
              </div>

              <div class="col-lg-12 line">
                <div class="row align-center">
                  <div class="col-lg-2 signature-pdf border-l-r">Nhân viên tư vấn<br /><span class="font-italic">Consutant</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf border-l-r">Trưởng bộ phận<br /><span class="font-italic">Team Leader</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf border-l-r">Phòng kế toán<br /><span class="font-italic">Accounting Dept.</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf border-l-r">Phòng CSKH<br /><span class="font-italic">CSO's verijication</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf">Giám đốc trung tâm<br /><span class="font-italic">Branch Manager</span><p class="dot">{{printLine(36)}}</p></div>
                </div>
              </div>
              <div class="col-lg-12">
                <i class="dot">{{printLine(131, '-')}}</i>
              </div>
              <div class="col-lg-12 im-bold font-italic">
                (Phần dành cho trường hợp đặt cọc và hoàn thành phí)
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số phí đã đóng:</div>
                <div class="col-lg-3">{{printLine(80)}}</div>
                <div class="col-lg-3">Số hoá đơn:</div>
                <div class="col-lg-2">{{printLine(40)}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số phí còn thiếu:</div>
                <div class="col-lg-8">{{printLine(80)}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Ngày cam kết hoàn thành phí:</div>
                <div class="col-lg-8">{{printLine(40)}}</div>
              </div>
              <div class="col-lg-12 field line">
                &nbsp;
              </div>
              <div class="col-lg-12 field line">
                &nbsp;
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số phí đã hoàn thành:</div>
                <div class="col-lg-3">{{printLine(80)}}</div>
                <div class="col-lg-3">Số hoá đơn:</div>
                <div class="col-lg-2">{{printLine(40)}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Ngày:</div>
                <div class="col-lg-8">{{printLine(120)}}</div>
              </div>
              <div class="col-lg-12 field line">
                &nbsp;
              </div>
              <div class="col-lg-12">
                <div class="row p-rela">
                  <div class="col-lg-3 signature-pdf align-center">Phòng kế toán</div>
                  <div class="col-lg-4 signature-pdf align-center">Phòng CSKH</div>
                  <div class="col-lg-4 signature-pdf align-center">Giám đốc trung tâm</div>
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
  import abtn from '../../../components/Button'
  import u from '../../../utilities/utility'
  import spell from 'written-number'
  import moment from 'moment'
  spell.defaults.lang = 'vi'


  export default {
    name: 'dashboard',
    props: {
      info: {
        type: Object,
        default() {
          return {
            ec_name: '',
            ec_leader_id: '',
            student_name: '',
            class: '',
            student_nick: '',
            tuition_fee_price: 0,
            tuition_fee_name: 3,
            payby_cash: 1,
            payby_card: 0,
            payby_transfer: 0,
            bill_info: '',
            available_sessions: 0
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
    watch: {
      info (){
        u.log('PRINT',this.info)
        if (this.info.student_name) {
          setTimeout(() => this.printForm(), 300)
        }
      }
    },
    created() {

    },
    mounted() {
      // u.bus.$events.$on('printEnrolment', ()=>{
      //   u.print('register_enrolment', 'Phiếu Đăng Ký Lớp Học')
      // })
    },
    methods: {
      print(data) {
        console.log(data);

      },
      printForm() {
        u.print('register_enrolment', 'Phiếu Đăng Ký Lớp Học')
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
