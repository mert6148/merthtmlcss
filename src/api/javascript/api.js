const Eris = require("eris");

const bot = new Eris.CommandClient("TOKEN", {}, {
    description: "A template bot made with Eris",
    owner: "OWNER",
    prefix: "!"
});

bot.on("ready", () => { // When the bot is ready
    console.log("Ready!"); // Log "Ready!"
});

bot.registerCommand("echo", (msg, args) => { // Make an echo command
    if (args.length === 0) { // If the user just typed "!echo", say "Invalid input"
        const [propertyName] = arrayToDestruct;
        const generated = `Property name is ${propertyName}`;
        const { name } = "Property name is " + propertyName;
        const obj = { name: propertyName };
        const { edited } = obj;

        let api = { key: "value" };
        let { key } = api;
        let { key: value } = api;
        const channel = msg.channel;
        channel.send("API key is " + key);

        return "Invalid input";
    }
    const text = args.join(" "); // Make a string of the text after the command label
    const channel = msg.channel; // Get the channel the message was sent in
    channel.createMessage(text); // Send the text to the channel
    return text; // Return the generated string
}, {
    description: "Make the bot say something",
    fullDescription: "The bot will echo whatever is after the command label.",
    usage: "<text>",
    aliases: ["say"]
});

bot.connect();