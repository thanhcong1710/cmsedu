<template>
	<div class="animated" id="users-import">
	      <div class="col-12">
	        <b-card header>
	          <div slot="header">
	            <i class="fa fa-filter"></i> <b class="uppercase">Form Import</b>
	          </div>
	          <div id="filter_content">
	            <div class="row">
	              <div class="col-lg-12">
	                <div class="panel">
	                  <div class="panel-body">
	                  	<div class="row">
	                  		<div class="col-lg-7">
						      <label>File Import Nhân viên: 
						        <input type="file" ref="file" @change="handleFileUpload()"/>
						      </label>
						        <button @click="uploadFile()" :disabled="stage">Import</button>
						    </div>
						    <div class="col-lg-5"><strong class="result"><img src="/img/pending.gif" v-if="pending">{{notify}}</strong></div>
	                  	</div>
	                  </div>
	                </div>
	              </div>
	            </div>
	          </div>
	        </b-card>
	      </div>
  	</div>
</template>
<script>
	import u from '../../utilities/utility'

	export default {
  		name: 'Import',
  		components: {},
  		data() {
  			return {
  				file : '',
  				stage: false,
  				notify: '',
  				pending: false
  			}
  		},
		created() {
		},
		methods: {
			uploadFile(){
				let formData = new FormData()
				formData.append('file', this.file)
				this.pending = true
				if(!this.stage){
					this.stage = true
					u.p('/api/upload/users',formData)
		              .then(response => {
		              	this.notify = response.message
						console.log(response)
						this.pending = false	                
	              	})
				}
				
	      	},
	      	handleFileUpload(){
		        this.file = this.$refs.file.files[0];
		        this.stage = false
      		}
		}
	}
</script>

<style>
	strong.result img{
		width: 30px;
	}
</style>		