<template>
	<div class="wrapper">
		<div class="animated fadeIn apax-form">
			<b-row>
		        <b-col cols="12">
		        	<b-card
		        		header-tag="header"
		        		footer-tag="footer">
		        		<div slot="header">
		            		<strong>Xếp Hạng Nhân Viên</strong>
			            	<div class="card-actions">
			            		<a href="skype:thanhcong1710?chat" target="_blank">  <small className="text-muted"><i class="fa fa-skype"></i></small></a>
			            	</div>
		        		</div>
		        		<div v-show="loading" class="ajax-load content-loading">
							<div class="load-wrapper">
								<div class="loader"></div>
								<div v-show="loading" class="loading-text cssload-loader">Đang tải dữ liệu...</div>
							</div>
						</div>
		        		<div class="content-detail">
		            		<div id="page-content">

		            			<div class="row">
		            				<div class="col-6"></div>

		            				<div class="col-6 mt-2">
		            					<button class="btn btn-primary" @click="showRankModal">Chọn kỳ xếp hạng...</button>
		            					<button class="btn btn-info ml-1" @click="exportExcelExample">Download biểu mẫu</button>
		            					<button class="btn btn-success ml-1" @click="exportExcel">Export</button>
		            				</div>
		            			</div>
		  
				            	<div class="row">
				            		<div class="table-responsive scrollable">
							            <table class="table table-striped table-bordered apax-table">
							                <thead>
							                  <tr class="text-sm">
							                    <th style="width:100px;" class="text-center width-50">Mã nhân viên</th>
							                    <th class="text-center width-50">Trung tâm</th>
							                    <th class="width-150">Tên nhân viên</th>
							                    <th class="width-150">Kỳ xếp hạng</th>
							                    <th class="width-50">Hạng nhân viên</th>
							                    <th style="width:150px;" class="width-150">Ghi chú</th>
							                    <th class="width-50">Thao tác</th>
							                  </tr>
							                </thead>
							                <tbody>
							                	<tr>
							                		<td><input type="text" class="form-control" placeholder="Tìm theo mã" v-model="hrm_id"></td>
							                		<td>
							                			<vue-select
								                            label="name"
								                            multiple
								                            placeholder="Lọc Trung Tâm"
								                            :options="branches"
								                            v-model="branch"
								                            :searchable="true"
								                            language="en-US"
								                            :onChange="searchUsersByBranch"
								                        ></vue-select>
							                		</td>
							                		<td><input type="text" class="form-control" placeholder="Tìm theo tên" v-model="user_name"></td>
							                		<td>
							                			<select class="form-control" v-model="score_item">
							              					<option value="">Tất cả</option>
							              					<option v-for="(score, ind) in score_list" :key="ind">{{ score.name }}</option>
							              				</select>
							                		</td>
							                		<td>
							                			<vue-select
								                            label="name"
								                            multiple
								                            placeholder="Lọc hạng"
								                            :options="ranks_list"
								                            v-model="ranks_item"
								                            :searchable="true"
								                            language="en-US"
								                            :onChange="searchUsersByBranch"
								                        ></vue-select>
							                		</td>
							                		<td></td>
							                		<td><button class="btn btn-default" @click="resetSearchKeyword">Bỏ lọc</button></td>
							                	</tr>
							                  <tr v-for="(user, index) in users" :key="index">
							                    <td>{{ user.hrm_id }}</td>
							                    <td>{{ user.branch }}</td>
							                    <td class="text-center">{{user.full_name}}</td>
							                    <td>{{user.score_name}}</td>
							                    <td>{{user.rank_name}} </td>
							                    <td>{{user.note}} </td>
							                    <td class="text-center">
																		<router-link class="btn btn-sm btn-warning" :to="{name: 'Sửa Xếp Hạng Nhân Viên' , params: {id: user.id}}">
																			<span class="fa fa-pencil"></span>
																		</router-link>
							                      <button @click="removeItem(user.id)" class="btn btn-danger btn-icon"><i class="fa fa-times"></i></button>
							                    </td>
							                  </tr>
							                </tbody>
							            </table>
							            <div class="text-center">
							                <nav aria-label="Page navigation">
							                	<paging
								                  :rootLink="router_url"
								                  :id="pagination_id"
								                  :listStyle="list_style"
								                  :customClass="pagination_class"
								                  :firstPage="pagination.spage"
								                  :previousPage="pagination.ppage"
								                  :nextPage="pagination.npage"
								                  :lastPage="pagination.lpage"
								                  :currentPage="pagination.cpage"
								                  :pagesItems="pagination.total"
								                  :pageList="pagination.pages"
								                  :pagesLimit="pagination.limit"
								                  :routing="goTo"
								                >
							                	</paging>
							            	</nav>
							            </div>
					          		</div>
				            	</div>
			          		</div>
			        	</div>  
			      	</b-card>
			    </b-col>
			</b-row>

			<b-modal 
				size="lg" 
				id="userRankModal" 
				v-model="userRankModal"
				title="Xếp hạng nhân viên"
				ok-variant="success" 
				class="modal-success"
			>

		        <b-container fluid>
		          <b-row class="mb-1">
		            <b-col cols="12">
		            	<div class="row">

		              		<div class="col-5">
		              			<div class="row">
		              				<div class="col-4"><strong>Kỳ xếp hạng</strong></div>
		              				
			              			<div class="col-8">
			              				<select class="form-control" v-model="score_item">
			              					<option value="" disabled>Chọn kỳ xếp hạng</option>
			              					<option v-for="(score, ind) in score_list" :key="ind" :value="score.id" >{{ score.name }}</option>
			              				</select>
			              			</div>
		              			</div>
		              		</div>

		              		<div class="col-5">
		              			<div class="row">
		              		
		              				
		              				<div class="col-4"><strong>Chọn file</strong></div>
			              			<div class="col-8">
			              				<input type="file" class="form-control"
																id="fileUploadExcel" 
						                    @change="fileChanged"
			              				>
			              			</div>
		              			</div>
		              		</div>

		              		<div class="col-2">
		              			<button class="btn btn-info" @click="btnUpload">Import</button>
		              		</div>
			       
		            	</div>
		            </b-col>
		          </b-row>
		        </b-container>

		        <div slot="modal-footer" class="w-100">
		          <b-btn size="sm"
		                 class="float-right"
		                 variant="primary"
		                 @click="closeModal">
		            Hủy
		          </b-btn>
		        </div>
		    </b-modal>

		</div> 
	</div>
