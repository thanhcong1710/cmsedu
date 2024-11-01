<template>
  <div class="tab-student-rank apax-content">
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-bar-chart"></i> <b class="uppercase">Xếp loại học sinh</b>        
      </div>
      <div class="panel">
        <div class="panel-body">
          <div class="row">            
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">Xếp loại học sinh</label>
                 <select name="" class="form-control" v-model="term_student_rank.rank_id" id="">
                    <option value="">Lựa chọn xếp loại</option>
                    <option :value="rank.id" v-for="(rank, index) in ranks" :key="index">{{rank.name}}</option>
                  </select>
              </div>
            </div>
            <div class="col-sm-9">
              <div class="form-group">
                <label class="control-label">Ý kiến đánh giá HS</label>
                <textarea class="form-control" v-model="term_student_rank.comment"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <div class="row">
            <div class="col-md-12 text-right">
              <button class="btn btn-success" type="submit" @click="addTerm">Thêm</button>
            </div>
          </div>
        </div>
      </div>
    </b-card>
    <b-card header-tag="header">
      <div slot="header">
        <i class="fa fa-calendar-check-o"></i> <b class="uppercase">Lịch sử xếp loại học sinh</b>        
      </div>
      <div class="panel">
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive scrollable">
              <table class="table table-striped table-bordered apax-table">
                <thead>
                  <tr class="text-sm">
                    <th class="text-center width-50">STT</th>
                    <th class="width-150">Ngày thực hiện</th>
                    <th class="width-150">Người thực hiện</th>
                    <th class="">Loại</th>
                    <th class="">Ý kiến đánh giá</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(term, index) in list" :key="index">
                    <td>{{index+1}}</td>
                    <td>{{term.created_at}}</td>
                    <td>{{term.creator}}</td>
                    <td> {{term.rank_id|rankType}}</td>
                    <td>{{term.comment}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
  import u from '../../utilities/utility'
  import rankType from '../../utilities/filters.js'

  export default {
    props: {
      ranks: {
        type: Array,
        default: () => []
      },
      list: {
        type: Array,
        default: () => []
      }
    },
    data () {
      return {
        term_student_rank: {
          comment: '',
          rank_id: '',
          rating_date: ''
        }
      }
    },
    watch: {

    },
    created() {
    },
    methods: {
      addTerm(){
         this.term_student_rank.student_id = this.$route.params.id
        u.p(`/api/termStudentRanks/add`, this.term_student_rank).then(response =>{
          this.list = response
          this.term_student_rank.comment = ''
          this.term_student_rank.rank_id = ''
          this.term_student_rank.rating_date = ''
          this.$notify({
            group: 'notify',
            title: 'Thông Báo',
            text: 'Đánh giá học sinh đã được cập nhật thànhc công!'
          })
        })
      }
    },
    components: {}
  }
</script>

<style scoped>

</style>
