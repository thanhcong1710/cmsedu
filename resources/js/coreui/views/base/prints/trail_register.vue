<template>
  <div id="apax-printing-detail">
    <div>
      <abtn id="print-button"
            :markup="success"
            :onClick="printForm">In Giấy Học trải nghiệm</abtn>
    </div>
    <div id="apax-printing-trail_register">
      <div class="print portrait A4">
        <div class="sheet trail_register">
          <div class="detail">
            <div class="row">
              <div class="col-lg-4">
                <img class="im-logo" src="/static/img/images/big_logo.png" />
              </div>
              <div class="col-lg-5 align-center">
                <h2>PHIẾU HỌC THỬ</h2>
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
                  Trial Note
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
                <div class="col-lg-4">Nhân viên tư vấn/ <span class="font-italic">Consultant</span>:</div>
                <div class="col-lg-3">{{infor.contract_ec_name}}</div>
                <div class="col-lg-2">Nhóm/ <span class="font-italic">Team</span>:</div>
                <div class="col-lg-3">{{infor.team}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Họ và tên học sinh/ <span class="font-italic">Student's full name</span>:</div>
                <div class="col-lg-7">{{infor.student_name}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Trường/ <span class="font-italic">School</span>:</div>
                <div class="col-lg-3">{{infor.school}}</div>
                <div class="col-lg-3">Ngày sinh <span class="font-italic">D.O.B</span>:</div>
                <div class="col-lg-3">{{infor.birthday}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Họ và tên phụ huynh/ <span class="font-italic">Parent's name</span>:</div>
                <div class="col-lg-7">{{infor.parent_name}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thư điện tử/ <span class="font-italic">E-mail</span>:</div>
                <div class="col-lg-8">{{infor.parent_email}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số điện thoại/ <span class="font-italic">Phone number</span>:</div>
                <div class="col-lg-8">{{infor.parent_mobile}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Lớp đăng kí/ <span class="font-italic">Class</span>:</div>
                <div class="col-lg-3">{{infor.expected_class}}</div>
                <div class="col-lg-3">Nickname</div>
                <div class="col-lg-3">{{infor.student_nick}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Ngày bắt đầu 1/ <span class="font-italic">Start</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-3">Ngày kết thúc 1/ <span class="font-italic">End</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-1">Số buổi:</div>
                <div class="col-lg-1"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Ngày bắt đầu 2/ <span class="font-italic">Start</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-3">Ngày kết thúc 2/ <span class="font-italic">End</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-1">Số buổi:</div>
                <div class="col-lg-1"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Các yêu cầu khác/ <span class="font-italic">Other requirements</span>:</div>
                <div class="col-lg-7"></div>
              </div>
              <div class="col-lg-12">
                <div class="row align-center">
                  <div class="col-lg-2 signature-pdf"></div>
                  <div class="col-lg-2 signature-pdf">Nhân viên tư vấn<br /><span class="font-italic">Consutant</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf"></div>
                  <div class="col-lg-2 signature-pdf">Nhân viên CSKH<br /><span class="font-italic">CSO</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf"></div>
                </div>
              </div>
              <div class="col-lg-12">
                <i class="dot">{{printLine(70, ' -')}}</i>
              </div>
              <div class="row">
              <div class="col-lg-4">
                <img class="im-logo" src="/static/img/images/big_logo.png" />
              </div>
              <div class="col-lg-5 align-center">
                <h2>PHIẾU HỌC THỬ</h2>
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
                  Trial Note
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
                <div class="col-lg-4">Nhân viên tư vấn/ <span class="font-italic">Consultant</span>:</div>
                <div class="col-lg-3">{{infor.contract_ec_name}}</div>
                <div class="col-lg-2">Nhóm/ <span class="font-italic">Team</span>:</div>
                <div class="col-lg-3">{{infor.team}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Họ và tên học sinh/ <span class="font-italic">Student's full name</span>:</div>
                <div class="col-lg-7">{{infor.student_name}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Trường/ <span class="font-italic">School</span>:</div>
                <div class="col-lg-3">{{infor.school}}</div>
                <div class="col-lg-3">Ngày sinh <span class="font-italic">D.O.B</span>:</div>
                <div class="col-lg-3">{{infor.birthday}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Họ và tên phụ huynh/ <span class="font-italic">Parent's name</span>:</div>
                <div class="col-lg-7">{{infor.parent_name}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thư điện tử/ <span class="font-italic">E-mail</span>:</div>
                <div class="col-lg-8">{{infor.parent_email}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số điện thoại/ <span class="font-italic">Phone number</span>:</div>
                <div class="col-lg-8">{{infor.parent_mobile}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Lớp đăng kí/ <span class="font-italic">Class</span>:</div>
                <div class="col-lg-3">{{infor.expected_class}}</div>
                <div class="col-lg-3">Nickname</div>
                <div class="col-lg-3">{{infor.student_nick}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Ngày bắt đầu 1/ <span class="font-italic">Start</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-3">Ngày kết thúc 1/ <span class="font-italic">End</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-1">Số buổi:</div>
                <div class="col-lg-1"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Ngày bắt đầu 2/ <span class="font-italic">Start</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-3">Ngày kết thúc 2/ <span class="font-italic">End</span>:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-1">Số buổi:</div>
                <div class="col-lg-1"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-5">Các yêu cầu khác/ <span class="font-italic">Other requirements</span>:</div>
                <div class="col-lg-7"></div>
              </div>

              <div class="col-lg-12">
                <div class="row align-center">
                  <div class="col-lg-2 signature-pdf"></div>
                  <div class="col-lg-2 signature-pdf">Nhân viên tư vấn<br /><span class="font-italic">Consutant</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf"></div>
                  <div class="col-lg-2 signature-pdf">Nhân viên CSKH<br /><span class="font-italic">CSO</span><p class="dot">{{printLine(36)}}</p></div>
                  <div class="col-lg-2 signature-pdf"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <abtn id="print-buttonx"
            :markup="success"
            :onClick="printForm">In Giấy Nhập Học</abtn>
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
    data() {
      return {
        success: 'success'
      }
    },
    props: {
      infor: {
        type: Object,
        default() {
          return {
            contract_ec_name: '',
            team: '',
            student_name: '',
            school: '',
            birthday: '',
            parent_name: '',
            parent_email: '',
            parent_mobile: '',
            student_nick:'',
            expected_class: ''
          }
        }
      },
      print: ''
    },
    components: {
      abtn
    },
    created() { },
    watch: {
      print (val) {
        u.log('EEEEE', val)
        if (this.infor.student_name) {
          setTimeout(() => this.printForm(), 300)
        }
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
