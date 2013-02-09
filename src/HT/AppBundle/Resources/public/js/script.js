$(function() {
    var duration = 1;

    $('#rdio').rdio('GA9RFYuN_____2R2cHlzNHd5ZXg3Z2M0OXdoaDY3aHdrbmNvbGxhYm9yZGlvLmRldu6dOvhJamFfJ31iUXuSSac=');

    $('#rdio').bind('ready.rdio', function() {
        $('#player').animate({
                opacity: 1
            }, 2000);
        $(this).rdio().play('t1230882');
    });

    $('#rdio').bind('playingTrackChanged.rdio', function(e, playingTrack, sourcePosition) {
        if (playingTrack) {
          duration = playingTrack.duration;
          $('#album-art').attr('src', playingTrack.icon);
          $('#playing-track').text(playingTrack.name);
          $('#playing-album').text(playingTrack.album);
          $('#playing-artist').text(playingTrack.artist);
        }
    });

    $('#rdio').bind('playStateChanged.rdio', function(e, playState) {
        // playState has the current state of the player:
        // 0: Paused
        // 1: Playing
        // 2: Stopped
        // 3: Buffering
        // 4: Paused

        var control = $('#play-pause-control');

        switch (playState) {
            case 0:
                control.html('Play');
                control.attr('data-action', 'play');
                break;

            case 1:
                control.html('Pause');
                control.attr('data-action', 'pause');
                break;
        }
    });

    $('#rdio').bind('positionChanged.rdio', function(e, position) {
        $('#playing-position').css('width', Math.floor(100 * position / duration) + '%');
    });

    $('#play-pause-control').click( function() {
        var action = $('#play-pause-control').attr('data-action');

        switch (action) {
            case 'play':
                $('#rdio').rdio().play();
                break;

            case 'pause':
                $('#rdio').rdio().pause();
                break;
        }

        return false;
    });

});
