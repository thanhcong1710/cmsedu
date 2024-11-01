<template>
    <div class="wrapper">
        <div class="animated fadeIn apax-form">
            <b-card header-tag="header">
                <div slot="header">
                    <i class="fa fa-reddit-alien"></i> <b class="uppercase">Công cụ Hỗ trợ</b>
                </div>
                <div class="panel">
                    <div class="row">
                        <div class="col-md-8">
                            <b-card header-tag="header">
                                <div slot="header">
                                    <i class="fa fa-leanpub"></i> <b class="uppercase">Cập nhật ngày học cuối, tất cả học sinh đang học theo Kỳ nghỉ lễ</b>
                                </div>
                                <div class="panel form-fields">
                                    <div class="row rline">
                                        <span class="col-md-4 tline title-bold txt-right">Trung tâm:</span>
                                        <span class="col-md-8">
                                        <select @change="selectBranch" class="form-control input-sm" v-model="branch">
                                            <option value="0">
                                                Chọn trung tâm
                                            </option>
                                            <option :value="branch.id" v-for="(branch,ind) in branches" :key="ind">
                                                {{branch.name}}
                                            </option>
                                        </select>
                                        </span>
                                    </div>
                                    <div class="row rline">
                                        <span class="col-md-4 tline title-bold txt-right">Số học sinh đang học:</span>
                                        <span class="col-md-8" style="margin-top:3px;">{{studying.total}}</span>
                                    </div>
                                    <div class="row rline">
                                        <span class="col-md-4 tline title-bold txt-right">Ngày cập nhật gần nhất:</span>
                                        <span class="col-md-8" style="margin-top:3px;">{{studying.updated_last_date}}</span>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <abt
                                         :markup="'success'"
                                         :icon="'fa fa-save'"
                                         label="Cập nhật"
                                         title="Cập nhật ngày học cuối tất cả học sinh theo ngày nghỉ lễ mới thêm"
                                         :disabled="disableSave ||![686868, 999999999, 56,58].includes(parseInt(session.user.role_id))"
                                         :onClick="updateProgram"
                                    >
                                    </abt>
                                </div>
                            </b-card>
                        </div>
                    </div>
                </div>
            </b-card>
        </div>
        <b-modal title="Thông Báo" :class="classModal" size="lg" v-model="modal" @ok="modal=false" ok-variant="primary">
            <div v-html="message"></div>
        </b-modal>
    </div>
</template>

<script>
  import tree from 'vue-jstree'
  import abt from '../../../components/Button'
  import u from '../../../utilities/utility'

  export default {
    name: 'Register-Add',
    components: {
      abt,
      tree
    },
    data () {
      return {
        studying:[],
        branches: [],
        classModal: '',
        message: '',
        term_program_product: {
          id: '',
          program_code_id: '',
          product_id: '',
          program_id: '',
          status: ''
        },
        modal: false,
        branch: 0,
        disableSave: true,
        session: u.session(),
        msg_type:'caution dark',
      }
    },
    filters:{
      typeToName: function(value){
        if (value==0) return 'Thường';
        else return 'Vip'
      }
    },
    created(){
      if (u.authorized()) {
        u.a().get('/api/branch/role').then(response => {
          this.branches = response.data
        })
      }
    },
    methods: {
      selectBranch(){
        u.a().get(`/api/branches/studying/${this.branch}`).then(response =>{
          if (response.data.data.total >0)
            this.disableSave = false
          else
            this.disableSave = true

          this.studying = response.data.data;
        })
      },
      updateProgram(){
        const confirmation = confirm("Bạn có chắc là muốn cập nhật ngày học cuối không?")
            if (confirmation){
              u.apax.$emit('apaxLoading', true)
              let formData = {id: this.branch}
              u.p('/api/tool/update-last-date/branch',formData)
                .then(response => {
                  u.apax.$emit('apaxLoading', false)
                  var currentDateWithFormat = new Date().toLocaleString()
                  this.studying.updated_last_date = currentDateWithFormat
                  this.disableSave = true
                  this.$notify({
                    group: 'apax-atc',
                    title: 'Thông báo',
                    type: 'success',
                    duration: 3000,
                    text: 'Cập nhật toàn bộ ngày học cuối thành công.'
                  })
                })
            }
        }
      },
  }
</script>

<style scoped>
    .row {
        line-height: 30px;
    }
    label.apax-title {
        margin:5px 0 3px;
        font-weight: 500;
        color:#333;
    }
    ul.apax-list.select-able li.active {
        background: #414c58;
        font-weight: 500;
        color: #FFF;
        text-shadow: 0 1px 1px #111;
    }
    .txt-right {
        text-align: right
    }
    .tline {
        padding: 3px 0px;
    }
    .title-bold {
        font-weight: bold;
    }
    .fline input {
        border: none;
        border-bottom: 1px solid rgb(224, 224, 224);
        color: #555;
        padding: 0;
        height: 36px;
        width: 100%;
    }
    .form-fields .rline input.last-line {
        margin: 0 0 17px 0;
    }
</style>