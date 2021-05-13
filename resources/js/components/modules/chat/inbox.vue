<template>
  <div>
      <b-card
        border-variant="primary"
        header="INBOX"
        header-bg-variant="primary"
        header-text-variant="white"
      >
        <div>
          <b-tabs
            active-nav-item-class="font-weight-bold text-uppercase text-danger"
            active-tab-class="font-weight-bold text-success"
            content-class="mt-3"
          >
            <b-tab title="Mensajes" active>
              <el-table :data="this.API.response.messages " :show-header="false">
                <el-table-column>
                  <template slot-scope="scope">
                    <el-link  :underline="false" :href="`social/${ scope.row.code }`"  >
                       {{ scope.row.externo }}
                    </el-link>
                  </template>
                </el-table-column>
                <el-table-column prop="fecha" align="right">
                </el-table-column>
              </el-table>
            </b-tab>
          </b-tabs>
        </div>
      </b-card>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        API:{
          route:{
            inboxMessage: "inbox-message"
          },
          response: {
            messages: []
          }
        }
      }
    },
    methods: {
     getMessage(){
       axios.get(this.API.route.inboxMessage)
        .then(response => {
          this.API.response.messages = response.data
          console.log(response.data);
        })
     } 
    },
    mounted() {
      this.getMessage();
    }
  }
</script>

<style lang="scss" scoped>

</style>