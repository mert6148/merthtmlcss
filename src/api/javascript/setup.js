const { ChatCompletionStreamingRunner } = require("openai/lib/ChatCompletionStreamingRunner.js");

class Setup {
    constructor(parameters) {
        this.number1 = 1;
        this.number2 = 2;
        this.sum = this.number1 + this.number2;
        this.sum2 = this.number1 - this.number2;
        console.log(this.sum);
    }
}

let setup = new Setup();
let int = 1;