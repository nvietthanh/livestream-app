<template>
  <div class="container mx-auto">
    <h1 class="text-center">Laravel Video Chat</h1>
    <div>

      <div class="video-main">
        <div class="flex gap-4">
          <div class="flex-1 video-container">
            <video class="screen-main" ref="broadcaster" autoplay playsinline></video>
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
            <div>
              <button @click="startShareScreen()">Start share</button>
            </div>
          </div>
        </div>
        <div v-if="false" class="flex flex-col">
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
        </div>
      <div class="w-[700px] h-[300px] border">
        <video id="broadcaster2" ref="broadcaster2" autoplay></video>
      </div>
      </div>
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
      channelId: this.appRoute().params.id ?? this.$page.props.auth.user.id,
      listMessage: [],
      formData: {
        content: null,
      },

      channel: null,
      stream: null,
      peers: {},
      peerInstance: null,
    };
  },
  async created() {
    this.setupStream();
  },
  methods: {
    async startShareScreen() {
      const stream = await navigator.mediaDevices.getDisplayMedia({
        video: true,
        audio: true,
      });
      this.peerCreator(
        stream,
        this.user,
        this.signalCallback
      )
    },
    peerCreator(stream, user, signalCallback) {
      let roomId = `room_${this.channelId}`
      let peer = new Peer(roomId)
      peer.on('open', (id) => {
          console.log("Peer Room ID: ", roomId)
          const screenMain = this.$refs["broadcaster"];
          screenMain.srcObject = stream;
      })
      peer.on('call', (call) => {
          console.log("🚀 ~ peer.on ~ call:", call)
          call.answer(stream);
          call.on('stream', (stream) => {
              console.log("got call");
              // console.log(stream);
              // setRemoteStream(stream)
          })
          // currentPeer = call;
      })
    },
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

          this.peerCreator(
            this.$refs.broadcaster.srcObject,
            user,
            this.signalCallback
          )
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
    signalCallback(offer, user) {
        let peer = new SimplePeer({
          initiator: false,
          stream: this.stream,
          trickle: false
        });
        peer.on('signal', (data) => {
          // this.channel.trigger(`client-signal-${userId}`, {
          //   userId: this.user.id,
          //   data: data
          // });
        })
        .on('stream', (stream) => {
          console.log(1)
          const videoThere = this.$refs['broadcaster2'];
          videoThere.srcObject = stream;
        })
        .on('close', () => {
          const peer = this.peers[userId];
          if(peer !== undefined) {
            peer.destroy();
          }
          delete this.peers[userId];
        });
        peer.signal(offer);
      // let peer2 =  new SimplePeer({
      //   initiator: true,
      //   trickle: false,
      //   config: {
      //     iceServers: [
      //       {
      //         "urls": [
      //           "stun:stun.l.google.com:19302",
      //           "stun:stun1.l.google.com:19302",
      //           "stun:stun2.l.google.com:19302"
      //         ]
      //       },
      //       {
      //         "urls": "turn:numb.viagenie.ca",
      //         "credential": "muazkh",
      //         "username": "webrtc@live.com" 
      //       }
      //     ]
      //   }
      // })
      // peer2.on("stream", (stream) => {
      //   console.log(stream)
      //   this.$refs.broadcaster2.srcObject = stream
      //   // signalCallback(data, user);
      // });
      // peer2.signal(offer)
      // axios
      //   .post("/stream-offer", {
      //     broadcaster: this.channelId,
      //     receiver: user,
      //     offer,
      //   })
      //   .then((res) => {
      //     // console.log(res);
      //   })
      //   .catch((err) => {
      //     // console.log(err);
      //   });
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