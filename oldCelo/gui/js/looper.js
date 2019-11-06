var looper = {
    is_active: false,

    /**
     * Register event listeners on the buttons.
     */
    initialize_buttons: function() {
        var start_button = document.getElementById('polling_start');
        var end_button   = document.getElementById('polling_end');

        start_button.onclick = function() {
            looper.start();
        };
        end_button.onclick = function() {
            looper.end();
        };
    },


    /**
     * Initiate looping.
     */
    start: function() {
        console.log('start looper'); //todo remove
        if (this.is_active !== true) {
            this.is_active = true;
            this.do_poll();
        }
    },

    /**
     * Perform polling action.
     */
    do_poll: function() {
        if (this.is_active === true) {
            var interval = 2000;
            if (typeof initialize.settings['polling_frequency'] !== 'undefined') {
                interval = initialize.settings['polling_frequency'];
            }

            //skip this loop if a request is already outgoing
            if (requests.skip_polling === false) {
                console.log('poll '+utilities.current_time()); //todo remove
                requests.send_state();
            }

            /* TODO unblock
            setTimeout(function(){
                looper.do_poll();
            }, interval);
            */
        }
    },

    /**
     * Interrupt looping.
     */
    end: function() {
        console.log('end looper'); //todo remove
        this.is_active = false;
    }
};