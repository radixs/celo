var gui_console = {

    //these mark the current position in the command history
    pointer_position: {
        server: null,
        node: null
    },

    //storage for the commands
    history: {
        server: [],
        node: []
    },


    /**
     * Register console event listeners and load console command history from the server.
     */
    start: function() {
        //register key events inside input areas
        var server_console = document.getElementById('server_console');
        var node_console   = document.getElementById('node_console');

        gui_console.attach_key_bindings(server_console);
        gui_console.attach_key_bindings(node_console);

        //register button events above input fields
        document.getElementById('server_send').onclick = function() {
            gui_console.send_input(server_console);
        };
        document.getElementById('node_send').onclick = function() {
            gui_console.send_input(node_console);
        };

        document.getElementById('server_clear').onclick = function() {
            gui_console.clear_input(server_console);
        };
        document.getElementById('node_clear').onclick = function() {
            gui_console.clear_input(node_console);
        };

        document.getElementById('server_previous').onclick = function() {
            gui_console.cycle_history(server_console, 'previous');
        };
        document.getElementById('server_next').onclick = function() {
            gui_console.cycle_history(server_console, 'next');
        };
        document.getElementById('node_previous').onclick = function() {
            gui_console.cycle_history(node_console, 'previous');
        };
        document.getElementById('node_next').onclick = function() {
            gui_console.cycle_history(node_console, 'next');
        };

        //get command history
        requests.send_request({action: 'fetch_console_history'});
    },


    /**
     * Register actions for key hits inside the input fields.
     *
     * @param input_container
     */
    attach_key_bindings: function(input_container) {
        input_container.onkeydown = function(event) {
            switch (event.keyCode) {
                case 13: //ENTER
                    event.preventDefault();
                    gui_console.send_input(input_container);
                    break;
                case 27: //ESC
                    event.preventDefault();
                    gui_console.clear_input(input_container);
                    break;
                case 33: //PAGE UP
                    event.preventDefault();
                    gui_console.cycle_history(input_container, 'previous');
                    break;
                case 34: //PAGE DOWN
                    event.preventDefault();
                    gui_console.cycle_history(input_container, 'next');
                    break;
            }
        };
    },


    /**
     * Send the command to the server and register it in console history.
     *
     * @param input_container
     */
    send_input: function(input_container) {
        //get command value
        var command = gui_console.cleanup_command(input_container.value);

        if (command !== '') {
            //get type of the input container
            var type = gui_console.get_input_type(input_container);

            //add command to history
            gui_console.add_to_history(type, command);

            //reset pointer
            gui_console.reset_pointer(type);

            //send the command
            requests.send_request({
                action: 'register_command',
                type:   type,
                input:  command
            });

            gui_console.clear_input(input_container);
        }
    },


    /**
     * Clear input field.
     *
     * @param input_container
     */
    clear_input: function(input_container) {
        input_container.value = '';
        //reset pointer
        var type = gui_console.get_input_type(input_container);
        gui_console.reset_pointer(type);
    },


    /**
     * Select a command in the console history and display it in the provided input text area.
     *
     * @param input_container
     * @param direction
     */
    cycle_history: function(input_container, direction) {
        //get type of the input container
        var type = gui_console.get_input_type(input_container);

        //get pointer position for the type
        var current_pointer_position = gui_console.pointer_position[type];

        //reduce/increase pointer by one
        if (direction === 'previous' && current_pointer_position > 0) {
            gui_console.pointer_position[type] = gui_console.pointer_position[type] - 1;
        }
        if (direction === 'next'  && current_pointer_position < gui_console.history[type].length - 1) {
            gui_console.pointer_position[type] = gui_console.pointer_position[type] + 1;
        }
        if (direction === 'previous' && current_pointer_position === null) {
            gui_console.pointer_position[type] = gui_console.history[type].length - 1;
        }

        //get updated pointer position
        var new_pointer_position = gui_console.pointer_position[type];

        //load command at the input and put it into the container
        input_container.value = gui_console.history[type][new_pointer_position];
    },


    /**
     * Load the server console history return object into JS container.
     *
     * @param console_history_raw
     */
    parse_console_history: function(console_history_raw) {
        var console_history_loader = function(source, destination) {
            var number_of_items = source.length;
            for (var item_index = 0; number_of_items>item_index; item_index++) {
                destination.push(source[item_index]['text']);
            }
        };

        if (typeof console_history_raw['server'] !== 'undefined') {
            console_history_loader(console_history_raw['server'], gui_console.history.server);
            this.reset_pointer('server');
        }

        if (typeof console_history_raw['node'] !== 'undefined') {
            console_history_loader(console_history_raw['node'], gui_console.history.node);
            this.reset_pointer('node');
        }
    },


    /**
     * Reset pointer by type.
     *
     * Will make the pointer sit at the position of the last item in history container.
     *
     * @param type string
     */
    reset_pointer: function(type) {
        gui_console.pointer_position[type] = null;
    },


    /**
     * Get console type name from the provided input text area object.
     *
     * @param input_container
     * @returns {string}
     */
    get_input_type: function(input_container) {
        var container_id = input_container.id;
        return container_id.replace('_console', '');
    },


    /**
     * Insert new command to the history.
     *
     * @param type   string
     * @param value  string
     */
    add_to_history: function(type, value) {
        gui_console.history[type].push(value);
    },


    /**
     * Prepare the command to be able to be sent to the server.
     *
     * @param value  string
     * @returns {string}
     */
    cleanup_command: function(value) {
        //TODO add filters and encoding
        return value.trim();
    }
};
