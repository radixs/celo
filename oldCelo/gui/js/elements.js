var elements = {

    single_button_actions: [
        'send_loop_start',
        'send_loop_end'
    ],


    /**
     * Attach event listeners to all interactive elements. TODO
     */
    register_events: function() {

        var buttons = document.getElementsByClassName('button');
        var number_of_buttons = buttons.length;

        for (var i=0; i<number_of_buttons; i++) {
            var button_id = buttons[i].id;
            //switches that activate proper event handlers
            if (utilities.in_array(button_id, this.single_button_actions) === true) {
                this.attach_single_action(buttons[i]);
            }
        }
    },


    /**
     * Create server settings containers.
     *
     * @param options from initialize.settings
     */
    build_server_settings: function(options) {
        var main_settings_wrapper = document.getElementById('server-settings');

        for (var setting_name in options) {
            if (options.hasOwnProperty(setting_name)) {

                var new_setting_wrapper = document.createElement('div');
                new_setting_wrapper.className = 'button-bar level-3';

                var new_setting_name = document.createElement('div');
                new_setting_name.className = 'text-container level-4 two_columns';
                new_setting_name.innerHTML = setting_name.replace(/_/g, ' ');
                new_setting_wrapper.appendChild(new_setting_name);

                var new_setting_value = document.createElement('input');
                new_setting_value.className = 'text-container level-4 two_columns';
                new_setting_value.setAttribute("type", "text");
                new_setting_value.id = setting_name;
                if (typeof options[setting_name] !== 'undefined') {
                    new_setting_value.value = options[setting_name];
                }

                //register onchange event on the input field
                new_setting_value.addEventListener("change", function(event){
                    elements.send_single_value(event.target);
                });

                new_setting_wrapper.appendChild(new_setting_value);

                main_settings_wrapper.appendChild(new_setting_wrapper);
            }
        }
    },


    /**
     * Create server status containers.
     *
     * @param options from initialize.statistics
     */
    build_server_status: function(options) {
        var main_status_wrapper = document.getElementById('server-status');

        for (var status_name in options) {
            if (options.hasOwnProperty(status_name)) {

                var new_stat_wrapper = document.createElement('div');
                new_stat_wrapper.className = 'status-bar level-3';

                var new_stat_name = document.createElement('div');
                new_stat_name.className = 'server-status-name text-container level-4';
                new_stat_name.innerHTML = status_name.replace(/_/g, ' ');
                new_stat_wrapper.appendChild(new_stat_name);

                var new_stat_value = document.createElement('div');
                new_stat_value.className = 'server-status-value text-container level-4';
                new_stat_value.id = status_name;
                if (options[status_name] === 'empty') {
                    new_stat_value.innerHTML = '-';
                } else {
                    new_stat_value.innerHTML = options[status_name];
                }
                new_stat_wrapper.appendChild(new_stat_value);

                main_status_wrapper.appendChild(new_stat_wrapper);
            }
        }

        //TODO add STATUS VIEW
        //TODO add refresh events
    },


    //TODO
    build_node_status: function() {
        //TODO add NODE VIEW
        //TODO add refresh events
    },


    /**
     * Buttons that do not require for any data being
     * read from the DOM uses this method to have their
     * event listeners attached.
     *
     * @param element node
     */
    attach_single_action: function(element) {
        var data = {
            action: element.id
        };
        element.onclick = function() {
            requests.send_request(data);
        }
    },


    /**
     * Get the value and id from the provided element and send to server.
     *
     * @param element
     */
    send_single_value: function(element) {
        var data = {
            action: element.id,
            value:  element.value
        };
        requests.send_request(data);
    },


    /**
     * Finds a proper server setting option in DOM and updates its value.
     *
     * @param setting_name
     * @param setting_value
     */
    update_server_settings: function(setting_name, setting_value) {
        //TODO check if conversion to/from null is required

        var target_setting = document.getElementById(setting_name);
        if (typeof target_setting !== 'undefined') {
            target_setting.value = setting_value;
        } else {
            console.log('ERROR: Missing server setting container: '+setting_name);
        }
    },


    /**
     * Finds a proper server status option in DOM and updates its value.
     *
     * @param status_name
     * @param status_value
     */
    update_server_status: function(status_name, status_value) {
        if (status_value === 'empty') {
            status_value = '-';
        }
        var target_status = document.getElementById(status_name);
        if (typeof target_status !== 'undefined') {
            target_status.innerHTML = status_value;
        } else {
            console.log('ERROR: Missing server status container: '+status_name);
        }
    }

};
