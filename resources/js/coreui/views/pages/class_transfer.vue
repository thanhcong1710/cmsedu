<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-class-tran">
            <div class="print-box">
                <div class="container">
                    <div class="print-body">
                        <div class="inner">
                            <div v-show="flags.form_loading" class="ajax-load content-loading">
                                <div class="load-wrapper">
                                    <div class="loader"></div>
                                    <div v-show="flags.form_loading" class="loading-text cssload-loader">Đang tải dữ liệu...
                                    </div>
                                </div>
                            </div>
                            <div class="print-header">
                                <div class="h-left">
                                    <span class="logo"><img src="/images/print-logo.png" /></span>
                                </div>
                                <div class="h-right">
                                   <h4>CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ CMS</h4>
                                    <p><i class="fa fa-map-marker"></i> Tầng 4, 21T2 Hapulico Complex, 01 Nguyễn Huy Tưởng, Phường Thanh Xuân Trung, Quận Thanh Xuân, Thành phố Hà Nội, Việt Nam</p>
                                    <!-- <p><i class="fa fa-phone"></i>(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope"></i> cms@cmsedu.vn &nbsp;&nbsp;<i class="fa fa-globe"></i> www.cmsedu.vn</p> -->
                                </div>
                            </div>
                            <div class="print-content">
                                <h2 class="title">PHIẾU XÁC NHẬN CHUYỂN LỚP HỌC</h2>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Họ & tên học sinh: {{item.name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Ngày sinh: {{item.date_of_birth}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Chương trình học: {{item.product_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lớp chuyển đi: {{item.from_class_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã học: {{item.done_sessions}} buổi</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-line">
                                            <span class="label">Số buổi còn lại: {{item.session_left}} buổi</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lớp chuyển đến: {{item.to_class_name}}</span>
                                            <i class="doted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-bottom:30px;"></div>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6">
                                        <span>......, ngày</span><i class="dot-line">........</i><span>tháng</span><i class="dot-line">.........</i><span>năm</span><i class="dot-line">...........</i>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 center">
                                        <strong>Phụ huynh Học sinh</strong>
                                        <span>(Ký và ghi rõ họ tên)</span>
                                    </div>
                                    <div class="col-sm-6 center">
                                        <strong>Chăm sóc Khách hàng</strong>
                                        <span>(Ký và ghi rõ họ tên)</span>
                                    </div>
                                </div>
                                <div class="margin150"></div>
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
        name: 'class_transfer',
      data() {
        return {
          item: {},
          flags: {
            form_loading: false
          }
        }
      },
      created() {
        this.getPrintData();
      },
      methods: {
        getPrintData() {
          this.flags.form_loading = true;
          u.a().get(`/api/class-transfers/print/${this.$route.params.id}`)
            .then(resp => {
              this.flags.form_loading = false;
              this.item = resp.data.data;
              setTimeout(function () {
                window.print();
              }, 500)
            })
        }
      }
    }
</script>
