<template>
  <div class="max-w-[1000px] mx-auto py-[32px]">
    <div>
      <button @click="startStreaming()">Start streaming</button>
    </div>
    <div class="mt-2">
      <div class="font-bold">List user online</div>
      <div class="border mt-2 p-[12px] grid grid-cols-5">
        <div
          v-for="(item, index) in users"
          :key="index"
          class="cursor-pointer"
          @click="startCall(item)"
        >
          <div class="flex items-center gap-2">
            <div
              class="relative rounded-[50%] bg-[#dad7d7] border flex items-center justify-center w-[40px] h-[40px]"
            >
              <span class="text-[red] font-bold">{{ item.name[0] }}</span>
              <div
                v-if="onlineUsers.includes(item.id)"
                class="absolute bottom-[2px] right-[2px] w-[10px] h-[10px] rounded-[50%] bg-[green]"
              />
            </div>
            <span>{{ item.name }}</span>
          </div>
        </div>
      </div>
      <div class="mt-2">
        <button @click="isEnableView = !isEnableView">Open camera</button>
      </div>
      <div class="border min-h-[100px] mt-2 flex" title="Streaming">
        <video ref="localView" autoplay class="w-1/2"></video>
        <video ref="remoteView" autoplay class="w-1/2"></video>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import Echo from "laravel-echo";

// const servers = {
//     iceServers: [
//         { urls: ["stun:bn-turn1.xirsys.com"] },
//         {
//             username:"LpTQzzNnOhyCNV2c-XzFWdQLfX9rjGUb_8A9FR82F59dG-y2bjDkk8hIxh5aEboqAAAAAGHaoB1rYXZpbg==",
//             credential: "2c2be25c-7128-11ec-b2b6-0242ac140004",
//             urls: [
//                 "turn:bn-turn1.xirsys.com:80?transport=udp",
//                 "turn:bn-turn1.xirsys.com:3478?transport=udp",
//                 "turn:bn-turn1.xirsys.com:80?transport=tcp",
//                 "turn:bn-turn1.xirsys.com:3478?transport=tcp",
//                 "turns:bn-turn1.xirsys.com:443?transport=tcp",
//                 "turns:bn-turn1.xirsys.com:5349?transport=tcp",
//             ],
//         },
//     ],
// }
const servers = {};

