<template>
  <div class="wrapper">
    <div class="animated fadeIn ada-form">
      <b-card header-tag="header">
        <div slot="header">
          <i class="fa fa-gears"></i> <b class="uppercase">Cấu hình thông số vận hành trung tâm {{ obj.title }}</b>
        </div>
        <div class="panel">
          <div class="row">
            <div class="col-md-4">
              <b-card header-tag="header">
                <div slot="header">
                  <i class="fa fa-list-alt"></i> <b class="uppercase">Danh Sác Các Trung Tâm</b>
                </div>
                <div class="panel">
                  <div class="col-md-12 ada-form-block">
                    <branch
                      :result="test"
                    ></branch>
                  </div>
                </div>
              </b-card>              
            </div>
            <div class="col-md-8">
              <b-card header-tag="header">
                <div slot="header">
                  <i class="fa fa-gear"></i> <b class="uppercase">Thông tin cấu hình thiết lập cho trung tâm</b>
                </div>
                <div class="panel body-content">
                  <ul class="col-md-12 ada-form-input">
                    <li class="row row-title">
                      <div class="col-md-1 line-title num">
                        #
                      </div>
                      <div class="col-md-2 line-title name">
                        Tiêu Đề
                      </div>
                      <div class="col-md-2 line-title key">
                        Tên Khóa
                      </div>
                      <div class="col-md-2 line-title value">
                        Giá Trị
                      </div>
                      <div class="col-md-4 line-title description">
                        Mô Tả
                      </div>
                      <div class="col-md-1 line-title action">
                        <span @click="add(index)" class="apax-btn add"><i title="" class="fa fa-plus" data-original-title="Nhấp vào để thêm cấu hình"></i></span>
                      </div>
                    </li>
                    <li class="row row-content" v-for="(config, index) in obj.configs" :key="index" :id="`input-row-${config.id}`">
                      <div class="col-md-1 line-detail num">
                        {{(index + 1)}}
                      </div>
                      <div class="col-md-2 line-detail name">
                        <input type="text" v-model="config.name" />
                      </div>
                      <div class="col-md-2 line-detail key">
                        <input type="text" v-model="config.key" />
                      </div>
                      <div class="col-md-2 line-detail value">
                        <input type="text" v-model="config.value" />
                      </div>
                      <div class="col-md-4 line-detail description">
                        <input type="text" v-model="config.description" />
                      </div>
                      <div class="col-md-1 line-detail branch">
                        <span @click="remove(config.id)" class="apax-btn remove"><i title="" class="fa fa-remove" data-original-title="Nhấp vào để xóa bỏ"></i></span>
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="panel-footer">
                  <abt
                    :markup="'success'"
                    :icon="'fa fa-save'"
                    label="Cập Nhật"
                    title="Cập Nhật Thông Tin Chương Trình Học"
                    :disabled="html.button.save.disabled"
                    :onClick="html.button.save.action"
                    >
                  </abt>
                </div>
              </b-card>
            </div>
          </div>
        </div>
      </b-card>
    </div>
    <b-modal size="lg" 
      :ok-variant="html.modal.notification.variant"
      :title="html.modal.notification.title" 
      :class="html.modal.notification.class" 
      v-model="html.modal.notification.display" 
      @ok="html.modal.notification.action.ok" 
      @close="html.modal.notification.action.close">
      <div v-html="html.modal.notification.message"></div>
    </b-modal>
  </div>
</template>

<script>
  import u from '../../../utilities/utility'
  import abt from '../../../components/Button'
  import branch from '../../../components/Branches'
    
  export default {
    name: 'Settings',
    components: {
      abt,
      branch
    },
    data () {
      const model = u.m('settings').page      
      model.obj = {
        title: '',
        configs: [{
          id: '',
          name: '',
          key: '',
          value: '',
          description: '',
        }],
        settings: [],
        branches: [],
        branches_ids: '',
        description: ''
      }
      model.html = {
        modal: {
          notification: {
            title: 'Thông báo',
            class: 'modal-success',
            variant: 'success',
            display: false,
            action: {
              ok: () => this.notificationComplete(),
              close: () => this.notificationClose()
            }
          }
        },
        button: {
          save: {
            disabled: true,
            action: () => this.save()
          }
        }
      }
      model.filter = {
        id: 0,
        lms: 0,
        name: '',
        branch: 0,
        area_id: 0,
        zone_id: 0,
        region_id: 0
      }
      model.branch = {
        id: 0,          
        name: '',
        code: '',
        status: 0,
        brch_id: 0,
        area_id: 0,
        zone_id: 0,
        region_id: 0,
        zone_name: '',          
        region_name: ''
      }
      model.config = {
        
      }
      return model
    },
    created () {
      this.start ()
    },
    methods: {
      start () {
      },
      add () {
        const n = `${this.moment().unix()}${Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)}`
        this.obj.configs.push({
          id: n,
          name: '',
          key: '',
          value: '',
          description: '',
        })
      },
      remove () {

      },
      test (list) {
        u.log('CHECKED LIST', list)
      },
      save () {

      },
      notificationComplete () {

      },
      notificationClose () {

      }
    }
  }
</script>

<style scoped>
  
</style>

