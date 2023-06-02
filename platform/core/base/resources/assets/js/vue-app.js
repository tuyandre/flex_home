import Vue from 'vue'
import emitter from 'tiny-emitter/instance'
import sanitizeHTML from 'sanitize-html'
import _ from 'lodash'
import { BootstrapVue } from 'bootstrap-vue'

class VueApp {
    constructor() {
        this.vue = Vue
        this.vue.use(BootstrapVue)

        this.vue.prototype.__ = (key) => {
            if (typeof window.trans === 'undefined') {
                return key
            }

            return _.get(window.trans, key, key)
        }

        this.vue.prototype.$sanitize = sanitizeHTML

        this.bootingCallbacks = []
        this.bootedCallbacks = []
        this.vueInstance = null
        this.hasBooted = false
    }

    booting(callback) {
        this.bootingCallbacks.push(callback)
    }

    booted(callback) {
        this.bootedCallbacks.push(callback)
    }

    boot() {
        for (const callback of this.bootingCallbacks) {
            callback(this.vue)
        }

        this.vueInstance = new this.vue({
            el: '#app',
        })

        for (const callback of this.bootedCallbacks) {
            callback(this)
        }

        this.hasBooted = true
    }
}

window.vueApp = new VueApp()
window.$event = emitter

document.addEventListener('DOMContentLoaded', () => {
    if (! window.vueApp.hasBooted) {
        window.vueApp.boot()
    }
})
