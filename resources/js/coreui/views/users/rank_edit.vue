<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Cập nhật xếp hạng nhân viên</strong>
              <div class="card-actions">
                <a href="skype:thanhcong1710?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Nhân Viên</label>
                      <input type="text" v-model="userName" class="form-control" readonly>
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Trung tâm</label>
                      <select v-model="rates.branch_id" class="form-control">
                         <option v-for="(item,index) in this.branches" :key="index" :value="item.id">{{item.name}}</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Kỳ</label>
                      <select v-model="rates.score_id" class="form-control">
                         <option v-for="(item,index) in this.scores" :key="index" :value="item.id">{{item.name}}</option>
                      </select>
                    </div>
                  </div>


                  <div class="col-sm-3">
                    <div class="form-group">
                      <label class="control-label">Rank</label>
                      <select v-model="rates.rank_id" class="form-control">
                         <option v-for="(item,index) in this.ranks" :key="index" :value="item.id">{{item.name}}</option>
                      </select>
                    </div>
                  </div>
                    <br/>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label">Note</label><br>
                      <textarea v-model="rates.note" class="form-control">{{this.rates.note}}</textarea>
                    </div>
                  </div>
                 
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3 text-right">
                    <button class="btn btn-success" type="submit" @click="updateRate">Lưu</button>
                    <button class="btn btn-default" type="reset" @click="resetAll">Hủy</button>
                    <router-link class="btn btn-sm btn-danger" :to="'/user-ranks'">
                      Quay lại
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
  import u from '../../utilities/utility'
  import moment from 'moment'

  export default {
    name: 'Edit-Room',
    data() {
      return {
        rates: {
          user_id: '',
          branch_id: '',
          score_id: '',
          rank_id:'',
          note:'',
        },
        userName:'',
        products: [],
        branches:[],
        ranks: [],
        scores: [],
      }
    },
    created(){
        this.getRate();
        this.getBranch();
        this.getRanks();
        this.getScore();
    },
    methods: {
      getRate() {
        let uri = '/api/rate/users/show/'+this.$route.params.id;
        u.a().get(uri).then((response) => {
            this.rates = response.data.data;
            this.getUser(this.rates.user_id);
        });
      },
      getUser(id) {
        let uri = `/api/users/${id}/get-user-info`;
        u.a().get(uri).then((response) => {
            this.userName = response.data.full_name;
        });
      },

      getBranch() {
        let uri = `/api/branches`;
        u.a().get(uri).then((response) => {
            this.branches = response.data;
        });
      },

      getRanks() {
        let uri = `/api/ranks/get-by-type/0`;
        u.a().get(uri).then((response) => {
            this.ranks = response.data.data;
        });
      },

      getScore() {
        let uri = `/api/scores`;
        u.a().get(uri).then((response) => {
            this.scores = response.data;
        });
      },

      resetAll(){
        this.getRate();
      },
      updateRate(){
        let uri = `/api/rate/users/update/`+this.$route.params.id;
        u.a().post(uri, this.rates).then((response) => {
          this.$router.push('/user-ranks');
        })
      },
    }
  }
</script>


