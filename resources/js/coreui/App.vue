<!--
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 *  ApaxEnglish ERP System
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package ApaxEnglish ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */-->

<template>
  <div class="general-frame-container">
    <notifications class="apax-alert ptc" width="33%" group="apax-atc" position="top center">
      <template slot="body" slot-scope="props">
        <div class="apax-alert-content" :class="`alert-frame ${props.item.type}`">
            <div class="apax-alert-button close" @click="props.close">
              <i class="fa fa-fw fa-close"></i>
            </div>
            <div class="header">
              <h5 class="title">{{props.item.title}}</h5>
            </div>
            <div class="body">
              <div v-html="props.item.text"></div>
            </div>
        </div>
      </template>
    </notifications>
    <notifications class="apax-alert ptl" width="25%" group="apax-atl" position="top left">
      <template slot="body" slot-scope="props">
        <div class="apax-alert-content" :class="`alert-frame ${props.item.type}`">
            <div class="apax-alert-button close" @click="props.close">
              <i class="fa fa-fw fa-close"></i>
            </div>
            <div class="header">
              <h5 class="title">{{props.item.title}}</h5>
            </div>
            <div class="body">
              <div v-html="props.item.text"></div>
            </div>
        </div>
      </template>
    </notifications>
    <notifications class="apax-alert ptr" width="25%" group="apax-atr" position="top right">
      <template slot="body" slot-scope="props">
        <div class="apax-alert-content" :class="`alert-frame ${props.item.type}`">
            <div class="apax-alert-button close" @click="props.close">
              <i class="fa fa-fw fa-close"></i>
            </div>
            <div class="header">
              <h5 class="title">{{props.item.title}}</h5>
            </div>
            <div class="body">
              <div v-html="props.item.text"></div>
            </div>
        </div>
      </template>
    </notifications>
    <notifications class="apax-alert pbl" width="25%" group="apax-abl" position="bottom left">
      <template slot="body" slot-scope="props">
        <div class="apax-alert-content" :class="`alert-frame ${props.item.type}`">
            <div class="apax-alert-button close" @click="props.close">
              <i class="fa fa-fw fa-close"></i>
            </div>
            <div class="header">
              <h5 class="title">{{props.item.title}}</h5>
            </div>
            <div class="body">
              <div v-html="props.item.text"></div>
            </div>
        </div>
      </template>
    </notifications>
    <notifications class="apax-alert pbr" width="25%" group="apax-abr" position="bottom right">
      <template slot="body" slot-scope="props">
        <div class="apax-alert-content" :class="`alert-frame ${props.item.type}`">
            <div class="apax-alert-button close" @click="props.close">
              <i class="fa fa-fw fa-close"></i>
            </div>
            <div class="header">
              <h5 class="title">{{props.item.title}}</h5>
            </div>
            <div class="body">
              <div v-html="props.item.text"></div>
            </div>
        </div>
      </template>
    </notifications>
    <notifications class="apax-alert pbc" width="33%" group="apax-abc" position="bottom center">
      <template slot="body" slot-scope="props">
        <div class="apax-alert-content" :class="`alert-frame ${props.item.type}`">
            <div class="apax-alert-button close" @click="props.close">
              <i class="fa fa-fw fa-close"></i>
            </div>
            <div class="header">
              <h5 class="title">{{props.item.title}}</h5>
            </div>
            <div class="body">
              <div v-html="props.item.text"></div>
            </div>
        </div>
      </template>
    </notifications>
    <notifications position="top center" group="form_upload" />
    <modal name="apax-modal"
      :delay="modal_delay"
      :reset="modal_reset"
      :clickToClose="modal_click_to_close"
      :draggable="modal_drag"
      :resizable="modal_resize"
      :adaptive="modal_adaptive"
      :transition="modal_transition"
      :classes="modal_classes"
      :width="modal_width"
      :height="modal_height"
      :pivotX="modal_postion_x"
      :pivotY="modal_postion_y"
      @before-open="modal_before_open"
      @opened="modal_when_opened"
      @before-close="modal_before_close"
      @closed="modal_when_closed"
      >
      <div class="apax-modal-content-frame ada-modal" :class="modal_class">
        <header class="modal-header">
          <h5 class="modal-title"><span v-html="modal_title"></span></h5>
          <button @click="$modal.hide('apax-modal')" class="close">×</button>
        </header>
        <div class="modal-body" v-html="modal_body"></div>
        <footer class="modal-footer">
          <div class="footer-content" v-html="modal_footer"></div>
        </footer>
      </div>
    </modal>
    <b-modal
      v-model="popup"
      @ok="popup_confirm"
      @hidden="popup_hidden"
      :size="popup_size"
      :title="popup_title"
      :class="popup_class"
      :centered="popup_center"
      :variant="popup_variant"
      >
      <div class="apax-popup-content-detail d-block" v-html="popup_content"></div>
      <div slot="modal-footer" class="w-100">
        <p class="float-left"><span v-html="popup_footer"/></p>
        <b-button v-if="popup_confirm.primary && popup_confirm.primary.button !== ''" variant="primary" size="sm" class="float-right" @click="primary_confirm">
          <span v-html="popup_confirm.primary.button" />
        </b-button>
        <b-button v-if="popup_confirm.secondary && popup_confirm.secondary.button !== ''" variant="secondary" size="sm" class="float-right" @click="success_confirm">
          <span v-html="popup_confirm.secondary.button" />
        </b-button>
        <b-button v-if="popup_confirm.success && popup_confirm.success.button !== ''" variant="success" size="sm" class="float-right" @click="success_confirm">
          <span v-html="popup_confirm.success.button" />
        </b-button>
        <b-button v-if="popup_confirm.info && popup_confirm.info.button !== ''" variant="info" size="sm" class="float-right" @click="info_confirm">
          <span v-html="popup_confirm.info.button" />
        </b-button>
        <b-button v-if="popup_confirm.warning && popup_confirm.warning.button !== ''" variant="warning" size="sm" class="float-right" @click="warning_confirm">
          <span v-html="popup_confirm.warning.button" />
        </b-button>
        <b-button v-if="popup_confirm.danger && popup_confirm.danger.button !== ''" variant="danger" size="sm" class="float-right" @click="danger_confirm">
          <span v-html="popup_confirm.danger.button" />
        </b-button>
        <b-button v-if="popup_confirm.light && popup_confirm.light.button !== ''" variant="light" size="sm" class="float-right" @click="light_confirm">
          <span v-html="popup_confirm.light.button" />
        </b-button>
        <b-button v-if="popup_confirm.dark && popup_confirm.dark.button !== ''" variant="dark" size="sm" class="float-right" @click="dark_confirm">
          <span v-html="popup_confirm.dark.button" />
        </b-button>
      </div>
    </b-modal>
    <router-view></router-view>
  </div>
