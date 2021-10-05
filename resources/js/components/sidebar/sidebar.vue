<template>
  <div>
    <el-row class="tac mt-2">
      <el-col :span="24">
        <h5>Módulos</h5>
        <el-menu
          default-active="0"
          class="el-menu-vertical-demo"
          @open="handleOpen"
          @close="handleClose"
          background-color="#002a49"
          text-color="#fff"
          active-text-color="#ffd04b"
        >
          <el-submenu
            :index="index.idPadre + ''"
            v-for="(index, x) in response_list.ParentsChildren"
            :key="x"
          >
            <template slot="title">
              <i class="el-icon-location"></i>
              <span>{{ index.namePadre }}</span>
            </template>
            <el-menu-item
              :index="y + ''"
              v-for="(children, y) in index.children"
              :key="y"
            >
              <router-link to="prueba">
                {{ children.description }}
              </router-link>
              <!-- @click="toRoute(child.path)" -->
              <!-- {{ children.description }} -->
            </el-menu-item>
          </el-submenu>
        </el-menu>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      route: {
        get: {
          getSidebarItemParents: "getSidebarItemParents",
        },
        post: {
          getSidebarItemChildren: "getSidebarItemChildren",
        },
      },
      response_list: {
        ParentsChildren: [],
      },
    };
  },
  mounted() {
    this.getItemMenu();
  },
  methods: {
    getItemMenu() {
      const requestOne = axios.get(this.route.get.getSidebarItemParents);

      axios.all([requestOne]).then(
        axios.spread((responseOne) => {
          responseOne.data.forEach((element) => {
            axios
              .post(this.route.post.getSidebarItemChildren, {
                idPadre: element.id,
              })
              .then((responseTwo) => {
                this.response_list.ParentsChildren.push({
                  idPadre: element.id,
                  namePadre: element.description,
                  children: responseTwo.data,
                });
              });
          });
        })
      );
    },
    handleOpen(key, keyPath) {
      console.log(key, keyPath);
    },
    handleClose(key, keyPath) {
      console.log(key, keyPath);
    },
  },
};
</script>

<style>
.el-link {
  /* padding-left: 40px !important; */
  /* el-menu-item is-active */
  color: rgb(255, 208, 75) !important;
  background-color: rgb(0, 42, 73) !important;
}
</style>


