class name {
    constructor(parameters) {
        module.exports = {
            name: "commandName",
            aliases: ["aliase"],
            description: "description",
            category: "category",
            guildOnly: true,
            memberpermissions:"VIEW_CHANNEL",
            adminPermOverride: true,
            cooldown: 2,
            args: args,
            usage: "<usage>",
            execute(message, args) {
                message.reply("template command")
            },
        };
        
        const args = [
            {
                name: "argName",
                description: "argDescription",
                required: true,
                data() {
                    return {
                        required: true,
                        type: "string",
                        description: "argDescription",
                        name: "argName",
                        choices: [
                            { name: "choiceName", value: "choiceValue" },
                        ],
                    }
                },
            },
        ];
        
        const data = {
            name: "commandName",
            aliases: ["aliase"],
            description: "description",
            category: "category",
            guildOnly: true,
            memberpermissions:"VIEW_CHANNEL",
            adminPermOverride: true,
            cooldown: 2,
            args: args,
            usage: "<usage>",
            execute(message, args) {
                message.reply("template command")
            },
        
            functional: {
                name: "functionalName",
                description: "functionalDescription",
                category: "category",
                guildOnly: true,
                memberpermissions:"VIEW_CHANNEL",
                adminPermOverride: true,
                cooldown: 2,
            }
        }
        
        function args() {
            return [
                {
                    name: "argName",
                    description: "argDescription",
                    required: true,
                    data() {
                        return {
                            required: true,
                            type: "string",
                            description: "argDescription",
                            name: "argName",
                        }
                    },
                },
            ]
        }    
    }
}