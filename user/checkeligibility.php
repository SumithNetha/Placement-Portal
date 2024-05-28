<?php
session_start();

if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

require_once("../db.php");

if (isset($_GET['id'])) { // Check if the 'id' parameter is set in the URL

    // Fetch user's qualifications
    $sql = "SELECT hsc, ssc, ug, pg, qualification FROM users WHERE id_user = '$_SESSION[id_user]'";
    $result1 = $conn->query($sql);

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $sum = $row1['hsc'] . $row1['ssc'] . $row1['ug'] . $row1['pg'];
        $total = ($sum / 4);
        $userQualification = $row1['qualification'];
    }

    // Fetch job post details
    $sql = "SELECT maximumsalary, qualification FROM job_post WHERE id_jobpost = '$_GET[id]'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eligibility = $row['maximumsalary'];
        $jobQualification = $row['qualification'];

        if ($total >= $eligibility) {
            if ($userQualification == $jobQualification) {
                $_SESSION['status'] = "You are eligible for this drive, apply if you are interested.";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "You are not eligible for this drive due to the course criteria. Check out other drives.";
                $_SESSION['status_code'] = "error";
            }
        } else {
            $_SESSION['status'] = "You are not eligible for this drive either due to the overall percentage criteria or course criteria. Update your marks in your profile, if you think you are eligible.";
            $_SESSION['status_code'] = "error";
        }

        // Redirect to the job post page
        header("Location: ../view-job-post.php?id=$_GET[id]");
        exit();
    } else {
        // Job post not found
        $_SESSION['status'] = "Job post not found.";
        $_SESSION['status_code'] = "error";
        header("Location: ../index.php"); // Redirect to an appropriate page
        exit();
    }
} else {
    // 'id' parameter not set in the URL
    $_SESSION['status'] = "Invalid URL.";
    $_SESSION['status_code'] = "error";
    header("Location: ../index.php"); // Redirect to an appropriate page
    exit();
}
?>
