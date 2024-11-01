<template>
	<div class="apax-pagination" :class="customClass" :id="id">
    <div class="paging-info" v-show="allPage === 'yes'">
      Đang hiển thị trang {{currentPage}}/{{lastPage}} với {{pagesLimit}} bản ghi mỗi trang, tổng số có {{lastPage}} trang {{pagesItems}} bản ghi
    </div>
    <div class="paging-info" v-show="allPage === 'no'">
      Đang hiển thị trang {{currentPage}}/{{lastPage}}, tổng số có {{lastPage}} trang
    </div>
    <div class="first page">
      <div v-b-tooltip.hover title="Về trang đầu tiên" class="link-me"  @click="navigate(linking(firstPage))"><i class="fa fa-angle-double-left"></i></div>
    </div>
    <div class="previous page">
      <div v-b-tooltip.hover title="Quay lại trang trước" class="link-me" @click="navigate(linking(previousPage))"><i class="fa fa-angle-left"></i></div>
    </div>
    <div :id="listType" class="pages list">
      <ul class="pagination paging-bar customer-type form-control">
        <li v-for="(page_item, idx) in pages_list" :key="idx" :class="checkSelected(page_item)">
          <div v-if="page_item == currentPage && listType == 'list'" class="link-me page_item selected">{{ page_item }}/{{ lastPage }}</div>
          <div v-else-if="listType == 'list'" class="link-me page_item" @click="navigate(linking(page_item))">{{ page_item }}/{{ lastPage }}</div>
          <div v-else-if="page_item == currentPage && listType == 'line'" class="link-me page_item selected">{{ page_item }}</div>
          <div v-else class="link-me page_item" @click="navigate(linking(page_item))">{{ page_item }}</div>
        </li>
      </ul>
      <input type="text" class="selected page" :placeholder="`${currentPage}/${ lastPage }`" @input="navigate(linking(jum_to_page = $event.target.value))" :value="jum_to_page" />
    </div>
    <div class="next page">
      <div v-b-tooltip.hover title="Sang trang kế tiếp" class="link-me" @click="navigate(linking(nextPage))"><i class="fa fa-angle-right"></i></div>
    </div>
    <div class="last page">
      <div v-b-tooltip.hover title="Tới trang cuối cùng" class="link-me" @click="navigate(linking(lastPage))"><i class="fa fa-angle-double-right"></i></div>
    </div>
  </div>
</template>

<script>
  import { range } from 'lodash'
	export default {
    name: "apax-pagination",
		props: {
      rootLink: {
        type: String,
        default: null
      },
      id: {
        type: String,
        default: null
      },
      listStyle: {
        type: String,
        default: 'line'
      },
      customClass: {
        type: String,
        default: null
      },
      firstPage: {
        type: Number,
        default: 1
      },
      previousPage: {
        type: Number,
        default: 1
      },
      currentPage: {
        type: Number,
        default: 1
      },
      pagesLimit: {
        type: Number,
        default: 1
      },
      pagesItems: {
        type: Number,
        default: 1
      },
      nextPage: {
        type: Number,
        default: 2
      },
      lastPage: {
        type: Number,
        default: 2
      },
      routing: Function,
      pagesList: {
        type: Array,
        default: () => {
          return []
        }
      },
      allPage: {
        type: String,
        default: "yes"
      },
    },
		data (){
			return {
        listType: '',
        jum_to_page: null,
        pages_list: []
			}
		},
		methods: {
      checkSelected(page_item) {
        return this.currentPage == page_item
      },
      linking(page_item) {
        const page = parseInt(page_item) > 0 ? page_item : 1
        return this.rootLink + '/' + page
      },
      navigate(link) {
        this.routing ? this.routing(link) : ''
        this.$emit('routing', link)
      }
    },
    computed: {

    },
		watch: {
      lastPage (){
        this.listType = this.lastPage > 9 ? 'list' : this.listStyle
        this.pages_list = range(1, this.lastPage+1)
      }
		}
	}
</script>

<style lang="scss" scoped>
  .apax-pagination {
    position: relative;
  }
  .apax-pagination .page {
    float:left;
    width:30px;
    height:30px;
    border-top:1px solid #c2cfd6;
    border-bottom:1px solid #c2cfd6;
  }
  .apax-pagination .page div {
    height:100%;
    width:100%;
    display: block;
    padding: 3px 0 0 0;
    color:#223b54;
    background:#223b54;
  }
  .apax-pagination .page div i {
    font-weight: 500;
    color:#FFF;
  }
  .apax-pagination .page:hover div {
    cursor:pointer;
    background:#9C0200;
    text-shadow: 0 1px 1px #111;
  }
  .apax-pagination .page:hover div i {
    font-weight: 500;
  }
  .apax-pagination .pages.list {
    display: none;
  }
  .apax-pagination #list, .apax-pagination #line {
    display: block;
  }
  .apax-pagination #list.pages {
    float:left;
    width:90px;
    height:30px;
    position: relative;
    border:1px solid #c2cfd6;
  }
  .apax-pagination #line.pages {
    float:left;
    height:30px;
    position: relative;
    border-top:1px solid #c2cfd6;
    border-right:1px solid #c2cfd6;
    border-bottom:1px solid #c2cfd6;
  }
  .apax-pagination #list input {
    position: absolute;
    width: 88px;
    border: 0;
    height: 28px;
    top: 0px;
    font-size:12px;
    text-align: center;
    left: 0;
  }
  .apax-pagination #line input {
    display:none;
  }
  .apax-pagination .pages ul {
    list-style-type: none;
  }
  .apax-pagination #line ul {
    float: left;
    margin: 0;
    padding: 0;
    height: 28px;
    border: 0;
    overflow: hidden;
  }
  .apax-pagination #line li {
    float:left;
    width:30px;
    height:30px;
    text-align:center;
    overflow:hidden;
    font-weight:700;
    border-left:1px solid #c2cfd6;
  }
  .apax-pagination #line li div {
    width:100%;
    height:100%;
    display:block;
    padding: 7px 0 0 0;
  }
  .apax-pagination #list ul {
    position: absolute;
    height: 300%;
    overflow-y: scroll;
    top: -300%;
    display:none;
  }
  .apax-pagination  #list:hover ul,
  .apax-pagination  #list ul:hover {
    display:block;
  }
  .apax-pagination  #list ul li {
    padding:3px 0;
    line-height:150%;
  }
  .apax-pagination .pages ul li {
    color:#555;
    font-size:12px;
    cursor:pointer;
  }
  .apax-pagination .pages ul li:hover {
    color:#223b54;
    text-shadow: 0 1px 1px #999;
  }
  .apax-pagination #line ul li:hover div {
    color:#FFF;
    font-weight:500;
    background:#223b54;
    text-shadow: 0 1px 1px #111;
  }
  .apax-pagination #line ul li .selected, .apax-pagination #line ul li:hover .selected {
    color:#fff;
    background:#9C0200;
  }
  .apax-pagination .pages ul li:hover .selected, .apax-pagination .pages ul li .selected {
    color:#9C0200;
  }
  .apax-pagination .first {
    border-left:1px solid #c2cfd6;
  }
  .apax-pagination .previous {
    border-left:1px solid #c2cfd6;
  }
  .apax-pagination .next {
    border-right:1px solid #c2cfd6;
  }
  .apax-pagination .last {
    border-right:1px solid #c2cfd6;
  }
  .paging-info {
    font-size:11px;
    color: #7f90a3;
    font-style: italic;
  }
  /* width */
::-webkit-scrollbar {
    width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #b3cbe5;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #779cc4;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #3a6896;
}
</style>
