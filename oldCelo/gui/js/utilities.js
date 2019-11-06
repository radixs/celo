var utilities = {

    /**
     * Check if parameter is in array.
     *
     * @param needle   mixed
     * @param haystack array
     * @returns {boolean}
     */
    in_array: function(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] === needle) return true;
        }
        return false;
    },


    /**
     * Fetch current time.
     *
     * @returns {string}
     */
    current_time: function() {
        var d = new Date();
        return d.getHours()+':'+d.getMinutes()+':'+d.getSeconds()+'.'+d.getMilliseconds();
    }

};
