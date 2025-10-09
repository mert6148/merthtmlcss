const { appendFileSync } = require("fs");
const { send } = require("process");

appendFileSync("log.txt", "Initializing bot...\n");
appendFileSync("log.txt", "Bot started\n");


console.log("Log file created");

