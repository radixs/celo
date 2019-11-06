var requests = {

    refresh_interval: 1000, //default it one second
    server_address: 'request.php',

    skip_polling: false,


    /**
     * Get initial server settings. TODO expand description
     */
    start: function() {
        this.send_state();
    },


    /**
     * Send a request to the server and return response.
     * @param data
     */
    send_request: function(data) {
        console.log(data);
        requests.skip_polling = true;
        var encoded_data = JSON.stringify(data);
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                requests.receive_state(xhttp.responseText);
                requests.skip_polling = false;
            }
        };
        xhttp.open("POST", this.server_address, true);
        xhttp.send(encoded_data);
    },


    /**
     * Send current settings state and statistics so updates can be received.
     */
    send_state: function() {
        this.send_request({
            action:     'fetch_state',
            settings:   initialize.settings,
            statistics: initialize.statistics
            //TODO node settings
            //TODO node status
            //TODO command processing status
            //TODO node listing
        });
    },


    /**
     * Update DOM containers with new data received from the server.
     *
     * @param data  Object containing updated values from the server.
     */
    receive_state: function(data) {
        try {
            var new_state = JSON.parse(data);

            //update settings if present
            if (typeof new_state.settings !== 'undefined') {
                for (var setting_name in new_state.settings) {
                    if (new_state.settings.hasOwnProperty(setting_name) && initialize.settings.hasOwnProperty(setting_name)) {
                        initialize.settings[setting_name] = new_state.settings[setting_name];
                        elements.update_server_settings(setting_name, initialize.settings[setting_name]);
                    }
                }
            }

            //update stats if present
            if (typeof new_state.statistics !== 'undefined') {
                for (var stat_name in new_state.statistics) {
                    if (new_state.statistics.hasOwnProperty(stat_name) && initialize.statistics.hasOwnProperty(stat_name)) {
                        initialize.statistics[stat_name] = new_state.statistics[stat_name];
                        elements.update_server_status(stat_name, initialize.statistics[stat_name]);
                    }
                }
            }

            //update console history if present
            if (typeof new_state['console_input_history'] !== 'undefined') {
                gui_console.parse_console_history(new_state['console_input_history']);
            }

            //TODO node settings
            //TODO node status
            //TODO command processing status
            //TODO node listing

        } catch (e) {
            console.log(data);
        }
        requests.skip_polling = false;
    }



};
