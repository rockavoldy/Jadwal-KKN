<template>
  <v-layout wrap>
    <v-flex xs12 class="text-center">
      <span>KKN TEMATIK CITARUM HARUM</span>
      <br />
      <span>DESA SUKAMENAK, MARGAHAYU, BANDUNG</span>
      <br />
      <span>KELOMPOK 1</span>
    </v-flex>
    <v-flex xs12 class="my-2">
      <v-card class="pa-3">
        <v-sheet height="64">
          <v-toolbar flat color="white">
            <v-btn fab text small @click="calendarPrev">
              <v-icon small>mdi-chevron-left</v-icon>
            </v-btn>
            <v-btn text color="primary" class="mx-4" @click="setToday">Today</v-btn>
            <v-btn fab text small @click="calendarNext">
              <v-icon small>mdi-chevron-right</v-icon>
            </v-btn>
            <v-btn text color="primary" class="mx-4" @click="setDay">Change View</v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="primary" class="mx-4" @click="getWord">Export</v-btn>
          </v-toolbar>
        </v-sheet>
        <v-sheet height="600" class="pa-2">
          <v-calendar
            ref="calendar"
            v-model="focus"
            :events="events"
            :now="today"
            color="primary"
            @click:event="getEvent"
            @click:more="setDay"
            :type="type"
            locale="id-id"
          ></v-calendar>
          <v-menu
            v-model="selectedOpen"
            :close-on-content-click="false"
            :activator="selectedElement"
            full-width
            offset-x
          >
            <v-card color="grey lighten-4" min-width="350px" flat>
              <v-toolbar dark>
                <v-toolbar-title v-html="selectedEvent.title"></v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <span v-html="selectedEvent.details"></span>
                <div v-if="selectedEvent.img">
                  <v-img
                    :src="'https://kkn.akhmad.id/api/upload/'+selectedEvent.img"
                    max-width="400px"
                  ></v-img>
                </div>
                <div v-else>
                  <v-file-input v-model="selectImg" prepend-icon="mdi-camera" accept="image/*"></v-file-input>
                </div>
              </v-card-text>
              <v-card-actions>
                <v-btn v-if="!selectedEvent.img" text color="danger" @click="addPhoto">Update Foto</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="secondary" @click="selectedOpen = false">Cancel</v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>
        </v-sheet>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
import moment from "moment";
export default {
  data: () => ({
    focus: moment().format("YYYY-MM-DD"),
    today: moment().format("YYYY-MM-DD"),
    start: null,
    end: null,
    type: "month",
    events: [],
    selectedEvent: {},
    selectedElement: null,
    selectedOpen: false,
    selectImg: null
  }),
  mounted() {
    this.getJadwal();
  },
  methods: {
    moment: function() {
      return moment();
    },
    setToday() {
      this.focus = this.today;
      this.type = "month";
    },
    getEvent({ nativeEvent, event }) {
      const open = () => {
        this.selectedEvent = event;
        this.selectedElement = nativeEvent.target;
        setTimeout(() => (this.selectedOpen = true), 10);
      };

      if (this.selectedOpen) {
        this.selectedOpen = false;
        setTimeout(open, 10);
      } else {
        open();
      }

      nativeEvent.stopPropagation();
    },
    setDay({ date }) {
      this.focus = date;
      if (this.type === "month" ? (this.type = "day") : (this.type = "month"));
    },
    async getJadwal() {
      await this.$axios
        .get("kegiatan")
        .then(response => {
          this.events = response.data.data.map(el => {
            return {
              id: el.id,
              name: el.mulai + " " + el.deskripsi,
              title: el.mulai + " - " + el.akhir,
              details: el.deskripsi,
              start: moment(el.tanggal).format("YYYY-MM-DD"),
              end: moment(el.tanggal).format("YYYY-MM-DD"),
              img: el.img
            };
          });
        })
        .catch(e => {
          console.log(e);
        });
    },
    async addPhoto() {
      let formdata = new FormData();
      formdata.append("id", this.selectedEvent.id);
      formdata.append("img", this.selectImg);
      formdata.append("tanggal", this.selectedEvent.start);
      await this.$axios
        .post("foto", formdata, {
          headers: { "Content-Type": "multipart/form-data" }
        })
        .then(response => {
          alert("Berhasil update foto");
          location.reload();
        })
        .catch(e => {
          console.log(e);
          location.reload();
        });
    },
    getWord() {
      var fileLink = document.createElement("a");

      fileLink.href = "https://kkn.akhmad.id/api/genword";

      // fileLink.setAttribute("download", "laporan-harian.docx");

      document.body.appendChild(fileLink);

      fileLink.click();
      // await this.$axios.get("genword").then(response => {
      //   console.log(response);
      //   var fileURL = window.URL.createObjectURL(new Blob([response.data]));

      //   var fileLink = document.createElement("a");

      //   fileLink.href = fileURL;

      //   fileLink.setAttribute("download", "laporan-harian.docx");

      //   document.body.appendChild(fileLink);

      //   fileLink.click();
      // });
    },
    calendarPrev: function() {
      this.$refs.calendar.prev();
    },
    calendarNext: function() {
      this.$refs.calendar.next();
    }
  }
};
</script>

<style lang="stylus" scoped>
.event {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  border-radius: 2px;
  background-color: #1867c0;
  color: #ffffff;
  border: 1px solid #1867c0;
  width: 100%;
  font-size: 12px;
  padding: 3px;
  cursor: pointer;
  margin-bottom: 1px;
}

.more {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  border-radius: 2px;
  background-color: #f44336;
  color: #ffffff;
  border: 1px solid #f44336;
  width: 100%;
  font-size: 12px;
  padding: 3px;
  cursor: pointer;
  margin-bottom: 1px;
}
</style>
