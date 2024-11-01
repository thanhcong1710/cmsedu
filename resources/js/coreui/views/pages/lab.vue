<!--
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 *  Apax ERP System
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */-->

<template>
    <div class="app flex-row align-items-center apax-html">
        <div class="container">
            <b-card>
                <h1>Laboratory</h1>
                <div class="content apax-form">
                    <div class="col-md-12 pad-no">
                        <selectBranch
                                :id="html.search.id"
                                :cls="html.search.class"
                                :options="html.search.options"
                                :disabled="html.search.disabled"
                                :placeholder="html.search.placehloder"
                                @change="doSelect"
                        >
                        </selectBranch>
                        <br/>
                        <br/>
                    </div>
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group price-must-charge">
                                    <label class="control-label">Miền:  (Miền Bắc - 1; Miền Nam - 2)</label>
                                    <input class="form-control" type="text" v-model="zoneid" @change="calcSes"/>
                                </div>
                                <div class="form-group price-must-charge">
                                    <label class="control-label">ID Sản phẩm</label>
                                    <input class="form-control" type="text" v-model="productid" @change="calcSes"/>
                                </div>
                                <div class="form-group price-must-charge">
                                    <label class="control-label">Ngày bắt đầu học</label>
                                    <DatePicker v-model="start" :lang="lang" @change="calcSes" class="time-picker"></DatePicker>                                    
                                </div>
                                <div class="form-group price-must-charge">
                                    <label class="control-label">Số buổi được học để tính ra ngày kết thúc</label>
                                    <input class="form-control" type="text" v-model="sessions" @change="calcSes"/>
                                </div>
                                <div class="form-group">
                                    <select @change="prepare" v-model="useholidays">
                                        <option value="1">Có tính public holidays
                                        </option>
                                        <option value="0">Không tính public holidays
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="filter-label control-label">Chi tiết các ngày nghỉ lễ</label>
                                    <ul>
                                        <li v-for="(item, index) in list" :key="index">
                                            {{ `${item.start_date} - ${item.end_date}` }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group price-must-charge">
                                    <label class="control-label">Buổi học</label>
                                    <input class="form-control" type="text" v-model="classdates" @change="load"/>
                                </div>
                                <div class="form-group">
                                    <label class="filter-label control-label">Số buổi học được tính ra
                                        là</label>
                                    <input class="form-control" type="text" v-model="total"/>
                                </div>
                                <div class="form-group">
                                    <label class="filter-label control-label">Chọn ngày học cuối để tính số
                                        buổi học</label>
                                    <DatePicker v-model="end" :lang="lang" @change="prepare" class="time-picker"></DatePicker>
                                </div>
                                <div class="form-group">
                                    <label class="filter-label control-label">Ngày học cuối được tính ra là
                                        ngày</label>
                                    <input class="form-control" type="text" v-model="end"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="filter-label control-label">Chi tiết các buổi học tính
                                        theo hàm ngày học cuối</label>
                                    <ul>
                                        <li v-for="(date1, index) in dates1" :key="index">
                                            {{ date1 }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label class="filter-label control-label">Chi tiết các buổi học tính
                                        theo hàm tìm số buổi</label>
                                    <ul>
                                        <li v-for="(date2, index) in dates2" :key="index">
                                            {{ date2 }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-card>
            <b-card>
                <h1>Test Chuyển Phí</h1>
                <div class="content apax-form">
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">ID Gói Phí Chuyển</label>
                                    <input class="form-control" type="text" v-model="tftfid" @change="calcTransfer"/>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Tiền Chuyển</label>
                                    <input class="form-control" type="text" v-model="tfamnt" @change="calcTransfer"/>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Số Buổi Chuyển</label>
                                    <input class="form-control" type="text" v-model="tfsess" @change="calcTransfer"/>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">ID Trung Tâm Nhận</label>
                                    <input class="form-control" type="text" v-model="brchid" @change="calcTransfer"/>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">ID Sản Phẩm Nhận</label>
                                    <input class="form-control" type="text" v-model="prodid" @change="calcTransfer"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pad-no">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Thông Tin Gói Phí Chuyển</label>
                                    <div class="detail-info">
                                        Tên: {{ tftfif.name }}<br/>
                                        ID Gói Phí: {{ tftfif.id }}<br/>
                                        Product: {{ tftfif.product_name }}<br/>
                                        Số Buổi: {{ tftfif.session }}<br/>
                                        Giá Gốc: {{ tftfif.price | formatMoney }}<br/>
                                        Triết Khấu: {{ tftfif.discount | formatMoney }}<br/>
                                        Thực Thu: {{ (tftfif.price - tftfif.discount) | formatMoney }}<br/>
                                        Branch IDs: {{ tftfif.branch_id }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Thông Tin Gói Phí Nhận</label>
                                    <div class="detail-info">
                                        Tên: {{ rctfif.name }}<br/>
                                        ID Gói Phí: {{ rctfif.id }}<br/>
                                        Product: {{ rctfif.product_name }}<br/>
                                        Số Buổi: {{ rctfif.session }}<br/>
                                        Giá Gốc: {{ rctfif.price | formatMoney }}<br/>
                                        Triết Khấu: {{ rctfif.discount | formatMoney }}<br/>
                                        Thực Thu: {{ (rctfif.price - rctfif.discount) | formatMoney }}<br/>
                                        Branch IDs: {{ rctfif.branch_id }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Tiền Đã Chuyển</label>
                                    <div class="detail-info">
                                        {{ tfamnt | formatMoney }}
                                    </div>
                                    <label class="control-label">Đơn Giá Gói Phí Nhận</label>
                                    <div class="detail-info">
                                        {{ sptfif | formatMoney }}
                                    </div>
                                    <label class="control-label">Kết Quả Tính Toán</label>
                                    <div v-if="specia === 0" class="detail-info">
                                        {{ tfamnt | formatMoney }} / {{ sptfif | formatMoney }} = {{
                                        Math.round(tfamnt/sptfif, 2) }}
                                    </div>
                                    <div v-if="specia === 1" class="detail-info">
                                        Chuyển ngang buổi từ HN - HCM gói April: {{ sstfif }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Số Buổi Chuyển Được</label>
                                    <div class="detail-info">
                                        {{ sstfif }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-card>
<!--            <b-card>-->
<!--                <div class="col-sm-4"></div>-->
<!--                <div class="col-sm-4">-->
<!--                    <h3>Withdraw Học Sinh</h3>-->
<!--                    <button class="btn btn-success" @click="withDraw">Withdraw Học Sinh</button>-->
<!--                </div>-->
<!--                <div class="col-sm-4"></div>-->
<!--            </b-card>-->
            <b-card>
                <div class="col-sm-12">
                    <h3>Tính ngày học cuối</h3>
                    <div class="form-group">
                        <input class="form-control" placeholder="Nhập id trung tâm" v-model="calculator.branch_id"/>
                    </div>
                    <div class="form-group">
                        <span class="text-info">{{calculator.done}}/{{calculator.total}}</span>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" id="process"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success" @click="recalculate">Start</button>
                    </div>
                </div>
            </b-card>
        </div>
    </div>
</template>


<script>
    import DatePicker from 'vue2-datepicker'
    import u from '../../utilities/utility'
    import selectBranch from '../../components/Selection'

    export default {
        name: 'Laboratory',
        components: {
            selectBranch,
            DatePicker
        },
        data() {
            return {
                highlighted: {
                    // days: [6, 0] // Highlight Saturday's and Sunday's
                },
                zoneid: 1,
                tftfid: 0,
                tfamnt: 0,
                brchid: 0,
                prodid: 0,
                tftfif: '',
                rctfif: '',
                sptfif: 0,
                sstfif: 0,
                tfsess: 0,
                lang: 'en',
                item: '',
                list: [],
                date1: '',
                dates1: [],
                date2: '',
                dates2: [],
                total: '',
                classid: 1,
                productid: 1,
                holidays: [],
                classdays: [],
                useholidays: 1,
                classdates: '1',
                start: this.moment().format('YYYY-MM-DD'),
                begin: '',
                end: '',
                specia: 0,
                sessions: 3,
                html: {
                    search: {
                        id: 'id-of-select-box',
                        class: 'select-branch-box',
                        options: [],
                        disabled: false,
                        placehloder: 'Vui lòng chọn 1 trung tâm trước'
                    }
                },
                calculator: {
                    branch_id: 0,
                    students: [],
                    index: 0,
                    ratio: 0,
                    total: 0,
                    done: 0,
                }
            }
        },
        created() {
            this.load()
            const start = '2019-02-09'
            const begin = '2019-02-09'
            const end = '2019-06-19'
            const holidays = [{"start_date":"2018-04-25","end_date":"2018-04-25"},{"start_date":"2018-04-28","end_date":"2018-04-28"},{"start_date":"2018-04-29","end_date":"2018-04-29"},{"start_date":"2018-04-30","end_date":"2018-04-30"},{"start_date":"2018-05-01","end_date":"2018-05-02"},{"start_date":"2018-06-03","end_date":"2018-06-03"}]
            const class_days = [2]
            const sessions = 36
            const x = u.calEndDate(sessions, class_days, holidays, start)
            const z = u.calSessions(begin, end, holidays, class_days)
            u.log('Holidays', holidays)
            u.log('Result last date: ', x)
            u.log('Result done sess: ', z)
        },
        watch: {},
        computed: {},
        methods: {
            load() {
                u.g(`/api/settings/holidays/v2/${this.zoneid}/${this.productid}`)
                    .then(response => {
                        const data = response
                        if (parseInt(this.useholidays) === 1) {
                            this.list = data.holidays
                        } else {
                            this.list = []
                        }
                        const buff = this.classdates.split(',')
                        if (buff.length) {
                            this.classdays = []                            
                            buff.map(item => this.classdays.push(parseInt(item, 10)))
                        }
                        this.calcSes()
                    }).catch(e => console.log(e));
            },
            calcTransfer() {
                if (this.tftfid && this.tfamnt && this.brchid && this.prodid) {
                    const params = {
                        tftfid: this.tftfid,
                        tfamnt: this.tfamnt,
                        brchid: this.brchid,
                        prodid: this.prodid,
                        tfsess: this.tfsess
                    }
                    u.g(`/api/scope/test/transfer/${JSON.stringify(params)}`)
                        .then(response => {
                            u.log('response', response)
                            this.tftfif = response.transfer_tuition_fee
                            this.rctfif = response.receive_tuition_fee
                            this.sptfif = response.single_price
                            this.sstfif = response.sessions
                            this.specia = response.special
                        }).catch(e => console.log(e))
                }
            },
            calcSes() {
                let x = null                
                if (parseInt(this.useholidays) === 1) {
                    this.holidays = this.list                    
                    x = u.calEndDate(this.sessions, this.classdays, this.holidays, this.start)
                    u.log('ZZZZZ Result last date: ', x)
                } else {
                    this.holidays = []                    
                    x = u.calEndDate(this.sessions, this.classdays, [], this.start)
                    u.log('XXXXX Result last date: ', x)
                }
                this.end = x.end_date
                this.dates1 = x.dates
            },
            prepare() {
                this.begin = this.start
                let z = null
                if (parseInt(this.useholidays) === 1) {
                    this.holidays = this.list
                    z = u.calSessions(this.begin, this.end, this.holidays, this.classdays)
                    u.log('AAAAA Result done sess: ', z)
                } else {
                    this.holidays = []
                    z = u.calSessions(this.begin, this.end, [], this.classdays)
                    u.log('BBBBB Result done sess: ', z)
                }
                this.dates2 = z.dates
                this.total = z.total
            },
            doSelect(val) {
                u.log('Select', val)
                this.load();
            },
            withDraw() {
                var x = confirm("Bạn có chắc withdraw ?");

                if (x)
                    u.a().get(`/api/daily-checking-withdraw-status`).then(response => {
                        console.log(response);
                    })
                else
                    return false;
            },
            recalculate(){
                const url = `/api/students/count-students/${this.calculator.branch_id}`;
                u.a().get(url)
                    .then(
                        response => {
                            this.calculator.done = 0;
                            let students = response.data.data;
                            if(students.length){
                                this.calculator.total = students.length;
                                this.calculator.students = students;

                                if(this.calculator.total < 10){
                                    this.thread(0);
                                }else{
                                    for(let i = 0; i < 10; i++){
                                        this.thread(i);
                                    }
                                }
                            }
                        }
                    )
                    .catch(e => {
                    })
            },
            thread(index){
                if(index < this.calculator.total){
                    this.calculator.index += 1;
                    let url = `/api/services/update-learning-time/${this.calculator.students[index].id}/${this.calculator.branch_id}`;
                    u.a().get(url)
                        .then(response => {
                            this.createNewThread();
                        })
                        .catch(e => {
                            this.createNewThread();
                        })
                }else{
                    return true;
                }
            },
            createNewThread(){
                this.calculator.done += 1;
                this.calculator.ratio = (this.calculator.index + 1) * 100 / this.calculator.total;
                $("#process").css({'width':`${this.calculator.ratio}%`});
                this.thread(this.calculator.index);
            }
        }
    }
</script>

<style scoped language="scss">
    .time-picker {
        width: 300px;
        height: 80px;
    }
    #process{
        width: 0;
    }
</style>