function AnswersCanvas(canvas, answersJson) {
    if(!(this instanceof AnswersCanvas)) {
        throw new Error("Calling the AnswersCanvas constructor as function is forbidden");
    }

    if(!(canvas instanceof HTMLCanvasElement)){
        throw new Error("Arqument canvas is not a HTMLCanvasElement");
    }

    this.canvas = canvas;
    this.context = canvas.getContext("2d");

    this.row = 60;
    this.cell = 60;
    this.radius = 20;
    this.answersJson = answersJson;
    this.answers = [];
    this.colors = {
        'default'   : '#4527A0',
        'active'    : '#FFA000',
        'isTrue'    : '#388E3C',
        'deactive'  : '#616161'
    };

    this.init = function () {
        if(Object.keys(this.answersJson).length < 1){
            throw new Error("Answer list is empty");
        }

        var rowCounter = 1;
        for(var i in this.answersJson){
            var cellCounter = 2;
            var questionRow = [];


            for(var j in this.answersJson[i].answers){
                var status = 'default';

                if(this.answersJson[i].selectedAnswer){
                    status = 'deactive';

                    if(this.answersJson[i].selectedAnswer == j){
                        status = 'active';
                    }

                    if(this.answersJson[i].trueAnswer == j){
                        status = 'isTrue';
                    }
                }
                questionRow.push(
                    (new Answer({
                        x : this.cell * cellCounter - this.cell/2,
                        y : this.row * rowCounter - this.row/2,
                        quizId : this.answersJson[i].quiz_id,
                        questionId : this.answersJson[i].question_id,
                        answerId : j,
                        content : this.answersJson[i].answers[j],
                        status : status
                    }))
                );
                cellCounter++;
            }

            this.answers.push(questionRow);
            rowCounter++;
        }

        this.canvas.height = rowCounter * this.row;
        return this;
    };

    this.draw = function () {
        var rowCounter = 1;
        for(var i in this.answers){
            var color = this.colors['default'];
            var stats = {};

            for(var j in this.answers[i]){
                this.drawAnswer(this.answers[i][j]);
                stats[ this.answers[i][j].status ] = 1;
            }

            if(stats['isTrue']){
                color = stats['active'] ? this.colors['active'] : this.colors['isTrue'];
            }

            this.drawQuestion(this.cell/2, this.row * rowCounter - this.row/2,  rowCounter, color);
            rowCounter++;
        }
        return this;
    };

    this.clear = function () {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

        return this;
    };

    this.drawAnswer = function (answer) {
        var drawColor = this.colors[answer.status];
        this.context.globalAlpha = 0.85;

        this.context.beginPath();
        this.context.arc(answer.x, answer.y, this.radius, 0, Math.PI*2);
        this.context.lineWidth = 3;
        this.context.strokeStyle = drawColor;
        this.context.stroke();

        this.context.font = "bold 20px sans-serif";
        this.context.textAlign = 'center';
        this.context.textBaseline = 'middle';
        this.context.fillStyle = drawColor;
        this.context.fillText(answer.content, answer.x, answer.y);
    };

    this.drawQuestion = function (x,y,key,color) {
        this.context.globalAlpha = 0.85;
        this.context.beginPath();
        this.context.font = "bold 40px sans-serif";
        this.context.textAlign = 'center';
        this.context.textBaseline = 'middle';
        this.context.fillStyle = color;
        this.context.fillText(key+'.', x, y);
    };
}

function Answer(p){
    if(!(this instanceof Answer)) {
        throw new Error("Calling the Answer constructor as function is forbidden");
    }

    this.x          = p.x;
    this.y          = p.y;
    this.quizId     = p.quizId;
    this.questionId = p.questionId;
    this.answerId   = p.answerId;
    this.content    = p.content;
    this.status     = p.status ? p.status : 'default';
}

window.onload = function() {
    var Canvas = document.getElementById('quiz-test-questions');
    var Cnvs = new AnswersCanvas(Canvas, QuizQuestions);
    Cnvs.init().draw();
};