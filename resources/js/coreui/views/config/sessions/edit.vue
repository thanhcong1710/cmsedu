<template>
    <div class="wrapper">
      <div class="animated fadeIn apax-form">
          <b-row>
            <b-col cols="12">
                <b-card
                header-tag="header"
                footer-tag="footer">
                  <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Xem Thông Tin Buổi Học</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
                  </div>
                  <div class="content-detail">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form group">
                          <label class="control-label">Tên buổi học</label>
                          <select class="form-control text-select" v-model="session.class_day" :readonly="true">
                            <option value="" disabled >Chọn buổi học </option>
                            <option value="1">Thứ 2</option>
                            <option value="2">Thứ 3</option>
                            <option value="3">Thứ 4</option>
                            <option value="4">Thứ 5</option>
                            <option value="5">Thứ 6</option>
                            <option value="6">Thứ 7</option>
                            <option value="0">Chủ nhật</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form group">
                          <label class="control-label">Trung tâm</label>
                          <select class="form-control text-select" v-model="session.branch_id" :readonly="true">
                              <option value="" disabled>Chọn trung tâm</option>
                              <option :value="branch.id" v-for="(branch, index) in branches">{{branch.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form group">
                          <label class="control-label">Kỳ học</label>
                          <select class="form-control text-select" v-model="session.semester_id" :readonly="true">
                              <option value="" disabled>Chọn kỳ học</option>
                              <option :value="semester.id" v-for="(semester, index) in semesters">{{semester.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Lớp học</label>
                          <input type="text" class="form-control" v-model="session.class_name" :readonly="true">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Giáo viên</label>
                          <select class="form-control text-select" v-model="session.teacher_id" :readonly="true">
                              <option value="" disabled>Chọn giáo viên</option>
                              <option :value="teacher.id" v-for="(teacher, index) in teachers">{{teacher.ins_name}}</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Phòng học</label>
                          <select class="form-control text-select" v-model="session.room_id" :readonly="true">
                              <option value="" disabled>Chọn phòng học</option>
                              <option :value="room.id" v-for="(room, index) in rooms">{{room.room_name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Ca học</label>
                          <select class="form-control text-select" v-model="session.shift_id" :readonly="true">
                              <option value="" disabled>Chọn ca học</option>
                              <option :value="shift.id" v-for="(shift, index) in shifts">{{shift.name}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Ngày học</label>
                          <div class="form-check-group"> 
                              <div class="form-check-left">
                                  <input type="checkbox" value="1" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Thứ 2</label>
                              </div>
                              <div class="form-check-left">
                                  <input type="checkbox" value="2" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Thứ 3</label>
                              </div>
                              <div class="form-check-left">
                                  <input type="checkbox" value="3" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Thứ 4</label>
                              </div>
                              <div class="form-check-left">
                                  <input type="checkbox" value="4" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Thứ 5</label>
                              </div>
                              <div class="form-check-left">
                                  <input type="checkbox" value="5" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Thứ 6</label>
                              </div>
                              <div class="form-check-left">
                                  <input type="checkbox" value="6" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Thứ 7</label>
                              </div>
                              <div class="form-check-left">
                                  <input type="checkbox" value="0" class="form-check-input" v-model="session.weekdays" :disabled="true"/>
                                  <label class="form-check-label left-check">Chủ nhật</label>
                              </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" title="Chọn quãng thời gian học">Thời Gian Học: </label>
                            <date-picker style="width:100%;" 
                                        :disabled="true"
                                        v-model="timeline"
                                        :clearable="true"
                                        :not-before=datepickerOptions.minDate
                                        range
                                        :lang="datepickerOptions.lang"
                                        format="YYYY-MM-DD"
                                        id="apax-date-range"
                                        placeholder="Chọn thời gian học từ ngày đến ngày">
                            </date-picker>
                        </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group">
                              <label class="control-label" title="Trạng thái lớp hiện tại">Trạng Thái: </label>
                              <div class="form-check-group">
                                  <input type="checkbox" value="1" class="form-check-input" v-model="session.is_cancelled" disabled="true"/>
                                  <label class="form-check-label left-check">Ngưng hoạt động</label>
                              </div>                                                             
                          </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group">
                              <label class="control-label" title="Số lượng học sinh">Số lượng HS tối đa: </label>
                              <input type="number" value="15" class="form-control" v-model="session.max_students" :readonly="true"/>
                         </div>
                      </div>
                      <div class="col-sm-3">
                          <div class="form-group">
                              <label class="control-label" title="Người phụ trách">CS - Giáo Viên Chủ Nhiệm: </label>
                               <select class="form-control text-select" v-model="session.cm_id" :readonly="true">
                                  <option value="" disabled>CS - Giáo Viên Chủ Nhiệm</option>
                                  <option :value="cm.user_id" v-for="(cm, index) in list_cm">{{cm.full_name}}</option>
                              </select>
                         </div>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="row">
                        <div class="col-sm-12 col-sm-offset-3 text-right">
                          <!--<button class="apax-btn full edit" type="submit" @click="updateSession"><i class="fa fa-save"></i> Lưu</button>-->
                          <router-link class="apax-btn full warning" :to="'/sessions'">
                            <i class="fa fa-sign-out"></i> Quay lại
                          </router-link>
                        </div>
                      </div>
                    </div>
                  </div>  
                </b-card>
            </b-col>
        </b-row>

        <b-modal 
            :title="html.modal.title" 
            :class="html.modal.class" size="sm" 
            v-model="html.modal.display" 
            @ok="action.modal" 
            ok-variant="primary"
        >
         <div v-html="html.modal.message"></div>
      </b-modal>


      </div> 
    </div>
</template>


<script>
import datePicker from 'vue2-datepicker'
import axios from 'axios'
import u from '../../../utilities/utility'
import abt from '../../../components/Button'
import file from '../../../components/File'
import search from '../../../components/Search'
import select from 'vue-select'

export default {
  name: 'Edit-Session',
  components: {
      datePicker,
      abt,
      file,
      search,
      datePicker,
      'vue-select': select,
  },
  data () {
    return {
      html: {
          modal: {
            title: 'Thông Báo',
            class: 'modal-success',
            message: '',
            display:  false
          }
      },
      action: {
        modal: () => this.exitModal()
      },
      session: {
        branch_id: '',
        semester_id:'',
        class_id: '',
        class_name: '',
        teacher_id: '',
        weekdays: [],
        room_id: '',
        shift_id: '',
        max_students:'',
        cm_id:'',
        start_date:'',
        end_date:'',
        status:'',
        is_cancelled:1,
      },
      timeline:'',
      branches: [],
      semesters: [],
      rooms: [],
      teachers: [],
      shifts: [],
      list_cm: [],
      show: false,
      datepickerOptions: {
          closed: true,
          value: '',
          minDate: '',
          shortcuts:[
            {
              text: '3 tháng',
              start: new Date(),
              end: new Date(Date.now() + 3600 * 1000 * 24 * 86)
            }
          ],
          lang: {
              days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
              months: [
                  'Tháng 1',
                  'Tháng 2',
                  'Tháng 3',
                  'Tháng 4',
                  'Tháng 5',
                  'Tháng 6',
                  'Tháng 7',
                  'Tháng 8',
                  'Tháng 9',
                  'Tháng 10',
                  'Tháng 11',
                  'Tháng 12'
              ],
          }
      },
    }
  },
  created(){
    u.a().get(`/api/reports/branches`).then(response =>{
      this.branches = response.data;
    })
    u.a().get(`/api/semesters`).then(response =>{
      this.semesters = response.data.data;
    })
    this.getEditSession()
  },
  methods: {
    getEditSession(){
      u.a().get(`/api/sessions/${this.$route.params.id}`).then(response =>{
        this.session = response.data;
        if(response.data.cls_iscancelled=='no'){
            this.session.is_cancelled=0
        }else{
            this.session.is_cancelled=1
        }
        u.a().get(`/api/branches/${this.session.branch_id}/teachers`).then(response =>{
          this.teachers = response.data
        })
        u.a().get(`/api/rooms/branch/${this.session.branch_id}`).then(response =>{
          this.rooms = response.data
        })
          u.a().get(`/api/branches/${this.session.branch_id}/cm`).then(response =>{
          this.list_cm = response.data;
        })
        u.a().get(`/api/shifts/branch/${this.session.branch_id}`).then(response =>{
          this.shifts = response.data;
        })  
        this.timeline = [new Date(response.data.start_date), new Date(response.data.end_date)];
      })
    },
    updateSession(){
      const session = {
        branch_id: this.session.branch_id,
        semester_id: this.session.semester_id,
        class_id: this.session.class_id,
        class_name: this.session.class_name,
        teacher_id: this.session.teacher_id,
        weekdays: this.session.weekdays,
        room_id: this.session.room_id,
        shift_id: this.session.shift_id,
        status: this.session.status,
        cm_id: this.session.cm_id,
        max_students: this.session.max_students,
        start_date: this.getDate(typeof(this.timeline[0]) != 'undefined' ? this.timeline[0] : ''),
        end_date: this.getDate(typeof(this.timeline[1]) != 'undefined' ? this.timeline[1] : ''),
      }
      if(session.weekdays == ''){
        alert("Ngày học không để trống")
        return false
      }else if(session.branch_id == ''){
        alert("Trung tâm không để trống ")
        return false
      }else if(session.semester_id == ''){
        alert("Kỳ học không để trống ")
        return false
      }else if(session.room_id == ''){
        alert("Phòng học không để trống")
        return false
      }else if(session.timeline == ''){
        alert("Thời gian học không để trống")
        return false
      }else if(session.cm_id == ''){
        alert("CS - Giáo Viên Chủ Nhiệm không để trống")
        return false
      }else if(session.teacher_id == ''){
        alert("Giáo viên không để trống")
        return false
      }
      else {
        this.saveUpdateSession(session)
      }
    },
    saveUpdateSession(session){
      // console.log('test', session);
      u.a().put(`/api/sessions/${this.$route.params.id}`, session).then(response => {
        this.html.modal.message = "Sửa buổi học thành công!"
        this.html.modal.display = true
      })
    },
    exitModal(){
        this.$router.push('/sessions')
    },
    getDate(date) {
        if (date instanceof Date && !isNaN(date.valueOf())) {
            var year = date.getFullYear(),
                month = (date.getMonth() + 1).toString(),
                day = date.getDate().toString(),
                strMonth = month < 10 ? "0" + month : month,
                strYear = day < 10 ? "0" + day : day;

            return `${year}-${strMonth}-${strYear}`;
        }
        return '';
    },
  }
}
</script>

<style scoped>
.text-select{
  font-size: 12px;
}  
.apax-form .card-body{
  padding: 15px;
}
.v-select .vs__selected-options{
  height:30px;
}
.form-check-group{
  padding-top:0px;
}
.form-check-left {
    float: left;
    display: block;
    width: 75px;
}
.form-check-label{
  padding-left:15px;
}
.form-check-input{
  margin-left: 0px;
}
</style>