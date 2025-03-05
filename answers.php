<?php
include 'db.php';  // Include the database connection

// Get the score and user_id from the query string
$score = isset($_GET['score']) ? $_GET['score'] : 0;
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

// Fetch the most recent quiz ID
$sql = "SELECT id FROM quizzes ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
    $quizId = $quiz['id'];

    // Fetch quiz questions and correct answers for the most recent quiz
    $sql = "SELECT * FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    } else {
        echo "<script>alert('No quiz found! Please create a quiz first in the admin panel.'); window.location.href='index.html';</script>";
        exit;
    }
} else {
    echo "<script>alert('No quizzes found! Please create a quiz first in the admin panel.'); window.location.href='index.html';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
          rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
          crossorigin="anonymous">
    <title>Quiz Answers</title>
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            font-family: 'Poppins', sans-serif;
        }

        .answers-container {
            background-color: rgb(255, 255, 255);
            color: #333;
            padding: 30px;
            margin: auto;
            margin-top: 50px;
            box-shadow: 0px 0px 10px rgb(0, 0, 0);
            max-width: 600px;
            border-radius: 10px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .correct-answer {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .btn-back {
            margin-top: 20px;
        }

        .btn-dark:hover {
            background-color: #555;
            border-color: #555;
        }

        hr {
            border-top: 2px solid #2c3e50;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="container answers-container">
    <h2 class="text-center">Correct Answers</h2>

    <div id="answersList">
        <?php
        // Loop through the questions and display the correct answers
        foreach ($questions as $index => $question) {
            $questionText = htmlspecialchars($question['question_text']);
            $correctAnswer = htmlspecialchars($question['correct_option']);

            echo "
                <div class='correct-answer'>
                    <strong>Question " . ($index + 1) . ":</strong> $questionText <br>
                    <span class='text-success'>Answer: $correctAnswer</span>
                </div>
                <hr>
            ";
        }
        ?>
    </div>

    <div class="text-center">
        <a href="index.html" class="btn btn-dark btn-back">Back to Quiz</a>
    </div>
</div>

</body>
</html>
