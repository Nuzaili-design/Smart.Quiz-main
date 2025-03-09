<?php
include 'db.php';

// Retrieve score and user ID from URL parameters
$score = isset($_GET['score']) ? (int)$_GET['score'] : 0;
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Insert the score into the result table if valid user
if ($userId > 0) {
    $stmt = $conn->prepare("INSERT INTO results (user_id, score, submission_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userId, $score);
    $stmt->execute();
    $stmt->close();
} else {
    echo "<script>alert('Invalid user!'); window.location.href='index.html';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Quiz Result</title>
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            text-align: center;
            padding: 50px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-out;
        }
        h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .btn-dark {
            background-color: #333;
            border-color: #333;
            padding: 10px 30px;
            font-size: 1.1rem;
        }
        .btn-dark:hover {
            background-color: #555;
            border-color: #555;
        }
        .progress-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
            margin-top: 30px;
        }
        .progress-bar {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(
                #3498db calc(var(--percentage) * 1%),
                #e0e0e0 calc(var(--percentage) * 1%)
            );
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #timeLeft {
            font-size: 1.5rem;
            color: #ff0000;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Thank You for Taking the Quiz!</h2>
    <p>Your score is: <span id="scoreDisplay"><?php echo $score; ?> / 5</span></p>
    <p>Goodbye, and get ready for the next quiz!</p>

    <div class="progress-container">
        <div class="progress-bar" id="progressBar" style="--percentage: 100;">
            <div id="timeLeft">0:10</div>
        </div>
    </div>

    <p id="timerMessage">You'll be able to view answers when the timer is done</p>
    <a href="answers.php" class="btn btn-dark" id="viewAnswersBtn" style="display: none; margin-top: 20px;">View Correct Answers</a>
</div>

<script>
    // Timer logic
    let timeRemaining = 10; // 10 seconds in seconds
    const progressBar = document.getElementById('progressBar');
    const timeLeftElement = document.getElementById('timeLeft');
    const viewAnswersBtn = document.getElementById('viewAnswersBtn');
    const totalTime = 10; // Total time is now 10 seconds

    const countdownInterval = setInterval(() => {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        timeLeftElement.textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

        // Update progress bar
        const percentage = (timeRemaining / totalTime) * 100;
        progressBar.style.setProperty('--percentage', percentage);

        // When time runs out
        if (timeRemaining <= 0) {
            clearInterval(countdownInterval);
            timeLeftElement.textContent = "Time's up!";
            viewAnswersBtn.style.display = 'inline-block'; // Show the "View Answers" button
        }

        timeRemaining--;
    }, 1000);
</script>

</body>
</html>
