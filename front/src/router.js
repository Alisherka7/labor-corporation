import Vue from 'vue'
import Router from 'vue-router'

/////////////////////////////////////////////////////////////////////////////////////

import Home from './views/Home.vue'

import Help from './views/home/Help.vue'
// import Manage from './views/home/Manage.vue'
// import MyPage from './views/home/MyPage.vue'
import Receipt from './views/home/Receipt.vue'

/////////////////////////////////////////////////////////////////////////////////////

import About from './views/About.vue'

import Welcome from './views/about/Welcome.vue'
import Login from './views/about/Login.vue'

/////////////////////////////////////////////////////////////////////////////////////

import Test from './views/Test.vue'

/////////////////////////////////////////////////////////////////////////////////////


import Config from "./views/home/manage/Config";
import ReceiptManage from "./views/home/manage/ReceiptManage";
import UserManage from "./views/home/manage/UserManage";
import Privacy from "./views/home/mypage/Privacy";
import ReceiptHistory from "./views/home/mypage/ReceiptHistory";



Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/test',
      name: 'test',
      component: Test,
    },
    {
      path: '/about',
      name: 'about',
      component: About,
      children: [
        {
          path: 'welcome',
          name: 'welcome',
          component: Welcome,
        },
        {
          path: 'login',
          name: 'Login',
          component: Login,
        },
      ]
    },
    {
      path: '/home',
      name: 'home',
      component: Home,
      children: [
        {
          path: 'help',
          name: 'help',
          component: Help,
        },
        {
          path: 'config',
          name: 'config',
          component: Config,
        },
        
        {
          path: 'receipt',
          name: 'receipt',
          component: Receipt,
        },
        {
          path: 'receipt_manage',
          name: 'receipt_manage',
          component: ReceiptManage,
        },
        {
          path: 'privacy',
          name: 'privacy',
          component: Privacy,
        },
        {
          path: 'user_manage',
          name: 'user_manage',
          component: UserManage,
        },
        {
          path: 'receipt_history',
          name: 'receipt_history',
          component: ReceiptHistory,
        },
      ]
    },
  ]
})