export default {
  props: {
    users: {
      type: Object,
    },
  },
  data() {
    return {
      user: this.$page.props.auth.user,
      onlineUsers: [],
      peerConnection: null,
      localStream: null,
      remoteStream: null,
      isEnableView: false,
    };
  },
  watch: {
    isEnableView(value) {
      this.localStream.getVideoTracks()[0].enabled = value;
    },
  },
  async created() {
    this.peerConnection = new RTCPeerConnection(servers);
    this.handlePusher();
    this.handShaking();
  },
  methods: {
    handlePusher() {
      Echo.join("online-users")
        .here((users) => {
          // console.log('List users', users)
          this.onlineUsers = users.map((item) => item.id);
        })
        .joining((user) => {
          // console.log('Joining user', user)
          this.onlineUsers.push(user.id);
        })
        .leaving((user) => {
          // console.log('Leaving user', user)
          this.onlineUsers = this.onlineUsers.filter((item) => item != user.id);
        });
    },
    handShaking() {
      Echo.private(`handshake.${this.user.id}`).listen(
        "SendHandShake",
        (data) => {
          const handShakeData = JSON.parse(data.data);

          if (handShakeData.type === "incoming-call") {
            console.log("incoming-call");
            this.caller = handShakeData.data;

            if (confirm(`${this.caller.name} is calling you`) == true) {
              this.answerCall();
            }

            // if (this.isCallOn) {
            //     axios.post(route("handshake"), {
            //         senderId: this.user.id,
            //         reciverId: this.caller.id,
            //         data: JSON.stringify({
            //         type: "incoming-call",
            //         data: this.user,
            //         }),
            //     });
            // } else {
            //     axios.post(route("handshake"), {
            //         senderId: this.user.id,
            //         reciverId: this.caller.id,
            //         _token: csrfToken,
            //         data: JSON.stringify({
            //         type: "ring",
            //         data: this.user,
            //         }),
            //     });
            // }
          }

          // 1 person got when 2nd person answer call
          if (handShakeData.type == "offer") {
            console.log("offer-received");
            this.setOffer(data, handShakeData);
          }

          // 2st person got
          if (handShakeData.type == "answer") {
            console.log("answer-received");
            this.setAnswer(handShakeData);
          }

          // second person got
          if (handShakeData.type == "candidate") {
            console.log("candidate-received");
            this.setCandidate(handShakeData);
          }
        }
      );
    },
    async setupCall(isOwner = false) {
      console.log("🚀 ~ setupCall ~ isOwner:", isOwner)
      const conState = this.peerConnection.connectionState;
      if (conState === "closed") {
        this.peerConnection = new RTCPeerConnection(this.servers);
      }

      if (isOwner == false) {
        this.localStream = await this.createVideoStream();
        if (this.localStream === null) {
          this.localStream = await this.createVideoStream(true);
        }
        if (this.localStream === null) {
          this.localStream = await this.createVideoStream(false, true);
        }

        // Push tracks from local stream to peer connection
        this.localStream.getTracks().forEach((track) => {
          this.senderTrack = this.peerConnection.addTrack(
            track,
            this.localStream
          );
        });
        this.$refs.localView.srcObject = this.localStream;

        // get latest ice candidate and sent to other party
        this.peerConnection.onicecandidate = (event) => {
          if (event.candidate !== null || event.candidate !== undefined) {
            console.log("icecandidate:send", event.candidate);
            axios.post(route("handshake"), {
              senderId: this.user.id,
              reciverId: this.caller.id,
              data: JSON.stringify({
                type: "candidate",
                data: event.candidate,
              }),
            });
          }
        };
      }
      // this.localStream.getVideoTracks()[0].enabled = false

      // Pull tracks from remote stream, add to video stream
      this.remoteStream = new MediaStream();
      this.peerConnection.ontrack = (event) => {
        if (!event.streams.length) return;
        event.streams[0].getTracks().forEach((track) => {
          this.remoteStream.addTrack(track);
        });
      };
      this.$refs.remoteView.srcObject = this.remoteStream;
    },
    async setOffer(data, offerData) {
      const offerDescription = new RTCSessionDescription(offerData);
      await this.peerConnection.setRemoteDescription(
        new RTCSessionDescription(offerDescription)
      );

      // Create answer
      const answerDescription = await this.peerConnection.createAnswer();
      await this.peerConnection.setLocalDescription(answerDescription);

      axios.post(route("handshake"), {
        senderId: this.user.id,
        reciverId: this.caller.id,
        data: JSON.stringify(answerDescription),
      });
    },
    async setAnswer(answerData) {
      const answerDescription = new RTCSessionDescription(answerData);
      this.peerConnection.setRemoteDescription(answerDescription);
    },
    setCandidate(candidateData) {
      if (candidateData.data === null || candidateData.data === undefined) {
        return;
      }
      const candidate = new RTCIceCandidate(candidateData.data);
      // console.log('icecandidate:received', candidate);
      this.peerConnection
        .addIceCandidate(candidate)
        .catch((e) => console.log("candidate-error", e));
    },
    async startCall(user) {
      this.caller = user;
      if (user.id === this.user.id) {
        alert("can't call to own");
        return false;
      }
      await this.setupCall(true);
      axios.post(route("handshake"), {
        senderId: this.user.id,
        reciverId: this.caller.id,
        data: JSON.stringify({
          type: "incoming-call",
          data: this.user,
        }),
      });
    },
    async answerCall() {
      await this.setupCall();

      // Create offer
      const offerDescription = await this.peerConnection.createOffer();
      await this.peerConnection.setLocalDescription(offerDescription);

      axios.post(route("handshake"), {
        senderId: this.user.id,
        reciverId: this.caller.id,
        data: JSON.stringify(offerDescription),
      });
    },
    async createVideoStream(isOnlyAudio = false, isOnlyVideo = false) {
      let constraints = {
        video: {
          width: { min: 1280, max: 1920 },
          height: { min: 720, max: 1080 },
          frameRate: 30,
        },
        audio: {
          noiseSuppression: true,
        },
      };

      if (isOnlyAudio) {
        constraints = {
          video: false,
          audio: {
            noiseSuppression: true,
          },
        };
      }
      if (isOnlyVideo) {
        constraints = {
          video: true,
          audio: false,
        };
      }
      return await navigator.mediaDevices
        .getDisplayMedia(constraints)
        .then((stream) => {
          return stream;
        })
        .catch((err) => {
          console.log("🚀 ~ createVideoStream ~ err:", err)
          return null;
        });
    },
    startStreaming() {
      this.$inertia.visit(route("room.livestream"));
    },
  },
};
</script>

