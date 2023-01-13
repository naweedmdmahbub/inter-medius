import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)


const routes = [
//   {
//       path: '/products',
//       component: () => import('./components/Products/List.vue'),
//       name: 'ProductList',
//       meta: { noCache: true },
//   },
  {
      path: '/product/create',
      component: () => import('./components/CreateProduct.vue'),
      name: 'CreateProduct',
      meta: { noCache: true },
  },
  {
      path: '/product/:id/edit',
      component: () => import('./components/EditProduct.vue'),
      name: 'EditProduct',
      meta: { noCache: true },
  },
]


const router = new VueRouter({
    routes,
    mode: 'history'
})

export default router