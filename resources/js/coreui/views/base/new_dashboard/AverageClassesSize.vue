<template>
	<div>
		<div class="table-responsive scroll-tb">
			<div class="dashboad-title">
				{{tableName}}
			</div>
			<div class="fixed-table">
				<table class="table table-striped table-bordered apax-table">
					<thead>
						<tr>
							<th class="width-250">Trung tâm</th>
							<th>Phòng học</th>
							<th>Lớp học tối đa</th>
							<th>Tổng số lớp thực tế</th>
							<th>Tổng số phí học sinh</th>
							<th>Toàn bộ phí</th>
							<th>Hiệu suất phòng</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>
<script>
import axios from 'axios';

export default {
	components: { },
	props: ['tableName'],
	data(){
		return {
		}
	},
	methods: {
	}
}
</script>
