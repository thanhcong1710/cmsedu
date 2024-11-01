<template>
  <div id="apax-printing-detail">
    <div>
      <abtn id="print-button"
            :markup="success"
            :onClick="callPrintForm"
            >In Giấy Tái Phí</abtn>
    </div>
    <div id="apax-printing-recycle">
      <div class="print portrait A4">
        <div class="sheet recycle">
          <div class="detail">
            <div class="col-lg-4">
              <img class="im-logo" src="/static/img/images/big_logo.png" />
            </div>
            <div class="col-lg-8">
              <div class="row">
                <div class="col-lg-3">&nbsp;</div>
                <div class="col-lg-9">
                  <div class="col-lg-12">
                    <img class="im-sm" src="/static/img/images/big_logo.png" />
                  </div>
                  <div class="col-lg-12 info-apax">
                    <div class="row">
                      <div class="col-lg-12">
                        Trụ sở chính: Tầng 7, tầng 10 toà nhà 14 Láng Hạ, Ba Đình, Hà Nội
                      </div>
                      <div class="col-lg-12">
                        Tel: 0438 318 318 - Fax: 0438 328 328
                      </div>
                      <div class="col-lg-12">
                        Website: ApaxEnglish.com - Fanpage: facebook.com/ApaxEnglish
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 align-center doc-title">
              <h2>HỒ SƠ TÁI TỤC APAX ENGLISH</h2>
            </div>
            <div class="doc-content">
              <div class="clearfix"></div>
              <div class="col-lg-12 sub-title">
                <h4>THÔNG TIN HỌC VIÊN</h4>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Họ tên học sinh:</div>
                <div class="col-lg-3">{{ student.name }}</div>
                <div class="col-lg-2">Nickname:</div>
                <div class="col-lg-3">{{ student.nick }}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Lớp học hiện tại:</div>
                <div class="col-lg-8">{{ student.school }}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4">Họ và tên bố/mẹ:</div>
                <div class="col-lg-8">{{student.parent_name}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-lg-4"> Di động:</div>
                <div class="col-lg-3">{{student.tel}}</div>
                <div class="col-lg-2">Email:</div>
                <div class="col-lg-3">{{student.email}}</div>
              </div>
              <div class="col-lg-12 field line">
                <div class="row">
                  <div class="col-lg-4">
                    Phương thức liên lạc:
                  </div>
                  <div class="col-lg-4">
                    Điện thoại: &nbsp;&nbsp;<i class="square"></i>
                  </div>
                  <div class="col-lg-4">
                    Email: &nbsp;&nbsp;<i class="square"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="col-lg-12 sub-title">
                <h4>THÔNG TIN TÁI TỤC</h4>
              </div>
              <div class="col-lg-12">
                &nbsp;
              </div>
              <div class="">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="table-header">
                      <th width="12%">
                        <p>Cấp độ</p>
                      </th>
                      <th width="17%">
                        <p>Thời gian hết phí</p>
                      </th>
                      <th width="17%">
                        <p>Thời gian tái phí</p>
                      </th>
                      <th width="16%">
                        <p>Số hoá đơn</p>
                      </th>
                      <th width="17%">
                        <p>Số phí đã đóng</p>
                      </th>
                      <th width="25%">
                        <p>Ghi chú</p>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="table-content" v-for="(item, ind) in recycleInfo" v-bind:key="ind">
                      <td>{{item.level}}</td>
                      <td>{{item.timeoutFee}}</td>
                      <td>{{item.timeRecycle}}</td>
                      <td>{{item.billNumber}}</td>
                      <td>{{item.feePaid}}</td>
                      <td>{{item.notes}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-lg-12 field line height">
                Tôi, {{customerName}}, đồng ý cho CMSsử dụng thông tin và hình ảnh của con tôi cho các tài liệu truyền thông với điều kiện CMSsẽ không gây bất cứ ảnh hưởng nào cho bé.
              </div>

              <div class="dv-white-space"></div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-4">&nbsp;</div>
                  <div class="col-lg-4">&nbsp;</div>
                  <div class="col-lg-4">Hà Nội, ngày {{getDate('d')}} tháng {{getDate('m')}} năm {{getDate('y')}}</div>
                </div>
              </div>
              <div class="col-lg-12 line">
                <div class="row p-rela">
                  <div class="col-lg-3 signature-pdf border-l-r align-center"> Xác nhận của CSKH </div>
                  <div class="col-lg-4 signature-pdf border-l-r align-center"> Xác nhận của kế toán </div>
                  <div class="col-lg-4 signature-pdf align-center">Phụ huynh học sinh<br /><span>(Ký và ghi rõ họ tên)</span></div>
                </div>
              </div>
              <div class="dv-white-space"></div>
              <div class="dv-white-space"></div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-4">&nbsp;</div>
                  <div class="col-lg-4">&nbsp;</div>
                  <div class="col-lg-4 im-bold">Xác nhận của GĐ Trung tâm</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <abtn id="print-buttonx"
        :markup="success"
        :onClick="printForm"
        >In Giấy Nhập Học</abtn>
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
    props: ['student', 'contract'],
    data() {
      return {
        success: 'success',
        item: ''
      }
    },
    components: {
      abtn
    },
    created() { },
    methods: {
      printForm(){
        u.print('recycle', 'Giấy Nhập Học')
      },
      callPrintForm() {
        this.print('recycle', 'Đơn Tái Phí')
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
      infor (){
        if (this.infor.student_name) {
          setTimeout(() => this.printForm(), 300)
        }
      },
      customerName (){
        if (this.customerName) {
          setTimeout(() => this.printForm(), 300)
        }
      }
    }
  }
</script>
