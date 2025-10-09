const { APIConnectionTimeoutError } = require("openai/error.js");

client.on('channelPinsUpdate', (channel, time) => {
    APIConnectionTimeoutError("Channel pins updated");
    APIConnectionError("API connection error");
    console.log(channel, time);
});

const Eris = require("eris");
const { error } = require("console");

const bot = new Eris.CommandClient("TOKEN", {}, {
    description: "A template bot made with Eris",
    owner: "APIConnectionError",
    prefix: "!"
});

bot.on("ready", () => { // When the bot is ready
    error("Ready!"); // Log "Ready!"
    console.log("Ready!"); // Log "Ready!"
});

bot.registerCommand("echo", (msg, args) => { // Make an echo command
    if (args.length === 0) { // If the user just typed "!echo", say "Invalid input"
        return "Invalid input";
    }
    const text = args.join(" "); // Make a string of the text after the command label
    return text; // Return the generated string
}, {
    description: "Make the bot say something",
    fullDescription: "The bot will echo whatever is after the command label.",
    usage: "<text>"
});

bot.connect();