<template>
  <div id="apax-printing-detail">
    <div>
        <abtn id="print-button"
        :markup="success"
        :onClick="printForm"
        >In Giấy Nhập Học</abtn>
    </div>
    <div id="apax-printing-contract">
      <div class="print portrait A4">
        <div class="sheet contract">
          <div class="detail">
            <div class="col-lg-12">
              <div class="col-md-3 float-right note corner top right">
                BM: 04 –TTApax
              </div>
            </div>
            <div class="col-lg-12 align-center doc-title">
              <h2>ĐƠN NHẬP HỌC APAX ENGLISH</h2>
            </div>
            <div class="doc-content">
              <div class="col-lg-12 sub-title">
                <h4>THÔNG TIN HỌC VIÊN</h4>
              </div>
              <div class="col-lg-12 field line">
                <label class="label">Họ và tên:</label> {{infor.student_name}}
              </div>
              <div class="col-md-12 line">
                <div class="col-md-7 field">
                  <label class="label">Ngày sinh:</label> {{formatDate(infor.birthday)}}
                </div>
                <div class="col-md-5 field">
                  <label class="label">Giới tính:</label> {{infor.student_gender === 'F' ? 'Nữ' : 'Nam'}}
                </div>
              </div>
              <div class="col-lg-12 field line">
                Địa chỉ nhà: {{infor.address}}
              </div>
              <div class="col-lg-12 field line">
                Trường học hiện tại: {{infor.school}}
              </div>
              <div class="col-lg-12 line">
                <div class="col-md-7 field">
                  Đã từng học tiếng Anh tại: <i class="dot">{{printLine(50)}}</i>
                </div>
                <div class="col-md-5 field">
                  Trong thời gian: <i class="dot">{{printLine(34)}}</i>
                </div>
              </div>
              <div class="col-lg-12 field line">
                Nguồn biết thông tin: <i class="dot">{{printLine(123)}}</i>
              </div>
              <div class="col-lg-12 empty line">
              </div>
              <div class="col-lg-12 field line">
                Họ và tên bố/mẹ: {{infor.parent_name}}
              </div>
              <div class="col-lg-12 line">
                <div class="col-md-7 field">
                  Di động: {{infor.parent_mobile}}
                </div>
                <div class="col-md-5 field">
                  Email: {{infor.parent_email}}
                </div>
              </div>
              <div class="col-lg-12 field line">
                Facebook: <i class="dot">{{printLine(140)}}</i>
              </div>
              <div class="col-lg-12 field line">
                Một vài lưu ý về học sinh (tính cách, điểm mạnh yếu):
              </div>
              <div class="col-lg-12 field line">
                <i class="dot">{{printLine(156)}}</i>
              </div>
              <div class="col-lg-12 field line">
                <div class="col-md-7 field">
                  Tư vấn viên: {{infor.ec_name}}
                </div>
                <div class="col-md-5 field">
                  Nhóm: {{infor.ec_leader_name}}
                </div>
              </div>
              <div class="col-lg-12 sub-title line">
                <h4>THÔNG TIN KHÓA HỌC</h4>
              </div>
              <div class="col-lg-12 line">
                <table class="table table-striped table-bordered apax-table">
                  <thead>
                    <tr class="table-header">
                      <th width="12%">
                        <p>Khóa học</p><i>(Trình độ)</i></th>
                      <th width="12%">
                        <p>Gói học phí</p><i>(Tháng)</i></th>
                      <th width="15%">
                        <p>Thời gian học</p><i>(Ngày)</i></th>
                      <th width="15%">
                        <p>Nguyên giá</p><i>(Giá gốc)</i></th>
                      <th width="30%">
                        <p>Chương trình khuyến mại</p>
                      </th>
                      <th width="15%">
                        <p>Tổng mức học phí thực đóng</p><i>(Giá sau chiết khấu)</i></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="table-content">
                      <td>{{infor.program_name}}</td>
                      <td>{{infor.tuition_fee_name}}</td>
                      <td>{{formatDate(infor.start_date)}}</td>
                      <td>{{format(parseInt(infor.tuition_fee_price))}}</td>
                      <td v-html="infor.bill_info">{{infor.bill_info}}</td>
                      <td>{{format(parseInt(infor.must_charge, 10))}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-lg-12 field line height bold amount-detail">
                <div class="col-md-4 field">
                  Số tiền đặt cọc: {{format(infor.payment_amount, '-')}}VNĐ
                </div>
                <div class="col-md-8 field">
                  <i>(Số tiền bằng chữ: {{spellNumber(parseInt(infor.payment_amount))}} đồng chẵn./.)</i>
                </div>
              </div>
              <div class="col-lg-12 field line height bold amount-detail">
                Số học phí còn phải hoàn thành: {{format(parseInt(infor.must_charge - infor.payment_amount, 10))}}VNĐ
              </div>
              <div class="col-lg-12 field line height">
                Tôi,{{printLine(30, '_')}}, đồng ý cho CMSsử dụng thông tin và hình ảnh của con tôi cho các tài liệu truyền thông với điều kiện CMSsẽ không gây bất cứ ảnh hưởng nào cho bé.
              </div>
              <div class="col-lg-12">
                <div class="col-md-5 float-right note corner bottom right signature">
                  <div class="today align-center italic">
                    Ngày {{getDate('d')}} tháng {{getDate('m')}} năm {{getDate('y')}}
                  </div>
                  <div class="parent-label align-center bold">
                    Phụ Huynh Học Sinh
                  </div>
                  <div class="parent-note align-center italic">
                    (Ký và ghi rõ họ tên)
                  </div>
                  <div class="parent-signature">
                  </div>
                  <div class="parent-name align-center bold italic">
                    {{infor.parent_name}}
                  </div>
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
        :onClick="printForm"
        >In Giấy Nhập Học</abtn>
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
            student_name: '',
            birthday: '',
            student_gender: '',
            address: '',
            school: '',
            parent_name: '',
            parent_mobile: '',
            parent_email: '',
            program_name: '',
            tuition_fee_name: '',
            start_date: '',
            tuition_fee_price: '',
            bill_info: '',
            must_charge: '',
            payment_amount: ''
          }
        }
      },
      print: ''
    },
    components: {
      abtn
    },
    watch: {
      print (val) {
        if (this.infor.student_name) {
          setTimeout(() => this.printForm(), 300)
        }
      }
    },
    created() {},
    methods: {

      printForm(){
        u.print('contract', 'Giấy Nhập Học')
      },

    formatDate: (inputtime) => inputtime ? moment(inputtime).format('DD/MM/YYYY') : '',
      prepareText: txt => txt && txt.length ? u.sub(txt, 20) : '',
      format: (num, c) => {
        let crc = c ? c === '-' ? '' : c : ''
        return num && num > 1000 ? u.currency(num, crc) : `0${crc}`
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
      spellNumber (num) {
        return spell(num)
      }
    }
  }
</script>
