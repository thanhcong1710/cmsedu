<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card header-tag="header" footer-tag="footer">
                        <div slot="header">
                            <i class="fa fa-align-justify"></i> <strong>DANH SÁCH BÁO CÁO</strong>
                        </div>
                        <div class="content-detail">
                            <p class="bc-font" v-if="showBC01a1 == true">
                              <router-link to="/reports/1a1" >01. BÁO CÁO CHI TIẾT HỌC SINH FULL FEE ACTIVE / STUDENTS REPORT OF CMS VIETNAM</router-link>
                            </p>
                            <p class="bc-font" v-if="showBC01b1 == true">
                                <span style="cursor:no-drop">02. BÁO CÁO HỌC SINH TÁI PHÍ</span><br>
                            </p>
                            <p class="bc-font" v-if="showBC01b1 == true">
                                <router-link :to="'/reports/1b-1'" class="report-1a">- BÁO CÁO CHI TIẾT</router-link>
                                <br>
                            </p>
                            <p class="bc-font" v-if="showBC01b2 == true">
                                <router-link :to="'/reports/1b-2'" class="report-1a">- BÁO CÁO TỔNG HỢP</router-link>
                                <br>
                            </p>
                            <p class="bc-font" v-if="showBC01b3 == true">
                                <router-link :to="'/reports/1b-3'" class="report-1a">- BÁO CÁO THEO EC</router-link>
                                <br>
                            </p>
                            <p class="bc-font" v-if="showBC02a == true">
                              <router-link to="/reports/2a" >03. BÁO CÁO CHI TIẾT HỌC SINH PENDING</router-link>
                            </p>
                            <p class="bc-font" v-if="showBC02b == true">
                              <router-link to="/reports/2b" >04. BÁO CÁO CHI TIẾT HỌC SINH BẢO LƯU</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr01 == true">
                                <router-link to="/reports/r01" >05. BÁO CÁO KHÁCH HUB LÊN GÓI PHÍ TẠI TRUNG TÂM</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr02 == true">
                                <router-link to="/reports/r02" >06. BÁO CÁO KHÁCH MARKETING LÊN GÓI PHÍ TẠI TRUNG TÂM</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr04 == true">
                                <router-link to="/reports/r04" >08. BÁO CÁO HỌC SINH MỚI</router-link>
                            </p>
                             <p class="bc-font" v-if="showBCr05 == true">
                                <router-link to="/reports/r05" >09. BÁO CÁO DOANH THU</router-link>
                            </p>
                             <p class="bc-font" v-if="showBCr06 == true">
                                <router-link to="/reports/r06" >10. BÁO CÁO DOANH THU THEO NGUỒN</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr07 == true">
                                <router-link to="/reports/r07" >11. BÁO CÁO TÌNH HÌNH HỌC SINH UCREA</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr08 == true">
                                <router-link to="/reports/r08" >12. BÁO CÁO TÌNH HÌNH HỌC SINH BRIGHT IG - BLACK HOLE</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr09 == true">
                                <router-link to="/reports/r09" >13. BÁO CÁO HỌC SINH CHECKIN - PHỤ HUYNH SHOWUP</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr10 == true">
                                <router-link to="/reports/r10" >14. BÁO CÁO HỌC SINH ĐANG HỌC DEMO</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr11 == true">
                                <router-link to="/reports/r11" >15. BÁO CÁO HỌC SINH ĐÃ HOC DEMO</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr12 == true">
                                <router-link to="/reports/r12" >16. BÁO CÁO CONFIRM HỌC SINH CHI TIẾT</router-link>
                            </p>
                            <p class="bc-font" v-if="showBCr13 == true">
                                <router-link to="/reports/r13" >17. BÁO CÁO CONFIRM</router-link>
                            </p>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import u from '../../utilities/utility'

    export default {
        name: 'Form-Report',
        components: {},
        data() {
            return {
                showBC01a1: true,
                showBC01b1: false,
                showBC01b2: false,
                showBC01b3: false,
                showBC02a:false,
                showBC02b: false,
                showBCr01: false,
                showBCr04: false,
                showBCr05: false,
                showBCr06: false,
                showBCr07: false,
                showBCr08: false,
                showBCr09:false,
                showBCr10:false,
                showBCr11:false,
                showBCr12: false,
                showBCr13: false,
            }
        },
        created() {
            var x = u.session().user.code.split("-")
            this.user_id = x[0];
            this.role_id = u.session().user.role_id
            this.checkRole()
        },
        computed: {},
        methods: {
            checkRole() {
                const role = this.role_id
                if(role === 686868){
                    this.showBC17a = false;
                    this.showBC17b = false;
                }else if(role === 56){
                    this.showBC14 = false;
                    this.showBC17a = false;
                    this.showBC17b = false;
                }else if(role == '999999999' || role == '86868686'){
                    this.showBC02a= true
                    this.showBC02b= true
                }
                if([101,102,68, 69, 55, 56, 686868, 85858585, 7676767, 84 ,'999999999','86868686'].indexOf(role)!= -1){
                    this.showBC01b1= true
                    this.showBC01b2= true
                    this.showBC01b3= true
                }
                if([55, 56,85858585,'999999999','86868686'].indexOf(role)!= -1){
                    this.showBC02a= true
                    this.showBC02b= true
                }
                if([101,80,81].indexOf(role)!= -1){
                    this.showBC01a1= false
                }
                if(['999999999',7676767,80,'86868686'].indexOf(role)!= -1){
                    this.showBCr01 = true
                }
                if([68,69,56,55,57,84,1200,676767,686868, 7676767,'999999999','86868686'].indexOf(role)!= -1){
                    this.showBCr02 = true
                }
                if([102].indexOf(role)!= -1){
                    this.showBC02a= true
                }
                if([36,37,68,69,56,55,57,84,101,102,676767,686868, 7676767,7777777,'999999999','86868686'].indexOf(role)!= -1){
                    this.showBCr04 = true
                }
                 if([84,68,69,7676767,686868,676767,7777777,'999999999','86868686'].indexOf(role)!= -1){
                    this.showBCr05 = true
                }
                if(['999999999',1300,81,84,'86868686'].indexOf(role)!= -1){
                    this.showBCr06 = true
                }
                if(['999999999',36,37,'86868686'].indexOf(role)!= -1){
                    this.showBCr07 = true
                }
                if(['999999999',36,37,'86868686'].indexOf(role)!= -1){
                    this.showBCr08 = true
                }
                if([81, 68, 69, 676767, 686868,7676767, '999999999','86868686'].indexOf(role)!= -1){
                    this.showBCr09 = true
                }
                if([55,56,36,37,676767, 686868, 7676767,7777777, '999999999'].indexOf(role)!= -1){
                    this.showBCr10 = true
                }
                if([ 55,56,36,37,676767, 686868, 7676767,7777777, '999999999'].indexOf(role)!= -1){
                    this.showBCr11 = true
                }
                if([102].indexOf(role)!= -1){
                    this.showBC01a1= true
                    this.showBC01b1= true
                    this.showBC01b2= true
                    this.showBC01b3= true
                    this.showBC02a= true
                    this.showBC02b= true
                    this.showBCr01= true
                    this.showBCr04= true
                    this.showBCr05= true
                    this.showBCr06= true
                    this.showBCr07= true
                    this.showBCr08= true
                    this.showBCr09= true
                    this.showBCr10= true
                    this.showBCr11= true
                }
                if([55,56,676767, 686868, 7676767,7777777,'999999999'].indexOf(role)!= -1){
                    this.showBCr12 = true
                }
                if([55,56,676767, 686868,85858585, 7676767,7777777,'999999999'].indexOf(role)!= -1){
                    this.showBCr13 = true
                }
            },
        }
    }
</script>

<style scoped lang="scss">
    .content-detail {
        border: 1px solid grey;
        padding: 20px;
        p {
            border-bottom: 1px dashed grey;
            margin-buttom: 5px;
            color: #9c0200;
        }
        p span {
            cursor: pointer;
        }
        p span:hover {
            color: #350006;
        }

    }

    .report-1a {
        margin-left: 25px;
    }

    .report-title {
        margin-left: 150px;
    }

    .report {
        margin-bottom: 10px;
    }

    .report-16 {
        // margin: auto;
        font-size: 15px;
    }

    .heading-title {
        margin: auto;
        padding: 50px;
    }

    .btn-export {
        margin-left: 300px;
        margin-top: 100px;
    }

    .bc17 {
        margin-left: 62px;
    }

    .bc20_detail {
        margin-left: 65px;
    }

    .bc-font{
        font-size: 12px;
        color: red;
    }


</style>
