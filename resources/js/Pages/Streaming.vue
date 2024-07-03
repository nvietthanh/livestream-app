<template>
  <div class="container mx-auto">
    <h1 class="text-center">Laravel Video Chat</h1>
    <div class="video-main">
      <div class="flex gap-4">
        <div class="flex-1 video-container">
          <video class="screen-main" ref="viewBroadcaster" autoplay></video>
          <!-- <div class="video-here-main">
            <video class="video-here" ref="video-here" autoplay></video>
          </div> -->
        </div>
        <div class="w-[100px] flex flex-col">
          <div class="flex-1">
            <div v-for="(user, index) in listUser" :key="index">
              {{ user.name }}
            </div>
          </div>
        </div>
      </div>
      <!-- <div class="flex flex-col">
        <div
          class="flex flex-col-reverse max-h-[300px] h-[300px] border w-full overflow-y-scroll"
        >
          <div class="py-[8px] flex flex-col-reverse">
            <div
              v-for="item in listMessage"
              :key="item.id"
              class="px-[12px] py-[8px] border-b-[1px] flex gap-[8px]"
            >
              <div class="font-bold">{{ item.user.name }}:</div>
              <div>
                {{ item.content }}
              </div>
            </div>
          </div>
        </div>
        <div class="flex w-full gap-2">
          <input
            type="text"
            v-model="formData.content"
            class="w-full"
            @keypress.enter.prevent="sendMessage()"
          />
          <button @click="sendMessage()">Send</button>
        </div>
      </div> -->
    </div>
  </div>
</template>
<script>
import SimplePeer from "simple-peer";
import axios from "axios";
import Peer from 'peerjs';

export default {
  data() {
    return {
      user: this.$page.props.auth.user,
      listUser: [],
      channelId: this.appRoute().params.id,
      listMessage: [],
      formData: {
        content: null,
      },

      channel: null,
      stream: null,
      peers: {},
      myPeer: {}
    };
  },
  mounted() {
    this.setupStream();

    let roomId = `room_${this.channelId}`
    let peer = new Peer()
    peer.on('open', (id) => {
        console.log("Connected room with Id: " + roomId)

        const createMediaStreamFake = () => {
            return new MediaStream([createEmptyAudioTrack(), createEmptyVideoTrack({ width: 640, height: 480 })]);
        }

        const createEmptyAudioTrack = () => {
            const ctx = new AudioContext();
            const oscillator = ctx.createOscillator();
            const dst = oscillator.connect(ctx.createMediaStreamDestination());
            oscillator.start();
            const track = dst.stream.getAudioTracks()[0];
            return Object.assign(track, { enabled: false });
        }

        const createEmptyVideoTrack = ({ width, height }) => {
            const canvas = Object.assign(document.createElement('canvas'), { width, height });
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = "green";
            ctx.fillRect(0, 0, width, height);

            const stream = canvas.captureStream();
            const track = stream.getVideoTracks()[0];

            return Object.assign(track, { enabled: false });
        };

        let call = peer.call(roomId, createMediaStreamFake())
        console.log(roomId)
        call.on('stream', (stream) => {
          const screenMain = this.$refs["viewBroadcaster"];
          screenMain.srcObject = stream;
        })

    })

    // window.Echo.private('client-channel.' + this.channelId)
    //   .listen("StreamOffer", ({ data }) => {
    //       this.broadcasterId = data.broadcaster;
    //       this.createViewerPeer(data.offer, data.broadcaster);
    //     }
    //   );
  },
  methods: {
    setupStream() {
      this.channel = Echo.join("client-channel." + this.channelId);
      this.pusherUserChannel();
      this.getPusherInstance();
    },
    pusherUserChannel() {
      this.channel.here((users) => {
          this.listUser = users;
          console.log("List user", this.listUser);
        })
        .joining((user) => {
          this.listUser.push(user);
        })
        .error((error) => {
          console.error(error);
        });
    },
    getPusherInstance() {
      this.channel.listen("SendMessageEvent", (e) => {
          let data = e.data;
          this.listMessage.unshift(data);
        })
        .error((error) => {
          console.error(error);
        });
    },
    createViewerPeer(incomingOffer, broadcaster) {
      // Initialize Peer events for connection to remote peer
      this.handlePeerEvents(
        incomingOffer,
        broadcaster,
        this.removeBroadcastVideo
      );
    },
    handlePeerEvents(incomingOffer, broadcaster, cleanupCallback) {
      peer.on("signal", (data) => {
      });
      peer.on("stream", (stream) => {
        // display remote stream
      });
      peer.on("track", (track, stream) => {
        const screenMain = this.$refs["viewBroadcaster"];
        screenMain.srcObject = stream;
        console.log("onTrack");
      });
      peer.on("connect", () => {
        console.log("Viewer Peer connected");
      });
      peer.on("close", () => {
        console.log("Viewer Peer closed");
        peer.destroy();
        cleanupCallback();
      });
      peer.on("error", (err) => {
        console.log("handle error gracefully");
      });
      const updatedOffer = {
        ...incomingOffer,
        sdp: `${incomingOffer.sdp}\n`,
      };
      peer.signal(updatedOffer);
    },
    removeBroadcastVideo() {
      console.log("removingBroadcast Video");
      alert("Livestream ended by broadcaster");
      const tracks = this.$refs.viewer.srcObject.getTracks();
      tracks.forEach((track) => {
        track.stop();
      });
      this.$refs.broadcaster.srcObject = null;
    },
    sendMessage() {
      axios.post(this.appRoute("send-message", this.channelId), this.formData)
        .then(({ data }) => {
          this.formData.content = null;
          this.listMessage.unshift(data.data);
        });
    },
  },
};
</script>
<style>
.video-main {
  width: 800px;
  height: 280px;
  margin: 8px auto;
}
.video-container {
  height: 100%;
  position: relative;
  border: 3px solid #000;
  box-shadow: 1px 1px 1px #9e9e9e;
}
.video-here {
  width: 130px;
  position: absolute;
  left: 10px;
  bottom: 16px;
  border: 1px solid #000;
  border-radius: 2px;
  z-index: 2;
}
.screen-main {
  width: 100%;
  height: 100%;
  z-index: 1;
}
</style>