</template>

<script>
import axios from 'axios'
import u from '../../utilities/utility'
import paging from '../../components/Pagination'
import select from 'vue-select'

export default {
	components: {
		paging,
		"vue-select": select,
	},
	name: 'user-rank',
  data () {
    return {
   		userRankModal: false,
    	scores: [],
    	router_url: '/scores/list',
     	pagination_id: 'cycles_paging',
    	pagination_class: 'cycles paging list',
     	list_style: 'line',
    	pagination: {
        	limit: 20,
        	spage: 0,
        	ppage: 0,
        	npage: 0,
        	lpage: 0,
        	cpage: 0,
        	total: 0,
        	pages: []
    	},
    	search: {
        	name: '',
        	year: ''
      	},
    	key: '',
    	value: '',
    	users: [],
    	score_list: [],
    	score_item: '',
    	files: {
	        attached_file: ''
	      },
      	loading: false,
      	branches: [],
      	branch: '',
      	ranks_list: [],
      	ranks_item: '',
      	hrm_id: '',
      	user_name: '',
      	selectedBranch: []
    }
  },
 	created(){
        this.getScores()
        this.getUserRankList()
        this.getBranchList()
        this.fetchScoreList()
        this.getRankList()
	},
	methods: {
		fileChanged(e) {
			const fileReader = new FileReader();
		      const fileName = e.target.value.split( '\\' ).pop();
		      fileReader.readAsDataURL(e.target.files[0])
		      fileReader.onload = (e) => {
		        this.files.attached_file = e.target.result
		      }
		},
		resetSearchKeyword(){
			this.ranks_item = ''
			this.branch = ''
			this.score_item = ''
			this.user_name = ''
			this.hrm_id = ''
		},
		btnUpload() {
			const cf = confirm('Bạn có muốn Import?');
			if(cf) {
				this.userRankModal = false;
					this.loading = true;
					let Url = `api/rate/users/uploadExcel`;
					var dataUpload = {
						'files': this.files.attached_file,
						'score': this.score_item
					};
					u.a().post(Url, dataUpload).then(response => {
						this.loading = false;
						this.score_item = '';
						this.files.attached_file = '';
						$('#fileUploadExcel').val('');
						alert('Upload thanh cong!');
						this.getUserRankList();
						}).catch(e => console.log(e))
			}
		},
		removeItem(id) {
			const cf = confirm('Bạn có chắc chắn muốn xóa?');
			if( cf ) {
				this.loading = true;
				let Url = `api/rate/users/remove/${id}`;
				u.a().post(Url).then(response => {
					this.getUserRankList();
					this.loading = false;
				})
			}
		},
		reset(){
		  this.key = ''
		  this.value = ''
		  this.search.name = ''
		  this.search.year = ''
		  this.getScores()
		},
		searchUsersByBranch(){
			const keywords = {
				user_hrm_id: this.hrm_id,
				branch: this.selectedBranch,
				user_name: this.user_name,
				rank: this.rank,
				score: this.score_item
			}
			u.a().post(`/api/search/search-users-rank-by-multi-keyword`, keywords).then(response => {
		        this.users = response.data
		    })
		},
		getBranchList(){
			u.a().get(`/api/reports/branches`).then(response => {
				this.branches = response.data
			})
		},
		getRankList(){
			u.a().get(`/api/get-rank/ranks/get-ranks-list`).then(response => {
				this.ranks_list = response.data
			})
		},
		fetchScoreList(){
			u.a().get(`/api/scores`).then(response => {
				this.score_list = response.data
			})
		},
		getUserRankList(){
			u.a().post(`api/rate/users/lists`, this.search).then(response => {
				this.users = response.data;
			})
		},
		exportExcel() {
			let url = `api/rate/users/exportExcel`;
			window.open(url, '_blank');
		},
		exportExcelExample() {
			let url = `api/rate/users/exportExcelExample`;
			window.open(url, '_blank');
		},
	    searchScore(){
	        var url = '/api/scores/list/1/';
	        this.key ='';
	        this.value = ''
	        var name = this.search.name ? this.search.name:""
	        if (name){
	          this.key += "name,"
	          this.value += this.search.name+","
	        }
	        var year = this.search.year ? this.search.year :""
	        if (year) {
	          this.key += "year,"
	          this.value += this.search.year+","
	        }
	        
	        this.key = this.key? this.key.substring(0, this.key.length - 1):'_'
	        this.value = this.value? this.value.substring(0, this.value.length - 1) : "_"
	        url += this.key+"/"+this.value
	        this.get(url);
	    },
	    indexOf(value){
	      return (value+1)+((this.pagination.cpage-1)*this.pagination.limit)
	    },
	    goTo(link){
	      this.getScores(link)
	      },
	      get(link){
	        this.ajax_loading = true
	         u.a().get(link)
	            .then(response => {
	              this.scores = response.data.scores
	              this.pagination = response.data.pagination
	              this.ajax_loading = false
	            }).catch(e => console.log(e));
	      },
      getScores(page_url) {
        const key = this.keyword ? this.keyword : '_'
        const fil = this.fil ? this.fil : '_'
        page_url = page_url ? '/api'+page_url : '/api/scores/list/1'
        page_url+= '/' + key + '/' + fil
        this.get(page_url)
      },
      makePagination(meta, links){
        let pagination = {
         current_page: data.current_page,
         last_page: data.last_page,
         next_page_url: data.next_page_url,
         prev_page_url: data.prev_page_url
       }
       this.pagination = pagination;
     },
     selectItem(){
     	this.userRankModal = false
     },
     showRankModal(){
     	this.fetchScoreList()
     	this.userRankModal = true
     },
     importUserRank(){
     	this.userRankModal = false
     },
     closeModal() {
        this.userRankModal = false
    },
    deleteScore(id, index){
      const delStdConf = confirm("Do you ready want to delete this student?");
      if (delStdConf === true) {
              // console.log(`xId = ${xId}, Index = ${idx}`)
              u.a().delete(`/api/scores/${id}`)
              .then(response => {
                this.scores.splice(index, 1);
              })
              .catch(error => {
              });
            }
          }
        },
        
        filters: {
          statusName(value){
            return value === 1 ? 'Hoạt động': 'Không hoạt động'
          }
        }
      }
      </script>
      <style scoped lang="scss">
  .apax-table .mx-datepicker.time-picker {
    width: 100%!important;
    min-width: 100px!important;
  }
  .user-upload-frame {
    position: relative;
  }
  .user-upload-file {
    position: absolute;
    top: 0;
    right: 0;
  }
  .ajax-load.content-loading {
  	z-index:99999999 !important;
  }
</style>