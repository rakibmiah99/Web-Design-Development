<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
    </head>
    <body class="flex p-3 h-[100vh] flex-row flex justify-center items-center ">
            <div class="h-[400px] w-[400px] border p-3">
                <h3 class="mb-3">My Peer ID: <span id="peer_id"></span></h3>
               <div>
                   <input placeholder="enter another peer id" type="text" id="text" class="border px-4 py-1">
                   <button id="connect-btn" class="bg-purple-700 px-4 py-1 text-white">Connect</button>
                   <button id="send-data-btn" class="bg-purple-700 px-4 py-1 text-white">Send Data</button>
                   <button id="call-btn" class="bg-purple-700 px-4 py-1 text-white">Call</button>
               </div>

                <hr class="mt-3">

                <div>
                    <video id="my-video"></video>
                </div>
            </div>

        <script>
            var pusher = new Pusher("{{env('PUSHER_APP_KEY')}}", {
                cluster: "{{env('PUSHER_APP_CLUSTER')}}",
                appId: "{{env('PUSHER_APP_ID')}}",
                secret: "{{env('PUSHER_APP_SECRET')}}",
                encrypted: true
            });

            /*var pusher = new Pusher({
                appId: "{{env('PUSHER_APP_ID')}}",
                key: "{{env('PUSHER_APP_KEY')}}",
                secret: "{{env('PUSHER_APP_SECRET')}}",
                cluster: "{{env('PUSHER_APP_CLUSTER')}}",
            });*/



            let chanel = pusher.subscribe('web-rtc-chanel')
            chanel.bind('make-call', (data) => {
                console.log('data', data)
            })





            $(document).ready(async function(){
                var peer = new Peer({
                    config: {
                        iceServers: [
                            { urls: 'stun:stun.l.google.com:19302' },  // Public Google STUN server
                            {
                                urls: 'turn:relay1.expressturn.com:3478',
                                username: 'efLA7R35EUSZO917QZ',
                                credential: 'ovU0YbgXiub77Pa5'
                            }
                        ]
                    },
                    debug: 0
                });

                // Listen for connections from other peers
                peer.on('connection', function(conn) {
                    console.log('Connected to peer: ' + conn.peer);

                    // Listen for incoming data
                    conn.on('data', function(data) {
                        // Display the received message
                        // document.getElementById('receivedMessage').innerHTML = 'Received: ' + data;
                        console.log('Received message: ' + data);
                    });
                });



                function getPearId(){
                    return new Promise((resolve, reject) => {
                        peer.on('open', function (id){
                            resolve(id)
                        })
                    })
                }

                let peer_id = await getPearId();
                $('#peer_id').text(peer_id)


                let conn = null;
                document.getElementById('connect-btn').addEventListener('click', function (){
                    let destination_peer_id = $('input').val();


                    console.log(destination_peer_id)
                    conn = peer.connect(destination_peer_id);

                    conn.on('open', function() {
                        // Send messages
                        conn.send('Hello!');


                    });

                    // Handle connection errors
                    conn.on('error', function(err) {
                        console.error('Connection error:', err);
                    });

                    /*axios.post('{{route('make-call')}}', {
                        peer_id: peer_id
                    })*/
                })

                document.getElementById('send-data-btn').addEventListener('click', function (){
                    conn.send("hello")
                })


                document.getElementById('call-btn').addEventListener('click', function (){
                    let destination_peer_id = $('input').val()
                    navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: true
                    }).then(function (stream){
                        let video = document.getElementById('my-video')
                        video.srcObject = stream

                        video.onloadedmetadata = function (e){
                            video.play()
                        }

                        var call = peer.call(destination_peer_id, stream)
                    })

                })


                peer.on('call', function(call) {
                    // Answer the call, providing our mediaStream
                    navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: true
                    }).then(function (stream) {
                        let video = document.getElementById('my-video')
                        video.srcObject = stream

                        video.onloadedmetadata = function (e) {
                            video.play()
                        }
                        call.answer(stream);
                    });
                });

            });














        </script>
    </body>
</html>
