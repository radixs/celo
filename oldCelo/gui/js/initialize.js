var initialize = {

    //these need to be the same as names in frame_settings table
    settings: {
        interval_microseconds: null,
        forget_previous_session: null,
        shutdown_flag: null,
        report_last_x_frames: null, //TODO
        hold_last_x_frames: null, //TODO
        polling_frequency: 1000
    },

    //all stats are empty initially. This is a keyword for NULL in database.
    statistics: {
        average_memory_usage:       'empty',
        peak_memory_usage:          'empty',
        frames_per_second:          'empty',
        last_reported_frame_number: 'empty'
    },

    //TODO node settings
    //TODO node status
    //TODO console data
    //TODO node listing


    start: function() {
        elements.build_server_settings(initialize.settings);
        elements.build_server_status(initialize.statistics);
        gui_console.start();
        //requests.start(); TODO unblock
        elements.register_events();
        looper.initialize_buttons();
        looper.start();
    }

};
window.onload = initialize.start;
