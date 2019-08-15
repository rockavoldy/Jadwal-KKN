<template>
  <v-layout wrap>
    <v-flex xs12>
      <v-layout wrap class="justify-center">
        <v-flex md6>
          <v-card class="text-center pa-5 ma-5" color="grey lighten-3">
            <h2 display-3 class="text-center mb-3 mx-2">Tambah Kegiatan</h2>
            <v-form ref="tambahkegiatan">
              <v-select
                v-model="selectedUsers"
                :items="users"
                label="Users"
                placeholder="Yang mengikuti kegiatan"
                multiple
                chips
              >
                <template v-slot:prepend-item>
                  <v-list-item ripple @click="toggle">
                    <v-list-item-action>
                      <v-icon :color="selectedUsers.length > 0 ? 'indigo darken-4' : ''">{{ icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                      <v-list-item-title>Select All</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                  <v-divider class="mt-2"></v-divider>
                </template>
              </v-select>
              <v-menu
                v-model="menuTanggal"
                :close-on-content-click="false"
                full-width
                max-width="290"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    :value="dateFormat"
                    :placeholder="dateFormat"
                    clearable
                    label="Pilih tanggal"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="date" @change="menuTanggal = false"></v-date-picker>
              </v-menu>
              <v-menu
                v-model="menuMulai"
                :close-on-content-click="false"
                full-width
                max-width="290"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    :value="mulai"
                    :placeholder="mulai"
                    clearable
                    label="Waktu Mulai"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-time-picker format="24hr" v-model="mulai" @change="menuMulai = false"></v-time-picker>
              </v-menu>
              <v-menu
                v-model="menuAkhir"
                :close-on-content-click="false"
                full-width
                max-width="290"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    :value="akhir"
                    :placeholder="akhir"
                    clearable
                    label="Waktu Akhir"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-time-picker format="24hr" v-model="akhir" @change="menuAkhir = false"></v-time-picker>
              </v-menu>
              <v-textarea v-model="deskripsi" label="Deskripsi Kegiatan" placeholder="Deskripsi"></v-textarea>
              <v-file-input
                v-model="img"
                prepend-icon="mdi-camera"
                accept="image/*"
                label="Foto Kegiatan"
                placeholder="foto.jpg"
              >
                <template v-slot:selection="{ text }">
                  <v-chip small label color="primary">{{ text }}</v-chip>
                </template>
              </v-file-input>
              <v-btn color="primary" @click="kirimData">Simpan</v-btn>
            </v-form>
          </v-card>
        </v-flex>
      </v-layout>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from "moment";
export default {
  data: () => ({
    users: [],
    selectedUsers: [],
    menuTanggal: false,
    menuMulai: false,
    menuAkhir: false,
    date: moment().format("YYYY-MM-DD"),
    mulai: moment().format("HH:mm:ss"),
    akhir: moment().format("HH:mm:ss"),
    img: [],
    deskripsi: null
  }),
  mounted() {
    this.getUser();
  },
  computed: {
    dateFormat() {
      return this.date
        ? moment(this.date)
            .locale("id")
            .format("dddd, DD MMMM YYYY")
        : "";
    },
    selectAllUsers() {
      return this.selectedUsers.length === this.users.length;
    },
    selectSomeUsers() {
      return this.selectedUsers.length > 0 && !this.selectAllUsers;
    },
    icon() {
      if (this.selectAllUsers) return "check_box";
      if (this.selectSomeUsers) return "indeterminate_check_box";
      return "check_box_outline_blank";
    }
  },

  methods: {
    async getUser() {
      await this.$axios
        .get("user")
        .then(response => {
          this.users = response.data.data.map(el => {
            return {
              text: el.nickname,
              value: el.id,
              nim: el.nim
            };
          });
        })
        .catch(e => {
          console.log(e);
        });
    },
    toggle() {
      this.$nextTick(() => {
        if (this.selectAllUsers) {
          this.selectedUsers = [];
        } else {
          this.selectedUsers = [];
          this.users.forEach(el => {
            this.selectedUsers.push(el.value);
          });
        }
      });
    },
    async kirimData() {
      let formdata = new FormData();
      formdata.append("tanggal", this.date);
      formdata.append("mulai", this.mulai);
      formdata.append("akhir", this.akhir);
      formdata.append("deskripsi", this.deskripsi);
      formdata.append("img", this.img);
      formdata.append("user", this.selectedUsers);
      let post = await this.$axios
        .post("kegiatan", formdata, {
          headers: { "Content-Type": "multipart/form-data" }
        })
        .then(response => {
          alert("Berhasil");
          location.reload();
        })
        .catch(error => {
          console.log(error);
          alert("error");
          location.reload();
        });
    }
  }
};
</script>

<style>
</style>