</template>

<script>
import u from './utilities/utility'
import Vue from 'vue'
export default {
  name: 'app',
  data() {
    return {      
      modal_body: '',
      modal_class: '',
      modal_title: '',
      modal_delay: 0,
      modal_width: 600,
      modal_height: 300,
      modal_footer: '',
      modal_classes: '',
      modal_transition: '',
      modal_drag: true,
      modal_reset: true,
      modal_resize: true,
      modal_scroll: true,
      modal_adaptive: true,
      modal_postion_x: 0.5,
      modal_postion_y: 0.5,
      modal_click_to_close: true,
      modal_before_open: Function,
      modal_when_opened: Function,
      modal_when_closed: Function,
      modal_before_close: Function,
      redirect_url: '',
      popup: false,
      popup_size: 'md',
      popup_title: '',
      popup_class: '',
      popup_content: '',
      popup_center: false,
      popup_show: Function,
      popup_close: Function,
      popup_confirm: Function,
      popup_after_hidden: Function,
      popup_variant: '',
      popup_footer: '',
      popup_confirm: {
        primary: {
          button: '',
          action: Function
        },
        secondary: {
          button: '',
          action: Function
        },
        success: {
          button: '',
          action: Function
        },
        warning: {
          button: '',
          action: Function
        },
        danger: {
          button: '',
          action: Function
        },
        light: {
          button: '',
          action: Function
        },
        dark: {
          button: '',
          action: Function
        }
      }
    }
  },
  methods: {
    primary_confirm() {
      this.popup = false
      this.popup_confirm.primary.action()
    },
    secondary_confirm() {
      this.popup = false
      this.popup_confirm.secondary.action()
    },
    success_confirm() {
      this.popup = false
      this.popup_confirm.success.action()
    },
    info_confirm() {
      this.popup = false
      this.popup_confirm.info.action()
    },
    warning_confirm() {
      this.popup = false
      this.popup_confirm.warning.action()
    },
    danger_confirm() {
      this.popup = false
      this.popup_confirm.danger.action()
    },    
    light_confirm() {
      this.popup = false
      this.popup_confirm.light.action()
    },
    dark_confirm() {
      this.popup = false
      this.popup_confirm.dark.action()
    },
    popup_hidden() {
      this.popup_after_hidden()
      if (this.redirect_url) {
        this.$router.push(`/${this.redirect_url}`)
      }
    }
  },
  created() {
    u.apax.$on('apaxModal', (data) => {
      this.modal_body = data.body || ''
      this.modal_class = data.class || '',
      this.modal_title = data.title || '',
      this.modal_delay = data.delay || 0,
      this.modal_width = data.width || 600,
      this.modal_height = data.height || 300,
      this.modal_footer = data.footer || '',
      this.modal_classes = data.classes || '',
      this.modal_transition = data.transition || '',
      this.modal_drag = data.draggable || true,
      this.modal_reset = data.reset || true,
      this.modal_resize = data.resizeable || true,
      this.modal_scroll = data.scrollable || true,
      this.modal_adaptive = data.adaptive || true,
      this.modal_postion_x = data.posx || 0.5,
      this.modal_postion_y = data.posy || 0.5,
      this.modal_before_open = data.bopen || [],
      this.modal_when_opened = data.opened || [],
      this.modal_before_close = data.bclose || [],
      this.modal_when_closed = data.closed || [],
      this.modal_click_to_close = data.closeable || true
    })
    u.apax.$on('apaxPopup', (data) => {
      this.popup = data.on || false
      this.popup_footer = data.message || ''
      this.redirect_url = data.redirect_url || ''
      this.popup_show = data.show || Function
      this.popup_size = data.size || 'md'
      this.popup_title = data.title || ''
      this.popup_class = data.class || ''
      this.popup_close = data.close || Function
      this.popup_after_hidden = data.hidden || Function
      this.popup_center = data.center || false
      this.popup_content = data.content || ''
      this.popup_variant = data.varian || ''
      this.popup_confirm = data.action || Function
      this.popup_confirm = data.confirm || {
        primary: {
          button: '',
          action: Function
        },
        secondary: {
          button: '',
          action: Function
        },
        success: {
          button: '',
          action: Function
        },
        warning: {
          button: '',
          action: Function
        },
        danger: {
          button: '',
          action: Function
        },
        light: {
          button: '',
          action: Function
        },
        dark: {
          button: '',
          action: Function
        }
      }
    })
  },
  watch: {

  },
  method: {

  }
}
</script>

<style lang="scss">
  // CoreUI Icons Set
  @import '~@coreui/icons/css/coreui-icons.min.css';
  /* Import Font Awesome Icons Set */
  $fa-font-path: '~font-awesome/fonts/';
  @import '~font-awesome/scss/font-awesome.scss';
  /* Import Simple Line Icons Set */
  $simple-line-font-path: '~simple-line-icons/fonts/';
  @import '~simple-line-icons/dist/styles/simple-line-icons.css';
  /* Import Flag Icons Set */
  @import '~flag-icon-css/css/flag-icon.min.css';
  /* Import Bootstrap Vue Styles */
  @import '~bootstrap-vue/dist/bootstrap-vue.css';
  // Import Main styles for this application
  @import '../../sass/coreui/style';
</style>
