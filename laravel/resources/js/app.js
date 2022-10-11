import './bootstrap'
import Vue from 'vue'
import ArticleLike from './components/ArticleLike'

// bladeで#app要素の子要素として@yield('content')とあるので各bladeでVueコンポーネントを取り扱える
const app = new Vue({
  el: '#app',
  components: {
    ArticleLike,
  }
})