let activeMeeting, remoteShareStream, webex;

$(document).ready(function () {
    /* eslint-env browser */
    /* global Webex */
    /* eslint-disable camelcase */
    /* eslint-disable max-nested-callbacks */
    /* eslint-disable no-alert */
    /* eslint-disable no-console */
    /* eslint-disable require-jsdoc */
    /* eslint-disable arrow-body-style */
    /* eslint-disable max-len */

    // Declare some globals that we'll need throughout

    // There's a few different events that'll let us know we should initialize
    // Webex and start listening for incoming calls, so we'll wrap a few things
    // up in a function.
    function connect() {
        return new Promise((resolve) => {
            if (!webex) {
                // eslint-disable-next-line no-multi-assign
                webex = window.webex = Webex.init({
                    config: {
                        logger: {
                            level: 'debug'
                        },
                        meetings: {
                            reconnection: {
                                enabled: true
                            }
                        }
                        // Any other sdk config we need
                    },

                    credentials: {
                        access_token: document.getElementById('access-token').value
                    }
                });
            }

            // Listen for added meetings
            webex.meetings.on('meeting:added', (addedMeetingEvent) => {
                if (addedMeetingEvent.type === 'INCOMING') {
                    const addedMeeting = addedMeetingEvent.meeting;

                    // Acknowledge to the server that we received the call on our device
                    addedMeeting.acknowledge(addedMeetingEvent.type).then(() => {
                        if (confirm('Answer incoming call')) {
                            joinMeeting(addedMeeting);
                        } else {
                            addedMeeting.decline();
                        }
                    });
                }
            });

            // Register our device with Webex cloud
            if (!webex.meetings.registered) {
                webex.meetings.register()
                    // Sync our meetings with existing meetings on the server
                    .then(() => webex.meetings.syncMeetings())
                    .then(() => {
                        // This is just a little helper for our selenium tests and doesn't
                        // really matter for the example
                        document.body.classList.add('listening');
                        //document.getElementById('connection-status').innerHTML = 'connected';
                        // Our device is now connected
                        resolve();
                    })
                    // This is a terrible way to handle errors, but anything more specific is
                    // going to depend a lot on your app
                    .catch((err) => {
                        console.error(err);
                        // we'll rethrow here since we didn't really *handle* the error, we just
                        // reported it
                        throw err;
                    });
            } else {
                // Device was already connected
                resolve();
            }
        });
    }

    // Similarly, there are a few different ways we'll get a meeting Object, so let's
    // put meeting handling inside its own function.
    function bindMeetingEvents(meeting) {
        // meeting is a meeting instance, not a promise, so to know if things break,
        // we'll need to listen for the error event. Again, this is a rather naive
        // handler.
        meeting.on('error', (err) => {
            console.error(err);
        });

        meeting.on('meeting:startedSharingRemote', () => {
            // Set the source of the video element to the previously stored stream
            document.getElementById('remote-screen').srcObject = remoteShareStream;
            //document.getElementById('screenshare-tracks-remote').innerHTML = 'SHARING';
        });

        meeting.on('meeting:stoppedSharingRemote', () => {
            document.getElementById('remote-screen').srcObject = null;
            //document.getElementById('screenshare-tracks-remote').innerHTML = 'STOPPED';
        });

        // Handle media streams changes to ready state
        meeting.on('media:ready', (media) => {
            if (!media) {
                return;
            }
            console.log(`MEDIA:READY type:${media.type}`);
            if (media.type === 'local') {
                document.getElementById('self-view').srcObject = media.stream;
            }
            if (media.type === 'remoteVideo') {
                document.getElementById('remote-view-video').srcObject = media.stream;
            }
            if (media.type === 'remoteAudio') {
                document.getElementById('remote-view-audio').srcObject = media.stream;
            }
            if (media.type === 'remoteShare') {
                // Remote share streams become active immediately on join, even if nothing is being shared
                remoteShareStream = media.stream;
            }
            if (media.type === 'localShare') {
                document.getElementById('self-screen').srcObject = media.stream;
            }
        });

        // Handle media streams stopping
        meeting.on('media:stopped', (media) => {
            // Remove media streams
            if (media.type === 'local') {
                document.getElementById('self-view').srcObject = null;
            }
            if (media.type === 'remoteVideo') {
                document.getElementById('remote-view-video').srcObject = null;
            }
            if (media.type === 'remoteAudio') {
                document.getElementById('remote-view-audio').srcObject = null;
            }
            if (media.type === 'localShare') {
                document.getElementById('self-screen').srcObject = null;
            }
        });

        // Handle share specific events
        meeting.on('meeting:startedSharingLocal', () => {
            //document.getElementById('screenshare-tracks').innerHTML = 'SHARING';
        });
        meeting.on('meeting:stoppedSharingLocal', () => {
            //document.getElementById('screenshare-tracks').innerHTML = 'STOPPED';
        });

        // Of course, we'd also like to be able to end the meeting:
        //        const leaveMeeting = () => meeting.leave();
        //        document.getElementById('btn-hangup').addEventListener('click', leaveMeeting, {once: true});

        meeting.on('all', (event) => {
            console.log(event);
        });
    }

    // Waits for the meeting to be media update ready
    function waitForMediaReady(meeting) {
        return new Promise((resolve, reject) => {
            if (meeting.canUpdateMedia()) {
                resolve();
            } else {
                console.info('SHARE-SCREEN: Unable to update media, pausing to retry...');
                let retryAttempts = 0;

                const retryInterval = setInterval(() => {
                    retryAttempts += 1;
                    console.info('SHARE-SCREEN: Retry update media check');

                    if (meeting.canUpdateMedia()) {
                        console.info('SHARE-SCREEN: Able to update media, continuing');
                        clearInterval(retryInterval);
                        resolve();
                    }
                    // If we can't update our media after 15 seconds, something went wrong
                    else if (retryAttempts > 15) {
                        console.error('SHARE-SCREEN: Unable to share screen, media was not able to update.');
                        clearInterval(retryInterval);
                        reject();
                    }
                }, 1000);
            }
        });
    }

    // Join the meeting and add media
    function joinMeeting(meeting) {
        // Save meeting to global object
        activeMeeting = meeting;

        // Call our helper function for binding events to meetings
        bindMeetingEvents(meeting);

        return meeting.join().then(() => {
            const mediaSettings = {
                receiveVideo: true,
                receiveAudio: true,
                receiveShare: true,
                sendVideo: true,
                sendAudio: true,
                sendShare: false
            };

            return meeting.getMediaStreams(mediaSettings).then((mediaStreams) => {
                const [localStream, localShare] = mediaStreams;

                meeting.addMedia({
                    localShare,
                    localStream,
                    mediaSettings
                });
            });
        });
    }

    // In order to simplify the state management needed to keep track of our button
    // handlers, we'll rely on the current meeting global object and only hook up event
    // handlers once.
    document.getElementById('btn-share-screen').addEventListener('click', () => {
        if (activeMeeting) {
            // First check if we can update
            waitForMediaReady(activeMeeting).then(() => {
                console.info('SHARE-SCREEN: Sharing screen via `shareScreen()`');
                activeMeeting.shareScreen().then(() => {
                    
                    console.info('SHARE-SCREEN: Screen successfully added to meeting.');
                    document.getElementById('btn-share-screen').classList.toggle("hide");
                    document.getElementById('stop-screen-share').classList.toggle("hide");
                })
                .catch((e) => {
                    console.error('SHARE-SCREEN: Unable to share screen, error:');
                    console.error(e);
                });
            });
        } else {
            console.error('No active meeting available to share screen.');
        }
        
    });

    document.getElementById('stop-screen-share').addEventListener('click', () => {
       
        if (activeMeeting) {
            // First check if we can update, if not, wait and retry
            waitForMediaReady(activeMeeting).then(() => {
                activeMeeting.stopShare();
                document.getElementById('stop-screen-share').classList.toggle("hide");
                document.getElementById('btn-share-screen').classList.toggle("hide");
            });
            
        }
    });

    // And finally, let's wire up dialing
    document.getElementById('dial').addEventListener('click', (event) => {
        // again, we don't want to reload when we try to dial
        event.preventDefault();

        dialHangMeeting();

        document.getElementById('btn-audio-manager').classList.toggle('disabled');
        document.getElementById('btn-audio-manager').classList.remove('btn-danger');
        document.getElementById('btn-audio-manager').classList.toggle('btn-outline-primary');

        document.getElementById('btn-video-manager').classList.toggle('disabled');
        document.getElementById('btn-video-manager').classList.remove('btn-danger');
        document.getElementById('btn-video-manager').classList.toggle('btn-outline-primary');

        document.getElementById('btn-share-screen').classList.toggle('disabled');
        
        document.getElementById('btn-share-screen').classList.toggle('btn-outline-primary');
        document.getElementById('btn-share-screen').classList.toggle('btn-danger');

        document.getElementById('partecipants').classList.toggle('disabled');
        document.getElementById('partecipants').classList.toggle('btn-outline-primary');
    });

    document.getElementById('btn-audio-manager').addEventListener('click', function () {
        if (activeMeeting) {
            audioManager();
        }
    });

    document.getElementById('btn-video-manager').addEventListener('click', () => {
        if (activeMeeting) {
            videoManager();
        }
    });

    document.getElementById('partecipants').addEventListener('click', () => {
        if(!document.getElementById('partecipants').classList.contains('disabled')){
            var partecipants = document.getElementById("partecipants-container");
            partecipants.classList.toggle("not-active");
            partecipants.classList.toggle("active");
        }
    });

    function dialHangMeeting() {
        if (activeMeeting) {
            activeMeeting.leave();
            webex.meetings.unregister();
            document.getElementById('dial').setAttribute('title', 'Partecipa');
            document.getElementById('dial').innerHTML = 'Partecipa';
            document.getElementById('dial').classList.remove('btn-outline-primary');
            document.getElementById('dial').classList.add('btn-primary');
            
            return null;
        } else {
            document.getElementById('loader').classList.toggle('hide');
            document.getElementById('dial').classList.remove('btn-primary');
            document.getElementById('dial').classList.add('btn-outline-primary');
            document.getElementById('dial').setAttribute('title', 'Esci');
            document.getElementById('dial').innerHTML = 'Esci';
        }
    
        const destination = document.getElementById('sip-address').value;
        // we'll use `connect()` (even though we might already be connected or
        // connecting) to make sure we've got a functional webex instance.
        connect().then(() => {
            // Create the meeting
            return webex.meetings.create(destination).then((meeting) => {
                // Pass the meeting to our join meeting helper
                document.getElementById('loader').classList.toggle('hide');
                return joinMeeting(meeting);
            });
        })
        .catch((error) => {
            // Report the error
            console.error(error);
            // Implement error handling here
        });
    }

    function audioManager() {
        if (activeMeeting.isAudioMuted()) {
            activeMeeting.unmuteAudio().then(() => {
                document.getElementById('btn-audio-manager').classList.add('btn-outline-primary');
                document.getElementById('btn-audio-manager').classList.remove('btn-danger');
                document.getElementById('mute-audio').classList.remove('am-mic-off');
                document.getElementById('mute-audio').classList.add('am-mic-outline');
                document.getElementById('mute-audio').setAttribute('title', 'Disattiva audio');
            });
        } else {
            activeMeeting.muteAudio().then(() => {
                document.getElementById('btn-audio-manager').classList.remove('btn-outline-primary');
                document.getElementById('btn-audio-manager').classList.add('btn-danger');
                document.getElementById('mute-audio').classList.remove('am-mic-outline');
                document.getElementById('mute-audio').classList.add('am-mic-off');
                document.getElementById('mute-audio').setAttribute('title', 'Riattiva audio');
            });
        }
    }

    function videoManager() {
        if (activeMeeting.isVideoMuted()) {
            activeMeeting.unmuteVideo().then(() => {
                document.getElementById('btn-video-manager').classList.add('btn-outline-primary');
                document.getElementById('btn-video-manager').classList.remove('btn-danger');
                document.getElementById('mute-video').classList.remove('am-videocam-off');
                document.getElementById('mute-video').classList.add('am-videocam');
                document.getElementById('mute-video').setAttribute('title', 'Disattiva la videocamera');
            });
        } else {
            activeMeeting.muteVideo().then(() => {
                document.getElementById('btn-video-manager').classList.remove('btn-outline-primary');
                document.getElementById('btn-video-manager').classList.add('btn-danger');
                document.getElementById('mute-video').classList.remove('am-videocam');
                document.getElementById('mute-video').classList.add('am-videocam-off');
                document.getElementById('mute-video').setAttribute('title', 'Riattiva la videocamera');
            });
        }
    }
    
});
