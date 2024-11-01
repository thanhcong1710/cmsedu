<template>
  <div class="apax-search default" :id="id" :class="customClass" @keyup="searchEnter">
    <div class="input-prepend input-group">
      <span class="input-group-addon"><i v-b-tooltip.hover :title="label" class="fa fa-search"></i></span>
      <input class="form-control" style="width: auto; margin-left: 0px;" :name="name" :size="size" type="text" :placeholder="placeholder" :disabled="disabled" v-model="search"
          @blur="handleBlur"
          @focus="handleFocus"
          @input="handleInput"
          @keydown="handleKeydown"
          @mouseover="handleHover"
          @mouseleave="handleLeave"
          @dblclick="handleDoubleClick"
        />
      <button type="button" @click="searchBy(search)" class="input-group-addon btn btn-success search-button">Tìm</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'apax-button',
  data (){
    return {
      search: ''
    }
  },
  props: {
    id: {
      type: String,
      default: null
    },
    size: {
      type: Number,
      default: 18
    },
    name: {
      type: String,
      default: null
    },
    label: {
      type: String,
      default: null
    },
    customClass: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    placeholder: {
      type: String,
      default: null
    },
    onBlur: Function,
    onFocus: Function,
    onInput: Function,
    onHover: Function,
    onLeave: Function,
    onSearch: Function,
    onKeydown: Function,
    onDoubleClick: Function,
  },
  methods: {
    info(e) {
      return {
        event: e,
        name: this.name,
        id: this.id,
        class: this.customClass
      }
    },
    searchBy (keyword) {
      this.onSearch ? this.onSearch(keyword) : null
      this.$emit('click', keyword)
    },
    searchEnter(e) {
      if(e.key == "Enter"){
        this.searchBy(this.search)
      }
    },
    handleBlur (e) {
      const resp = this.info(e)
      this.onBlur ? this.onBlur(resp) : null
      this.$emit('blur', resp)
    },
    handleInput (e) {
      const resp = this.info(e)
      this.onInput ? this.onInput(resp) : null
      this.$emit('input', resp)
    },
    handleKeydown (e) {
      const resp = this.info(e)
      this.onKeydown ? this.onKeydown(resp) : null
      this.$emit('keydown', resp)
    },
    handleFocus (e) {
      const resp = this.info(e)
      this.onFocus ? this.onFocus(resp) : null
      this.$emit('focus', resp)
    },
    handleHover (e) {
      const resp = this.info(e)
      this.onHover ? this.onHover(resp) : null
      this.$emit('mouseover', resp)
    },
    handleLeave (e) {
      const resp = this.info(e)
      this.onLeave ? this.onLeave(resp) : null
      this.$emit('mouseleave', resp)
    },
    handleDoubleClick (e) {
      const resp = this.info(e)
      this.onDoubleClick ? this.onDoubleClick(resp) : null
      this.$emit('dblclick', resp)
    }
  }
}
</script>

<style lang="scss" scoped>
  .apax-search {
    max-width:100%;
    margin: 0 0 15px 0;
  }
</style>
