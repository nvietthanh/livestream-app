<template>
    <div class="max-w-[1200px] mx-auto py-[32px]">
        <div class="font-bold">Livestream show detail</div>
        <button @click="startVideo()">
            Start video
        </button>
        <button :disabled="isStream" @click="startStream()">
            Start stream
        </button>
        <div class="mt-2">
            <div class="flex h-[400px]">
                <div class="flex-1 border flex" title="Streaming">
                    <video
                        ref="localView"
                        webkit-playsinline
                        autoplay
                        muted
                        class="w-full h-full bg-[#000]"
                    >
                    </video>
                </div>
                <div class="w-[35%] border flex flex-col">
                    <div class="py-[8px] px-[8px]">
                        {{ listUsers.length }} người tham gia
                    </div>
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
                        <input v-model="formData.content" type="text" class="w-full" @keypress.enter="sendMessage()" />
                        <button @click="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
            <div v-if="true" class="flex flex-col">
                <div class="font-bold mt-[8px] mb-[12px]">List user: ({{ listUsers.length }})</div>
                <div class="h-full max-h-[100%] overflow-y-scroll">
                    <div v-for="(item, index) in listUsers" :key="index" class="px-[8px] py-[12px] border-[1px]">
                        {{ item.name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import Echo from 'laravel-echo';

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
            channelId: this.$page.props.auth.user.id,
            isStartVideo: false,
            isStream: false,
            listMessage: [],
            formData: {
                content: null
            },
            listUsers: [],
            peerConnections: {}
        };
    },
    async created() {
        this.handlePusher()
        this.handShaking()
    },
    methods: {
        handlePusher() {
            Echo.join('streaming-room.' + this.channelId)
                .here((users) => {
                    console.log('List users', users)
                    this.listUsers = users
                })
                .joining(async (user) => {
                    console.log('Joining user', user)
                    this.listUsers.push(user);
                    if (this.isStream) {
                        this.createPeerConnection(user.id); // Create peer connection for the new user
                        this.createAndSendOffer(user.id); // Create and send offer to the new user
                    }
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
            Echo.private(`room_livestream_answer.` + this.channelId) 
                .listen("RoomStreamingAnswerEvent", (data) => {
                    const handShakeData = JSON.parse(data.data);
                    const userId = data.userId; // Assuming userId is included in the event data

                    if (!handShakeData) return;
                    
                    // 2st person got
                    if (handShakeData.type == "answer") {
                        console.log("answer-received", data, handShakeData, userId);
                        this.peerConnections[userId].setRemoteDescription(handShakeData);
                    }

                    // second person got
                    if (handShakeData.type == "candidate") {
                        console.log("candidate-received");
                        this.peerConnections[userId].addIceCandidate(handShakeData.data);
                    }
                }
            );
        },
        async startVideo() {
            // let localStream = await this.createVideoStream();
            // if (localStream === null) {
            //     localStream = await this.createVideoStream(true);
            // }
            // if (localStream === null) {
            //     localStream = await this.createVideoStream(false, true);
            // }
            let localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true })
            this.$refs.localView.srcObject = localStream
        },
        async startStream() {
            this.isStream = !this.isStream
            if (this.isStream) {
                this.listUsers.forEach((user) => {
                    if (user.id != this.user.id) {
                        this.createPeerConnection(user.id)
                        this.createAndSendOffer(user.id)
                    }
                })
            }
        },
        createPeerConnection(userId) {
            const peerConnection = new RTCPeerConnection(servers)

            peerConnection.onicecandidate = (event) => {
                if (event.candidate !== null) {
                    console.log("icecandidate:send", event.candidate);
                    let candidate = null
                    if (event.candidate !== null) {
                        candidate = {
                            candidate: event.candidate.candidate,
                            sdpMid: event.candidate.sdpMid,
                            sdpMLineIndex: event.candidate.sdpMLineIndex,
                        };
                    }
                    axios.post(route("room.stream-offer"), {
                        roomId: this.channelId,
                        userId: userId,
                        data: JSON.stringify({
                            type: "candidate",
                            data: candidate,
                        }),
                    });
                }
            };

            // Push tracks from local stream to peer connection
            this.$refs.localView.srcObject.getTracks()
                .forEach((track) => {
                    peerConnection.addTrack(track, this.$refs.localView.srcObject);
                });

            this.peerConnections[userId] = peerConnection; // Store the peer connection
        },
        async createVideoStream(isOnlyAudio = false, isOnlyVideo = false) {
            let constraints = {
                video: {
                    width: { min: 1280, max: 1920 },
                    height: { min: 720, max: 1080, },
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
                .getUserMedia(constraints)
                .then((stream) => {
                    return stream;
                })
                .catch((err) => {
                    console.log("🚀 ~ createVideoStream ~ err:", err)
                    return null;
                });
        },
        createAndSendOffer(userId) {
            const peerConnection = this.peerConnections[userId];
            peerConnection.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: true })
                .then(async offer => {
                    await peerConnection.setLocalDescription(offer);
                    console.log("Created offer, sending...", offer);
                    axios.post(route("room.stream-offer"), {
                        roomId: this.channelId,
                        userId: userId,
                        data: JSON.stringify(offer),
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

