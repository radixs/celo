import '../css/app.css'
import CommandInput from "./Command/CommandInput";

class Application {
    /**
     * Load all modules.
     *
     * @param CommandInput
     */
    constructor(CommandInput)
    {
        this.CommandInput = CommandInput;
        return this;
    }

    /**
     * Run initialization for each module.
     *
     * @returns {Application}
     */
    initialize()
    {

        return this;
    }

    /**
     * Start the application.
     */
    run()
    {

    }
}

new Application(new CommandInput).initialize().run();