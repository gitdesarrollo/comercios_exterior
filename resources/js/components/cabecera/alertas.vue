<template>
  <div>
    <li class="nav-item dropdown no-arrow mx-1">
      <a
        class="nav-link dropdown-toggle"
        href="#"
        id="messagesDropdown"
        role="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <i class="fas fa-envelope fa-fw"></i>
        <span class="badge badge-danger badge-counter">{{ countMessage }}</span>
      </a>
      <div
        class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
        aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">Mensajes</h6>
        <a class="dropdown-item d-flex align-items-center" href="#">
          <div class="dropdown-list-image mr-3">
            <!-- <img class="rounded-circle" src="img/undraw_posting_photo.svg" alt=""> -->
            <div class="status-indicator bg-success"></div>
          </div>
          <div class="font-weight-bold" v-for="(datos,x) in list_response.listMessage" :key="x">
            <div class="text-truncate">{{ datos.dirigido }}</div>
            <div class="small text-gray-500">{{ datos.direccion }}</div>
          </div>
        </a>
        <a class="dropdown-item text-center small text-gray-500" href="#">Leer todos los mensajes</a>
      </div>
    </li>
  </div>
</template>
<script>
export default {
 
  data() {
    return {
      countMessage: 0,
      url_list: {
        message: "getRecepcionMessage",
      },
      list_response: {
        list_message: 0,
        listMessage:[]
      },
    };
  },
  mounted() {
    this.getMessage();
  },
  methods: {
    getMessage() {
      axios.get(this.url_list.message).then((response) => {
        this.countMessage = response.data.length;
        this.list_response.listMessage = response.data;
        console.log(response.data);
      });
    },
  },
};
</script>