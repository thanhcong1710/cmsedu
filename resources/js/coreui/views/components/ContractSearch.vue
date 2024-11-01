<template>
    <div class="suggetion-search">
        <input type="hidden" :v-model="selectedContract" />
        <autocomplete
                url="/api/tuition-transfers/suggest/"
                anchor="contract_name"
                label="label"
                name="search_suggest_contract"
                id="sugget_contract"
                className="search-autocomplete"
                :title="title"
                :classes="{ wrapper: 'form-wrapper', input: 'form-control', list: 'data-list', item: 'data-list-item' }"
                :min="3"
                :debounce="10"
                :filterByAnchor="true"
                :onShouldGetData="onSearchContract"
                :onSelect="selectItem">
        </autocomplete>
        <div :class="{active: isLoading}" class="icon-group loading"><img src="/static/img/images/loading/tgl.gif"></div>
        <div :class="{active: !isLoading}" class="icon-group search"><i v-b-tooltip.hover title="Nhập thông tin tìm kiếm" class="fa fa-search"></i></div>
    </div>
</template>

<style scoped language="scss">
    .suggetion-search {
        width:100%;
        position: relative;
    }
    .suggetion-search .icon-group {
        position: absolute;
        right: 0;
        padding:1px;
        height: 35px;
        width: 43px;
        top:0;
        display: none;
    }
    .suggetion-search .icon-group.active {
        display: block;
    }
    .suggetion-search .icon-group.loading {
        padding: 4px 0 0 0;
        text-align: center;

    }
    .suggetion-search .icon-group.search {
        border: 1px solid #cacaca;
        background: #ffffff;
        text-align: center;
        padding: 6px 4px 0 5px;
    }
</style>

<script>
    import u from '../../utilities/utility'
    import Autocomplete from 'vue2-autocomplete-js'

    export default {
        props:{
            title: String,
            endpoint: {
                type: Number,
                default: 0
            },
            // selectedStudent: Object,
            responseContracts: Array,
            onSelectContract: Function,
            beforeSearchContract: Function,
            afterSearchContract: Function,
            onSearchContract: Function
        },
        data () {
            return {
                name: '',
                selectedContract: Object,
                isLoading: false,
                searchClass: {
                    active: this.ajaxloading ? false : true,
                    'icon-group': true,
                    search: true
                }
            }
        },
        components: {
            Autocomplete
        },
        methods: {
            selectItem(contract) {
                this.$emit('doSelectContract', contract);
                this.onSelectContract ? this.onSelectContract(contract) : null;
            }
        }
    }
</script>
