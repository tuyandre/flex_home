import iframeResize from 'iframe-resizer/js/iframeResizer'
import Plugins from './components/Plugins.vue'
import CardPlugin from './components/CardPlugin.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.directive('resize', {
            bind: function (el, { value = {} }) {
                el.addEventListener('load', () => iframeResize(value, el))
            },
            unbind: function (el) {
                el.iFrameResizer.removeListeners()
            },
        })
    })
}

vueApp.booting((vue) => {
    vue.component('marketplace-plugins', Plugins)
    vue.component('marketplace-card-plugin', CardPlugin)
})
