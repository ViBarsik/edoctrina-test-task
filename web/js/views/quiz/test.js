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

    this.canvasHover = function(e){
        var hovX = e.pageX - this.canvas.offsetLeft;
        var hovY = e.pageY - this.canvas.offsetTop;
        var cell = (hovX - (hovX%this.cell) ) / this.cell;
        var row =  (hovY - (hovY%this.row) ) / this.row;

        if(cell < 1 || !this.answers[row] || !this.answers[row][cell-1]){
            return this.canvas.style.cursor = 'auto';
        }

        var answer = this.answers[row][cell-1];
        if(answer.status !== 'default'){
            return this.canvas.style.cursor = 'auto';
        }

        var distanceFromCenter = Math.sqrt(Math.pow(answer.x - hovX, 2) + Math.pow(answer.y - hovY, 2));
        if (distanceFromCenter > this.radius) {
            return this.canvas.style.cursor = 'auto';
        }

        return this.canvas.style.cursor = 'pointer';
    };

    this.canvasClick = function (e) {
        var clickX = e.pageX - this.canvas.offsetLeft;
        var clickY = e.pageY - this.canvas.offsetTop;
        var answerObject = this.click(clickX,clickY);
        if(!answerObject){
           return false;
        }
        this.sendAnswer(answerObject.answer, answerObject.row);
    };

    this.socketSelect = function(data){
        var color = this.colors.default;
        var asnwers = this.answers[data.row];

        for(var i in asnwers){
            if(data.anserSelect == data.anserTrue) {
                asnwers[i].status = asnwers[i].answerId == data.anserSelect ? 'isTrue' : 'deactive';
                continue;
            } else {
                if( asnwers[i].answerId == data.anserSelect ){
                    asnwers[i].status = 'active';
                    continue;
                } else if( asnwers[i].answerId == data.anserTrue ){
                    asnwers[i].status = 'isTrue';
                    continue;
                } else {
                    asnwers[i].status = 'deactive';
                }
            }
        }

        this.clear().draw();
    };

    this.click = function(clickX,clickY){
        var cell = (clickX - (clickX%this.cell) ) / this.cell;
        var row =  (clickY - (clickY%this.row) ) / this.row;

        if(cell < 1){
            return;
        }

        var answer = this.answers[row][cell-1];
        if(answer.status !== 'default'){
            return;
        }

        var distanceFromCenter = Math.sqrt(Math.pow(answer.x - clickX, 2) + Math.pow(answer.y - clickY, 2));
        if (distanceFromCenter > this.radius) {
            return;
        }

        for(var i in this.answers[row]){
            this.answers[row][i].status = 'deactive';
        }
        answer.status = 'active';

        this.clear().draw();
        return {answer:answer, row:row};
    };

    this.sendAnswer = function(answer, row){
        var csrfName = document.querySelector('meta[name="csrf-param"]');
        var csrfToken = document.querySelector('meta[name="csrf-token"]');

        if(!csrfName || !csrfToken){
            MessageBox('danger', 'Csrf Token is not found. Please reload this page');
            return false;
        }

        fd = {};
        fd[csrfName.content] = csrfToken.content;
        fd['answer'] = JSON.stringify({
            quiz_id : answer.quizId,
            question_id : answer.questionId,
            answer_id : answer.answerId
        });

        $.post('/quiz/set-answer', fd, cb(function(bd) {
            if(bd.message){
                MessageBox(bd.message.key, bd.message.value);
            }

            for(var i in this.answers[row]){
                if(this.answers[row][i].answerId == bd.answerTrue){
                    this.answers[row][i].status = 'isTrue';
                    break;
                }
            }

            socket.emit('selectAnswer', {row:row, anserSelect : answer.answerId, anserTrue : bd.answerTrue});

            this.clear().draw();

        }, this, row, answer));
    }
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

function cb(fnc, context) {
    return function() {
        return fnc.apply(context, arguments);
    }
}

window.onload = function() {
    var Canvas = document.getElementById('quiz-test-questions');
    Cnvs = new AnswersCanvas(Canvas, QuizQuestions);
    Cnvs.init().draw();

    Canvas.addEventListener('click', function (e) {
        Cnvs.canvasClick(e);
    });

    Canvas.addEventListener('mousemove', function(e) {
        Cnvs.canvasHover(e);
    });





};