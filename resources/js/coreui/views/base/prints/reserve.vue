<template>
  <div id="apax-printing-detail">
    <div>
      <abtn id="print-button"
            :markup="success"
            :onClick="callPrintForm"
            >In Đơn Bảo Lưu Học Phí</abtn>
    </div>
    <div id="apax-printing-reserve">
      <div class="print portrait A4">
        <div class="sheet reserve">
          <div class="detail">
            <div class="col-lg-12">
              <img class="im-logo" src="/static/img/images/big_logo.png" />
            </div>
            <div class="col-lg-12">
              <div class="col-md-3 float-right note corner top right">
                BM: 01 –TTApax
              </div>
            </div>
            <div class="col-lg-12 align-center doc-title">
              <h2>ĐƠN BẢO LƯU HỌC PHÍ</h2>
            </div>
            <div class="doc-content">
              <div class="clearfix"></div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Họ tên phụ huynh: </div>
                <div class="col-lg-8">{{item.ten_phu_huynh}}</div>
              </div>
              <div class="col-lg-12 field line">
                 <div class="col-lg-4">Họ tên học sinh: </div>
                <div class="col-lg-8">{{item.ten_hoc_sinh}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Điện thoại liên hệ: </div>
                <div class="col-lg-8">{{this.student.phone}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-2">Khoá học:</div>
                <div class="col-lg-2">{{item.khoa_hoc}}</div>
                <div class="col-lg-2">Ngày giờ học:</div>
                <div class="col-lg-2">{{item.ngay_gio_hoc}}</div>
                <div class="col-lg-2">Phòng:</div>
                <div class="col-lg-2"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thời gian diễn ra khoá học:</div>
                <div class="col-lg-2">Từ ngày:</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-2">đến</div>
                <div class="col-lg-2"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-2">Ngày nộp phí:</div>
                <div class="col-lg-2">{{ item.ngay_nop_phi}}</div>
                <div class="col-lg-2">Số phí:</div>
                <div class="col-lg-2">{{ item.so_phi }}</div>
                <div class="col-lg-2">Số biên lai:</div>
                <div class="col-lg-2">{{item.so_bien_lai}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Số buổi đã học:</div>
                <div class="col-lg-3">{{item.so_buoi_da_hoc}}</div>
                <div class="col-lg-3">Số buổi chưa học xin bảo lưu:</div>
                <div class="col-lg-3">{{item.so_buoi_bao_luu}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số tiền bảo lưu:</div>
                <div class="col-lg-8">{{item.so_tien_bao_luu}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thời gian bảo lưu:</div>
                <div class="col-lg-2">Từ ngày:</div>
                <div class="col-lg-2">{{item.tu_ngay}}</div>
                <div class="col-lg-2">đến</div>
                <div class="col-lg-2">{{item.den_ngay}}</div>
              </div>
              <div class="col-lg-12 field line">
                 <div class="col-lg-4">Lý do xin bảo lưu:</div>
                <div class="col-lg-8">{{item.ly_do}}</div>
              </div>
              <div class="col-lg-12 field line">
                <i class="dot">{{printLine(151)}}</i>
              </div>
              <div class="col-lg-12 field line">
                <i class="dot">{{printLine(151)}}</i>
              </div>

              <div class="col-lg-12">
                Đề nghị CMScho tôi được bảo lưu học phí theo thời gian trên. Sau thời gian này nếu con tôi không tiếp tục học tại trường, học phí đã đóng sẽ không còn giá trị bảo lưu và sẽ không được hoàn trả. Tôi cam đoan sẽ không khiếu nại gì sau này.<br/>
                Xin chân thành cảm ơn!
              </div>

              <div class="dv-white-space"></div>
              <div class="col-lg-12 line">
                <div class="row p-rela">
                  <div class="col-lg-2 signature-pdf border-l-r"> Phòng Kế toán<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                  <div class="col-lg-3 signature-pdf border-l-r"> Giám đốc Trung tâm<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                  <div class="col-lg-3 signature-pdf border-l-r"> Phòng CSKH<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                  <div class="col-lg-1 signature-pdf"> Phụ huynh học sinh<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                </div>
              </div>
              <div class="dv-white-space"></div>
              <div class="dv-white-space"></div>
              <div class="col-lg-12 field line height bold amount-detail font-italic">
                <div class="col-lg-1">Lưu ý:</div>
                <div class="col-lg-11 float-right font-normal">1. Đơn bảo lưu sẽ được xét duyệt và trả lời trong vòng 3 ngày làm việc.</div>
                <div class="col-lg-11 float-right font-normal">2. Đơn bảo lưu sẽ có giá trị trong 01 tháng kể từ khi được xét duyệt. Sau 01 tháng, đơn bảo lưu này không còn hiệu lực</div>
                <div class="col-lg-11 float-right font-normal">3. Phụ huynh thông báo 03 ngày trước khi quay lại học.</div>
                <div class="col-lg-11 float-right font-normal">4. Phòng CSKH lưu 1 bản. Phòng kế toán lưu 1 bản.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>

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
    props: ['item'],
      
    data() {
      return {
        success: 'success',
        student: {
          phone: '0999000987'
        }
      }
    },
    components: {
      abtn
    },
    created() { },
    methods: {

      callPrintForm() {
        this.print('reserve', 'Đơn Bảo Lưu Học Phí')
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
    },
    watch: {
      data (){
        if (this.data.fullNameStudent) {
          setTimeout(() => this.printForm(), 300)
        }
      }
    }
  }
</script>
