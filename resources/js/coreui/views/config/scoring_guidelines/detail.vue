<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <i class="fa fa-clipboard"></i> <strong>Thêm Hướng Dẫn Mới</strong>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Điểm</label>
                      <input type="text" v-model="scoring_guideline.name" class="form-control">
                    </div>
                  </div>
                   <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Hướng dẫn</label>
                      <input type="text" v-model="scoring_guideline.name" class="form-control">
                    </div>
                  </div>
                   <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Giải thích</label>
                      <input type="text" v-model="scoring_guideline.name" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select name="" id="" class="form-control" v-model="scoring_guideline.status">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="apax-btn full edit" type="submit" @click.prevent="addscoring_guideline"><i class="fa fa-save"></i> Lưu</button>
                    <router-link class="apax-btn full warning" :to="'/scoring_guidelines'">
                      <i class="fa fa-sign-out"></i> Quay lại
                    </router-link>
                  </div>
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
  import u from '../../../utilities/utility'

  export default {
    name: 'Add-Scoring-Guidelines',
    data() {
      return {
        show: false,
        scoring_guideline: {
          name: '',
          status: ''
        },
        branches: [],
        listSelectedBranchs: [],
        listTemporary: []
      }
    },
    created(){
      axios.get(`/api/branches`).then(response => {
        this.branches = response.data;
      })
    },
    methods: {
      removeBranch(id){
        var self = this;
        var result = confirm("Bạn có chắc chắn rằng muốn xoá trung tâm này?");
        if (result) {
          self.listSelectedBranchs = self.listSelectedBranchs.filter(function (x) {
            return x.id !== id;
          })
          self.listTemporary = self.listSelectedBranchs;
        } return false;
      },
      saveBranchs(){
        var self = this;
        if (self.listTemporary) {
          self.listSelectedBranchs = self.listTemporary;
        }
        self.show = false
      },
      cancelBranchs(){
        var self = this;
        self.show = false
        self.listTemporary = self.listSelectedBranchs;
      },
      checkAll(id){
        var p = {}
        if ($(id).prop("checked")) {
          if (this.branches) {
            $.each(this.branches, function (index, val) {
              $("#branch_" + val.id).prop("checked", true)
              
            })
          }
          this.listTemporary = this.branches;
        } else {
          $.each(this.branches, function (index, val) {
            $("#branch_" + val.id).prop("checked", false)
            
          })
          this.listTemporary = []
        }
      },
      addBranchs(){
        var self = this;
        self.show = true;
        if (self.listSelectedBranchs) {
          $.each(self.listSelectedBranchs, function (index, val) {
            $("#branch_" + val.id).prop("checked", true)
          })
        }
      },
      addscoring_guideline() {
        if (this.scoring_guideline.name && this.scoring_guideline.status){
          u.a().post(`/api/scoring_guidelines`, this.scoring_guideline).then(response =>{
            for(var i in this.listTemporary){
              this.listTemporary[i].scoring_guideline_id = response.data.id;
              u.a().put(`/api/branches/`+this.listTemporary[i].id, this.listTemporary[i]).then(response => {

              })
            }
          })
        }
        this.$router.push('/scoring_guidelines')
      },
    }
  }
</script>

<style scoped>
  .test {
    margin-top: 10px;
  }

    .test .padding-fix {
      padding: 17px;
    }

  .title-bold {
    font-weight: bold;
  }

  .add-btn {
    float: right;
    margin: 10px 0;
  }

  .info-register {
    border: 1px solid #d7d6d6;
    padding: 10px;
  }

  .status-active {
    border: 1px solid #becdbf;
    border-radius: 4px;
    padding: 3px;
    background-color: #2e9fff;
    color: #fff;
  }

  .title-search {
    text-align: right;
    padding: 5px 0 0 0;
    font-size: 13px;
    font-weight: 500;
  }

  .col-4 {
    padding: 0px 0px 8px 8px;
  }

  #class_infor {
    padding: 10px 10px;
  }

  #table_list th, td {
    font-size: 0.8rem;
  }

  .fix-boder {
    border: 1px solid #223b54;
  }

  .txt-right {
    text-align: right
  }

  .row-boss {
    padding: 20px;
  }

  .fix-form {
    width: 30% !important;
  }

  .btn-gray {
    background-color: #6a6a6a;
  }

  .scroll-tb {
    max-height: 350px;
    overflow-y: auto;
  }

  .remove-branch {
    cursor: pointer;
  }

    .remove-branch:hover {
      background-color: #b4b1b1;
      border: 2px solid #b4b1b1;
      border-radius: 3px;
    }

  .title-modal-fix {
    margin-bottom: 20px;
    margin-top: 5px
  }

  .fixed-input {
    font-size: 14px;
    color: #737373;
  }
  #tuition span{
  cursor: pointer;
  }
  .apax-form .card-body{
    padding: 15px;
  }
</style>
