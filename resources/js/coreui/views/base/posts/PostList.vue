<template>
	<div class="animated fadeIn">
	    <div class="row">
	    	<div class="col-sm-12">
	        	<b-card>
		        	<div slot="header">
		            	<strong>Post list</strong>
		        	</div>
		        	<table class="table table-bordered">
		        		<thead>
		        			<tr>
		        				<th>ID</th>
		        				<th width="120">Featured</th>
		        				<th>Title</th>
		        				<th>Content</th>
		        				<th>Category</th>
		        				<th>Action</th>
		        			</tr>
		        		</thead>
		        		<tbody>
		        			<tr v-for="post in posts">
		        				<td>{{post.id}}</td>
		        				<td><img :src="`/${post.featured}`" class="avatar"></td>
		        				<td>{{post.title}}</td>
		        				<td>{{post.content}}</td>
		        				<td>{{post.category_id}}</td>
		        				<td><a href="">Edit</a></td>
		        			</tr>
		        		</tbody>
		        	</table>
		        	<nav>
			            <ul class="pagination">
			              <li class="page-item"><a class="page-link" href="#">Prev</a></li>
			              <li class="page-item"><a class="page-link" href="#">1</a></li>
			              <li class="page-item"><a class="page-link" href="#">2</a></li>
			              <li class="page-item"><a class="page-link" href="#">3</a></li>
			              <li class="page-item"><a class="page-link" href="#">4</a></li>
			              <li class="page-item"><a class="page-link" href="#">Next</a></li>
			            </ul>
		          	</nav>

		        </b-card>
		    </div>
		</div>
	</div>
</template>
<style scoped>
	tr>td>img{
		width: 100px;

	}
</style>

<script>
	var pagePagination = {
		currentPage: 1,
		totalPages: 4+5,
		pageSize: 5,
		text: 'next'
	}
	import axios from 'axios';
	export default {
		data(){
			return {
				posts: [],
				visiblePosts: [],
				page: pagePagination
			}
		},
		created(pageNo){
			axios.get(`/api/posts`).then(response => {
				this.posts = response.data;
				// this.currentPage = response.data.current_page
			})
		},
		methods: {
			updateVisiblePosts(){
				
			}
		}
	}
	
</script>
