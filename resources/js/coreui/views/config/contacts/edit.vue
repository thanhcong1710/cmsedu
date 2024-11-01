<template>
  <div class="wrapper">
    <div class="animated fadeIn apax-form">
      <b-row>
        <b-col cols="12">
          <b-card header-tag="header"
                  footer-tag="footer">
            <div slot="header">
              <strong>Thêm Hình Thức Liên Lạc Mới</strong>
              <div class="card-actions">
                <a href="skype:live:c7a5d68adf8682ff?chat" target="_blank">
                  <small className="text-muted"><i class="fa fa-skype"></i></small>
                </a>
              </div>
            </div>
            <div class="content-detail">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Tên hình thức liên lạc</label>
                      <input class="form-control" type="text" v-model="contact.name">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label">Trạng thái</label>
                      <select  class="form-control" v-model="contact.status">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-12 col-sm-offset-3">
                    <button class="btn btn-success" type="submit" @click.prevent="updatecontact">Lưu</button>
                    <router-link class="btn btn-sm btn-danger" :to="'/contacts'">
                      Quay lại
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
  import axios from 'axios'
  import u from '../../../utilities/utility'

  export default {
    name: 'Add-Contact',
    data() {
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
          modal: () => this.exitModal(),
        },
        contact: {
          name: '',
          status: ''
        }
      }
    },
    created() {
      let uri = '/api/contacts/' + this.$route.params.id + '/edit';
      axios.get(uri).then((response) => {
        this.contact = response.data;
      })
      // let uri = '/api/contacts/'+this.$route.params.id;
      // axios.get(uri).then((response) => {
      // 	this.contact = response.data;
      // 	console.log(`this.contact ${JSON.stringify(this.contact)}`)
      // });
    },
    methods: {
      updatecontact() {
        let uri = `/api/contacts/` + this.$route.params.id;
        axios.put(uri, this.contact).then((response) => {
          // this.$router.push('/contacts')
          this.html.modal.message = "Sửa thành công Hình thức liên lạc!"
          this.html.modal.display = true
        })
      },
      exitModal(){
          this.$router.push('/contacts')
      },
    }
  }
</script>


