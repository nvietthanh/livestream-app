<template>
    <div class="max-w-[1200px] mx-auto py-[32px]">
        <div class="font-bold">Livestream show</div>
        <div class="mt-2">
            <div class="flex h-[400px]">
                <div class="flex-1 border flex" title="Streaming">
                    <video 
                        ref="remoteView" 
                        webkit-playsinline
                        playsinline
                        autoplay
                        class="w-full h-full bg-[#000]"
                    ></video>
                </div>
                <div class="w-[35%] border flex flex-col">
                    <div class="h-full max-h-[100%] flex flex-col-reverse border w-full overflow-y-scroll">
                        <div class="py-[8px] flex flex-col-reverse">
                            <div
                                v-for="item in listMessage"
                                :key="item.id"
                                class="px-[12px] py-[6px] border-b-[1px] flex items-start gap-[6px]"
                            >
                                <div class="rounded-[50%] bg-[#dad7d7] border flex items-center justify-center w-[40px] h-[40px]">
                                    <span class="text-[red] font-bold ">{{ item.user.name[0] }}</span>
                                </div>
                                <div class="flex-1 mt-[1px]">
                                    <div class="font-bold">{{ item.user.name }}:</div>
                                    <div>{{ item.content }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex w-full gap-2">
                        <input type="text" v-model="formData.content" class="w-full" @keypress.enter="sendMessage()" />
                        <button @click="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
            <div v-if="true" class="flex flex-col">
                <div class="font-bold mt-[8px] mb-[12px]">List user: ({{ listUsers.length }})</div>
                <div class="h-full max-h-[100%] overflow-y-scroll">
                    <div class="px-[8px] py-[12px] border-[1px]" v-for="(item, index) in listUsers" :key="index">
                        {{ item.name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

const servers = {
    iceServers: [
        { urls: ["stun:bn-turn1.xirsys.com"] },
        {
            username:"LpTQzzNnOhyCNV2c-XzFWdQLfX9rjGUb_8A9FR82F59dG-y2bjDkk8hIxh5aEboqAAAAAGHaoB1rYXZpbg==",
            credential: "2c2be25c-7128-11ec-b2b6-0242ac140004",
            urls: [
                "turn:bn-turn1.xirsys.com:80?transport=udp",
                "turn:bn-turn1.xirsys.com:3478?transport=udp",
                "turn:bn-turn1.xirsys.com:80?transport=tcp",
                "turn:bn-turn1.xirsys.com:3478?transport=tcp",
                "turns:bn-turn1.xirsys.com:443?transport=tcp",
                "turns:bn-turn1.xirsys.com:5349?transport=tcp",
            ],
        },
    ],
}

export default {
    props: {
        users: {
            type: Object
        }
    },
    data() {
        return {
            user: this.$page.props.auth.user,
            channelId: this.appRoute().params.id,
            listMessage: [],
            formData: {
                content: null
            },
            listUsers: [],
            peerConnection: null,
            remoteStream: null,
        };
    },
    async mounted() {
        this.handlePusher()
        this.handShaking()
    },
    methods: {
        handlePusher() {
            let channel = Echo.join('streaming-room.' + this.channelId)
                .here((users) => {
                    console.log('List users', users)
                    this.listUsers = users
                })
                .joining((user) => {
                    console.log('Joining user', user)
                    this.listUsers.push(user);
                })
                .leaving((user) => {
                    console.log('Leaving user', user)
                    this.listUsers = this.listUsers.filter((item) => item.id != user.id);
                })
            Echo.private('streaming-room.' + this.channelId)
                .listen('SendMessageStreamEvent', (data) => {
                    this.listMessage.unshift(data)
                })
        },
        handShaking() {
            Echo.private('room_livestream_offer.' + this.channelId)
                .listen("RoomStreamingOfferEvent", (data) => {
                    const handShakeData = JSON.parse(data.data);
                    const userId = data.userId; // Assuming userId is included in the event data

                    if (!handShakeData) return;
                    
                    if (userId == this.user.id && handShakeData.type == "candidate") {
                        console.log("candidate-received", handShakeData);
                        this.peerConnection?.addIceCandidate(handShakeData.data)
                    }
                    
                    if (userId == this.user.id && handShakeData.type == "offer") {
                        console.log("offer-received", handShakeData);
                        this.handleOffer(handShakeData);
                    }
                }
            );
        },
        handleOffer(offer) {
            this.peerConnection = new RTCPeerConnection(servers)
            this.peerConnection.addEventListener("track", e => {
                this.$refs.remoteView.srcObject = e.streams[0];
                this.$refs.remoteView.play();
            });
            this.peerConnection.addEventListener("icecandidate", event => {
                let candidate = null
                if (event.candidate !== null) {
                    candidate = {
                        candidate: event.candidate.candidate,
                        sdpMid: event.candidate.sdpMid,
                        sdpMLineIndex: event.candidate.sdpMLineIndex,
                    };
                }
                axios.post(route("room.stream-answer"), {
                    roomId: this.channelId,
                    userId: this.user.id,
                    data: JSON.stringify({
                        type: "candidate",
                        data: candidate,
                    }),
                });
            });

            this.peerConnection.setRemoteDescription(offer)
                .then(() => this.peerConnection.createAnswer())
                .then(async answer => {
                    await this.peerConnection.setLocalDescription(answer);
                    console.log("Created answer, sending...")
                    axios.post(route("room.stream-answer"), {
                        roomId: this.channelId,
                        userId: this.user.id,
                        data: JSON.stringify({
                            type: "answer",
                            sdp: answer.sdp,
                        })
                    });
                });
        },
        sendMessage() {
            if (!this.formData.content) return;
            axios.post(route("room.send-message"), {
                roomId: this.channelId,
                content: this.formData.content
            });
            this.formData.content = null
        }
    },
};
</script>

