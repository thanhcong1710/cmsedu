<template>
	<div class="table-responsive scrollable">
		<div class="dashboad-title">
			{{tableName}}		
		</div>
		<table class="table table-striped table-bordered apax-table">
			<thead>
				<tr class="text-sm">
					<th>Mã Leader</th>
					<th>Tên Leader</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				</tr>
			</tbody>
		</table>
		<b-modal :title="modalTitle" class="reNew-Modal" v-model="isOpen" @ok="isOpen = false">
			<table class="table table-striped table-bordered apax-table">
				<thead>
					<tr class="text-sm">
						<th class="width-20">STT</th>
						<th class="width-50">Mã LMS</th>
						<th class="width-50">Mã Effect</th>
						<th class="width-50">Tên học sinh</th>
						<th class="width-50">Sản phẩm</th>
						<th class="width-50">Chương trình</th>
						<th class="width-50">Lớp</th>
						<th class="width-50">Loại khách hàng</th>
						<th class="width-50">Gói phí</th>
						<th class="width-50">Ngày đóng phí</th>
						<th class="width-50">Số tiền phải đóng</th>
						<th class="width-50">Đã đóng</th>
						<th class="width-50">Công nợ</th>
						<th class="width-50">EC</th>
						<th class="width-50">CM</th>
					</tr>
				</thead>
				<tbody>
					<tr></tr>
				</tbody>
			</table>
		</b-modal>
	</div>
</template>
<script>
import axios from 'axios';

export default {
	components: { },
	props: ["tableName"],
	data(){
		return {
			modalTitle: '',
			isOpen: false
		}
	},
	methods:{
		clickModal(item, index){
			this.isOpen = true;
		}
	}
}
</script>