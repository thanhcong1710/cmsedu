<template>
  <div id="apax-printing-detail">
    <div>
      <abtn id="print-button"
            :markup="success"
            :onClick="callPrintForm"
            >In Chuyển Học Phí</abtn>
    </div>
    <div id="apax-printing-tuition_fee_transfer">
      <div class="print portrait A4">
        <div class="sheet tuition_fee_transfer">
          <div class="detail">
            <!--<div class="col-lg-12">
              <img class="im-logo" src="/static/img/images/big_logo.png" />
            </div>-->
            <div class="col-lg-12">
              <div class="col-md-3 float-right note corner top right">
                BM: 02 –TTApax
              </div>
            </div>
            <div class="col-lg-12 align-center doc-title">
              <h2>ĐƠN CHUYỂN HỌC PHÍ</h2>
            </div>
            <div class="doc-content">
              <div class="clearfix"></div>

              <div class="col-lg-12 field line">
                <div class="col-lg-4">Họ tên phụ huynh:</div>
                <div class="col-lg-8">{{item.ten_phu_huynh}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Họ tên học sinh:</div>
                <div class="col-lg-8">{{item.ten_hoc_sinh_cho}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-2"> Khoá học: </div>
                <div class="col-lg-2">{{item.khoa_hoc_hs_cho}}</div>
                <div class="col-lg-2"> Ngày giờ học: </div>
                <div class="col-lg-2">{{item.ngay_gio_hoc}}</div>
                <div class="col-lg-2">Phòng: </div>
                <div class="col-lg-2"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thời gian: Từ ngày</div>
                <div class="col-lg-3"></div>
                <div class="col-lg-2">Đến:</div>
                <div class="col-lg-3"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Số buổi đã học:</div>
                <div class="col-lg-3">{{item.so_buoi_da_hoc}}</div>
                <div class="col-lg-3">Số buổi còn lại:</div>
                <div class="col-lg-3">{{item.so_buoi_con_lai}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số học phí còn lại:</div>
                <div class="col-lg-8">{{item.so_hoc_phi_con_lai}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-6">Nay tôi làm đơn này xin chuyển học phí sang cho học sinh:</div>
                <div class="col-lg-6">{{item.hoc_sinh_nhan}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-2"> Khoá học: </div>
                <div class="col-lg-2">{{item.khoa_hoc_hs_nhan}}</div>
                <div class="col-lg-2"> Ngày giờ học: </div>
                <div class="col-lg-2">{{item.ngay_gio_hoc}}</div>
                <div class="col-lg-2">Phòng: </div>
                <div class="col-lg-2"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Thời gian: Từ ngày</div>
                <div class="col-lg-3"></div>
                <div class="col-lg-2">Đến:</div>
                <div class="col-lg-3"></div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Số buổi chuyển</div>
                <div class="col-lg-8">{{item.so_buoi_chuyen}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-3">Lý do:</div>
                <div class="col-lg-9">{{item.ly_do}}</div>
              </div>
              <div class="col-lg-12 field line" style="font-style: italic">
                Đề nghị CMSxem xét và tạo điều kiện cho tôi. Tôi cam đoan sẽ không khiếu nại gì sau này.<br />
                Xin chân thành cảm ơn!
              </div>
              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="col-lg-12 line">
                <div class="row p-rela">
                  <div class="col-lg-2 signature-pdf border-l-r"> Phòng kế toán<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                  <div class="col-lg-3 signature-pdf border-l-r"> Giám đốc Trung tâm<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                  <div class="col-lg-3 signature-pdf border-l-r"> Phòng CSKH<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                  <div class="col-lg-2 signature-pdf">Phụ huynh học sinh<br /><span>(Ký và ghi rõ họ tên)</span><p>Ngày...../...../.....</p></div>
                </div>
              </div>
              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="col-lg-12 field line height bold amount-detail font-italic">
                <div class="col-lg-1">Lưu ý:</div>
                <div class="col-lg-11 float-right font-normal">1. Đơn chuyển phí sẽ được xét duyệt và trả lời trong vòng 3 ngày làm việc.</div>
                <div class="col-lg-11 float-right font-normal">2. Phòng CSKH lưu 1 bản. Phòng kế toán lưu 1 bản.</div>
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
      }
    },
    components: {
      abtn
    },
    created() { },
    methods: {

      callPrintForm() {
        this.print('tuition_fee_transfer', 'Đơn Chuyển Học Phí')
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
