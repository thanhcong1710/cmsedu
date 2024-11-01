<template>
    <div class="wrapper">
        <div class="animated fadeIn">
            <b-row>
                <b-col cols="12">
                    <b-card
                            header-tag="header"
                            footer-tag="footer">
                        <div slot="header">
                            <strong>BC16 - Bảng đo hiệu suất Apax</strong>
                            <div class="card-actions">
                                <a href="skype:thanhcong1710?chat" target="_blank">
                                    <small className="text-muted"><i class="fa fa-skype"></i></small>
                                </a>
                            </div>
                        </div>
                        <div class="content-detail">
                            <div class="row">
                                <h4 class="text-center title-heading">BẢNG ĐO HIỆU SUẤT APAX ENGLISH</h4>
                            </div>
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-8">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-4"><strong>Ngày</strong></div>
                                                <div class="col-8">
                                                    <calendar
                                                            class="form-control calendar"
                                                            :value="from_date"
                                                            :transfer="true"
                                                            :format="html.calendar.options.formatSelectDate"
                                                            :disabled-days-of-week="html.calendar.options.disabledDaysOfWeek"
                                                            :clear-button="html.calendar.options.clearSelectedDate"
                                                            :placeholder="html.calendar.options.placeholderSelectDate"
                                                            :pane="1"
                                                            :disabled="html.calendar.disabled"
                                                            :onDrawDate="onDrawDate"
                                                            :lang="html.calendar.lang"
                                                            @input="selectDate"
                                                    ></calendar>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2"></div>
                            </div>
                            <div class="row">
                                <div class="print-btn-group">
                                    <button class="btn btn-success button-print" @click="printReportBC16"><i
                                            class="fa fa-print"> &nbsp;Xuất báo cáo</i></button>
                                    <router-link class="btn btn-warning btn-back" :to="'/forms'">Quay lại</router-link>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import u from '../../utilities/utility'
    import Datepicker from 'vue2-datepicker'
    import moment from 'moment'
    import calendar from 'vue2-datepicker'


    export default {
        name: 'Report16',
        components: {
            Datepicker,
            calendar
        },
        data() {
            return {
                highlighted: {},
                lang: 'en',
                from_date: '',
                to_date: '',
                html: {
                    calendar: {
                        disabled: false,
                        options: {
                            formatSelectDate: 'YYYY-MM-DD',
                            disabledDaysOfWeek: [],
                            clearSelectedDate: true,
                            placeholderSelectDate: 'Chọn ngày bắt đầu',
                        },
                        lang: {
                            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                            months: [
                            "Tháng 1",
                            "Tháng 2",
                            "Tháng 3",
                            "Tháng 4",
                            "Tháng 5",
                            "Tháng 6",
                            "Tháng 7",
                            "Tháng 8",
                            "Tháng 9",
                            "Tháng 10",
                            "Tháng 11",
                            "Tháng 12"
                            ],
                            pickers: ["", "", "7 ngày trước", "30 ngày trước"]
                        }
                    },
                }
            }
        },
        created() {

        },
        methods: {
            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD');
            },
            printReportBC16() {
                let data = {
                    date: this.from_date,
                    tk: u.token()
                };

                let data_string = JSON.stringify(data);

                let p = `/api/exel/print-report-bc16/${data_string}`;
                window.open(p, '_blank');
            },
            selectDate(date) {
                this.from_date = date;
            },
            onDrawDate() {

            }

        }
    }
</script>

<style scoped>
    .btn-print {
        width: 60px;
        margin-left: -16px;
    }

    .mt-30 {
        margin-top: 30px;
    }

    .selected-button {
        margin: auto;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .title-heading {
        margin: auto;
        margin-top: 20px;
        margin-bottom: 50px;
    }

    .button-print {
        margin-left: 492px;
        margin-top: 50px;
    }

    .print-btn-group {
        text-align: center;
    }

    .time-picker {
        height: 50px;
    }

    .btn-back {
        margin-top: 50px;
    }
</style>