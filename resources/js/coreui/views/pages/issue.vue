<template>
    <div id="apax-printing-detail">
        <div id="apax-printing-trail_register">
            <div class="print portrait A4 custom issue">
                <div class="sheet trail_register unlimited">
                    <div class="detail">
                        <div class="row">
                            <div class="col-12">
                                <b-card header>
                                    <div id="page-content">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table">
                                                        <thead>
                                                        <tr>
                                                            <th width="50%" class="issue-title">
                                                                <img class="im-logo" src="/static/img/images/icon/apax-logo.png"/>
                                                                <span>Book {{book_name}} Comments</span>
                                                            </th>
                                                            <th width="50%" class="student-name">
                                                                Name: {{issue.student_name}}
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 summary">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="2">CLASS TERM SUMMARY <br> <i>Tổng kết
                                                                khóa</i></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr class="text-xs-left">
                                                            <td width="50%" class="td-text">
                                                                <div class="row">
                                                                    <div class="col-sm-12 text-left">
                                                                        <p>{{issue.trail_report}} <br /><i>({{issue.cm_trail_report}})</i></p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td width="50%">
                                                                <div class="row">
                                                                    <div class="col-sm-12 text-left">
                                                                        <p>{{issue.summary}} <br /><i>({{issue.cm_summary}})</i></p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 support-at-home">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="2">HOW TO SUPPORT YOUR CHILD AT HOME
                                                                <br><i>Cách hỗ trợ học sinh học tại nhà</i></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td width="50%">Score</td>
                                                            <td width="50%">Guideline</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left text-table">1. Đảm bảo con phải
                                                                hoàn thành học E-learning ở nhà hoặc tại trung tâm
                                                            </td>
                                                            <td class="text-left text-table">
                                                                4. Giúp con làm bài tập về nhà phần từ vựng và viết
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left text-table">2. Để cho con đọc to
                                                                truyện phần Gallery
                                                            </td>
                                                            <td class="text-left text-table">5. Nghe các bài hát ABC
                                                                để giúp nhận biết chữ cái và các âm
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left text-table">3. Luyện tập với sách
                                                                Palette (các câu mệnh lệnh)
                                                            </td>
                                                            <td class="text-left text-table">6. Ôn lại từ vựng và
                                                                câu trả lời trong sách Atelier
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 guild">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="3">SCORING GUIDELINES</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td width="10%">Score</td>
                                                            <td width="15%">Guideline</td>
                                                            <td width="75%">Explanation</td>
                                                        </tr>
                                                        <tr v-for="(score, i) in scores" :key="i">
                                                            <td class="text-center text-table">{{score.score}}</td>
                                                            <td class="text-left text-table">{{score.guideline}}
                                                            </td>
                                                            <td class="text-left text-table">{{score.explanation}}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 scores">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table mar-bot-0">
                                                        <thead>
                                                        <tr class="small">
                                                            <th width="17%">Category(<i>Kĩ năng</i>)</th>
                                                            <th width="20%">Sub-Category(<i>Thành phần</i>)</th>
                                                            <th width="53%">Description(<i>Miêu tả</i>)</th>
                                                            <th width="10%"><span>Score / 5</span></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td rowspan="3" class="text-heading">Classroom Duty</td>
                                                            <td class="text-table">
                                                                Attendance <br>
                                                                Điểm danh
                                                            </td>
                                                            <td class="text-left text-table">Student attends class
                                                                regularly. <br>
                                                                <i>Học sinh đi học đều</i>
                                                            </td>
                                                            <td>
                                                                <span class="score">{{issue.scoring_classroom_duty_attendance}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-table">
                                                                Punctuality <br>
                                                                Đúng giờ
                                                            </td>
                                                            <td class="text-left text-table">
                                                                Student arrives to class on time.. <br>
                                                                <i>Học sinh đến lớp đúng giờ</i>
                                                            </td>
                                                            <td>
                                                                <span class="score">{{issue.scoring_classroom_duty_punctuality}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-table">
                                                                Homework <br>
                                                                Bài tập về nhà
                                                            </td>
                                                            <td class="text-left text-table">
                                                                Student completes homework assignments (E-Learning
                                                                and Homework) on time. <br>
                                                                <i>Học sinh làm bài đầy đủ (E-learning và
                                                                    Notebook)</i>
                                                            </td>
                                                            <td>
                                                                <span class="score">{{issue.scoring_classroom_duty_homework}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr class="table-primary">
                                                            <td>Category((<i>Kĩ năng</i>)</td>
                                                            <td>Sub-Category(<i>Thành phần</i>)</td>
                                                            <td>Description(<i>Miêu tả</i>)</td>
                                                            <td><span>Score / 5</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td rowspan="2" class="text-heading">ABC</td>
                                                            <td class="text-table">
                                                                Letter Knowledge <br>
                                                                <i>Nhận biết chữ cái</i>
                                                            </td>
                                                            <td class="text-left text-table">
                                                                Student recognizes letter names, which may include
                                                                consonants and/or vowels. <br>
                                                                <i>Học sinh nhận biết tên chữ cái, bao gồm phụ âm
                                                                    hoặc/và nguyên âm </i>
                                                            </td>
                                                            <td>
                                                                <span class="score">{{issue.scoring_reading_comprehension}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-table">
                                                                Letter-Sound Knowledge <br>
                                                                Nhận biết chữ cái - âm
                                                            </td>
                                                            <td class="text-left text-table">
                                                                Student articulates letter-sounds accurately and
                                                                associates them with the letter name.<br>
                                                                <i>Học sinh đọc chữ cái - âm chính xác và biết cách
                                                                    ghép chúng với tên chữ cái</i>
                                                            </td>
                                                            <td>
                                                                <span class="score">{{issue.scoring_reading_fluency}}</span>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="break-page"></div>
                                                    <table class="table table-bordered apax-table fake-table">
                                                        <thead>
                                                        <tr>
                                                            <th width="17%"></th>
                                                            <th width="20%"></th>
                                                            <th width="53%"></th>
                                                            <th width="10%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td rowspan="2" class="text-heading">Handwriting <p
                                                                        class="vn-text-heading">Viết chữ</p></td>
                                                                <td class="text-table">
                                                                    Fine-Motor Skills <br>
                                                                    Kỹ năng vận động tinh
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student has good vision and hand-eye coordination
                                                                    skills and uses a pencil with confidence. <br>
                                                                    <i>Học sinh có khả năng quan sát, kỹ năng điều phối
                                                                        tay-mắt tốt và sử dụng bút thành thạo</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_writing_sentence_structure}}</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-table">
                                                                    Accurary and Legibility <br>
                                                                    Chính xác và dễ đọc
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student writes letters and words with convention and
                                                                    writing is neat and legible. <br>
                                                                    <i>>Học sinh viết đúng các nét của từ và chữ, chữ
                                                                        viết gọn gàng và dễ đọc</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_writing_creativity}}</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="2" class="text-heading">Spelling <p
                                                                        class="vn-text-heading">Đánh vần</p></td>
                                                                <td class="text-table">
                                                                    Vocabulary <br>
                                                                    Từ vựng
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student is able to spell vocabulary words learned in
                                                                    the lesson. <br>
                                                                    <i>Học sinh có thể đánh vần các từ đã học trong bài
                                                                        học</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_ctp_speaking_expression}}</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-table">
                                                                    Phonetic Spelling <br>
                                                                    Đánh vần
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student uses decoding, letter-sounds, and other
                                                                    strategies to spell words. <br>
                                                                    <i>Học sinh sử dụng quy tắc, chữ cái- âm để đánh vần
                                                                        từ</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_ctp_teamwork}}</span>
                                                                </td>
                                                            </tr>
                                                            <tr class="table-primary">
                                                                <td>Category((<i>Kĩ năng</i>)</td>
                                                                <td>Sub-Category(<i>Thành phần</i>)</td>
                                                                <td>Description(<i>Miêu tả</i>)</td>
                                                                <td><span>Score / 5</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="3" class="text-heading">Social Skills <p
                                                                        class="vn-text-heading">Kỹ năng xã hội</p></td>
                                                                <td class="text-table">
                                                                    Behavior <br>
                                                                    Ứng xử
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student is well-behaved and follows class rules.
                                                                    <br>
                                                                    <i>Học sinh ngoan ngoãn và tuân thủ nội quy lớp
                                                                        học.</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_social_skills_behavior}}</span>
                                                                </td>
                                                            </tr>
                                                            <tr>

                                                                <td class="text-table">
                                                                    Confidence <br>
                                                                    Tự tin
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student shares ideas and thoughts to teachers and
                                                                    students with confidence. <br>
                                                                    <i>Học sinh chia sẻ ý tưởng, suy nghĩ với giáo viên
                                                                        và các bạn một cách tự tin</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_social_skills_confidence}}</span>
                                                                </td>
                                                            </tr>
                                                            <tr>

                                                                <td class="text-table">
                                                                    Participation <br>
                                                                    Tham gia
                                                                </td>
                                                                <td class="text-left text-table">
                                                                    Student raises their hand often and engages well
                                                                    with lesson content. <br>
                                                                    <i>Học sinh thường xuyên giơ tay tham gia xây dựng
                                                                        bài</i>
                                                                </td>
                                                                <td>
                                                                    <span class="score">{{issue.scoring_social_skills_participation}}</span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 comment">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="2">STUDENT'S GREATEST STRENGTH <br><i>Ưu
                                                                điểm nổi trội nhất của học sinh</i></th>
                                                        </tr>

                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td width="50%">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                         <p>{{issue.tc_gretest_strength}} <br /><i>({{issue.cm_gretest_strength}})</i></p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 comment">
                                                <div class="table-responsive scrollable">
                                                    <table class="table table-bordered apax-table">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="2">WHAT NEEDS TO BE IMPROVED <br><i>Kỹ năng
                                                                cần cải thiện</i></th>
                                                        </tr>

                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td width="50%">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <p>{{issue.tc_need_improved}} <br /><i>({{issue.cm_need_improved}})</i></p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </b-card>
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
        name: 'dashboard',
        data() {
            return {
                success: 'success',
                issue: {},
                scores: [],
                book_name: '',
                class_info: {}
            }
        },
        created() {
            this.getPrintData();
        },
        methods: {
            getPrintData(){
                // this.flags.form_loading = true;
                u.a().get(`/api/issues/print/${this.$route.params.id}`)
                    .then(resp => {
                        // this.flags.form_loading = false;
                        this.issue = resp.data.issue[0];
                        this.scores = resp.data.scores.original;
                        this.book_name = resp.data.issue[0].book_name;

                        if(parseInt(this.$route.params.mode)){
                            setTimeout(function () {
                                window.print();
                            }, 500)
                        }
                    })
            }
        }
    }
