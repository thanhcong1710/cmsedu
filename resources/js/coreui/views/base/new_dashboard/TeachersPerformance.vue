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
							<th>Tổng số giáo viên</th>
							<th>Tổng số lớp</th>
							<th>Tổng số lớp thực tế</th>
							<th>Tổng học sinh toàn bộ gói</th>
							<th>Số lớp/ Giáo viên</th>
							<th>Toàn bộ học sinh/ Giáo viên</th>
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
