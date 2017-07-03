# General overview

- Please implement test project using PHP 7.0+, JavaScript and MySQL (or MairaDB).
- Results must be submitted as pull request to GitHub repository [https://github.com/jagermesh/edoctrina-test-task](https://github.com/jagermesh/edoctrina-test-task).
- Project must include SQL script for database/table creation and INSTALL.MD for installation instructions.
- You can use any PHP/JS frameworks and libraries. All PHP dependencies must be configured to be installed using composer (so your project must contain package.json in this case).
- We expect nice looking project so you can use some out of the box libraries for UI design, such as Twitter Bootstrap for example, your preferences here.

# Project description

You need to create small project for taking online quizes. There will be 3 screens (please see below). There must be some navigation element to switch between screens. You must design database structure for this project by yourself.

## Screen #1 - Create Quiz

On this screen user can create quiz. He must enter `Quiz Name` and `Questions Count`. Quiz can have up to 30 questions. To create quiz, user must click on `Create Quiz` button. Every question can have up to 5 possible answers. There could be only one correct answer. You need to create quiz with specified number of questions. Number of answers and which one is the correct one must be picked randomly. Here is the example to give you the idea:

![](https://s3.amazonaws.com/docscamp/uploads/images/0/5/0/4/1/2/05041217237e3346f953f8e29fb500c0.png)


## Screen #2 - List of the quizes

This screens must show all existing quizes. Please show `ID`, `Name`, `Questions Count` and - if quiz already taken - `Score`. Score is the number and % of the correct responses. So if user took the quiz with 20 questions and answered correctly on 5 questions, score column must contain `5/20 (25%)`. There must be `Take the quiz` button for every quiz in the list which was not taken yet. This button must open `Screen #3`

## Screen #3 - Take the quiz

On this page you must show Quiz Name and render UI to take the quiz. Something similar to the following image (we assuming you'll use HTML canvas for this). 

![](https://s3.amazonaws.com/docscamp/uploads/images/9/b/5/d/e/d/9b5ded3f342111eed41c87bd68b54674.png)

You can print either letters or numbers as response indexes. Circles with possible responses must be clickable. Cursor must change itself to `pointer` when user move mouse over clickable circle. Clicking on the circle mean selecting that specific response. Selected response must be highlighted (drawn as yellow circle, drawn with thicker frame, etc, your preference). User can pick only one response, so if some other response already selected for this question - that response must be re-drawn as white circle. Click on selected response must de-select this response.

There must be `Save and finish` button. It will save selected responses and will redirect user back to quizes list (`screen #2`). There must be a check that user selected responses for all questions.

There must be some solution which will sync clicks if this screen opened in multiple tabs (or browsers) for this same quiz. So, basically, if you have same quiz opened in two different tabs (or browsers) - click on answer `1` for question `1` in one tab must redraw other tab and indicate that answer `1` selected for question `1`. Finishing quiz in one tab must redirect all other opened tabs for this same quiz to quizes list (`screen #2`).