</script>
<style lang="scss">
    @import '/static/css/bootstrap/print.css';

    .apax-form .filter-label {
        padding: 5px;
    }

    #register-detail {
        border-left: 0.5px solid #bbd5e8;
    }

    .filter-classes {
        border-top: 0.5px solid #bbd5e8;
        padding: 20px 0 0 20px;
        margin: 10px 0 0 0;
    }

    .tree-view {
        padding: 0;
        margin: 5px 0 20px 10px;
        width: 95%;
        overflow: -webkit-paged-x;
        overflow-x: scroll;
    }

    /* width */
    ::-webkit-scrollbar {
        height: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #b3cbe5;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #779cc4;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #3a6896;
    }

    #register-detail .class-info {
        padding: 0 0 20px 0;
        margin: 0 0 20px 0;
        border-bottom: 0.5px solid #bbd5e8;
    }

    .apax-form .field-label {
        font-weight: 600;
    }

    .apax-form .class-info .field-detail img {
        border-radius: 50%;
        width: 30px;
        height: 30px;
    }

    .class-info .col-md-12 {
        margin-bottom: 5px;
    }

    #register-detail table tr td {
        font-size: 0.6rem;
        padding: 0.6rem;
        text-align: center;
        font-weight: 400;
        text-transform: capitalize;
    }

    #register-detail table.contracts-list tr td {
        padding: 6px 6px 0 6px !important;
    }

    .contracts-list select.class-date {
        padding: 0 5px;
        border: 0.5px dashed #add8ff;
        font-size: 10px;
        width: 140px;
        margin: 0 !important;
        display: inline;
        height: 20px;
    }

    #register-detail table.contracts-list tr td.nick-empty {
        background: #fff5f5;
    }

    label.check-item .custom-control-indicator {
        width: 0.9rem;
        height: 0.9rem;
    }

    .contracts-list .contract-nick {
        background: none;
        text-align: center;
        border: none;
    }

    .contracts-list .contract-nick.input-nick {
        border: 0.5px solid #d6e6fc;
        border-top: 0.5px solid #8cc8ff;
        color: #2a374e;
        text-shadow: 0 -1px 0 #fff;
        background: #f6fbff;
    }

    .contract-nick.input-nick.alert {
        color: #862323;
        border: 0.5px solid #fcd6d6;
        border-top: 0.5px solid #ff8c8c;
        background: #fff5f5;
    }

    .search-contracts {
        padding: 0 0 0 10px;
    }

    .buttons-bar .apax-search {
        float: left;
    }

    .buttons-bar .apax-button {
        float: left;
        height: 35px;
        font-size: 12px;
        padding: 3px 15px 0;
        margin: 0 0 0 20px;
    }

    #add-contract___BV_modal_footer_ button:first-child {
        display: none !important;
    }

    .float-left {
        float: left;
    }

    .book-title {
        margin-left: 8px;
    }

    #book-input {
        margin-left: 12px;
    }

    .evaluation-text {
        margin-left: 10px;
    }

    .input-text {
        width: 570px;
    }

    .text-input {
        height: 60px;
    }

    .text-small {
        font-size: 10px;
    }

    .input-small {

    }

    .text-heading {
        font-size: 18px;
    }

    .text-table {
        font-size: 12px;
        font-weight: normal;
    }

    td {
        line-height: 15px;
        padding: 5px;
    }

    #btn-sm {
        margin-bottom: 5px;
        width: 30px;
        height: 30px;
        text-align: center;
        line-height: 20px;
    }

    .button-save-group {
        margin-right: 1px;
    }

    #button-save-group {
        margin-bottom: 10px;

    }

    .vn-text-heading {
        font-size: 12px;
        font-weight: normal;
        margin-top: 5px;
    }

    #print-button {
        height: 35px;
        margin-top: -3px;
    }

    td, th {
        text-transform: none !important;
    }
</style>
