<template>
	<div class="animated fadeIn apax-form" @keyup="binding" id="students-management">
		<div class="row">
			<div class="col-12">
				<b-card header>
					<div slot="header">
						<i class="fa fa-filter"></i> <b class="uppercase">Bộ lọc</b>
					</div>
					<div id="students-list">
						<div class="row">
							<div class="col-lg-12" :class="html.dom.filter.branch.display">
								<suggestion
									:onSelect="html.dom.filter.branch.action"
									:options="html.dom.filter.branch.options"
									:disabled="html.dom.filter.branch.disabled"
									:placeholder="html.dom.filter.branch.placeholder">
								</suggestion><br/>
							</div>
							<div class="col-lg-12">
								<div class="panel">
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-3">
												<label class="filter-label control-label">Tìm Kiếm</label><br/>
												<search
													:onSearch="html.dom.filter.search.action"
													:disabled="html.dom.filter.search.disabled"
													:placeholder="html.dom.filter.search.placeholder"
												>
												</search>
											</div>
											<div class="col-sm-9">
												<div class="row">
													<div :class="showCM ? 'col-sm-3' : 'col-sm-4'">
														<div class="form-group">
															<label class="filter-label control-label">Giới Tính</label><br/>
															<p class="input-group-addon filter-lbl">
																<i v-b-tooltip.hover title="Lọc theo giới tính" class="fa fa-venus-mars"></i>
															</p>
															<select
																id="select_gender"
																name="search[gender]"
																class="filter-selection gender form-control"
																v-model="html.data.filter.gender"
																@change="html.dom.filter.gender.action"
																:disabled="html.dom.filter.gender.disabled"
																:placeholder="html.dom.filter.gender.placeholder"
															>
																<option value="">Lọc theo giới tính</option>
																<option value="M">Nam</option>
																<option value="F">Nữ</option>
															</select>
														</div>
													</div>
													<div :class="showCM ? 'col-sm-3' : 'col-sm-4'">
														<div class="form-group">
															<label class="filter-label control-label">Loại Khách Hàng</label><br/>
															<p class="input-group-addon filter-lbl">
																<i v-b-tooltip.hover title="Lọc theo loại khách hàng" class="fa fa-diamond"></i>
															</p>
															<select
																id="select_customer_type"
																name="search[customer_type]"
																class="filter-selection customer-type form-control"
																v-model="html.data.filter.customer_type"
																@change="html.dom.filter.customer_type.action"
																:disabled="html.dom.filter.customer_type.disabled"
																:placeholder="html.dom.filter.customer_type.placeholder"
															>
																<option value="">Lọc theo loại khách hàng</option>
																<option value="0">Thường</option>
																<option value="1">VIP</option>
															</select>
														</div>
													</div>
													<div :class="showCM ? 'col-sm-3' : 'col-sm-4'">
														<div class="form-group">
															<label class="filter-label control-label">Nhân Viên EC</label><br/>
															<p class="input-group-addon filter-lbl">
																<i v-b-tooltip.hover title="Lọc theo nhân viên EC" class="fa fa-optin-monster"></i>
															</p>
															<select
																id="select_filter_ec"
																name="search[filter_ec]"
																class="filter-selection filter-ec form-control"
																v-model="html.data.filter.ec"
																@change="html.dom.filter.ec.action"
																:disabled="html.dom.filter.ec.disabled"
																:placeholder="html.dom.filter.ec.placeholder"
															>
																<option v-for="(ec, idx) in html.dom.filter.ec.data"
																	:value="ec.id"
																	:key="idx">
																	{{ ec.name }}
																</option>
															</select>
														</div>
													</div>
													<div class="col-sm-3" v-show="showCM">
														<div class="form-group">
															<label class="filter-label control-label">Nhân Viên CS</label><br/>
															<p class="input-group-addon filter-lbl">
																<i v-b-tooltip.hover title="Lọc theo nhân viên CM" class="fa fa-github-alt"></i>
															</p>
															<select
																id="select_filter_ec"
																name="search[filter_ec]"
																class="filter-selection filter-ec form-control"
																v-model="html.data.filter.cm"
																@change="html.dom.filter.cm.action"
																:disabled="html.dom.filter.cm.disabled"
																:placeholder="html.dom.filter.cm.placeholder"
															>
																<option v-for="(cm, idx) in html.dom.filter.cm.data"
																	:value="cm.id"
																	:key="idx">
																	{{ cm.name }}
																</option>
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
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
									<th>Trạng Thái</th>
									<th>Học Lớp</th>
									<th>Giới tính</th>
									<th>Ngày sinh</th>
									<th>Phụ huynh</th>
									<th>Điện thoại</th>
									<th>EC</th>
									<th>Trung tâm</th>
									<th width="50">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, index) in list" v-bind:key="index">
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{ (parseInt(html.pagination.cpage) -1) *parseInt(html.pagination.limit) + index + 1 }}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										<img width="36px" :src="`${item.student_avatar}`" class="avatar" />
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.student_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.crm_id}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.accounting_id}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.source | studentSource}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item | studentsStatus}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.class_name, item.class_status | displayClass}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.student_gender | genderToName}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.student_birthday}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.parent_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.parent_mobile}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.ec_name}}
										</router-link></td>
									<td><router-link v-b-tooltip.hover class="link-me" :title="`${html.page.title}`" :to="`${html.target}${item.id}`">
										{{item.branch_name}}
										</router-link></td>
									<td class="text-center">
										<span class="apax-btn detail">
											<router-link
												title="Chi tiết học sinh"
												:to="`${html.page.url.link}${item.id}`">
												<i class="fa fa-eye"></i>
											</router-link>
										</span>
										<span v-if="item.total_care > 0" class="apax-btn edit">
											<router-link v-b-tooltip.hover
												class="link-me"
												title="Xem lịch sử chăm sóc"
												:to="`${html.target}${item.id}`">
												<i class="fa fa-vcard"></i>
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
	import u from '../../../utilities/utility'

	export default {

	components: {
		suggestion,
		paging,
		search
	},

	data() {
		const model = u.m('students').list
		model.html.target = 'student-care/'
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
					action: () => this.selectStudentStatus(),
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
				student_status: ''
			}
    }
    model.html.order.by = 's.id'
	  model.cache.filter = model.html.data.filter
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
			return [84].indexOf(this.session.user.role_id) === -1
		}
	},

	methods: {
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
			this.html.data.filter.keyword = ''
			this.cache.data = data
			this.list = data.list
			this.html.pagination = data.pagination
		},
		searching(word = '') {
			const key = u.live(word) && word != '' ? word : this.html.data.filter.keyword
			this.cache.filter.keyword = key ? key : ''
			this.get(this.link(), this.load)
		},
		selectGender() {
			if (this.cache) {
				this.cache.filter.gender = this.html.data.filter.gender
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
