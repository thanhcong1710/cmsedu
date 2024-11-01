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
                                   <h4>CÔNG TY CỔ PHẦN GIÁO DỤC TƯ DUY VÀ SÁNG TẠO QUỐC TẾ CMS</h4>
                                    <p><i class="fa fa-map-marker"></i> Tầng 4, 21T2 Hapulico Complex, 01 Nguyễn Huy Tưởng, Phường Thanh Xuân Trung, Quận Thanh Xuân, Thành phố Hà Nội, Việt Nam</p>
                                    <!-- <p><i class="fa fa-phone"></i>(+84) 24 7356 8806 &nbsp;&nbsp;<i class="fa fa-envelope"></i> cms@cmsedu.vn &nbsp;&nbsp;<i class="fa fa-globe"></i> www.cmsedu.vn</p> -->
                                </div>
                            </div>
                            <div class="print-content">
                                <h2 class="title">PHIẾU XẾP LỚP</h2>
                                
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="input-line">
                                            <span class="label">Tên học viên:</span>
                                            {{student.name}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Mã học viên:</span>
                                            {{student.crm_id}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Tên phụ huynh:</span>
                                            {{student.parent_name}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Giáo viên chủ nhiệm:</span>
                                            {{sclass.full_name}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Ngày bắt đầu học:</span>
                                            {{contract.enrolment_start_date | formatDate}}
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-line">
                                            <span class="label">Ngày tháng năm:</span>
                                            {{student.date_of_birth | formatDate}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Tên lớp:</span>
                                            {{sclass.cls_name}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Số điện thoại:</span>
                                            {{student.parent_mobile}}
                                        </div>
                                        <div class="input-line">
                                            <span class="label">Khung giờ học:</span>
                                            {{sclass.clse_shift_start + ' - ' + sclass.clse_shift_end}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-line">
                                            <span class="label">Lớp học đăng kí: {{sclass.cls_name}}</span>
                                        </div>
                                    </div>
                                    <div style="padding:20px;width:100%;display:flex">
                                        <div class="col-sm-4">
                                            <div class="box-op">
                                                <div class="input-line">
                                                    <span class="label">Ucrea:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                                <div class="input-line">
                                                    <span class="label">Ucrea:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                                <div class="input-line">
                                                    <span class="label">Ucrea:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="box-op">
                                                <div class="input-line">
                                                    <span class="label">Bright IG:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                                <div class="input-line">
                                                    <span class="label">Bright IG:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                                <div class="input-line">
                                                    <span class="label">Bright IG:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="box-op">
                                                <div class="input-line">
                                                    <span class="label">Black Hole:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                                <div class="input-line">
                                                    <span class="label">Black Hole:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                                <div class="input-line">
                                                    <span class="label">Black Hole:</span>
                                                    <i class="doted"></i>
                                                    <span class="boxcheck"><input type="checkbox" ></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p>Tôi đã được TVV và CSKH tại trung tâm tư vấn về việc xếp lớp cho con. Tôi đã hiểu việc xếp lớp hoàn toàn phù hợp với trình độ của con và tôi đồng ý với việc sắp xếp của trung tâm.</p>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6 text-right">
                                        <span>Hà Nội, ngày</span><i class="dot-line">.........</i><span>tháng</span><i class="dot-line">.........</i><span>năm</span><i class="dot-line">..........</i>
                                    </div>
                                </div>
                                <table class="table table-bordered sign">
                                    <thead>
                                        <tr>
                                            <th><strong>Tư vấn viên</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Chăm sóc khách hàng</strong><i>Ký ghi rõ họ tên</i></th>
                                            <th><strong>Phụ huynh</strong><i>Ký ghi rõ họ tên</i></th>
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
    export default {
        name: 'Enrolment',
        data() {
            return {
                contract: {},
                student: {},
                gender : true,
                sclass : '',
                is_ucrea : false,
                is_bright : false,
                is_blkhole : false
            }
        },
        created() {
            let conId = this.$route.params.id;
            u.g('/api/contracts/'+conId)
                  .then(response => {
                    this.student = response.student;
                    this.contract = response.contracts.length ? response.contracts[0] : [];
                    this.gender = response.student.gender.toLowerCase() == 'm' ? true : false;
                  });
            u.p('/api/classes/bycontract',{conId: conId})
              .then(response => {
                this.sclass = response;
                // console.log(response)
              });
            setTimeout(function () {
                window.print();
            }, 1000)
        },
        methods: {
            getPrintData(){
            }
        }
    }
</script>
