<?php
include 'db.php';

// Fetch the most recent quiz ID
$sql = "SELECT id FROM quizzes ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
    $quizId = $quiz['id'];

    // Fetch quiz questions from the most recent quiz
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user's name and email
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Check if the user already exists in the database
    $userCheckQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($userCheckQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If the user doesn't exist, insert into the 'users' table
    if ($stmt->num_rows == 0) {
        $insertUserQuery = "INSERT INTO users (name, email) VALUES (?, ?)";
        $stmt = $conn->prepare($insertUserQuery);
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $userId = $stmt->insert_id; // Get the last inserted user's ID
    } else {
        // User already exists, get the user ID
        $stmt->bind_result($userId);
        $stmt->fetch();
    }

    // Calculate the score
    $score = 0;
    foreach ($questions as $question) {
        $questionId = $question['id'];

        // Get the correct option directly from the database
        $correctAnswer = $question['correct_option']; // A, B, C, or D
        $userAnswer = isset($_POST["question$questionId"]) ? $_POST["question$questionId"] : '';

        // Compare user's answer with the correct answer
        if ($userAnswer == $correctAnswer) {
            $score++;
        }
    }

    // Redirect to result.php with score and user ID
    header("Location: result.php?score=$score&user_id=$userId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Start Quiz</title>
    <style>
        body { background: linear-gradient(135deg, #2c3e50, #3498db); color: white; font-family: 'Poppins', sans-serif; }
        .quiz-container { background-color: white; color: #333; padding: 30px; margin: auto; margin-top: 50px; box-shadow: 0px 0px 10px black; max-width: 700px; border-radius: 10px; }
        #timer { font-weight: bold; margin-bottom: 20px; }
        hr { border-top: 2px solid #2c3e50; margin: 25px 0; }
        .question-container { margin-bottom: 30px; }
        .form-check-label { font-weight: normal; margin-left: 10px; }
        .question-text { font-size: 1.1rem; margin-bottom: 25px; }
        .form-check { margin-bottom: 15px; }
        .option-label { font-weight: bold; margin-right: 30px; }
        .answer-option { display: flex; align-items: center; margin-bottom: 25px; }
        .form-check-input {
            margin-right: 2px; /* Space between the radio button and the option text */
        }
    </style>
</head>
<body>

<div class="container quiz-container">
    <h2 class="text-center">Take the Quiz</h2>
    <div id="timer" class="text-center">Time Remaining: 4:00</div>
    
    <form id="quizForm" method="POST">
        <?php foreach ($questions as $index => $question): ?>
            <div class="question-container">
                <p class="question-text"><strong>Question <?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($question['question_text']); ?></p>
                
                <!-- Options with labels A, B, C, D outside the radio buttons -->
                <div class="form-check answer-option">
                    <label class="option-label" for="question<?php echo $question['id']; ?>_A">A)</label>
                    <input type="radio" class="form-check-input" 
                           name="question<?php echo $question['id']; ?>" 
                           value="A" id="question<?php echo $question['id']; ?>_A" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_a']); ?></label>
                </div>
                <div class="form-check answer-option">
                    <label class="option-label" for="question<?php echo $question['id']; ?>_B">B)</label>
                    <input type="radio" class="form-check-input" 
                           name="question<?php echo $question['id']; ?>" 
                           value="B" id="question<?php echo $question['id']; ?>_B" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_b']); ?></label>
                </div>
                <div class="form-check answer-option">
                    <label class="option-label" for="question<?php echo $question['id']; ?>_C">C)</label>
                    <input type="radio" class="form-check-input" 
                           name="question<?php echo $question['id']; ?>" 
                           value="C" id="question<?php echo $question['id']; ?>_C" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_c']); ?></label>
                </div>
                <div class="form-check answer-option">
                    <label class="option-label" for="question<?php echo $question['id']; ?>_D">D)</label>
                    <input type="radio" class="form-check-input" 
                           name="question<?php echo $question['id']; ?>" 
                           value="D" id="question<?php echo $question['id']; ?>_D" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_d']); ?></label>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>
        
        <div class="mb-3">
            <label for="userName" class="form-label">Name:</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
            <label for="userEmail" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        
        <button type="submit" class="btn btn-dark">Submit Quiz</button>
    </form>
</div>

<script>
    let timeLeft = 240; // 4 minutes in seconds
    function updateTimer() {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        document.getElementById('timer').textContent = `Time Remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        timeLeft--;
        if (timeLeft < 0) {
            clearInterval(timerInterval);
            alert("Time's up! Submitting quiz...");
            document.getElementById('quizForm').submit();
        }
    }
    const timerInterval = setInterval(updateTimer, 1000);
</script>

</body>
</html>
