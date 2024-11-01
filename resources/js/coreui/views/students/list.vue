<template>
	<div class="animated fadeIn apax-form" @keyup="binding" id="students-management">
		<div class="row">
			<div class="col-12">
				<b-card header>
					<div slot="header">
						<i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc ..</b>
					</div>
					<div id="students-list">
						<div class="row">
							<div class="col-sm-3" v-if="filter_branch">
								<div class="first-item">
								<p class="input-group-addon filter-lbl">
									<i v-b-tooltip.hover title="Lọc theo trung tâm" class="fa fa-bank"></i>
								</p>
								<suggestion
									class="select-branch"
									:onSelect="html.dom.filter.branch.action"
									:disabled="html.dom.filter.branch.disabled"
									:placeholder="html.dom.filter.branch.placeholder"
								></suggestion>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<p class="input-group-addon filter-lbl">
									<i v-b-tooltip.hover title="Từ khóa tìm kiếm" class="fa fa-search"></i>
								</p>
								<input
									id="input_keyword"
									name="search[keyword]"
									class="filter-selection field form-control filter-input"
									v-model="html.data.filter.keyword"
									:placeholder="html.dom.filter.search.placeholder"
									@input="validate_keyword()"
								>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								<p class="input-group-addon filter-lbl">
									<i
									v-b-tooltip.hover
									title="Tiêu chí tìm kiếm theo trường dữ liệu"
									class="fa fa-book"
									></i>
								</p>
								<select
									id="select_field"
									name="search[field]"
									class="filter-selection field form-control filter-input"
									v-model="html.data.filter.field"
								>
									<option value>Tìm theo dữ kiện</option>
									<option value="0">Tất cả</option>
									<option value="1">Tên học sinh</option>
									<option value="2">Mã CMS</option>
									<option value="3">Mã Cyber</option>
									<option value="4">Điện thoại</option>
									<option value="5">Tên phụ huynh</option>
									<option value="6">Địa chỉ</option>
									<option value="7">Trường học</option>
								</select>
								</div>
							</div>
							<!-- <div class="col-sm-3">
								<div class="form-group">
								<p class="input-group-addon filter-lbl">
									<i
									v-b-tooltip.hover
									title="Tìm kiếm theo trạng thái"
									class="fa fa-book"
									></i>
								</p>
								<select
									id="select_field"
									name="search[field]"
									class="filter-selection field form-control filter-input"
									v-model="html.data.filter.status"
								>
									<option value="0">Tìm theo trạng thái</option>
									<option value="5">Học sinh tiềm năng</option>
									<option value="1.1">Học sinh chưa xếp lớp do cọc</option>
									<option value="1.2">Học sinh đủ phí chưa xếp lớp</option>
									<option value="1.3">Học sinh chuyển trung tâm chưa được xếp lớp</option>
									<option value="1.4">Học sinh hết hạn bảo lưu chưa được xếp lớp</option>
									<option value="2.1">Học sinh đang học</option>
									<option value="2.5">Học sinh được xếp lớp trước ngày học </option>
									<option value="2.2">Học sinh chuyển lớp</option>
									<option value="2.3">Học sinh chuyển trung tâm đã xếp lớp</option>
									<option value="2.4">Học sinh bảo lưu quay lại học</option>
									<option value="3">Học sinh đang bảo lưu</option>
									<option value="4">Học sinh ngừng học</option>
									<option value="6">Học sinh phát sinh gói phí nhưng chưa có tiền</option>
								</select>
								</div>
							</div> -->
						</div>
					</div>
					<div slot="footer" class="text-center">
						<div class="text-center">
							<!-- <router-link to="/students/add-student" v-if="hasPermissionUpsert">
								<button type="button" class="apax-btn full error"><i class="fa fa-plus"></i> Thêm mới </button>
							</router-link> -->
							<button @click="searching" class="apax-btn full edit">
								<i class="fa fa-filter" aria-hidden="true"></i> Lọc
							</button>
							<button @click="html.dom.filter.clearSearch.action" class="apax-btn full "><i class="fa fa-ban"></i> Bỏ lọc</button>
							<button v-if="['999999999',85858585,102,55,56,36,7676767].indexOf(session.user.role_id) > -1"  @click="extract()" class="apax-btn full edit"><i class="fa fa-file-word-o"></i> Trích xuất</button>
						</div>
					</div>
				</b-card>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<b-card header>
					<div slot="header">
						<i class="fa fa-address-book"></i> <strong>Danh sách học viên</strong>
					</div>
					<div class="table-responsive scrollable">
						<table id="apax-printing-students-list" class="table table-striped table-bordered apax-table">
							<thead>
								<tr>
									<th>STT</th>
									<th>Avatar</th>
									<th>Tên học sinh</th>
									<th>Mã CMS</th>
									<th>Mã Cyber</th>
									<th>Nguồn Từ</th>
									<!-- <th>Trạng Thái</th> -->
									<th>Học Lớp</th>
									<th>Giới tính</th>
									<th>Ngày sinh</th>
									<th>Phụ huynh</th>
									<th>Điện thoại</th>
									<th>Địa chỉ</th>
									<th>EC</th>
									<th>Trung tâm</th>
									<th>Ngày đóng phí gần nhất</th>
									<th width="50">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in list" v-bind:key="index">
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{ (parseInt(html.pagination.cpage) -1) *parseInt(html.pagination.limit) + index + 1 }}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										<img width="36px" :src="`${item.student_avatar}`" class="avatar" />
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.student_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.crm_id}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.accounting_id}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.source_name}}
										</router-link></td>
									<!-- <td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.status_name}}
										</router-link></td> -->
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.class_name, item.class_status | displayClass}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.student_gender | genderToName}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.student_birthday}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.parent_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.parent_mobile}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.student_address}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.ec_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.branch_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.page.url.link}${item.id}`">
										{{item.last_charge_date}}
										</router-link></td>
									<td class="text-center">
										<span class="apax-btn edit" :class="showEdit()">
											<router-link v-b-tooltip.hover
												class="link-me"
												title="Cập nhật hồ sơ học sinh"
												:to="{name: 'Cập Nhật Thông Tin Học Sinh', params: {id: item.id }}">
												<i class="fa fa-pencil"></i>
											</router-link>
										</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="text-center">
						<nav aria-label="Page navigation">
							<paging
								:rootLink="html.pagination.url"
								:id="html.pagination.id"
								:listStyle="html.pagination.style"
								:customClass="html.pagination.class"
								:firstPage="html.pagination.spage"
								:previousPage="html.pagination.ppage"
								:nextPage="html.pagination.npage"
								:lastPage="html.pagination.lpage"
								:currentPage="html.pagination.cpage"
								:pagesItems="html.pagination.total"
								:pagesLimit="html.pagination.limit"
								:pageList="html.pagination.pages"
								:routing="redirect">
							</paging>
						</nav>
					</div>
				</b-card>
			</div>
		</div>
	</div>
</template>

<script>

	import suggestion from '../../components/Selection'
	import paging from '../../components/Pagination'
	import search from '../../components/Search'
	import u from '../../utilities/utility'

	export default {

	components: {
		suggestion,
		paging,
		search
	},

	data() {
		const model = u.m('students').list
		model.html.dom = {
			full: 'hidden',
			filter: {
				branch: {
					options: [],
					disabled: true,
					display: 'hidden',
					action: branch => this.selectBranch(branch),
					placeholder: 'Lọc theo trung tâm'
				},
				search: {
					disabled: true,
					action: keyword => this.searching(keyword),
					placeholder: 'Từ khóa tìm kiếm'
				},
				ec: {
					data: [],
					disabled: true,
					action: () => this.selectEC(),
					placeholder: 'Lọc theo nhân viên EC'
				},
				cm: {
					data: [],
					disabled: true,
					action: () => this.selectCM(),
					placeholder: 'Lọc theo nhân viên CS'
				},
				gender: {
					disabled: true,
					action: () => this.selectGender(),
					placeholder: 'Lọc theo giới tính'
				},
				customer_type: {
					disabled: true,
					action: () => this.selectCustomerType(),
					placeholder: 'Lọc theo loại khách hàng'
				},
				student_status: {
					disabled: true,
					action: () => this.studentStatus(),
					placeholder: 'Lọc theo trạng thái'
				},
				field: {
					disabled: true,
					action: () => this.selectField(),
					placeholder: 'Tìm theo dữ liệu'
				},
				source: {
					disabled: true,
					action: () => this.selectSource(),
					placeholder: 'Lọc theo nguồn từ'
				},
				product: {
					disabled: true,
					action: () => this.selectProduct(),
					placeholder: 'Lọc theo sản phẩm'
				},
				program: {
					disabled: true,
					action: () => this.selectProduct(),
					placeholder: 'Lọc theo sản phẩm'
				},
				tuition_fee: {
					disabled: true,
					action: () => this.selectTuitionFee(),
					placeholder: 'Lọc theo gói học phí'
				},
				year_of_birth: {
					disabled: true,
					action: () => this.yearOfBirth(),
					placeholder: 'Nhập năm sinh'
				},
				clearSearch: {
					action: () => this.clearSearch()
				}
			}
		}
		model.html.data = {
			filter: {
				ec: '',
				cm: '',
				field: '',
				branch: '',
				keyword: '',
				gender: '',
				source: '',
				product: '',
				program: '',
				tuition_fee: '',
				customer_type: '',
				student_status: '',
				year_of_birth: '',
				status:0,
			}
    }
    model.html.order.by = 's.id'
	model.cache.filter = model.html.data.filter
	model.filter =  true
	model.filter_branch = true
	model.session = u.session()
    return model
	},

	created() {
		this.start()
	},

	computed: {
		showCM(){
			return ['EC', 'CM', 'EC_Leader'].indexOf(this.session.user.title) === -1
		},
		hasPermissionUpsert(){
		let roles = [999999999] // 55,56,999999999,68,69
		let uid  = parseInt(u.session().user.role_id)
		if (roles.includes(uid)){
			return  true
		}
		else
			return  false
				// return [84].indexOf(this.session.user.role_id) === -1
			}
	},

	methods: {
	  showEdit(){
      let roles = [55,56,999999999,68,69,99]
      let uid  = parseInt(u.session().user.role_id)
      if (roles.includes(uid)){
        return  ''
      }
      else
        return  'hidden'
    },
		selectField() {
			if (this.cache) {
				this.cache.filter.field = this.html.data.filter.field;
				this.get(this.link(), this.load);
			}
		},
		validate_keyword() {
			// this.html.data.filter.keyword = this.html.data.filter.keyword.replace(
			// 	/[~`!#$%^&*()=+{}[,\]./<>?;'\\:"|\t]/gi,
			// 	""
			// );
		},
		clearSearch() {
			location.reload();
		},
		start() {
			if (u.authorized() || parseInt(_.get(this.session, "user.branches.length", 0)) > 1) {
				this.html.dom.filter.branch.display = 'display'
				this.html.dom.filter.branch.disabled = false
			} else {
				this.html.dom.full = ''
				this.html.data.filter.branch = this.session.user.branch_id
				this.cache.filter.branch = this.session.user.branch_id
				this.loadECCMS()
				u.g(`${this.html.page.url.load}${this.session.user.branch_id}`)
					.then(response => {
					this.html.dom.filter.ec.disabled = false
					this.html.dom.filter.cm.disabled = false
					this.html.dom.filter.gender.disabled = false
					this.html.dom.filter.search.disabled = false
					this.html.dom.filter.customer_type.disabled = false
				}).catch(e => console.log(e))
                this.filter_branch=false
			}
			this.searching()
		},
		link() {
			this.html.data.filter.branch = this.cache.filter.branch
			const sort = u.jss(this.html.order)
			const search = u.jss(this.html.data.filter)
			const pagination = u.jss({
				spage: this.html.pagination.spage,
				ppage: this.html.pagination.ppage,
				npage: this.html.pagination.npage,
				lpage: this.html.pagination.lpage,
				cpage: this.html.pagination.cpage,
				total: this.html.pagination.total,
				limit: this.html.pagination.limit
			})
			return `${this.html.page.url.list}${pagination}/${search}/${sort}`
		},
		get(link, callback) {
			u.apax.$emit('apaxLoading', true)
			// this.html.loading.action = true
			u.g(link)
			.then(response => {
				const data = response
				callback(data)
				setTimeout(() => {
					u.apax.$emit('apaxLoading', false)
					// this.html.loading.action = false
				}, data.duration)
			}).catch(e => {
				u.apax.$emit('apaxLoading', false)
				u.log('Exeption', e)
			})
		},
		load(data) {
			this.cache.data = data
			this.list = data.list
			this.html.pagination = data.pagination
		},
		searching(word = '') {
			this.get(this.link(), this.load)
		},
		selectGender() {
			if (this.cache) {
				this.cache.filter.gender = this.html.data.filter.gender
				this.get(this.link(), this.load)
			}
		},
		studentStatus() {
			if (this.cache) {
				this.cache.filter.student_status = this.html.data.filter.student_status
				this.get(this.link(), this.load)
			}
		},
		yearOfBirth() {
			if (this.cache) {
				this.cache.filter.year_of_birth = this.html.data.filter.year_of_birth
				this.get(this.link(), this.load)
			}
		},
		selectCustomerType() {
			if (this.cache) {
				this.cache.filter.customer_type = this.html.data.filter.customer_type
				this.get(this.link(), this.load)
			}
		},
		selectEC() {
			if (this.cache) {
				this.cache.filter.ec = this.html.data.filter.ec
				this.get(this.link(), this.load)
			}
		},
		selectCM() {
			if (this.cache) {
				this.cache.filter.cm = this.html.data.filter.cm
				this.get(this.link(), this.load)
			}
		},
		binding(e) {
			if (e.key == "Enter") {
				this.searching()
			}
		},
		redirect(link) {
			const info = link.toString().split('/')
			const page = info.length > 1 ? info[1] : 1
			this.html.pagination.cpage = parseInt(page)
			this.get(this.link(), this.load)
		},
		loading() {
		},
		extract() {
			let message = ""
			let status = true
			if (_.get(u.session(), 'user.role_id') != 7676767 && _.get(u.session(), 'user.role_id') != 999999999 && _.get(u.session(), 'user.role_id') !=85858585 && _.get(u.session(), 'user.role_id') !=102
			&& _.get(u.session(), 'user.role_id') !=56) {
				message += `Tài khoản của bạn không được trích xuất danh sách HS!<br/>`
				status = false
			}
			if (!status){
				this.$notify({
					group: 'apax-atc',
					title: 'Thông báo!',
					type: 'warning',
					duration: 3000,
					text: `<br/>-----------------------------------------------<br/>${message}`
				})
				return false
			}
			u.apax.$emit('apaxLoading', true)
			 u.getFile('/api/export/student-list-export', this.html.data.filter).then(() => {
			 	u.apax.$emit('apaxLoading', false)
			 })
		},
		resetFilter(){
			this.html.data = {
				filter: {
					ec: '',
					cm: '',
					field: '',
					branch: '',
					keyword: '',
					gender: '',
					source: '',
					product: '',
					program: '',
					tuition_fee: '',
					customer_type: '',
					student_status: '',
					year_of_birth: '',
				}
			}
			return this.$router.go()
		},
		loadECCMS() {
			if (this.session.user.role_id === u.r.ec) {
				this.cache.filter.ec = this.session.user.id
			}
			if (this.session.user.role_id === u.r.cm) {
				this.cache.filter.cm = this.session.user.id
			}
			u.g(`${this.html.page.url.list}load/eccms/filter/${this.cache.filter.branch}`)
			.then(response => {
				this.html.dom.filter.ec.data = response.ecs
				this.html.dom.filter.cm.data = response.cms
				if (response.ecs.length > 1) {
					this.html.dom.filter.ec.disabled = false
				}
				if (response.cms.length > 1) {
					this.html.dom.filter.cm.disabled = false
				}
			}).catch(e => console.log(e))
		},
		selectBranch(data) {
			u.log('Select Branch', data)
			if (data) {
				this.cache.filter.branch = data.id
				this.html.data.filter.branch = data.id
				this.get(this.link(), this.load)
				this.loadECCMS()
				u.g(`${this.html.page.url.load}${data.id}`)
				.then(response => {
					this.html.dom.full = ''
					this.html.dom.filter.ec.disabled = false
					this.html.dom.filter.cm.disabled = false
					this.html.dom.filter.gender.disabled = false
					this.html.dom.filter.search.disabled = false
					this.html.dom.filter.customer_type.disabled = false
				}).catch(e => console.log(e))
			}
		}
	}
}

</script>

<style scoped lang="scss">

a {
	color: blue;
}
.avatar-frame {
	padding: 3px 0 0 0;
	line-height: 40px;
}
.avatar {
	height: 29px;
	width: 29px;
	margin: 0 auto;
	overflow: hidden;
}
p.filter-lbl {
	width: 40px;
	height: 35px;
	float: left;
}
.filter-selection {
	width: calc(100% - 40px);
	float: left;
	padding: 3px 5px;
	height: 35px !important;
}
.drag-me-up {
	margin: -25px -15px -15px;
}

</style>
